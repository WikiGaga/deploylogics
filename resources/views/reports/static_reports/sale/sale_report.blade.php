@extends('layouts.report')
@section('title', 'Sale Report')

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
        $customerDtl = \Illuminate\Support\Facades\DB::table('tbl_sale_customer')->where('customer_id',$data['customer_ids'])->first();
        $supplierDtl = \Illuminate\Support\Facades\DB::table('tbl_purc_supplier')->where('supplier_id',$data['supplier_ids'])->first();
    @endphp
    <div class="kt-portlet" id="kt_portlet_table">
        <div class="kt-portlet__head" >
            <div class="kt-invoice__brand">
                <h3 class="kt-invoice__title">{{strtoupper($data['page_title'])}}</h3>
                <h6 class="kt-invoice__criteria">
                    <span style="color: #e27d00;">Date:</span>
                    <span style="color: #5578eb;">{{" ".date('d-m-Y', strtotime($data['date_time_from']))." to ". date('d-m-Y', strtotime($data['date_time_to']))." "}}</span>
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
                @if(isset($data['customer_ids']) && !empty($data['customer_ids']))
                    <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;">Customer:</span>
                        <span style="color: #5578eb;">{{" ".$customerDtl->customer_name." "}}</span>
                    </h6>
                @endif
                @if(isset($data['supplier_ids']) && !empty($data['supplier_ids']))
                    <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;">Supplier:</span>
                        <span style="color: #5578eb;">{{" ".$supplierDtl->supplier_name." "}}</span>
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
                @if(count($data['product_ids']) != 0)
                    <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;">Product:</span>
                        @foreach($data['product_ids'] as $product)
                            <span style="color: #5578eb;">{{$product}}</span><span style="color: #fd397a;">, </span>
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
                        $where = "";
                        /*if(count($data['customer_ids']) != 0){
                            $where .= " and customer_id in( ".implode(",",$data['customer_ids']).")";
                        }

                        if(count($data['supplier_ids']) != 0){
                          $where .= " and supplier_id in (".implode(",",$data['supplier_ids']).")";
                        }*/

                        if(isset($data['customer_ids']) && !empty($data['customer_ids']) != 0){
                            $where .= " and customer_id = ".$customerDtl->customer_id."";
                        }

                        if(isset($data['supplier_ids']) && !empty($data['supplier_ids']) != 0){
                            $where .= " and SUP_PROD.supplier_id = ".$supplierDtl->supplier_id."";
                        }
                        if(count($data['product_ids']) != 0){
                            $where .= " and product_name in ('".implode("','",$data['product_ids'])."') ";
                        }
                        if(count($data['product_group']) != 0){
                            $inner_where = "";
                            foreach($data['product_group'] as $product_group){
                                $group_item_item = \App\Models\TblPurcGroupItem::where('group_item_id',$product_group)->first();
                                if($group_item_item->group_item_level == 1){

                                }
                                if($group_item_item->group_item_level == 2){
                                    $group_items = \App\Models\TblPurcGroupItem::where('parent_group_id',$product_group)->pluck('group_item_id')->toArray();

                                    $inner_where .= " group_item_id in (".implode(",",$group_items).") OR";
                                }
                                if($group_item_item->group_item_level == 3){
                                    $inner_where .= " group_item_id = $product_group OR ";
                                }
                            }
                            if(!empty($inner_where)){
                                $inner_where = rtrim($inner_where, " OR ");
                                $where .= "and ( ".$inner_where." ) ";
                            }
                        }
$vendorfrom = "";
$vendorjoin = "";
// Vendor Wise whereclause
if(isset($data['supplier_ids']) && !empty($data['supplier_ids']) != 0){
    $vendorfrom = " ,VW_PURC_PRODUCT_FOC_PROD_WISE SUP_PROD";
    $vendorjoin = " AND VW_SALE_SALES_INVOICE.PRODUCT_ID = SUP_PROD.PRODUCT_ID";
}

