<?php

namespace App\Http\Middleware;

use Closure;
use App\Services\StagingService;
use App\Traits\HasStaging;

class AutoStaging
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
        $response = $next($request);

        // Only process for create/edit routes that return views
        if ($request->isMethod('get') &&
            (strpos($request->route()->getActionName(), '@create') !== false ||
             strpos($request->route()->getActionName(), '@edit') !== false)) {

            // Staging data will be added via View Composer
            // This middleware can be used for additional processing if needed
        }

        return $response;
    }
}
