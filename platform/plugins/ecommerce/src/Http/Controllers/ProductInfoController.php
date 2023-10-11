<?php

namespace Botble\Ecommerce\Http\Controllers;

use Botble\Base\Events\BeforeEditContentEvent;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Facades\PageTitle;
use Botble\Base\Forms\FormBuilder;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Ecommerce\Forms\ProductInfoForm;
use Botble\Ecommerce\Http\Requests\ProductInfoRequest;
use Botble\Ecommerce\Repositories\Interfaces\ProductInfoInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductLabelInterface;

use Botble\Ecommerce\Tables\ProductLabelTable;
use Botble\Ecommerce\Tables\ProductInfoTable;

use Exception;
use Illuminate\Http\Request;

class ProductInfoController extends BaseController
{
    public function __construct(protected ProductInfoInterface $productLabelRepository)
    {
    }

    public function index(ProductInfoTable $table)
    {
        // dd('sdfs');
        PageTitle::setTitle('info');

        return $table->renderTable();
    }

    public function create(FormBuilder $formBuilder)
    {
        PageTitle::setTitle(trans('plugins/ecommerce::product-info.create'));

        return $formBuilder->create(ProductInfoForm::class)->renderForm();
    }

    public function store(ProductInfoRequest $request, BaseHttpResponse $response)
    {
        $productLabel = $this->productLabelRepository->createOrUpdate($request->input());

        event(new CreatedContentEvent(PRODUCT_LABEL_MODULE_SCREEN_NAME, $request, $productLabel));

        return $response
            ->setPreviousUrl(route('product-info.index'))
            ->setNextUrl(route('product-info.edit', $productLabel->id))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function edit(int|string $id, FormBuilder $formBuilder, Request $request)
    {
        $productLabel = $this->productLabelRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $productLabel));

        PageTitle::setTitle(trans('core/base::forms.edit_item', ['name' => $productLabel->name]));

        return $formBuilder->create(ProductInfoForm::class, ['model' => $productLabel])->renderForm();
    }

    public function update(int|string $id, ProductInfoRequest $request, BaseHttpResponse $response)
    {
        $productLabel = $this->productLabelRepository->findOrFail($id);

        $productLabel->fill($request->input());

        $this->productLabelRepository->createOrUpdate($productLabel);

        event(new UpdatedContentEvent(PRODUCT_LABEL_MODULE_SCREEN_NAME, $request, $productLabel));

        return $response
            ->setPreviousUrl(route('product-info.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(int|string $id, Request $request, BaseHttpResponse $response)
    {
        try {
            $productLabel = $this->productLabelRepository->findOrFail($id);

            $this->productLabelRepository->delete($productLabel);

            event(new DeletedContentEvent(PRODUCT_LABEL_MODULE_SCREEN_NAME, $request, $productLabel));

            return $response->setMessage(trans('core/base::notices.delete_success_message'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    public function deletes(Request $request, BaseHttpResponse $response)
    {
        $ids = $request->input('ids');
        if (empty($ids)) {
            return $response
                ->setError()
                ->setMessage(trans('core/base::notices.no_select'));
        }

        foreach ($ids as $id) {
            $productLabel = $this->productLabelRepository->findOrFail($id);
            $this->productLabelRepository->delete($productLabel);
            event(new DeletedContentEvent(PRODUCT_LABEL_MODULE_SCREEN_NAME, $request, $productLabel));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }
}