$qry = "SELECT 
  BRANCH_ID,
  BRANCH_NAME,
  SALES_DATE,
  SALES_SALES_MAN,
  SALES_SALES_MAN_NAME,
  CUSTOMER_NAME,
  CASE
    WHEN SALES_TYPE = 'POS' 
    THEN 'Sale Invoice' 
    WHEN SALES_TYPE = 'RPOS' 
    THEN 'Sale Return' 
    ELSE '' 
  END AS SALES_TYPE,
  SALES_ID,
  SALES_CODE,
  PRODUCT_NAME,
  PRODUCT_BARCODE_BARCODE,
  SALES_DTL_QUANTITY,
  SALES_DTL_RATE,
  SALES_DTL_AMOUNT,
  CASE
    WHEN NVL (EXT_DISC_AMOUNT, 0) > 0 
    THEN NVL (EXT_DISC_AMOUNT, 0) / SALES_DTL_AMOUNT 
    ELSE 0 
  END DISC_PER,
  CASE
    WHEN NVL (EXT_DISC_AMOUNT, 0) > 0 
    THEN NVL (EXT_DISC_AMOUNT, 0) / SALES_DTL_QUANTITY 
    ELSE 0 
  END PER_ITEM_DISC,
  SALES_DTL_DISC_AMOUNT AS TOTAL_ITEM_DISC,
  EXT_DISC_AMOUNT INV_DISCOUNT,
  SALES_DTL_NET_AMOUNT - EXT_DISC_AMOUNT AS SALES_DTL_NET_AMOUNT 
FROM
  VW_SALE_SALES_INVOICE
  $vendorfrom
WHERE branch_id in (".implode(",",$data['branch_ids']).")
    and (created_at between to_date ('".$data['date_time_from']."', 'yyyy/mm/dd HH24:MI') and to_date ('".$data['date_time_to']."', 'yyyy/mm/dd HH24:MI') )                           
    $vendorjoin
    $where
