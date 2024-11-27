<?php

namespace App\Providers;

use App\Models\TblStgFormFlowProcess;
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
        Blade::if('stgaccess', function($stg_id,$flow_id) {
            if(empty($stg_id) || empty($flow_id)){
                return false;
            }
            $useraccess = TblStgFormFlowProcess::where('stg_form_cases_id',$stg_id)
                ->where('stg_flows_id',$flow_id)
                ->where('process_id',auth()->user()->id)
                ->where('process_type','=','App\Models\User')->first();
            if ($useraccess) {
                return true;
            }else{
                return false;
            }
            /*return "<?php echo {$user} ?>";*/
        });
        Blade::if('stgaccessUser', function($boolean) {
            return $boolean;
        });
    }
}
