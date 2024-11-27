<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\WhatsApp\WhatsAppApiController;
use Exception;
use App\Models\TblSoftBranch;
use Illuminate\Console\Command;
use App\Models\TblVerifyReports;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class ClosingDayReportVerification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reportverify:closingday';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verify That Closing Day Report Is Verified Daily.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {   $branches = [1,2,3,4,5];
        $msg = $completeMsg = "";
        try {
            $date = date('Y-m-d');
            foreach ($branches as $branch) {
                $exist = TblVerifyReports::with('branch')
                    ->where('report_name' , 'closing_day')
                    ->whereDate('report_date' , $date)
                    ->where('branch_id' , $branch);      
                if(!$exist->exists()){
                    $branchData = TblSoftBranch::where('branch_id' , $branch)->first();
                    $msg = 'Closing Day Report For Date : ' . $date . ' Is Not Verified For Branch : *' . $branchData->branch_name  . '*';
                    $completeMsg .= 'Closing Day Report For Date : ' . $date . ' Is Not Verified For Branch : *' . $branchData->branch_name  . '*\n\r';
                    // WhatsAppApiController::sendWhatsAppText($msg , 'BRANCH_MANAGER');        
                }
            }
            WhatsAppApiController::sendWhatsAppText($msg , '96899828227');
        } catch (Exception $e) {
            Log::error('Unable To Send WhatsApp Notification' , [$e->getMessage()]);
        }
    }
}
