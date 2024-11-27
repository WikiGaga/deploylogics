@extends('layouts.report')
@section('title', 'Product Rate List')

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
                <h1 class="kt-invoice__title">{{strtoupper($data['page_title'])}}</h1>
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
                
                @if(count($data['product_ids']) != 0)
                    <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;">Product:</span>
                        @php $i = 0; @endphp
                        @foreach($data['product_ids'] as $product)
                            @php $i++; @endphp
                            @if($i <= 7) 
                                <span style="color: #5578eb;">{{$product}}</span><span style="color: #fd397a;">, </span>
                            @endif
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
                        $diff_perc = "";

                        if(!empty($data['diff_perc'])){
                            $diff_perc = "WHERE PERC <= ".$data['diff_perc']." ";
                        }

                        $product_status = "";

                        if($data['product_status'] == "active"){
                            $product_status = " and PRODUCT_ENTRY_STATUS = '1' ";
                        }
                        if($data['product_status'] == "inactive"){
                            $product_status = " and PRODUCT_ENTRY_STATUS = '0' ";
                        }

                        /*if(!empty($data['greater_than_net_tp'])){
                            $where .= " and sale_rate > net_tp ";
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

                        $qry = "SELECT * FROM (
                        SELECT 
                            PRODUCT_ID , 
                            PRODUCT_NAME,
                            GROUP_ITEM_PARENT_NAME,
                            GROUP_ITEM_NAME,
                            PRODUCT_BARCODE_BARCODE, 
                            UOM_NAME,
                            PRODUCT_BARCODE_PACKING,
                            SALE_RATE,
                            NET_TP,
                            HS_CODE,
                            SALE_TAX_RATE,
                            (SALE_RATE - NET_TP) AS DIFF,
                            (CASE
                                WHEN (SALE_RATE > 0 AND NET_TP > 0) THEN ((SALE_RATE - NET_TP) / SALE_RATE) * 100
                                ELSE 0
                            END) AS PERC
                        FROM 
                            VW_PURC_PRODUCT_BARCODE_RATE
                        WHERE branch_id in (".implode(",",$data['branch_ids']).")
                            AND created_at between to_date ('".$data['date_time_from']."', 'yyyy/mm/dd HH24:MI') and to_date ('".$data['date_time_to']."', 'yyyy/mm/dd HH24:MI')
                            $where
                            $product_status
                        GROUP BY PRODUCT_ID , 
                            PRODUCT_NAME,
                            GROUP_ITEM_PARENT_NAME,
                            GROUP_ITEM_NAME,
                            PRODUCT_BARCODE_BARCODE, 
                            UOM_NAME,
                            PRODUCT_BARCODE_PACKING,
                            SALE_RATE,
                            NET_TP,
                            HS_CODE,
                            SALE_TAX_RATE
                        )ABS
                        $diff_perc
                        ORDER BY PRODUCT_BARCODE_BARCODE";
                    
           //dd($qry);    
           //VW_PURC_STOCK_DTL
                        
                        $getdata = \Illuminate\Support\Facades\DB::select($qry);
                       //  dd($getdata);
                        $list = [];
                        foreach ($getdata as $row){
                            $list[$row->group_item_parent_name][$row->group_item_name][] = $row;
                        }
                       //dd($list);
                        @endphp
                        <table width="100%" id="rep_user_log_datatable" class="static_report_table table bt-datatable table-bordered">
                            <tr class="sticky-header">
                                <th class="text-center">S.#</th>
                                <th class="text-center">Bar Code</th>
                                <th class="text-left">Product</th>
                                <th class="text-center">UoM</th>
                                <th class="text-center">Pack</th>
                                <th class="text-center">HS Code</th>
                                <th class="text-center">Sale Tax Rate</th>
                                <th class="text-center">Sale Rate</th>
                                <th class="text-center">Net TP</th>
                                <th class="text-center">Diff</th>
                                <th class="text-center">Diff(%)</th>
                            </tr>
                            @foreach($list as $group_parent_key=>$group_parent_row)
                                @php
                                    $group_parent_name = ucwords(strtolower($group_parent_key));
                                @endphp
                                <tr class="outer_total">
                                    <td colspan="11">{{ucwords(strtolower($group_parent_key))}}</td>
                                </tr>
                                @foreach($group_parent_row as $group_key=>$group_row)
                                    @php
                                        $group_name = ucwords(strtolower($group_key));
                                    @endphp
                                    <tr class="inner_total" >
                                        <td colspan="11">&nbsp;&nbsp;{{ucwords(strtolower($group_key))}}</td>
                                    </tr>
                                    @php
                                        $ki = 1;
                                    @endphp
                                    @foreach($group_row as $i_key=>$si_detail)
                                            <tr>
                                                <td class="text-center">{{$ki}}</td>
                                                <td class="text-center">{{$si_detail->product_barcode_barcode}}</td>
                                                <td class="text-left">{{$si_detail->product_name}}</td>
                                                <td class="text-center">{{$si_detail->uom_name}}</td>
                                                <td class="text-center">{{$si_detail->product_barcode_packing}}</td>
                                                <td class="text-center">{{$si_detail->hs_code}}</td>
                                                <td class="text-center">{{$si_detail->sale_tax_rate}}</td>
                                                <td class="text-right">{{number_format($si_detail->sale_rate,0)}}</td>
                                                <td class="text-right">{{number_format($si_detail->net_tp,0)}}</td>
                                                <td class="text-right">{{number_format($si_detail->diff,0)}}</td>
                                                <td class="text-center">{{round($si_detail->perc,2)}}</td>
                                            </tr>
                                        @php
                                            $ki++;
                                        @endphp
                                    @endforeach
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
{{-- @section('exportXls')
    @if($data['form_file_type'] == 'xls')
        <script>
            $(document).ready(function() {
                $("#rep_user_log_datatable").table2excel({
                    // exclude: ".noExport",
                    filename: "report.xls",
                });
            });
        </script>
    @endif
@endsection --}}
