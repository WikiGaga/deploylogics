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
                        <span style="color: #e27d00;">Barcode:</span>
                        <span style="color: #5578eb;">{{" ".$productDtl->product_barcode_barcode." "}}</span>
                    </h6>
                    <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;">UOM:</span>
                        <span style="color: #5578eb;">{{" ".$productDtl->uom_name." "}}</span>
                        <span style="color: #e27d00;">Packing:</span>
                        <span style="color: #5578eb;">{{" ".$productDtl->product_barcode_packing." "}}</span>
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
                            <th class="text-center" width="10%">Document Code</th>
                            <th class="text-center" width="20%">Remarks</th>
                            <th class="text-center">Inputs</th>
                            {{--<th class="text-center">Price</th>--}}
                            <th class="text-center">Outputs</th>
                            {{--<th class="text-center">Price</th>--}}
                            <th class="text-center">Balance</th>
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
                            }else{
                                $date_field2 = "AND (STOCK.DOCUMENT_DATE between to_date ('".$data['from_date']."', 'yyyy/mm/dd') and to_date ('".$data['to_date']."', 'yyyy/mm/dd'))";
                                $date_field = " AND GRN.DOCUMENT_DATE <= to_date ('".$from_date."', 'yyyy/mm/dd')";
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
                                    $date_field";

                           // dd($store_qry);
                            $store_stock = DB::selectOne($store_qry);
                            $store_stock = ($store_stock->qty != null)?$store_stock->qty:0;

                               $dt = '';
                               if(count($data['all_document_type']) != 0){
                                   $dt .= "AND STOCK.DOCUMENT_TYPE in( '".implode("','",$data['all_document_type'])."') ";
                               }
                               if(count($data['store']) != 0){
                                   $dt .= "AND STOCK.SALES_STORE_ID in( '".implode("','",$data['store'])."') ";
                               }
                                if(isset($data['month_wise']) && $data['month_wise']){
                                    $query = "SELECT* FROM (
                                            SELECT PROD.PRODUCT_NAME , STOCK.STOCK_EXPIRY,
                                            STOCK.DOCUMENT_DATE , STOCK.DOCUMENT_ID, STOCK.DOCUMENT_TYPE ,  STOCK.DOCUMENT_CODE ,
                                            NVL (STOCK.QTY_IN, 0) +  NVL (STOCK.BONUS_QTY_IN, 0) QTY_IN  ,
                                            DOCUMENT_ACT_RATE  IN_RATE ,
                                            STOCK.QTY_OUT  +   BONUS_QTY_OUT   QTY_OUT,
                                            NVL(STOCK.DOCUMENT_RATE, 0)  OUT_RATE ,
                                            NVL(STOCK.DOCUMENT_RATE, 0) BAL_RATE ,
                                            STOCK.BONUS_QTY_IN ,  STOCK.TRANSFER_FROM_BRANCH_ID, STOCK.TRANSFER_TO_BRANCH_ID , SORTING_ID
                                            FROM  VW_PURC_STOCK_DTL  STOCK  , VW_PURC_PRODUCT PROD
                                            WHERE STOCK.PRODUCT_ID = PROD.PRODUCT_ID   AND  PROD.PRODUCT_ID = ".$productDtl->product_id."
                                                $date_field2
                                                $dt
                                                AND STOCK.BUSINESS_ID = ".auth()->user()->business_id."
                                                AND STOCK.COMPANY_ID = ".auth()->user()->company_id."
                                                AND STOCK.BRANCH_ID in( ".implode(",",$data['branch_ids']).")
                                                and DOCUMENT_TYPE NOT in ('POS','RPOS')
                                            
                                                union all
                                                SELECT 
                                                    PROD.PRODUCT_NAME ,MAX(STOCK.STOCK_EXPIRY) STOCK_EXPIRY ,
                                                    MAX(STOCK.DOCUMENT_DATE) DOCUMENT_DATE , MAX(STOCK.DOCUMENT_ID) DOCUMENT_ID, STOCK.DOCUMENT_TYPE ,  MAX(STOCK.DOCUMENT_CODE) DOCUMENT_CODE ,
                                                    SUM(NVL (STOCK.QTY_IN, 0) +  NVL (STOCK.BONUS_QTY_IN, 0)) QTY_IN  ,
                                                    MAX(DOCUMENT_ACT_RATE)  IN_RATE ,
                                                    SUM(STOCK.QTY_OUT  +   BONUS_QTY_OUT)   QTY_OUT,
                                                    MAX(NVL(STOCK.DOCUMENT_RATE, 0))  OUT_RATE ,
                                                    MAX(NVL(STOCK.DOCUMENT_RATE, 0)) BAL_RATE ,
                                                    SUM(STOCK.BONUS_QTY_IN) BONUS_QTY_IN ,   MAX(STOCK.TRANSFER_FROM_BRANCH_ID) TRANSFER_FROM_BRANCH_ID , MAX(STOCK.TRANSFER_TO_BRANCH_ID) TRANSFER_TO_BRANCH_ID ,
                                                    MAX(SORTING_ID ) SORTING_ID
                                                FROM  
                                                    VW_PURC_STOCK_DTL  STOCK  , VW_PURC_PRODUCT PROD
                                                WHERE STOCK.PRODUCT_ID = PROD.PRODUCT_ID   AND  PROD.PRODUCT_ID = ".$productDtl->product_id."
                                                    $date_field2
                                                    $dt
                                                    AND STOCK.BUSINESS_ID = ".auth()->user()->business_id."
                                                    AND STOCK.COMPANY_ID = ".auth()->user()->company_id."
                                                    AND STOCK.BRANCH_ID in( ".implode(",",$data['branch_ids']).")
                                                    and DOCUMENT_TYPE in ('POS','RPOS')
                                                GROUP BY to_char(STOCK.DOCUMENT_DATE, 'YYYY-MM'),
                                                    PROD.PRODUCT_NAME ,  STOCK.DOCUMENT_TYPE
                                            ) STOCK
                                            ORDER BY STOCK.DOCUMENT_DATE , STOCK.SORTING_ID , 
                                                COALESCE(TO_NUMBER(REGEXP_SUBSTR(STOCK.DOCUMENT_CODE, '^\d+')), 0),
                                                STOCK.DOCUMENT_CODE";

                                }else{
                                    $query = "SELECT PROD.PRODUCT_NAME , '' PRODUCT_BARCODE_BARCODE,
                                        '' UOM_NAME ,  '' PRODUCT_BARCODE_PACKING ,STOCK.STOCK_EXPIRY,
                                        STOCK.DOCUMENT_DATE , STOCK.DOCUMENT_ID, STOCK.DOCUMENT_TYPE ,  STOCK.DOCUMENT_CODE ,
                                        NVL (STOCK.QTY_IN, 0) +  NVL (STOCK.BONUS_QTY_IN, 0) QTY_IN  ,
                                        DOCUMENT_ACT_RATE  IN_RATE ,
                                        STOCK.QTY_OUT  +   BONUS_QTY_OUT   QTY_OUT,
                                        NVL(STOCK.DOCUMENT_RATE, 0)  OUT_RATE ,
                                        NVL(STOCK.DOCUMENT_RATE, 0) BAL_RATE ,
                                        STOCK.BONUS_QTY_IN ,  STOCK.TRANSFER_FROM_BRANCH_ID, STOCK.TRANSFER_TO_BRANCH_ID
                                    FROM  
                                        VW_PURC_STOCK_DTL  STOCK  , VW_PURC_PRODUCT PROD
                                    WHERE STOCK.PRODUCT_ID = PROD.PRODUCT_ID   AND  PROD.PRODUCT_ID = ".$productDtl->product_id."
                                        $date_field2
                                        $dt
                                        AND STOCK.BUSINESS_ID = ".auth()->user()->business_id."
                                        AND STOCK.COMPANY_ID = ".auth()->user()->company_id."
                                        AND STOCK.BRANCH_ID in( ".implode(",",$data['branch_ids']).")
                                    ORDER BY  STOCK.DOCUMENT_DATE , STOCK.SORTING_ID    , COALESCE(TO_NUMBER(REGEXP_SUBSTR(STOCK.DOCUMENT_CODE, '^\d+')), 0), STOCK.DOCUMENT_CODE ";
                                }


