<?php

namespace Botble\Ecommerce\Repositories\Caches;

use Botble\Ecommerce\Repositories\Interfaces\ProductInfoInterface;
use Botble\Support\Repositories\Caches\CacheAbstractDecorator;

class ProductInfoCacheDecorator extends CacheAbstractDecorator implements ProductInfoInterface
{
}
