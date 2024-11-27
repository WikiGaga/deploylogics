@extends('layouts.report')
@section('title', 'GRN List Report')

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
                <h1 class="kt-invoice__title">{{strtoupper($data['page_title'])}}</h1>
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
            </div>
            @include('reports.template.branding')
        </div>
        <div class="kt-portlet__body">
            <div class="row row-block">
                <div class="col-lg-12">
                    <table width="100%" id="rep_sale_invoice_datatable" class="table bt-datatable table-bordered">
                        <tr class="sticky-header">
                            <th class="text-center">Sales Type</th>
                            <th class="text-left">Product Name</th>
                            <th class="text-center">UOM</th>
                            <th class="text-center">Packing</th>
                            <th class="text-center">Quantity</th>
                            <th class="text-center">Rate</th>
                            <th class="text-center">Amount</th>
                            <th class="text-center">Disc Amount</th>
                            <th class="text-center">Vat Amount</th>
                            <th class="text-center">Net Amount</th>
                        </tr>
                        @php
                            $grand_total_quantity = 0;
                            $grand_total_amount = 0;
                            $grand_total_disc_amount = 0;
                            $grand_total_vat_amount = 0;
                            $grand_total_total_amount = 0;

                            $where = "";
                            if(count($data['supplier_ids']) != 0){
                              $where .= " and supplier_id in (".implode(",",$data['supplier_ids']).")";
                            }
                            if(count($data['product_ids']) != 0){
                                $where .= " and product_name in (".implode(",",$data['product_ids']).") ";
                            }
                            $qq = "select  grn_date,grn_code, supplier_name, product_name, uom_name, tbl_purc_grn_dtl_packing,
                                    case   when GRN_TYPE ='PR' THEN tbl_purc_grn_dtl_quantity * -1 ELSE tbl_purc_grn_dtl_quantity END  tbl_purc_grn_dtl_quantity ,
                                    tbl_purc_grn_dtl_rate,
                                    case   when GRN_TYPE ='PR' THEN tbl_purc_grn_dtl_amount * -1 ELSE tbl_purc_grn_dtl_amount END  tbl_purc_grn_dtl_amount ,
                                    case   when GRN_TYPE ='PR' THEN tbl_purc_grn_dtl_disc_amount * -1 ELSE tbl_purc_grn_dtl_disc_amount END  tbl_purc_grn_dtl_disc_amount ,
                                    case   when GRN_TYPE ='PR' THEN tbl_purc_grn_dtl_vat_amount * -1 ELSE tbl_purc_grn_dtl_vat_amount END  tbl_purc_grn_dtl_vat_amount ,
                                    case   when GRN_TYPE ='PR' THEN tbl_purc_grn_dtl_total_amount * -1 ELSE tbl_purc_grn_dtl_total_amount END  tbl_purc_grn_dtl_total_amount
                                    from vw_purc_grn where branch_id in (".implode(",",$data['branch_ids']).") and (grn_date between to_date ('".$data['from_date']."', 'yyyy/mm/dd') and to_date ('".$data['to_date']."', 'yyyy/mm/dd') )
                                    $where ORDER BY grn_date, grn_code";
                            
                            $getdata = \Illuminate\Support\Facades\DB::select($qq);
                            $list = [];
                            foreach ($getdata as $row)
                            {
                                $today = date('Y-m-d', strtotime($row->grn_date));
                                $list[$today][$row->grn_code][] = $row;
                            }
                        @endphp
                        @foreach($list as $key=>$list)
                            @php
                                $sub_total_quantity = 0;
                                $sub_total_amount = 0;
                                $sub_total_disc_amount = 0;
                                $sub_total_vat_amount = 0;
                                $sub_total_total_amount = 0;
                            @endphp
                            <tr>
                                <td colspan="10"><b>{{date('d-m-Y', strtotime($key))}}</b></td>
                            </tr>
                            @foreach($list as $k=>$invoice)
                                @php
                                    $total_quantity = 0;
                                    $total_amount = 0;
                                    $total_disc_amount = 0;
                                    $total_vat_amount = 0;
                                    $total_total_amount = 0;
                                @endphp
                                <tr>
                                    <td colspan="10">{{$k}} {{isset($invoice[0]->supplier_name)?$invoice[0]->supplier_name:''}}</td>
                                </tr>
                                @foreach($invoice as $product)
                                    <tr>
                                        <td></td>
                                        <td>{{$product->product_name}}</td>
                                        <td class="text-center">{{$product->uom_name}}</td>
                                        <td class="text-center">{{$product->tbl_purc_grn_dtl_packing}}</td>
                                        <td class="text-right">{{number_format($product->tbl_purc_grn_dtl_quantity)}}</td>
                                        <td class="text-right">{{number_format($product->tbl_purc_grn_dtl_rate,3)}}</td>
                                        <td class="text-right">{{number_format($product->tbl_purc_grn_dtl_amount,3)}}</td>
                                        <td class="text-right">{{number_format($product->tbl_purc_grn_dtl_disc_amount,3)}}</td>
                                        <td class="text-right">{{number_format($product->tbl_purc_grn_dtl_vat_amount,3)}}</td>
                                        <td class="text-right">{{number_format($product->tbl_purc_grn_dtl_total_amount,3)}}</td>
                                    </tr>
                                    @php
                                        $total_quantity += $product->tbl_purc_grn_dtl_quantity;
                                        $total_amount += $product->tbl_purc_grn_dtl_amount;
                                        $total_disc_amount += $product->tbl_purc_grn_dtl_disc_amount;
                                        $total_vat_amount += $product->tbl_purc_grn_dtl_vat_amount;
                                        $total_total_amount += $product->tbl_purc_grn_dtl_total_amount;
                                    @endphp
                                @endforeach
                                <tr>
                                    <td colspan="4" class="rep-font-bold">Total:</td>
                                    <td class="text-right rep-font-bold">{{number_format($total_quantity)}}</td>
                                    <td class="text-right rep-font-bold"></td>
                                    <td class="text-right rep-font-bold">{{number_format($total_amount,3)}}</td>
                                    <td class="text-right rep-font-bold">{{number_format($total_disc_amount,3)}}</td>
                                    <td class="text-right rep-font-bold">{{number_format($total_vat_amount,3)}}</td>
                                    <td class="text-right rep-font-bold">{{number_format($total_total_amount,3)}}</td>
                                </tr>
                                @php
                                    $sub_total_quantity += $total_quantity;
                                    $sub_total_amount += $total_amount;
                                    $sub_total_disc_amount += $total_disc_amount;
                                    $sub_total_vat_amount += $total_vat_amount;
                                    $sub_total_total_amount += $total_total_amount;
                                @endphp
                            @endforeach
                            <tr class="sub_total">
                                <td colspan="4" class="rep-font-bold">( {{date('d-m-Y', strtotime($key))}} ) Sub Total:</td>
                                <td class="text-right rep-font-bold">{{number_format($sub_total_quantity)}}</td>
                                <td class="text-right rep-font-bold"></td>
                                <td class="text-right rep-font-bold">{{number_format($sub_total_amount,3)}}</td>
                                <td class="text-right rep-font-bold">{{number_format($sub_total_disc_amount,3)}}</td>
                                <td class="text-right rep-font-bold">{{number_format($sub_total_vat_amount,3)}}</td>
                                <td class="text-right rep-font-bold">{{number_format($sub_total_total_amount,3)}}</td>
                            </tr>
                            @php
                                $grand_total_quantity += $sub_total_quantity;
                                $grand_total_amount += $sub_total_amount;
                                $grand_total_disc_amount += $sub_total_disc_amount;
                                $grand_total_vat_amount += $sub_total_vat_amount;
                                $grand_total_total_amount += $sub_total_total_amount;
                            @endphp
                        @endforeach
                        <tr class="grand_total">
                            <td colspan="4" class="rep-font-bold">Grand Total:</td>
                            <td class="text-right rep-font-bold">{{number_format($grand_total_quantity)}}</td>
                            <td class="text-right rep-font-bold"></td>
                            <td class="text-right rep-font-bold">{{number_format($grand_total_amount,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($grand_total_disc_amount,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($grand_total_vat_amount,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($grand_total_total_amount,3)}}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="kt-portlet__foot sale_invoice_footer" style="background: #f7f8fa">
            <div class="row">
                <div class="col-lg-12 kt-align-right">
                    <div class="date"><span>Date: </span>{{ date('d-m-Y') }} - <span>User: </span>{{auth()->user()->name}}</div>
                </div>
            </div>
        </div>
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



