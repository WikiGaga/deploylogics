<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckWanAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = Auth::user();
       $wanAddress = 'erp.risen.com.pk';
        // $wanAddress = '8000';

        if (str_contains($request->url(), $wanAddress) && ($user && $user->ip_address_apply == 0)) {
            Auth::logout();
            abort(403, 'Access Denied');
        }
        return $next($request);
    }
}
