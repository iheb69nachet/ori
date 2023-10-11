<?php

namespace Botble\Ecommerce\Repositories\Eloquent;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Models\BaseQueryBuilder;
use Botble\Ecommerce\Enums\OrderStatusEnum;
use Botble\Ecommerce\Models\Option;
use Botble\Ecommerce\Models\OptionValue;
use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Models\ProductAttribute;
use Botble\Ecommerce\Repositories\Interfaces\RecettetInterface;
use Botble\Support\Repositories\Eloquent\RepositoriesAbstract;
use Carbon\Carbon;
use Botble\Ecommerce\Facades\EcommerceHelper;
use Exception;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Botble\Language\Facades\Language;
use App\Model\Recette;

class RecetteRepository extends RepositoriesAbstract implements RecettetInterface
{
    public function getSearch(string|null $keyword, int $paginate = 10)
    {
        return $this->filterProducts([
            'keyword' => $keyword,
            'paginate' => [
                'per_page' => $paginate,
                'current_paged' => 1,
            ],
        ]);
    }

    protected function exceptOutOfStockProducts()
    {
        /**
         * @var Recette $model
         */
        $model = $this->model;

        return $model->notOutOfStock();
    }

    public function getRelatedProductAttributes($product)
    {
        try {
            $data = ProductAttribute::query()
                ->join(
                    'recettes_variation_items',
                    'recettes_variation_items.attribute_id',
                    '=',
                    'recettes_attributes.id'
                )
                ->join(
                    'recettes_variations',
                    'recettes_variation_items.variation_id',
                    '=',
                    'recettes_variations.id'
                )
                ->where('configurable_product_id', $product->id)
                ->where('recettes_attributes.status', BaseStatusEnum::PUBLISHED)
                ->select('recettes_attributes.*')
                ->distinct();

            return $this->applyBeforeExecuteQuery($data)->get();
        } catch (Exception) {
            return collect();
        }
    }

    public function getProducts(array $params)
    {
      

        return $this->filterProducts([]);
    }

    public function getProductsWithCategory(array $params)




