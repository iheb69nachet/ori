<?php

namespace Botble\Ecommerce\Models;

use Botble\Base\Casts\SafeContent;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Models\BaseModel;

class ProductInfo extends BaseModel
{
    protected $table = 'ec_product_infos';

    protected $fillable = [
        'name',
        'type',
    ];

    protected $casts = [
        'name' => SafeContent::class,
        'type' => SafeContent::class,

    ];
}
