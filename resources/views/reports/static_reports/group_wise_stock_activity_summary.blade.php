@extends('layouts.report')
@section('title', 'Stock Activity Summary')

@section('pageCSS')
    <style>
        /* Styles go here */
        .dtl-head{
            vertical-align: middle !important;
            text-align: center;
        }
        .vertical {
            writing-mode: vertical-rl;
            text-orientation: sideways;
            transform: rotate(180deg);
            font-size:12px;
            font-weight:bold;
        }
        .barnch_color{
            vertical-align: middle;
            text-align: center;
            background: #cffbc7;
        }
        .sale_type_color{
            vertical-align: middle;
            text-align: center;
            background: #fbf9c7;
        }
        .terminal_total{
            background: #deb887;
        }
        .sticky-header-to{
            position: sticky;
    top: 19px;
    background-color: #f7f8fa;
        }
        @media print {
            thead {display: table-header-group;}
            tfoot {display: table-footer-group;}
            tfoot>tr>td {padding:0 !important;}
            body {margin: 0;}
        }
    </style>
@endsection
@section('content')
    @php
        $data = Session::get('data');
    @endphp
    <div class="kt-portlet" id="kt_portlet_table">
        <div class="kt-portlet__head" >
            <div class="kt-invoice__brand">
                <h3 class="kt-invoice__title">{{strtoupper($data['page_title'])}}</h3>
                <h6 class="kt-invoice__criteria">
                    <span style="color: #e27d00;">Date:</span>
                    @if($data['date_time_wise'] == 1)
                        <span style="color: #5578eb;">{{" ".date('d-m-Y', strtotime($data['time_from']))." to ". date('d-m-Y', strtotime($data['time_to']))." "}}</span>
                    @else
                        <span style="color: #5578eb;">{{" ".date('d-m-Y', strtotime($data['from_date']))." to ". date('d-m-Y', strtotime($data['to_date']))." "}}</span>
                    @endif
                </h6>
                @if(count($data['branch_ids']) != 0)
                    @php $branch_lists = \Illuminate\Support\Facades\DB::table('tbl_soft_branch')->whereIn('branch_id',$data['branch_ids'])->get('branch_name'); @endphp
                    <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;">Branch:</span>
                        @foreach($branch_lists as $branch_list)
                            <span style="color: #5578eb;">{{$branch_list->branch_name}}</span><span style="color: #fd397a;">, </span>
                        @endforeach
                    </h6>
                @endif
                @if(count($data['supplier_ids']) != 0)
                    @php $suppliers = \Illuminate\Support\Facades\DB::table('tbl_purc_supplier')->whereIn('supplier_id',$data['supplier_ids'])->get(); @endphp
                    <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;">Supplier:</span>
                        @foreach($suppliers as $supplier)
                            <span style="color: #5578eb;">{{$supplier->supplier_name}}</span><span style="color: #fd397a;">, </span>
                        @endforeach
                    </h6>
                @endif
                @if(count($data['product_ids']) != 0)
                    <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;">Product:</span>
                        @php $i = 0; @endphp
                        @foreach($data['product_ids'] as $product)
                            @php $i++; @endphp
                            @if($i <= 7) 
                                <span style="color: #5578eb;">{{$product}}</span><span style="color: #fd397a;">, </span>
                            @endif
                        @endforeach
                    </h6>
                @endif
                @if(count($data['product_group']) != 0 && $data['product_group'] != "" && $data['product_group'] != null)
                    @php $product_groups = \Illuminate\Support\Facades\DB::table('vw_purc_group_item')->whereIn('group_item_id',$data['product_group'])->get('group_item_name_string'); @endphp
                    <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;">Product Group:</span>
                        @foreach($product_groups as $product_group)
                            <span style="color: #5578eb;">{{$product_group->group_item_name_string}}</span><span style="color: #fd397a;">, </span>
                        @endforeach
                    </h6>
                @endif
            </div>
            @include('reports.template.branding')
        </div>
        <div class="kt-portlet__body">
            <div class="row row-block">
                <div class="col-lg-12">
                    @php

                    $insert_data = "insert into  TBL_INVE_STOCK_dtl  (
                        STOCK_ID,
                        BUSINESS_ID,
                        COMPANY_ID,
                        BRANCH_ID,
                        STOCK_DTL_ID,
                        PRODUCT_ID,
                        UOM_ID,
                        STOCK_DTL_QUANTITY,
                        STOCK_DTL_RATE,
                        STOCK_DTL_AMOUNT,
                        CREATED_AT,
                        UPDATED_AT,
                        PRODUCT_BARCODE_ID,
                        PRODUCT_BARCODE_BARCODE,
                        STOCK_DTL_QTY_BASE_UNIT
                    )
                    SELECT  
                        SALES_ID, 
                        BUSINESS_ID,
                        COMPANY_ID,
                        BRANCH_ID,
                        GET_UUID(),
                        PRODUCT_ID,
                        UOM_ID,
                        TOT_QTY,
                        COST_RATE,
                        COST_AMOUNT ,
                        CREATED_AT,
                        UPDATED_AT,
                        PRODUCT_BARCODE_ID,
                        PRODUCT_BARCODE_BARCODE,
                        TOT_QTY
                    FROM 
                        VW_INVE_DEAL_SALE 
                    WHERE  SALES_ID NOT IN
                    (
                        select distinct  STOCK_ID from TBL_INVE_STOCK where STOCK_CODE_TYPE = 'POS' OR STOCK_CODE_TYPE = 'RPOS' 
                    )";


                    $insert = \Illuminate\Support\Facades\DB::insert($insert_data);

                    $insert_data2 = "insert into  TBL_INVE_STOCK (
                        STOCK_ID,
                        BUSINESS_ID,
                        COMPANY_ID,
                        BRANCH_ID,
                        STOCK_USER_ID,
                        STOCK_ENTRY_STATUS,
                        STOCK_DATE,
                        CREATED_AT,
                        UPDATED_AT,
                        STOCK_CODE,
                        STOCK_CODE_TYPE,
                        STOCK_MENU_ID
                    )
                    SELECT distinct
                        SALES_ID,
                        BUSINESS_ID,
                        COMPANY_ID,
                        BRANCH_ID,
                        SALES_SALES_MAN,
                        1,
                        SALES_DATE,
                        CREATED_AT,
                        UPDATED_AT,
                        SALES_CODE,
                        SALES_TYPE,
                        116
                    FROM 
                        VW_INVE_DEAL_SALE  
                    WHERE  SALES_ID NOT IN
                    (
                        select distinct  STOCK_ID from TBL_INVE_STOCK where STOCK_CODE_TYPE = 'POS' OR STOCK_CODE_TYPE = 'RPOS' 
                    )";

                    $insert2 = \Illuminate\Support\Facades\DB::insert($insert_data2);





                    $where = "";
                    $wheresupplier = "";
                    $inner_where = "";
                    $forsupplier="";
                    $sup="";
                    $groupsup="";

                    if(count($data['supplier_ids']) != 0){
                       // $where .= " and PROD.supplier_id in (".implode(",",$data['supplier_ids']).")";
                        $wheresupplier .= " and SUP_PROD.supplier_id in (".implode(",",$data['supplier_ids']).")";
                    }
                    if(count($data['product_ids']) != 0){
                        $where .= " and PROD.product_name in ('".implode("','",$data['product_ids'])."') ";
                    }
                    if(count($data['product_group']) != 0){
                        $where .= " and PROD.group_item_id in ('".implode("','",$data['product_group'])."') ";
                    }
                          

                    if(count($data['supplier_ids']) != 0){
                        $inner_where =",VW_PURC_PRODUCT_FOC_PROD_WISE SUP_PROD -- for supplier ";
                        $forsupplier = " AND STOCK.PRODUCT_ID = SUP_PROD.PRODUCT_ID -- for supplier";
                        $groupsup = ", SUP_PROD.supplier_id -- supplier case";
                        $sup = ", SUP_PROD.supplier_id -- supplier case ";
                    }
                    if($data['date_time_wise'] == 1){
                        $date_time_from = $data['time_from'];
                        $date_time_to = $data['time_to'];
                    }

                    if($data['date_time_wise'] == 1){
                        $date_field2 = " AND created_at < to_date ('".$date_time_from."', 'yyyy/mm/dd HH24:MI')";
                        $date_field = " AND (created_at between to_date ('".$date_time_from."', 'yyyy/mm/dd HH24:MI') and to_date ('".$date_time_to."', 'yyyy/mm/dd HH24:MI'))";
                    }else{
                        $date_field2 = "AND document_date < to_date ('".$data['from_date']."', 'yyyy/mm/dd')";
                        $date_field = "AND document_date between to_date ('".$data['from_date']."', 'yyyy/mm/dd') and to_date ('".$data['to_date']."', 'yyyy/mm/dd')";
                    }
