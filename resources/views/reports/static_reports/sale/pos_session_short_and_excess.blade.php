@extends('layouts.report')
@section('title', 'POS Short/Excess Activity')

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
                @if(isset($data['users']) && count($data['users']) != 0)
                @php
                    $data['Salesman'] = \App\Models\User::whereIn('id',$data['users'])->get();
                @endphp
                    <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;">Salesman:</span>
                        @foreach($data['Salesman'] as $Salesman)
                            <span style="color: #5578eb;">{{" ".ucfirst(strtolower($Salesman->name))}}</span><span style="color: #ff0000">,</span>
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
                        if(count($data['users']) != 0){
                            $where .= " and TBL_SALE_DAY_dtl.SALEMAN_ID in (".implode(",",$data['users']).")";
                        }
                        
                        $qry = "SELECT DISTINCT 
                            TBL_SALE_DAY.BRANCH_ID,
                            tbl_soft_branch.BRANCH_NAME,
                            TBL_SALE_DAY_dtl.SALEMAN_ID,
                            TBL_SALE_DAY_dtl.DAY_DATE ,
                            TBL_SALE_DAY_dtl.DAY_ID ,
                            TBL_SALE_DAY_dtl.CASH_DIFFERENCE,
                            TBL_SALE_DAY_dtl.CREATED_AT,
                            users.NAME,
                            TBL_SOFT_POS_TERMINAL.TERMINAL_NAME
                        FROM
                            TBL_SALE_DAY_dtl,
                            users,
                            tbl_soft_branch,
                            TBL_SALE_DAY ,
                            TBL_SOFT_POS_TERMINAL
                        WHERE TBL_SALE_DAY_dtl.DAY_CASE_TYPE = 'day_calc' 
                            AND TBL_SALE_DAY_dtl.saleman_id = users.ID 
                            and TBL_SALE_DAY.BRANCH_ID = tbl_soft_branch.BRANCH_ID
                            AND TBL_SALE_DAY.DAY_ID = TBL_SALE_DAY_dtl.DAY_ID 
                            AND TBL_SALE_DAY.TERMINAL_ID = TBL_SOFT_POS_TERMINAL.TERMINAL_ID(+)
                            AND TBL_SALE_DAY.BRANCH_ID in (".implode(",",$data['branch_ids']).")
                            and (TBL_SALE_DAY.CREATED_AT between to_date ('".$data['date_time_from']."', 'yyyy/mm/dd HH24:MI') and to_date ('".$data['date_time_to']."', 'yyyy/mm/dd HH24:MI'))
                            $where
                            ORDER BY TBL_SALE_DAY_dtl.DAY_DATE
                            ";
//dd($qry);
                        $getdata = \Illuminate\Support\Facades\DB::select($qry);
                        //dd($getdata);
                        
                        $list = [];
                        foreach ($getdata as $list_row){
                            $list[$list_row->branch_name][$list_row->name][] = $list_row;
                        }
                        //dd($list);
                        
                    @endphp
                    <table width="100%" id="rep_pos_short_and_excess_datatable" class="static_report_table table bt-datatable table-bordered">
                        <tr class="sticky-header">
                            <th width="6%" class="text-center">S.No</th>
                            <th width="13%" class="text-center">Opening date</th>
                            <th width="13%" class="text-center">Closing date</th>
                            <th width="34%" class="text-left">Operator Name</th>
                            <th width="10%" class="text-center">Counter</th>
                            <th width="12%" class="text-center">Short Amount</th>
                            <th width="12%" class="text-center">Excess Amount</th>
                        </tr>
                        @php
                            $gtotshortamount = 0;
                            $gtotexcessamount = 0;
                            $branch_name="";
                        @endphp
                        @foreach($list as $branch_key=>$branch_row)
                            @php
                                $branch_name = $branch_key;
                            @endphp
                            <tr class="outer_total">
                                <td colspan="7"><b>Branch : {{ucwords(strtolower($branch_key))}}</b></td>
                            </tr>
                            @php
                                $stotshortamount = 0;
                                $stotexcessamount = 0;
                            @endphp
                            @foreach($branch_row as $users_key=>$users_row)
                                <tr class="inner_total">
                                    <td colspan="7"><b>Operator : {{ucwords(strtolower($users_key))}}</b></td>
                                </tr>
                                @php
                                    $ki = 1;
                                    $shortamount = 0;
                                    $excessamount = 0;
                                    $totshortamount = 0;
                                    $totexcessamount = 0;
                                    $operator_name="";
                                @endphp
                                @foreach($users_row as $i_key=>$item)
                                    @php
                                        if($item->cash_difference > 0){
                                            $shortamount = $item->cash_difference;
                                        }else{
                                            $shortamount=0;
                                        }

                                        if($item->cash_difference < 0){
                                            $excessamount = $item->cash_difference;
                                        }else{
                                            $excessamount=0;
                                        }
                                        
                                        $totshortamount = $totshortamount + $shortamount;
                                        $totexcessamount = $totexcessamount + $excessamount;

                                        $stotshortamount = $stotshortamount + $shortamount;
                                        $stotexcessamount = $stotexcessamount + $excessamount;

                                        $gtotshortamount = $gtotshortamount + $shortamount;
                                        $gtotexcessamount = $gtotexcessamount + $excessamount;
                                        
                                        $operator_name = $item->name;

                                    @endphp
                                    <tr>
                                        <td class="text-center">{{$ki}}</td>
                                        <td class="text-center">{{date('d-m-Y', strtotime($item->day_date))}}</td>
                                        <td>{{date('d-m-Y h:i A', strtotime($item->created_at))}}</td>
                                        <td class="text-left">{{$item->name}}</td>
                                        <td class="text-center">{{$item->terminal_name}}</td>
                                        <td class="text-right">{{number_format($shortamount,2)}}</td>
                                        <td class="text-right">{{number_format($excessamount,2)}}</td>
                                    </tr>
                                    @php
                                        $ki += 1;
                                    @endphp
                                @endforeach
                                <tr>
                                    <td colspan="5" class="text-right"><strong>{{$operator_name}} Total: </strong></td>
                                    <td class="text-right"><strong>{{number_format($totshortamount,2)}}</strong></td>
                                    <td class="text-right"><strong>{{number_format($totexcessamount,2)}}</strong></td>
                                </tr>
                            @endforeach
                                <tr>
                                    <td colspan="5" class="text-right"><strong>{{$branch_name}} Sub Total: </strong></td>
                                    <td class="text-right"><strong>{{number_format($stotshortamount,2)}}</strong></td>
                                    <td class="text-right"><strong>{{number_format($stotexcessamount,2)}}</strong></td>
                                </tr>
                        @endforeach
                        <tr>
                            <td colspan="5" class="text-right"><strong>Grand Total: </strong></td>
                            <td class="text-right"><strong>{{number_format($gtotshortamount,2)}}</strong></td>
                            <td class="text-right"><strong>{{number_format($gtotexcessamount,2)}}</strong></td>
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
                $("#rep_pos_short_and_excess_datatable").table2excel({
                    // exclude: ".noExport",
                    filename: "report.xls",
                });
            });
        </script>
    @endif
@endsection



