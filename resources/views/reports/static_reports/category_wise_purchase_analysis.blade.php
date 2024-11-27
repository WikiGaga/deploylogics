@extends('layouts.report')
@section('title', 'Category Wise Purchase Analysis')

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

                        if(count($data['supplier_ids']) != 0){
                            $where .= " and supplier_id in (".implode(",",$data['supplier_ids']).")";
                        }


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

                        $qry = "SELECT GROUP_ITEM_PARENT_NAME, GROUP_ITEM_NAME,
                                    SUM(TBL_PURC_GRN_DTL_AMOUNT) OVER (PARTITION BY GROUP_ITEM_PARENT_NAME ) AS PARENT_WISE_TOTAL_AMOUNT ,
                                    SUM(TBL_PURC_GRN_DTL_QUANTITY) OVER (PARTITION BY GROUP_ITEM_PARENT_NAME ) AS PARENT_WISE_TOTAL_QTY ,
                                    SUM(TBL_PURC_GRN_DTL_DISC_AMOUNT) OVER (PARTITION BY GROUP_ITEM_PARENT_NAME ) AS PARENT_WISE_TOTAL_DISCOUNT ,
                                    SUM(TBL_PURC_GRN_DTL_AMOUNT) OVER (PARTITION BY GROUP_ITEM_NAME ) AS GROUP_ITEM_WISE_TOTAL_AMOUNT ,
                                    SUM(TBL_PURC_GRN_DTL_QUANTITY) OVER (PARTITION BY GROUP_ITEM_NAME ) AS GROUP_ITEM_WISE_TOTAL_QTY ,
                                    SUM(TBL_PURC_GRN_DTL_DISC_AMOUNT) OVER (PARTITION BY GROUP_ITEM_NAME ) AS GROUP_ITEM_WISE_TOTAL_DISCOUNT ,
                                    PRODUCT_ID, PRODUCT_BARCODE_ID, PRODUCT_BARCODE_BARCODE, PRODUCT_NAME,
                                    TBL_PURC_GRN_DTL_QUANTITY, AVG_NET_RATE, TBL_PURC_GRN_DTL_DISC_AMOUNT,
                                    TBL_PURC_GRN_DTL_AMOUNT, INV_NUMBERS
                                    FROM (
                                        SELECT  GROUP_ITEM_PARENT_NAME, GROUP_ITEM_NAME, PRODUCT_ID ,
                                        PRODUCT_BARCODE_ID, PRODUCT_BARCODE_BARCODE, PRODUCT_NAME,
                                        sum(TBL_PURC_GRN_DTL_QUANTITY)  TBL_PURC_GRN_DTL_QUANTITY,
                                        round( (sum(TBL_PURC_GRN_DTL_NET_AMOUNT) / sum(TBL_PURC_GRN_DTL_QUANTITY)) ,2) avg_net_rate ,
                                        sum(TBL_PURC_GRN_DTL_DISC_AMOUNT) + sum(TBL_PURC_GRN_DTL_SPEC_DISC_AMOUNT)    TBL_PURC_GRN_DTL_DISC_AMOUNT ,
                                        sum(TBL_PURC_GRN_DTL_AMOUNT)  TBL_PURC_GRN_DTL_AMOUNT ,
                                        COUNT(GRN_ID)  INV_NUMBERS
                                        FROM VW_PURC_GRN where branch_id in (".implode(",",$data['branch_ids']).")
                                        $where
                                        GROUP BY GROUP_ITEM_PARENT_NAME, GROUP_ITEM_NAME, PRODUCT_ID,PRODUCT_BARCODE_ID,
                                        PRODUCT_BARCODE_BARCODE, PRODUCT_NAME
                                    ) ABC";

                        $getdata = \Illuminate\Support\Facades\DB::select($qry);
                        $list = [];
                        foreach ($getdata as $list_row){
                           // $list[$list_row->group_item_parent_name][$list_row->group_item_name][] = $list_row;
                            $list[$list_row->group_item_parent_name]['info'] = $list_row;
                            $list[$list_row->group_item_parent_name]['items'][$list_row->group_item_name]['info'] = $list_row;
                            $list[$list_row->group_item_parent_name]['items'][$list_row->group_item_name]['items'][] = $list_row;
                        }

                    @endphp
                    <table width="100%" id="rep_sale_invoice_datatable" class="static_report_table table bt-datatable table-bordered">
                        <tr class="sticky-header">
                            <th class="text-center">S.#</th>
                            <th class="text-center">Barcode</th>
                            <th class="text-center">Product Name</th>
                            <th class="text-center">Quantity</th>
                            <th class="text-center">Avg.</th>
                            <th class="text-center">Discount</th>
                            <th class="text-center">Amount</th>
                            <th class="text-center">No. of Inv</th>
                        </tr>
                        @foreach($list as $f_key=>$f_row)
                            <tr class="outer_total">
                                <td colspan="3">{{ucwords(strtolower($f_key))}}</td>
                                <td class="text-right">{{number_format($f_row['info']->parent_wise_total_qty)}}</td>
                                <td></td>
                                <td class="text-right">{{number_format($f_row['info']->parent_wise_total_discount,3)}}</td>
                                <td class="text-right">{{number_format($f_row['info']->parent_wise_total_amount,3)}}</td>
                                <td></td>
                            </tr>
                            @foreach($f_row['items'] as $sec_key=>$sec_row)
                                <tr class="inner_total">
                                    <td></td>
                                    <td colspan="2">{{ucwords(strtolower($sec_key))}}</td>
                                    <td class="text-right">{{number_format($sec_row['info']->parent_wise_total_qty)}}</td>
                                    <td></td>
                                    <td class="text-right">{{number_format($sec_row['info']->parent_wise_total_discount,3)}}</td>
                                    <td class="text-right">{{number_format($sec_row['info']->parent_wise_total_amount,3)}}</td>
                                    <td></td>
                                </tr>
                                @php
                                    $ki = 1;
                                @endphp
                                @foreach($sec_row['items'] as $i_key=>$item)
                                    <tr>
                                        <td>{{$ki}}</td>
                                        <td>{{$item->product_barcode_barcode}}</td>
                                        <td>{{$item->product_name}}</td>
                                        <td class="text-right">{{number_format($item->tbl_purc_grn_dtl_quantity)}}</td>
                                        <td class="text-right">{{number_format($item->avg_net_rate)}}</td>
                                        <td class="text-right">{{number_format($item->tbl_purc_grn_dtl_disc_amount,3)}}</td>
                                        <td class="text-right">{{number_format($item->tbl_purc_grn_dtl_amount,3)}}</td>
                                        <td class="text-right">{{$item->inv_numbers}}</td>
                                    </tr>
                                    @php
                                        $ki += 1;
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



