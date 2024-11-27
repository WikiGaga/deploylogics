<?php

namespace App\Http\Middleware;

use App\Library\Utilities;
use Closure;
use Illuminate\Support\Facades\DB;

class CheckBranch
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
        $getDefBranch = Utilities::getDefaultBranches();
        $getOptBranch = Utilities::getOptionalBranches();

        if(count($getOptBranch) == 0){
            session(['user_branch' => $getDefBranch->branch_id]);
            Utilities::addSession('middleware');
        }else{
            if(!session()->has('user_branch')){
                return redirect()->action('HomeController@branchCreate');
            }
        }

        return $next($request);
    }
}
