<?php

namespace App\Providers;
use App\Models\TblSoftMenu;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
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
            $user = auth()->user();
            $businessId = $user->business_id;

            $menus = Cache::remember("sidebar_menus:business:{$businessId}", 3600, function () use ($businessId) {
                return TblSoftMenu::with('submenu')
                    ->where('menu_id', '!=', 0)
                    ->where('menu_id', '!=', 10)
                    ->where('business_id', $businessId)
                    ->orderby('menu_sorting', 'asc')
                    ->get();
            });

            $permissionNames = Cache::remember("sidebar_permissions:user:{$user->id}", 3600, function () use ($user) {
                $direct = DB::table('permission_user')
                    ->join('permissions', 'permissions.id', '=', 'permission_user.permission_id')
                    ->where('permission_user.user_id', $user->id)
                    ->pluck('permissions.name')
                    ->all();

                $viaRoles = DB::table('role_user')
                    ->join('permission_role', 'permission_role.role_id', '=', 'role_user.role_id')
                    ->join('permissions', 'permissions.id', '=', 'permission_role.permission_id')
                    ->where('role_user.user_id', $user->id)
                    ->pluck('permissions.name')
                    ->all();

                return array_values(array_unique(array_merge($direct, $viaRoles)));
            });

            $view->with('menus', $menus);
            $view->with('permissionNames', $permissionNames);
        });
    }
}
