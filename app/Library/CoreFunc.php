<?php
/**
 * User: M.Umar
 * Date: 08/Mar/2021
 * Time: 12:20 AM
 */

namespace App\Library;

use Illuminate\Support\Facades\DB;

class CoreFunc
{
    static function acco_opening_bal($paras){
        $date = date('Y-m-d',(strtotime ( '-1 day' , strtotime ($paras['voucher_date']) ) ));
        if(is_array($paras['chart_account_id'])){
            $chart_account_condition = " chart_account_id in (".implode(",",$paras['chart_account_id']).") "; 
        }else{
            $chart_account_condition = " chart_account_id  = " . $paras['chart_account_id'] ; 
        }

        $qry = "select sum(voucher_debit) - sum(voucher_credit) opening_bal from tbl_acco_voucher
                where $chart_account_condition
                and voucher_date <= to_date('".$date."','yyyy/mm/dd')
                and company_id = ".auth()->user()->business_id."
                and business_id = ".auth()->user()->company_id."
                and branch_id in(".implode(",",$paras['branch_ids']).")";
        $data = DB::selectOne($qry);
        return $data->opening_bal;
    }

    static function cash_flow_acco_opening_bal($paras){
        $date = date('Y-m-d',(strtotime ( '-1 day' , strtotime ($paras['voucher_date']) ) ));
        if(is_array($paras['chart_account_id'])){
            $chart_account_condition = " chart_account_id in (".implode(",",$paras['chart_account_id']).") "; 
        }else{
            $chart_account_condition = " chart_account_id  = " . $paras['chart_account_id'] ; 
        }

        $qry = "select sum(voucher_debit) - sum(voucher_credit) opening_bal from tbl_acco_voucher
                where $chart_account_condition
                and voucher_date <= to_date('".$date."','yyyy/mm/dd')
                and company_id = ".auth()->user()->business_id."
                and business_id = ".auth()->user()->company_id."
                and branch_id = ".$paras['branch_ids']."";
        $data = DB::selectOne($qry);
        return $data->opening_bal;
    }


    static function acco_dispatch_opening_bal($paras){
        $date = date('Y-m-d',(strtotime ( '-1 day' , strtotime ($paras['voucher_date']) ) ));
        if(is_array($paras['chart_account_id'])){
            $chart_account_condition = " chart_account_id in (".implode(",",$paras['chart_account_id']).") "; 
        }else{
            $chart_account_condition = " chart_account_id  = " . $paras['chart_account_id'] ; 
        }

        $qry = "select sum(voucher_debit) - sum(voucher_credit) opening_bal from tbl_acco_voucher
                where $chart_account_condition
                and voucher_mode_date <= to_date('".$date."','yyyy/mm/dd')
                and company_id = ".auth()->user()->business_id."
                and business_id = ".auth()->user()->company_id."
                and branch_id in(".implode(",",$paras['branch_ids']).")";
        $data = DB::selectOne($qry);
        return $data->opening_bal;
    }



}