/*
$query = "select distinct  
    BRANCH_ID,
    BRANCH_NAME,
    PRODUCT_NAME,
    GROUP_ITEM_NAME,
    OPENING_BALANCE,
    PURCHASE,
    STOCK_REC,
    SALE_RETURN,
    TOTAL_STOCK_IN,
    SALE,
    STOCK_TRANSFER,
    PURCH_RETURN,
    STOCK_ADJUSTMENT, 
    TOTAL_STOCK_OUT,
    STOCK_BALANCE 
from
    (SELECT 
        STOCK.BRANCH_ID,
        TBL_SOFT_BRANCH.BRANCH_NAME,
        PROD.PRODUCT_NAME,
        GROUP_ITEM_NAME,
        SUM(OPENING_BALANCE) OPENING_BALANCE,
        SUM(PURCHASE) PURCHASE,
        SUM(STOCK_REC) STOCK_REC,
        SUM(SALE_RETURN) SALE_RETURN,
        SUM(OPENING_BALANCE) + SUM(PURCHASE) + SUM(STOCK_REC) + SUM(SALE_RETURN) TOTAL_STOCK_IN,
        SUM(SALE * - 1) SALE,
        SUM(STOCK_TRANSFER * - 1) STOCK_TRANSFER,
        SUM(PURCH_RETURN * - 1) PURCH_RETURN,
        SUM(STOCK_ADJUSTMENT) STOCK_ADJUSTMENT,
        SUM(SALE * - 1) + SUM(STOCK_TRANSFER * - 1) + SUM(PURCH_RETURN * - 1) + SUM(STOCK_ADJUSTMENT) TOTAL_STOCK_OUT,
        (
        SUM(OPENING_BALANCE) + SUM(PURCHASE) + SUM(STOCK_REC) + SUM(SALE_RETURN)
        ) - (
        SUM(SALE * - 1) + SUM(STOCK_TRANSFER * - 1) + SUM(PURCH_RETURN * - 1) + SUM(STOCK_ADJUSTMENT)
        ) STOCK_BALANCE 
        $sup
    FROM(
        SELECT 
            BRANCH_ID,
            PRODUCT_ID,
            QTY_BASE_UNIT_VALUE OPENING_BALANCE,
            0 PURCHASE,
            0 STOCK_REC,
            0 SALE_RETURN,
            0 SALE,
            0 STOCK_TRANSFER,
            0 PURCH_RETURN,
            0 STOCK_ADJUSTMENT 
        FROM
            VW_PURC_STOCK_DTL 
        WHERE BRANCH_ID in (".implode(",",$data['branch_ids']).") 
            $date_field2
        UNION
        ALL 
        SELECT 
            BRANCH_ID,
            PRODUCT_ID,
            0 OPENING_BALANCE,
            QTY_BASE_UNIT_VALUE PURCHASE,
            0 STOCK_REC,
            0 SALE_RETURN,
            0 SALE,
            0 STOCK_TRANSFER,
            0 PURCH_RETURN,
            0 STOCK_ADJUSTMENT 
        FROM
            VW_PURC_STOCK_DTL 
        WHERE DOCUMENT_TYPE = 'GRN' 
            AND BRANCH_ID IN (".implode(",",$data['branch_ids']).") 
            $date_field
        UNION
        ALL 
        SELECT 
            BRANCH_ID,
            PRODUCT_ID,
            0 OPENING_BALANCE,
            0 PURCHASE,
            QTY_BASE_UNIT_VALUE STOCK_REC,
            0 SALE_RETURN,
            0 SALE,
            0 STOCK_TRANSFER,
            0 PURCH_RETURN,
            0 STOCK_ADJUSTMENT 
        FROM
            VW_PURC_STOCK_DTL 
        WHERE DOCUMENT_TYPE = 'STR'
            AND BRANCH_ID IN (".implode(",",$data['branch_ids']).") 
            $date_field
        UNION
        ALL 
        SELECT 
            BRANCH_ID,
            PRODUCT_ID,
            0 OPENING_BALANCE,
            0 PURCHASE,
            0 STOCK_REC,
            ABS(QTY_BASE_UNIT_VALUE) SALE_RETURN,
            0 SALE,
            0 STOCK_TRANSFER,
            0 PURCH_RETURN,
            0 STOCK_ADJUSTMENT 
        FROM
            VW_PURC_STOCK_DTL 
        WHERE DOCUMENT_TYPE = 'RPOS'
            AND BRANCH_ID IN (".implode(",",$data['branch_ids']).") 
            $date_field
        UNION
        ALL 
        SELECT 
            BRANCH_ID,
            PRODUCT_ID,
            0 OPENING_BALANCE,
            0 PURCHASE,
            0 STOCK_REC,
            0 SALE_RETURN,
            QTY_BASE_UNIT_VALUE SALE,
            0 STOCK_TRANSFER,
            0 PURCH_RETURN,
            0 STOCK_ADJUSTMENT 
        FROM
            VW_PURC_STOCK_DTL 
        WHERE DOCUMENT_TYPE = 'POS'
            AND BRANCH_ID IN (".implode(",",$data['branch_ids']).")
            $date_field
        UNION
        ALL 
        SELECT 
            BRANCH_ID,
            PRODUCT_ID,
            0 OPENING_BALANCE,
            0 PURCHASE,
            0 STOCK_REC,
            0 SALE_RETURN,
            0 SALE,
            QTY_BASE_UNIT_VALUE STOCK_TRANSFER,
            0 PURCH_RETURN,
            0 STOCK_ADJUSTMENT 
        FROM
            VW_PURC_STOCK_DTL 
        WHERE DOCUMENT_TYPE = 'ST'
            AND BRANCH_ID IN (".implode(",",$data['branch_ids']).") 
            $date_field
        UNION
        ALL 
        SELECT 
            BRANCH_ID,
            PRODUCT_ID,
            0 OPENING_BALANCE,
            0 PURCHASE,
            0 STOCK_REC,
            0 SALE_RETURN,
            0 SALE,
            0 STOCK_TRANSFER,
            QTY_BASE_UNIT_VALUE PURCH_RETURN,
            0 STOCK_ADJUSTMENT 
        FROM
            VW_PURC_STOCK_DTL 
        WHERE DOCUMENT_TYPE = 'PR'
            AND BRANCH_ID IN (".implode(",",$data['branch_ids']).") 
            $date_field
        UNION
        ALL 
        SELECT 
            BRANCH_ID,
            PRODUCT_ID,
            0 OPENING_BALANCE,
            0 PURCHASE,
            0 STOCK_REC,
            0 SALE_RETURN,
            0 SALE,
            0 STOCK_TRANSFER,
            QTY_BASE_UNIT_VALUE PURCH_RETURN,
            0 STOCK_ADJUSTMENT 
        FROM
            VW_PURC_STOCK_DTL 
        WHERE DOCUMENT_TYPE = 'SA'
            AND BRANCH_ID IN (".implode(",",$data['branch_ids']).")
            $date_field
        ) STOCK, VW_PURC_PRODUCT PROD, TBL_SOFT_BRANCH
            $inner_where
        WHERE STOCK.PRODUCT_ID = PROD.PRODUCT_ID 
            AND STOCK.BRANCH_ID = TBL_SOFT_BRANCH.BRANCH_ID 
            $forsupplier
            $wheresupplier
            $where 
        GROUP BY STOCK.BRANCH_ID,
            PROD.PRODUCT_NAME,
            GROUP_ITEM_NAME,
            TBL_SOFT_BRANCH.BRANCH_NAME
            $groupsup
        )abc
    ORDER BY BRANCH_NAME,
        GROUP_ITEM_NAME,
        PRODUCT_NAME  ";
        */


