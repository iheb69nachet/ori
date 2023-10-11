<?php

namespace Botble\Ecommerce\Forms;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Forms\FormAbstract;
use Botble\Ecommerce\Http\Requests\ProductLabelRequest;
use Botble\Ecommerce\Models\ProductInfo;

class ProductInfoForm extends FormAbstract
{
    public function buildForm(): void
    {
        $this
            ->setupModel(new ProductInfo())
            ->setValidatorClass(ProductLabelRequest::class)
            ->withCustomFields()
            ->add('name', 'text', [
                'label' => trans('core/base::forms.name'),
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'placeholder' => trans('core/base::forms.name_placeholder'),
                    'data-counter' => 120,
                ],
            ])
            ->add('type', 'customSelect', [
                'label' => 'type',
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'class' => 'select-search-full',
                ],
                'choices' => ['Pourquoi ?'=>'Pourquoi ?','Où ?'=>'Où ?','Quoi ?'=>'Quoi ?','Qui ?'=>'Qui ?','Quand ?'=>'Quand ?','Combien ?'=>'Combien ?','Comment ?'=>'Comments ?'],
            ])
            
           
            
            ->setBreakFieldPoint('status');
    }
}
