@php
    Theme::set('bodyClass', 'single-product');

    $layout = MetaBox::getMetaData($product, 'layout', true);

    if (!$layout) {
        $layout = theme_option('product_single_layout');
    }

    $layout = ($layout && in_array($layout, array_keys(get_product_single_layouts()))) ? $layout : 'product-right-sidebar';
    Theme::layout("product-full-width");

    Theme::asset()->container('footer')->usePath()->add('slick-js', 'js/plugins/slick.js', ['jquery']);

    Theme::asset()->usePath()->add('magnific-popup-css', 'css/plugins/magnific-popup.css');
    Theme::asset()->container('footer')->usePath()->add('magnific-popup-js', 'js/plugins/magnific-popup.js', ['jquery']);

    Theme::asset()->usePath()->add('jquery-ui-css', 'css/plugins/jquery-ui.css');
    Theme::asset()->container('footer')->usePath()->add('jquery-ui-js', 'js/plugins/jquery-ui.js');
@endphp

<div class="product-detail accordion-detail">
    <div class="row mb-50 mt-30">
        <div class="col-md-6 col-sm-12 col-xs-12 mb-md-0 mb-sm-5">
            <div class="detail-gallery">
                <span class="zoom-icon"><i class="fi-rs-search"></i></span>
                <!-- MAIN SLIDES -->
                <div class="product-image-slider">
                    @foreach ($productImages as $img)
                        <figure class="border-radius-10">
                            <a href="{{ RvMedia::getImageUrl($img) }}"><img src="{{ RvMedia::getImageUrl($img, 'medium') }}" alt="{{ $product->name }}"></a>
                        </figure>
                    @endforeach
                </div>
                <!-- THUMBNAILS -->
                <div class="slider-nav-thumbnails">
                    @foreach ($productImages as $img)
                        <div><img src="{{ RvMedia::getImageUrl($img, 'thumb') }}" alt="{{ $product->name }}"></div>
                    @endforeach
                </div>
            </div>
            {!! Theme::partial('social-share', ['url' => $product->url, 'description' => strip_tags(SeoHelper::getDescription())]) !!}
            <a class="mail-to-friend font-sm color-grey" href="mailto:someone@example.com?subject={{ __('Buy') }} {!! BaseHelper::clean($product->name) !!}&body={{ __('Buy this one: :link', ['link' => $product->url]) }}"><i class="fi-rs-envelope"></i> {{ __('Email to a Friend') }}</a>
        </div>
        <div class="col-md-6 col-sm-12 col-xs-12">
            <div class="detail-info pr-30 pl-30">
                @foreach ($product->productLabels as $label)
                    <div class="product-badges">
                        <span @if ($label->color) style="background-color: {{ $label->color }}" @endif>{{ $label->name }}</span>
                    </div>
                @endforeach

                <h2 class="title-detail">{!! BaseHelper::clean($product->name) !!}</h2>
                <div class="product-detail-rating">
                    @if (EcommerceHelper::isReviewEnabled())
                        <a href="#Reviews">
                            <div class="product-rate-cover text-end">
                                <div class="product-rate d-inline-block">
                                    <div class="product-rating" style="width: {{ $product->reviews_avg * 20 }}%"></div>
                                </div>
                                <span class="font-small ml-5 text-muted">({{ __(':count reviews', ['count' => $product->reviews_count]) }})</span>
                            </div>
                        </a>
                    @endif
                </div>
                <div class="clearfix product-price-cover">
                    <div class="product-price primary-color float-left">
                        <span class="current-price text-brand">{{ format_price($product->front_sale_price_with_taxes) }}</span>
                            <span>
                                <span class="save-price font-md color3 ml-15 @if ($product->front_sale_price == $product->price) d-none @endif">
                                    <span class="percentage-off">{{ get_sale_percentage($product->price, $product->front_sale_price) }} {{ __('Off') }}</span>
                                </span>
                                <span class="old-price font-md ml-15 @if ($product->front_sale_price == $product->price) d-none @endif">{{ format_price($product->price_with_taxes) }}</span>
                            </span>
                    </div>
                </div>

                <div class="short-desc mb-30">
                    @if (is_plugin_active('marketplace') && $product->store_id)
                        <p>{{ __('Sold By') }}: <a href="{{ $product->store->url }}"><strong>{!! BaseHelper::clean($product->store->name) !!}</strong></a></p>
                    @endif

                    {!! apply_filters('ecommerce_before_product_description', null, $product) !!}
                    {!! BaseHelper::clean($product->description) !!}
                    {!! apply_filters('ecommerce_after_product_description', null, $product) !!}
                </div>

                <form class="add-to-cart-form" method="POST" action="{{ route('public.cart.add-to-cart') }}">
                    @csrf

                    @if ($product->variations()->count() > 0)
                        <div class="pr_switch_wrap">
                            {!! render_product_swatches($product, [
                                'selected' => $selectedAttrs,
                                'view'     => Theme::getThemeNamespace() . '::views.ecommerce.attributes.swatches-renderer'
                            ]) !!}
                        </div>
                    @endif

                    {!! render_product_options($product) !!}

                    {!! Theme::partial('product-availability', compact('product', 'productVariation')) !!}

                    {!! apply_filters(ECOMMERCE_PRODUCT_DETAIL_EXTRA_HTML, null, $product) !!}
                    <input type="hidden" name="id" class="hidden-product-id" value="{{ ($product->is_variation || !$product->defaultVariation->product_id) ? $product->id : $product->defaultVariation->product_id }}"/>
                    <div class="detail-extralink mb-50">
                        @if (EcommerceHelper::isCartEnabled())
                            <div class="detail-qty border radius">
                                <a href="#" class="qty-down"><i class="fi-rs-angle-small-down"></i></a>
                                <input type="number" min="1" value="1" name="qty" class="qty-val qty-input" />
                                <a href="#" class="qty-up"><i class="fi-rs-angle-small-up"></i></a>
                            </div>
                        @endif

                        <div class="product-extra-link2 @if (EcommerceHelper::isQuickBuyButtonEnabled()) has-buy-now-button @endif">
                            @if (EcommerceHelper::isCartEnabled())
                                <button type="submit"
                                    class="button button-add-to-cart @if ($product->isOutOfStock()) btn-disabled @endif"
                                    @if ($product->isOutOfStock()) disabled @endif><i class="fi-rs-shopping-cart"></i>{{ __('Add to cart') }}</button>
                                @if (EcommerceHelper::isQuickBuyButtonEnabled())
                                    <button class="button button-buy-now ms-2 @if ($product->isOutOfStock()) btn-disabled @endif"
                                        type="submit" name="checkout"
                                        @if ($product->isOutOfStock()) disabled @endif>{{ __('Buy Now') }}</button>
                                @endif
                            @endif

                            @if (EcommerceHelper::isWishlistEnabled())
                                <a aria-label="{{ __('Add To Wishlist') }}" class="action-btn hover-up js-add-to-wishlist-button" data-url="{{ route('public.wishlist.add', $product->id) }}" href="#"><i class="fi-rs-heart"></i></a>
                            @endif
                            @if (EcommerceHelper::isCompareEnabled())
                                <a aria-label="{{ __('Add To Compare') }}" href="#" class="action-btn hover-up js-add-to-compare-button" data-url="{{ route('public.compare.add', $product->id) }}"><i class="fi-rs-shuffle"></i></a>
                            @endif
                        </div>
                    </div>
                </form>
                <div class="font-xs">

                    <ul class="mr-50 float-start">

                        <li class="mb-5 @if ($product->sku) d-none @endif" id="product-sku">
                            <span class="d-inline-block">{{ __('SKU') }}</span>: <span class="sku-text">{{ $product->sku }}</span>
                        </li>

                        @if ($product->categories->count())
                            <li class="mb-5">
                                <span class="d-inline-block">{{ __('Categories') }}: </span>
                                @foreach($product->categories as $category)
                                    <a href="{{ $category->url }}" title="{{ $category->name }}">{!! BaseHelper::clean($category->name) !!}</a>@if (!$loop->last),@endif
                                @endforeach
                            </li>
                        @endif
                        @if ($product->tags->count())
                            <li class="mb-5">
                                <span class="d-inline-block">{{ __('Tags') }}: </span>
                                @foreach($product->tags as $tag)
                                    <a href="{{ $tag->url }}" rel="tag" title="{{ $tag->name }}">{{ $tag->name }}</a>@if (!$loop->last),@endif
                                @endforeach
                            </li>
                        @endif

                        @if ($product->brand->id)
                            <li class="mb-5">
                                <span class="d-inline-block">{{ __('Brands') }}: </span>
                                <a href="{{ $product->brand->url }}" title="{{ $product->brand->name }}">{{ $product->brand->name }}</a>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
            <!-- Detail Info -->
        </div>
    </div>
    <div class="product-info">
        <div class="tab-style3">
       
            <div class="tab-content shop_info_tab entry-main-content">
                <div class="tab-pane fade show active" id="Description">
                    {!! BaseHelper::clean($product->content) !!}
               
                </div>
                


               
            </div>
        </div>
    </div>
    @php
        $crossSellProducts = get_cross_sale_products($product, $layout == 'product-full-width' ? 4 : 3);
    @endphp
    @if (count($crossSellProducts) > 0)
        <div class="row mt-60">
            <div class="col-12">
                <h3 class="section-title style-1 mb-30">{{ __('You may also like') }}</h3>
            </div>
            @foreach($crossSellProducts as $crossProduct)
                <div class="col-lg-{{ 12 / ($layout == 'product-full-width' ? 4 : 3) }} col-md-4 col-12 col-sm-6">
                    @include(Theme::getThemeNamespace() . '::views.ecommerce.includes.product-item', ['product' => $crossProduct])
                </div>
            @endforeach
        </div>
    @endif

    <div class="row mt-60" id="related-products">
        <div class="col-12">
            <h3 class="section-title style-1 mb-30">{{ __('Related products') }}</h3>
        </div>
        <related-products-component url="{{ route('public.ajax.related-products', $product->id) }}" :limit="{{ $layout == 'product-full-width' ? 4 : 3 }}"></related-products-component>
    </div>
</div>
