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
    </style>
@endsection

@section('content')
    @php
        $data = Session::get('data');
        //dd($data);
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
                        $filter_group_item = "";
                        $whereSup = "";


                        if(isset($data['supplier_ids']) && count($data['supplier_ids']) != 0){
                            $whereSup .= " and SUP_PROD.SUPPLIER_ID in (".implode(",",$data['supplier_ids']).") ";
                        }

                        
                        if(isset($product_groups) && count($product_groups) != 0)
                        {
                            $filter_group_item = " AND ( ";
                            $arr_count = count($product_groups) - 1;
                            foreach ($product_groups as $k=>$product_group){
                                $filter_group_item .= " SALE.GROUP_ITEM_PARENT_ID like '".$product_group->group_item_id."%'";
                                if($arr_count != $k){
                                    $filter_group_item .= " OR ";
                                }
                            }
                            $filter_group_item .= " ) ";
                        }

                        $query = "SELECT *
                            FROM (SELECT 
                                    SALE.GROUP_ITEM_NAME,
                                    EXTRACT (MONTH FROM CAST (SALE.CREATED_AT AS TIMESTAMP)) AS month1,
                                    SALE.SALES_DTL_AMOUNT
                                FROM 
                                    VW_SALE_SALES_INVOICE SALE, VW_PURC_PRODUCT_FOC_PROD_WISE SUP_PROD
                                WHERE SALE.PRODUCT_ID = SUP_PROD.PRODUCT_ID
                                    AND SALE.branch_id IN (".implode(",",$data['branch_ids']).")
                                    and (SALE.SALES_DATE BETWEEN TO_DATE('".$data['from_date']."', 'yyyy/mm/dd') AND TO_DATE('".$data['to_date']."', 'yyyy/mm/dd')) 
                                    $filter_group_item
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
                            ORDER BY GROUP_ITEM_NAME";

                       //dd($query);
                        $get_data = DB::select($query);
                    @endphp
                    <table width="100%" id="month_wise_product_group_sale_datatable" class="table bt-datatable table-bordered data_table_rows_total">
                       <tr class="sticky-header">
                            <th class="text-center" rowspan="2">Sr#</th>
                            <th class="text-left" rowspan="2">Group Name</th>
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
                        <tbody>
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
                        @foreach ($get_data as $item)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }} </td>
                                <td class="text-left">{{ $item->group_item_name }}</td>
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
                            @endphp
                        @endforeach
                        </tbody>
                        <tr class="grand_total">
                            <td colspan="2" class="text-right rep-font-bold"><b>Total : </b></td>
                            <td class="text-right rep-font-bold">{{ @number_format($totJan,2)  }}</td>
                            <td class="text-right rep-font-bold">{{ @number_format($totFeb,2)  }}</td>
                            <td class="text-right rep-font-bold">{{ @number_format($totMarch,2)  }}</td>
                            <td class="text-right rep-font-bold">{{ @number_format($totApr,2)  }}</td>
                            <td class="text-right rep-font-bold">{{ @number_format($totMay,2)  }}</td>
                            <td class="text-right rep-font-bold">{{ @number_format($totJun,2)  }}</td>
                            <td class="text-right rep-font-bold">{{ @number_format($totJul,2)  }}</td>
                            <td class="text-right rep-font-bold">{{ @number_format($totAug,2)  }}</td>
                            <td class="text-right rep-font-bold">{{ @number_format($totSept,2)  }}</td>
                            <td class="text-right rep-font-bold">{{ @number_format($totOct,2)  }}</td>
                            <td class="text-right rep-font-bold">{{ @number_format($totNov,2)  }}</td>
                            <td class="text-right rep-font-bold">{{ @number_format($totDec,2)  }}</td>
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
    <script>
        var $contextMenu = $("#contextMenu");
            $("body").on("contextmenu", 'td[data-id="pd_product_name"]', function(e) {
                var thix = $(this);
                var val = thix.val();
                var product_id = thix.parents('tr').find('td:first-child>.product_id').val();
                var pd_barcode = thix.parents('tr').find('.pd_barcode').val();
                $("#contextMenu li a").attr('data-id',product_id);
                $("#contextMenu li a").attr('data-val',val);
                $("#contextMenu li a").attr('data-barcode',pd_barcode);
                $("#contextMenu li.product_card a").attr('href','/product/edit/'+product_id);

                $contextMenu.css({display: "block",left: e.pageX,top: e.pageY});
                return false;
            });
    </script>
@endsection

@section('customJS')

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



