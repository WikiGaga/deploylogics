<?php

namespace App\Http\Middleware;

use Closure;
use JWTAuth;
use Exception;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use App\Http\Controllers\Controller;

class JwtMiddleware
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
        $data = [];
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if(!$user) {
                return $this->jsonErrorResponse($data,'User Not Found');
            }
        } catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
                return $this->jsonErrorResponse($data,'Token is Invalid');
            }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
                return $this->jsonErrorResponse($data,'Token is Expired');
            }else{
                return $this->jsonErrorResponse($data,'Authorization Token not found');
            }
        }
        return $next($request);
    }
}
