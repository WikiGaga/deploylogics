<?php

namespace App\Providers;
use App\Models\Menu;
use View;
use Illuminate\Support\ServiceProvider;

class MenuServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Using class based composers...
      //  View::composer('profile', 'App\Http\ViewComposers\ProfileComposer');

        // Using Closure based composers...
        View::composer('elements.sidebar', function($view)
        {
            $menus = Menu::with('submenu')
                ->where('menu_id','!=', 0)
                ->where('business_id', auth()->user()->business_id)
                ->orderby('menu_sorting','asc')
                ->get();
            $view->with('menus', $menus) ;
        });
    }
}
