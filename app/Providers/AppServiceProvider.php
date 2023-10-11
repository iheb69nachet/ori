<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Botble\Ecommerce\Models\ProductInfo;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // $d=ProductInfo::get();
        // dd($d);
        Blade::directive('hello', function ($expression) {
            list($name, $why, $choices) = explode(',', $expression, 3);
            $name = str_replace("'", '', $name);
            $why = str_replace("'", '', $why);
            // dd($name);
            // dd($name);
            $blade = "";
            $h5='<h5 class="mb-20 widget__title" data-title="why">{{ __("By :name", ["name" => "'.$name .'"]) }}</h5>
            ';
            $blade .= $h5;
            
            // $choices = explode(',', $choices); 
            $choices=ProductInfo::where('type',$name)->pluck('name','id');
            // if($name='Qui ?'){
            //     dd($choices);
            // }
            // dd($choices);
            // dd($choices);
            foreach ($choices as $x => $choice) {
                // $choice = str_replace("'", '', $choice);
                $blade .= '<div class="custome-checkbox ">
                    <input class="form-check-input '.$why.'-filter-input" data-id="" data-parent-id=""
                        name="' . $why . '[]"
                        type="checkbox"
                        id="'.$why.'-filter-' . $x . '"
                        value="' . $x . '">
                    <label class="form-check-label" for="'.$why.'-filter-' . $x . '">
                        <span class="d-inline-block">' . $choice. '</span>
                    </label>
                    <br>
                </div>';
            }
            // dd($blade);
        
            return $blade;
        });
        
    
    }
}
