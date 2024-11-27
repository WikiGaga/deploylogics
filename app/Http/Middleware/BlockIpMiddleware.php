<?php

namespace App\Http\Middleware;

use Closure;

class BlockIpMiddleware
{
    // set IP addresses
    public $blockIps = ['206.42.127.62','182.176.161.39','127.0.0.1'];
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!in_array($request->ip(), $this->blockIps)) {
            return response()->json(['message' => "You don't have permission to access this website."]);
        }
        return $next($request);
    }
}
