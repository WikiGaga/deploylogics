@extends('layouts.report')
@section('title', 'Product Activity Report')

@section('pageCSS')
    <style>
        /* Styles go here */
        @media print {
            thead {display: table-header-group;}
            tfoot {display: table-footer-group;}
            tfoot>tr>td {padding:0 !important;}
            body {margin: 0;}
        }
        tr.grn {
            background: #f0f8ff;
        }
        tr.rpos {
            background: #ffeae2;
        }
        tr.pr {
            background: #f5f5dc;
        }
    </style>
@endsection
@section('content')
    @php
        $data = Session::get('data');
        $productDtl = \Illuminate\Support\Facades\DB::table('vw_purc_product_barcode')->where('product_id',$data['product'])->first();
        $productBarcodes = \Illuminate\Support\Facades\DB::table('vw_purc_product_barcode')
            ->where('product_id', $data['product'])
            ->get(['product_barcode_id', 'product_barcode_barcode', 'product_barcode_packing', 'uom_name']);
        $productBarcodesById = $productBarcodes->pluck('product_barcode_barcode', 'product_barcode_id');
        $averagePriceBranches = array_values(array_filter(array_map('intval', $data['branch_ids'] ?? [])));
        $averagePriceProductId = (int) ($data['product'] ?? 0);
        $averagePriceBusinessId = (int) auth()->user()->business_id;
        $averagePriceCompanyId = (int) auth()->user()->company_id;
        $averagePriceEndDateFilter = $data['date_time_wise'] == 1
            ? "AND STOCK.CREATED_AT <= TO_DATE('" . str_replace("'", "''", $data['time_to']) . "', 'yyyy/mm/dd HH24:MI')"
            : "AND STOCK.DOCUMENT_DATE < TO_DATE('" . str_replace("'", "''", $data['to_date']) . "', 'yyyy/mm/dd') + 1";
        $averagePriceOpeningDateFilter = $data['date_time_wise'] == 1
            ? "AND STOCK.CREATED_AT < TO_DATE('" . str_replace("'", "''", $data['time_from']) . "', 'yyyy/mm/dd HH24:MI')"
            : "AND STOCK.DOCUMENT_DATE < TO_DATE('" . str_replace("'", "''", $data['from_date']) . "', 'yyyy/mm/dd')";
        $averagePricePerItem = 0;
        $runningAverageAmount = 0;
        $runningAverageBaseQty = 0;
        if ($averagePriceProductId && count($averagePriceBranches)) {
            $getAveragePriceTotals = function ($dateFilter) use (
                $averagePriceProductId,
                $averagePriceBusinessId,
                $averagePriceCompanyId,
                $averagePriceBranches
            ) {
                $averagePriceQuery = "SELECT
                    NVL(SUM((CASE WHEN STOCK.DOCUMENT_TYPE = 'GRN'
                        THEN NVL(GRN_RATE.TBL_PURC_GRN_DTL_RATE, 0)
                        ELSE NVL(NULLIF(STOCK.STOCK_RATE, 0), STOCK.DOCUMENT_ACT_RATE)
                    END) * (NVL(STOCK.QTY_IN, 0) + NVL(STOCK.BONUS_QTY_IN, 0))), 0) INBOUND_AMOUNT,
                    NVL(SUM((NVL(STOCK.QTY_IN, 0) + NVL(STOCK.BONUS_QTY_IN, 0))
                        * NVL(STOCK.PRODUCT_PACKING, 1)), 0) INBOUND_BASE_QTY
                FROM VW_PURC_STOCK_DTL STOCK
                LEFT JOIN TBL_PURC_GRN_DTL GRN_RATE
                    ON GRN_RATE.GRN_ID = STOCK.DOCUMENT_ID
                    AND GRN_RATE.PRODUCT_ID = STOCK.PRODUCT_ID
                    AND GRN_RATE.PRODUCT_BARCODE_ID = STOCK.PRODUCT_BARCODE_ID
                    AND GRN_RATE.BUSINESS_ID = STOCK.BUSINESS_ID
                    AND GRN_RATE.COMPANY_ID = STOCK.COMPANY_ID
                    AND GRN_RATE.BRANCH_ID = STOCK.BRANCH_ID
                    AND GRN_RATE.GRN_TYPE = 'GRN'
                WHERE STOCK.PRODUCT_ID = $averagePriceProductId
                    AND STOCK.BUSINESS_ID = $averagePriceBusinessId
                    AND STOCK.COMPANY_ID = $averagePriceCompanyId
                    AND STOCK.BRANCH_ID IN (" . implode(',', $averagePriceBranches) . ")
                    AND STOCK.POSTED = 1
                    $dateFilter
                    AND NVL(STOCK.QTY_IN, 0) + NVL(STOCK.BONUS_QTY_IN, 0) > 0
                    AND NVL(STOCK.PRODUCT_PACKING, 0) > 0
                    AND ((STOCK.DOCUMENT_TYPE = 'GRN' AND NVL(GRN_RATE.TBL_PURC_GRN_DTL_RATE, 0) > 0)
                        OR (STOCK.DOCUMENT_TYPE <> 'GRN'
                            AND NVL(NULLIF(STOCK.STOCK_RATE, 0), STOCK.DOCUMENT_ACT_RATE) > 0))";
                return \Illuminate\Support\Facades\DB::selectOne($averagePriceQuery);
            };

            $openingAverageTotals = $getAveragePriceTotals($averagePriceOpeningDateFilter);
            $runningAverageAmount = (float) ($openingAverageTotals->inbound_amount ?? 0);
            $runningAverageBaseQty = (float) ($openingAverageTotals->inbound_base_qty ?? 0);

            $finalAverageTotals = $getAveragePriceTotals($averagePriceEndDateFilter);
            $finalAverageAmount = (float) ($finalAverageTotals->inbound_amount ?? 0);
            $finalAverageBaseQty = (float) ($finalAverageTotals->inbound_base_qty ?? 0);
            $averagePricePerItem = $finalAverageBaseQty > 0
                ? $finalAverageAmount / $finalAverageBaseQty
                : 0;
        }
    @endphp
    <div class="kt-portlet" id="kt_portlet_table">
        <div class="kt-portlet__head">
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
                @if(count($data['all_document_type']) != 0)
                <h6 class="kt-invoice__criteria">
                    <span style="color: #e27d00;">Type:</span>
                        @foreach($data['all_document_type'] as $ad_type)
                            <span style="color: #5578eb;">{{" ".$ad_type.", "}}</span>
                        @endforeach
                </h6>
                @endif
                @if(count($data['store']) != 0 && $data['store'] != "" && $data['store'] != null)
                    @php $stores = \Illuminate\Support\Facades\DB::table('tbl_defi_store')->whereIn('store_id',$data['store'])->get('store_name'); @endphp
                    <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;">Stores:</span>
                        @foreach($stores as $store)
                            <span style="color: #5578eb;">{{$store->store_name}}</span><span style="color: #fd397a;">, </span>
                        @endforeach
                    </h6>
                @endif
                @if(isset($data['product']) && !empty($data['product']))
                    <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;">Product:</span>
                        <span style="color: #5578eb;">{{" ".$productDtl->product_name." "}}</span>
                    </h6>
                    <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;">Barcodes (UOM, Packing):</span>
                        @foreach($productBarcodes as $productBarcode)
                            <span style="color: #5578eb;">{{ $productBarcode->product_barcode_barcode }} ({{ $productBarcode->uom_name }}, {{ number_format((float) $productBarcode->product_barcode_packing, 3) }})</span>@if(!$loop->last), @endif
                        @endforeach
                    </h6>
                @endif
            </div>
            @include('reports.template.branding')
        </div>
        <div class="kt-portlet__body">
            <div class="row row-block">
                <div class="col-lg-12">
                    <table width="100%" id="rep_sale_invoice_datatable" class="table bt-datatable table-bordered">
                        {{--<tr  class="sticky-header">
                            <th class="text-center" colspan="4"></th>
                            <th class="text-center">Inputs</th>
                            <th class="text-center">Outputs</th>
                            <th class="text-center">Balance</th>
                            <th class="text-center"></th>
                        </tr>--}}
                        <tr class="sticky-header">
                            <th class="text-center" width="10%">Document Date</th>
                            <th class="text-center" width="10%">Document Type</th>
                            <th class="text-center" width="12%">Branch Name</th>
                            <th class="text-center" width="10%">Document Code</th>
                            <th class="text-center">Barcode</th>
                            <th class="text-center" width="20%">Remarks</th>
                            <th class="text-center">Packing</th>
                            <th class="text-center">In Qty</th>
                            <th class="text-center">In Rate</th>
                            <th class="text-center">In Amount</th>
                            <th class="text-center">Out Qty</th>
                            <th class="text-center">Out Rate</th>
                            <th class="text-center">Out Amount</th>
                            <th class="text-center">In Qty per item</th>
                            <th class="text-center">In Rate per item</th>
                            <th class="text-center">Out Qty per item</th>
                            <th class="text-center">Out Rate per item</th>
                            <th class="text-center">Balance Qty per item</th>
                            <th class="text-center">Average Price per item</th>
                            {{--<th class="text-center">Price</th>--}}
                            {{--<th class="text-center">Expire Date</th>--}}
                        </tr>
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
                            if($data['date_time_wise'] == 1){
                                $date_time_from = $data['time_from'];
                                $date_time_to = $data['time_to'];
                            }
                            if($data['date_time_wise'] == 1){
                                $from_date = date('d-m-Y', strtotime($data['time_from']));
                            }else{
                                $from_date = date('Y-m-d',(strtotime ( '-1 day' , strtotime ($data['from_date']) ) ));
                            }

                            if($data['date_time_wise'] == 1){
                                $date_field2 = " AND (STOCK.created_at between to_date ('".$date_time_from."', 'yyyy/mm/dd HH24:MI') and to_date ('".$date_time_to."', 'yyyy/mm/dd HH24:MI'))";
                                $date_field = " AND GRN.created_at <= to_date ('".$date_time_from."', 'yyyy/mm/dd HH24:MI')";
                                $usage_date_field = " AND USAGE.USAGE_DATE >= TRUNC(to_date ('".$date_time_from."', 'yyyy/mm/dd HH24:MI')) AND USAGE.USAGE_DATE < TRUNC(to_date ('".$date_time_to."', 'yyyy/mm/dd HH24:MI')) + 1";
                                $usage_opening_date_field = " AND USAGE.USAGE_DATE < TRUNC(to_date ('".$date_time_from."', 'yyyy/mm/dd HH24:MI'))";
                                $usage_report_date = "TRUNC(USAGE.USAGE_DATE)";
                            }else{
                                $date_field2 = "AND (STOCK.DOCUMENT_DATE >= to_date ('".$data['from_date']."', 'yyyy/mm/dd') and STOCK.DOCUMENT_DATE < to_date ('".$data['to_date']."', 'yyyy/mm/dd') + 1)";
                                $date_field = " AND GRN.DOCUMENT_DATE <= to_date ('".$from_date."', 'yyyy/mm/dd')";
                                $usage_date_field = " AND USAGE.USAGE_DATE >= to_date ('".$data['from_date']."', 'yyyy/mm/dd') AND USAGE.USAGE_DATE < to_date ('".$data['to_date']."', 'yyyy/mm/dd') + 1";
                                $usage_opening_date_field = " AND USAGE.USAGE_DATE < to_date ('".$data['from_date']."', 'yyyy/mm/dd')";
                                $usage_report_date = "TRUNC(USAGE.USAGE_DATE)";
                            }


                            $store_stock = 0;

                           /* $store_qry = "SELECT SUM (NVL (QTY_BASE_UNIT_VALUE, 0)) qty
                                   FROM VW_PURC_STOCK_DTL GRN
                                   WHERE GRN.PRODUCT_ID =  $productDtl->product_id
                                    AND GRN.BUSINESS_ID = ".auth()->user()->business_id."
                                    AND GRN.COMPANY_ID = ".auth()->user()->company_id."
                                    AND  GRN.branch_id in( ".implode(",",$data['branch_ids']).")
                                    $date_field";*/
                            $store_qry = "SELECT (SUM (NVL (QTY_IN, 0)) - SUM (NVL (QTY_OUT, 0)) ) qty
                                   FROM VW_PURC_STOCK_DTL GRN
                                   WHERE GRN.PRODUCT_ID =  $productDtl->product_id
                                    AND GRN.BUSINESS_ID = ".auth()->user()->business_id."
                                    AND GRN.COMPANY_ID = ".auth()->user()->company_id."
                                    AND  GRN.branch_id in( ".implode(",",$data['branch_ids']).")
                                    AND GRN.posted = 1
                                    $date_field";

                           // dd($store_qry);
                            $store_stock = DB::selectOne($store_qry);
                            $store_stock = ($store_stock->qty != null)?$store_stock->qty:0;

                            $store_stock_item_qry = "SELECT NVL(SUM(NVL(QTY_BASE_UNIT_VALUE, 0)), 0) qty
                                   FROM VW_PURC_STOCK_DTL GRN
                                   WHERE GRN.PRODUCT_ID = $productDtl->product_id
                                    AND GRN.BUSINESS_ID = ".auth()->user()->business_id."
                                    AND GRN.COMPANY_ID = ".auth()->user()->company_id."
                                    AND GRN.branch_id in( ".implode(",",$data['branch_ids']).")
                                    AND GRN.posted = 1
                                    $date_field";
                            $store_stock_item = DB::selectOne($store_stock_item_qry)->qty ?? 0;

                            // Recipe usage is maintained outside the stock ledger. Include usage
                            // before the report range in opening balance, then the dated rows below
                            // reduce the running balance inside the selected period.
                            $recipe_usage_opening_qry = "SELECT NVL(SUM(USAGE.PRODUCT_QUANTITY_USED), 0) qty
                                FROM ORDER_RECIPE_USAGES USAGE
                                WHERE USAGE.PRODUCT_ID = $productDtl->product_id
                                    AND USAGE.RESTAURANT_ID in (".implode(",",$data['branch_ids']).")
                                    $usage_opening_date_field";
                            $recipe_usage_opening = DB::selectOne($recipe_usage_opening_qry)->qty ?? 0;
                            $store_stock_item -= (float) $recipe_usage_opening;

                               $dt = '';
                               if(count($data['all_document_type']) != 0){
                                   $dt .= "AND STOCK.DOCUMENT_TYPE in( '".implode("','",$data['all_document_type'])."') ";
                               }
                               if(count($data['store']) != 0){
                                   $dt .= "AND STOCK.SALES_STORE_ID in( '".implode("','",$data['store'])."') ";
                               }
                                if(isset($data['month_wise']) && $data['month_wise']){
                                    $query = "SELECT PRODUCT_NAME, PRODUCT_BARCODE_ID, PRODUCT_BARCODE_PACKING, STOCK_EXPIRY, DOCUMENT_DATE, DOCUMENT_ID, DOCUMENT_TYPE, BRANCH_NAME, DOCUMENT_CODE, QTY_IN, IN_RATE, QTY_OUT, OUT_RATE, BAL_RATE, BONUS_QTY_IN, TRANSFER_FROM_BRANCH_ID, TRANSFER_TO_BRANCH_ID, SORTING_ID FROM (
                                            SELECT PROD.PRODUCT_NAME , STOCK.PRODUCT_BARCODE_ID, NVL(STOCK.PRODUCT_PACKING, 1) PRODUCT_BARCODE_PACKING, STOCK.STOCK_EXPIRY,
                                            STOCK.DOCUMENT_DATE , STOCK.DOCUMENT_ID, STOCK.DOCUMENT_TYPE , BR.BRANCH_NAME ,
                                            STOCK.DOCUMENT_CODE ,
                                            NVL (STOCK.QTY_IN, 0) +  NVL (STOCK.BONUS_QTY_IN, 0) QTY_IN  ,
                                            CASE
                                                WHEN UPPER(STOCK.DOCUMENT_TYPE) = 'GRN' THEN NVL((
                                                    SELECT MAX(DTL.TBL_PURC_GRN_DTL_RATE)
                                                    FROM TBL_PURC_GRN_DTL DTL
                                                    WHERE DTL.GRN_ID = STOCK.DOCUMENT_ID
                                                      AND DTL.PRODUCT_ID = STOCK.PRODUCT_ID
                                                      AND DTL.PRODUCT_BARCODE_ID = STOCK.PRODUCT_BARCODE_ID
                                                      AND DTL.BUSINESS_ID = STOCK.BUSINESS_ID
                                                      AND DTL.COMPANY_ID = STOCK.COMPANY_ID
                                                      AND DTL.BRANCH_ID = STOCK.BRANCH_ID
                                                ), 0)
                                                ELSE NVL(STOCK.STOCK_RATE, NVL(DOCUMENT_ACT_RATE, 0))
                                            END IN_RATE ,
                                            STOCK.QTY_OUT  +   BONUS_QTY_OUT   QTY_OUT,
                                            CASE WHEN UPPER(STOCK.DOCUMENT_TYPE) = 'ST' THEN NVL((
                                                SELECT MAX(DTL.STOCK_DTL_RATE)
                                                FROM TBL_INVE_STOCK_DTL DTL
                                                WHERE DTL.STOCK_ID = STOCK.DOCUMENT_ID
                                                  AND DTL.PRODUCT_ID = STOCK.PRODUCT_ID
                                                  AND DTL.PRODUCT_BARCODE_ID = STOCK.PRODUCT_BARCODE_ID
                                                  AND DTL.BUSINESS_ID = STOCK.BUSINESS_ID
                                                  AND DTL.COMPANY_ID = STOCK.COMPANY_ID
                                            ), 0) ELSE NVL(NULLIF(STOCK.STOCK_RATE, 0), NVL(NULLIF(STOCK.DOCUMENT_ACT_RATE, 0), NVL(STOCK.DOCUMENT_RATE, 0))) END OUT_RATE ,
                                            NVL(STOCK.DOCUMENT_RATE, 0) BAL_RATE ,
                                            STOCK.BONUS_QTY_IN ,  STOCK.TRANSFER_FROM_BRANCH_ID, STOCK.TRANSFER_TO_BRANCH_ID , SORTING_ID
                                            FROM  VW_PURC_STOCK_DTL  STOCK
                                            INNER JOIN VW_PURC_PRODUCT PROD ON STOCK.PRODUCT_ID = PROD.PRODUCT_ID
                                            LEFT JOIN TBL_SOFT_BRANCH BR ON BR.BRANCH_ID = STOCK.BRANCH_ID
                                            WHERE PROD.PRODUCT_ID = ".$productDtl->product_id."
                                                $date_field2
                                                $dt
                                                AND STOCK.posted = 1
                                                AND STOCK.BUSINESS_ID = ".auth()->user()->business_id."
                                                AND STOCK.COMPANY_ID = ".auth()->user()->company_id."
                                                AND STOCK.BRANCH_ID in( ".implode(",",$data['branch_ids']).")
                                                and DOCUMENT_TYPE NOT in ('POS','RPOS')

                                                union all
                                                SELECT
                                                    PROD.PRODUCT_NAME ,CASE WHEN MIN(STOCK.PRODUCT_BARCODE_ID) = MAX(STOCK.PRODUCT_BARCODE_ID) THEN MIN(STOCK.PRODUCT_BARCODE_ID) END PRODUCT_BARCODE_ID, MAX(STOCK.PRODUCT_PACKING) PRODUCT_BARCODE_PACKING, MAX(STOCK.STOCK_EXPIRY) STOCK_EXPIRY ,
                                                    MAX(STOCK.DOCUMENT_DATE) DOCUMENT_DATE , MAX(STOCK.DOCUMENT_ID) DOCUMENT_ID, STOCK.DOCUMENT_TYPE ,
                                                    MAX(BR.BRANCH_NAME) BRANCH_NAME ,
                                                    MAX(STOCK.DOCUMENT_CODE) DOCUMENT_CODE ,
                                                    SUM(NVL (STOCK.QTY_IN, 0) +  NVL (STOCK.BONUS_QTY_IN, 0)) QTY_IN  ,
                                                    MAX(
                                                        CASE
                                                            WHEN UPPER(STOCK.DOCUMENT_TYPE) = 'GRN' THEN NVL((
                                                                SELECT MAX(DTL.TBL_PURC_GRN_DTL_RATE)
                                                                FROM TBL_PURC_GRN_DTL DTL
                                                                WHERE DTL.GRN_ID = MAX(STOCK.DOCUMENT_ID)
                                                                  AND DTL.PRODUCT_ID = MAX(STOCK.PRODUCT_ID)
                                                                  AND DTL.PRODUCT_BARCODE_ID = MAX(STOCK.PRODUCT_BARCODE_ID)
                                                                  AND DTL.BUSINESS_ID = MAX(STOCK.BUSINESS_ID)
                                                                  AND DTL.COMPANY_ID = MAX(STOCK.COMPANY_ID)
                                                                  AND DTL.BRANCH_ID = MAX(STOCK.BRANCH_ID)
                                                            ), 0)
                                                            ELSE NVL(MAX(STOCK.STOCK_RATE), NVL(MAX(DOCUMENT_ACT_RATE), 0))
                                                        END
                                                    )  IN_RATE ,
                                                    SUM(STOCK.QTY_OUT  +   BONUS_QTY_OUT)   QTY_OUT,
                                                    MAX(NVL(NULLIF(STOCK.STOCK_RATE, 0), NVL(NULLIF(STOCK.DOCUMENT_ACT_RATE, 0), NVL(STOCK.DOCUMENT_RATE, 0)))) OUT_RATE ,
                                                    MAX(NVL(STOCK.DOCUMENT_RATE, 0)) BAL_RATE ,
                                                    SUM(STOCK.BONUS_QTY_IN) BONUS_QTY_IN ,   MAX(STOCK.TRANSFER_FROM_BRANCH_ID) TRANSFER_FROM_BRANCH_ID , MAX(STOCK.TRANSFER_TO_BRANCH_ID) TRANSFER_TO_BRANCH_ID ,
                                                    MAX(SORTING_ID ) SORTING_ID
                                                FROM
                                                    VW_PURC_STOCK_DTL  STOCK
                                                INNER JOIN VW_PURC_PRODUCT PROD ON STOCK.PRODUCT_ID = PROD.PRODUCT_ID
                                                LEFT JOIN TBL_SOFT_BRANCH BR ON BR.BRANCH_ID = STOCK.BRANCH_ID
                                                WHERE PROD.PRODUCT_ID = ".$productDtl->product_id."
                                                    $date_field2
                                                    $dt
                                                    AND STOCK.posted = 1
                                                    AND STOCK.BUSINESS_ID = ".auth()->user()->business_id."
                                                    AND STOCK.COMPANY_ID = ".auth()->user()->company_id."
                                                    AND STOCK.BRANCH_ID in( ".implode(",",$data['branch_ids']).")
                                                    and DOCUMENT_TYPE in ('POS','RPOS')
                                                GROUP BY to_char(STOCK.DOCUMENT_DATE, 'YYYY-MM'),
                                                    PROD.PRODUCT_NAME, STOCK.DOCUMENT_TYPE, STOCK.PRODUCT_PACKING

                                            UNION ALL

                                            SELECT
                                                MAX(PROD.PRODUCT_NAME) , NULL PRODUCT_BARCODE_ID, 1 PRODUCT_BARCODE_PACKING, NULL STOCK_EXPIRY,
                                                $usage_report_date DOCUMENT_DATE ,
                                                NVL(MIN(USAGE.ID), 0) DOCUMENT_ID,
                                                'Recipe Consumption' DOCUMENT_TYPE ,
                                                BRU.BRANCH_NAME ,
                                                'RECIPE-CONSUMPTION' DOCUMENT_CODE ,
                                                0 QTY_IN  ,
                                                0 IN_RATE ,
                                                NVL(SUM(USAGE.PRODUCT_QUANTITY_USED), 0) QTY_OUT,
                                                0 OUT_RATE ,
                                                0 BAL_RATE ,
                                                0 BONUS_QTY_IN ,
                                                NULL TRANSFER_FROM_BRANCH_ID,
                                                NULL TRANSFER_TO_BRANCH_ID,
                                                999999 SORTING_ID
                                            FROM ORDER_RECIPE_USAGES USAGE
                                            INNER JOIN TBL_SOFT_BRANCH BRU
                                                ON BRU.BRANCH_ID = USAGE.RESTAURANT_ID
                                            INNER JOIN TBL_PURC_PRODUCT PROD
                                                ON PROD.PRODUCT_ID = USAGE.PRODUCT_ID
                                            WHERE USAGE.PRODUCT_ID = ".$productDtl->product_id."
                                                AND USAGE.RESTAURANT_ID IN (".implode(",",$data['branch_ids']).")
                                                $usage_date_field
                                            GROUP BY TRUNC(USAGE.USAGE_DATE), BRU.BRANCH_ID, BRU.BRANCH_NAME

                                            ) STOCK
                                            ORDER BY DOCUMENT_DATE, SORTING_ID, DOCUMENT_CODE";

                                }else{
                                    $query = "SELECT PRODUCT_NAME, PRODUCT_BARCODE_ID, PRODUCT_BARCODE_BARCODE, UOM_NAME, PRODUCT_BARCODE_PACKING, STOCK_EXPIRY, DOCUMENT_DATE, DOCUMENT_ID, DOCUMENT_TYPE, BRANCH_NAME, DOCUMENT_CODE, QTY_IN, IN_RATE, QTY_OUT, OUT_RATE, BAL_RATE, BONUS_QTY_IN, TRANSFER_FROM_BRANCH_ID, TRANSFER_TO_BRANCH_ID, SORTING_ID FROM (
                                        SELECT PROD.PRODUCT_NAME , STOCK.PRODUCT_BARCODE_ID, '' PRODUCT_BARCODE_BARCODE,
                                        '' UOM_NAME ,  NVL(STOCK.PRODUCT_PACKING, 1) PRODUCT_BARCODE_PACKING ,STOCK.STOCK_EXPIRY,
                                        STOCK.DOCUMENT_DATE , STOCK.DOCUMENT_ID, STOCK.DOCUMENT_TYPE , BR.BRANCH_NAME ,
                                        STOCK.DOCUMENT_CODE ,
                                        NVL (STOCK.QTY_IN, 0) +  NVL (STOCK.BONUS_QTY_IN, 0) QTY_IN  ,
                                        CASE
                                            WHEN UPPER(STOCK.DOCUMENT_TYPE) = 'GRN' THEN NVL((
                                                SELECT MAX(DTL.TBL_PURC_GRN_DTL_RATE)
                                                FROM TBL_PURC_GRN_DTL DTL
                                                WHERE DTL.GRN_ID = STOCK.DOCUMENT_ID
                                                  AND DTL.PRODUCT_ID = STOCK.PRODUCT_ID
                                                  AND DTL.PRODUCT_BARCODE_ID = STOCK.PRODUCT_BARCODE_ID
                                                  AND DTL.BUSINESS_ID = STOCK.BUSINESS_ID
                                                  AND DTL.COMPANY_ID = STOCK.COMPANY_ID
                                                  AND DTL.BRANCH_ID = STOCK.BRANCH_ID
                                            ), 0)
                                            ELSE NVL(STOCK.STOCK_RATE, NVL(DOCUMENT_ACT_RATE, 0))
                                        END IN_RATE ,
                                        STOCK.QTY_OUT  +   BONUS_QTY_OUT   QTY_OUT,
                                        CASE WHEN UPPER(STOCK.DOCUMENT_TYPE) = 'ST' THEN NVL((
                                            SELECT MAX(DTL.STOCK_DTL_RATE)
                                            FROM TBL_INVE_STOCK_DTL DTL
                                            WHERE DTL.STOCK_ID = STOCK.DOCUMENT_ID
                                              AND DTL.PRODUCT_ID = STOCK.PRODUCT_ID
                                              AND DTL.PRODUCT_BARCODE_ID = STOCK.PRODUCT_BARCODE_ID
                                              AND DTL.BUSINESS_ID = STOCK.BUSINESS_ID
                                              AND DTL.COMPANY_ID = STOCK.COMPANY_ID
                                        ), 0) ELSE NVL(NULLIF(STOCK.STOCK_RATE, 0), NVL(NULLIF(STOCK.DOCUMENT_ACT_RATE, 0), NVL(STOCK.DOCUMENT_RATE, 0))) END OUT_RATE ,
                                        NVL(STOCK.DOCUMENT_RATE, 0) BAL_RATE ,
                                        STOCK.BONUS_QTY_IN ,  STOCK.TRANSFER_FROM_BRANCH_ID, STOCK.TRANSFER_TO_BRANCH_ID,
                                        NVL(STOCK.SORTING_ID, 0) SORTING_ID
                                    FROM
                                        VW_PURC_STOCK_DTL  STOCK
                                    INNER JOIN VW_PURC_PRODUCT PROD ON STOCK.PRODUCT_ID = PROD.PRODUCT_ID
                                    LEFT JOIN TBL_SOFT_BRANCH BR ON BR.BRANCH_ID = STOCK.BRANCH_ID
                                    WHERE PROD.PRODUCT_ID = ".$productDtl->product_id."
                                        $date_field2
                                        $dt
                                        AND STOCK.posted = 1
                                        AND STOCK.BUSINESS_ID = ".auth()->user()->business_id."
                                        AND STOCK.COMPANY_ID = ".auth()->user()->company_id."
                                        AND STOCK.BRANCH_ID in( ".implode(",",$data['branch_ids']).")

                                    UNION ALL

                                    SELECT
                                        MAX(PROD.PRODUCT_NAME) , NULL PRODUCT_BARCODE_ID, '' PRODUCT_BARCODE_BARCODE,
                                        '' UOM_NAME , 1 PRODUCT_BARCODE_PACKING , NULL STOCK_EXPIRY,
                                        $usage_report_date DOCUMENT_DATE ,
                                        NVL(MIN(USAGE.ID), 0) DOCUMENT_ID,
                                        'Recipe Consumption' DOCUMENT_TYPE ,
                                        BRU.BRANCH_NAME ,
                                        'RECIPE-CONSUMPTION' DOCUMENT_CODE ,
                                        0 QTY_IN  ,
                                        0 IN_RATE ,
                                        NVL(SUM(USAGE.PRODUCT_QUANTITY_USED), 0) QTY_OUT,
                                        0 OUT_RATE ,
                                        0 BAL_RATE ,
                                        0 BONUS_QTY_IN ,
                                        NULL TRANSFER_FROM_BRANCH_ID,
                                        NULL TRANSFER_TO_BRANCH_ID,
                                        999999 SORTING_ID
                                    FROM ORDER_RECIPE_USAGES USAGE
                                    INNER JOIN TBL_SOFT_BRANCH BRU
                                        ON BRU.BRANCH_ID = USAGE.RESTAURANT_ID
                                    INNER JOIN TBL_PURC_PRODUCT PROD
                                        ON PROD.PRODUCT_ID = USAGE.PRODUCT_ID
                                    WHERE USAGE.PRODUCT_ID = ".$productDtl->product_id."
                                        AND USAGE.RESTAURANT_ID IN (".implode(",",$data['branch_ids']).")
                                        $usage_date_field
                                    GROUP BY TRUNC(USAGE.USAGE_DATE), BRU.BRANCH_ID, BRU.BRANCH_NAME
                                    ) STOCK_RESULT
                                    ORDER BY DOCUMENT_DATE, SORTING_ID, DOCUMENT_CODE";
                                }


