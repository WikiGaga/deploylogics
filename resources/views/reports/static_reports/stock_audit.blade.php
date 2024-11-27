@extends('layouts.report')
@section('title', 'Audit Stock Report')

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
                            BRANCH_ID,
                            BRANCH_NAME, 
                            PRODUCT_ID, 
                            PRODUCT_NAME, 
                            PRODUCT_BARCODE_BARCODE, 
                            PRODUCT_BARCODE_ID, 
                            GROUP_ITEM_NAME, 
                            GROUP_ITEM_PARENT_NAME, 
                            UOM_NAME, 
                            PRODUCT_BARCODE_PACKING, 
                            SUPPLIER_NAME, 
                            COST_RATE, 
                            SUM(STOCK_DTL_STOCK_QUANTITY) AS QTY, 
                            SUM(STOCK_DTL_PHYSICAL_QUANTITY) AS PHY_QTY, 
                            SUM(STOCK_DTL_QUANTITY) as DIFF,
                            SUM(STOCK_DTL_STOCK_QUANTITY) * COST_RATE as wrt_stock_cost_rate,
                            SUM(STOCK_DTL_PHYSICAL_QUANTITY) * COST_RATE as wrt_phy_cost_rate,
                            SUM(STOCK_DTL_QUANTITY) * COST_RATE as wrt_diff_cost_rate
                        FROM 
                            VW_INVE_STOCK
                        WHERE BRANCH_ID IN (".implode(",",$data['branch_ids']).") 
                            AND (STOCK_DATE between to_date ('".$data['from_date']."', 'yyyy/mm/dd') and to_date ('".$data['to_date']."', 'yyyy/mm/dd'))
                            $where
                        GROUP BY BRANCH_ID,
                            BRANCH_NAME,
                            PRODUCT_ID,
                            PRODUCT_NAME,
                            PRODUCT_BARCODE_BARCODE,
                            PRODUCT_BARCODE_ID,
                            GROUP_ITEM_NAME,
                            GROUP_ITEM_PARENT_NAME,
                            UOM_NAME,
                            PRODUCT_BARCODE_PACKING,
                            SUPPLIER_NAME,
                            COST_RATE
                        HAVING SUM(STOCK_DTL_QUANTITY) <> 0
                        ORDER BY PRODUCT_NAME";
                                
           //dd($qry);    
                        
                        $getdata = \Illuminate\Support\Facades\DB::select($qry);
                        $list = [];
                        foreach ($getdata as $row){
                            $list[$row->group_item_parent_name][$row->group_item_name][] = $row;
                        }
                        @endphp
                        <table width="100%" id="rep_stock_audit_data_datatable" class="static_report_table table bt-datatable table-bordered">
                            <tr class="sticky-header">
                                <th class="text-center">S.#</th>
                                <th class="text-left">Product Name</th>
                                <th class="text-center">Cost Rate</th>
                                <th class="text-center">Sys. Stock</th>
                                <th class="text-center">Sys. St. Value W.r.t Cost Rate</th>
                                <th class="text-center">Ph. Stock</th>
                                <th class="text-center">Ph. St. Value W.r.t Cost Rate</th>
                                <th class="text-center">Stock Diff.</th>
                                <th class="text-center">Diff. Value W.r.t Cost Rate</th>
                            </tr>
                            @php
                                $gtotamount = 0;
                                $gtotphyamount = 0;
                                $gtotdiffamount= 0;
                            @endphp
                            @foreach($list as $group_keys=>$group_row)
                                <tr>
                                    <td colspan="9"><b style="color:brown">{{ucwords(strtoupper($group_keys))}}</b></td>
                                </tr>
                                @php
                                    $stotamount = 0;
                                    $stotphyamount = 0;
                                    $stotdiffamount = 0;
                                @endphp
                                @foreach($group_row as $sub_group_keys=>$sub_group_row)
                                    <tr>
                                        <td colspan="9"><b style="color:#5578eb">{{ucwords(strtoupper($sub_group_keys))}}</b></td>
                                    </tr>
                                    @php
                                        $ki=1;
                                        $totamount = 0;
                                        $totphyamount = 0;
                                        $totdiffamount = 0;
                                    @endphp
                                    @foreach($sub_group_row as $inv_k=>$si_detail)
                                        @php
                                            $totamount = $totamount + $si_detail->wrt_stock_cost_rate;
                                            $totphyamount = $totphyamount + $si_detail->wrt_phy_cost_rate;
                                            $totdiffamount = $totdiffamount + $si_detail->wrt_diff_cost_rate;

                                            $stotamount = $stotamount + $si_detail->wrt_stock_cost_rate;
                                            $stotphyamount = $stotphyamount + $si_detail->wrt_phy_cost_rate;
                                            $stotdiffamount = $stotdiffamount + $si_detail->wrt_diff_cost_rate;

                                            $gtotamount = $gtotamount + $si_detail->wrt_stock_cost_rate;
                                            $gtotphyamount = $gtotphyamount + $si_detail->wrt_phy_cost_rate;
                                            $gtotdiffamount = $gtotdiffamount + $si_detail->wrt_diff_cost_rate;
                                        @endphp
                                            <tr>
                                                <td class="text-center">{{$ki}}</td>
                                                <td class="text-left">{{$si_detail->product_name}}</td>
                                                <td class="text-right">{{number_format($si_detail->cost_rate,2)}}</td>
                                                <td class="text-right">{{number_format($si_detail->qty,2)}}</td>
                                                <td class="text-right">{{number_format($si_detail->wrt_stock_cost_rate,2)}}</td>
                                                <td class="text-right">{{number_format($si_detail->phy_qty,2)}}</td>
                                                <td class="text-right">{{number_format($si_detail->wrt_phy_cost_rate,2)}}</td>
                                                <td class="text-right">{{number_format($si_detail->diff,2)}}</td>
                                                <td class="text-right">{{number_format($si_detail->wrt_diff_cost_rate,2)}}</td>
                                            </tr>
                                        @php
                                            $ki += 1;
                                        @endphp
                                    @endforeach
                                    <tr>
                                        <td colspan="4" class="text-right"><strong>Total: </strong></td>
                                        <td class="text-right">
                                            <strong>
                                                {{number_format($totamount,2)}}
                                            </strong>
                                        </td>
                                        <td class="text-right"></td>
                                        <td class="text-right">
                                            <strong>
                                                {{number_format($totphyamount,2)}}
                                            </strong>
                                        </td>
                                        <td class="text-right"></td>
                                        <td class="text-right">
                                            <strong>
                                                {{number_format($totdiffamount,2)}}
                                            </strong>
                                        </td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="4" class="text-right"><strong style="color:#5578eb">Sub Total: </strong></td>
                                    <td class="text-right">
                                        <strong>
                                            {{number_format($stotamount,2)}}
                                        </strong>
                                    </td>
                                    <td class="text-right"></td>
                                    <td class="text-right">
                                        <strong>
                                            {{number_format($stotphyamount,2)}}
                                        </strong>
                                    </td>
                                    <td class="text-right"></td>
                                    <td class="text-right">
                                        <strong>
                                            {{number_format($stotdiffamount,2)}}
                                        </strong>
                                    </td>
                                </tr>
                            @endforeach
                            <tr>
                                <td colspan="4" class="text-right"><strong>Grand Total: </strong></td>
                                <td class="text-right">
                                    <strong>
                                        {{number_format($gtotamount,2)}}
                                    </strong>
                                </td>
                                <td class="text-right"></td>
                                <td class="text-right">
                                    <strong>
                                        {{number_format($gtotphyamount,2)}}
                                    </strong>
                                </td>
                                <td class="text-right"></td>
                                <td class="text-right">
                                    <strong>
                                        {{number_format($gtotdiffamount,2)}}
                                    </strong>
                                </td>
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
{{-- @section('exportXls')
    @if($data['form_file_type'] == 'xls')
        <script>
            $(document).ready(function() {
                $("#rep_stock_audit_data_datatable").table2excel({
                    // exclude: ".noExport",
                    filename: "report.xls",
                });
            });
        </script>
    @endif
@endsection --}}
