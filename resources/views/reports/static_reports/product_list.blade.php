@extends('layouts.report')
@section('title', 'Product List')

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
                <h3 class="kt-invoice__title">{{strtoupper($data['page_title'])}}</h3>
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
                    @php $product_lists = \Illuminate\Support\Facades\DB::table('tbl_purc_product')->whereIn('product_name',$data['product_ids'])->get('product_name'); @endphp
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
                            BRANCH_ID ,
                            BRANCH_NAME ,
                            GROUP_ITEM_NAME ,
                            GROUP_ITEM_PARENT_NAME ,
                            PRODUCT_BARCODE_BARCODE ,
                            PRODUCT_ID  ,
                            PRODUCT_NAME  ,
                            UOM_NAME  ,
                            PRODUCT_BARCODE_PACKING ,
                            NET_TP  ,
                            PRODUCT_BARCODE_COST_RATE  ,
                            SALE_RATE  ,
                            MRP  ,
                            TAX_GROUP_NAME  ,
                            TAX_RATE  ,
                            INCLUSIVE_TAX_PRICE  ,
                            GP_PERC  ,
                            GP_AMOUNT  ,
                            HS_CODE
                            CREATED_BY  ,
                            CREATED_AT
                            FROM VW_PURC_PRODUCT_BARCODE_RATE
                                where branch_id in (".implode(",",$data['branch_ids']).")
                                $where ";
                        $getdata = \Illuminate\Support\Facades\DB::select($qry);
                        $list = [];
                        foreach ($getdata as $row)
                        {
                            $list[$row->group_item_parent_name][$row->group_item_name][$row->product_name][] = $row;
                        }

                    @endphp
                    <table width="100%" id="rep_sale_invoice_datatable" class="static_report_table table bt-datatable table-bordered">
                        <tr class="sticky-header">
                            <th class="text-center">Sr No.</th>
                            <th class="text-center">Barcode</th>
                            <th class="text-center">Product Name</th>
                            <th class="text-center">Packing</th>
                            <th class="text-center">Cost Price</th>
                            <th class="text-center">Sale Price</th>
                            <th class="text-center">MRP</th>
                            <th class="text-center">DE Date</th>
                            <th class="text-center">GP %</th>
                            <th class="text-center">User Name</th>
                        </tr>
                        @foreach($list as $fk=>$f_row)
                            <tr class="first_group_title">
                                <td colspan="10">{{ucwords(strtolower($fk))}}</td>
                            </tr>
                            @foreach($f_row as $sk=>$sec_row)
                                <tr class="second_group_title">
                                    <td></td>
                                    <td colspan="9">{{ucwords(strtolower($sk))}}</td>
                                </tr>
                                @foreach($sec_row as $tk=>$items)
                                    <tr class="third_group_title">
                                        <td></td>
                                        <td></td>
                                        <td colspan="8">{{ucwords(strtolower($tk))}}</td>
                                    </tr>
                                    @php $ki = 1; @endphp
                                    @foreach($items as $item)
                                        <tr>
                                            <td>{{$ki}}</td>
                                            <td>{{$item->product_barcode_barcode}}</td>
                                            <td>{{$item->product_name}}</td>
                                            <td>{{$item->product_barcode_packing}}</td>
                                            <td class="text-right">{{number_format($item->product_barcode_cost_rate,3)}}</td>
                                            <td class="text-right">{{number_format($item->sale_rate,3)}}</td>
                                            <td class="text-right">{{number_format($item->mrp,3)}}</td>
                                            <td class="text-right"></td>
                                            <td class="text-right">{{number_format($item->gp_perc,3)}}</td>
                                            <td>{{$item->created_by}}</td>
                                        </tr>
                                        @php
                                            $ki += 1;
                                        @endphp
                                    @endforeach
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



