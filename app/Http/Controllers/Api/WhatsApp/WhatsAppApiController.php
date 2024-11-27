<?php
namespace App\Http\Controllers\Api\WhatsApp;

set_time_limit(0);

use App\Events\handleOnAppWhatsappMessage;
use Exception;
use App\Models\TblScheme;
use App\Library\Utilities;
use Illuminate\Http\Request;
use App\Models\TblSaleCoupons;
use App\Models\TblSaleSchemes;
use App\Models\TblSchemeCoupon;
use App\Models\TblWhatsAppWords;
use App\Models\TblWhatsAppContact;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\TblWhatsAppGroupContacts;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class WhatsAppApiController extends Controller 
{
    function response(Request $request , $link = null){
        $data = [];
        $link = str_replace('^' , '/' , $link);
        $link = str_replace('*' , '?' , $link);
        
        $response = file_get_contents($link);
        return $response;
    }

    // Validate Our Webhook Address
    public function validateResponse(Request $request){
        $mode = $request->hub_mode;
        $token = $request->hub_verify_token;
        $challnge = (int)$request->hub_challenge;
        if(isset($mode) && isset($token)){
            if ($mode === "subscribe" && $token === config('whatsapp.verify_token')) {
                return response()->json($challnge , 200);
            }else{
                return response()->json(NULL , 403);
            }
        }
    }

    // Handle WhatsApp WebHooks Responses
    function handleWebhookResponse(Request $request){
        $preSetWordsList = [];
        $object = $request->object;
        $entry = $request->entry;

        // Record Webhook Response
        // Log::channel('whatsapp')->info('Webhook Reponse :' , [$entry]);
        // $dir = $_SERVER['DOCUMENT_ROOT'];
        // $myfile = fopen($dir . '/webhookresponse.txt', "w") or die("Unable to open file!");
        // fwrite($myfile, json_encode($request));
        // fclose($myfile);
        
        $responseId = $entry[0]['id'];
        $changes = $entry[0]['changes'];
        $mainData = $changes[0]['value'];

        $metaData = $mainData['metadata'];
        $displayPhoneNumber = $metaData['display_phone_number'];
        $displayName = $mainData['contacts'][0]['profile']['name'] ?? 'Unknown';
        $phoneNumberId = $metaData['phone_number_id'];
        $messages = $mainData['messages'] ?? [];

        //Pre Set Words
        $wordsFromDb = TblWhatsAppWords::where('is_active' , 'YES')->get();
    
        foreach ($wordsFromDb as $word) {
            array_push($preSetWordsList , strtolower($word->word_name));    
        }
        foreach ($messages as $message) {
            if($message['type'] == 'text'){
                $from = $message['from'];
                $text = strtolower($message['text']['body']);
                $messageId = $message['id'];
                $couponId = time();
                $customer = TblWhatsAppContact::where('phone_no' , $from)->first();
                if(!isset($customer)){
                    DB::beginTransaction();
                        $customer = new TblWhatsAppContact();
                        $customer->cnt_id = Utilities::uuid();
                        $customer->grp_id = 29117122191833; // Customer Group
                        $customer->cnt_name = $displayName;
                        $customer->is_verified = 1;
                        $customer->is_active = 1;
                        $customer->phone_no = $from;
                        $customer->country_id = 1;
                        $customer->business_id = 1;
                        $customer->company_id = 1;
                        $customer->branch_id = 1;
                        $customer->save();
                        
                        // Add Newly Added Phone No To Customer Group
                        TblWhatsAppGroupContacts::updateOrCreate([
                            'phone_no' => $from , 'grp_id' => 29117122191833
                        ],[
                            'group_contact_id' => Utilities::uuid()
                        ]);
                    DB::commit();
                }
                
                // First Save Message In DB
                DB::beginTransaction();
                    $saveMessage = $this->saveWhatsAppMessage($entry, $message['id'], $message['from'], $message['type'], $message['text']['body'], 0, 1,'unread');
                DB::commit();
                // Register New Event For Pusher
                try {
                    event(new handleOnAppWhatsappMessage($saveMessage));
                } catch (Exception $e) {
                    Log::error("Unable to create Event : " , [ $e->getMessage() ]);
                }
                
                $activeSchemeId = TblScheme::where('is_active' , 'YES')->orderBy('created_at')->first('scheme_id');
                $activeSchemeId = $activeSchemeId->scheme_id ?? 0;
                if($text == 'add me' || $text == 'اضافتي' || $text == 'اضافتى'){
                    $exists = TblSchemeCoupon::where('scheme_id' , $activeSchemeId)->where('customer_id' , $from)->first();
                    if(isset($exists)){
                        $couponNo = $exists->coupon_no;
                        $couponStatus = $exists->coupon_status;
                        if($couponStatus == 'ACTIVE'){
                            $components = [
                                [
                                    "type" => "header",
                                    "parameters" => [
                                        [
                                            "type"=> "IMAGE",
                                            "image"=> [
                                                "link"=> 'https://api.atayebatgroup.com/media/coupon.jpeg'
                                            ]
                                        ]
                                    ]
                                ],
                                [
                                    "type" => "body",
                                    "parameters" => [
                                        [
                                            "type"=> "text",
                                            "text" => $couponNo
                                        ]
                                    ]
                                ]
                            ];
                            
                            $this->sendWhatsAppTemplate('discount_coupon_already_sent ' , $from , 'ar' , $components);
                            try {
                                $saveTemplate = $this->saveWhatsAppMessage([] , 'template' , $from , 'template' , 'Template Sent : discount_coupon_already_sent' , 1 , 0 , 'sent');
                                event(new handleOnAppWhatsappMessage($saveTemplate));
                            } catch (Exception $e) {
                                Log::error("Unable to create Event : " , [ $e->getMessage() ]);
                            }

                        }else{
                            $components = [
                                [
                                    "type" => "body",
                                    "parameters" => [
                                        [
                                            "type"=> "text",
                                            "text" => $couponNo
                                        ]
                                    ]
                                ]
                            ];

                            $this->sendWhatsAppTemplate('discount_coupon_already_used' , $from , 'ar' , $components);
                            try {
                                $saveTemplate = $this->saveWhatsAppMessage([] , 'template' , $from , 'template' , 'Template Sent : discount_coupon_already_used' , 1 , 0 , 'sent');
                                event(new handleOnAppWhatsappMessage($saveTemplate));
                            } catch (Exception $e) {
                                Log::error("Unable to create Event : " , [ $e->getMessage() ]);
                            }
                        }
                    }else{
                        $components = [
                            [
                                "type" => "header",
                                "parameters" => [
                                    [
                                        "type"=> "IMAGE",
                                        "image"=> [
                                            "link"=> 'https://api.atayebatgroup.com/media/coupon.jpeg'
                                        ]
                                    ]
                                ]
                            ],
                            [
                                "type" => "body",
                                "parameters" => [
                                    [
                                        "type"=> "text",
                                        "text" => $couponId
                                    ]
                                ]
                            ]
                        ];

                        // Send Him Coupon
                        $this->sendWhatsAppTemplate('discount_coupon_add_me ' , $from , 'ar' , $components);
                        try {
                            $saveTemplate = $this->saveWhatsAppMessage([] , 'template' , $from , 'template' , 'Template Sent : add_me' , 1 , 0 , 'sent');
                            event(new handleOnAppWhatsappMessage($saveTemplate));
                        } catch (Exception $e) {
                            Log::error("Unable to create Event : " , [ $e->getMessage() ]);
                        }
                        
                        // Inform About Number Added To System
                        $this->sendWhatsAppTemplate('number_added_in_system' , $from , 'ar');
                        try {
                            $saveTemplate = $this->saveWhatsAppMessage([] , 'template' , $from , 'template' , 'Template Sent : number_added_in_system' , 1 , 0 , 'sent');
                            event(new handleOnAppWhatsappMessage($saveTemplate));
                        } catch (Exception $e) {
                            Log::error("Unable to create Event : " , [ $e->getMessage() ]);
                        }
                        
                        // Insert The Customer Mobile No. In Our System.
                        DB::beginTransaction();
                            $insert = new TblSchemeCoupon();
                            $insert->coupon_id  = Utilities::uuid();
                            $insert->scheme_id = $activeSchemeId;
                            $insert->slab_id = 1;
                            $insert->coupon_no = $couponId;
                            $insert->customer_id = $from;
                            $insert->ref_no = TblSchemeCoupon::max('ref_no') + 1;
                            $insert->remarks = 'OFFER COUPON';
                            $insert->coupon_status = 'ACTIVE';
                            $insert->issue_date = date('Y-m-d');
                            $insert->expiry_date = date('Y-m-d' , strtotime('+1 month'));
                            $insert->branch_id = 1;
                            $insert->business_id = 1;
                            $insert->company_id = 1;
                            $insert->customer_mobile = $from;
                            $insert->save();
                        DB::commit();

                        return true;
                    }
                    return true;
                }
                if($text == 'location'){
                    $saveTemplate = $this->saveWhatsAppMessage([] , 'template' , $from , 'template' , 'Template Sent : Location' , 1 , 0 , 'sent');
                    try {
                        Log::debug("Registeing Event");
                        event(new handleOnAppWhatsappMessage($saveTemplate));
                    } catch (Exception $e) {
                        Log::error("Unable to create Event : " , [ $e->getMessage() ]);
                    }

                    // Send Branch List
                    return true;
                }
                if($text == 'help' || $text == 'مساعدة'){
                    // Send One message to customer and managment
                    $this->sendWhatsAppTemplate('help_message_receive' , $from , 'ar');                    
                    try {
                        $saveTemplate = $this->saveWhatsAppMessage([] , 'tamplate' , $from , 'template' , 'Template Sent : Help Message' , 1, 0, 'sent');
                        event(new handleOnAppWhatsappMessage($saveTemplate));
                    } catch (Exception $e) {
                        Log::error("Unable to create Event : " , [ $e->getMessage() ]);
                    }

                    $customerNeedHelpSentence = "Sir! A customer Need Some Help! Mobile No.: " . '+'.$from . " Please check and assist him/her. Thanks.";
                    
                    $this->sendWhatsAppText($customerNeedHelpSentence , "96890640940"); // Mr. Yousaf
                    $this->sendWhatsAppText($customerNeedHelpSentence , "96899828227"); // Salim Boss
                    return true;
                }

                // $this->sendWhatsAppTemplate('unknown_message_receive' , $from , 'ar');
                // try {
                //     $saveTemplate = $this->saveWhatsAppMessage([] , 'template' , $from , 'template' , 'Template Sent : unknown_message_receive' , 1 , 0 , 'sent');
                //     event(new handleOnAppWhatsappMessage($saveTemplate));
                // } catch (Exception $e) {
                //     Log::error("Unable to create Event : " , [ $e->getMessage() ]);
                // }
                return false;
            }  
        }

        // Error Handeling
        if(isset($changes[0]['statuses'])){
            $statuses = $changes[0]['statuses'];
            if($statuses[0]->status == 'failed'){
                $errorMessage = $statuses[0]->errors[0]->title;
                Log::error('WhatsApp Message Filed To Sent :' , [$errorMessage]);
            }else{
                Log::debug('Message is Sent To :' , [$changes[0]['statuses']->id]);
            }
        }
    }

    // Send WhatsApp Template Message - It Receive Template Components
    public static function sendWhatsAppTemplate($template , $to , $lang = 'en_US' , $params = []){
        if(count($params) > 0){
            $components = '"components" : ' . json_encode($params);
        }else{
            $components = '"components" : []';
        }
        
        try {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://graph.facebook.com/v13.0/'. config('whatsapp.phone_number_id') .'/messages',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS =>'{
                    "messaging_product": "whatsapp",
                    "to": "'.$to.'",
                    "type": "template",
                    "template": {
                        "name": "'.$template.'",
                        "language": {
                            "code": "'.$lang.'"
                        },
                        '.$components.'
                    },
                }',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer ' . config('whatsapp.whatsapp_token'),
                    'Content-Type: application/json'
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            return $response;
        } catch (Exception $e) {
            return $e->getMessage();
        }
        
    }
    
    // Send WhatsApp Simple Text Message
    public static function sendWhatsAppText($text , $to , $lang = 'en_US' , $params = []){
        if(count($params) > 0){
            $components = '"components" : ' . json_encode($params);
        }else{
            $components = '"components" : []';
        }
        
        try {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://graph.facebook.com/v13.0/'. config('whatsapp.phone_number_id') .'/messages',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS =>'{
                    "messaging_product": "whatsapp",
                    "preview_url": false,
                    "recipient_type": "individual",
                    "to": "'.$to.'",
                    "type": "text",
                    "text": {
                        "body": "'.$text.'"
                    }
                }',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer ' . config('whatsapp.whatsapp_token'),
                    'Content-Type: application/json'
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            return $response;
        } catch (Exception $e) {
            return $e->getMessage();
        }
        
    }

    // Send WhatsApp Location Message
    public static function sendWhatsAppLocation($to , $lat, $lng , $name, $address){
        try {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://graph.facebook.com/v13.0/'. config('whatsapp.phone_number_id') .'/messages',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS =>'{
                    "messaging_product": "whatsapp",
                    "recipient_type": "individual",
                    "to": "'.$to.'",
                    "type": "location",
                    "location": {
                        "latitude": "'.$lat.'",
                        "longitude": "'.$lng.'",
                        "name": "'. $name .'",
                        "address": "'. $address .'"
                    }
                }',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer ' . config('whatsapp.whatsapp_token'),
                    'Content-Type: application/json'
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            return $response;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    // Send Custom Bill File
    public function sendWhatsAppDocument($text, $link, $to){
        $link = str_replace("^" , "/" , $link);
        $link = 'https://' . $link; 
        try {
            if($this->does_url_exists($link)){
                $components = [
                    [
                        "type" => "header",
                        "parameters" => [
                            [
                                "type"=> "document",
                                "document"  => [
                                    "link"      => $link,
                                ]
                            ]
                        ]
                    ]
                ];
                $response = $this->sendWhatsAppTemplate('send_customer_shop_bill' , $to , 'ar' , $components);
                return $response;
            }else{
                return $this->jsonErrorResponse(['link' => $link] , 'File does not Exist!' , 200);
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    // BroadCast WhatsApp Offers To All Customers.
    public function sendWhatsAppOfferFile(Request $request , $link){
        $link = str_replace("^" , "/" , $link);
        $link = 'https://' . $link; 
        try {
            if($this->does_url_exists($link)){

                $template = '';
                $extention = pathinfo($link, PATHINFO_EXTENSION);
                if($extention == 'pdf'){
                    $components = [
                        [
                            "type" => "header",
                            "parameters" => [
                                [
                                    "type"=> "DOCUMENT",
                                    "document"=> [
                                        "link"      => $link,
                                        "filename" => "Special Offer - عرض خاص"
                                    ]
                                ]
                            ]
                        ],
                    ];
                   $template = 'sent_offer_pdf';
                }
                if($extention == 'jpg' || $extention == 'jpeg' || $extention == 'png'){
                    $components = [
                        [
                            "type" => "header",
                            "parameters" => [
                                [
                                    "type"=> "IMAGE",
                                    "image"=> [
                                        "link"      => $link
                                    ]
                                ]
                            ]
                        ],
                    ];
                    $template = 'sent_offer_img'; 
                }
                
                $contacts = TblWhatsAppContact::get();
                foreach ($contacts as $contact) {
                    $response = $this->sendWhatsAppTemplate($template ,$contact->phone_no , 'en' , $components);
                    sleep(1);
                    dump($response);
                }
            }else{
                return $this->jsonErrorResponse(['link' => $link] , 'File does not Exist!' , 200);
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    // Mark Message As Read By Message ID
    private function markMessageRead($id){
        try {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://graph.facebook.com/v13.0/'. config('whatsapp.phone_number_id') .'/messages',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'PUT',
                CURLOPT_POSTFIELDS =>'{
                    "messaging_product": "whatsapp",
                    "status": "read",
                    "message_id": "'.$id.'"
                }',
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . config('whatsapp.whatsapp_token'),
                ),
            ));
            
            $response = curl_exec($curl);
            curl_close($curl);
            return  $response;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    // Check If the File is Exist on given URL
    function does_url_exists($url) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
        if ($code == 200) {
            $status = true;
        } else {
            $status = false;
        }
        curl_close($ch);
        return $status;
    }
}
