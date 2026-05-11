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
        // $date = date('Y-m-d',(strtotime ( '-1 day' , strtotime ($paras['voucher_date']) ) ));
        // if(is_array($paras['chart_account_id'])){
        //     $chart_account_condition = " chart_account_id in (".implode(",",$paras['chart_account_id']).") "; 
        // }else{
        //     $chart_account_condition = " chart_account_id  = " . $paras['chart_account_id'] ; 
        // }

        // $qry = "select sum(voucher_debit) - sum(voucher_credit) opening_bal from tbl_acco_voucher
        //         where $chart_account_condition
        //         and voucher_date <= to_date('".$date."','yyyy/mm/dd')
        //         and company_id = ".auth()->user()->business_id."
        //         and business_id = ".auth()->user()->company_id."
        //         and branch_id in(".implode(",",$paras['branch_ids']).")";
        // $data = DB::selectOne($qry);
        // return $data->opening_bal;

        $date = date('Y-m-d', strtotime('-1 day', strtotime($paras['voucher_date'])));

        // 1. Robust check for Chart Account ID
        if (is_array($paras['chart_account_id']) && count($paras['chart_account_id']) > 0) {
            $chart_account_condition = " chart_account_id in (" . implode(",", $paras['chart_account_id']) . ") ";
        } elseif (!empty($paras['chart_account_id']) || $paras['chart_account_id'] === 0 || $paras['chart_account_id'] === '0') {
            $chart_account_condition = " chart_account_id = " . $paras['chart_account_id'];
        } else {
            // If it's totally empty, we force a condition that returns 0 
            // or handle it based on your business logic. 
            // Here we force it to look for ID 0 to prevent SQL crash.
            $chart_account_condition = " chart_account_id = 0 ";
        }

        // 2. Robust check for Branch IDs
        $branch_list = (is_array($paras['branch_ids']) && count($paras['branch_ids']) > 0) 
                    ? implode(",", $paras['branch_ids']) 
                    : "0"; // Default to 0 if no branches

        $qry = "select nvl(sum(voucher_debit) - sum(voucher_credit), 0) as opening_bal 
                from tbl_acco_voucher
                where $chart_account_condition
                and voucher_date <= to_date('" . $date . "', 'yyyy/mm/dd')
                and company_id = " . auth()->user()->business_id . "
                and business_id = " . auth()->user()->company_id . "
                and branch_id in (" . $branch_list . ")";

        $data = DB::selectOne($qry);
        
        // Use nvl() in SQL or null coalescing in PHP to ensure a number is returned
        return $data->opening_bal ?? 0;
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