//dd($query);

                               $listdata = \Illuminate\Support\Facades\DB::select($query);

                               $totInQty = 0;
                               $totOutQty = 0;
                               $BalQty = 0;
                               $BalPrice = 0;
                        @endphp
                        <tr>
                            <td class="text-center">{{date('d-m-Y',strtotime($from_date))}}</td>
                            <td colspan="3"> - - - - Opening Stock - - - - </td>
                            <td class="text-center"></td>
                            <td class="text-center"></td>
                            <td class="text-center">{{number_format($store_stock,3)}}</td>
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
                                if($qty_out == 0){$OutRate = 0;}else{$OutRate = $row->out_rate;}

                                $totInQty += $qty_in;
                                $totOutQty += $qty_out;
                                $BalQty += $Qty;
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
                                $bg = in_array(strtolower(strtoupper($row->document_type)),['pos','rpos'])?'#faebd7':""
                            @endphp
                            <tr class="{{isset($row->document_type)? strtolower(strtoupper($row->document_type)):''}}" style="background: {{$bg}};">
                                <td class="text-center">{{date('d-m-Y', strtotime($row->document_date))}}</td>
                                <td class="text-center">{{isset($row->document_type)? $row->document_type:''}}</td>
                                <td class="text-center"><span class="generate_report" data-id="{{$row->document_id}}" data-type="{{$row->document_type}}">{{isset($row->document_code)? $row->document_code:''}}</span></td>
                                <td style="font-size: 12px;">
                                    @if($row->bonus_qty_in != null && abs($row->bonus_qty_in) != 0)
                                        <span style="color: #2196f3 !important; font-size: 12px;">FOC Qty :</span> {{$row->bonus_qty_in}} ,
                                    @endif
                                    @if($row->transfer_from_branch_id != null && $row->transfer_from_branch_id != "" && $row->transfer_from_branch_id != 0)
                                        @php
                                            $branch = \App\Models\TblSoftBranch::where('branch_id',$row->transfer_from_branch_id)->first(['branch_short_name']);
                                        @endphp
                                        <span style="color: #2196f3 !important; font-size: 12px;">Transfer From :</span> {{$branch->branch_short_name ?? ''}} <br>
                                    @endif
                                    @if($row->transfer_to_branch_id != null && $row->transfer_to_branch_id != "" && $row->transfer_to_branch_id != 0)
                                        @php
                                            $branch = \App\Models\TblSoftBranch::where('branch_id',$row->transfer_to_branch_id)->first(['branch_short_name']);
                                        @endphp
                                        <span style="color: #2196f3 !important; font-size: 12px;">Transfer To :</span> {{$branch->branch_short_name ?? ''}} <br>
                                    @endif
                                </td>
                                <td class="text-center">{{$qty_in==0?"":number_format($qty_in,3)}}</td>
                                {{--<td class="text-center">{{$InRate==0?"":number_format($InRate,3)}}</td>--}}
                                <td class="text-center">{{$qty_out==0?"":number_format($qty_out,3)}}</td>
                                {{--<td class="text-center">{{$OutRate==0?"":number_format($OutRate,3)}}</td>--}}
                                <td class="text-center">{{number_format($BalQty,3)}}</td>
                                {{--<td class="text-center">{{number_format($BalPrice,3)}}</td>--}}
                                {{--<td class="text-center">{{$expire_date}}</td>--}}
                           </tr>
                        @endforeach
                        <tr class="grand_total">
                            <td colspan="4" class="rep-font-bold">Total:</td>
                            <td class="text-center rep-font-bold">{{number_format($totInQty,3)}}</td>
                            {{--<td class="text-right rep-font-bold"></td>--}}
                            <td class="text-center rep-font-bold">{{number_format($totOutQty,3)}}</td>
                            {{--<td class="text-right rep-font-bold"></td>--}}
                            <td class="text-center rep-font-bold">{{number_format($BalQty,3)}}</td>
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