//dd($query);

                               $listdata = \Illuminate\Support\Facades\DB::select($query);

                               $totInQty = 0;
                               $totOutQty = 0;
                               $BalQty = 0;
                               $totInQtyPerItem = 0;
                               $totOutQtyPerItem = 0;
                               $totalInAmount = 0;
                               $totalOutAmount = 0;
                               $BalQtyPerItem = (float)$store_stock_item;
                               $BalPrice = 0;
                               $runningAveragePrice = $runningAverageBaseQty > 0
                                   ? $runningAverageAmount / $runningAverageBaseQty
                                   : 0;
                        @endphp
                        <tr>
                            <td class="text-center">{{date('d-m-Y',strtotime($from_date))}}</td>
                            <td colspan="5"> - - - - Opening Stock - - - - </td>
                            <td class="text-center"></td>
                            <td class="text-center"></td>
                            <td class="text-center"></td>
                            <td class="text-center"></td>
                            <td class="text-center"></td>
                            <td class="text-center"></td>
                            <td class="text-center"></td>
                            <td class="text-center"></td>
                            <td class="text-center"></td>
                            <td class="text-center"></td>
                            <td class="text-center"></td>
                            <td class="text-center">{{number_format($store_stock_item,3)}}</td>
                            <td class="text-center">{{number_format($runningAveragePrice,3)}}</td>
                           {{-- <td></td>--}}
                        </tr>
                        @foreach($listdata as $row)
                            @php
                                if(in_array(strtolower(strtoupper($row->document_type)),['sr'])){
                                    $qty_in = abs($row->qty_in);
                                }else{
                                    $qty_in = $row->qty_in;
                                }
                                $in_rate = $row->in_rate;
                                $qty_out = $row->qty_out;
                                if ($loop->first){
                                    $Qty = ((float)$store_stock + (float)$qty_in) - $qty_out;
                                    /*$qty_in = (float)$store_stock + (float)$qty_in;*/
                                }else{
                                    $Qty = $qty_in - $qty_out;
                                }
                                if($qty_in == 0){$InRate = 0;}else{$InRate = $in_rate;}

                                $packing = (float) ($row->product_barcode_packing ?? 1);
                                if ($packing <= 0) $packing = 1;
                                $qty_in_per_item = (float)$qty_in * $packing;
                                $qty_out_per_item = (float)$qty_out * $packing;
                                $in_rate_per_item = (float)$InRate / $packing;
                                $inAmount = (float)$qty_in * (float)$InRate;
                                $totalInAmount += $inAmount;
                                if ((float)$qty_in > 0 && (float)$InRate > 0) {
                                    $runningAverageAmount += $inAmount;
                                    $runningAverageBaseQty += (float)$qty_in * $packing;
                                }
                                $runningAveragePrice = $runningAverageBaseQty > 0
                                    ? $runningAverageAmount / $runningAverageBaseQty
                                    : 0;
                                $OutRate = (float)$qty_out == 0 ? 0 : (float)$row->out_rate;
                                $out_rate_per_item = $OutRate / $packing;
                                $outAmount = (float)$qty_out * $OutRate;
                                $totalOutAmount += $outAmount;
                                $BalQtyPerItem += $qty_in_per_item - $qty_out_per_item;

                                $totInQty += $qty_in;
                                $totOutQty += $qty_out;
                                $BalQty += $Qty;
                                $totInQtyPerItem += $qty_in_per_item;
                                $totOutQtyPerItem += $qty_out_per_item;
                                if($qty_in != 0 && in_array(strtolower(strtoupper($row->document_type)),['grn','str','os']))
                                {
                                    $BalPrice = $in_rate;
                                }
                                if($row->bonus_qty_in != null && abs($row->bonus_qty_in) != 0 && in_array(strtolower(strtoupper($row->document_type)),['grn','str','os'])){
                                    $BalPrice = $row->bal_rate;
                                }
                                if(isset($row->document_type) && in_array(strtolower(strtoupper($row->document_type)),['grn','str','os'])){
                                    $expire_date = date('d-m-Y',strtotime($row->stock_expiry));
                                    $expire_date = ($expire_date == '01-01-1970') ? '' : $expire_date;
                                    session()->forget('expire_date');
                                    session(['expire_date' => $expire_date]);
                                }else{
                                    $expire_date = session('expire_date');
                                }
                                $bg = in_array(strtolower(strtoupper($row->document_type)),['pos','rpos'])?'#faebd7':"";
                                $isRecipeConsumption = strtolower(trim((string) $row->document_type)) === 'recipe consumption';
                            @endphp
                            @php
                                $rowBarcode = $productBarcodesById->get($row->product_barcode_id ?? null, '');
                                if (($data['month_wise'] ?? false) && !$rowBarcode
                                    && in_array(strtoupper((string) $row->document_type), ['POS', 'RPOS'], true)) {
                                    $rowBarcode = 'Multiple barcodes';
                                }
                            @endphp
                            <tr class="{{isset($row->document_type)? strtolower(strtoupper($row->document_type)):''}}" style="background: {{$bg}};">
                                <td class="text-center">{{date('d-m-Y', strtotime($row->document_date))}}</td>
                                <td class="text-center">{{isset($row->document_type)? $row->document_type:''}}</td>
                                <td class="text-center">{{isset($row->branch_name)? $row->branch_name:''}}</td>
                                <td class="text-center"><span class="generate_report" data-id="{{$row->document_id}}" data-type="{{$row->document_type}}">{{isset($row->document_code)? $row->document_code:''}}</span></td>
                                <td class="text-center">{{$rowBarcode}}</td>
                                <td style="font-size: 12px;">
                                    @if($isRecipeConsumption)
                                        <span style="color: #2196f3 !important; font-size: 12px;">Recipe Consumption</span><br>
                                    @endif
                                    @if(strtolower((string) $row->document_type) === 'di')
                                        <span style="color: #2196f3 !important; font-size: 12px;">Damaged Items</span><br>
                                    @endif
                                    @if($row->bonus_qty_in != null && abs($row->bonus_qty_in) != 0)
                                        <span style="color: #2196f3 !important; font-size: 12px;">FOC Qty :</span> {{$row->bonus_qty_in}} ,
                                    @endif
                                    @if($row->transfer_from_branch_id != null && $row->transfer_from_branch_id != "" && $row->transfer_from_branch_id != 0)
                                        @php
                                            $branch = \App\Models\TblSoftBranch::where('branch_id',$row->transfer_from_branch_id)->first(['branch_short_name']);
                                        @endphp
                                        @if(strtolower((string) $row->document_type) !== 'di')
                                            <span style="color: #2196f3 !important; font-size: 12px;">Transfer From :</span> {{$branch->branch_short_name ?? ''}} <br>
                                        @endif
                                    @endif
                                    @if($row->transfer_to_branch_id != null && $row->transfer_to_branch_id != "" && $row->transfer_to_branch_id != 0)
                                        @php
                                            $branch = \App\Models\TblSoftBranch::where('branch_id',$row->transfer_to_branch_id)->first(['branch_short_name']);
                                        @endphp
                                        <span style="color: #2196f3 !important; font-size: 12px;">Transfer To :</span> {{$branch->branch_short_name ?? ''}} <br>
                                    @endif
                                </td>
                                <td class="text-center">{{number_format($packing,3)}}</td>
                                <td class="text-center">{{$qty_in==0?"":number_format($qty_in,3)}}</td>
                                <td class="text-center">{{$InRate==0?"":number_format($InRate,3)}}</td>
                                <td class="text-center">{{$inAmount==0?"":number_format($inAmount,3)}}</td>
                                <td class="text-center">{{$qty_out==0 && !$isRecipeConsumption ? "" : number_format($qty_out,3)}}</td>
                                <td class="text-center">{{$qty_out==0 && !$isRecipeConsumption ? "" : number_format($OutRate,3)}}</td>
                                <td class="text-center">{{$qty_out==0 && !$isRecipeConsumption ? "" : number_format($outAmount,3)}}</td>
                                <td class="text-center">{{$qty_in_per_item==0?"":number_format($qty_in_per_item,3)}}</td>
                                <td class="text-center">{{$in_rate_per_item==0?"":number_format($in_rate_per_item,3)}}</td>
                                <td class="text-center">{{$qty_out_per_item==0 && !$isRecipeConsumption ? "" : number_format($qty_out_per_item,3)}}</td>
                                <td class="text-center">{{$qty_out==0 && !$isRecipeConsumption ? "" : number_format($out_rate_per_item,3)}}</td>
                                <td class="text-center">{{number_format($BalQtyPerItem,3)}}</td>
                                <td class="text-center">{{number_format($runningAveragePrice,3)}}</td>
                                {{--<td class="text-center">{{number_format($BalPrice,3)}}</td>--}}
                                {{--<td class="text-center">{{$expire_date}}</td>--}}
                           </tr>
                        @endforeach
                        <tr class="grand_total">
                            <td colspan="7" class="rep-font-bold">Total:</td>
                            <td class="text-center rep-font-bold"></td>
                            <td class="text-right rep-font-bold"></td>
                            <td class="text-center rep-font-bold">{{number_format($totalInAmount,3)}}</td>
                            <td class="text-center rep-font-bold"></td>
                            <td class="text-right rep-font-bold"></td>
                            <td class="text-center rep-font-bold">{{number_format($totalOutAmount,3)}}</td>
                            <td class="text-center rep-font-bold">{{number_format($totInQtyPerItem,3)}}</td>
                            <td class="text-right rep-font-bold"></td>
                            <td class="text-center rep-font-bold">{{number_format($totOutQtyPerItem,3)}}</td>
                            <td class="text-right rep-font-bold"></td>
                            <td class="text-center rep-font-bold">{{number_format($BalQtyPerItem,3)}}</td>
                            <td class="text-center rep-font-bold">{{number_format($averagePricePerItem,3)}}</td>
                            {{--<td class="text-right rep-font-bold"></td>--}}
                            {{--<td class="text-right rep-font-bold"></td>--}}
                        </tr>
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
                $("#rep_sale_invoice_datatable").table2excel({
                    // exclude: ".noExport",
                    filename: "report.xls",
                });
            });
        </script>
    @endif
@endsection




