@extends('layouts.report')
@section('title', 'Reporting')

@section('pageCSS')
    <style>
        .tdBorder{
            border-top: 2px solid #777777 !important;
            cursor: pointer;
        }
        .totFont{
            font-weight: 500 !important;
        }
        .table tr>th:first-child,
        .table tr>td:first-child {
            border-left: 0 !important;
        }
        .table tr>th:last-child,
        .table tr>td:last-child {
            border-right: 0 !important;
        }
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
        <div class="kt-portlet__head">
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
                    @php $product_lists = \Illuminate\Support\Facades\DB::table('tbl_purc_product')->whereIn('product_id',$data['product_ids'])->get('product_name'); @endphp
                    <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;">Product Name:</span>
                        @foreach($product_lists as $product_list)
                            <span style="color: #5578eb;">{{$product_list->product_name}}</span><span style="color: #fd397a;">, </span>
                        @endforeach
                    </h6>
                @endif
                @if(count($data['store']) != 0 && $data['store'] != "" && $data['store'] != null)
                    @php $stores = \Illuminate\Support\Facades\DB::table('tbl_defi_store')->whereIn('store_id',$data['store'])->get('store_name'); @endphp
                    <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;">Stores:</span>
                        @foreach($stores as $store)
                            <span style="color: #5578eb;">{{$store->store_name}}</span><span style="color: #fd397a;">, </span>
                        @endforeach
                    </h6>
                @endif
            </div>
            @include('reports.template.branding')
        </div>
        <div class="kt-portlet__body">
            <div class="row row-block">
                <div class="col-lg-12">
                    <table width="100%" id="rep_sale_invoice_datatable" class="static_report_table table bt-datatable table-bordered">
                        @php
                            $store_ids = [];
                        @endphp
                        <tr class="sticky-header">
                            <th width="5%" class="text-center">Sr No.</th>
                            <th width="15%" class="text-center">Barcode</th>
                            <th width="15%" class="text-center">Product Name</th>
                            @if(count($data['store']) != 0)
                                @php
                                    $store_ids = $data['store'];
                                    $s = \Illuminate\Support\Facades\DB::table('tbl_defi_store')->whereIn('store_id',$data['store'])->get('store_name');
                                @endphp
                                @foreach($s as $pl)
                                    <th width="10%" class="text-center">{{ucwords(strtolower(strtoupper($pl->store_name)))}}</th>
                                @endforeach
                            @else
                                @php
                                    $qry = "SELECT distinct SALES_STORE_ID FROM VW_PURC_STOCK_DTL where SALES_STORE_ID IS not NULL and
                                        business_id = 1 AND company_id = 1 AND branch_id in (".implode(",",$data['branch_ids']).") and
                                        DOCUMENT_DATE between to_date('".$data['from_date']."','yyyy/mm/dd') and to_date('".$data['to_date']."','yyyy/mm/dd')";
                                    $pivot_lists = \Illuminate\Support\Facades\DB::select($qry);
                                @endphp
                                @if(count($pivot_lists) != 0)
                                    @foreach($pivot_lists as $pl)
                                        @php
                                            array_push($store_ids,$pl->sales_store_id);
                                            $s = \App\Models\TblDefiStore::where('store_id',$pl->sales_store_id)->first();
                                        @endphp
                                        <th width="10%" class="text-center">{{ucwords(strtolower(strtoupper($s->store_name)))}}</th>
                                    @endforeach
                                @endif
                            @endif
                        </tr>
                        @if(count($store_ids) != 0)
                            @php
                                if(count($data['product_ids']) != 0){
                                    $product = "PROD.PRODUCT_ID IN (".implode(",",$data['product_ids']).")";
                                }else{
                                    $product = "PROD.PRODUCT_ID not IN (-1)";
                                }
                                if(count($data['product_group']) != 0){
                                    $product_group =  "PROD.GROUP_ITEM_ID IN (".implode(",",$data['product_group']).")";
                                }else{
                                    $product_group =  "PROD.GROUP_ITEM_ID not IN (-1)";
                                }

                                $SALES_STORE_ID = "SALES_STORE_ID IN (".implode(",",$store_ids).")";

                                $row_qry = "SELECT * FROM (
                                        select  PROD.PRODUCT_BARCODE_BARCODE,product_name,PRODUCT_QTY,SALES_STORE_ID from (
                                            SELECT  PRODUCT_ID ,  BUSINESS_ID, COMPANY_ID ,   BRANCH_ID , SALES_STORE_ID, SUM( QTY_BASE_UNIT_VALUE) PRODUCT_QTY
                                                FROM VW_PURC_STOCK_DTL where  business_id = 1 AND company_id = 1 AND branch_id in (".implode(",",$data['branch_ids']).")
                                                and VW_PURC_STOCK_DTL.DOCUMENT_DATE BETWEEN to_date('".$data['from_date']."','yyyy/mm/dd') AND to_date('".$data['to_date']."','yyyy/mm/dd')
                                                GROUP BY PRODUCT_ID , BUSINESS_ID, COMPANY_ID ,  BRANCH_ID , SALES_STORE_ID
                                                HAVING SUM(QTY_BASE_UNIT_VALUE) <> 0
                                        ) STOCK, VW_PURC_PRODUCT_BARCODE_FIRST PROD
                                        where STOCK.PRODUCT_ID = PROD.PRODUCT_ID   AND ".$product." AND ".$product_group."
                                    ) PIVOT (
                                        SUM(PRODUCT_QTY) FOR ".$SALES_STORE_ID."
                                    )";
                                $rows = \Illuminate\Support\Facades\DB::select($row_qry);
                            @endphp
                            @foreach($rows as $row)
                                <tr>
                                    <td>{{$loop->iteration}}</td>
                                    <td>{{$row->product_barcode_barcode}}</td>
                                    <td>{{$row->product_name}}</td>
                                    @foreach($store_ids as $all_st)
                                        <td class="text-right">{{number_format($row->$all_st,3)}}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        @else
                           <tr>
                               <td colspan="3"> No Data Found.. </td>
                           </tr>
                        @endif
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