$query = "SELECT DISTINCT 
    PROD_BARCODE.PRODUCT_BARCODE_BARCODE,
    abc.PRODUCT_ID,
    BRANCH_ID,
    BRANCH_NAME,
    PRODUCT_NAME,
    GROUP_ITEM_NAME,
    OPENING_BALANCE,
    PURCHASE,
    STOCK_REC,
    SALE_RETURN,
    TOTAL_STOCK_IN,
    SALE,
    STOCK_TRANSFER,
    PURCH_RETURN,
    STOCK_ADJUSTMENT,
    TOTAL_STOCK_OUT,
    STOCK_BALANCE 
FROM
  (SELECT 
    STOCK.PRODUCT_ID,
    STOCK.BRANCH_ID,
    TBL_SOFT_BRANCH.BRANCH_NAME,
    PROD.PRODUCT_NAME,
    GROUP_ITEM_NAME,
    SUM(OPENING_BALANCE) OPENING_BALANCE,
    SUM(PURCHASE) PURCHASE,
    SUM(STOCK_REC) STOCK_REC,
    SUM(SALE_RETURN) SALE_RETURN,
    SUM(OPENING_BALANCE) + SUM(PURCHASE) + SUM(STOCK_REC) + SUM(SALE_RETURN) TOTAL_STOCK_IN,
    SUM(SALE * - 1) SALE,
    SUM(STOCK_TRANSFER * - 1) STOCK_TRANSFER,
    SUM(PURCH_RETURN * - 1) PURCH_RETURN,
    SUM(STOCK_ADJUSTMENT) STOCK_ADJUSTMENT,
    SUM(SALE * - 1) + SUM(STOCK_TRANSFER * - 1) + SUM(PURCH_RETURN * - 1) + SUM(STOCK_ADJUSTMENT) TOTAL_STOCK_OUT,
    (
      SUM(OPENING_BALANCE) + SUM(PURCHASE) + SUM(STOCK_REC) + SUM(SALE_RETURN)
    ) - (
      SUM(SALE * - 1) + SUM(STOCK_TRANSFER * - 1) + SUM(PURCH_RETURN * - 1) + SUM(STOCK_ADJUSTMENT)
    ) STOCK_BALANCE 
    $sup
  FROM
    (SELECT 
        BRANCH_ID,
        PRODUCT_ID,
        QTY_BASE_UNIT_VALUE OPENING_BALANCE,
        0 PURCHASE,
        0 STOCK_REC,
        0 SALE_RETURN,
        0 SALE,
        0 STOCK_TRANSFER,
        0 PURCH_RETURN,
        0 STOCK_ADJUSTMENT 
    FROM
        VW_PURC_STOCK_DTL 
    WHERE BRANCH_ID IN (".implode(",",$data['branch_ids']).") 
        $date_field2
    UNION
    ALL 
    SELECT 
        BRANCH_ID,
        PRODUCT_ID,
        0 OPENING_BALANCE,
        QTY_BASE_UNIT_VALUE PURCHASE,
        0 STOCK_REC,
        0 SALE_RETURN,
        0 SALE,
        0 STOCK_TRANSFER,
        0 PURCH_RETURN,
        0 STOCK_ADJUSTMENT 
    FROM
        VW_PURC_STOCK_DTL 
    WHERE DOCUMENT_TYPE = 'GRN' 
        AND BRANCH_ID IN (".implode(",",$data['branch_ids']).") 
        $date_field
    UNION
    ALL 
    SELECT 
        BRANCH_ID,
        PRODUCT_ID,
        0 OPENING_BALANCE,
        0 PURCHASE,
        QTY_BASE_UNIT_VALUE STOCK_REC,
        0 SALE_RETURN,
        0 SALE,
        0 STOCK_TRANSFER,
        0 PURCH_RETURN,
        0 STOCK_ADJUSTMENT 
    FROM
        VW_PURC_STOCK_DTL 
    WHERE DOCUMENT_TYPE = 'STR' 
        AND BRANCH_ID IN (".implode(",",$data['branch_ids']).") 
        $date_field 
    UNION
    ALL 
    SELECT 
        BRANCH_ID,
        PRODUCT_ID,
        0 OPENING_BALANCE,
        0 PURCHASE,
        0 STOCK_REC,
        ABS(QTY_BASE_UNIT_VALUE) SALE_RETURN,
        0 SALE,
        0 STOCK_TRANSFER,
        0 PURCH_RETURN,
        0 STOCK_ADJUSTMENT 
    FROM
        VW_PURC_STOCK_DTL 
    WHERE DOCUMENT_TYPE = 'RPOS' 
        AND BRANCH_ID IN (".implode(",",$data['branch_ids']).") 
        $date_field 
    UNION
    ALL 
    SELECT 
        BRANCH_ID,
        PRODUCT_ID,
        0 OPENING_BALANCE,
        0 PURCHASE,
        0 STOCK_REC,
        0 SALE_RETURN,
        QTY_BASE_UNIT_VALUE SALE,
        0 STOCK_TRANSFER,
        0 PURCH_RETURN,
        0 STOCK_ADJUSTMENT 
    FROM
        VW_PURC_STOCK_DTL 
    WHERE DOCUMENT_TYPE = 'POS' 
        AND BRANCH_ID IN (".implode(",",$data['branch_ids']).") 
        $date_field 
    UNION
    ALL 
    SELECT 
        BRANCH_ID,
        PRODUCT_ID,
        0 OPENING_BALANCE,
        0 PURCHASE,
        0 STOCK_REC,
        0 SALE_RETURN,
        0 SALE,
        QTY_BASE_UNIT_VALUE STOCK_TRANSFER,
        0 PURCH_RETURN,
        0 STOCK_ADJUSTMENT 
    FROM
        VW_PURC_STOCK_DTL 
    WHERE DOCUMENT_TYPE = 'ST' 
        AND BRANCH_ID IN (".implode(",",$data['branch_ids']).")
        $date_field 
    UNION
    ALL 
    SELECT 
        BRANCH_ID,
        PRODUCT_ID,
        0 OPENING_BALANCE,
        0 PURCHASE,
        0 STOCK_REC,
        0 SALE_RETURN,
        0 SALE,
        0 STOCK_TRANSFER,
        QTY_BASE_UNIT_VALUE PURCH_RETURN,
        0 STOCK_ADJUSTMENT 
    FROM
        VW_PURC_STOCK_DTL 
    WHERE DOCUMENT_TYPE = 'PR' 
        AND BRANCH_ID IN (".implode(",",$data['branch_ids']).")
        $date_field 
    UNION
    ALL 
    SELECT 
        BRANCH_ID,
        PRODUCT_ID,
        0 OPENING_BALANCE,
        0 PURCHASE,
        0 STOCK_REC,
        0 SALE_RETURN,
        0 SALE,
        0 STOCK_TRANSFER,
        QTY_BASE_UNIT_VALUE PURCH_RETURN,
        0 STOCK_ADJUSTMENT 
    FROM
        VW_PURC_STOCK_DTL 
    WHERE DOCUMENT_TYPE = 'SA' 
        AND BRANCH_ID IN (".implode(",",$data['branch_ids']).")
        $date_field
    ) STOCK, VW_PURC_PRODUCT PROD, TBL_SOFT_BRANCH 
        $inner_where
    WHERE STOCK.PRODUCT_ID = PROD.PRODUCT_ID 
        AND STOCK.BRANCH_ID = TBL_SOFT_BRANCH.BRANCH_ID
        $forsupplier
        $wheresupplier
        $where
    GROUP BY STOCK.BRANCH_ID,
        PROD.PRODUCT_NAME,
        STOCK.PRODUCT_ID,
        GROUP_ITEM_NAME,
        TBL_SOFT_BRANCH.BRANCH_NAME
        $groupsup
    ) abc 
    LEFT OUTER JOIN 
    (SELECT 
        MAX(PRODUCT_BARCODE_BARCODE) PRODUCT_BARCODE_BARCODE,
        PRODUCT_ID 
    FROM
        tbl_purc_product_barcode 
    WHERE BASE_BARCODE = 1 
    GROUP BY PRODUCT_ID
    ) PROD_BARCODE 
    ON abc.PRODUCT_ID = PROD_BARCODE.PRODUCT_ID 
