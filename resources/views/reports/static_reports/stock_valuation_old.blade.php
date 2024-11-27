@extends('layouts.report')
@section('title', 'Customer List Report')

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
        <div class="kt-portlet__head">
            <div class="kt-invoice__brand">
                <h1 class="kt-invoice__title">{{strtoupper($data['page_title'])}}</h1>
                <h6 class="kt-invoice__title">
                    <span style="color: #e27d00;">Date: </span>
                    <span style="color: #5578eb;">{{date('d-m-Y', strtotime($data['date']))}}</span>
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
            </div>
            @include('reports.template.branding')
        </div>
        <div class="kt-portlet__body">
            <div class="row row-block">
                <div class="col-lg-12">
                    <table width="100%" id="rep_sale_invoice_datatable" class="table bt-datatable table-bordered">
                        <tr class="sticky-header">
                            <th width="5%" class="text-center">Sr No.</th>
                            <th width="10%" class="text-center">Barcode</th>
                            <th width="15%" class="text-center">Product Name</th>
                            <th width="7%" class="text-center">UOM Name</th>
                            <th width="5%" class="text-center">Packing</th>
                            <th width="8%" class="text-center">Product Qty</th>
                            <th width="10%" class="text-center">Purchase Rate</th>
                            <th width="10%" class="text-center">Cost Value</th>
                            <th width="10%" class="text-center">Sale Rate</th>
                            <th width="10%" class="text-center">Retail Sale Rate</th>
                            <th width="10%" class="text-center">Whole Sale Rate</th>
                        </tr>
                        @php
                            if(count($data['product_ids']) != 0){
                                $product = "STOCK.PRODUCT_ID IN (".implode(",",$data['product_ids']).")";
                            }else{
                                $product = "STOCK.PRODUCT_ID not IN (-1)";
                            }
                            if(count($data['product_group']) != 0){
                                $product_group =  "PROD.GROUP_ITEM_ID IN (".implode(",",$data['product_group']).")";
                            }else{
                                $product_group =  "PROD.GROUP_ITEM_ID not IN (-1)";
                            }
                            $qry = "SELECT * FROM   (
                                    select DISTINCT  TBL_STOCK.PRODUCT_ID ,  TBL_STOCK.PRODUCT_BARCODE_ID ,   TBL_STOCK.PRODUCT_BARCODE_BARCODE ,  TBL_STOCK.product_name ,  TBL_STOCK.UOM_NAME ,
		                            TBL_STOCK.PRODUCT_BARCODE_PACKING,
		                            TBL_STOCK.BUSINESS_ID, TBL_STOCK.COMPANY_ID ,   TBL_STOCK.BRANCH_ID , TBL_STOCK.PRODUCT_QTY  , TBL_STOCK.PRODUCT_BARCODE_PURCHASE_RATE, CATEGORY_NAME  , TBL_SALE_RATE.PRODUCT_BARCODE_SALE_RATE_RATE
                                    from(
		                                select   TBL_STOCK.PRODUCT_ID ,  TBL_STOCK.PRODUCT_BARCODE_ID ,   TBL_STOCK.PRODUCT_BARCODE_BARCODE ,  TBL_STOCK.product_name ,  TBL_STOCK.UOM_NAME ,
		                                TBL_STOCK.PRODUCT_BARCODE_PACKING,
		                                TBL_STOCK.BUSINESS_ID, TBL_STOCK.COMPANY_ID ,   TBL_STOCK.BRANCH_ID , TBL_STOCK.PRODUCT_QTY  , TBL_PURCH_RATE.PRODUCT_BARCODE_PURCHASE_RATE
		                                from (
                                            SELECT  PROD.PRODUCT_ID ,  PROD.PRODUCT_BARCODE_ID ,  PROD.PRODUCT_BARCODE_BARCODE , PROD.product_name , PROD.UOM_NAME , PROD.PRODUCT_BARCODE_PACKING,   STOCK.BUSINESS_ID, STOCK.COMPANY_ID ,   STOCK.BRANCH_ID , SUM( STOCK.QTY_BASE_UNIT_VALUE) PRODUCT_QTY
                                            FROM VW_PURC_STOCK_DTL   STOCK , VW_PURC_PRODUCT_BARCODE_FIRST PROD
                                            where STOCK.PRODUCT_ID = PROD.PRODUCT_ID
                                            and STOCK.business_id = ".auth()->user()->business_id." AND STOCK.company_id = ".auth()->user()->company_id." AND STOCK.branch_id in (".implode(",",$data['branch_ids']).")
				                            and STOCK.DOCUMENT_DATE BETWEEN to_date('1970-01-01','yyyy/mm/dd') AND to_date('".$data['date']."','yyyy/mm/dd')
				                            AND ".$product." AND ".$product_group."
				                            GROUP BY  PROD.PRODUCT_ID ,  PROD.PRODUCT_BARCODE_ID ,
				                            PROD.PRODUCT_BARCODE_BARCODE , PROD.product_name , PROD.UOM_NAME , PROD.PRODUCT_BARCODE_PACKING,
				                            STOCK.PRODUCT_ID , STOCK.BUSINESS_ID, STOCK.COMPANY_ID ,  STOCK.BRANCH_ID
				                            HAVING SUM(STOCK.QTY_BASE_UNIT_VALUE) <> 0
                                        ) TBL_STOCK ,
                                        TBL_PURC_PRODUCT_BARCODE_PURCH_RATE TBL_PURCH_RATE
                                        WHERE  TBL_STOCK.PRODUCT_ID = TBL_PURCH_RATE.PRODUCT_ID (+) AND
                                        TBL_STOCK.PRODUCT_BARCODE_ID =  TBL_PURCH_RATE.PRODUCT_BARCODE_ID (+)
		                            ) TBL_STOCK ,
		                            VW_PURC_PRODUCT_RATE  TBL_SALE_RATE
		                            WHERE TBL_STOCK.PRODUCT_ID = TBL_SALE_RATE.PRODUCT_ID (+) AND
		                            TBL_STOCK.PRODUCT_BARCODE_ID =  TBL_SALE_RATE.PRODUCT_BARCODE_ID (+)
                            ) PIVOT (
                                    sum(PRODUCT_BARCODE_SALE_RATE_RATE) FOR CATEGORY_NAME IN (
                                    'Sale Rate' AS  Sale_Rate ,
                                    'Retail Sale Rate' AS Retail_Sale_Rate ,
                                    'Whole Sale Rate' AS  Whole_Sale_Rate ))";
                            $rows = \Illuminate\Support\Facades\DB::select($qry);
                         //   dd($rows);
                        @endphp
                        @foreach($rows as $row)
                            <tr>
                                <td>{{$loop->iteration}}</td>
                                <td class="text-left">{{$row->product_barcode_barcode}}</td>
                                <td class="text-left">{{$row->product_name}}</td>
                                <td class="text-center">{{$row->uom_name}}</td>
                                <td class="text-right">{{$row->product_barcode_packing}}</td>
                                <td class="text-right">{{number_format($row->product_qty,3)}}</td>
                                <td class="text-right">{{number_format($row->product_barcode_purchase_rate,3)}}</td>
                                @php
                                    $costvalue = (float)$row->product_qty * (float)$row->product_barcode_purchase_rate;
                                @endphp
                                <td class="text-right">{{number_format($costvalue,3)}}</td>
                                <td class="text-right">{{number_format($row->sale_rate,3)}}</td>
                                <td class="text-right">{{number_format($row->retail_sale_rate,3)}}</td>
                                <td class="text-right">{{number_format($row->whole_sale_rate,3)}}</td>
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



