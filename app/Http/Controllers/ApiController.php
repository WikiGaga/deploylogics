<?php

namespace App\Http\Controllers;

use App\Models\TblDefiConfiguration;
use Illuminate\Http\Request;
use App\Library\ApiUtilities;
use Carbon\Carbon;
use App\Models\TblAccCoa;
use App\Models\TblAccoVoucher;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class ApiController extends BaseController
{
    public function __construct()
    {
        if(!isset(auth()->user()->branch_id)){
            $data = [];
            Auth::logout();
            return $this->ApiJsonErrorResponse($data,'Please Select Branch');
        }
    }

    public function guard()
    {
        return Auth::guard('api');
    }

    protected function ApiJsonSuccessResponse( $data, $message,  $statusCode = 200)
    {
        return response()->json(['data'=>$data, 'message'=>$message, 'status'=>'success',], $statusCode);
    }

    protected function ApiJsonErrorResponse( $data, $message,  $statusCode = 200)
    {
        $data = (object)[];
        return response()->json(['data'=>$data, 'message'=>$message, 'status'=>'error',], $statusCode);
    }

    public function addNo($str){
        $no = str_replace( ',', '', $str);
        $no = (Float)$no;
        return $no;
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

    public function proAccoVoucherInsert($id,$action,$table_name,$data,$where_clause){
        if($action == 'update'){
            $this->proAccoVoucherDelete($id);
        }
        $data['created_at'] = Carbon::now();
        $data['updated_at'] = Carbon::now();
        DB::table($table_name)->insert($data);
    }
    public function proAccoVoucherDelete($id){
        $acc_vouchers = TblAccoVoucher::where('voucher_id',$id)->get();
        foreach($acc_vouchers as $acc_voucher){
            $acc_voucher->delete();
        }
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

    public function addSession(){
        /*if(!session()->has('api_user_branch')){
            $data = [];
            Auth::logout();
            return response()->json([ 'data'=>$data, 'message'=>'Please Select Branch First','status'=>'error']);
        }*/

        Session::forget('ApiDataSession');
        $config = TblDefiConfiguration::first();
        $dataSession = [];
        if(!empty($config)){
            $dataSession = (object)[
                'customer_group' => $config->customer_group,
                'sale_income' => $config->sale_income,
                'sale_discount' => $config->sale_discount,
                'sale_vat_payable' => $config->sale_vat_payable,
                'sale_stock' => $config->sale_stock,
                'sale_stock_consumption' => $config->sale_stock_consumption,
                'supplier_group' => $config->supplier_group,
                'purchase_stock' => $config->purchase_stock,
                'purchase_discount' => $config->purchase_discount,
                'purchase_vat' => $config->purchase_vat,
                'bank_group' => $config->bank_group,
                'cash_group' => $config->cash_group,
                'user_id' => Auth()->user()->id,
               // 'branch_id' => Session::get('api_user_branch'),
                'company_id' => Session::get('api_user_business'),
                'business_id' => Session::get('api_user_business'),
            ];
        }
        session(['ApiDataSession' => $dataSession]);
    }
}
