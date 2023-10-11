@if (is_plugin_active('ecommerce'))
    @php
        Theme::asset()->usePath()->add('jquery-ui-css', 'css/plugins/jquery-ui.css');
        Theme::asset()->container('footer')->usePath()->add('jquery-ui-js', 'js/plugins/jquery-ui.js');
        Theme::asset()->container('footer')->usePath()->add('jquery-ui-touch-punch-js', 'js/plugins/jquery.ui.touch-punch.min.js');
    @endphp
    <form action="{{ URL::current() }}" method="GET" id="products-filter-ajax">
        <div class="sidebar-widget price_range range mb-30 widget-filter-item" data-type="price">
        <div class=" widget-filter-item">
        @hello('Pourquoi ?,why,pour le local,pour ma famille,pour ma planète,pour ma santé')
        <br/>
        @hello('ou ?,where,à la maison,au bureau,en activité plein air,en cuisine')
        <br/>
        @hello('Comment ?,how,brut,en salé,en sucré,plutôt épicé')
        <br/>
        @hello('Quoi ?,what,de la farine,du grain,en craker,snaking energie')
        <br/>
        @hello('Qui ?,who,coeliaque,curieux,faible en charge glycémique,gourmand(e)')
        <br/>
        @hello('Quand ?,when,coeliaque,curieux,faible en charge glycémique,gourmand(e)')
        <br/>
        @hello('Combien ?,how_many,coeliaque,curieux,faible en charge glycémique,gourmand(e)')


            </div>
           
        
           
            <button class="btn btn-sm btn-default mt-3"><i class="fi-rs-filter mr-5"></i> {{ __('Filter') }}</button>
        </div>
    </form>
@endif
