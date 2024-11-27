<?php

namespace App\Console\Commands;

use Exception;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\TblAccoChequeManagment;
use App\Http\Controllers\Accounts\ChequeManagmentController;
use App\Http\Controllers\Api\WhatsApp\WhatsAppApiController;

class ChequeManagmentNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cheque:notify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cheque The Notifications for Cheques.';

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
    {
        try {
            $notifications = TblAccoChequeManagment::with('dtls')->where('notified' , 0)->where('notify_on' , '<=' ,  date('Y-m-d'))->get();
            foreach ($notifications as $notification) {
                $notifyTo = User::where('id' , $notification->notify_to)->first('mobile_no');
                $mobileNo = $notifyTo->mobile_no;
                if($mobileNo != ""){
                    $msg = '';
                        $cheques = $notification->dtls;
                        foreach ($cheques as $cheque) {
                            $msg .= 'The Cheque *' . $cheque->cheque_no . '* ('.ucfirst($cheque->cheque_managment_dtl_type).'d), clearnace date is ' . date('Y-m-d' , strtotime($cheque->cheque_date)) . ' and its status is ' . $cheque->cheque_status . '. Narration: '. $cheque->cheque_description .'.\n';
                        }
                    $whatsapp = new WhatsAppApiController();
                    $components = [
                        [
                            "type" => "body",
                            "parameters" => [
                                [
                                    "type"=> "text",
                                    "text" => $msg
                                ]
                            ]
                        ]
                    ];
                    try {
                        $response = $whatsapp->sendWhatsAppTemplate('cheque_managment_alert_beta' , $mobileNo , 'en' , $components);
                        $response = json_decode($response);
                        if(isset($response->error)){
                            Log::error('Whatsapp Message Send Error : ' , [$response->error->message]);
                        }else{
                            DB::beginTransaction();
                                $notified = TblAccoChequeManagment::where('cheque_managment_id' , $notification->cheque_managment_id)->first();
                                $notified->notified = 1;
                                $notified->save();
                            DB::commit();
                        }
                        
                    } catch (Exception $e) {
                        Log::error("While Sending Whatsapp Message From Cheque Management :" , [$e->getMessage()] );
                    }
                }
            }
            echo "Notifications has been sent!";
        } catch (Exception $e) {
            Log::error("Error While Sending Cheque Management Notification :" , [ $e->getMessage() ]);
        }
    }
}
