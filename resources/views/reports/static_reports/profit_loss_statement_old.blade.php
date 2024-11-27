@extends('layouts.report')
@section('title', 'Reporting')

@section('pageCSS')
    <style>
        /* Styles go here */
        @media print {
            thead {display: table-header-group;}
            tfoot {display: table-footer-group;}
            tfoot>tr>td {padding:0 !important;}
            body {margin: 0;}
        }
        .acc_heading {
            color: #e27d00;
            font-size: 16px;
            font-weight: 400;
            display: block;
            margin-bottom: 7px;
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
            $where .= "v.voucher_date between to_date ('".$data['from_date']."', 'yyyy/mm/dd') and to_date ('".$data['to_date']."', 'yyyy/mm/dd')";
            $where .= " and v.business_id = ".auth()->user()->business_id." and v.branch_id in (".implode(",",$data['branch_ids']).")";
            if (!empty($data['chart_account_id'])) {
                $where .= " and v.chart_account_id in (204,32142,209)";
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
                    $currency = \App\Models\TblDefiCurrency::where(\App\Library\Utilities::currentBC())->where('currency_default',1)->first();
        @endphp
        <div class="kt-portlet__body">
            <div class="row row-block">
                <div class="col-lg-12">
                    <table width="100%" id="rep_sale_invoice_datatable" class="table bt-datatable table-bordered">
                        <tr>
                            <th width="70%" class="text-left">Sales</th>
                            <th width="30%" class="text-center">Amount</th>
                        </tr>
                        @php
                            $total_sales_amount = 0;

                            $sale_query = "select trial.business_id,trial.company_id,trial.chart_level,ca.chart_account_id,trial.chart_account_code,ca.chart_name, trial.balance
                                            from (    
                                                
                                                select distinct 4 chart_level, business_id, company_id , chart_account_code, sum(balance) balance
                                                from  tbl_tmp_trial    group by      business_id, company_id , chart_account_code  
                                            )  trial,
                                            tbl_acco_chart_account ca
                                            where  trial.chart_account_code = ca.chart_code(+) and trial.chart_account_code like '7-01-01%' order by trial.chart_account_code , trial.chart_level";
                            $sales = DB::select($sale_query);
                        @endphp
                            @foreach($sales as $sale)
                                @if($sale->chart_account_code == '7-01-01-0001')
                                    @php $sale->balance = $sale->balance*-1; @endphp 
                                @endif
                                <tr>
                                    <td>{{$sale->chart_account_code}} => {{$sale->chart_name}}</td>
                                    <td class="text-center">{{number_format($sale->balance,3)}}</td>
                                </tr>
                                @php $total_sales_amount += $sale->balance; @endphp
                            @endforeach
                        <tr class="sub_total">
                            <td class="rep-font-bold"> Total Sales</td>
                            <td class="text-center rep-font-bold">{{number_format($total_sales_amount,3)}}</td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="row row-block">
                <div class="col-lg-12">
                    <table width="100%" id="rep_sale_invoice_datatable" class="table bt-datatable table-bordered">
                        <tr>
                            <th width="70%" class="text-left">Cost Of Sales</th>
                            <th width="30%" class="text-center"></th>
                        </tr>
                        @php
                            $total_cost_sales_amount = 0;

                            $cost_query = "select trial.business_id,trial.company_id,trial.chart_level,ca.chart_account_id,trial.chart_account_code,ca.chart_name, trial.balance
                                            from (    
                                                
                                                select distinct 4 chart_level, business_id, company_id , chart_account_code, sum(balance) balance
                                                from  tbl_tmp_trial    group by      business_id, company_id , chart_account_code  
                                            )  trial,
                                            tbl_acco_chart_account ca
                                            where  trial.chart_account_code = ca.chart_code(+) and trial.chart_account_code like '8-01-01%' order by trial.chart_account_code , trial.chart_level";
                            $costs = DB::select($cost_query);
                        @endphp
                            @foreach($costs as $cost)
                                <tr>
                                    <td>{{$cost->chart_account_code}} => {{$cost->chart_name}}</td>
                                    <td class="text-center">{{number_format($cost->balance,3)}}</td>
                                </tr>
                                @php $total_cost_sales_amount += $cost->balance; @endphp
                            @endforeach
                        <tr class="sub_total">
                            <td class="rep-font-bold"> Total Cost Of Sales</td>
                            <td class="text-center rep-font-bold">{{number_format($total_cost_sales_amount,3)}}</td>
                        </tr>
                        @php $gross_profit = $total_sales_amount - $total_cost_sales_amount;  @endphp
                        <tr class="grand_total">
                            <td class="rep-font-bold">Gross Profit / (Loss):</td>
                            <td class="text-center rep-font-bold">{{number_format($gross_profit,3)}}</td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="row row-block">
                <div class="col-lg-12">
                    <table width="100%" id="rep_sale_invoice_datatable" class="table bt-datatable table-bordered">
                        <tr>
                            <th width="70%" class="text-left">Other Income</th>
                            <th width="30%" class="text-center"></th>
                        </tr>
                        @php
                            $total_income_amount = 0;

                            $incm_query = "select trial.business_id,trial.company_id,trial.chart_level,ca.chart_account_id,trial.chart_account_code,ca.chart_name, trial.balance
                                            from (    
                                                
                                                select distinct 4 chart_level, business_id, company_id , chart_account_code, sum(balance) balance
                                                from  tbl_tmp_trial    group by      business_id, company_id , chart_account_code  
                                            )  trial,
                                            tbl_acco_chart_account ca
                                            where  trial.chart_account_code = ca.chart_code(+) and trial.chart_account_code like '7-02-05%' order by trial.chart_account_code , trial.chart_level";
                            $income = DB::select($incm_query);
                        @endphp
                            @foreach($income as $incom)
                                <tr>
                                    <td>{{$incom->chart_account_code}} => {{$incom->chart_name}}</td>
                                    <td class="text-center">{{number_format($incom->balance,3)}}</td>
                                </tr>
                                @php $total_income_amount += $incom->balance; @endphp
                            @endforeach
                        <tr class="sub_total">
                            <td class="rep-font-bold"> Total Other Income</td>
                            <td class="text-center rep-font-bold">{{number_format($total_income_amount,3)}}</td>
                        </tr>
                        @php $gross_profit = $gross_profit + $total_income_amount;  @endphp
                        <tr class="grand_total">
                            <td class="rep-font-bold">Total:</td>
                            <td class="text-center rep-font-bold">{{number_format($gross_profit,3)}}</td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="row row-block">
                <div class="col-lg-12">
                    <table width="100%" id="rep_sale_invoice_datatable" class="table bt-datatable table-bordered">
                        <tr>
                            <th width="70%" class="text-left">Operating Expenses</th>
                            <th width="30%" class="text-center"></th>
                        </tr>
                        @php
                            $total_expense_amount = 0;

                            $exp_query = "select trial.business_id,trial.company_id,trial.chart_level,ca.chart_account_id,trial.chart_account_code,ca.chart_name, trial.balance
                                            from (    
                                                
                                                select distinct 4 chart_level, business_id, company_id , chart_account_code, sum(balance) balance
                                                from  tbl_tmp_trial    group by      business_id, company_id , chart_account_code  
                                            )  trial,
                                            tbl_acco_chart_account ca
                                            where  trial.chart_account_code = ca.chart_code(+) and trial.chart_account_code like '9-%' order by trial.chart_account_code , trial.chart_level";
                            $expenses = DB::select($exp_query);
                        @endphp
                            @foreach($expenses as $expense)
                                <tr>
                                    <td>{{$expense->chart_account_code}} => {{$expense->chart_name}}</td>
                                    <td class="text-center">{{number_format($expense->balance,3)}}</td>
                                </tr>
                                @php $total_expense_amount += $expense->balance; @endphp
                            @endforeach
                        <tr class="sub_total">
                            <td class="rep-font-bold"> Total Operating Expenses</td>
                            <td class="text-center rep-font-bold">{{number_format($total_expense_amount,3)}}</td>
                        </tr>
                        @php $gross_profit = $gross_profit - $total_expense_amount;  @endphp
                        <tr class="grand_total">
                            <td class="rep-font-bold">Net Profit / (Loss):</td>
                            <td class="text-center rep-font-bold">{{number_format($gross_profit,3)}}</td>
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



