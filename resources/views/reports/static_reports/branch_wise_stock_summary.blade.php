@extends('layouts.report')
@section('title', 'Branch Wise Stock Summary')

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
        $supplierDtl = \Illuminate\Support\Facades\DB::table('tbl_purc_supplier')->where('supplier_id',$data['supplier_ids'])->first();
    @endphp
    <div class="kt-portlet" id="kt_portlet_table">
        <div class="kt-portlet__head">
            <div class="kt-invoice__brand">
                <h3 class="kt-invoice__title">{{strtoupper($data['page_title'])}}</h3>
                <h6 class="kt-invoice__criteria">
                    <span style="color: #e27d00;">Date:</span>
                    @if($data['date_time_wise'] == 1)
                        <span style="color: #5578eb;">{{" ".date('d-m-Y', strtotime($data['time_from']))." "}}</span>
                    @else
                        <span style="color: #5578eb;">{{" ".date('d-m-Y', strtotime($data['date']))." "}}</span>
                    @endif
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
                @if(isset($data['supplier_ids']) && !empty($data['supplier_ids']))
                    <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;">Supplier:</span>
                        <span style="color: #5578eb;">{{" ".$supplierDtl->supplier_name." "}}</span>
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
            </div>
            @include('reports.template.branding')
        </div>
        <div class="kt-portlet__body">
            <div class="row row-block">
                <div class="col-lg-12">
                    @php
                        $where = "";
                        $where_bottom = "";
                        if(isset($data['supplier_ids']) && !empty($data['supplier_ids']) != 0){
                            $where .= " and SUP_PROD.supplier_id = ".$supplierDtl->supplier_id."";
                        }
                        
                        if(count($data['product_group']) != 0){
                            $inner_where = "";
                            foreach($data['product_group'] as $product_group){
                                $group_item_item = \App\Models\TblPurcGroupItem::where('group_item_id',$product_group)->first();
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
                                $where_bottom .= "and ( ".$inner_where." ) ";
                            }
                        }
                        if($data['date_time_wise'] == 1)
                        {
                            $date_time_from = $data['time_from'];
                            $date_field = " AND VW_PURC_STOCK_DTL.created_at <= to_date ('".$date_time_from."', 'yyyy/mm/dd HH24:MI')";
                        }else{
                            $date_field = "AND (VW_PURC_STOCK_DTL.DOCUMENT_DATE <= to_date('".$data['date']."','yyyy/mm/dd'))";
                        }
                        
                        $vendorfrom = "";
                        $vendorjoin = "";
                        // Vendor Wise whereclause
                        if(isset($data['supplier_ids']) && !empty($data['supplier_ids']) != 0){
                            $vendorfrom = " ,VW_PURC_PRODUCT_FOC_PROD_WISE SUP_PROD";
                            $vendorjoin = " AND STOCK.PRODUCT_ID = SUP_PROD.PRODUCT_ID";
                        }


                        $qry = " select 
                            STOCK.BRANCH_ID, 
                            BB.BRANCH_NAME, 
                            SUM(PRODUCT_QTY)  PRODUCT_QTY , 
                            SUM(NVL(STOCK_AMOUNT,0))  STOCK_AMOUNT  ,  
                            SUM(NVL(SALE_STOCK_AMOUNT,0))  SALE_STOCK_AMOUNT    
                        from (
                            SELECT 
                                VW_PURC_STOCK_DTL.BRANCH_ID, 
                                '' BRANCH_NAME, 
                                VW_PURC_STOCK_DTL.PRODUCT_ID,  
                                SUM(QTY_BASE_UNIT_VALUE)  PRODUCT_QTY ,  
                                SUM(QTY_BASE_UNIT_VALUE) * MAX(NET_TP) STOCK_AMOUNT  ,  
                                SUM(QTY_BASE_UNIT_VALUE) * MAX(SALE_RATE)  SALE_STOCK_AMOUNT 
                            FROM 
                                VW_PURC_STOCK_DTL 
                                LEFT OUTER JOIN
                                (SELECT DISTINCT 
                                    BRANCH_ID, 
                                    '' BRANCH_NAME, 
                                    PROD_RATE.PRODUCT_ID , 
                                    MAX(NET_TP) NET_TP , 
                                    MAX(SALE_RATE) SALE_RATE
                                from 
                                    TBL_PURC_PRODUCT_BARCODE_PURCH_RATE  PROD_RATE 
                                GROUP BY  PROD_RATE.PRODUCT_ID ,BRANCH_ID  
                                ) PROD_RATE ON VW_PURC_STOCK_DTL.PRODUCT_ID = PROD_RATE.PRODUCT_ID 
                                AND  VW_PURC_STOCK_DTL.BRANCH_ID = PROD_RATE.BRANCH_ID 
                            where VW_PURC_STOCK_DTL.BRANCH_ID in (".implode(",",$data['branch_ids']).")
                                $date_field
                            GROUP BY VW_PURC_STOCK_DTL.BRANCH_ID, VW_PURC_STOCK_DTL.PRODUCT_ID 
                            ) STOCK, VW_PURC_PRODUCT  PROD , 
                                tbl_soft_branch BB
                                $vendorfrom
                        where STOCK.PRODUCT_ID = PROD.PRODUCT_ID 
                            and STOCK.BRANCH_ID = BB.BRANCH_ID 
                            $vendorjoin
                            $where_bottom
                            $where
                        GROUP BY STOCK.BRANCH_ID , BB.BRANCH_NAME";
//dd($qry);
                        $getdata = \Illuminate\Support\Facades\DB::select($qry);
                        //dd($getdata);
                        $list = [];
                        foreach ($getdata as $row)
                        {
                            $list[$row->branch_name][] = $row;
                        }
                        $i = 1;
                    @endphp
                    <table width="100%" id="rep_branch_wise_stock_summary_datatable" class="static_report_table table bt-datatable table-bordered">
                        <tr class="sticky-header">
                            <th class="text-center">Sr. No.</th>
                            <th class="text-center">Branch</th>
                            <th class="text-center">Stock Qty</th>
                            <th class="text-center">TP Stock Amount</th>
                            <th class="text-center">Sale Stock Amount</th>
                            <th class="text-center">Gross Profit Amount</th>
                            <th class="text-center">Gross Profit Percentage</th>
                        </tr>
                        @foreach($list as $name=>$items)
                            <tr>
                                <td colspan="7">{{$name}}</td>
                            </tr>
                            @foreach ($items as $key=>$list_row)
                                @php
                                    $gross_profit_amount = $list_row->sale_stock_amount - $list_row->stock_amount;
                                
                                    $gross_profit_perc = 0;
                                    if($gross_profit_amount != 0){
                                        $gross_profit_perc = @(($gross_profit_amount / $list_row->stock_amount) * 100) ;
                                    }
                                @endphp
                                <tr>
                                    <td class="text-center">{{$i}}</td>
                                    <td class="text-left">{{$list_row->branch_name}}</td>
                                    <td class="text-center">{{$list_row->product_qty}}</td>
                                    <td class="text-center">{{number_format($list_row->stock_amount,3)}}</td>
                                    <td class="text-center">{{number_format($list_row->sale_stock_amount,3)}}</td>
                                    <td class="text-center">{{number_format($gross_profit_amount,3)}}</td>
                                    <td class="text-center">{{number_format($gross_profit_perc,3)}}</td>
                                </tr>
                                @php
                                    $i += 1;
                                @endphp
                            @endforeach
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
                $("#rep_branch_wise_stock_summary_datatable").table2excel({
                    // exclude: ".noExport",
                    filename: "report.xls",
                });
            });
        </script>
    @endif
@endsection



