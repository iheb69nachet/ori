<?php

namespace Botble\Ecommerce\Forms;

use Botble\Base\Facades\Assets;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Forms\Fields\MultiCheckListField;
use Botble\Base\Forms\Fields\TagField;
use Botble\Base\Forms\FormAbstract;
use Botble\Ecommerce\Enums\GlobalOptionEnum;
use Botble\Ecommerce\Enums\ProductTypeEnum;
use Botble\Ecommerce\Forms\Fields\CategoryMultiField;
use Botble\Ecommerce\Http\Requests\ProductRequest;
use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Models\ProductInfo;

use Botble\Ecommerce\Repositories\Interfaces\BrandInterface;
use Botble\Ecommerce\Repositories\Interfaces\GlobalOptionInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductAttributeSetInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductCollectionInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductLabelInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductVariationInterface;
use Botble\Ecommerce\Repositories\Interfaces\TaxInterface;
use Botble\Ecommerce\Facades\EcommerceHelper;
use Collective\Html\HtmlFacade as Html;
use Botble\Ecommerce\Facades\ProductCategoryHelper;
use Botble\Ecommerce\Tables\ProductVariationTable;
use DB;
class ProductForm extends FormAbstract
{
    public function buildForm(): void
    {
        $this->addAssets();

        $brands = app(BrandInterface::class)->pluck('name', 'id');
        $brands = [0 => trans('plugins/ecommerce::brands.no_brand')] + $brands;

        $productCollections = app(ProductCollectionInterface::class)->pluck('name', 'id');

        $productLabels = app(ProductLabelInterface::class)->pluck('name', 'id');
        // $who=DB
        $who = ProductInfo::where('type',"Qui ?")->pluck('name','id');
        $when = ProductInfo::where('type',"Quand ?")->pluck('name','id');
        $how = ProductInfo::where('type',"Comment ?")->pluck('name','id');
        $why = ProductInfo::where('type',"Pourquoi ?")->pluck('name','id');
        $where = ProductInfo::where('type',"Où ?")->pluck('name','id');
        $howMany = ProductInfo::where('type',"Combien ?")->pluck('name','id');
        $what = ProductInfo::where('type',"Quoi ?")->pluck('name','id');

       
        $productId = null;
        $selectedCategories = [];
        $selectedProductCollections = [];
        $selectedProductLabels = [];
        $tags = null;
        $totalProductVariations = 0;
        if ($this->getModel()) {
            $productId = $this->getModel()->id;

            $selectedCategories = $this->getModel()->categories()->pluck('category_id')->all();
            $selectedProductCollections = $this->getModel()
                ->productCollections()
                ->pluck('product_collection_id')
                ->all();
            $selectedProductLabels = $this->getModel()->productLabels()->pluck('product_label_id')->all();

            $totalProductVariations = app(ProductVariationInterface::class)->count([
                'configurable_product_id' => $productId,
            ]);

            $tags = $this->getModel()->tags()->pluck('name')->implode(',');
        }
// dd($this->getModel()->where==""?[]:explode(',',$this->getModel()->where));

// dd($this->getModel()->where==null);
$whereVal= is_array($this->getModel()->where) ? $this->getModel()->where : (empty($this->getModel()->where) ? [] : explode(',', $this->getModel()->where));
$whenVal= is_array($this->getModel()->when) ? $this->getModel()->when : (empty($this->getModel()->when) ? [] : explode(',', $this->getModel()->when));
$howVal= is_array($this->getModel()->how) ? $this->getModel()->how : (empty($this->getModel()->how) ? [] : explode(',', $this->getModel()->how));
$how_manyVal= is_array($this->getModel()->how_many) ? $this->getModel()->how_many : (empty($this->getModel()->how_many) ? [] : explode(',', $this->getModel()->how_many));
$whatVal= is_array($this->getModel()->what) ? $this->getModel()->what : (empty($this->getModel()->what) ? [] : explode(',', $this->getModel()->what));
$whereVal= is_array($this->getModel()->where) ? $this->getModel()->where : (empty($this->getModel()->where) ? [] : explode(',', $this->getModel()->where));
$whoVal= is_array($this->getModel()->who) ? $this->getModel()->who : (empty($this->getModel()->who) ? [] : explode(',', $this->getModel()->who));
$whyVal= is_array($this->getModel()->why) ? $this->getModel()->why : (empty($this->getModel()->why) ? [] : explode(',', $this->getModel()->why));
// dd($this->getModel()->why);
        $this
            ->setupModel(new Product())
            ->setValidatorClass(ProductRequest::class)
            ->withCustomFields()
            ->addCustomField('categoryMulti', CategoryMultiField::class)
            ->addCustomField('multiCheckList', MultiCheckListField::class)
            ->addCustomField('tags', TagField::class)
            ->setFormOption('files', true)
            ->add('name', 'text', [
                'label' => trans('plugins/ecommerce::products.form.name'),
                'label_attr' => ['class' => 'text-title-field required'],
                'attr' => [
                    'placeholder' => trans('core/base::forms.name_placeholder'),
                    'data-counter' => 150,
                ],
            ])
            ->add('description', 'editor', [
                'label' => trans('core/base::forms.description'),
                'label_attr' => ['class' => 'control-label'],
                'attr' => [
                    'rows' => 2,
                    'placeholder' => trans('core/base::forms.description_placeholder'),
                    'data-counter' => 1000,
                ],
            ])
            ->add('content', 'editor', [
                'label' => trans('plugins/ecommerce::products.form.content'),
                'label_attr' => ['class' => 'text-title-field'],
                'attr' => [
                    'rows' => 4,
                    'with-short-code' => true,
                ],
            ])
            ->add('images[]', 'mediaImages', [
                'label' => trans('plugins/ecommerce::products.form.image'),
                'label_attr' => ['class' => 'control-label'],
                'values' => $productId ? $this->getModel()->images : [],
            ])
            ->addMetaBoxes([
                'with_related' => [
                    'title' => null,
                    'content' => Html::tag('div', '', [
                        'class' => 'wrap-relation-product',
                        'data-target' => route('products.get-relations-boxes', $productId ?: 0),
                    ]),
                    'wrap' => false,
                    'priority' => 9999,
                ],
            ])
            ->add('product_type', 'hidden', [
                'value' => request()->input('product_type') ?: ProductTypeEnum::PHYSICAL,
            ])
            ->add('status', 'customSelect', [
                'label' => trans('core/base::tables.status'),
                'label_attr' => ['class' => 'control-label required'],
                'choices' => BaseStatusEnum::labels(),
            ])
            
            ->add('is_featured', 'onOff', [
                'label' => trans('core/base::forms.is_featured'),
                'label_attr' => ['class' => 'control-label'],
                'default_value' => false,
            ])

            ->add('where[]', 'multiCheckList', [
                'label' =>'Où ?',
                'label_attr' => ['class' => 'control-label'],
                'choices' => $where,
                'value' => old('where',$whereVal),
            ])
            ->add('who[]', 'multiCheckList', [
                'label' =>'Qui ?',
                'label_attr' => ['class' => 'control-label'],
                'choices' => $who,
                'value' => old('who',$whoVal),
            ])
            ->add('what[]', 'multiCheckList', [
                'label' =>'Quoi ?',
                'label_attr' => ['class' => 'control-label'],
                'choices' => $what,
                'value' => old('what',$whatVal),
            ])
            ->add('when[]', 'multiCheckList', [
                'label' =>'Quand ?',
                'label_attr' => ['class' => 'control-label'],
                'choices' => $when,
                'value' => old('when',$whenVal),
            ])
            ->add('how[]', 'multiCheckList', [
                'label' =>'Comment ?',
                'label_attr' => ['class' => 'control-label'],
                'choices' => $how,
                'value' => old('how',$howVal),
            ])
            ->add('why[]', 'multiCheckList', [
                'label' =>'Pourquoi ?',
                'label_attr' => ['class' => 'control-label'],
                'choices' => $why,
                'value' => old('why',$whyVal),
            ])
            ->add('how_many[]', 'multiCheckList', [
                'label' =>'Combien ?',
                'label_attr' => ['class' => 'control-label'],
                'choices' => $howMany,
                'value' => old('how_many',$how_manyVal),
            ])
           
          
            ->add('categories[]', 'categoryMulti', [
                'label' => trans('plugins/ecommerce::products.form.categories'),
                'label_attr' => ['class' => 'control-label'],
                'choices' => ProductCategoryHelper::getAllProductCategoriesWithChildren(),
                'value' => old('categories', $selectedCategories),
            ])
            ->add('brand_id', 'customSelect', [
                'label' => trans('plugins/ecommerce::products.form.brand'),
                'label_attr' => ['class' => 'control-label'],
                'choices' => $brands,
            ])
            ->add('image', 'mediaImage', [
                'label' => trans('plugins/ecommerce::products.form.featured_image'),
                'label_attr' => ['class' => 'control-label'],
            ])
            ->add('product_collections[]', 'multiCheckList', [
                'label' => trans('plugins/ecommerce::products.form.collections'),
                'label_attr' => ['class' => 'control-label'],
                'choices' => $productCollections,
                'value' => old('product_collections', $selectedProductCollections),
            ])
            ->add('product_labels[]', 'multiCheckList', [
                'label' => trans('plugins/ecommerce::products.form.labels'),
                'label_attr' => ['class' => 'control-label'],
                'choices' => $productLabels,
                'value' => old('product_labels', $selectedProductLabels),
            ]);

        if (EcommerceHelper::isTaxEnabled()) {
            $taxes = app(TaxInterface::class)->all()->pluck('title_with_percentage', 'id');

            $selectedTaxes = [];
            if ($this->getModel() && $this->getModel()->id) {
                $selectedTaxes = $this->getModel()->taxes()->pluck('tax_id')->all();
            } elseif ($defaultTaxRate = get_ecommerce_setting('default_tax_rate')) {
                $selectedTaxes = [$defaultTaxRate];
            }

            $this->add('taxes[]', 'multiCheckList', [
                'label' => trans('plugins/ecommerce::products.form.taxes'),
                'label_attr' => ['class' => 'control-label'],
                'choices' => $taxes,
                'value' => old('taxes', $selectedTaxes),
            ]);
        }

        $globalOptions = app(GlobalOptionInterface::class)->select(['name', 'id'])->get();

        $globalOptionArray = [];

        foreach ($globalOptions as $globalOption) {
            $globalOptionArray[$globalOption->id] = $globalOption->name;
        }

        $this
            ->add('tag', 'tags', [
                'label' => trans('plugins/ecommerce::products.form.tags'),
                'label_attr' => ['class' => 'control-label'],
                'value' => $tags,
                'attr' => [
                    'placeholder' => trans('plugins/ecommerce::products.form.write_some_tags'),
                    'data-url' => route('product-tag.all'),
                ],
            ])
            ->setBreakFieldPoint('status')
            ->addMetaBoxes([
                'product_options_box' => [
                    'title' => trans('plugins/ecommerce::product-option.name'),
                    'content' => view('plugins/ecommerce::products.partials.product-option-form', [
                        'options' => GlobalOptionEnum::options(),
                        'globalOptions' => $globalOptionArray,
                        'product' => $this->getModel(),
                        'routes' => [
                            'ajax_option_info' => route('global-option.ajaxInfo'),
                        ],
                    ]),
                    'priority' => 4,
                ],
            ]);

        $productAttributeSets = app(ProductAttributeSetInterface::class)->getAllWithSelected($productId, []);

        $this
            ->addMetaBoxes([
                'attribute-sets' => [
                    'content' => '',
                    'before_wrapper' => '<div class="d-none product-attribute-sets-url" data-url="' . route('products.product-attribute-sets') . '">',
                    'after_wrapper' => '</div>',
                    'priority' => 3,
                ],
            ]);

        if (! $totalProductVariations) {
            $this
                ->removeMetaBox('variations')
                ->addMetaBoxes([
                    'general' => [
                        'title' => trans('plugins/ecommerce::products.overview'),
                        'content' => view(
                            'plugins/ecommerce::products.partials.general',
                            [
                                'product' => $productId ? $this->getModel() : null,
                                'isVariation' => false,
                                'originalProduct' => null,
                            ]
                        )->render(),
                        'before_wrapper' => '<div id="main-manage-product-type">',
                        'priority' => 2,
                    ],
                    'attributes' => [
                        'title' => trans('plugins/ecommerce::products.attributes'),
                        'content' => view('plugins/ecommerce::products.partials.add-product-attributes', [
                            'product' => $this->getModel(),
                            'productAttributeSets' => $productAttributeSets,
                            'addAttributeToProductUrl' => $this->getModel()->id ? route('products.add-attribute-to-product', $this->getModel()->id) : null,
                        ]),
                        'after_wrapper' => '</div>',
                        'priority' => 3,
                    ],
                ]);
        } elseif ($productId) {
            $productVariationTable = app(ProductVariationTable::class)
                ->setProductId($productId)
                ->setProductAttributeSets($productAttributeSets);

            if ($this->getModel()->isTypeDigital()) {
                $productVariationTable->isDigitalProduct();
            }

            $this
                ->removeMetaBox('general')
                ->addMetaBoxes([
                    'variations' => [
                        'title' => trans('plugins/ecommerce::products.product_has_variations'),
                        'content' => view('plugins/ecommerce::products.partials.configurable', [
                            'product' => $this->getModel(),
                            'productAttributeSets' => $productAttributeSets,
                            'productVariationTable' => $productVariationTable,
                        ]),
                        'before_wrapper' => '<div id="main-manage-product-type">',
                        'after_wrapper' => '</div>',
                        'priority' => 3,
                        'render' => false,
                    ],
                ]);
        }
    }

    public function addAssets(): void
    {
        Assets::addStyles(['datetimepicker'])
            ->addScripts([
                'moment',
                'datetimepicker',
                'jquery-ui',
                'input-mask',
                'blockui',
            ])
            ->addStylesDirectly(['vendor/core/plugins/ecommerce/css/ecommerce.css'])
            ->addScriptsDirectly([
                'vendor/core/plugins/ecommerce/js/edit-product.js',
                'vendor/core/plugins/ecommerce/js/product-option.js',
            ]);
    }
}