ORDER BY SALES_DATE,
  SALES_TYPE,
  SALES_CODE";
           //dd($qry);
                        $getdata = \Illuminate\Support\Facades\DB::select($qry);
                       //  dd($getdata);
                        $list = [];
                        foreach ($getdata as $row){
                            $list[$row->branch_name][$row->sales_type][$row->customer_name][] = $row;
                        }
                       //dd($list);
                        @endphp
                        @php
                            $si_grand_total_amount = 0;
                        @endphp
                        <table width="100%" id="rep_sales_report_datatable" class="static_report_table table bt-datatable table-bordered">
                            <tr class="sticky-header">
                                <th class="text-center">S.#</th>
                                <th class="text-center">Date</th>
                                <th class="text-center">Inv#</th>
                                <th class="text-center">Item Name</th>
                                <th class="text-center">Qty/Wt(Pkt)</th>
                                <th class="text-center">Rate</th>
                                <th class="text-center">Amount</th>
                                <th class="text-center">Discount</th>
                                <th class="text-center">Net Amount</th>
                            </tr>
                            @php
                                $gtotqty = 0;
                                $gtotamount = 0;
                                $gtotdiscount = 0;
                                $gtotnetamnt = 0;
                            @endphp
                            @foreach($list as $branch_keys=>$branch_row)
                                @php
                                    $branch_name = ucwords(strtolower($branch_keys));
                                @endphp
                                <tr>
                                    <td colspan="10"><b>Company Branch: {{ucwords(strtolower($branch_keys))}}</b></td>
                                </tr>
                                @foreach($branch_row as $sale_type_key=>$sale_type_row)
                                    @php
                                        $sale_type_name = ucwords(strtolower($sale_type_key));
                                    @endphp
                                    <tr>
                                        <td colspan="10"><b>{{ucwords(strtolower($sale_type_key))}}</b></td>
                                    </tr>
                                    @php
                                        $stotqty = 0;
                                        $stotamount = 0;
                                        $stotdiscount = 0;
                                        $stotnetamnt = 0;
                                    @endphp
                                    @foreach($sale_type_row as $cust_key=>$cust_row)
                                        @php
                                            $cust_name = ucwords(strtolower($cust_key));
                                        @endphp
                                        <tr>
                                            <td colspan="10">
                                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                <b>Customer Name: </b>{{ucwords(strtolower($cust_key))}} 
                                            </td>
                                        </tr>
                                        @php
                                            $ki=1;
                                            $totqty = 0;
                                            $totamount = 0;
                                            $totdiscount = 0;
                                            $totnetamnt = 0;
                                            $disc = 0;
                                        @endphp
                                        @foreach($cust_row as $i_key=>$detail)
                                            @php
                                                
                                                $disc = $detail->per_item_disc + $detail->total_item_disc;

                                                $totqty = $totqty + $detail->sales_dtl_quantity;
                                                $totamount = $totamount + $detail->sales_dtl_amount;
                                                $totdiscount = $totdiscount + $disc;
                                                $totnetamnt = $totnetamnt + $detail->sales_dtl_net_amount;
                                                
                                                $stotqty = $stotqty + $detail->sales_dtl_quantity;
                                                $stotamount = $stotamount + $detail->sales_dtl_amount;
                                                $stotdiscount = $stotdiscount + $disc;
                                                $stotnetamnt = $stotnetamnt + $detail->sales_dtl_net_amount;
                                                
                                                $gtotqty = $gtotqty + $detail->sales_dtl_quantity;
                                                $gtotamount = $gtotamount + $detail->sales_dtl_amount;
                                                $gtotnetamnt = $gtotnetamnt + $detail->sales_dtl_net_amount;
                                                $gtotdiscount = $gtotdiscount + $disc;
                                            @endphp
                                            <tr>
                                                <td class="text-center">{{$ki}}</td>
                                                <td class="text-center">
                                                    {{date('d-m-Y', strtotime($detail->sales_date))}}
                                                </td>
                                                <td class="text-center">{{$detail->sales_code}}</td>
                                                <td class="text-left">{{$detail->product_name}}</td>
                                                <td class="text-right">{{$detail->sales_dtl_quantity}}</td>
                                                <td class="text-right">{{$detail->sales_dtl_rate}}</td>
                                                <td class="text-right">{{$detail->sales_dtl_amount}}</td>
                                                <td class="text-right">{{$disc}}</td>
                                                <td class="text-right">{{$detail->sales_dtl_net_amount}}</td>
                                            </tr>
                                            @php
                                                $ki += 1;
                                            @endphp
                                        @endforeach
                                            <tr>
                                                <td colspan="4" class="text-right"><strong>{{$cust_name}} Total: </strong></td>
                                                <td class="text-right"><strong>{{number_format($totqty,0)}}</strong></td>
                                                <td class="text-right"></td>
                                                <td class="text-right"><strong>{{number_format($totamount,0)}}</strong></td>
                                                <td class="text-right">
                                                    <strong>
                                                        {{number_format($totdiscount,0)}}
                                                    </strong>
                                                </td>
                                                <td class="text-right"><strong>{{number_format($totnetamnt,0)}}</strong></td>
                                            </tr>
                                    @endforeach
                                        <tr>
                                            <td colspan="4" class="text-right"><strong>{{$sale_type_name}} Total: </strong></td>
                                            <td class="text-right"><strong>{{number_format($stotqty,0)}}</strong></td>
                                            <td class="text-right"></td>
                                            <td class="text-right"><strong>{{number_format($stotamount,0)}}</strong></td>
                                            <td class="text-right">
                                                <strong>
                                                    {{number_format($stotdiscount,0)}}
                                                </strong>
                                            </td>
                                            <td class="text-right"><strong>{{number_format($stotnetamnt,0)}}</strong></td>
                                        </tr>
                                @endforeach
                                    <tr>
                                        <td colspan="4" class="text-right"><strong>{{$branch_name}} Total: </strong></td>
                                        <td class="text-right"><strong>{{number_format($gtotqty,0)}}</strong></td>
                                        <td class="text-right"></td>
                                        <td class="text-right"><strong>{{number_format($gtotamount,0)}}</strong></td>
                                        <td class="text-right">
                                            <strong>
                                                {{number_format($gtotdiscount,0)}}
                                            </strong>
                                        </td>
                                        <td class="text-right"><strong>{{number_format($gtotnetamnt,0)}}</strong></td>
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
{{-- @section('exportXls')
    @if($data['form_file_type'] == 'xls')
        <script>
            $(document).ready(function() {
                $("#rep_sales_report_datatable").table2excel({
                    // exclude: ".noExport",
                    filename: "report.xls",
                });
            });
        </script>
    @endif
@endsection --}}
