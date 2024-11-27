@extends('layouts.report')
@section('title', 'Consumer List Report')

@section('pageCSS')
    <style>
        /* Styles go here */
        @media print {
            thead {display: table-header-group;}
            tfoot {display: table-footer-group;}
            tfoot>tr>td {padding:0 !important;}
            body {margin: 0;}
        }
        .link{
            text-decoration: underline;
            color: #fd397a;
        }
    </style>
@endsection
@section('content')
    @php
        $data = Session::get('data');
    @endphp
    <div class="kt-portlet" id="kt_portlet_table">
        <div class="kt-portlet__head">
            <div class="kt-invoice__brand">
                <h1 class="kt-invoice__title">{{strtoupper($data['page_title'])}}</h1>
                @if(count($data['branch_ids']) != 0)
                    @php $branch_lists = \Illuminate\Support\Facades\DB::table('tbl_soft_branch')->whereIn('branch_id',$data['branch_ids'])->where(\App\Library\Utilities::currentBC())->where('branch_active_status',1)->get(['branch_name','branch_short_name']); @endphp
                    <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;">Branch:</span>
                        @foreach($branch_lists as $branch_list)
                            <span style="color: #5578eb;">{{$branch_list->branch_name}}</span><span style="color: #fd397a;">, </span>
                        @endforeach
                    </h6>
                @endif
                @if(count($data['product_group']) != 0 && $data['product_group'] != "" && $data['product_group'] != null)
                    @php $product_groups = \Illuminate\Support\Facades\DB::table('vw_purc_group_item')->whereIn('group_item_id',$data['product_group'])->select('group_item_name_string','group_item_id','group_item_name_code_string')->get(); @endphp
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
                    {{-- Get Data From Query --}}
                    @php
                        if(count($data['product_group']) != 0 && $data['product_group'] != "" && $data['product_group'] != null){
                            $filter_product_group = "AND GI.GROUP_ITEM_ID IN (".implode(",",$data['product_group']).")";
                        }else{
                            $filter_product_group = "AND GI.GROUP_ITEM_ID NOT IN (-1)";
                        }

                        $query = "SELECT 
                            P.product_id, 
                            GI.GROUP_ITEM_NAME, 
                            GI.GROUP_ITEM_ID,
                            P.product_name, 
                            P.product_arabic_name, 
                            P.SUPPLIER_ID,
                            S.SUPPLIER_NAME,
                            U.uom_name,
                            P.product_code, 
                            B.business_id, 
                            B.company_id, 
                            B.BRANCH_ID, 
                            PB.PRODUCT_BARCODE_BARCODE, 
                            PB.PRODUCT_BARCODE_PACKING, 
                            PB.PRODUCT_BARCODE_ID, 
                            PR.PRODUCT_BARCODE_COST_RATE, 
                            PS.PRODUCT_BARCODE_SALE_RATE_RATE,
                            BR.BRAND_NAME
                            FROM 
                            tbl_purc_product P 
                            JOIN tbl_purc_product_barcode PB ON PB.PRODUCT_ID = P.PRODUCT_ID and PB.product_barcode_packing = 1 
                            JOIN TBL_PURC_PRODUCT_BARCODE_PURCH_RATE PR ON PR.PRODUCT_BARCODE_ID = PB.PRODUCT_BARCODE_ID 
                            JOIN TBL_DEFI_UOM U ON U.UOM_ID = PB.UOM_ID 
                            LEFT JOIN TBL_PURC_BRAND BR ON BR.BRAND_ID = P.PRODUCT_BRAND_ID
                            LEFT JOIN tbl_purc_supplier S on S.supplier_id = P.supplier_id 
                            JOIN TBL_PURC_PRODUCT_BARCODE_SALE_RATE PS ON PS.PRODUCT_BARCODE_ID = PB.PRODUCT_BARCODE_ID 
                            AND PS.PRODUCT_CATEGORY_ID = 2 
                            JOIN TBL_PURC_GROUP_ITEM GI ON GI.GROUP_ITEM_ID = P.GROUP_ITEM_ID 
                            JOIN TBL_SOFT_BRANCH B ON B.BRANCH_ID = PR.BRANCH_ID AND B.BRANCH_ID = PS.BRANCH_ID 
                            AND B.BRANCH_ID = PS.BRANCH_ID AND B.BRANCH_ID IN (".implode(",",$data['branch_ids']).")
                            $filter_product_group
                            where 
                            P.PRODUCT_ID in (
                                SELECT 
                                product_id 
                                from 
                                tbl_purc_grn_dtl
                            ) 
                            OR P.PRODUCT_ID in (
                                SELECT 
                                product_id 
                                from 
                                tbl_sale_sales_dtl
                            ) 
                        order by  P.product_name";
                        
                        $get_data = DB::select($query);
                        $sr = 1;
                    @endphp
                    <table width="100%" id="rep_sale_invoice_datatable" class="table bt-datatable table-bordered">
                        <tr class="sticky-header">
                            <th class="text-left">Sr.</th>
                            <th class="text-left">Product Code</th>
                            <th class="text-left">Product Name</th>
                            <th class="text-left">Product Name Arabic</th>
                            <th class="text-left">Barcode</th>
                            <th class="text-left">Group</th>
                            <th class="text-left">Supplier</th>
                            <th class="text-left">Unit</th>
                            <th class="text-left">Brand</th>
                            <th class="text-left">Cost Rate</th>
                            <th class="text-left">Sale Rate</th>
                        </tr>
                        @php $totalCostRate = 0; $totalSaleRate = 0;  @endphp
                        @foreach($get_data as $product)
                            @php
                                $totalCostRate = $totalCostRate + $product->product_barcode_cost_rate;
                                $totalSaleRate = $totalSaleRate + $product->product_barcode_sale_rate_rate;
                            @endphp
                            <tr>
                                <td>{{ $sr++ }}</td>
                                <td class="text-center link"><a class="report_link" href="{{ url('product/view' , $product->product_id) }}" target="_blank">{{ $product->product_code }}</a></td>
                                <td class="text-center">{{ $product->product_name }}</td>
                                <td class="text-center">{{ $product->product_arabic_name }}</td>
                                <td class="text-center">{{ $product->product_barcode_barcode }}</td>
                                <td class="text-center">{{ $product->group_item_name }}</td>
                                <td class="text-center">{{ $product->supplier_name }}</td>
                                <td class="text-center">{{ $product->uom_name }}</td>
                                <td class="text-center">{{ $product->brand_name }}</td>
                                <td class="text-right">{{ number_format($product->product_barcode_cost_rate,3) }}</td>
                                <td class="text-right">{{ number_format($product->product_barcode_sale_rate_rate,3) }}</td>
                           </tr>
                        @endforeach
                        <tr class="grand_total">
                            <td class="rep-font-bold" colspan="2">Grand Total:</td>
                            <td class="text-right rep-font-bold"></td>
                            <td class="text-right rep-font-bold"></td>
                            <td class="text-right rep-font-bold"></td>
                            <td class="text-right rep-font-bold"></td>
                            <td class="text-right rep-font-bold"></td>
                            <td class="text-right rep-font-bold"></td>
                            <td class="text-right rep-font-bold"></td>
                            <td class="text-right rep-font-bold">{{ number_format($totalCostRate , 3) }}</td>
                            <td class="text-right rep-font-bold">{{ number_format($totalSaleRate , 3) }}</td>
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



