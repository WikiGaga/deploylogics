@extends('layouts.report')
@section('title', 'Date Wise Sales Summary')

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
                    <span style="color: #5578eb;">{{" ".date('d-m-Y H:s:i', strtotime($data['from_date_time']))." to ". date('d-m-Y H:s:i', strtotime($data['to_date_time']))." "}}</span>
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
            </div>
            @include('reports.template.branding')
        </div>
        <div class="kt-portlet__body">
            <div class="row row-block">
                <div class="col-lg-12">
                    @php
                        $where = "where branch_id in (".implode(",",$data['branch_ids']).") ";
                        $where .= " and (created_at between to_date('".$data['from_date_time']."','yyyy/mm/dd HH24:MI:SS') and to_date('".$data['to_date_time']."','yyyy/mm/dd HH24:MI:SS') )";
                       // $where .= " and (created_at between to_date('".date('d-m-Y H:s:i', strtotime($data['from_date_time']))."','yyyy/mm/dd HH24:MI:SS') and to_date('".date('d-m-Y H:s:i', strtotime($data['to_date_time']))."','yyyy/mm/dd HH24:MI:SS') )";

                        $qry = "SELECT BRANCH_ID,
                            BRANCH_NAME,
                            SALES_DATE,
                            SUM (SALE_AMOUNT)                                   SALE_AMOUNT,
                            SUM (SALE_RET_AMOUNT)                               SALE_RET_AMOUNT,
                            SUM (SALE_AMOUNT) - ABS (SUM (SALE_RET_AMOUNT))     SALE_NET_AMOUNT
                        FROM (  SELECT SALES_CODE,
                            BRANCH_ID,
                            BRANCH_NAME,
                            SALES_DATE,
                            CASE
                                WHEN SALES_TYPE = 'POS'
                                THEN SUM (NVL (SALES_DTL_NET_AMOUNT, 0)) + MAX (NVL (FBR_CHARGES, 0))
                                ELSE 0
                            END SALE_AMOUNT,
                            CASE
                                WHEN SALES_TYPE = 'RPOS'
                                THEN SUM (NVL (SALES_DTL_NET_AMOUNT, 0)) + MAX (NVL (FBR_CHARGES, 0))
                                ELSE 0
                            END SALE_RET_AMOUNT
                        FROM 
                            VW_SALE_SALES_INVOICE
                            $where
                        GROUP BY SALES_CODE,
                            BRANCH_ID,
                            BRANCH_NAME,
                            SALES_DATE,
                            SALES_TYPE
                        ) gaga
                        GROUP BY BRANCH_ID, BRANCH_NAME, SALES_DATE
                        ORDER BY BRANCH_NAME, SALES_DATE";

                        //dd($qry);

                        $getdata = \Illuminate\Support\Facades\DB::select($qry);
                        // dd($getdata);
                        $list = [];
                        foreach ($getdata as $list_row){
                            $list[$list_row->branch_name][$list_row->sales_date][] = $list_row;
                        }
                        //dd($list);
                    $grand_sale_amount = 0;
                    $grand_sale_ret_amount = 0;
                    $grand_sale_net_amount = 0;
                    @endphp
                    <table width="100%" id="date_wise_summarized_sales" class="static_report_table table bt-datatable table-bordered">
                        <tr class="sticky-header">
                            <th class="text-center">Date</th>
                            <th class="text-center">Sales</th>
                            <th class="text-center">Sales Return</th>
                            <th class="text-center">Net Sales</th>
                        </tr>
                        @foreach($list as $bn=>$br_row)
                            @php
                                $sale_amount = 0;
                                $sale_ret_amount = 0;
                                $sale_net_amount = 0;
                            @endphp
                            <tr>
                                <td colspan="7">{{$bn}}</td>
                            </tr>
                            @foreach($br_row as $rows)
                                @foreach($rows as $row)
                                    <tr>
                                        <td>{{date('d-m-Y',strtotime($row->sales_date))}}</td>
                                       <td class="text-right">{{number_format($row->sale_amount,3)}}</td>
                                        <td class="text-right">{{number_format($row->sale_ret_amount,3)}}</td>
                                        <td class="text-right">{{number_format($row->sale_net_amount,3)}}</td>
                                    </tr>
                                @endforeach
                                @php
                                    $sale_amount += $row->sale_amount;
                                    $sale_ret_amount += $row->sale_ret_amount;
                                    $sale_net_amount += $row->sale_net_amount;
                                @endphp
                            @endforeach
                            @php
                                $grand_sale_amount += $sale_amount;
                                $grand_sale_ret_amount += $sale_ret_amount;
                                $grand_sale_net_amount += $sale_net_amount;
                            @endphp
                            <tr class="sub_total">
                                <td class="rep-font-bold">Branch Total:</td>
                                <td class="text-right rep-font-bold">{{number_format($sale_amount,3)}}</td>
                                <td class="text-right rep-font-bold">{{number_format($sale_ret_amount,3)}}</td>
                                <td class="text-right rep-font-bold">{{number_format($sale_net_amount,3)}}</td>
                            </tr>
                        @endforeach
                        <tr class="grand_total">
                            <td class="rep-font-bold">Grand Total:</td>
                            <td class="text-right rep-font-bold">{{number_format($grand_sale_amount,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($grand_sale_ret_amount,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($grand_sale_net_amount,3)}}</td>
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
                $("#date_wise_summarized_sales").table2excel({
                    // exclude: ".noExport",
                    filename: "report.xls",
                });
            });
        </script>
    @endif
@endsection



