@extends('layouts.report')
@section('title', 'Stock Detail Document Wise')

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
            @include('reports.template.criteria')
            @include('reports.template.branding')
        </div>
        @php
            $query = "select * from (select product_id,business_id,company_id,branch_id,document_type,
                    get_stock_current_qty_date (product_id,'',business_id,company_id,branch_id,'',to_date('".$data['date_opening_bal']."', 'yyyy/mm/dd')) opening_stock,qty_base_unit_value,
                    0 closing_bal
                    from vw_purc_stock_dtl s where document_date between to_date('".$data['from_date']."', 'yyyy/mm/dd') and to_date('".$data['to_date']."', 'yyyy/mm/dd'))
                    pivot (sum (qty_base_unit_value) for document_type in (".$data['types'].")) order by product_id";
           // dd($query);
            $list_data = DB::select($query);
            //dd($data);
        @endphp
        <div class="kt-portlet__body">
            <div class="row row-block">
                <div class="col-lg-12">
                    <table width="100%" id="rep_sale_invoice_datatable" class="static_report_table table bt-datatable table-bordered">
                        <tr class="sticky-header">
                            <th width="5%" class="text-left">Sr. No</th>
                            <th width="25%" class="text-center">Barcode</th>
                            <th width="25%" class="text-center">Product Name</th>
                            <th width="5%" class="text-center">UOM</th>
                            <th width="10%" class="text-center">Opening Balance</th>
                            @foreach($data['document_type'] as $type)
                                <th width="5%" class="text-center">{{$type->field_heading}}</th>
                            @endforeach
                            <th width="10%" class="text-right">Balance</th>
                        </tr>
                        @php
                            $TotalClosingBalc = 0;
                        @endphp
                        @foreach($list_data as $list)
                            <tr>
                                @php
                                    $closingbal = 0;
                                    $barcode = \App\Models\ViewPurcProductBarcodeHelp::where('product_id',$list->product_id)->first();
                                    //dd($barcode);
                                @endphp
                                <td>{{$loop->iteration}}</td>
                                <td>{{ $barcode->product_barcode_barcode }}</td>
                                <td>{{ $barcode->product_name }}</td>
                                <td>{{ $barcode->uom_name .' - '.$barcode->product_barcode_packing }} </td>
                                <td class="text-right">{{ $list->opening_stock }}</td>
                                @foreach($data['document_type'] as $type)
                                    @php
                                        $field_value =  strtolower($type->field_value);
                                        $closingbal += (int)$list->$field_value;
                                        $TotalClosingBalc += $closingbal;
                                    @endphp
                                    <td class="text-right">{{ ($list->$field_value != null) ? $list->$field_value : 0 }}</td>
                                @endforeach

                                <td class="text-right">{{ $closingbal }}</td>
                            </tr>
                        @endforeach
                        <tr class="grand_total">
                            <td class="rep-font-bold">Total:</td>
                            <td class="text-right rep-font-bold"></td>
                            <td class="text-right rep-font-bold"></td>
                            <td class="text-right rep-font-bold"></td>
                            <td class="text-right rep-font-bold"></td>
                            @foreach($data['document_type'] as $type)
                                <td class="text-right rep-font-bold"></td>
                            @endforeach
                            <td class="text-right rep-font-bold">{{$TotalClosingBalc}}</td>
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
