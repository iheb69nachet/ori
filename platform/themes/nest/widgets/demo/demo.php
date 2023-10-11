<?php

use Botble\Widget\AbstractWidget;

class DemoWidget extends AbstractWidget
{
    public function __construct()
    {
        parent::__construct([
            'name' => __('Demo'),
            'description' => __('Display ads on sidebar'),
            'ads_key' => 0,
        ]);
    }
}
