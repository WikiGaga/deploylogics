@extends('layouts.report')
@section('title', 'Reporting')

@section('pageCSS')
    <style>
        tr.level_1>td:first-child,
        tr.level_1>td:nth-child(2){
            font-size: 15px;
        }
        tr.level_1>td{
            font-weight: 500 !important;
        }
        tr.level_2>td:first-child,
        tr.level_2>td:nth-child(2){
            font-size: 13px;
        }
        tr.level_2>td{
            font-weight: 500 !important;
        }
        tr.level_3>td:first-child,
        tr.level_3>td:nth-child(2){
            font-size: 12px;
        }
        tr.level_3>td{
            font-weight: 500 !important;
        }

        tr.level_4>td {

        }
        td.right_number {
            text-align: right;
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
                <h1 class="kt-invoice__title">{{strtoupper($data['page_title'])}}</h1>
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
            </div>
            @include('reports.template.branding')
        </div>
        @php
            $where = '';
            $where .= " v.voucher_date between to_date ('".$data['from_date']."', 'yyyy/mm/dd') and to_date ('".$data['to_date']."', 'yyyy/mm/dd')";
            $where .= " and v.business_id = ".auth()->user()->business_id." and v.branch_id in (".implode(",",$data['branch_ids']).")";
           
            if(!empty($data['chart_account_id'])){
                $chart_lists  = DB::table('tbl_acco_chart_account')->select('chart_code','chart_name')->where('chart_account_id',$data['chart_account_id'])->first();
                $chart_data = explode ("-", $chart_lists->chart_code); 

                $where .= " and coa.chart_code Like '".$chart_data[0]."-%'";
            }
      
            DB::statement('delete from TBL_TMP_TRIAL');

            $query = "INSERT INTO TBL_TMP_TRIAL   
                        (
                            CHART_ACCOUNT_ID,
                            BALANCE,
                            CHART_ACCOUNT_CODE,
                            BUSINESS_ID,
                            COMPANY_ID  
                        )
                    select distinct  coa.CHART_ACCOUNT_ID ,  
                    sum (v.voucher_debit) over (partition by v.chart_account_id,v.business_id,v.company_id)  - 
                    sum (v.voucher_credit) over (partition by v.chart_account_id,v.business_id,v.company_id) AS CR_BALANCE ,
                    coa.chart_code  ,v.BUSINESS_ID , v.COMPANY_ID  
                    from tbl_acco_voucher v, tbl_acco_chart_account coa
                    where v.chart_account_id = coa.chart_account_id and ".$where;

                    DB::statement($query);

            $qry = "select trial.business_id,trial.company_id,trial.chart_level,ca.chart_account_id,trial.chart_account_code,ca.chart_name, trial.balance,
                balance_per 
                from (    
                    
                    select distinct 4 chart_level, business_id, company_id , chart_account_code, sum(balance) balance , 
                    round(100*(sum(balance) / sum(sum(balance)) over ()),2) balance_per   
                    from  tbl_tmp_trial    group by      business_id, company_id , chart_account_code  
                    
                    union all  

                    select distinct 3 chart_level, business_id, company_id , (substr (chart_account_code, 0, 7) || '-0000') chart_account_code ,  sum (balance) over (partition by substr (chart_account_code, 0, 7) ) balance ,
                    0 balance_per    from  tbl_tmp_trial  

                    union all 

                    select distinct 2 chart_level, business_id, company_id , (substr (chart_account_code, 0, 4) || '-00-0000') chart_account_code ,  sum (balance) over (partition by substr (chart_account_code, 0, 4) ) , 0 balance_per  from  tbl_tmp_trial  

                    union all 

                    select distinct 1 chart_level, business_id, company_id , (substr (chart_account_code, 0, 1) || '-00-00-0000' ) chart_account_code ,  sum (balance) over (partition by substr (chart_account_code, 0, 1) ) , 0 balance_per  from  tbl_tmp_trial  


                )  trial,
                tbl_acco_chart_account ca
                where  trial.chart_account_code = ca.chart_code(+) order by trial.chart_account_code , trial.chart_level";

                $list = DB::select($qry);
                
        @endphp
        <div class="kt-portlet__body">
            <div class="row row-block">
                <div class="col-lg-12">
                    <table width="100%" id="rep_sale_invoice_datatable" class="table bt-datatable table-bordered">
                        <tr class="sticky-header">
                            <th>Account Code</th>
                            <th>Account Title</th>
                            <th class="text-center">Amount</th>
                            <th class="text-center">Amount %</th>
                        </tr>
                        @php
                            $level_4_amount = 0;
                            $level_4_perc = 0;
                        @endphp
                        @foreach($list as $accounts)
                            @if($accounts->chart_level == 1)
                                <tr class="level_1">
                                    <td><span class="generate_report" data-id="{{$accounts->chart_account_id}}">{{$accounts->chart_account_code}}</span></td>
                                    <td>{{ucwords(strtolower($accounts->chart_name))}}</td>
                                    <td class="right_number">{{number_format($accounts->balance,3)}}</td>
                                    <td class="right_number">{{number_format($accounts->balance_per,3)}}</td>
                                </tr>
                            @endif
                            @if($accounts->chart_level == 2)
                                <tr class="level_2">
                                    <td><span class="generate_report" data-id="{{$accounts->chart_account_id}}">{{$accounts->chart_account_code}}</span></td>
                                    <td  style="padding-left: 15px !important;">{{ucwords(strtolower($accounts->chart_name))}}</td>
                                    <td class="right_number">{{number_format($accounts->balance,3)}}</td>
                                    <td class="right_number">{{number_format($accounts->balance_per,3)}}</td>
                                </tr>
                            @endif
                            @if($accounts->chart_level == 3)
                                <tr class="level_3">
                                    <td><span class="generate_report" data-id="{{$accounts->chart_account_id}}">{{$accounts->chart_account_code}}</span></td>
                                    <td style="padding-left: 30px !important;">{{ucwords(strtolower($accounts->chart_name))}}</td>
                                    <td class="right_number">{{number_format($accounts->balance,3)}}</td>
                                    <td class="right_number">{{number_format($accounts->balance_per,3)}}</td>
                                </tr>
                            @endif
                            @if($accounts->chart_level == 4)
                                <tr class="level_4">
                                    <td style="padding-left: 25px !important;"><span class="generate_report" data-id="{{$accounts->chart_account_id}}">{{$accounts->chart_account_code}}</span></td>
                                    <td style="padding-left: 45px !important;">{{ucwords(strtolower($accounts->chart_name))}}</td>
                                    <td class="right_number">{{number_format($accounts->balance,3)}}</td>
                                    <td class="right_number">{{number_format($accounts->balance_per,3)}}</td>

                                    @php
                                        $level_4_amount += $accounts->balance;
                                        $level_4_perc += $accounts->balance_per;
                                    @endphp
                                </tr>
                            @endif
                        @endforeach
                        <tr class="grand_total">
                            <td colspan="2" class="rep-font-bold">Total:</td>
                            <td class="text-right rep-font-bold">{{number_format($level_4_amount,2)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($level_4_perc,2)}}</td>
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
        $(".generate_report").click(function(e){
            var account_id = $(this).data('id');
            var from_date = "{{date('d-m-Y', strtotime($data['to_date']))}}";
            var to_date =  "{{date('d-m-Y', strtotime($data['from_date']))}}";

            var formData = {
                report_branch_ids : [{{auth()->user()->branch_id}}],
                chart_account : account_id,
                date_to : from_date,
                date_from : to_date,
                report_case : 'accounting_ledger',
                report_type :  "static",
                form_file_type : "report"
            }
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url         : '{{ action('Report\UserReportsController@staticStore', ['static','static','accounting_ledger']) }}',
                type        : 'POST',
                dataType	: 'json',
                data        : formData,
                success: function(response) {
                    if(response.status == 'success'){
                        toastr.success(response.message);
                        window.open(response['data']['url'], "_blank");
                    }else{
                        toastr.error(response.message);
                        window.location.reload();
                    }
                }
            });
        });
    </script>
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