        return $this->filterProducts([]);
    }

    public function getOnSaleProducts(array $params)
    {
        $this->model = $this->originalModel;

        $params = array_merge([
            'condition' => [
                'status' => BaseStatusEnum::PUBLISHED,
                'is_variation' => 0,
            ],
            'order_by' => [
                'order' => 'ASC',
                'created_at' => 'DESC',
            ],
            'take' => null,
            'paginate' => [
                'per_page' => null,
                'current_paged' => 1,
            ],
            'with' => [],
        ], $params);

        $this->model = $this->model
            ->where(function (EloquentBuilder $query) {
                return $query
                    ->where(function (EloquentBuilder $subQuery) {
                        return $subQuery
                            ->where('sale_type', 0)
                            ->where('sale_price', '>', 0);
                    })
                    ->orWhere(function (EloquentBuilder $subQuery) {
                        return $subQuery
                            ->where(function (EloquentBuilder $sub) {
                                return $sub
                                    ->where('sale_type', 1)
                                    ->where('start_date', '<>', null)
                                    ->where('end_date', '<>', null)
                                    ->where('start_date', '<=', Carbon::now())
                                    ->where('end_date', '>=', Carbon::today());
                            })
                            ->orWhere(function (EloquentBuilder $sub) {
                                return $sub
                                    ->where('sale_type', 1)
                                    ->where('start_date', '<>', null)
                                    ->where('start_date', '<=', Carbon::now())
                                    ->whereNull('end_date');
                            });
                    });
            });

        $this->exceptOutOfStockProducts();

        return $this->advancedGet($params);
    }

    public function getProductVariations(int|string|null $configurableProductId, array $params = [])
    {
        $this->model = $this->model
            ->join('recettes_variations', function (JoinClause $join) use ($configurableProductId) {
                return $join
                    ->on('recettes_variations.product_id', '=', 'recettes.id')
                    ->where('recettes_variations.configurable_product_id', $configurableProductId);
            })
            ->join(
                'recettes as original_products',
                'recettes_variations.configurable_product_id',
                '=',
                'original_products.id'
            );

        $params = array_merge([
            'select' => [
                'recettes.*',
                'recettes_variations.id as variation_id',
                'recettes_variations.configurable_product_id as configurable_product_id',
                'original_products.images as original_images',
            ],
        ], $params);

        return $this->advancedGet($params);
    }

    public function getProductsByCollections(array $params)
    {
        $params = array_merge([
            'collections' => [
                'by' => 'id',
                'value_in' => [],
            ],
            'condition' => [
                'recettes.status' => BaseStatusEnum::PUBLISHED,
                'recettes.is_variation' => 0,
            ],
            'order_by' => [
                'recettes.order' => 'ASC',
                'recettes.created_at' => 'DESC',
            ],
            'take' => null,
            'paginate' => [
                'per_page' => null,
                'current_paged' => 1,
            ],
            'select' => [
                'recettes.*',
            ],
            'with' => [],
            'withCount' => [],
        ], $params);

        $filters = ['collections' => $params['collections']['value_in']];

        Arr::forget($params, 'categories');

        return $this->filterProducts($filters, $params);
    }

    public function getProductByBrands(array $params)
    {
        $params = array_merge([
            'brand_id' => null,
            'condition' => [
                'status' => BaseStatusEnum::PUBLISHED,
                'is_variation' => 0,
            ],
            'order_by' => [
                'order' => 'ASC',
                'created_at' => 'DESC',
            ],
            'take' => null,
            'paginate' => [
                'per_page' => null,
                'current_paged' => 1,
            ],
            'select' => [
                '*',
            ],
            'with' => [

            ],
        ], $params);

        $filters = ['brands' => (array)$params['brand_id']];

        Arr::forget($params, 'brand_id');

        return $this->filterProducts($filters, $params);
    }

    public function getProductsByCategories(array $params)
    {
        $params = array_merge([
            'categories' => [
                'by' => 'id',
                'value_in' => [],
            ],
            'condition' => [
                'recettes.status' => BaseStatusEnum::PUBLISHED,
                'recettes.is_variation' => 0,
            ],
            'order_by' => [
                'recettes.order' => 'ASC',
                'recettes.created_at' => 'DESC',
            ],
            'take' => null,
            'paginate' => [
                'per_page' => null,
                'current_paged' => 1,
            ],
            'select' => [
                'recettes.*',
            ],
            'with' => [],
            'withCount' => [],
        ], $params);

        $filters = ['categories' => $params['categories']['value_in']];

        Arr::forget($params, 'categories');

        return $this->filterProducts($filters, $params);
    }

    public function getProductByTags(array $params)
    {
        $params = array_merge([
            'product_tag' => [
                'by' => 'id',
                'value_in' => [],
            ],
            'condition' => [
                'recettes.status' => BaseStatusEnum::PUBLISHED,
                'recettes.is_variation' => 0,
            ],
            'order_by' => [
                'recettes.order' => 'ASC',
                'recettes.created_at' => 'DESC',
            ],
            'take' => null,
            'paginate' => [
                'per_page' => null,
                'current_paged' => 1,
            ],
            'select' => [
                'recettes.*',
            ],
            'with' => [],
            'withCount' => [],
        ], $params);

        $filters = ['tags' => $params['product_tag']['value_in']];

        Arr::forget($params, 'product_tag');

        return $this->filterProducts($filters, $params);
    }

    public function filterProducts(array $filters, array $params = [])
    {
        $filters = array_merge([
            'name' => null,
            'ingredient' => [],
            'continent' => [],
            'repas' => [],
        ], $filters);



     
        $this->model = $this->originalModel;
// dd($this->model);
        $now = Carbon::now();

        $this->model = $this->model
            ->select('*');
            

        return $this->advancedGet($params);
    }

    public function getProductsByIds(array $ids, array $params = [])
    {
        $this->model = $this->originalModel;

        $params = array_merge([
            'condition' => [
                'recettes.status' => BaseStatusEnum::PUBLISHED,
                'recettes.is_variation' => 0,
            ],
            'paginate' => [
                'per_page' => null,
                'current_paged' => 1,
            ],
        ], $params);

        $this->model = $this->model
            ->whereIn('id', $ids);

        if (config('database.default') == 'mysql') {
            $idsOrdered = implode(',', $ids);
            if (! empty($idsOrdered)) {
                $this->model = $this->model->orderByRaw("FIELD(id, $idsOrdered)");
            }
        }

        return $this->advancedGet($params);
    }

    public function getProductsWishlist(int|string $customerId, array $params = [])
    {
        $this->model = $this->originalModel;

        $params = array_merge([
            'condition' => [
                'recettes.status' => BaseStatusEnum::PUBLISHED,
                'recettes.is_variation' => 0,
            ],
            'paginate' => [
                'per_page' => null,
                'current_paged' => 1,
            ],
            'with' => ['slugable'],
            'order_by' => ['ec_wish_lists.updated_at' => 'desc'],
            'select' => ['recettes.*'],
        ], $params);

        $this->model = $this->model
            ->join('ec_wish_lists', 'ec_wish_lists.product_id', 'recettes.id')
            ->where('ec_wish_lists.customer_id', $customerId);

        return $this->advancedGet($params);
    }

    public function getProductsRecentlyViewed(int|string $customerId, array $params = [])
    {
        $this->model = $this->originalModel;

        $params = array_merge([
            'condition' => [
                'recettes.status' => BaseStatusEnum::PUBLISHED,
                'recettes.is_variation' => 0,
            ],
            'paginate' => [
                'per_page' => null,
                'current_paged' => 1,
            ],
            'with' => ['slugable'],
            'order_by' => ['ec_customer_recently_viewed_products.id' => 'desc'],
            'select' => ['recettes.*'],
        ], $params);

        $this->model = $this->model
            ->join('ec_customer_recently_viewed_products', 'ec_customer_recently_viewed_products.product_id', 'recettes.id')
            ->where('ec_customer_recently_viewed_products.customer_id', $customerId);

        return $this->advancedGet($params);
    }

    public function saveProductOptions(array $options, Product $product)
    {
        try {
            $existsOptionIds = [];
            foreach ($options as $opt) {
                if (isset($opt['id']) && intval($opt['id']) > 0) {
                    $option = Option::query()->find($opt['id']);

                    if (! $option) {
                        $option = new Option();
                    }

                    $existsOptionIds[] = $opt['id'];
                } else {
                    $option = new Option();
                }

                $opt['required'] = isset($opt['required']) && $opt['required'] === 'on';
                $option->fill($opt);
                $option->product_id = $product->id;
                $option->save();
                $option->values()->delete();
                if (! empty($opt['values'])) {
                    $optionValues = $this->formatOptionValue($opt['values']);
                    $option->values()->saveMany($optionValues);
                }
                $existsOptionIds[] = $option->id;
            }

            if (! empty($existsOptionIds)) {
                Option::whereNotIn('id', $existsOptionIds)
                    ->where('product_id', $product->id)
                    ->delete();

                OptionValue::whereNotIn('option_id', $existsOptionIds)
                    ->whereHas('option', function ($query) use ($product) {
                        $query->where('product_id', $product->id);
                    })
                    ->delete();
            } else {
                foreach ($product->options()->get() as $option) {
                    $option->delete();
                }
            }
        } catch (Exception $exception) {
            info($exception->getMessage());
        }
    }

    protected function formatOptionValue(array $options): array
    {
        $values = [];
        foreach ($options as $value) {
            $optionValue = new OptionValue();
            if (! isset($value['option_value'])) {
                $value['option_value'] = '';
            }
            $optionValue->fill($value);
            $values[] = $optionValue;
        }

        return $values;
    }

    public function productsNeedToReviewByCustomer(int|string $customerId, int $limit = 12, array $orderIds = [])
    {
        $data = $this->model
            ->select([
                'recettes.id',
                'recettes.name',
                'recettes.image',
                DB::raw('MAX(ec_orders.id) as ec_orders_id'),
                DB::raw('MAX(ec_orders.completed_at) as order_completed_at'),
                DB::raw('MAX(ec_order_product.product_name) as order_product_name'),
                DB::raw('MAX(ec_order_product.product_image) as order_product_image'),
            ])
            ->where('recettes.is_variation', 0)
            ->leftJoin('recettes_variations', 'recettes_variations.configurable_product_id', 'recettes.id')
            ->leftJoin('ec_order_product', function ($query) {
                $query
                    ->on('ec_order_product.product_id', 'recettes.id')
                    ->orOn('ec_order_product.product_id', 'recettes_variations.product_id');
            })
            ->join('ec_orders', function (JoinClause $query) use ($customerId, $orderIds) {
                $query
                    ->on('ec_orders.id', 'ec_order_product.order_id')
                    ->where('ec_orders.user_id', $customerId)
                    ->where('ec_orders.status', OrderStatusEnum::COMPLETED);
                if ($orderIds) {
                    $query->whereIn('ec_orders.id', $orderIds);
                }
            })
            ->whereDoesntHave('reviews', function (EloquentBuilder $query) use ($customerId) {
                $query->where('ec_reviews.customer_id', $customerId);
            })
            ->orderBy('order_completed_at', 'desc')
            ->groupBy('recettes.id', 'recettes.name', 'recettes.image');

        return $data->limit($limit)->get();
    }
}
