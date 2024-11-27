@extends('layouts.report')
@section('title', 'Product Wise Profit & Loss')

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
                    <span style="color: #5578eb;">{{" ".date('d-m-Y', strtotime($data['date_time_from']))." to ". date('d-m-Y', strtotime($data['date_time_to']))." "}}</span>
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

                        /*if(count($data['supplier_ids']) != 0){
                            $where .= " and supplier_id in (".implode(",",$data['supplier_ids']).")";
                        }*/

                        if(count($data['product_ids']) != 0){
                            $where .= " and product_name in ('".implode("','",$data['product_ids'])."') ";
                        }
                        if(count($data['product_group']) != 0){
                            $inner_where = "";
                            foreach($data['product_group'] as $product_group){
                                $group_item_item = \App\Models\TblPurcGroupItem::where('group_item_id',$product_group)->first();
                                if($group_item_item->group_item_level == 1){

                                }
                                if($group_item_item->group_item_level == 2){
                                    $group_items = \App\Models\TblPurcGroupItem::where('parent_group_id',$product_group)->pluck('group_item_id')->toArray();

                                    $inner_where .= " group_item_id in (".implode(",",$group_items).") OR";
                                }
                                if($group_item_item->group_item_level == 3){
                                    $inner_where .= " group_item_id = $product_group OR ";
                                }
                            }
                            if(!empty($inner_where)){
                                $inner_where = rtrim($inner_where, " OR ");
                                $where .= "and ( ".$inner_where." ) ";
                            }
                        }

                        $qry = "SELECT 
                            GROUP_ITEM_PARENT_NAME ,
                            GROUP_ITEM_NAME,
                            PRODUCT_ID ,
                            PRODUCT_NAME, 
                            sum(SALES_DTL_QUANTITY) QTY , 
                            CASE WHEN  sum(SALES_DTL_QUANTITY) > 0 THEN  ROUND( sum(COST_AMOUNT) /  sum(SALES_DTL_QUANTITY),2) ELSE 0  END AS PER_PIECE_COST, 
                            sum(COST_AMOUNT) COST_AMOUNT  ,
                            sum(SALES_DTL_AMOUNT)  SALE_AMOUNT , 
                            CASE  WHEN  sum(SALES_DTL_QUANTITY) > 0 THEN  ROUND( sum(SALES_DTL_AMOUNT) /  sum(SALES_DTL_QUANTITY),2) ELSE 0  END AS PER_PIECE_SALE, 
                            sum(SALES_DTL_AMOUNT)  -  sum(COST_AMOUNT)   PROFIT_AMOUNT  , 
                            CASE WHEN  sum(SALES_DTL_QUANTITY) > 0   THEN   ROUND(( sum(SALES_DTL_AMOUNT)  -  sum(COST_AMOUNT) )   /  sum(SALES_DTL_QUANTITY) ,2) ELSE 0  END AS PROFIT_AMOUNT_PER_PIECE  
                        FROM 
                            VW_SALE_SALES_INVOICE
                        where branch_id in (".implode(",",$data['branch_ids']).")  
                            AND (created_at between to_date ('".$data['date_time_from']."', 'yyyy/mm/dd HH24:MI') and to_date ('".$data['date_time_to']."', 'yyyy/mm/dd HH24:MI') )
                            $where
                        GROUP BY 
                            GROUP_ITEM_PARENT_NAME ,
                            GROUP_ITEM_NAME,
                            PRODUCT_ID ,
                            PRODUCT_NAME";

                        $getdata = \Illuminate\Support\Facades\DB::select($qry);


                    @endphp
                    <table width="100%" id="rep_sale_invoice_datatable" class="static_report_table table bt-datatable table-bordered">
                        <tr class="sticky-header">
                            <th class="text-center">S.#</th>
                            <th class="text-center">Product Name</th>
                            <th class="text-center">Group Item</th>
                            <th class="text-center">Parent Group Item</th>
                            <th class="text-center">Quanity</th>
                            <th class="text-center">Per Piece Cost</th>
                            <th class="text-center">Cost Amount</th>
                            <th class="text-center">Sale Amount</th>
                            <th class="text-center">Per Piece Sale</th>
                            <th class="text-center">Profit Amount</th>
                            <th class="text-center">Profit Amount Per Piece</th>
                            <th class="text-center">GP(%)</th>
                        </tr>
                        @php 
                        $tot_cost_amount = 0;
                        $tot_sale_amount = 0;
                        $tot_profit_amount = 0;
                                
                        $ki = 1;
                        @endphp
                        @foreach($getdata as $i_key=>$item)
                            @php 
                                $tot_cost_amount = $tot_cost_amount + $item->cost_amount;
                                $tot_sale_amount = $tot_sale_amount + $item->sale_amount;
                                $tot_profit_amount = $tot_profit_amount + $item->profit_amount;
                                
                                
                                $gp_perc = @round(($item->profit_amount_per_piece / $item->per_piece_sale) * 100,2)
                            @endphp
                            <tr>
                                <td>{{$ki}}</td>
                                <td>{{$item->product_name}}</td>
                                <td>{{$item->group_item_parent_name}}</td>
                                <td>{{$item->group_item_name}}</td>
                                <td class="text-right">{{number_format($item->qty,3)}}</td>
                                <td class="text-right">{{number_format($item->per_piece_cost,3)}}</td>
                                <td class="text-right">{{number_format($item->cost_amount,3)}}</td>
                                <td class="text-right">{{number_format($item->sale_amount,3)}}</td>
                                <td class="text-right">{{number_format($item->per_piece_sale,3)}}</td>
                                <td class="text-right">{{number_format($item->profit_amount,3)}}</td>
                                <td class="text-right">{{number_format($item->profit_amount_per_piece,3)}}</td>
                                <td class="text-center">{{ $gp_perc }}</td>
                            </tr>
                            @php $ki += 1; @endphp
                        @endforeach
                        <tr>
                            <td class="text-right" colspan="6"><strong>TOTAL:</strong></td>
                            <td class="text-right"><strong>{{number_format($tot_cost_amount,0)}}</strong></td>
                            <td class="text-right"><strong>{{number_format($tot_sale_amount,0)}}</strong></td>
                            <td class="text-right">&nbsp;</td>
                            <td class="text-right"><strong>{{number_format($tot_profit_amount,0)}}</strong></td>
                            <td class="text-right">&nbsp;</td>
                            <td class="text-right">&nbsp;</td>
                        </tr>
                        
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



