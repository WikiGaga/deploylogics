@extends('layouts.report')
@section('title', 'Month Wise Product Group Sale')

@section('pageCSS')
    <style>
        /* Styles go here */
        @media print {
            thead {display: table-header-group;}
            tfoot {display: table-footer-group;}
            tfoot>tr>td {padding:0 !important;}
            body {margin: 0;}
        }
        .border_right{
            border-right: 1px solid #000 !important;
        }
        .sec-sticky-header{
            position: sticky;
            top: 28px;
            background-color: #f7f8fa;
        }
        tbody.month_wise_group tr:hover {
            background: antiquewhite;
        }
        .grand_total{
            font-weight: bold !important;
        }
    </style>
@endsection

@section('content')
    @php
    $data = Session::get('data');
   // $supplierDtl = \Illuminate\Support\Facades\DB::table('tbl_purc_supplier')->where('supplier_id',$data['supplier_ids'])->first();
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
                    @php $branch_lists = \Illuminate\Support\Facades\DB::table('tbl_soft_branch')->whereIn('branch_id',$data['branch_ids'])->where(\App\Library\Utilities::currentBC())->where('branch_active_status',1)->get(['branch_id','branch_name','branch_short_name']); @endphp
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
                @if(count($data['product_group']) != 0 && $data['product_group'] != "" && $data['product_group'] != null)
                    @php $product_groups = \Illuminate\Support\Facades\DB::table('vw_purc_group_item')->whereIn('group_item_id',$data['product_group'])->select('group_item_name_string','group_item_id')->get(); @endphp
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

                        $whereSup = "";
                        $where = "";

                        if(isset($data['supplier_ids']) && count($data['supplier_ids']) != 0){
                            $whereSup .= " and SUP_PROD.SUPPLIER_ID in (".implode(",",$data['supplier_ids']).") ";
                        }

                        $vendorfrom = "";
                        $vendorjoin = "";
                        $vendor="";
                        // Vendor Wise whereclause
                        if(isset($data['supplier_ids']) && !empty($data['supplier_ids']) != 0){
                            $vendorfrom = " ,VW_PURC_PRODUCT_FOC_PROD_WISE SUP_PROD";
                            $vendorjoin = " AND SALE.PRODUCT_ID = SUP_PROD.PRODUCT_ID";
                           $vendor = "SUP_PROD.supplier_id,";
                        }

                        if($data['product_sub_group'] == 1)
                        {
                            if(isset($product_groups) && count($product_groups) != 0)
                            {
                                $where = " AND ( ";
                                $arr_count = count($product_groups) - 1;
                                foreach ($product_groups as $k=>$product_group){
                                    $where .= " SALE.GROUP_ITEM_ID like '".$product_group->group_item_id."%'";
                                    if($arr_count != $k){
                                        $where .= " OR ";
                                    }
                                }
                                $where .= " ) ";
                            }

                            $query = "SELECT *
                            FROM (SELECT
                                    SALE.BRANCH_ID,
                                    SALE.BRANCH_NAME,
                                    $vendor
                                    SALE.GROUP_ITEM_NAME as GROUP_ITEM_PARENT_NAME,
                                    EXTRACT (MONTH FROM CAST (SALE.CREATED_AT AS TIMESTAMP)) AS month1,
                                    SALE.SALES_DTL_AMOUNT
                                FROM
                                    VW_SALE_SALES_INVOICE SALE
                                    $vendorfrom
                                WHERE SALE.branch_id IN (".implode(",",$data['branch_ids']).")
                                    and (SALE.SALES_DATE BETWEEN TO_DATE('".$data['from_date']."', 'yyyy/mm/dd') AND TO_DATE('".$data['to_date']."', 'yyyy/mm/dd'))
                                    $vendorjoin
                                    $where
                                    $whereSup
                                )PIVOT (SUM (SALES_DTL_AMOUNT)
                                FOR month1
                                IN (1 AS Jan,
                                    2 AS Feb,
                                    3 AS March,
                                    4 AS Apr,
                                    5 AS May,
                                    6 AS Jun,
                                    7 AS Jul,
                                    8 AS Aug,
                                    9 AS Sept,
                                    10 AS Oct,
                                    11 AS Nov,
                                    12 AS Dec)
                            )
                            ORDER BY GROUP_ITEM_PARENT_NAME";
                        }else{

                            if(isset($product_groups) && count($product_groups) != 0)
                            {
                                $where = " AND ( ";
                                $arr_count = count($product_groups) - 1;
                                foreach ($product_groups as $k=>$product_group){
                                    $where .= " SALE.GROUP_ITEM_PARENT_ID like '".$product_group->group_item_id."%'";
                                    if($arr_count != $k){
                                        $where .= " OR ";
                                    }
                                }
                                $where .= " ) ";
                            }

                            $query = "SELECT *
                            FROM (SELECT
                                    SALE.BRANCH_ID,
                                    SALE.BRANCH_NAME,
                                    $vendor
                                    SALE.GROUP_ITEM_PARENT_ID,
                                    SALE.GROUP_ITEM_PARENT_NAME,
                                    EXTRACT (MONTH FROM CAST (SALE.CREATED_AT AS TIMESTAMP)) AS month1,
                                    SALE.SALES_DTL_AMOUNT
                                FROM
                                    VW_SALE_SALES_INVOICE SALE
                                    $vendorfrom
                                WHERE SALE.branch_id IN (".implode(",",$data['branch_ids']).")
                                    and (SALE.SALES_DATE BETWEEN TO_DATE('".$data['from_date']."', 'yyyy/mm/dd') AND TO_DATE('".$data['to_date']."', 'yyyy/mm/dd'))
                                    $vendorjoin
                                    $where
                                    $whereSup
                                )PIVOT (SUM (SALES_DTL_AMOUNT)
                                FOR month1
                                IN (1 AS Jan,
                                    2 AS Feb,
                                    3 AS March,
                                    4 AS Apr,
                                    5 AS May,
                                    6 AS Jun,
                                    7 AS Jul,
                                    8 AS Aug,
                                    9 AS Sept,
                                    10 AS Oct,
                                    11 AS Nov,
                                    12 AS Dec)
                            )
                            ORDER BY GROUP_ITEM_PARENT_NAME";
                        }

               // dd($query);
                        $getdata = DB::select($query);

                        $list = [];
                        foreach ($getdata as $list_row){
                            $list[$list_row->branch_name][] = $list_row;
                        }

                        $gtotJan = 0;
                        $gtotFeb = 0;
                        $gtotMarch = 0;
                        $gtotApr = 0;
                        $gtotMay = 0;
                        $gtotJun = 0;
                        $gtotJul = 0;
                        $gtotAug = 0;
                        $gtotSept = 0;
                        $gtotOct = 0;
                        $gtotNov = 0;
                        $gtotDec = 0;
                    @endphp
                    <table width="100%" id="month_wise_product_group_sale_datatable" class="table bt-datatable table-bordered data_table_rows_total">
                       <tr class="sticky-header">
                            <th class="text-center">Sr#</th>
                            <th class="text-left">Group Name</th>
                            <th class="text-center">Jan</th>
                            <th class="text-center">Feb</th>
                            <th class="text-center">March</th>
                            <th class="text-center">Apr</th>
                            <th class="text-center">May</th>
                            <th class="text-center">Jun</th>
                            <th class="text-center">Jul</th>
                            <th class="text-center">Aug</th>
                            <th class="text-center">Sept</th>
                            <th class="text-center">Oct</th>
                            <th class="text-center">Nov</th>
                            <th class="text-center">Dec</th>
                        </tr>
                        @php
                            if($data['product_sub_group'] == 1)
                            {
                                $group_item_class = '';
                            }else{
                                $group_item_class = "month_wise_group";
                            }
                        @endphp
                        <tbody>
                            @php
                                $gtotJan = 0;
                                $gtotFeb = 0;
                                $gtotMarch = 0;
                                $gtotApr = 0;
                                $gtotMay = 0;
                                $gtotJun = 0;
                                $gtotJul = 0;
                                $gtotAug = 0;
                                $gtotSept = 0;
                                $gtotOct = 0;
                                $gtotNov = 0;
                                $gtotDec = 0;
                            @endphp
                            @foreach($list as $branch_key=>$branch_row)
                                @php
                                    $branch_name = $branch_key;
                                @endphp
                                <tr class="outer_total">
                                    <td colspan="14"><b>Branch : {{ucwords(strtolower($branch_key))}}</b></td>
                                </tr>
                                @php
                                    $totJan = 0;
                                    $totFeb = 0;
                                    $totMarch = 0;
                                    $totApr = 0;
                                    $totMay = 0;
                                    $totJun = 0;
                                    $totJul = 0;
                                    $totAug = 0;
                                    $totSept = 0;
                                    $totOct = 0;
                                    $totNov = 0;
                                    $totDec = 0;
                                @endphp
                                @foreach ($branch_row as $i_key=>$item)
                                    @php
                                        if($data['product_sub_group'] == 1)
                                        {
                                            if(isset($data['supplier_ids']) && !empty($data['supplier_ids']) != 0)
                                            {
                                                $group_item = $item->branch_id."@".$item->group_item_parent_id."@".$item->supplier_id;
                                            }else{
                                                $group_item = '';
                                            }
                                        }else{
                                            $group_item = '';
                                        }
                                    @endphp
                                    <tr class={{$group_item_class}} data-id="{{$group_item}}">
                                        <td class="text-center">{{ $loop->iteration }} </td>
                                        <td class="text-left">{{ $item->group_item_parent_name }}</td>
                                        <td class="text-right">{{ @number_format($item->jan,2)  }} </td>
                                        <td class="text-right">{{ @number_format($item->feb,2)  }} </td>
                                        <td class="text-right">{{ @number_format($item->march,2)  }} </td>
                                        <td class="text-right">{{ @number_format($item->apr,2)  }} </td>
                                        <td class="text-right">{{ @number_format($item->may,2)  }} </td>
                                        <td class="text-right">{{ @number_format($item->jun,2)  }} </td>
                                        <td class="text-right">{{ @number_format($item->jul,2)  }} </td>
                                        <td class="text-right">{{ @number_format($item->aug,2)  }} </td>
                                        <td class="text-right">{{ @number_format($item->sept,2)  }} </td>
                                        <td class="text-right">{{ @number_format($item->oct,2)  }} </td>
                                        <td class="text-right">{{ @number_format($item->nov,2)  }} </td>
                                        <td class="text-right">{{ @number_format($item->dec,2)  }} </td>
                                    </tr>
                                    @php
                                        $totJan += $item->jan;
                                        $totFeb += $item->feb;
                                        $totMarch += $item->march;
                                        $totApr += $item->apr;
                                        $totMay += $item->may;
                                        $totJun += $item->jun;
                                        $totJul += $item->jul;
                                        $totAug += $item->aug;
                                        $totSept += $item->sept;
                                        $totOct += $item->oct;
                                        $totNov += $item->nov;
                                        $totDec += $item->dec;

                                        $gtotJan += $item->jan;
                                        $gtotFeb += $item->feb;
                                        $gtotMarch += $item->march;
                                        $gtotApr += $item->apr;
                                        $gtotMay += $item->may;
                                        $gtotJun += $item->jun;
                                        $gtotJul += $item->jul;
                                        $gtotAug += $item->aug;
                                        $gtotSept += $item->sept;
                                        $gtotOct += $item->oct;
                                        $gtotNov += $item->nov;
                                        $gtotDec += $item->dec;
                                    @endphp
                                @endforeach
                            </tbody>
                            <tr>
                                <td colspan="2" class="text-right grand_total "><b>Total : </b></td>
                                <td class="text-right grand_total">{{ @number_format($totJan,2)  }}</td>
                                <td class="text-right grand_total">{{ @number_format($totFeb,2)  }}</td>
                                <td class="text-right grand_total">{{ @number_format($totMarch,2)  }}</td>
                                <td class="text-right grand_total">{{ @number_format($totApr,2)  }}</td>
                                <td class="text-right grand_total">{{ @number_format($totMay,2)  }}</td>
                                <td class="text-right grand_total">{{ @number_format($totJun,2)  }}</td>
                                <td class="text-right grand_total">{{ @number_format($totJul,2)  }}</td>
                                <td class="text-right grand_total">{{ @number_format($totAug,2)  }}</td>
                                <td class="text-right grand_total">{{ @number_format($totSept,2)  }}</td>
                                <td class="text-right grand_total">{{ @number_format($totOct,2)  }}</td>
                                <td class="text-right grand_total">{{ @number_format($totNov,2)  }}</td>
                                <td class="text-right grand_total">{{ @number_format($totDec,2)  }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="2" class="text-right grand_total"><b>Grand Total : </b></td>
                            <td class="text-right grand_total">{{ @number_format($gtotJan,2)  }}</td>
                            <td class="text-right grand_total">{{ @number_format($gtotFeb,2)  }}</td>
                            <td class="text-right grand_total">{{ @number_format($gtotMarch,2)  }}</td>
                            <td class="text-right grand_total">{{ @number_format($gtotApr,2)  }}</td>
                            <td class="text-right grand_total">{{ @number_format($gtotMay,2)  }}</td>
                            <td class="text-right grand_total">{{ @number_format($gtotJun,2)  }}</td>
                            <td class="text-right grand_total">{{ @number_format($gtotJul,2)  }}</td>
                            <td class="text-right grand_total">{{ @number_format($gtotAug,2)  }}</td>
                            <td class="text-right grand_total">{{ @number_format($gtotSept,2)  }}</td>
                            <td class="text-right grand_total">{{ @number_format($gtotOct,2)  }}</td>
                            <td class="text-right grand_total">{{ @number_format($gtotNov,2)  }}</td>
                            <td class="text-right grand_total">{{ @number_format($gtotDec,2)  }}</td>
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
    <script>
        var xhrGetData = true;
        $(document).on('click','td',function(){
            var thix = $(this);
            var tbody = thix.parents('tr.month_wise_group');
            var tr = thix.parents('tr');
            var array = tr.attr('data-id');
            var myArray = array.split("@");
            var supplier_id;
            var branch_id = myArray[0];
            var group_item_parent_id = myArray[1];
            if(myArray[2] != "")
            {
                supplier_id = myArray[2];
            }
            var validate = true;


            if(tbody.length == 1){
                if(valueEmpty(branch_id)){
                    toastr.error("Branch not Found");
                    validate = false;
                    return true;
                }
                if(valueEmpty(group_item_parent_id)){
                    toastr.error("Group not Found");
                    validate = false;
                    return true;
                }
                if(valueEmpty(supplier_id)){
                    toastr.error("Supplier not Found");
                    validate = false;
                    return true;
                }
                if(validate && xhrGetData){
                    $('body').addClass('pointerEventsNone');
                    xhrGetData = false;
                    var formData = {
                        report_case : 'month_wise_group_first_level',
                        report_type: 'static',
                        date_from: '{{ date('d-m-Y', strtotime($data['from_date'])) }}',
                        date_to: '{{ date('d-m-Y', strtotime($data['to_date'])) }}',
                        form_file_type: 'report',
                        report_business_id : '{{auth()->user()->business_id}}',
                        'product_group[0]' : group_item_parent_id,
                        'report_branch_ids[0]' : branch_id,
                        'supplier_ids[0]' : supplier_id,
                    };

                    var url = "{{ action('Report\UserReportsController@staticStore', ['static','month_wise_group_first_level','']) }}";
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: "POST",
                        url: url,
                        dataType	: 'json',
                        data        : formData,
                        success: function(response,data) {

                            if(response.status == 'success'){
                                toastr.success(response.message);
                                window.open(response['data']['url'], parseInt(Math.random()*10000000000));
                            }else{
                                toastr.error(response.message);
                            }
                            xhrGetData = true;
                            $('body').removeClass('pointerEventsNone');
                        },
                        error: function(response,status) {
                            toastr.error(response.responseJSON.message);
                            xhrGetData = true;
                            $('body').removeClass('pointerEventsNone');
                        }
                    });
                }
            }

        })
    </script>
@endsection
@section('exportXls')
    @if($data['form_file_type'] == 'xls')
        <script>
            $(document).ready(function() {
                $("#month_wise_product_group_sale_datatable").table2excel({
                    // exclude: ".noExport",
                    filename: "report.xls",
                });
            });
        </script>
    @endif
@endsection



