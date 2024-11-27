@extends('layouts.report')
@section('title', 'Stock Valuation Report')

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
                <h3 class="kt-invoice__title">{{strtoupper($data['page_title'])}}</h3>
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
                            <th class="text-center">Sr No.</th>
                            <th class="text-center">First Level Category</th>
                            <th class="text-center">Last Level Category</th>
                            <th class="text-center">Product Name</th>
                            <th class="text-center">Product Qty</th>
                            <th class="text-center">Cost Rate</th>
                            <th class="text-center">Stock Cost Value</th>
                            <th class="text-center">Sale Rate</th>
                            <th class="text-center">Stock Sale Rate</th>
                            <th class="text-center">Avg. Rate</th>
                            <th class="text-center">Stock Avg. Rate</th>
                        </tr>
                        @php
                            if(count($data['product_ids']) != 0){
                                $product = "PROD.PRODUCT_NAME IN ('".implode("','",$data['product_ids'])."') ";
                            }else{
                                $product = "PROD.PRODUCT_ID not IN (-1)";
                            }
                            $and_product_group = "";
                            if(count($data['product_group']) != 0){
                                $inner_where = "";
                                foreach($data['product_group'] as $product_group){
                                    $group_item_item = \App\Models\TblPurcGroupItem::where('group_item_id',$product_group)->first();
                                  //  dd($group_item_item->toArray());
                                    if($group_item_item->group_item_level == 1){

                                    }
                                    if($group_item_item->group_item_level == 2){
                                        $group_items = \App\Models\TblPurcGroupItem::where('parent_group_id',$product_group)->pluck('group_item_id')->toArray();

                                        $inner_where .= " PROD.group_item_id in (".implode(",",$group_items).") OR";
                                    }
                                    if($group_item_item->group_item_level == 3){
                                        $inner_where .= " PROD.group_item_id = $product_group OR ";
                                    }
                                }
                                if(!empty($inner_where)){
                                    $inner_where = rtrim($inner_where, " OR ");
                                    $and_product_group .= "and ( ".$inner_where." ) ";
                                }
                            }else{
                                $and_product_group =  "and PROD.GROUP_ITEM_ID not IN (-1)";
                            }
                           // dd($and_product_group);
                            $qry = "select  GROUP_ITEM_ID,GROUP_ITEM_NAME ,GROUP_ITEM_PARENT_ID , GROUP_ITEM_PARENT_NAME ,product_name,STOCK.BRANCH_ID,PRODUCT_QTY,
                                PROD.PRODUCT_BARCODE_COST_RATE ,
                                NVL(PRODUCT_QTY,0) * NVL(PROD.PRODUCT_BARCODE_COST_RATE,0) STOCK_COST_VALUE  ,
                                PROD.SALE_RATE ,
                                NVL(PRODUCT_QTY,0) * NVL(PROD.SALE_RATE,0) STOCK_SALE_VALUE  ,
                                ROUND((NVL(PRODUCT_VAL,0)/NVL(PRODUCT_QTY,0)),3) AVG_RATE ,
                                PRODUCT_VAL STOCK_AVG_VALUE
                                 from (
                                        SELECT  PRODUCT_ID ,  BUSINESS_ID, COMPANY_ID ,   BRANCH_ID , SUM( QTY_BASE_UNIT_VALUE) PRODUCT_QTY  ,   SUM( QTY_BASE_UNIT_VALUE * DOCUMENT_ACT_RATE )   PRODUCT_VAL
                                            FROM VW_PURC_STOCK_DTL where  business_id = ".auth()->user()->business_id." AND company_id = ".auth()->user()->company_id." AND branch_id in (".implode(",",$data['branch_ids']).")
                                            and VW_PURC_STOCK_DTL.DOCUMENT_DATE BETWEEN to_date('2000-01-01','yyyy/mm/dd') AND to_date('".$data['date']."','yyyy/mm/dd')
                                            GROUP BY PRODUCT_ID , BUSINESS_ID, COMPANY_ID ,  BRANCH_ID
                                            HAVING SUM(QTY_BASE_UNIT_VALUE) <> 0
                                    ) STOCK, VW_PURC_PRODUCT_BARCODE_RATE PROD
                                    where STOCK.PRODUCT_ID = PROD.PRODUCT_ID(+)
                                        AND STOCK.COMPANY_ID = PROD.COMPANY_ID(+)
                                        AND STOCK.BUSINESS_ID = PROD.BUSINESS_ID(+)
                                        AND STOCK.BRANCH_ID = PROD.BRANCH_ID(+)
                                        AND $product $and_product_group";
                            $rows = \Illuminate\Support\Facades\DB::select($qry);
                         //   dd($rows);
                        @endphp
                        @foreach($rows as $row)
                            <tr>
                                <td>{{$loop->iteration}}</td>
                                <td class="text-left">{{$row->group_item_parent_name}}</td>
                                <td class="text-left">{{$row->group_item_name}}</td>
                                <td class="text-left">{{$row->product_name}}</td>
                                <td class="text-right">{{number_format($row->product_qty)}}</td>
                                <td class="text-right">{{number_format($row->product_barcode_cost_rate,3)}}</td>
                                <td class="text-right">{{number_format($row->stock_cost_value,3)}}</td>
                                <td class="text-right">{{number_format($row->sale_rate,3)}}</td>
                                <td class="text-right">{{number_format($row->stock_sale_value,3)}}</td>
                                <td class="text-right">{{number_format($row->avg_rate,3)}}</td>
                                <td class="text-right">{{number_format($row->stock_avg_value,3)}}</td>
                           </tr>
                        @endforeach
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



