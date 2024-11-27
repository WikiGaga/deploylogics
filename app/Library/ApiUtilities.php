<?php

namespace App\Library;


use App\Models\TblDefiConfiguration;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class ApiUtilities
{
    public static function uuid()
    {
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
    public static function documentCode($doc_data)
    {
        $biz_type = $doc_data['biz_type'];
        $business_id = $doc_data['business_id'];
        $branch_id = $doc_data['branch_id'];
        $model = $doc_data['model'];
        $code_field = $doc_data['code_field'];
        $code_prefix = $doc_data['code_prefix'];
        if(isset($doc_data['code_type_field']) && isset($doc_data['code_type'])){
            $code_type_field = $doc_data['code_type_field'];
            $code_type = $doc_data['code_type'];
        }
        $modelN = 'App\Models\\'.$model;
        if(isset($code_type_field) && isset($code_type)){
            $max = $modelN::where($code_type_field,$code_type)
                ->where('business_id',$business_id);
        }else{
            $max = $modelN::where('business_id',$business_id);
        }
        $max = $max->where('company_id',$business_id);
        if($biz_type == 'branch'){
            $max = $max->where('branch_id',$branch_id);
        }
        $max = $max->max($code_field);
        // max = "SP-0000000", type = "SP"
        if(!empty($max)){
            $max = explode('-',$max);
            $max = end($max);
            $max = $max+1;
        }else{
            $max = 1;
        }
        $new_code =  sprintf("%'07d", $max); // return 12 to 0000012
        $code = strtoupper($code_prefix)."-".$new_code; // return "SP-0000012"
        return $code; // return "SP-0000012"
    }
    public static function getAllBranches($id=null)
    {
        $data  = DB::table('tbl_soft_user_branch')
            ->join('tbl_soft_branch', 'tbl_soft_branch.branch_id','=','tbl_soft_user_branch.branch_id')
            ->where('user_id', isset($id)?$id:Auth()->user()->id)
            ->select('tbl_soft_user_branch.*','tbl_soft_branch.branch_name','tbl_soft_branch.branch_short_name')
            ->get();
        return $data;
    }
    public static function addSession()
    {
        if(!session()->has('api_user_branch')){
            $data = [];
            Auth::logout();
            return response()->json([ 'data'=>$data, 'message'=>'Please Select Branch First','status'=>'error']);
        }

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
                'branch_id' => Session::get('api_user_branch'),
                'company_id' => Session::get('api_user_business'),
                'business_id' => Session::get('api_user_business'),
            ];
        }
        session(['ApiDataSession' => $dataSession]);
    }
    public static function currentBCB(){
        // current Business Company Branch
       // ->where(Utilities::currentBCB())
      $data = [
          ['business_id',Session::get('ApiDataSession')->business_id],
          ['company_id',Session::get('ApiDataSession')->company_id],
          ['branch_id',Session::get('ApiDataSession')->branch_id]
      ];
      return $data;
   }
   public static function currentBC(){
       // current Business Company
       // where(Utilities::currentBC())
      $data = [
          ['business_id',Session::get('ApiDataSession')->business_id],
          ['company_id',Session::get('ApiDataSession')->company_id]
      ];
      return $data;
   }

   public static function currentB(){
       // current Branch
       // where(Utilities::currentB())
      $data = [
           ['branch_id',Session::get('ApiDataSession')->branch_id]
      ];
      return $data;
   }
}

?>
