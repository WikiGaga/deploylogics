<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use VwAccoVoucher;
use ArPHP\I18N\Arabic;
use App\Models\TblAccCoa;
use App\Library\Utilities;
use App\Models\TblAccBudget;
use App\Models\TblAccoVoucher;
use App\Models\ViewAccoVoucher;
use Illuminate\Support\Facades\DB;
use App\Models\TblDefiShortcutKeys;
use Illuminate\Support\Facades\Log;
use App\Models\TblDefiConfiguration;
use Illuminate\Support\Facades\Auth;
use App\Models\TblSoftUserActivityLog;
use App\Models\TblWhatsAppChat;
use App\Models\TblWhatsAppContact;
use App\Models\Languages;
use Illuminate\Support\Facades\Session;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use DateTime;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public $getDataSession;
    public $prefixIndexPage = '/listing/';
    public $prefixCreatePage = '/form';

    public function __construct()
    {
        if(!isset(auth()->user()->branch_id)){
            Auth::logout();
            return redirect('/login');
        }

    }

    public static function arabicText($text){
        $arabic = new Arabic();
        return $arabic->utf8Glyphs($text);
    }

    public function returnJsonSucccess($message,$status){
        $data = [ 'message'=>$message, 'status'=>$status ];
        return response()->json($data,200);
    }

    public function returnJsonError($message,$status){
        $data = [ 'message'=>$message, 'status'=>$status ];
        return response()->json($data,200);
    }
    /*
        Status   Code                   Meaning	Scenario
        200	    OK	                    The request was processed successfully
        400	    Bad Request	            Your request contained invalid or missing data
        401	    Unauthorized	        Authentication failed or the Authenticate header was not provided
        404	    Not Found	            The URI does not match any of the recognised resources, or, if you are asking for a specific resource with an ID, that resource does not exist
        405	    Method Not Allowed	    The HTTP request method you are trying to use is not allowed. Make an OPTIONS request to see the allowed methods
        406	    Not Acceptable	        The Accept content type you are asking for is not supported by the REST API
        415	    Unsupported Media Type	The Content-Type header is not supported by the REST API
    */
    protected function jsonSuccessResponse( $data, $message, $statusCode = 200 )
    {
        return response()->json(['status'=>'success', 'data'=>$data, 'message'=>$message], $statusCode);
    }

    protected function jsonErrorResponse( $data, $message,  $statusCode = 200)
    {
        return response()->json(['status'=>'error', 'data'=>$data, 'message'=>$message], $statusCode);
    }

    public function switchEntry($arr)
    {
        $code = isset($arr['code'])?$arr['code']:"";
        $table_name = $arr['table_name'];
        $col_id = $arr['col_id'];
        $col_code = $arr['col_code'];
        $biz_type = $arr['biz_type'];
        $link = $arr['link'];
        $where_type = '';
        if(isset($arr['code_type_field']) && isset($arr['code_type'])){
            $where_type = " AND ".$arr['code_type_field']." = '".$arr['code_type']."'";
        }

        $list = [];
        $BCB = "business_id = ".auth()->user()->business_id." AND company_id = ".auth()->user()->company_id;
        if($biz_type == 'branch'){ $BCB .= " AND branch_id = ".auth()->user()->branch_id; }
        $list['first'] = "javascript:;";
        $list['last'] = "javascript:;";
        $list['next'] = "javascript:;";
        $list['prev'] = "javascript:;";

        $firstData = DB::selectOne("select $col_id as id from $table_name where $BCB $where_type order by $col_code asc fetch first 1 rows only");
        if(!empty($firstData)){
            $list['first'] = $link.'/'.$firstData->id;
        }
        $lastData = DB::selectOne("select $col_id as id from $table_name where $BCB $where_type order by $col_code desc fetch first 1 rows only");
        if(!empty($lastData)){
            $list['last'] = $link.'/'.$lastData->id;
        }
        $nextData = DB::selectOne("select $col_id as id from $table_name where $BCB $where_type AND $col_code > '$code' ORDER BY $col_code asc fetch first 1 row only");
        if(!empty($nextData)){
            $list['next'] = $link.'/'.$nextData->id;
        }
        $prevData = DB::selectOne("select $col_id as id from $table_name where $BCB $where_type AND $col_code < '$code' ORDER BY $col_code desc fetch first 1 row only");
        if(!empty($prevData)){
            $list['prev'] = $link.'/'.$prevData->id;
        }
        return $list;
    }

	// uuid function
    function uuid(){
        $length = 3;
        $x = ''; $y = '';

        for($i = 1; $i <= 3; $i++){
            $x .= random_int(0,255);
        }
        for($i = 1; $i <= 3; $i++){
            $y .= random_int(256,999);
        }
        $random1 = substr($x, 0, $length);
        $random2 = substr($y, 0, $length);
        $year  = date("y", time());
        $day  = date("d", time());
        $hours  = date("H", time());
        $second  = date("s", time());

        $uuid = $random1.$random2.$year.$day.$hours.$second;
        return $uuid;
    }
    public function documentCode($max,$type){
        // max = "SP-0000000", type = "SP"
        if(!empty($max)){
            $max = explode('-',$max);
            $max = end($max);
            $max = $max+1;
        }else{
            $max = 1;
        }
        $new_code =  sprintf("%'07d", $max); // return 12 to 0000012
        $code = strtoupper($type)."-".$new_code; // return "SP-0000012"
        return $code; // return "SP-0000012"
    }
    public function voucherNumber($max,$type){
       //  max = JV-00-00-00000
        if(!empty($max)){
            $max = explode('-',$max);
            $max = end($max);
            $max = $max+1;
        }else{
            $max = 1;
        }
        $new_voucher = sprintf("%'05d", $max);
        $month = date('m');
        $day = date('d');

        $code = strtoupper($type).'-'.$month.'-'.$day.'-'.$new_voucher;
        return $code;
    }

    public function addNo($str){
        $no = str_replace( ',', '', $str);
        $no = (Float)$no;
        return $no;
    }


    public function proPurcChartInsert($level_no,$parent_account_code,$business_id,$company_id,$branch_id,$user_id,$chart_name){
        $pdo = DB::getPdo();
        $account_id = 0;
        $stmt = $pdo->prepare("begin ".Utilities::getDatabaseUsername().".PRO_PURC_CHART_INSERT(:p1, :p2, :p3, :p4, :p5, :p6, :p7, :p8); end;");
        $stmt->bindParam(':p1', $level_no);
        $stmt->bindParam(':p2', $parent_account_code);
        $stmt->bindParam(':p3', $business_id);
        $stmt->bindParam(':p4', $company_id);
        $stmt->bindParam(':p5', $branch_id);
        $stmt->bindParam(':p6', $user_id);
        $stmt->bindParam(':p7', $chart_name);
        $stmt->bindParam(':p8', $account_id,\PDO::PARAM_INT);
        $stmt->execute();
        return $account_id;
    }
    public function proPurcChartUpdate($business_id,$company_id,$branch_id,$chart_name,$uuid){
        $pdo = DB::getPdo();
        $stmt = $pdo->prepare("begin ".Utilities::getDatabaseUsername().".PRO_PURC_CHART_UPDATE(:p1, :p2, :p3, :p4, :p5); end;");
        $stmt->bindParam(':p1', $business_id);
        $stmt->bindParam(':p2', $company_id);
        $stmt->bindParam(':p3', $branch_id);
        $stmt->bindParam(':p4', $chart_name);
        $stmt->bindParam(':p5', $uuid);
        $stmt->execute();
    }
    public function proPurcChartDelete($business_id,$company_id,$branch_id,$uuid){
        $pdo = DB::getPdo();
        $stmt = $pdo->prepare("begin ".Utilities::getDatabaseUsername().".PRO_PURC_CHART_DELETE(:p1, :p2, :p3, :p4); end;");
        $stmt->bindParam(':p1', $business_id);
        $stmt->bindParam(':p2', $company_id);
        $stmt->bindParam(':p3', $branch_id);
        $stmt->bindParam(':p4', $uuid);
        $stmt->execute();
    }

    public function proAccoVoucherInsert($id,$action,$table_name,$data,$where_clause = null){
        if($action == 'update'){
            if($data['voucher_type'] == 'POS' || $data['voucher_type'] == 'RPOS'){
                TblAccoVoucher::where('voucher_document_id',$id)->delete();
            }else{
                TblAccoVoucher::where('voucher_id',$id)->delete();
            }
        }
        $data['created_at'] = Carbon::now();
        $data['updated_at'] = Carbon::now();
        DB::table($table_name)->insert($data);
    }

    public function proAccoVoucherDelete($id){
        TblAccoVoucher::where('voucher_id',$id)->delete();
    }

    public function ValidateCharCode($ChartArr){
        $ResArr = [];
        $length = count($ChartArr);
        if($length > 0){
            for($i = 0; $i < $length; $i++) {
               $res =  TblAccCoa::where('chart_Account_id',$ChartArr[$i])->exists();
               array_push($ResArr,$res);
            }
            if(in_array(false, $ResArr)){
                return false;
            }else{
                return true;
            }
        }else{
            return false;
        }
    }
    public function ValidateCharAccCodeIds($ChartArr){
        foreach ($ChartArr as $id){
            if(gettype($id) == 'integer'){
                $res =  TblAccCoa::where('chart_account_id',$id)->exists();
            }else{
                $res =  TblAccCoa::where('chart_code',$id)->exists();
            }
            if(empty($res)){
                return ['error'=>true,'id'=>$id];
            }
        }
        return false;
    }
    public function strLower($str){
        $string = strtolower(strtoupper($str));
        return $string;
    }
    public function strUcWords($str){
        $string = strtoupper(trim($str));
        $string = strtolower($string);
        $string = ucwords($string);
        return $string;
    }
    public function strLowerTrim($str){
        $string = strtolower(strtoupper($str));
        $string = str_replace(" ","",$string);
        $string = str_replace("\r","",$string);
        $string = str_replace("\t","",$string);
        return $string;
    }

    public static function getSqlWithBindings($query)
    {
        return vsprintf(str_replace('?', '%s', $query->toSql()), collect($query->getBindings())->map(function ($binding) {
            return is_numeric($binding) ? $binding : "'{$binding}'";
        })->toArray());
    }

    public function get_user_browser()
    {
        $u_agent = $_SERVER['HTTP_USER_AGENT'];
        $bname = 'Unknown';
        $platform = 'Unknown';
        $version= "";

        //First get the platform?
        if (preg_match('/linux/i', $u_agent)) {
            $platform = 'linux';
        }
        elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
            $platform = 'mac';
        }
        elseif (preg_match('/windows|win32/i', $u_agent)) {
            $platform = 'windows';
        }

        // Next get the name of the useragent yes seperately and for good reason
        if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent))
        {
            $bname = 'Internet Explorer';
            $ub = "MSIE";
        }
        elseif(preg_match('/Firefox/i',$u_agent))
        {
            $bname = 'Mozilla Firefox';
            $ub = "Firefox";
        }
        elseif(preg_match('/Chrome/i',$u_agent))
        {
            $bname = 'Google Chrome';
            $ub = "Chrome";
        }
        elseif(preg_match('/Safari/i',$u_agent))
        {
            $bname = 'Apple Safari';
            $ub = "Safari";
        }
        elseif(preg_match('/Opera/i',$u_agent))
        {
            $bname = 'Opera';
            $ub = "Opera";
        }
        elseif(preg_match('/Netscape/i',$u_agent))
        {
            $bname = 'Netscape';
            $ub = "Netscape";
        }

        // finally get the correct version number
        $known = array('Version', $ub, 'other');
        $pattern = '#(?<browser>' . join('|', $known) .
            ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
        if (!preg_match_all($pattern, $u_agent, $matches)) {
            // we have no matching number just continue
        }

        // see how many we have
        $i = count($matches['browser']);
        if ($i != 1) {
            //we will have two since we are not using 'other' argument yet
            //see if version is before or after the name
            if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
                $version= $matches['version'][0];
            }
            else {
                $version= $matches['version'][1];
            }
        }
        else {
            $version= $matches['version'][0];
        }

        // check if we have a number
        if ($version==null || $version=="") {$version="?";}

        return [
           // 'userAgent' => $u_agent,
            'name'      => $bname,
            'version'   => $version,
            'platform'  => $platform,
           // 'pattern'    => $pattern
        ];
    }

    function clean($string) {
        $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.

        return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
     }

    public function userFormLogs($arr){
        TblSoftUserActivityLog::create([
            'user_activity_log_id' => Utilities::uuid(),
            'menu_dtl_id' => $arr['menu_dtl_id'],
            'document_id' => $arr['document_id'],
            'document_name' => $arr['document_name'],
            'activity_form_menu_dtl_id' => isset($arr['activity_form_menu_dtl_id'])?$arr['activity_form_menu_dtl_id']:"",
            'activity_form_id' => isset($arr['activity_form_id'])?$arr['activity_form_id']:"",
            'activity_form_type' => isset($arr['activity_form_type'])?$arr['activity_form_type']:"",
            'action_type' => $arr['action_type'],
            'browser_dtl' => serialize($this->get_user_browser()),
            'ip_address' => $_SERVER['REMOTE_ADDR'],
            'form_data' => $arr['form_data'],
            'remarks' => isset($arr['remarks'])?$arr['remarks']:"",
            'user_id' => auth()->user()->id,
            'business_id' => auth()->user()->business_id,
            'company_id' => auth()->user()->company_id,
            'branch_id' => auth()->user()->branch_id,
        ]);
        /*
         'User IP Address - '.  $_SERVER['REMOTE_ADDR']
         'User Browser detail - '.  $_SERVER['HTTP_USER_AGENT']
            $log = [
                'menu_dtl_id' => self::$menu_dtl_id,
                'document_id' => $id,
                'document_name' => 'product',
                'activity_form_type' => 'product',
                'action_type' => 'update',
                'form_data' => serialize((object)$form_data),
            ];
            $this->userFormLogs($log);
          */
    }

    public function checkAccountBudgetLimit($entryDate, $accountId, $addAmount = 0){
        $budget = [];
        if(!isset($entryDate) || !isset($accountId)){
            return $budget;
        }
        $entryDate = date('Y-m-d' , strtotime($entryDate));
        $budgetRecord = TblAccBudget::with('accounts')->select('budget_start_date','budget_end_date','budget_credit_amount','budget_debit_amount')
        ->where('budget_start_date' , '<=' , $entryDate)
        ->where('budget_end_date' , '>=' , $entryDate)
        ->where('chart_account_id' , $accountId)
        ->where('branch_id' , auth()->user()->branch_id);

        if($budgetRecord->exists()){
            $budgetRecord = $budgetRecord->first();
            $budget['accountId'] = $accountId;
            $budget['creditLimit'] = $budgetRecord->budget_credit_amount;
            $budget['debitLimit'] = $budgetRecord->budget_debit_amount;

            $currentDC = ViewAccoVoucher::select(DB::raw('sum(voucher_debit) - sum(voucher_credit) as balance'))
            ->where('chart_account_id' , $accountId)
            ->where('branch_id' , auth()->user()->branch_id)
            ->whereBetween('voucher_date' , [date('Y-m-d' , strtotime($budgetRecord->budget_start_date)) , date('Y-m-d' , strtotime($budgetRecord->budget_end_date))])
            ->first();
            $balance = $currentDC->balance ?? 0;
            $vDC = '+';
            if($balance < 0){
                $balance = $balance * -1;
                $vDC = '-';
            }
            if($vDC == '+'){
                $limitedBalance = $budgetRecord->budget_debit_amount - ($balance + $addAmount);
                $budget['toAddpercentage'] = ( ($balance + $addAmount) / $budgetRecord->budget_debit_amount ) * 100;
                $budget['usedpercentage'] = ( $balance  / $budgetRecord->budget_debit_amount ) * 100;
            }else{
                $limitedBalance = $budgetRecord->budget_credit_amount - ($balance + $addAmount);
                $budget['toAddpercentage'] = ( ($balance + $addAmount) / $budgetRecord->budget_credit_amount ) * 100;
                $budget['usedpercentage'] = ( $balance  / $budgetRecord->budget_credit_amount ) * 100;
            }
            $budget['addAmount'] = number_format($addAmount , 3);
            $budget['usedBalance'] = number_format($balance , 3);
            $budget['balance'] = number_format($limitedBalance , 3);
            $budget['accountName'] = TblAccCoa::where('chart_account_id' , $accountId)->select('chart_name')->first()->chart_name;
        }

        return $budget;
    }

    public function saveWhatsAppMessage($messageBody = [], $messageId , $from, $messageType, $messageContent, $isSent, $isReceive, $messageStatus , $userId = null , $updateTime = true){
        try {
            DB::beginTransaction();
                $message = new TblWhatsAppChat();
                $message->chat_id = Utilities::uuid();
                $message->message_id = $messageId ?? '';
                $message->message_body = json_encode($messageBody);
                $message->receive_at = date('Y-m-d H:i:s' , time());
                $message->phone_no = $from;
                $message->chat_date = date('Y-m-d');
                $message->message = $messageContent;
                $message->is_sent = $isSent;
                $message->is_receive = $isReceive;
                $message->message_status = $messageStatus ?? 'unread';
                $message->message_type = $messageType ?? 'text';
                $message->user_id = $user_id ?? 0;
                $message->save();

                if($updateTime){
                    // Update Last Message Time
                    $contact = TblWhatsAppContact::where('phone_no', '=', $from)
                    ->update(['last_message' => date('Y-m-d H:i:s')]);
                }
            DB::commit();

            return $message->load('contact');
        } catch (Exception $e) {
            Log::error('WhatsApp Message Filed To Save :' , [ "error" => $e->getMessage() ]);
            return $e->getMessage();
        }
    }

    public static function timeAgo($timestamp){
        //$time_now = mktime(date('h')+0,date('i')+30,date('s'));
        $datetime1=new DateTime("now");
        $datetime2=date_create("@$timestamp");

        $diff=date_diff($datetime1, $datetime2);
        $timemsg='';
        if($diff->y > 0){
            $timemsg = $diff->y .' '. ($diff->y > 1?'Years':'Year');
        }
        else if($diff->m > 0){
            $timemsg = $diff->m .' '. ($diff->m > 1?'Months':'Month');
        }
        else if($diff->d > 0){
            $timemsg = $diff->d .' '. ($diff->d > 1?'Days':'Day');
        }
        else if($diff->h > 0){
            $timemsg = $diff->h .' '. ($diff->h > 1 ? 'Hours':'Hour');
        }
        else if($diff->i > 0){
            $timemsg = $diff->i .' '. ($diff->i > 1?'Mins':'Min');
        }
        else if($diff->s > 0){
            $timemsg = $diff->s .' '. ($diff->s > 1?'Secs':'Sec');
        }
        if($timemsg == ""){
            $timemsg = 'Just Now';
        }
        else{
            if($timestamp > time()){
                $timemsg = $timemsg.' Afterward';
            }else{
                $timemsg = $timemsg.' Ago';
            }
        }

        return $timemsg;
    }
}
