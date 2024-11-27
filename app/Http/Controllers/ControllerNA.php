<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Library\Utilities;
use App\Models\TblAccoVoucher;
use App\Models\TblAccCoa;
use App\Models\TblDefiConfiguration;
use App\Models\TblDefiShortcutKeys;
use App\Models\TblSoftUserActivityLog;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class ControllerNA extends Controller
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
        $BCB = "business_id = 1 AND company_id = 1";
        if($biz_type == 'branch'){ $BCB .= " AND branch_id = 1"; }
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
        $length = 6;
        $x = '';
        for($i = 1; $i <= $length; $i++){
            $x .= random_int(0,255);
        }
        $random = substr($x, 0, $length);
        $year  = date("y", time());
        $day  = date("d", time());
        $hours  = date("H", time());
        $second  = date("s", time());

        $uuid = $random.$year.$day.$hours.$second;
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
    public function proAccoVoucherInsert($id,$action,$table_name,$data,$where_clause){
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
    public function strLower($str){
        $string = strtolower(strtoupper($str));
        return $string;
    }
    public function strUcWords($str){
        $string = strtoupper($str);
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

    public function userFormLogs($arr){
        $log = new TblSoftUserActivityLog();
        $log->user_activity_log_id = Utilities::uuid();
        $log->menu_dtl_id = $arr['menu_dtl_id'];
        $log->document_id = $arr['document_id'];
        $log->document_name = $arr['document_name'];
        $log->activity_form_menu_dtl_id = isset($arr['activity_form_menu_dtl_id'])?$arr['activity_form_menu_dtl_id']:"";
        $log->activity_form_id = isset($arr['activity_form_id'])?$arr['activity_form_id']:"";
        $log->activity_form_type = isset($arr['activity_form_type'])?$arr['activity_form_type']:"";
        $log->action_type = $arr['action_type'];
        $log->browser_dtl = serialize($this->get_user_browser());
        $log->ip_address = $_SERVER['REMOTE_ADDR'];
        $log->form_data = $arr['form_data'];
        $log->remarks = isset($arr['remarks'])?$arr['remarks']:"";
        $log->user_id = 91;
        $log->business_id = 1;
        $log->company_id = 1;
        $log->branch_id = 1;
        $log->save();
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
}