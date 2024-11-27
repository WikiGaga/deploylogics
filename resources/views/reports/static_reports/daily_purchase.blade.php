@extends('layouts.report')
@section('title', 'Daily Purchase')

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
                        if(count($data['supplier_ids']) != 0){
                          $where .= " and supplier_id in (".implode(",",$data['supplier_ids']).")";
                        }
                        if(count($data['product_ids']) != 0){
                            $where .= " and product_name in ('".implode("','",$data['product_ids'])."') ";
                        }
                        if(isset($data['specific_purchase_type']) && $data['specific_purchase_type'] == 'grn'){
                            $where .= " and lower(grn_type) = 'grn' ";
                        }
                        if(isset($data['specific_purchase_type']) && $data['specific_purchase_type'] == 'pr'){
                            $where .= " and lower(grn_type) = 'pr' ";
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

                        $qq = "select grn_date,grn_code,grn_type,branch_short_name, supplier_name, product_name, uom_name,
                        case   when GRN_TYPE ='PR' THEN tbl_purc_grn_dtl_quantity * -1 ELSE tbl_purc_grn_dtl_quantity END  tbl_purc_grn_dtl_quantity ,
                        tbl_purc_grn_dtl_rate,
                        case   when GRN_TYPE ='PR' THEN tbl_purc_grn_dtl_amount * -1 ELSE tbl_purc_grn_dtl_amount END  tbl_purc_grn_dtl_amount ,
                        case   when GRN_TYPE ='PR' THEN tbl_purc_grn_dtl_disc_amount * -1 ELSE tbl_purc_grn_dtl_disc_amount END  tbl_purc_grn_dtl_disc_amount ,
                        case   when GRN_TYPE ='PR' THEN tbl_purc_grn_dtl_vat_amount * -1 ELSE tbl_purc_grn_dtl_vat_amount END  tbl_purc_grn_dtl_vat_amount ,
                        case   when GRN_TYPE ='PR' THEN TBL_PURC_GRN_DTL_SPEC_DISC_AMOUNT * -1 ELSE TBL_PURC_GRN_DTL_SPEC_DISC_AMOUNT END  TBL_PURC_GRN_DTL_SPEC_DISC_AMOUNT ,
                        case   when GRN_TYPE ='PR' THEN TBL_PURC_GRN_DTL_FED_AMOUNT * -1 ELSE TBL_PURC_GRN_DTL_FED_AMOUNT END  TBL_PURC_GRN_DTL_FED_AMOUNT ,
                        case   when GRN_TYPE ='PR' THEN tbl_purc_grn_dtl_total_amount * -1 ELSE tbl_purc_grn_dtl_total_amount END  tbl_purc_grn_dtl_total_amount ,
                        TBL_PURC_GRN_DTL_NET_TP
                        from vw_purc_grn where branch_id in (".implode(",",$data['branch_ids']).") and (grn_date between to_date ('".$data['from_date']."', 'yyyy/mm/dd') and to_date ('".$data['to_date']."', 'yyyy/mm/dd') )
                        $where ORDER BY grn_date, grn_code";

                        $getdata = \Illuminate\Support\Facades\DB::select($qq);
                        $pi = [];
                        $pr = [];
                        foreach ($getdata as $list_row){
                            if(strtolower($list_row->grn_type) == 'grn'){
                                $pi[] = $list_row;
                            }
                            if(strtolower($list_row->grn_type) == 'pr'){
                                $pr[] = $list_row;
                            }
                        }

                        $list_pi = [];
                        foreach ($pi as $row)
                        {
                            $list_pi[$row->supplier_name][] = $row;
                        }
                        $list_pr = [];
                        foreach ($pr as $row)
                        {
                            $list_pr[$row->supplier_name][] = $row;
                        }
                    @endphp
                    @php
                        $pi_grand_total_quantity = 0;
                        $pi_grand_total_gross_amount = 0;
                        $pi_grand_total_disc_amount = 0;
                        $pi_grand_total_vat_amount = 0;
                        $pi_grand_total_spec_disc_amount = 0;
                        $pi_grand_total_fed_amount = 0;
                        $pi_grand_total_total_amount = 0;
                        $ki = 1;
                    @endphp
                    @php
                        $pr_grand_total_quantity = 0;
                        $pr_grand_total_gross_amount = 0;
                        $pr_grand_total_disc_amount = 0;
                        $pr_grand_total_vat_amount = 0;
                        $pr_grand_total_spec_disc_amount = 0;
                        $pr_grand_total_fed_amount = 0;
                        $pr_grand_total_total_amount = 0;
                        $kr = 1;
                    @endphp
                    <table width="100%" id="rep_sale_invoice_datatable" class="static_report_table table bt-datatable table-bordered">
                        <tr class="sticky-header">
                            <th class="text-center">S.#</th>
                            <th class="text-left">Date</th>
                            <th class="text-center">Branch</th>
                            <th class="text-center">Inv #</th>
                            <th class="text-center">Item Description</th>
                            <th class="text-center">Quantity</th>
                            <th class="text-center">Unit</th>
                            <th class="text-center">Rate</th>
                            <th class="text-center">Gross Amount</th>
                            <th class="text-center">Disc Amount</th>
                            <th class="text-center">GST Amount</th>
                            <th class="text-center">Spec.Disc</th>
                            <th class="text-center">FED</th>
                            <th class="text-center">Net Amount</th>
                            <th class="text-center">Item TP</th>
                        </tr>
                        @if(isset($data['specific_purchase_type']) && in_array($data['specific_purchase_type'],['grn','all']) )
                        <tr><td colspan="15"><h6>Purchase Invoice</h6></td></tr>
                        @foreach($list_pi as $key=>$row)
                            @php
                                $sub_total_quantity = 0;
                                $sub_gross_amount = 0;
                                $sub_total_disc_amount = 0;
                                $sub_total_vat_amount = 0;
                                $sub_total_spec_disc_amount = 0;
                                $sub_total_fed_amount = 0;
                                $sub_total_total_amount = 0;
                            @endphp
                            <tr>
                                <td colspan="15"><b>{{$key}}</b></td>
                            </tr>
                            @foreach($row as $k=>$product)
                                <tr>
                                    <td>{{$ki}}</td>
                                    <td>{{isset($product->grn_date)? date('d-m-Y', strtotime(trim(str_replace('/','-',$product->grn_date)))):''}}</td>
                                    <td>{{$product->branch_short_name}}</td>
                                    <td>{{$product->grn_code}}</td>
                                    <td>{{$product->product_name}}</td>
                                    <td class="text-right">{{number_format($product->tbl_purc_grn_dtl_quantity)}}</td>
                                    <td class="text-center">{{$product->uom_name}}</td>
                                    <td class="text-right">{{number_format($product->tbl_purc_grn_dtl_rate,3)}}</td>
                                    <td class="text-right">{{number_format($product->tbl_purc_grn_dtl_amount,3)}}</td>
                                    <td class="text-right">{{number_format($product->tbl_purc_grn_dtl_disc_amount,3)}}</td>
                                    <td class="text-right">{{number_format($product->tbl_purc_grn_dtl_vat_amount,3)}}</td>
                                    <td class="text-right">{{number_format($product->tbl_purc_grn_dtl_spec_disc_amount,3)}}</td>
                                    <td class="text-right">{{number_format($product->tbl_purc_grn_dtl_fed_amount,3)}}</td>
                                    <td class="text-right">{{number_format($product->tbl_purc_grn_dtl_total_amount,3)}}</td>
                                    <td class="text-right">{{number_format($product->tbl_purc_grn_dtl_net_tp,3)}}</td>
                                </tr>
                                @php
                                    $ki += 1;
                                    $sub_total_quantity += $product->tbl_purc_grn_dtl_quantity;
                                    $sub_gross_amount += $product->tbl_purc_grn_dtl_amount;
                                    $sub_total_disc_amount += $product->tbl_purc_grn_dtl_disc_amount;
                                    $sub_total_vat_amount += $product->tbl_purc_grn_dtl_vat_amount;
                                    $sub_total_spec_disc_amount += $product->tbl_purc_grn_dtl_spec_disc_amount;
                                    $sub_total_fed_amount += $product->tbl_purc_grn_dtl_fed_amount;
                                    $sub_total_total_amount += $product->tbl_purc_grn_dtl_total_amount;
                                @endphp
                            @endforeach
                            <tr class="sub_total">
                                <td colspan="5" class="rep-font-bold">( {{$key}} ) Sub Total:</td>
                                <td class="text-right rep-font-bold">{{number_format($sub_total_quantity)}}</td>
                                <td class="text-right rep-font-bold"></td>
                                <td class="text-right rep-font-bold"></td>
                                <td class="text-right rep-font-bold">{{number_format($sub_gross_amount,3)}}</td>
                                <td class="text-right rep-font-bold">{{number_format($sub_total_disc_amount,3)}}</td>
                                <td class="text-right rep-font-bold">{{number_format($sub_total_vat_amount,3)}}</td>
                                <td class="text-right rep-font-bold">{{number_format($sub_total_spec_disc_amount,3)}}</td>
                                <td class="text-right rep-font-bold">{{number_format($sub_total_fed_amount,3)}}</td>
                                <td class="text-right rep-font-bold">{{number_format($sub_total_total_amount,3)}}</td>
                                <td class="text-right rep-font-bold"></td>
                            </tr>
                            @php
                                $pi_grand_total_quantity += $sub_total_quantity;
                                $pi_grand_total_gross_amount += $sub_gross_amount;
                                $pi_grand_total_disc_amount += $sub_total_disc_amount;
                                $pi_grand_total_vat_amount += $sub_total_vat_amount;
                                $pi_grand_total_spec_disc_amount += $sub_total_spec_disc_amount;
                                $pi_grand_total_fed_amount += $sub_total_fed_amount;
                                $pi_grand_total_total_amount += $sub_total_total_amount;
                            @endphp
                        @endforeach
                        <tr class="grand_total">
                            <td colspan="5" class="rep-font-bold">Purchase Invoice Total:</td>
                            <td class="text-right rep-font-bold">{{number_format($pi_grand_total_quantity)}}</td>
                            <td class="text-right rep-font-bold"></td>
                            <td class="text-right rep-font-bold"></td>
                            <td class="text-right rep-font-bold">{{number_format($pi_grand_total_gross_amount,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($pi_grand_total_disc_amount,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($pi_grand_total_vat_amount,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($pi_grand_total_spec_disc_amount,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($pi_grand_total_fed_amount,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($pi_grand_total_total_amount,3)}}</td>
                            <td class="text-right rep-font-bold"></td>
                        </tr>
                        @endif
                        @if(isset($data['specific_purchase_type']) && in_array($data['specific_purchase_type'],['pr','all']) )
                            <tr><td colspan="15"><h6>Purchase Return</h6></td></tr>
                            @foreach($list_pr as $pr_key=>$pr_row)
                                @php
                                    $sub_total_quantity = 0;
                                    $sub_gross_amount = 0;
                                    $sub_total_disc_amount = 0;
                                    $sub_total_vat_amount = 0;
                                    $sub_total_spec_disc_amount = 0;
                                    $sub_total_fed_amount = 0;
                                    $sub_total_total_amount = 0;
                                @endphp
                                <tr>
                                    <td colspan="15"><b>{{$pr_key}}</b></td>
                                </tr>
                                @foreach($pr_row as $k=>$pr_product)
                                    <tr>
                                        <td>{{$kr}}</td>
                                        <td>{{isset($pr_product->grn_date)? date('d-m-Y', strtotime(trim(str_replace('/','-',$pr_product->grn_date)))):''}}</td>
                                        <td>{{$pr_product->branch_short_name}}</td>
                                        <td>{{$pr_product->grn_code}}</td>
                                        <td>{{$pr_product->product_name}}</td>
                                        <td class="text-right">{{number_format($pr_product->tbl_purc_grn_dtl_quantity)}}</td>
                                        <td class="text-center">{{$pr_product->uom_name}}</td>
                                        <td class="text-right">{{number_format($pr_product->tbl_purc_grn_dtl_rate,3)}}</td>
                                        <td class="text-right">{{number_format($pr_product->tbl_purc_grn_dtl_amount,3)}}</td>
                                        <td class="text-right">{{number_format($pr_product->tbl_purc_grn_dtl_disc_amount,3)}}</td>
                                        <td class="text-right">{{number_format($pr_product->tbl_purc_grn_dtl_vat_amount,3)}}</td>
                                        <td class="text-right">{{number_format($pr_product->tbl_purc_grn_dtl_spec_disc_amount,3)}}</td>
                                        <td class="text-right">{{number_format($pr_product->tbl_purc_grn_dtl_fed_amount,3)}}</td>
                                        <td class="text-right">{{number_format($pr_product->tbl_purc_grn_dtl_total_amount,3)}}</td>
                                        <td class="text-right">{{number_format($pr_product->tbl_purc_grn_dtl_net_tp,3)}}</td>
                                    </tr>
                                    @php
                                        $kr += 1;
                                        $sub_total_quantity += $pr_product->tbl_purc_grn_dtl_quantity;
                                        $sub_gross_amount += $pr_product->tbl_purc_grn_dtl_amount;
                                        $sub_total_disc_amount += $pr_product->tbl_purc_grn_dtl_disc_amount;
                                        $sub_total_vat_amount += $pr_product->tbl_purc_grn_dtl_vat_amount;
                                        $sub_total_spec_disc_amount += $pr_product->tbl_purc_grn_dtl_spec_disc_amount;
                                        $sub_total_fed_amount += $pr_product->tbl_purc_grn_dtl_fed_amount;
                                        $sub_total_total_amount += $pr_product->tbl_purc_grn_dtl_total_amount;
                                    @endphp
                                @endforeach
                                <tr class="sub_total">
                                    <td colspan="5" class="rep-font-bold">( {{$pr_key}} ) Sub Total:</td>
                                    <td class="text-right rep-font-bold">{{number_format($sub_total_quantity)}}</td>
                                    <td class="text-right rep-font-bold"></td>
                                    <td class="text-right rep-font-bold"></td>
                                    <td class="text-right rep-font-bold">{{number_format($sub_gross_amount,3)}}</td>
                                    <td class="text-right rep-font-bold">{{number_format($sub_total_disc_amount,3)}}</td>
                                    <td class="text-right rep-font-bold">{{number_format($sub_total_vat_amount,3)}}</td>
                                    <td class="text-right rep-font-bold">{{number_format($sub_total_spec_disc_amount,3)}}</td>
                                    <td class="text-right rep-font-bold">{{number_format($sub_total_fed_amount,3)}}</td>
                                    <td class="text-right rep-font-bold">{{number_format($sub_total_total_amount,3)}}</td>
                                    <td class="text-right rep-font-bold"></td>
                                </tr>
                                @php
                                    $pr_grand_total_quantity += $sub_total_quantity;
                                    $pr_grand_total_gross_amount += $sub_gross_amount;
                                    $pr_grand_total_disc_amount += $sub_total_disc_amount;
                                    $pr_grand_total_vat_amount += $sub_total_vat_amount;
                                    $pr_grand_total_spec_disc_amount += $sub_total_spec_disc_amount;
                                    $pr_grand_total_fed_amount += $sub_total_fed_amount;
                                    $pr_grand_total_total_amount += $sub_total_total_amount;
                                @endphp
                            @endforeach
                            <tr class="grand_total">
                                <td colspan="5" class="rep-font-bold">Purchase Return Total:</td>
                                <td class="text-right rep-font-bold">{{number_format($pr_grand_total_quantity)}}</td>
                                <td class="text-right rep-font-bold"></td>
                                <td class="text-right rep-font-bold"></td>
                                <td class="text-right rep-font-bold">{{number_format($pr_grand_total_gross_amount,3)}}</td>
                                <td class="text-right rep-font-bold">{{number_format($pr_grand_total_disc_amount,3)}}</td>
                                <td class="text-right rep-font-bold">{{number_format($pr_grand_total_vat_amount,3)}}</td>
                                <td class="text-right rep-font-bold">{{number_format($pr_grand_total_spec_disc_amount,3)}}</td>
                                <td class="text-right rep-font-bold">{{number_format($pr_grand_total_fed_amount,3)}}</td>
                                <td class="text-right rep-font-bold">{{number_format($pr_grand_total_total_amount,3)}}</td>
                                <td class="text-right rep-font-bold"></td>
                            </tr>
                        @endif
                        @if(isset($data['specific_purchase_type']) && in_array($data['specific_purchase_type'],['all']) )
                        @php
                            $grand_total_quantity = $pi_grand_total_quantity + $pr_grand_total_quantity;
                            $grand_total_gross_amount = $pi_grand_total_gross_amount + $pr_grand_total_gross_amount;
                            $grand_total_disc_amount = $pi_grand_total_disc_amount + $pr_grand_total_disc_amount;
                            $grand_total_vat_amount = $pi_grand_total_vat_amount + $pr_grand_total_vat_amount;
                            $grand_total_spec_disc_amount = $pi_grand_total_spec_disc_amount + $pr_grand_total_spec_disc_amount;
                            $grand_total_fed_amount = $pi_grand_total_fed_amount + $pr_grand_total_fed_amount;
                            $grand_total_total_amount = $pi_grand_total_total_amount + $pr_grand_total_total_amount;
                        @endphp
                        <tr class="grand_total">
                            <td colspan="5" class="rep-font-bold">Grand Total:</td>
                            <td class="text-right rep-font-bold">{{number_format($grand_total_quantity)}}</td>
                            <td class="text-right rep-font-bold"></td>
                            <td class="text-right rep-font-bold"></td>
                            <td class="text-right rep-font-bold">{{number_format($grand_total_gross_amount,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($grand_total_disc_amount,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($grand_total_vat_amount,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($grand_total_spec_disc_amount,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($grand_total_fed_amount,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($grand_total_total_amount,3)}}</td>
                            <td class="text-right rep-font-bold"></td>
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



