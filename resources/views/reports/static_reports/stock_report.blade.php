@extends('layouts.report')
@section('title', 'Stock Report')

@section('pageCSS')
    <style>
        /* Styles go here */
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
       // dd($data);
    @endphp
    <div class="kt-portlet" id="kt_portlet_table">
        <div class="kt-portlet__head">
            <div class="kt-invoice__brand">
                <h3 class="kt-invoice__title">{{strtoupper($data['page_title'])}}</h3>
                <h6 class="kt-invoice__criteria">
                    <span style="color: #e27d00;">Date:</span>
                    @if($data['date_time_wise'] == 1)
                        <span style="color: #5578eb;">{{" ".date('d-m-Y', strtotime($data['time_from']))." "}}</span>
                    @else
                        <span style="color: #5578eb;">{{" ".date('d-m-Y', strtotime($data['date']))." "}}</span>
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
                        @foreach($data['product_ids'] as $product)
                            <span style="color: #5578eb;">{{$product}}</span><span style="color: #fd397a;">, </span>
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


                    $nagetivestock = "";

                    if($data['nagetivestock'] == "negative"){
                        $nagetivestock = "AND STOCK.PRODUCT_QTY < 0";
                    }
                    if($data['nagetivestock'] == "zero"){
                        $nagetivestock = "AND STOCK.PRODUCT_QTY = 0";
                    }

                    if($data['date_time_wise'] == 1){
                        $date_time_from = $data['time_from'];
                    }


                    $with_value_wise = "";
                    if($data['with_value_wise'] == 1){
                        $with_value_wise = "style=display:none;";
                    }


                    $where = "";
                    if(isset($data['supplier_ids']) && count($data['supplier_ids']) != 0){
                        $where .= " and SUP_PROD.supplier_id in (".implode(",",$data['supplier_ids']).") ";
                    }
                    if(count($data['product_ids']) != 0){
                        $where .= " and PROD.product_name in ('".implode("','",$data['product_ids'])."') ";
                    }
                    if(count($data['product_group']) != 0){
                        $inner_where = "";
                        foreach($data['product_group'] as $product_group){
                            $group_item_item = \App\Models\TblPurcGroupItem::where('group_item_id',$product_group)->first();
                            if($group_item_item->group_item_level == 1){

                            }
                            if($group_item_item->group_item_level == 2){
                                $group_items = \App\Models\TblPurcGroupItem::where('parent_group_id',$product_group)->pluck('group_item_id')->toArray();

                                $inner_where .= " PROD.group_item_id in (".implode(",",$group_items).") OR";
                            }
                            if($group_item_item->group_item_level == 3){
                                $inner_where .= " PROD.group_item_id = $product_group OR ";
                            }
                        }
                        if(!empty($inner_where)){
                            $inner_where = rtrim($inner_where, " OR ");
                            $where .= "and ( ".$inner_where." ) ";
                        }
                    }

                    if($data['date_time_wise'] == 1){
                        $date_field = " AND created_at <= to_date ('".$date_time_from."', 'yyyy/mm/dd HH24:MI')";
                    }else{
                        $date_field = "AND document_date <= to_date('".$data['date']."', 'yyyy/mm/dd')";
                    }

$vendorfrom = "";
$vendorjoin = "";
// Vendor Wise whereclause
if(isset($data['supplier_ids']) && count($data['supplier_ids']) != 0){
    $vendorfrom = " ,VW_PURC_PRODUCT_FOC_PROD_WISE SUP_PROD";
    $vendorjoin = " AND STOCK.PRODUCT_ID = SUP_PROD.PRODUCT_ID";
    $vendor = "SUP_PROD.supplier_id";
}else{
    $vendor = "PROD.supplier_id";
}



$qry = "SELECT 
  PROD_BARCODE.PRODUCT_BARCODE_BARCODE,
  PR.BRANCH_ID,
  PR.BRANCH_NAME,
  PR.product_id,
  PR.product_code,
  PR.supplier_id,
  PR.product_name,
  PR.product_arabic_name,
  PR.PRODUCT_QTY,
  NET_TP,
  SALE_RATE 
FROM
  (SELECT 
    PROD.product_id,
    PROD.product_code,
    $vendor,
    PROD.BRANCH_ID,
    PROD.BRANCH_NAME,
    product_name,
    product_arabic_name,
    PRODUCT_QTY 
  FROM
    (SELECT 
      PRODUCT_ID, 
      -- SUM(QTY_BASE_UNIT_VALUE) PRODUCT_QTY ,
      (SUM (NVL (QTY_IN, 0)) - SUM (NVL (QTY_OUT, 0)) ) PRODUCT_QTY 
    FROM
      VW_PURC_STOCK_DTL 
    WHERE branch_id in (".implode(",",$data['branch_ids']).")
        and ".$data['clause_business_id'] . $data['clause_company_id'] . " 
        $date_field
    GROUP BY PRODUCT_ID) STOCK,
    VW_PURC_PRODUCT PROD 
    $vendorfrom  
  WHERE STOCK.PRODUCT_ID = PROD.PRODUCT_ID 
    $vendorjoin
    $where
    $nagetivestock
  ) PR 
  LEFT OUTER JOIN 
    (SELECT DISTINCT 
      PRODUCT_ID,
      MAX(NET_TP) NET_TP,
      MAX(SALE_RATE) SALE_RATE 
    FROM
      TBL_PURC_PRODUCT_BARCODE_PURCH_RATE PROD_RATE 
    WHERE branch_id in (".implode(",",$data['branch_ids']).") 
    GROUP BY PRODUCT_ID) PROD_RATE 
    ON PR.PRODUCT_ID = PROD_RATE.PRODUCT_ID 
  LEFT OUTER JOIN 
    (SELECT 
      MAX(PRODUCT_BARCODE_BARCODE) PRODUCT_BARCODE_BARCODE,
      PRODUCT_ID 
    FROM
      tbl_purc_product_barcode 
    WHERE BASE_BARCODE = 1 
    GROUP BY PRODUCT_ID) PROD_BARCODE 
    ON PR.PRODUCT_ID = PROD_BARCODE.PRODUCT_ID 
