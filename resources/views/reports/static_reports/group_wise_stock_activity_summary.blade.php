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
        .product-row-even {
            background-color: #ffffff;
        }
        .product-row-odd {
            background-color: #e8f4fd;
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
                    WHERE NOT EXISTS (
                        SELECT 1
                        FROM TBL_INVE_STOCK S
                        WHERE S.STOCK_ID = VW_INVE_DEAL_SALE.SALES_ID
                          AND S.STOCK_CODE_TYPE IN ('POS', 'RPOS')
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
                    WHERE NOT EXISTS (
                        SELECT 1
                        FROM TBL_INVE_STOCK S
                        WHERE S.STOCK_ID = VW_INVE_DEAL_SALE.SALES_ID
                          AND S.STOCK_CODE_TYPE IN ('POS', 'RPOS')
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
                        $date_field2 = " AND (created_at < to_date ('".$date_time_from."', 'yyyy/mm/dd HH24:MI')
                            OR (DOCUMENT_TYPE = 'OS' AND created_at between to_date ('".$date_time_from."', 'yyyy/mm/dd HH24:MI') and to_date ('".$date_time_to."', 'yyyy/mm/dd HH24:MI')))";
                        $date_field = " AND (created_at between to_date ('".$date_time_from."', 'yyyy/mm/dd HH24:MI') and to_date ('".$date_time_to."', 'yyyy/mm/dd HH24:MI'))";
                        $pc_opening_date_field = " AND PC.created_at < to_date ('".$date_time_from."', 'yyyy/mm/dd HH24:MI')";
                        $pc_period_date_field = " AND PC.created_at between to_date ('".$date_time_from."', 'yyyy/mm/dd HH24:MI') and to_date ('".$date_time_to."', 'yyyy/mm/dd HH24:MI')";
                    }else{
                        $date_field2 = "AND (document_date < to_date ('".$data['from_date']."', 'yyyy/mm/dd')
                            OR (DOCUMENT_TYPE = 'OS' AND document_date between to_date ('".$data['from_date']."', 'yyyy/mm/dd') and to_date ('".$data['to_date']."', 'yyyy/mm/dd')))";
                        $date_field = "AND document_date between to_date ('".$data['from_date']."', 'yyyy/mm/dd') and to_date ('".$data['to_date']."', 'yyyy/mm/dd')";
                        $pc_opening_date_field = " AND PC.record_date < to_date ('".$data['from_date']."', 'yyyy/mm/dd')";
                        $pc_period_date_field = " AND PC.record_date between to_date ('".$data['from_date']."', 'yyyy/mm/dd') and to_date ('".$data['to_date']."', 'yyyy/mm/dd')";
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


$query = "SELECT
    abc.PRODUCT_BARCODE_BARCODE,
    abc.PRODUCT_BARCODE_PACKING,
    abc.UOM_NAME,
    abc.PRODUCT_ID,
    abc.PRODUCT_BARCODE_ID,
    abc.BRANCH_ID,
    abc.BRANCH_NAME,
    abc.PRODUCT_NAME,
    abc.GROUP_ITEM_NAME,
    abc.OPENING_BALANCE,
    abc.PURCHASE,
    abc.STOCK_REC,
    abc.SALE_RETURN,
    abc.PRODUCTION,
    abc.TOTAL_STOCK_IN,
    abc.SALE,
    abc.STOCK_TRANSFER,
    abc.PURCH_RETURN,
    abc.STOCK_ADJUSTMENT,
    abc.CONSUMPTION,
    abc.DAMAGED_ITEMS,
    abc.TOTAL_STOCK_OUT,
    abc.STOCK_BALANCE,
    NVL(AVERAGE_RATE.AVERAGE_PRICE, 0) AVERAGE_PRICE,
    NVL(
        LAST_INBOUND_RATE.PACK_RATE,
        NVL(AVERAGE_RATE.AVERAGE_PRICE, 0)
    ) LAST_PRICE,
    abc.STOCK_BALANCE
        * NVL(AVERAGE_RATE.BASE_UNIT_RATE, 0) AVERAGE_AMOUNT,
    abc.STOCK_BALANCE * NVL(
        LAST_INBOUND_RATE.BASE_UNIT_RATE,
        NVL(AVERAGE_RATE.BASE_UNIT_RATE, 0)
    ) LAST_PRICE_AMOUNT
FROM
  (SELECT 
    STOCK.PRODUCT_ID,
    STOCK.PRODUCT_BARCODE_ID,
    PROD_BARCODE.PRODUCT_BARCODE_BARCODE,
    PROD_BARCODE.PRODUCT_BARCODE_PACKING,
    UOM.UOM_NAME,
    STOCK.BRANCH_ID,
    TBL_SOFT_BRANCH.BRANCH_NAME,
    PROD.PRODUCT_NAME,
    GROUP_ITEM_NAME,
    SUM(OPENING_BALANCE) OPENING_BALANCE,
    SUM(PURCHASE) PURCHASE,
    SUM(STOCK_REC) STOCK_REC,
    SUM(SALE_RETURN) SALE_RETURN,
    SUM(PRODUCTION) PRODUCTION,
    SUM(OPENING_BALANCE) + SUM(PURCHASE) + SUM(STOCK_REC) + SUM(SALE_RETURN) + SUM(PRODUCTION) TOTAL_STOCK_IN,
    SUM(SALE * - 1) SALE,
    SUM(STOCK_TRANSFER * - 1) STOCK_TRANSFER,
    SUM(PURCH_RETURN * - 1) PURCH_RETURN,
    SUM(STOCK_ADJUSTMENT) STOCK_ADJUSTMENT,
    SUM(CONSUMPTION) CONSUMPTION,
    SUM(DAMAGED_ITEMS * -1) DAMAGED_ITEMS,
    SUM(SALE * - 1) + SUM(STOCK_TRANSFER * - 1) + SUM(PURCH_RETURN * - 1) + SUM(STOCK_ADJUSTMENT) + SUM(CONSUMPTION) + SUM(DAMAGED_ITEMS * -1) TOTAL_STOCK_OUT,
    (
      SUM(OPENING_BALANCE) + SUM(PURCHASE) + SUM(STOCK_REC) + SUM(SALE_RETURN) + SUM(PRODUCTION)
    ) - (
      SUM(SALE * - 1) + SUM(STOCK_TRANSFER * - 1) + SUM(PURCH_RETURN * - 1) + SUM(STOCK_ADJUSTMENT) + SUM(CONSUMPTION) + SUM(DAMAGED_ITEMS * -1)
    ) STOCK_BALANCE 
    $sup
  FROM
    (SELECT 
        BRANCH_ID,
        PRODUCT_ID,
        PRODUCT_BARCODE_ID,
        CASE
            WHEN DOCUMENT_TYPE IN ('OS', 'ST', 'STR')
                THEN (NVL(QTY_IN, 0) - NVL(QTY_OUT, 0)) * NVL(PRODUCT_PACKING, 1)
            ELSE QTY_BASE_UNIT_VALUE
        END OPENING_BALANCE,
        0 PURCHASE,
        0 STOCK_REC,
        0 SALE_RETURN,
        0 PRODUCTION,
        0 SALE,
        0 STOCK_TRANSFER,
        0 PURCH_RETURN,
        0 STOCK_ADJUSTMENT,
        0 CONSUMPTION,
        0 DAMAGED_ITEMS
    FROM
        VW_PURC_STOCK_DTL 
    WHERE BRANCH_ID IN (".implode(",",$data['branch_ids']).") 
        AND posted = 1
        $date_field2
    UNION
    ALL 
    SELECT 
        BRANCH_ID,
        PRODUCT_ID,
        PRODUCT_BARCODE_ID,
        0 OPENING_BALANCE,
        QTY_BASE_UNIT_VALUE PURCHASE,
        0 STOCK_REC,
        0 SALE_RETURN,
        0 PRODUCTION,
        0 SALE,
        0 STOCK_TRANSFER,
        0 PURCH_RETURN,
        0 STOCK_ADJUSTMENT,
        0 CONSUMPTION,
        0 DAMAGED_ITEMS
    FROM
        VW_PURC_STOCK_DTL 
    WHERE DOCUMENT_TYPE = 'GRN' 
        AND posted = 1
        AND BRANCH_ID IN (".implode(",",$data['branch_ids']).") 
        $date_field
    UNION
    ALL 
    SELECT 
        BRANCH_ID,
        PRODUCT_ID,
        PRODUCT_BARCODE_ID,
        0 OPENING_BALANCE,
        0 PURCHASE,
        NVL(QTY_IN, 0) * NVL(PRODUCT_PACKING, 1) STOCK_REC,
        0 SALE_RETURN,
        0 PRODUCTION,
        0 SALE,
        0 STOCK_TRANSFER,
        0 PURCH_RETURN,
        0 STOCK_ADJUSTMENT,
        0 CONSUMPTION,
        0 DAMAGED_ITEMS
    FROM
        VW_PURC_STOCK_DTL 
    WHERE DOCUMENT_TYPE = 'STR' 
        AND posted = 1
        AND BRANCH_ID IN (".implode(",",$data['branch_ids']).") 
        $date_field 
    UNION
    ALL 
    SELECT 
        BRANCH_ID,
        PRODUCT_ID,
        PRODUCT_BARCODE_ID,
        0 OPENING_BALANCE,
        0 PURCHASE,
        0 STOCK_REC,
        ABS(QTY_BASE_UNIT_VALUE) SALE_RETURN,
        0 PRODUCTION,
        0 SALE,
        0 STOCK_TRANSFER,
        0 PURCH_RETURN,
        0 STOCK_ADJUSTMENT,
        0 CONSUMPTION,
        0 DAMAGED_ITEMS
    FROM
        VW_PURC_STOCK_DTL 
    WHERE DOCUMENT_TYPE = 'RPOS' 
        AND posted = 1
        AND BRANCH_ID IN (".implode(",",$data['branch_ids']).") 
        $date_field 
    UNION
    ALL 
    SELECT 
        BRANCH_ID,
        PRODUCT_ID,
        PRODUCT_BARCODE_ID,
        0 OPENING_BALANCE,
        0 PURCHASE,
        0 STOCK_REC,
        0 SALE_RETURN,
        0 PRODUCTION,
        QTY_BASE_UNIT_VALUE SALE,
        0 STOCK_TRANSFER,
        0 PURCH_RETURN,
        0 STOCK_ADJUSTMENT,
        0 CONSUMPTION,
        0 DAMAGED_ITEMS
    FROM
        VW_PURC_STOCK_DTL 
    WHERE DOCUMENT_TYPE = 'POS' 
        AND posted = 1
        AND BRANCH_ID IN (".implode(",",$data['branch_ids']).") 
        $date_field 
    UNION
    ALL 
    SELECT 
        BRANCH_ID,
        PRODUCT_ID,
        PRODUCT_BARCODE_ID,
        0 OPENING_BALANCE,
        0 PURCHASE,
        0 STOCK_REC,
        0 SALE_RETURN,
        0 PRODUCTION,
        0 SALE,
        (NVL(QTY_IN, 0) - NVL(QTY_OUT, 0)) * NVL(PRODUCT_PACKING, 1) STOCK_TRANSFER,
        0 PURCH_RETURN,
        0 STOCK_ADJUSTMENT,
        0 CONSUMPTION,
        0 DAMAGED_ITEMS
    FROM
        VW_PURC_STOCK_DTL 
    WHERE DOCUMENT_TYPE = 'ST' 
        AND posted = 1
        AND BRANCH_ID IN (".implode(",",$data['branch_ids']).")
        $date_field 
    UNION
    ALL 
    SELECT 
        BRANCH_ID,
        PRODUCT_ID,
        PRODUCT_BARCODE_ID,
        0 OPENING_BALANCE,
        0 PURCHASE,
        0 STOCK_REC,
        0 SALE_RETURN,
        0 PRODUCTION,
        0 SALE,
        0 STOCK_TRANSFER,
        QTY_BASE_UNIT_VALUE PURCH_RETURN,
        0 STOCK_ADJUSTMENT,
        0 CONSUMPTION,
        0 DAMAGED_ITEMS
    FROM
        VW_PURC_STOCK_DTL 
    WHERE DOCUMENT_TYPE = 'PR' 
        AND posted = 1
        AND BRANCH_ID IN (".implode(",",$data['branch_ids']).")
        $date_field 
    UNION
    ALL 
    SELECT 
        BRANCH_ID,
        PRODUCT_ID,
        PRODUCT_BARCODE_ID,
        0 OPENING_BALANCE,
        0 PURCHASE,
        0 STOCK_REC,
        0 SALE_RETURN,
        0 PRODUCTION,
        0 SALE,
        0 STOCK_TRANSFER,
        0 PURCH_RETURN,
        QTY_BASE_UNIT_VALUE STOCK_ADJUSTMENT,
        0 CONSUMPTION,
        0 DAMAGED_ITEMS
    FROM
        VW_PURC_STOCK_DTL 
    WHERE DOCUMENT_TYPE = 'SA' 
        AND posted = 1
        AND BRANCH_ID IN (".implode(",",$data['branch_ids']).")
        $date_field
    UNION ALL
    SELECT
        PC.BRANCH_ID,
        PB.PRODUCT_ID,
        PB.PRODUCT_BARCODE_ID,
        CASE
            WHEN LOWER(PC.STOCK_TYPE) = 'production'
                THEN NVL(PC.QTY, 0) * NVL(PB.PRODUCT_BARCODE_PACKING, 1)
            ELSE NVL(PC.QTY, 0) * NVL(PB.PRODUCT_BARCODE_PACKING, 1) * -1
        END OPENING_BALANCE,
        0 PURCHASE,
        0 STOCK_REC,
        0 SALE_RETURN,
        0 PRODUCTION,
        0 SALE,
        0 STOCK_TRANSFER,
        0 PURCH_RETURN,
        0 STOCK_ADJUSTMENT,
        0 CONSUMPTION,
        0 DAMAGED_ITEMS
    FROM TBLPRODUCTIONCONSUMPTION PC
        INNER JOIN TBL_PURC_PRODUCT_BARCODE PB
            ON PB.PRODUCT_BARCODE_BARCODE = PC.ITEM_CODE
    WHERE PC.BRANCH_ID IN (".implode(",",$data['branch_ids']).")
        AND PC.BUSINESS_ID = ".auth()->user()->business_id."
        AND PC.COMPANY_ID = ".auth()->user()->company_id."
        AND NVL(PC.POSTED, 0) = 1
        AND NVL(PC.STATUS, 0) = 1
        AND NVL(PC.CANCEL, 0) = 0
        $pc_opening_date_field
    UNION ALL
    SELECT
        PC.BRANCH_ID,
        PB.PRODUCT_ID,
        PB.PRODUCT_BARCODE_ID,
        0 OPENING_BALANCE,
        0 PURCHASE,
        0 STOCK_REC,
        0 SALE_RETURN,
        NVL(PC.QTY, 0) * NVL(PB.PRODUCT_BARCODE_PACKING, 1) PRODUCTION,
        0 SALE,
        0 STOCK_TRANSFER,
        0 PURCH_RETURN,
        0 STOCK_ADJUSTMENT,
        0 CONSUMPTION,
        0 DAMAGED_ITEMS
    FROM TBLPRODUCTIONCONSUMPTION PC
        INNER JOIN TBL_PURC_PRODUCT_BARCODE PB
            ON PB.PRODUCT_BARCODE_BARCODE = PC.ITEM_CODE
    WHERE LOWER(PC.STOCK_TYPE) = 'production'
        AND PC.BRANCH_ID IN (".implode(",",$data['branch_ids']).")
        AND PC.BUSINESS_ID = ".auth()->user()->business_id."
        AND PC.COMPANY_ID = ".auth()->user()->company_id."
        AND NVL(PC.POSTED, 0) = 1
        AND NVL(PC.STATUS, 0) = 1
        AND NVL(PC.CANCEL, 0) = 0
        $pc_period_date_field
    UNION ALL
    SELECT
        PC.BRANCH_ID,
        PB.PRODUCT_ID,
        PB.PRODUCT_BARCODE_ID,
        0 OPENING_BALANCE,
        0 PURCHASE,
        0 STOCK_REC,
        0 SALE_RETURN,
        0 PRODUCTION,
        0 SALE,
        0 STOCK_TRANSFER,
        0 PURCH_RETURN,
        0 STOCK_ADJUSTMENT,
        NVL(PC.QTY, 0) * NVL(PB.PRODUCT_BARCODE_PACKING, 1) CONSUMPTION,
        0 DAMAGED_ITEMS
    FROM TBLPRODUCTIONCONSUMPTION PC
        INNER JOIN TBL_PURC_PRODUCT_BARCODE PB
            ON PB.PRODUCT_BARCODE_BARCODE = PC.ITEM_CODE
    WHERE LOWER(PC.STOCK_TYPE) = 'consumption'
        AND PC.BRANCH_ID IN (".implode(",",$data['branch_ids']).")
        AND PC.BUSINESS_ID = ".auth()->user()->business_id."
        AND PC.COMPANY_ID = ".auth()->user()->company_id."
        AND NVL(PC.POSTED, 0) = 1
        AND NVL(PC.STATUS, 0) = 1
        AND NVL(PC.CANCEL, 0) = 0
        $pc_period_date_field
    UNION ALL
    SELECT
        BRANCH_ID,
        PRODUCT_ID,
        PRODUCT_BARCODE_ID,
        0 OPENING_BALANCE,
        0 PURCHASE,
        0 STOCK_REC,
        0 SALE_RETURN,
        0 PRODUCTION,
        0 SALE,
        0 STOCK_TRANSFER,
        0 PURCH_RETURN,
        0 STOCK_ADJUSTMENT,
        0 CONSUMPTION,
        QTY_BASE_UNIT_VALUE DAMAGED_ITEMS
    FROM VW_PURC_STOCK_DTL
    WHERE DOCUMENT_TYPE = 'DI'
        AND posted = 1
        AND BRANCH_ID IN (".implode(",",$data['branch_ids']).")
        $date_field
    ) STOCK, VW_PURC_PRODUCT PROD, TBL_SOFT_BRANCH, TBL_PURC_PRODUCT_BARCODE PROD_BARCODE, TBL_DEFI_UOM UOM
        $inner_where
    WHERE STOCK.PRODUCT_ID = PROD.PRODUCT_ID 
        AND STOCK.PRODUCT_BARCODE_ID = PROD_BARCODE.PRODUCT_BARCODE_ID
        AND PROD_BARCODE.UOM_ID = UOM.UOM_ID(+)
        AND STOCK.BRANCH_ID = TBL_SOFT_BRANCH.BRANCH_ID
        $forsupplier
        $wheresupplier
        $where
    GROUP BY STOCK.BRANCH_ID,
        PROD.PRODUCT_NAME,
        STOCK.PRODUCT_ID,
        STOCK.PRODUCT_BARCODE_ID,
        PROD_BARCODE.PRODUCT_BARCODE_BARCODE,
        PROD_BARCODE.PRODUCT_BARCODE_PACKING,
        UOM.UOM_NAME,
        GROUP_ITEM_NAME,
        TBL_SOFT_BRANCH.BRANCH_NAME
        $groupsup
    ) abc
    LEFT JOIN AVERAGE_RATE
        ON AVERAGE_RATE.BRANCH_ID = abc.BRANCH_ID
        AND AVERAGE_RATE.PRODUCT_ID = abc.PRODUCT_ID
        AND AVERAGE_RATE.PRODUCT_BARCODE_ID = abc.PRODUCT_BARCODE_ID
    LEFT JOIN LAST_INBOUND_RATE
        ON LAST_INBOUND_RATE.BRANCH_ID = abc.BRANCH_ID
        AND LAST_INBOUND_RATE.PRODUCT_ID = abc.PRODUCT_ID
        AND LAST_INBOUND_RATE.PRODUCT_BARCODE_ID = abc.PRODUCT_BARCODE_ID
ORDER BY abc.BRANCH_NAME,
  abc.GROUP_ITEM_NAME,
  abc.PRODUCT_NAME,
  abc.PRODUCT_BARCODE_PACKING,
  abc.UOM_NAME ";

// The activity query uses the same posted ledger rows for opening and every
// movement column. Materialize that filtered set once instead of expanding
// and scanning the ledger view separately for each UNION branch.
$ledgerCteDateFilter = $data['date_time_wise'] == 1
    ? "AND CREATED_AT <= to_date ('".$date_time_to."', 'yyyy/mm/dd HH24:MI')"
    : "AND DOCUMENT_DATE <= to_date ('".$data['to_date']."', 'yyyy/mm/dd')";
$query = str_replace('VW_PURC_STOCK_DTL', 'STOCK_LEDGER', $query);
$query = "WITH STOCK_LEDGER AS (
    SELECT /*+ MATERIALIZE */
        BRANCH_ID,
        BUSINESS_ID,
        COMPANY_ID,
        PRODUCT_ID,
        PRODUCT_BARCODE_ID,
        DOCUMENT_ID,
        DOCUMENT_TYPE,
        QTY_BASE_UNIT_VALUE,
        QTY_IN,
        QTY_OUT,
        BONUS_QTY_IN,
        PRODUCT_PACKING,
        STOCK_RATE,
        DOCUMENT_ACT_RATE,
        RATE_EFFECT,
        DOCUMENT_DATE,
        CREATED_AT,
        POSTED
    FROM VW_PURC_STOCK_DTL
    WHERE POSTED = 1
      AND BUSINESS_ID = ".auth()->user()->business_id."
      AND COMPANY_ID = ".auth()->user()->company_id."
      AND BRANCH_ID IN (".implode(",",$data['branch_ids']).")
      ".$ledgerCteDateFilter."
), AVERAGE_RATE AS (
    SELECT
        BRANCH_ID,
        PRODUCT_ID,
        PRODUCT_BARCODE_ID,
        NVL(SUM(INBOUND_AMOUNT) / NULLIF(SUM(INBOUND_PACK_QTY), 0), 0) AVERAGE_PRICE,
        NVL(SUM(INBOUND_AMOUNT) / NULLIF(SUM(INBOUND_BASE_QTY), 0), 0) BASE_UNIT_RATE
    FROM (
        SELECT
            LEDGER.BRANCH_ID,
            LEDGER.PRODUCT_ID,
            LEDGER.PRODUCT_BARCODE_ID,
            CASE
                WHEN LEDGER.DOCUMENT_TYPE = 'GRN'
                    THEN NVL(GRN_RATE.TBL_PURC_GRN_DTL_RATE, 0)
                         * (NVL(LEDGER.QTY_IN, 0) + NVL(LEDGER.BONUS_QTY_IN, 0))
                ELSE NVL(NULLIF(LEDGER.STOCK_RATE, 0), LEDGER.DOCUMENT_ACT_RATE)
                     * (NVL(LEDGER.QTY_IN, 0) + NVL(LEDGER.BONUS_QTY_IN, 0))
            END INBOUND_AMOUNT,
            NVL(LEDGER.QTY_IN, 0) + NVL(LEDGER.BONUS_QTY_IN, 0) INBOUND_PACK_QTY,
            (NVL(LEDGER.QTY_IN, 0) + NVL(LEDGER.BONUS_QTY_IN, 0))
                * NVL(LEDGER.PRODUCT_PACKING, 1) INBOUND_BASE_QTY
        FROM STOCK_LEDGER LEDGER
        LEFT JOIN TBL_PURC_GRN_DTL GRN_RATE
            ON GRN_RATE.GRN_ID = LEDGER.DOCUMENT_ID
            AND GRN_RATE.PRODUCT_ID = LEDGER.PRODUCT_ID
            AND GRN_RATE.PRODUCT_BARCODE_ID = LEDGER.PRODUCT_BARCODE_ID
            AND GRN_RATE.BUSINESS_ID = LEDGER.BUSINESS_ID
            AND GRN_RATE.COMPANY_ID = LEDGER.COMPANY_ID
            AND GRN_RATE.BRANCH_ID = LEDGER.BRANCH_ID
            AND GRN_RATE.GRN_TYPE = 'GRN'
        WHERE NVL(LEDGER.QTY_IN, 0) + NVL(LEDGER.BONUS_QTY_IN, 0) > 0
          AND NVL(LEDGER.PRODUCT_PACKING, 0) > 0
          AND (
              (LEDGER.DOCUMENT_TYPE = 'GRN' AND NVL(GRN_RATE.TBL_PURC_GRN_DTL_RATE, 0) > 0)
              OR (LEDGER.DOCUMENT_TYPE <> 'GRN'
                  AND NVL(NULLIF(LEDGER.STOCK_RATE, 0), LEDGER.DOCUMENT_ACT_RATE) > 0)
          )
    ) INBOUND_RATES
    GROUP BY BRANCH_ID, PRODUCT_ID, PRODUCT_BARCODE_ID
), LAST_INBOUND_RATE AS (
    SELECT BRANCH_ID, PRODUCT_ID, PRODUCT_BARCODE_ID, PACK_RATE, BASE_UNIT_RATE
    FROM (
        SELECT
            LEDGER.BRANCH_ID,
            LEDGER.PRODUCT_ID,
            LEDGER.PRODUCT_BARCODE_ID,
            CASE
                WHEN LEDGER.DOCUMENT_TYPE = 'GRN' THEN NVL(GRN_RATE.TBL_PURC_GRN_DTL_RATE, 0)
                ELSE NVL(NULLIF(LEDGER.STOCK_RATE, 0), LEDGER.DOCUMENT_ACT_RATE)
            END PACK_RATE,
            CASE
                WHEN LEDGER.DOCUMENT_TYPE = 'GRN'
                    THEN NVL(GRN_RATE.TBL_PURC_GRN_DTL_RATE, 0) / NULLIF(LEDGER.PRODUCT_PACKING, 0)
                ELSE NVL(NULLIF(LEDGER.STOCK_RATE, 0), LEDGER.DOCUMENT_ACT_RATE)
                     / NULLIF(LEDGER.PRODUCT_PACKING, 0)
            END BASE_UNIT_RATE,
            ROW_NUMBER() OVER (
                PARTITION BY LEDGER.BRANCH_ID, LEDGER.PRODUCT_ID, LEDGER.PRODUCT_BARCODE_ID
                ORDER BY LEDGER.DOCUMENT_DATE DESC, LEDGER.CREATED_AT DESC
            ) RN
        FROM STOCK_LEDGER LEDGER
        LEFT JOIN TBL_PURC_GRN_DTL GRN_RATE
            ON GRN_RATE.GRN_ID = LEDGER.DOCUMENT_ID
            AND GRN_RATE.PRODUCT_ID = LEDGER.PRODUCT_ID
            AND GRN_RATE.PRODUCT_BARCODE_ID = LEDGER.PRODUCT_BARCODE_ID
            AND GRN_RATE.BUSINESS_ID = LEDGER.BUSINESS_ID
            AND GRN_RATE.COMPANY_ID = LEDGER.COMPANY_ID
            AND GRN_RATE.BRANCH_ID = LEDGER.BRANCH_ID
            AND GRN_RATE.GRN_TYPE = 'GRN'
        WHERE NVL(LEDGER.QTY_IN, 0) + NVL(LEDGER.BONUS_QTY_IN, 0) > 0
          AND NVL(LEDGER.PRODUCT_PACKING, 0) > 0
          AND (
              (LEDGER.DOCUMENT_TYPE = 'GRN' AND NVL(GRN_RATE.TBL_PURC_GRN_DTL_RATE, 0) > 0)
              OR (LEDGER.DOCUMENT_TYPE <> 'GRN'
                  AND NVL(NULLIF(LEDGER.STOCK_RATE, 0), LEDGER.DOCUMENT_ACT_RATE) > 0)
          )
    )
    WHERE RN = 1
) ".$query;


                        
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
                        $product_color_index = 0;
                        $prev_product_name_for_color = null;
                    @endphp
                    <table width="100%" id="rep_group_wise_activity_summary_datatable" class="static_report_table table bt-datatable table-bordered">
                        <tr class="sticky-header">
                            <th class="dtl-head" rowspan="2">Branch</th>
                            <th class="dtl-head" rowspan="2">Group</th>
                            <th class="dtl-head" rowspan="2">Barcode</th>
                            <th class="dtl-head" rowspan="2">Product</th>
                            <th class="dtl-head" rowspan="2">Packing</th>
                            <th class="dtl-head" rowspan="2">UOM</th>
                            <th class="dtl-head" colspan="6">STOCK IN</th>
                            <th class="dtl-head" colspan="7">STOCK OUT</th>
                            <th width="15%" class="dtl-head" rowspan="2">BALANCE</th>
                            <th class="dtl-head" rowspan="2">Average Price</th>
                            <th class="dtl-head" rowspan="2">Last Price</th>
                            <th class="dtl-head" rowspan="2">Average Amount</th>
                            <th class="dtl-head" rowspan="2">Last Price Amount</th>
                        </tr>
                        <tr class="sticky-header-to">
                            <td width="10%" class="dtl-head">Opening</td>
                            <td width="10%" class="dtl-head">Purchase</td>
                            <td width="10%" class="dtl-head">Stock Receive</td>
                            <td width="10%" class="dtl-head">Sale Return</td>
                            <td width="10%" class="dtl-head">Production</td>
                            <td width="10%" class="dtl-head">Total</td>

                            <td width="10%" class="dtl-head"> Sale</td>
                            <td width="10%" class="dtl-head">Stock Transfer</td>
                            <td width="10%" class="dtl-head">Purchase Return</td>
                            <td width="10%" class="dtl-head">Stock Adjustment</td>
                            <td width="10%" class="dtl-head">Consumption</td>
                            <td width="10%" class="dtl-head">Damaged Items</td>
                            <td width="10%" class="dtl-head">Total</td>
                        </tr>
                        @foreach($list_branch as $branch_key=>$branch_row)
                            @php
                                $stot_opening_balance = 0;
                                $stot_purchase = 0;
                                $stot_stock_rec = 0;
                                $stot_sale_return = 0;
                                $stot_production = 0;
                                $stotal_stock_in = 0;
                                $stot_sale = 0;
                                $stot_stock_transfer = 0;
                                $stot_purch_return = 0;
                                $stot_stock_adjustment = 0;
                                $stot_consumption = 0;
                                $stot_damaged_items = 0;
                                $stotal_stock_out = 0;
                                $stotal_stock_balance = 0;
                                $stotal_average_amount = 0;
                                $stotal_last_price_amount = 0;
                            @endphp
                           @foreach($branch_row as $group_item_name_key=>$group_item_name)
                                @php
                                    $tot_opening_balance = 0;
                                    $tot_purchase = 0;
                                    $tot_stock_rec = 0;
                                    $tot_sale_return = 0;
                                    $tot_production = 0;
                                    $total_stock_in = 0;
                                    $tot_sale = 0;
                                    $tot_stock_transfer = 0;
                                    $tot_purch_return = 0;
                                    $tot_stock_adjustment = 0;
                                    $tot_consumption = 0;
                                    $tot_damaged_items = 0;
                                    $total_stock_out = 0;
                                    $total_stock_balance = 0;
                                    $total_average_amount = 0;
                                    $total_last_price_amount = 0;
                                @endphp
                            @foreach($group_item_name as $product_name_key=>$product_name)
                                @php
                                    // Alternate color for every new product name within the group
                                    if ($prev_product_name_for_color !== $product_name_key) {
                                        $product_color_index++;
                                        $prev_product_name_for_color = $product_name_key;
                                    }
                                    $product_row_class = ($product_color_index % 2 === 0) ? 'product-row-even' : 'product-row-odd';
                                @endphp
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
                                        $tot_production = $tot_production + $item->production;
                                        $total_stock_in = $total_stock_in + $item->total_stock_in;
                                        $tot_sale = $tot_sale + $item->sale;
                                        $tot_stock_transfer = $tot_stock_transfer + $item->stock_transfer;
                                        $tot_purch_return = $tot_purch_return + $item->purch_return;
                                        $tot_stock_adjustment = $tot_stock_adjustment + $item->stock_adjustment;
                                        $tot_consumption = $tot_consumption + $item->consumption;
                                        $tot_damaged_items = $tot_damaged_items + $item->damaged_items;
                                        $total_stock_out = $total_stock_out + $item->total_stock_out;
                                        $total_stock_balance = $total_stock_balance + $item->stock_balance;
                                        $total_average_amount = $total_average_amount + $item->average_amount;
                                        $total_last_price_amount = $total_last_price_amount + $item->last_price_amount;



                                        $stot_opening_balance = $stot_opening_balance + $item->opening_balance;
                                        $stot_purchase = $stot_purchase + $item->purchase;
                                        $stot_stock_rec = $stot_stock_rec + $item->stock_rec;
                                        $stot_sale_return = $stot_sale_return + $item->sale_return;
                                        $stot_production = $stot_production + $item->production;
                                        $stotal_stock_in = $stotal_stock_in + $item->total_stock_in;
                                        $stot_sale = $stot_sale + $item->sale;
                                        $stot_stock_transfer = $stot_stock_transfer + $item->stock_transfer;
                                        $stot_purch_return = $stot_purch_return + $item->purch_return;
                                        $stot_stock_adjustment = $stot_stock_adjustment + $item->stock_adjustment;
                                        $stot_consumption = $stot_consumption + $item->consumption;
                                        $stot_damaged_items = $stot_damaged_items + $item->damaged_items;
                                        $stotal_stock_out = $stotal_stock_out + $item->total_stock_out;
                                        $stotal_stock_balance = $stotal_stock_balance + $item->stock_balance;
                                        $stotal_average_amount = $stotal_average_amount + $item->average_amount;
                                        $stotal_last_price_amount = $stotal_last_price_amount + $item->last_price_amount;

                                    @endphp
                                    <tr class="{{$product_row_class}}">
                                        @if($branch_key != "")
                                        <td class="vertical barnch_color" rowspan="{{$rowspans[$branch_key_new]}}">{{$branch_key}}</td>
                                        @endif
                                        @if($group_item_name_key != "")
                                        <td class="vertical sale_type_color" rowspan="{{$rowspans[$branch_key_new.'_'.$group_item_name_key_new]}}">{{$group_item_name_key}}</td>
                                        @endif
                                        <td>{{$item->product_barcode_barcode}}</td>
                                        <td data-id="pd_product_name"
                                            data-product-id="{{$item->product_id}}"
                                            data-barcode-id="{{$item->product_barcode_id}}"
                                            data-product-name="{{$product_name_key}}"
                                            data-barcode="{{$item->product_barcode_barcode}}">
                                            <strong>{{$product_name_key}}</strong>
                                        </td>
                                        <td class="text-right">{{number_format($item->product_barcode_packing,0)}}</td>
                                        <td class="text-center">{{$item->uom_name ?? '-'}}</td>
                                        <td class="text-right">{{number_format($item->opening_balance,0)}}</td>
                                        <td class="text-right">{{number_format($item->purchase,0)}}</td>
                                        <td class="text-right">{{number_format($item->stock_rec,0)}}</td>
                                        <td class="text-right">{{number_format($item->sale_return,0)}}</td>
                                        <td class="text-right">{{number_format($item->production,0)}}</td>
                                        <td class="text-right">{{number_format($item->total_stock_in,0)}}</td>

                                        <td class="text-right">{{number_format($item->sale,0)}}</td>
                                        <td class="text-right">{{number_format($item->stock_transfer,0)}}</td>
                                        <td class="text-right">{{number_format($item->purch_return,0)}}</td>
                                        <td class="text-right">{{number_format($item->stock_adjustment,0)}}</td>
                                        <td class="text-right">{{number_format($item->consumption,0)}}</td>
                                        <td class="text-right">{{number_format($item->damaged_items,0)}}</td>
                                        <td class="text-right">{{number_format($item->total_stock_out,0)}}</td>
                                        <td class="text-right">{{number_format($item->stock_balance,0)}}</td>
                                        <td class="text-right">{{number_format($item->average_price,3)}}</td>
                                        <td class="text-right">{{number_format($item->last_price,3)}}</td>
                                        <td class="text-right">{{number_format($item->average_amount,3)}}</td>
                                        <td class="text-right">{{number_format($item->last_price_amount,3)}}</td>
                                    </tr>
                                    @php
                                        $branch_key = "";
                                        $group_item_name_key = "";
                                        $product_name_key = "";
                                    @endphp
                                @endforeach
                            @endforeach
                                <tr>
                                    <td class="text-right sale_type_color" colspan="5"><b>TOTAL : </b> </td>
                                    <td class="text-right sale_type_color">{{number_format($tot_opening_balance,0)}}</td>
                                    <td class="text-right sale_type_color">{{number_format($tot_purchase,0)}}</td>
                                    <td class="text-right sale_type_color">{{number_format($tot_stock_rec,0)}}</td>
                                    <td class="text-right sale_type_color">{{number_format($tot_sale_return,0)}}</td>
                                    <td class="text-right sale_type_color">{{number_format($tot_production,0)}}</td>
                                    <td class="text-right sale_type_color">{{number_format($total_stock_in,0)}}</td>
                                    <td class="text-right sale_type_color">{{number_format($tot_sale,0)}}</td>
                                    <td class="text-right sale_type_color">{{number_format($tot_stock_transfer,0)}}</td>
                                    <td class="text-right sale_type_color">{{number_format($tot_purch_return,0)}}</td>
                                    <td class="text-right sale_type_color">{{number_format($tot_stock_adjustment,0)}}</td>
                                    <td class="text-right sale_type_color">{{number_format($tot_consumption,0)}}</td>
                                    <td class="text-right sale_type_color">{{number_format($tot_damaged_items,0)}}</td>
                                    <td class="text-right sale_type_color">{{number_format($total_stock_out,0)}}</td>
                                    <td class="text-right sale_type_color">{{number_format($total_stock_balance,0)}}</td>
                                    <td class="text-center sale_type_color">-</td>
                                    <td class="text-center sale_type_color">-</td>
                                    <td class="text-right sale_type_color">{{number_format($total_average_amount,3)}}</td>
                                    <td class="text-right sale_type_color">{{number_format($total_last_price_amount,3)}}</td>
                                </tr>
                           @endforeach
                                <tr>
                                    <td class="text-right barnch_color" colspan="6"><b>GRAND TOTAL : </b> </td>
                                    <td class="text-right barnch_color">{{number_format($stot_opening_balance,0)}}</td>
                                    <td class="text-right barnch_color">{{number_format($stot_purchase,0)}}</td>
                                    <td class="text-right barnch_color">{{number_format($stot_stock_rec,0)}}</td>
                                    <td class="text-right barnch_color">{{number_format($stot_sale_return,0)}}</td>
                                    <td class="text-right barnch_color">{{number_format($stot_production,0)}}</td>
                                    <td class="text-right barnch_color">{{number_format($stotal_stock_in,0)}}</td>
                                    <td class="text-right barnch_color">{{number_format($stot_sale,0)}}</td>
                                    <td class="text-right barnch_color">{{number_format($stot_stock_transfer,0)}}</td>
                                    <td class="text-right barnch_color">{{number_format($stot_purch_return,0)}}</td>
                                    <td class="text-right barnch_color">{{number_format($stot_stock_adjustment,0)}}</td>
                                    <td class="text-right barnch_color">{{number_format($stot_consumption,0)}}</td>
                                    <td class="text-right barnch_color">{{number_format($stot_damaged_items,0)}}</td>
                                    <td class="text-right barnch_color">{{number_format($stotal_stock_out,0)}}</td>
                                    <td class="text-right barnch_color">{{number_format($stotal_stock_balance,0)}}</td>
                                    <td class="text-center barnch_color">-</td>
                                    <td class="text-center barnch_color">-</td>
                                    <td class="text-right barnch_color">{{number_format($stotal_average_amount,3)}}</td>
                                    <td class="text-right barnch_color">{{number_format($stotal_last_price_amount,3)}}</td>
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
    <script>
        $(function () {
            $('body').on('contextmenu', '#rep_group_wise_activity_summary_datatable td[data-id="pd_product_name"]', function (e) {
                e.preventDefault();
                e.stopImmediatePropagation();

                var $cell = $(this);
                var productId = $cell.data('product-id');
                var productName = $cell.data('product-name');
                var barcode = $cell.data('barcode');
                var barcodeId = $cell.data('barcode-id');
                var $links = $('#contextMenu li a');

                $links.attr('data-id', productId)
                    .attr('data-val', productName)
                    .attr('data-barcode', barcode)
                    .attr('data-barcode_id', barcodeId);
                $('#contextMenu li.product_card a').attr('href', '/product/view/' + productId);
                $('#contextMenu li.tp_analysis a').attr('href', '/smart-product/tp-analysis');

                localStorage.setItem('product_barcode_barcode', barcode);
                localStorage.setItem('product_barcode_id', barcodeId);
                localStorage.setItem('product_id', productId);

                $('#contextMenu').css({
                    display: 'block',
                    left: e.pageX,
                    top: e.pageY
                });
                return false;
            });
        });
    </script>
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