ORDER BY BRANCH_NAME,
  GROUP_ITEM_NAME,
  PRODUCT_NAME,
  PRODUCT_ID ";


                        
//dd($query);
                        $getdata = \Illuminate\Support\Facades\DB::select($query);
                        //dd($getdata);
                        $list_branch = [];
                        foreach ($getdata as $row)
                        {
                            $list_branch[$row->branch_name][$row->group_item_name][$row->product_name][] = $row;
                        }

                        //dd($list_branch);
                      $rowspans = [];
                        $i = 1;
                   @endphp
                    @foreach($list_branch as $branch_key=>$branch_row)
                        @php
                            $rs_branch = 0;
                        @endphp
                        @foreach($branch_row as $group_item_name_key=>$group_item_name)
                            @php
                                $rs_group_item_name = 0;
                                $rs_branch += 1;
                            @endphp
                            @foreach($group_item_name as $product_name_key=>$product_name)
                                @php
                                    $rs_product_name = 0;
                                @endphp
                                @foreach($product_name as $item)
                                    @php
                                        $rs_branch += 1;
                                        $rs_group_item_name += 1;
                                        $rs_product_name += 1;

                                        $rowspans[$branch_key] = $rs_branch;
                                        $rowspans[$branch_key.'_'.$group_item_name_key] = $rs_group_item_name;
                                        $rowspans[$branch_key.'_'.$group_item_name_key.'_'.$product_name_key] = $rs_product_name;

                                    @endphp
                                @endforeach
                            @endforeach
                        @endforeach
                    @endforeach

                    @php
                        $branch_key_new = "";
                        $group_item_name_key_new = "";
                        $product_name_key_new = "";
                    @endphp
                    <table width="100%" id="rep_group_wise_activity_summary_datatable" class="static_report_table table bt-datatable table-bordered">
                        <tr class="sticky-header">
                            <th width="31%" class="dtl-head" colspan="4" rowspan="2"></th>
                            <th width="27%" class="dtl-head" colspan="5">STOCK IN</th>
                            <th width="27%" class="dtl-head" colspan="5">STOCK OUT</th>
                            <th width="15%" class="dtl-head" rowspan="2">BALANCE</th>
                        </tr>
                        <tr class="sticky-header-to">
                            <td width="10%" class="dtl-head">Opening</td>
                            <td width="10%" class="dtl-head">Purchase</td>
                            <td width="10%" class="dtl-head">Stock Receive</td>
                            <td width="10%" class="dtl-head">Sale Return</td>
                            <td width="10%" class="dtl-head">Total</td>

                            <td width="10%" class="dtl-head"> Sale</td>
                            <td width="10%" class="dtl-head">Stock Transfer</td>
                            <td width="10%" class="dtl-head">Purchase Return</td>
                            <td width="10%" class="dtl-head">Stock Adjustment</td>
                            <td width="10%" class="dtl-head">Total</td>
                        </tr>
                        @foreach($list_branch as $branch_key=>$branch_row)
                            @php
                                $stot_opening_balance = 0;
                                $stot_purchase = 0;
                                $stot_stock_rec = 0;
                                $stot_sale_return = 0;
                                $stotal_stock_in = 0;
                                $stot_sale = 0;
                                $stot_stock_transfer = 0;
                                $stot_purch_return = 0;
                                $stot_stock_adjustment = 0;
                                $stotal_stock_out = 0;
                                $stotal_stock_balance = 0;
                            @endphp
                           @foreach($branch_row as $group_item_name_key=>$group_item_name)
                                @php
                                    $tot_opening_balance = 0;
                                    $tot_purchase = 0;
                                    $tot_stock_rec = 0;
                                    $tot_sale_return = 0;
                                    $total_stock_in = 0;
                                    $tot_sale = 0;
                                    $tot_stock_transfer = 0;
                                    $tot_purch_return = 0;
                                    $tot_stock_adjustment = 0;
                                    $total_stock_out = 0;
                                    $total_stock_balance = 0;
                                @endphp
                            @foreach($group_item_name as $product_name_key=>$product_name)
                                @foreach($product_name as $date_key=>$item)
                                    @php

                                        if($branch_key != ""){
                                            $branch_key_new = $branch_key;
                                        }
                                        if($group_item_name_key != ""){
                                            $group_item_name_key_new = $group_item_name_key;
                                        }
                                        if($product_name_key != ""){
                                            $product_name_key_new = $product_name_key;
                                        }

                                        $tot_opening_balance = $tot_opening_balance + $item->opening_balance;
                                        $tot_purchase = $tot_purchase + $item->purchase;
                                        $tot_stock_rec = $tot_stock_rec + $item->stock_rec;
                                        $tot_sale_return = $tot_sale_return + $item->sale_return;
                                        $total_stock_in = $total_stock_in + $item->total_stock_in;
                                        $tot_sale = $tot_sale + $item->sale;
                                        $tot_stock_transfer = $tot_stock_transfer + $item->stock_transfer;
                                        $tot_purch_return = $tot_purch_return + $item->purch_return;
                                        $tot_stock_adjustment = $tot_stock_adjustment + $item->stock_adjustment;
                                        $total_stock_out = $total_stock_out + $item->total_stock_out;
                                        $total_stock_balance = $total_stock_balance + $item->stock_balance;



                                        $stot_opening_balance = $stot_opening_balance + $item->opening_balance;
                                        $stot_purchase = $stot_purchase + $item->purchase;
                                        $stot_stock_rec = $stot_stock_rec + $item->stock_rec;
                                        $stot_sale_return = $stot_sale_return + $item->sale_return;
                                        $stotal_stock_in = $stotal_stock_in + $item->total_stock_in;
                                        $stot_sale = $stot_sale + $item->sale;
                                        $stot_stock_transfer = $stot_stock_transfer + $item->stock_transfer;
                                        $stot_purch_return = $stot_purch_return + $item->purch_return;
                                        $stot_stock_adjustment = $stot_stock_adjustment + $item->stock_adjustment;
                                        $stotal_stock_out = $stotal_stock_out + $item->total_stock_out;
                                        $stotal_stock_balance = $stotal_stock_balance + $item->stock_balance;

                                    @endphp
                                    <tr>
                                        @if($branch_key != "")
                                        <td class="vertical barnch_color" rowspan="{{$rowspans[$branch_key_new]}}">{{$branch_key}}</td>
                                        @endif
                                        @if($group_item_name_key != "")
                                        <td class="vertical sale_type_color" rowspan="{{$rowspans[$branch_key_new.'_'.$group_item_name_key_new]}}">{{$group_item_name_key}}</td>
                                        @endif
                                        <td class="terminal_total">{{$item->product_barcode_barcode}}</td>
                                        <td class="terminal_total">{{$product_name_key}}</td>
                                        <td class="text-right">{{number_format($item->opening_balance,0)}}</td>
                                        <td class="text-right">{{number_format($item->purchase,0)}}</td>
                                        <td class="text-right">{{number_format($item->stock_rec,0)}}</td>
                                        <td class="text-right">{{number_format($item->sale_return,0)}}</td>
                                        <td class="text-right">{{number_format($item->total_stock_in,0)}}</td>

                                        <td class="text-right">{{number_format($item->sale,0)}}</td>
                                        <td class="text-right">{{number_format($item->stock_transfer,0)}}</td>
                                        <td class="text-right">{{number_format($item->purch_return,0)}}</td>
                                        <td class="text-right">{{number_format($item->stock_adjustment,0)}}</td>
                                        <td class="text-right">{{number_format($item->total_stock_out,0)}}</td>
                                        <td class="text-right">{{number_format($item->stock_balance,0)}}</td>
                                    </tr>
                                    @php
                                        $branch_key = "";
                                        $group_item_name_key = "";
                                        $product_name_key = "";
                                    @endphp
                                @endforeach
                            @endforeach
                                <tr>
                                    <td class="text-right sale_type_color" colspan="3"><b>TOTAL : </b> </td>
                                    <td class="text-right sale_type_color">{{number_format($tot_opening_balance,0)}}</td>
                                    <td class="text-right sale_type_color">{{number_format($tot_purchase,0)}}</td>
                                    <td class="text-right sale_type_color">{{number_format($tot_stock_rec,0)}}</td>
                                    <td class="text-right sale_type_color">{{number_format($tot_sale_return,0)}}</td>
                                    <td class="text-right sale_type_color">{{number_format($total_stock_in,0)}}</td>
                                    <td class="text-right sale_type_color">{{number_format($tot_sale,0)}}</td>
                                    <td class="text-right sale_type_color">{{number_format($tot_stock_transfer,0)}}</td>
                                    <td class="text-right sale_type_color">{{number_format($tot_purch_return,0)}}</td>
                                    <td class="text-right sale_type_color">{{number_format($tot_stock_adjustment,0)}}</td>
                                    <td class="text-right sale_type_color">{{number_format($total_stock_out,0)}}</td>
                                    <td class="text-right sale_type_color">{{number_format($total_stock_balance,0)}}</td>
                                </tr>
                           @endforeach
                                <tr>
                                    <td class="text-right barnch_color" colspan="4"><b>GRAND TOTAL : </b> </td>
                                    <td class="text-right barnch_color">{{number_format($stot_opening_balance,0)}}</td>
                                    <td class="text-right barnch_color">{{number_format($stot_purchase,0)}}</td>
                                    <td class="text-right barnch_color">{{number_format($stot_stock_rec,0)}}</td>
                                    <td class="text-right barnch_color">{{number_format($stot_sale_return,0)}}</td>
                                    <td class="text-right barnch_color">{{number_format($stotal_stock_in,0)}}</td>
                                    <td class="text-right barnch_color">{{number_format($stot_sale,0)}}</td>
                                    <td class="text-right barnch_color">{{number_format($stot_stock_transfer,0)}}</td>
                                    <td class="text-right barnch_color">{{number_format($stot_purch_return,0)}}</td>
                                    <td class="text-right barnch_color">{{number_format($stot_stock_adjustment,0)}}</td>
                                    <td class="text-right barnch_color">{{number_format($stotal_stock_out,0)}}</td>
                                    <td class="text-right barnch_color">{{number_format($stotal_stock_balance,0)}}</td>
                                </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
        @include('reports.template.footer')
    </div>
@endsection
@section('pageJS')

@endsection

@section('customJS')

@endsection
@section('exportXls')
    @if($data['form_file_type'] == 'xls')
        <script>
            $(document).ready(function() {
                $("#rep_group_wise_activity_summary_datatable").table2excel({
                    // exclude: ".noExport",
                    filename: "report.xls",
                });
            });
        </script>
    @endif
@endsection



