@extends('layouts.report')
@section('title', 'Stock Activity Summary')

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
        <div class="kt-portlet__head" >
            <div class="kt-invoice__brand">
                <h1 class="kt-invoice__title">{{strtoupper($data['page_title'])}}</h1>
                <h6 class="kt-invoice__criteria">
                    <span style="color: #e27d00;">Date:</span>
                    <span style="color: #5578eb;">{{" ".date('d-m-Y', strtotime($data['from_date']))." to ". date('d-m-Y', strtotime($data['to_date']))." "}}</span>
                </h6>
            </div>
            @include('reports.template.branding')
        </div>
        @php

            $query = "select distinct s.business_id, s.company_id, s.branch_id,br.branch_name, s.sales_store_id, st.store_name, s.product_id, vp.product_name, '' product_barcode_id,'' product_barcode_barcode,opening_stock,qty_in,qty_out,opening_stock + qty_in - qty_out balance
                    from (select distinct s.branch_id, s.sales_store_id, s.product_id, s.product_barcode_id, s.business_id, s.company_id, get_stock_current_qty_date ( s.product_id, s.product_barcode_id, s.business_id, s.company_id, s.branch_id, '', to_date('".$data['date_opening_bal']."', 'yyyy/mm/dd')) opening_stock,
                    sum (s.qty_in) over (partition by s.branch_id, s.sales_store_id, s.product_id) qty_in,
                    sum (s.qty_out) over (partition by s.branch_id, s.sales_store_id, s.product_id)  qty_out
                    from vw_purc_stock_dtl s where (document_date between to_date('".$data['from_date']."', 'yyyy/mm/dd') and to_date('".$data['to_date']."', 'yyyy/mm/dd')) and (".$data['clause_business_id'] . $data['clause_company_id'] . $data['clause_branch_id'].")) s,
                        tbl_soft_branch br,tbl_defi_store st,vw_purc_product vp
                        where s.branch_id = br.branch_id and s.sales_store_id = st.store_id(+) and s.product_id = vp.product_id order by vp.product_name";
//dd($query);
            $Result_List = DB::select($query);
        @endphp
        <div class="kt-portlet__body">
            <div class="row row-block">
                <div class="col-lg-12">
                    <table width="100%" id="rep_sale_invoice_datatable" class="table bt-datatable table-bordered">
                        <tr class="sticky-header">
                            <th width="5%" class="text-left">Sr. No</th>
                            <th width="35%" class="text-center">Product Name</th>
                            <th width="10%" class="text-center">Opening Stock</th>
                            <th width="10%" class="text-right">Qty IN</th>
                            <th width="10%" class="text-right">Qty OUT</th>
                            <th width="10%" class="text-right">Balance</th>
                        </tr>
                        @php
                            $remain_bal =  0;
                        @endphp
                        @foreach($Result_List as $list)
                            <tr>
                                <td>{{$loop->iteration}}</td>
                                <td>{{ $list->product_name }}</td>
                                <td class="text-right">{{ $list->opening_stock }}</td>
                                <td class="text-right">{{ $list->qty_in }}</td>
                                <td class="text-right">{{ $list->qty_out }}</td>
                                <td class="text-right">{{ $list->balance }}</td>
                            </tr>
                        @endforeach
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
