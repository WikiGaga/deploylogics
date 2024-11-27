@extends('layouts.report')
@section('title', 'Product Change Rate')

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
                        if(count($data['product_ids']) != 0){
                            $where .= " and product_name in ('".implode("','",$data['product_ids'])."') ";
                        }
                        $qry = "SELECT  * FROM vw_purc_product_change_rate where
                        (CHANGE_RATE_DATE between to_date ('".$data['from_date']."', 'yyyy/mm/dd') and to_date ('".$data['to_date']."', 'yyyy/mm/dd'))
                        and change_rate_branch_id in (".implode(",",$data['branch_ids']).")
                                        $where order by created_at";

                        $getdata = \Illuminate\Support\Facades\DB::select($qry);


                    @endphp
                    <table width="100%" id="rep_sale_invoice_datatable" class="static_report_table table bt-datatable table-bordered">
                        <tr class="sticky-header">
                            <th class="text-center">S.#</th>
                            <th class="text-center">Date</th>
                            <th class="text-center">Group Item</th>
                            <th class="text-center">Supplier</th>
                            <th class="text-center">Barcode</th>
                            <th class="text-center">Product Name</th>
                            <th class="text-center">Current TP</th>
                            <th class="text-center">Sale Rate</th>
                        </tr>
                        @php $ki = 1; @endphp
                        @foreach($getdata as $i_key=>$item)
                            <tr>
                                <td>{{$ki}}</td>
                                <td>{{date('d-m-Y',strtotime($item->change_rate_date))}}</td>
                                <td>{{$item->group_item_name}}</td>
                                <td>{{$item->supplier_name}}</td>
                                <td>{{$item->product_barcode_barcode}}</td>
                                <td>{{$item->product_name}}</td>
                                <td class="text-right">{{number_format($item->current_tp,3)}}</td>
                                <td class="text-right">{{number_format($item->sale_rate,3)}}</td>
                            </tr>
                            @php $ki += 1; @endphp
                        @endforeach
                        @if(count($getdata) == 0)
                            <tr><td colspan="8">No Data Found</td></tr>
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