ORDER BY PR.product_name";
                
                    //dd($qry);

                    $lists = \Illuminate\Support\Facades\DB::select($qry);
                    @endphp

                    <table width="100%" id="rep_sale_invoice_datatable" class="table bt-datatable table-bordered">
                        <tr class="sticky-header">
                            <th width="5%" class="text-left">Sr. No</th>
                            <th width="15%" class="text-center">BarCode</th>
                            <th width="24%" class="text-center">Product Name</th>
                            <th width="8%" class="text-right">Stock Qty</th>
                            <th width="8%" class="text-right" {{$with_value_wise}}>Net TP Rate</th>
                            <th width="8%" class="text-right" {{$with_value_wise}}>TP Stock Amount</th>
                            <th width="8%" class="text-right" {{$with_value_wise}}>Sale Rate</th>
                            <th width="8%" class="text-right" {{$with_value_wise}}>Sale Stock Amount</th>
                            <th width="8%" class="text-right" {{$with_value_wise}}>Gross Profit Amount</th>
                            <th width="8%" class="text-right" {{$with_value_wise}}>Gross Profit Perc</th>
                            {{-- <th width="5%" class="text-right">Gross Profit</th> --}}
                        </tr>
                        @php
                            $total_Stock =  0;
                            $total_net_tp =  0;
                            $netTp_rate = 0;
                            $sale_amount = 0;
                            $gross_profit_amount = 0;
                            $gross_profit = 0;
                            $total_tp_amount = 0;
                            $total_sale_rate =  0;
                            $total_sale_amount = 0;
                            $total_gross_profit_amount = 0;
                        @endphp
                        @foreach($lists as $list)
                            @php
                            //Net TP Rate
                            $netTp_rate = $list->product_qty * $list->net_tp;
                            $total_net_tp += (int)$list->net_tp;

                            //Sale Amount
                            $sale_amount = $list->product_qty * $list->sale_rate;
                            //Gross Profit Amount
                            $gross_profit_amount = $sale_amount - $netTp_rate;
                            $gross_profit_perc = 0;
                            if($netTp_rate != 0 && $gross_profit_amount != 0){
                                $gross_profit_perc = $gross_profit_amount / $netTp_rate  * 100 ;
                            }
                            @endphp

                            <tr>
                                <td>{{$loop->iteration}}</td>
                                <td>{{ $list->product_barcode_barcode }}</td>
                                <td>{{ $list->product_name }}</td>
                                <td class="text-right">
                                    @php
                                        $total_Stock += (int)$list->product_qty;
                                    @endphp
                                    {{ $list->product_qty }}
                                </td>
                                <td class="text-right" {{$with_value_wise}}>{{ number_format($list->net_tp,3) }}</td>

                                <td class="text-right" {{$with_value_wise}}>
                                    @php
                                        $total_tp_amount += (int)$netTp_rate;
                                    @endphp
                                    {{ number_format($netTp_rate,3) }}
                                </td>

                                <td class="text-right" {{$with_value_wise}}>
                                    @php
                                        $total_sale_rate += (int)$list->sale_rate;
                                    @endphp
                                    {{ number_format($list->sale_rate,3) }}
                                </td>

                                <td class="text-right" {{$with_value_wise}}>
                                    @php
                                        $total_sale_amount += (int)$sale_amount;
                                    @endphp
                                    {{ number_format($sale_amount,3) }}
                                </td>

                                <td class="text-right" {{$with_value_wise}}>
                                    @php
                                        $total_gross_profit_amount += (int)$gross_profit_amount;
                                    @endphp
                                    {{ number_format($gross_profit_amount,3) }}
                                </td>
                                <td class="text-right" {{$with_value_wise}}>
                                    {{ number_format($gross_profit_perc,3) }}
                                </td>
                                {{-- <td class="text-right">{{ number_format($gross_profit,3) }}</td> --}}
                            </tr>
                        @endforeach
                        <tr class="grand_total">
                            <td class="rep-font-bold text-right" colspan="3"><b> Total: </b></td>
                            <td class="text-right rep-font-bold">{{number_format($total_Stock,3)}}</td>
                            <td class="text-right rep-font-bold" {{$with_value_wise}}></td></td>
                            <td class="text-right rep-font-bold" {{$with_value_wise}}>{{number_format($total_tp_amount,3)}}</td>
                            <td class="text-right rep-font-bold" {{$with_value_wise}}></td>
                            <td class="text-right rep-font-bold" {{$with_value_wise}}>{{number_format($total_sale_amount,3)}}</td>
                            <td class="text-right rep-font-bold" {{$with_value_wise}}>{{number_format($total_gross_profit_amount,3)}}</td>
                            <td class="text-right rep-font-bold" {{$with_value_wise}}></td>
                            {{-- <td class="text-right rep-font-bold"></td> --}}
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
