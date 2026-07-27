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
        $date = date('Y-m-d', strtotime('-1 day', strtotime($paras['voucher_date'])));

        if (!empty($paras['chart_condition'])) {
            $chart_account_condition = $paras['chart_condition'];
        } elseif (is_array($paras['chart_account_id']) && count($paras['chart_account_id']) > 0) {
            $chart_account_condition = " chart_account_id in (" . implode(",", $paras['chart_account_id']) . ") ";
        } elseif (!empty($paras['chart_account_id']) || $paras['chart_account_id'] === 0 || $paras['chart_account_id'] === '0') {
            $chart_account_condition = " chart_account_id = " . $paras['chart_account_id'];
        } else {
            $chart_account_condition = " chart_account_id = 0 ";
        }

        $branch_list = (is_array($paras['branch_ids']) && count($paras['branch_ids']) > 0)
                    ? implode(",", $paras['branch_ids'])
                    : "0";

        $qry = "select nvl(sum(voucher_debit) - sum(voucher_credit), 0) as opening_bal
                from tbl_acco_voucher
                where $chart_account_condition
                and voucher_date <= to_date('" . $date . "', 'yyyy/mm/dd')
                and posted = 1
                and business_id = " . auth()->user()->business_id . "
                and company_id = " . auth()->user()->company_id . "
                and branch_id in (" . $branch_list . ")";

        $data = DB::selectOne($qry);

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
                and posted = 1
                and business_id = ".auth()->user()->business_id."
                and company_id = ".auth()->user()->company_id."
                and branch_id = ".$paras['branch_ids']."";
        $data = DB::selectOne($qry);
        return $data->opening_bal;
    }


    static function acco_dispatch_opening_bal($paras){
        $date = date('Y-m-d',(strtotime ( '-1 day' , strtotime ($paras['voucher_date']) ) ));
        if (!empty($paras['chart_condition'])) {
            $chart_account_condition = $paras['chart_condition'];
        } elseif(is_array($paras['chart_account_id'])){
            $chart_account_condition = " chart_account_id in (".implode(",",$paras['chart_account_id']).") ";
        }else{
            $chart_account_condition = " chart_account_id  = " . $paras['chart_account_id'] ;
        }

        $qry = "select nvl(sum(voucher_debit) - sum(voucher_credit), 0) opening_bal from tbl_acco_voucher
                where $chart_account_condition
                and voucher_mode_date <= to_date('".$date."','yyyy/mm/dd')
                and posted = 1
                and business_id = ".auth()->user()->business_id."
                and company_id = ".auth()->user()->company_id."
                and branch_id in(".implode(",",$paras['branch_ids']).")";
        $data = DB::selectOne($qry);
        return $data->opening_bal ?? 0;
    }



}
