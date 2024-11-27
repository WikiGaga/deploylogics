@extends('layouts.report')
@section('title', 'Purchase Register')

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

                        $qq = "select product_barcode_barcode,grn_date,grn_code,grn_type,branch_short_name, supplier_name, product_name, uom_name,tbl_purc_grn_dtl_tax_on,tbl_purc_grn_dtl_disc_on,grn_bill_no,
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
//dd($qq);
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
                            $list_pi[$row->grn_code][] = $row;
                        }
                        $list_pr = [];
                        foreach ($pr as $row)
                        {
                            $list_pr[$row->grn_code][] = $row;
                        }
                    @endphp
                    @php
                        $pi_grand_total_quantity = 0;
                        $pi_grand_total_gross_amount = 0;
                        $pi_grand_total_disc_amount = 0;
                        $pi_grand_total_vat_amount = 0;
                        $pi_grand_total_fed_amount = 0;
                        $pi_grand_total_spec_disc_amount = 0;
                        $pi_grand_total_inve_disc_amount = 0;
                        $pi_grand_total_total_amount = 0;
                    @endphp
                    @php
                        $pr_grand_total_quantity = 0;
                        $pr_grand_total_gross_amount = 0;
                        $pr_grand_total_disc_amount = 0;
                        $pr_grand_total_vat_amount = 0;
                        $pr_grand_total_fed_amount = 0;
                        $pr_grand_total_spec_disc_amount = 0;
                        $pr_grand_total_inve_disc_amount = 0;
                        $pr_grand_total_total_amount = 0;
                    @endphp
                    <table width="100%" id="rep_sale_invoice_datatable" class="static_report_table table bt-datatable table-bordered">
                        <tr class="sticky-header">
                            <th class="text-center">S.#</th>
                            <th class="text-center">Barcode</th>
                            <th class="text-center">Product Name</th>
                            <th class="text-center">Quantity</th>
                            <th class="text-center">Unit</th>
                            <th class="text-center">Rate</th>
                            <th class="text-center">Gross Amount</th>
                            <th class="text-center">Disc Amount</th>
                            <th class="text-center">Per Item Disc</th>
                            <th class="text-center">GST Criteria</th>
                            <th class="text-center">GST Amount</th>
                            <th class="text-center">FED</th>
                            <th class="text-center">Sp Disc Criteria</th>
                            <th class="text-center">Spec.Disc</th>
                            <th class="text-center">Inv.Disc</th>
                            <th class="text-center">Net Amount</th>
                        </tr>
                        @if(isset($data['specific_purchase_type']) && in_array($data['specific_purchase_type'],['grn','all']) )
                        <tr><td colspan="16"><h6>Purchase Invoice</h6></td></tr>
                        @foreach($list_pi as $pi_key=>$pi_row)
                            @php
                                $sub_total_quantity = 0;
                                $sub_gross_amount = 0;
                                $sub_total_disc_amount = 0;
                                $sub_total_vat_amount = 0;
                                $sub_total_fed_amount = 0;
                                $sub_total_spec_disc_amount = 0;
                                $sub_total_inve_disc_amount = 0;
                                $sub_total_total_amount = 0;
                                $ki = 1;
                                $date = isset($pi_row[0]->grn_date)?date('d-m-Y', strtotime(trim(str_replace('/','-',$pi_row[0]->grn_date)))):"";
                                $supplier_name = isset($pi_row[0]->supplier_name)?$pi_row[0]->supplier_name:"";
                                $grn_bill_no = isset($pi_row[0]->grn_bill_no)?$pi_row[0]->grn_bill_no:"";
                            @endphp
                            <tr>
                                <td colspan="3"><b>{{$pi_key}} - {{$date}}</b></td>
                                <td colspan="7"><b>Vendor: {{$supplier_name}}</b></td>
                                <td colspan="6"><b>Vendor Inv. No: {{$grn_bill_no}}</b></td>
                            </tr>
                            @foreach($pi_row as $pi_k=>$pi_product)
                                <tr>
                                    <td>{{$ki}}</td>
                                    <td>{{$pi_product->product_barcode_barcode}}</td>
                                    <td>{{$pi_product->product_name}}</td>
                                    <td class="text-right">{{number_format($pi_product->tbl_purc_grn_dtl_quantity)}}</td>
                                    <td class="text-center">{{$pi_product->uom_name}}</td>
                                    <td class="text-right">{{number_format($pi_product->tbl_purc_grn_dtl_rate,3)}}</td>
                                    <td class="text-right">{{number_format($pi_product->tbl_purc_grn_dtl_amount,3)}}</td>
                                    <td class="text-right">{{number_format($pi_product->tbl_purc_grn_dtl_disc_amount,3)}}</td>
                                    <td class="text-right">-</td>
                                    <td>{{strtoupper($pi_product->tbl_purc_grn_dtl_tax_on)}}</td>
                                    <td class="text-right">{{number_format($pi_product->tbl_purc_grn_dtl_vat_amount,3)}}</td>
                                    <td class="text-right">{{number_format($pi_product->tbl_purc_grn_dtl_fed_amount,3)}}</td>
                                    <td>{{strtoupper($pi_product->tbl_purc_grn_dtl_disc_on)}}</td>
                                    <td class="text-right">{{number_format($pi_product->tbl_purc_grn_dtl_spec_disc_amount,3)}}</td>
                                    <td class="text-right">-</td>
                                    <td class="text-right">{{number_format($pi_product->tbl_purc_grn_dtl_total_amount,3)}}</td>
                                </tr>
                                @php
                                    $ki += 1;
                                    $sub_total_quantity += $pi_product->tbl_purc_grn_dtl_quantity;
                                    $sub_gross_amount += $pi_product->tbl_purc_grn_dtl_amount;
                                    $sub_total_disc_amount += $pi_product->tbl_purc_grn_dtl_disc_amount;
                                    $sub_total_vat_amount += $pi_product->tbl_purc_grn_dtl_vat_amount;
                                    $sub_total_fed_amount += $pi_product->tbl_purc_grn_dtl_fed_amount;
                                    $sub_total_spec_disc_amount += $pi_product->tbl_purc_grn_dtl_spec_disc_amount;
                                    $sub_total_inve_disc_amount += 0;
                                    $sub_total_total_amount += $pi_product->tbl_purc_grn_dtl_total_amount;
                                @endphp
                            @endforeach
                            <tr class="sub_total">
                                <td colspan="3" class="rep-font-bold">( {{$pi_key}} ) Sub Total:</td>
                                <td class="text-right rep-font-bold">{{number_format($sub_total_quantity)}}</td>
                                <td class="text-right rep-font-bold"></td>
                                <td class="text-right rep-font-bold"></td>
                                <td class="text-right rep-font-bold">{{number_format($sub_gross_amount,3)}}</td>
                                <td class="text-right rep-font-bold">{{number_format($sub_total_disc_amount,3)}}</td>
                                <td class="text-right rep-font-bold"></td>
                                <td class="text-right rep-font-bold"></td>
                                <td class="text-right rep-font-bold">{{number_format($sub_total_vat_amount,3)}}</td>
                                <td class="text-right rep-font-bold">{{number_format($sub_total_fed_amount,3)}}</td>
                                <td class="text-right rep-font-bold"></td>
                                <td class="text-right rep-font-bold">{{number_format($sub_total_spec_disc_amount,3)}}</td>
                                <td class="text-right rep-font-bold">{{number_format($sub_total_inve_disc_amount,3)}}</td>
                                <td class="text-right rep-font-bold">{{number_format($sub_total_total_amount,3)}}</td>
                            </tr>
                            @php
                                $pi_grand_total_quantity += $sub_total_quantity;
                                $pi_grand_total_gross_amount += $sub_gross_amount;
                                $pi_grand_total_disc_amount += $sub_total_disc_amount;
                                $pi_grand_total_vat_amount += $sub_total_vat_amount;
                                $pi_grand_total_fed_amount += $sub_total_fed_amount;
                                $pi_grand_total_spec_disc_amount += $sub_total_spec_disc_amount;
                                $pi_grand_total_inve_disc_amount += $sub_total_inve_disc_amount;
                                $pi_grand_total_total_amount += $sub_total_total_amount;
                            @endphp
                        @endforeach
                        <tr class="grand_total">
                            <td colspan="3" class="rep-font-bold">Purchase Invoice Total:</td>
                            <td class="text-right rep-font-bold">{{number_format($pi_grand_total_quantity)}}</td>
                            <td class="text-right rep-font-bold"></td>
                            <td class="text-right rep-font-bold"></td>
                            <td class="text-right rep-font-bold">{{number_format($pi_grand_total_gross_amount,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($pi_grand_total_disc_amount,3)}}</td>
                            <td class="text-right rep-font-bold"></td>
                            <td class="text-right rep-font-bold"></td>
                            <td class="text-right rep-font-bold">{{number_format($pi_grand_total_vat_amount,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($pi_grand_total_fed_amount,3)}}</td>
                            <td class="text-right rep-font-bold"></td>
                            <td class="text-right rep-font-bold">{{number_format($pi_grand_total_spec_disc_amount,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($pi_grand_total_inve_disc_amount,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($pi_grand_total_total_amount,3)}}</td>
                        </tr>
                        @endif
                        @if(isset($data['specific_purchase_type']) && in_array($data['specific_purchase_type'],['pr','all']) )
                            <tr><td colspan="16"><h6>Purchase Return</h6></td></tr>
                            @foreach($list_pr as $pr_key=>$pr_row)
                                @php
                                    $sub_total_quantity = 0;
                                    $sub_gross_amount = 0;
                                    $sub_total_disc_amount = 0;
                                    $sub_total_vat_amount = 0;
                                    $sub_total_fed_amount = 0;
                                    $sub_total_spec_disc_amount = 0;
                                    $sub_total_inve_disc_amount = 0;
                                    $sub_total_total_amount = 0;
                                    $kr = 1;
                                    $date = isset($pr_row[0]->grn_date)?date('d-m-Y', strtotime(trim(str_replace('/','-',$pr_row[0]->grn_date)))):"";
                                    $supplier_name = isset($pr_row[0]->supplier_name)?$pr_row[0]->supplier_name:"";
                                    $grn_bill_no = isset($pr_row[0]->grn_bill_no)?$pr_row[0]->grn_bill_no:"";
                                @endphp
                                <tr>
                                    <td colspan="3"><b>{{$pi_key}} - {{$date}}</b></td>
                                    <td colspan="7"><b>Vendor: {{$supplier_name}}</b></td>
                                    <td colspan="6"><b>Vendor Inv. No: {{$grn_bill_no}}</b></td>
                                </tr>
                                @foreach($pr_row as $pr_k=>$pr_product)
                                    <tr>
                                        <td>{{$kr}}</td>
                                        <td>{{$pr_product->product_barcode_barcode}}</td>
                                        <td>{{$pr_product->product_name}}</td>
                                        <td class="text-right">{{number_format($pr_product->tbl_purc_grn_dtl_quantity)}}</td>
                                        <td class="text-center">{{$pr_product->uom_name}}</td>
                                        <td class="text-right">{{number_format($pr_product->tbl_purc_grn_dtl_rate,3)}}</td>
                                        <td class="text-right">{{number_format($pr_product->tbl_purc_grn_dtl_amount,3)}}</td>
                                        <td class="text-right">{{number_format($pr_product->tbl_purc_grn_dtl_disc_amount,3)}}</td>
                                        <td class="text-right">-</td>
                                        <td>{{strtoupper($pr_product->tbl_purc_grn_dtl_tax_on)}}</td>
                                        <td class="text-right">{{number_format($pr_product->tbl_purc_grn_dtl_vat_amount,3)}}</td>
                                        <td class="text-right">{{number_format($pr_product->tbl_purc_grn_dtl_fed_amount,3)}}</td>
                                        <td>{{strtoupper($pr_product->tbl_purc_grn_dtl_disc_on)}}</td>
                                        <td class="text-right">{{number_format($pr_product->tbl_purc_grn_dtl_spec_disc_amount,3)}}</td>
                                        <td class="text-right">-</td>
                                        <td class="text-right">{{number_format($pr_product->tbl_purc_grn_dtl_total_amount,3)}}</td>
                                    </tr>
                                    @php
                                        $ki += 1;
                                        $sub_total_quantity += $pr_product->tbl_purc_grn_dtl_quantity;
                                        $sub_gross_amount += $pr_product->tbl_purc_grn_dtl_amount;
                                        $sub_total_disc_amount += $pr_product->tbl_purc_grn_dtl_disc_amount;
                                        $sub_total_vat_amount += $pr_product->tbl_purc_grn_dtl_vat_amount;
                                        $sub_total_fed_amount += $pr_product->tbl_purc_grn_dtl_fed_amount;
                                        $sub_total_spec_disc_amount += $pr_product->tbl_purc_grn_dtl_spec_disc_amount;
                                        $sub_total_inve_disc_amount += 0;
                                        $sub_total_total_amount += $pr_product->tbl_purc_grn_dtl_total_amount;
                                    @endphp
                                @endforeach
                                <tr class="sub_total">
                                    <td colspan="3" class="rep-font-bold">( {{$pr_key}} ) Sub Total:</td>
                                    <td class="text-right rep-font-bold">{{number_format($sub_total_quantity)}}</td>
                                    <td class="text-right rep-font-bold"></td>
                                    <td class="text-right rep-font-bold"></td>
                                    <td class="text-right rep-font-bold">{{number_format($sub_gross_amount,3)}}</td>
                                    <td class="text-right rep-font-bold">{{number_format($sub_total_disc_amount,3)}}</td>
                                    <td class="text-right rep-font-bold"></td>
                                    <td class="text-right rep-font-bold"></td>
                                    <td class="text-right rep-font-bold">{{number_format($sub_total_vat_amount,3)}}</td>
                                    <td class="text-right rep-font-bold">{{number_format($sub_total_fed_amount,3)}}</td>
                                    <td class="text-right rep-font-bold"></td>
                                    <td class="text-right rep-font-bold">{{number_format($sub_total_spec_disc_amount,3)}}</td>
                                    <td class="text-right rep-font-bold">{{number_format($sub_total_inve_disc_amount,3)}}</td>
                                    <td class="text-right rep-font-bold">{{number_format($sub_total_total_amount,3)}}</td>
                                </tr>
                                @php
                                    $pr_grand_total_quantity += $sub_total_quantity;
                                    $pr_grand_total_gross_amount += $sub_gross_amount;
                                    $pr_grand_total_disc_amount += $sub_total_disc_amount;
                                    $pr_grand_total_vat_amount += $sub_total_vat_amount;
                                    $pr_grand_total_fed_amount += $sub_total_fed_amount;
                                    $pr_grand_total_spec_disc_amount += $sub_total_spec_disc_amount;
                                    $pr_grand_total_inve_disc_amount += $sub_total_inve_disc_amount;
                                    $pr_grand_total_total_amount += $sub_total_total_amount;
                                @endphp
                            @endforeach
                            <tr class="grand_total">
                                <td colspan="3" class="rep-font-bold">Purchase Return Total:</td>
                                <td class="text-right rep-font-bold">{{number_format($pr_grand_total_quantity)}}</td>
                                <td class="text-right rep-font-bold"></td>
                                <td class="text-right rep-font-bold"></td>
                                <td class="text-right rep-font-bold">{{number_format($pr_grand_total_gross_amount,3)}}</td>
                                <td class="text-right rep-font-bold">{{number_format($pr_grand_total_disc_amount,3)}}</td>
                                <td class="text-right rep-font-bold"></td>
                                <td class="text-right rep-font-bold"></td>
                                <td class="text-right rep-font-bold">{{number_format($pr_grand_total_vat_amount,3)}}</td>
                                <td class="text-right rep-font-bold">{{number_format($pr_grand_total_fed_amount,3)}}</td>
                                <td class="text-right rep-font-bold"></td>
                                <td class="text-right rep-font-bold">{{number_format($pr_grand_total_spec_disc_amount,3)}}</td>
                                <td class="text-right rep-font-bold">{{number_format($pr_grand_total_inve_disc_amount,3)}}</td>
                                <td class="text-right rep-font-bold">{{number_format($pr_grand_total_total_amount,3)}}</td>
                            </tr>
                        @endif
                        @if(isset($data['specific_purchase_type']) && in_array($data['specific_purchase_type'],['all']) )
                        @php
                            $grand_total_quantity = $pi_grand_total_quantity + $pr_grand_total_quantity;
                            $grand_total_gross_amount = $pi_grand_total_gross_amount + $pr_grand_total_gross_amount;
                            $grand_total_disc_amount = $pi_grand_total_disc_amount + $pr_grand_total_disc_amount;
                            $grand_total_vat_amount = $pi_grand_total_vat_amount + $pr_grand_total_vat_amount;
                            $grand_total_fed_amount = $pi_grand_total_fed_amount + $pr_grand_total_fed_amount;
                            $grand_total_spec_disc_amount = $pi_grand_total_spec_disc_amount + $pr_grand_total_spec_disc_amount;
                            $grand_total_inve_disc_amount = $pi_grand_total_inve_disc_amount + $pr_grand_total_inve_disc_amount;
                            $grand_total_total_amount = $pi_grand_total_total_amount + $pr_grand_total_total_amount;
                        @endphp
                        <tr class="grand_total">
                            <td colspan="3" class="rep-font-bold">Grand Total:</td>
                            <td class="text-right rep-font-bold">{{number_format($grand_total_quantity)}}</td>
                            <td class="text-right rep-font-bold"></td>
                            <td class="text-right rep-font-bold"></td>
                            <td class="text-right rep-font-bold">{{number_format($grand_total_gross_amount,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($grand_total_disc_amount,3)}}</td>
                            <td class="text-right rep-font-bold"></td>
                            <td class="text-right rep-font-bold"></td>
                            <td class="text-right rep-font-bold">{{number_format($grand_total_vat_amount,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($grand_total_fed_amount,3)}}</td>
                            <td class="text-right rep-font-bold"></td>
                            <td class="text-right rep-font-bold">{{number_format($grand_total_spec_disc_amount,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($grand_total_inve_disc_amount,3)}}</td>
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



