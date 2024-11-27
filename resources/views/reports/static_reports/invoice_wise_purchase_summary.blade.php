@extends('layouts.report')
@section('title', 'Invoice Wise Purchase Summary')

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
    @endphp
    <div class="kt-portlet" id="kt_portlet_table">
        <div class="kt-portlet__head" >
            <div class="kt-invoice__brand">
                <h3 class="kt-invoice__title">{{strtoupper($data['page_title'])}}</h3>
                <h6 class="kt-invoice__criteria">
                    <span style="color: #e27d00;">Date:</span>
                    <span style="color: #5578eb;">{{" ".date('d-m-Y', strtotime($data['from_date']))." to ". date('d-m-Y', strtotime($data['to_date']))." "}}</span>
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
                        $where = "";
                        if(count($data['supplier_ids']) != 0){
                          $where .= " and supplier_id in (".implode(",",$data['supplier_ids']).")";
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
                        if(isset($data['specific_purchase_type']) && $data['specific_purchase_type'] == 'grn'){
                            $where .= " and lower(grn_type) = 'grn' ";
                        }
                        if(isset($data['specific_purchase_type']) && $data['specific_purchase_type'] == 'pr'){
                            $where .= " and lower(grn_type) = 'pr' ";
                        }

                        $qry = "SELECT
                            GRN_DATE ,
                            SUPPLIER_NAME  ,
                            GRN_TYPE ,
                            GRN_ID ,
                            GRN_CODE ,
                            branch_short_name ,
                            COUNT(GRN_ID) ITEMS_NUMBERS ,
                            SUM(TBL_PURC_GRN_DTL_QUANTITY)  GRN_TOTAL_QTY ,
                            SUM(TBL_PURC_GRN_DTL_AMOUNT)  GRN_TOTAL_AMOUNT  ,
                            SUM(TBL_PURC_GRN_DTL_DISC_AMOUNT)  GRN_TOTAL_DISC_AMOUNT ,
                            SUM(TBL_PURC_GRN_DTL_VAT_AMOUNT) GRN_TOTAL_GST_AMOUNT ,
                            SUM(TBL_PURC_GRN_DTL_FED_AMOUNT)  GRN_TOTAL_FED_AMOUNT,
                            SUM(TBL_PURC_GRN_DTL_SPEC_DISC_AMOUNT) GRN_TOTAL_SPEC_DISC_AMOUNT  ,
                            SUM(GRN_OVERALL_DISC_AMOUNT) GRN_OVERALL_DISC_AMOUNT  ,
                            SUM(GRN_ADVANCE_TAX_AMOUNT)   GRN_ADVANCE_TAX_AMOUNT ,
                            SUM(TBL_PURC_GRN_DTL_TOTAL_AMOUNT) GRN_TOTAL_NET_AMOUNT
                        FROM 
                            VW_PURC_GRN 
                        where branch_id in (".implode(",",$data['branch_ids']).")
                            and (grn_date between to_date ('".$data['from_date']."', 'yyyy/mm/dd') and to_date ('".$data['to_date']."', 'yyyy/mm/dd') )
                            $where  
                        GROUP BY branch_short_name,SUPPLIER_NAME, GRN_DATE, GRN_ID, GRN_TYPE,GRN_CODE 
                        ORDER BY GRN_DATE";

                        //dd($qry);

                        $list = \Illuminate\Support\Facades\DB::select($qry);
                        $pi = [];
                        $pr = [];
                        foreach ($list as $list_row){
                            if(strtolower($list_row->grn_type) == 'grn'){
                                $pi[] = $list_row;
                            }
                            if(strtolower($list_row->grn_type) == 'pr'){
                                $pr[] = $list_row;
                            }
                        }
                    @endphp
                    @php
                        $pi_grand_total_amount = 0;
                        $pi_grand_total_gst_amount = 0;
                        $pi_grand_total_disc_amount = 0;
                        $pi_grand_total_vat_amount = 0;
                        $pi_grand_total_spec_disc_amount = 0;
                        $pi_grand_total_fed_amount = 0;
                        $pi_grand_total_overall_disc_amount = 0;
                        $pi_grand_total_advance_tax_amount = 0;
                        $pi_grand_total_total_amount = 0;
                        $ki = 1;
                    @endphp
                    @php
                        $pr_grand_total_amount = 0;
                        $pr_grand_total_gst_amount = 0;
                        $pr_grand_total_disc_amount = 0;
                        $pr_grand_total_vat_amount = 0;
                        $pr_grand_total_spec_disc_amount = 0;
                        $pr_grand_total_fed_amount = 0;
                        $pr_grand_total_overall_disc_amount = 0;
                        $pr_grand_total_advance_tax_amount = 0;
                        $pr_grand_total_total_amount = 0;
                        $pr_key = 1;
                    @endphp
                    <table width="100%" id="rep_sale_invoice_datatable" class="static_report_table table bt-datatable table-bordered">
                        <tr class="sticky-header">
                            <th class="text-center">S.#</th>
                            <th class="text-left">Date</th>
                            <th class="text-left">Branch</th>
                            <th class="text-left">Inv No.</th>
                            <th class="text-left">Vendor</th>
                            <th class="text-center">No. of Items</th>
                            <th class="text-center">Gross Amount</th>
                            <th class="text-center">Disc Amount</th>
                            <th class="text-center">GST Amount</th>
                            <th class="text-center">Spec.Disc</th>
                            <th class="text-center">Fed Discount</th>
                            <th class="text-center">Invoice Disc.</th>
                            <th class="text-center">Advance Tax</th>
                            <th class="text-center">Net Amount</th>
                        </tr>
                        @if(isset($data['specific_purchase_type']) && in_array($data['specific_purchase_type'],['grn','all']) )
                        <tr><td colspan="14"><h6>Purchase Invoice</h6></td></tr>
                        @foreach($pi as $key=>$product)
                            <tr>
                                <td>{{$ki}}</td>
                                <td>{{isset($product->grn_date)? date('d-m-Y', strtotime(trim(str_replace('/','-',$product->grn_date)))):''}}</td>
                                <td>{{$product->branch_short_name}}</td>
                                <td>{{$product->grn_code}}</td>
                                <td>{{$product->supplier_name}}</td>
                                <td class="text-right">{{$product->items_numbers}}</td>
                                <td class="text-right">{{number_format($product->grn_total_amount,3)}}</td>
                                <td class="text-right">{{number_format($product->grn_total_disc_amount,3)}}</td>
                                <td class="text-right">{{number_format($product->grn_total_gst_amount,3)}}</td>
                                <td class="text-right">{{number_format($product->grn_total_fed_amount,3)}}</td>
                                <td class="text-right">{{number_format($product->grn_total_spec_disc_amount,3)}}</td>
                                <td class="text-right">{{number_format($product->grn_overall_disc_amount,3)}}</td>
                                <td class="text-right">{{number_format($product->grn_advance_tax_amount,3)}}</td>
                                <td class="text-right">{{number_format($product->grn_total_net_amount,3)}}</td>
                            </tr>
                            @php
                                $ki += 1;
                                $pi_grand_total_amount += $product->grn_total_amount;
                                $pi_grand_total_disc_amount += $product->grn_total_disc_amount;
                                $pi_grand_total_gst_amount += $product->grn_total_gst_amount;
                                $pi_grand_total_spec_disc_amount += $product->grn_total_spec_disc_amount;
                                $pi_grand_total_fed_amount += $product->grn_total_fed_amount;
                                $pi_grand_total_overall_disc_amount += $product->grn_overall_disc_amount;
                                $pi_grand_total_advance_tax_amount += $product->grn_advance_tax_amount;
                                $pi_grand_total_total_amount += $product->grn_total_net_amount;
                            @endphp
                        @endforeach
                        <tr class="sub_total">
                            <td colspan="6" class="rep-font-bold">Purchase Invoice Total:</td>
                            <td class="text-right rep-font-bold">{{number_format($pi_grand_total_amount,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($pi_grand_total_disc_amount,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($pi_grand_total_gst_amount,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($pi_grand_total_fed_amount,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($pi_grand_total_spec_disc_amount,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($pi_grand_total_overall_disc_amount,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($pi_grand_total_advance_tax_amount,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($pi_grand_total_total_amount,3)}}</td>
                        </tr>
                        @endif
                        @if(isset($data['specific_purchase_type']) && in_array($data['specific_purchase_type'],['pr','all']) )
                            <tr><td colspan="14"><h6>Purchase Return</h6></td></tr>
                            @foreach($pr as $key=>$item)
                                <tr>
                                    <td>{{$pr_key}}</td>
                                    <td>{{isset($item->grn_date)? date('d-m-Y', strtotime(trim(str_replace('/','-',$item->grn_date)))):''}}</td>
                                    <td>{{$item->branch_short_name}}</td>
                                    <td>{{$item->grn_code}}</td>
                                    <td>{{$item->supplier_name}}</td>
                                    <td class="text-right">{{$item->items_numbers}}</td>
                                    <td class="text-right">{{number_format($item->grn_total_amount,3)}}</td>
                                    <td class="text-right">{{number_format($item->grn_total_disc_amount,3)}}</td>
                                    <td class="text-right">{{number_format($item->grn_total_gst_amount,3)}}</td>
                                    <td class="text-right">{{number_format($item->grn_total_fed_amount,3)}}</td>
                                    <td class="text-right">{{number_format($item->grn_total_spec_disc_amount,3)}}</td>
                                    <td class="text-right">{{number_format($item->grn_overall_disc_amount,3)}}</td>
                                    <td class="text-right">{{number_format($item->grn_advance_tax_amount,3)}}</td>
                                    <td class="text-right">{{number_format($item->grn_total_net_amount,3)}}</td>
                                </tr>
                                @php
                                    $pr_key += 1;
                                    $pr_grand_total_amount += $item->grn_total_amount;
                                    $pr_grand_total_disc_amount += $item->grn_total_disc_amount;
                                    $pr_grand_total_gst_amount += $item->grn_total_gst_amount;
                                    $pr_grand_total_spec_disc_amount += $item->grn_total_spec_disc_amount;
                                    $pr_grand_total_fed_amount += $item->grn_total_fed_amount;
                                    $pr_grand_total_overall_disc_amount += $item->grn_overall_disc_amount;
                                    $pr_grand_total_advance_tax_amount += $item->grn_advance_tax_amount;
                                    $pr_grand_total_total_amount += $item->grn_total_net_amount;
                                @endphp
                            @endforeach
                            <tr class="sub_total">
                                <td colspan="6" class="rep-font-bold">Purchase Return Total:</td>
                                <td class="text-right rep-font-bold">{{number_format($pr_grand_total_amount,3)}}</td>
                                <td class="text-right rep-font-bold">{{number_format($pr_grand_total_disc_amount,3)}}</td>
                                <td class="text-right rep-font-bold">{{number_format($pr_grand_total_gst_amount,3)}}</td>
                                <td class="text-right rep-font-bold">{{number_format($pr_grand_total_fed_amount,3)}}</td>
                                <td class="text-right rep-font-bold">{{number_format($pr_grand_total_spec_disc_amount,3)}}</td>
                                <td class="text-right rep-font-bold">{{number_format($pr_grand_total_overall_disc_amount,3)}}</td>
                                <td class="text-right rep-font-bold">{{number_format($pr_grand_total_advance_tax_amount,3)}}</td>
                                <td class="text-right rep-font-bold">{{number_format($pr_grand_total_total_amount,3)}}</td>
                            </tr>
                        @endif
                        @if(isset($data['specific_purchase_type']) && in_array($data['specific_purchase_type'],['all']) )
                        @php
                            $grand_total_amount = $pi_grand_total_amount - $pr_grand_total_amount;
                            $grand_total_gst_amount = $pi_grand_total_gst_amount - $pr_grand_total_gst_amount;
                            $grand_total_disc_amount = $pi_grand_total_disc_amount - $pr_grand_total_disc_amount;
                            $grand_total_vat_amount = $pi_grand_total_vat_amount - $pr_grand_total_vat_amount;
                            $grand_total_spec_disc_amount = $pi_grand_total_spec_disc_amount - $pr_grand_total_spec_disc_amount;
                            $grand_total_fed_amount = $pi_grand_total_fed_amount - $pr_grand_total_fed_amount;
                            $grand_total_overall_disc_amount = $pi_grand_total_overall_disc_amount - $pr_grand_total_overall_disc_amount;
                            $grand_total_advance_tax_amount = $pi_grand_total_advance_tax_amount - $pr_grand_total_advance_tax_amount;
                            $grand_total_total_amount = $pi_grand_total_total_amount - $pr_grand_total_total_amount;
                        @endphp
                        <tr class="grand_total">
                            <td colspan="6" class="rep-font-bold">Grand Total:</td>
                            <td class="text-right rep-font-bold">{{number_format($grand_total_amount,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($grand_total_disc_amount,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($grand_total_gst_amount,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($grand_total_fed_amount,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($grand_total_spec_disc_amount,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($grand_total_overall_disc_amount,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($grand_total_advance_tax_amount,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($grand_total_total_amount,3)}}</td>
                        </tr>
                        @endif
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



