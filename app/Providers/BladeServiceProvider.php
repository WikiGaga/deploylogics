<?php

namespace App\Providers;

use App\Services\StagingService;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class BladeServiceProvider extends ServiceProvider
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
        Blade::if('stgaccess', function($formName, $flowId) {
            if(empty($formName) || empty($flowId)){
                return false;
            }
            $service = new StagingService();
            return $service->getUserAccess($formName, $flowId);
        });
        Blade::if('stgaccessUser', function($boolean) {
            return $boolean;
        });
    }
}
