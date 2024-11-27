@extends('layouts.report')
@section('title', 'Invoice Wise Sale Report')

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
                @if(isset($data['sale_types_multiple']) && count($data['sale_types_multiple']) != 0)
                    <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;">Sales Type:</span>
                        @foreach($data['sale_types_multiple'] as $sales_type)
                            <span style="color: #5578eb;">{{" ".$sales_type." "}}</span>
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
                @if(isset($data['marchant_id']) && !empty($data['marchant_id']))
                    @php 
                        $chartDtl = \Illuminate\Support\Facades\DB::table('tbl_acco_chart_account')->where('chart_account_id',$data['marchant_id'])->first();
                    @endphp
                    <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;">Merchant:</span>
                        <span style="color: #5578eb;">{{" ".$chartDtl->chart_name." "}}</span>
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
                        $sales_type = "";
                        if(count($data['sale_types_multiple']) != 0){
                            $sales_type = " and lower(sales_type) in (";
                            foreach($data['sale_types_multiple'] as $stype){
                                $sales_type .= "'".strtolower($stype)."',";
                            }
                            $cc = rtrim($sales_type, ", ");
                            $sales_type = $cc.") ";
                        }
                        $whereUser = "";
                        if(count($data['users']) != 0){
                            $whereUser = " and SALES_SALES_MAN in (";
                            foreach($data['users'] as $user){
                                $whereUser .= $user.",";
                            }
                            $cc = rtrim($whereUser, ", ");
                            $whereUser = $cc.") ";
                        }

                        if(isset($data['marchant_id']) && !empty($data['marchant_id']) != 0){
                            $where .= " and merchant_id = ".$chartDtl->chart_account_id."";
                        }

                        $qry = "SELECT DISTINCT BRANCH_ID,
                                BRANCH_NAME,
                                TERMINAL_NAME COUNTER,
                                SALES_TYPE,
                                SALES_DATE,
                                SALES_ID,
                                CUSTOMER_CREDIT_CARD_NO,
                                CUSTOMER_NAME,
                                SALES_SALES_MAN_NAME,
                                MERCHANT_ID,
                                SALES_CODE,
                                MAX(CREATED_AT) CREATED_AT,
                                MAX(CASH_AMOUNT)  CASH_AMOUNT,
                                MAX(VISA_AMOUNT)   VISA_AMOUNT ,
                                MAX(LOYALTY_AMOUNT) LOYALTY_AMOUNT ,
                                SUM(NVL(SALES_DTL_AMOUNT,0)) + MAX(NVL(FBR_CHARGES,0))   GROSS_AMT ,
                                SUM(NVL(SALES_DTL_DISC_AMOUNT,0)) +
                                SUM(NVL(EXT_DISC_AMOUNT,0))  DISC_AMT ,
                                SUM(NVL(SALES_DTL_NET_AMOUNT,0)) + MAX(NVL(FBR_CHARGES,0)) NET_AMOUNT
                                FROM VW_SALE_SALES_INVOICE
                                where branch_id in (".implode(",",$data['branch_ids']).")
                                and (created_at between to_date ('".$data['date_time_from']."', 'yyyy/mm/dd HH24:MI') and to_date ('".$data['date_time_to']."', 'yyyy/mm/dd HH24:MI') )
                                $sales_type $whereUser
                                $where 
                                GROUP BY branch_id , branch_name , terminal_name , 
                                sales_type , sales_date , sales_id , sales_sales_man_name ,
                                 merchant_id , sales_code,CUSTOMER_CREDIT_CARD_NO,CUSTOMER_NAME
                                order by SALES_SALES_MAN_NAME,CREATED_AT";
                       //dd($qry);
                        $getdata = \Illuminate\Support\Facades\DB::select($qry);
                      //   dd($getdata);
                        $list = [];
                        foreach ($getdata as $row){
                            $list[$row->branch_name][$row->sales_date][] = $row;
                        }
                       //  dd($list);
                        @endphp
                        @php
                            $si_grand_total_amount = 0;
                        @endphp
                        <table width="100%" id="rep_sale_invoice_datatable" class="static_report_table table bt-datatable table-bordered">
                            <tr class="sticky-header">
                                <th class="text-center">S.#</th>
                                <th class="text-center">Inv. No</th>
                                <th class="text-center">Time</th>
                                <th class="text-center">Entry User</th>
                                <th class="text-center">Sale Type</th>
                                <th class="text-center">Client Name</th>
                                <th class="text-center">Credit Card #</th>
                                <th class="text-center">Cash</th>
                                <th class="text-center">Credit Card</th>
                                <th class="text-center">Loyalty Points</th>
                                <th class="text-center">Gross Amount</th>
                                <th class="text-center">Discount</th>
                                <th class="text-center">Invoice Total</th>
                            </tr>
                            @php
                                $grand_Cash = 0;
                                $grand_CreditCard = 0;
                                $grand_LoyaltyPoints = 0;
                                $grand_GrossAmount = 0;
                                $grand_Discount = 0;
                                $grand_InvoiceTotal = 0;
                            @endphp
                            @foreach($list as $si_keys=>$si_row)
                                @php
                                    $sub_total_amount = 0;
                                    $ki = 1;
                                    // dump($si_keys);
                                @endphp
                                <tr>
                                    <td colspan="13"><b>Company Branch: {{strtoupper($si_keys)}}</b></td>
                                </tr>
                                @foreach($si_row as $sit_k=>$sit_detail)
                                    @php
                                        $date_Cash = 0;
                                        $date_CreditCard = 0;
                                        $date_LoyaltyPoints = 0;
                                        $date_GrossAmount = 0;
                                        $date_Discount = 0;
                                        $date_InvoiceTotal = 0;
                                    @endphp
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td colspan="11"><b>Date: {{date('d-m-Y', strtotime($sit_k))}}</b></td>
                                    </tr>
                                    @foreach($sit_detail as $sd_k=>$si_detail)
                                        <tr>
                                            <td>{{$ki}}</td>
                                            <td class="text-right">{{$si_detail->sales_code}}</td>
                                            <td>{{date('H:i:s', strtotime($si_detail->created_at))}}</td>
                                            <td>{{$si_detail->sales_sales_man_name}}</td>
                                            <td>{{strtoupper($si_detail->sales_type)}}</td>
                                            <td>{{strtoupper($si_detail->customer_name)}}</td>
                                            <td>{{strtoupper($si_detail->customer_credit_card_no)}}</td>
                                            @if(strtoupper($si_detail->sales_type) == 'RPOS')
                                                @php $cash_amount = -1 * abs($si_detail->cash_amount); @endphp
                                                <td class="text-right">{{number_format($cash_amount,3)}}</td>
                                            @else
                                                @php $cash_amount = $si_detail->cash_amount; @endphp
                                                <td class="text-right">{{number_format($cash_amount,3)}}</td>
                                            @endif
                                            @if(strtoupper($si_detail->sales_type) == 'RPOS')
                                                @php $visa_amount = -1 * abs($si_detail->visa_amount); @endphp
                                                <td class="text-right">{{number_format($cash_amount,3)}}</td>
                                            @else
                                                @php $visa_amount = $si_detail->visa_amount; @endphp
                                                <td class="text-right">{{number_format($visa_amount,3)}}</td>
                                            @endif
                                            @if(strtoupper($si_detail->sales_type) == 'RPOS')
                                                @php $loyalty_amount = -1 * abs($si_detail->loyalty_amount); @endphp
                                                <td class="text-right">{{number_format($loyalty_amount,3)}}</td>
                                            @else
                                                @php $loyalty_amount = $si_detail->loyalty_amount; @endphp
                                                <td class="text-right">{{number_format($loyalty_amount,3)}}</td>
                                            @endif
                                            @if(strtoupper($si_detail->sales_type) == 'RPOS')
                                                @php $gross_amt = -1 * abs($si_detail->gross_amt); @endphp
                                                <td class="text-right">{{number_format($gross_amt,3)}}</td>
                                            @else
                                                @php $gross_amt = $si_detail->gross_amt; @endphp
                                                <td class="text-right">{{number_format($gross_amt,3)}}</td>
                                            @endif
                                            @if(strtoupper($si_detail->sales_type) == 'RPOS')
                                                @php $disc_amt = -1 * abs($si_detail->disc_amt); @endphp
                                                <td class="text-right">{{number_format($disc_amt,3)}}</td>
                                            @else
                                                @php $disc_amt = $si_detail->disc_amt; @endphp
                                                <td class="text-right">{{number_format($disc_amt,3)}}</td>
                                            @endif
                                            @if(strtoupper($si_detail->sales_type) == 'RPOS')
                                                @php $net_amount = -1 * abs($si_detail->net_amount); @endphp
                                                <td class="text-right">{{number_format($net_amount,3)}}</td>
                                            @else
                                                @php $net_amount = $si_detail->net_amount; @endphp
                                                <td class="text-right">{{number_format($net_amount,3)}}</td>
                                            @endif
                                        </tr>
                                        @php
                                            $ki += 1;
                                            $date_Cash += $cash_amount;
                                            $date_CreditCard += $visa_amount;
                                            $date_LoyaltyPoints += $loyalty_amount;
                                            $date_GrossAmount += $gross_amt;
                                            $date_Discount += $disc_amt;
                                            $date_InvoiceTotal += $net_amount;
                                        @endphp
                                    @endforeach
                                    <tr class="inner_total">
                                        <td colspan="7">Date wise Total: {{date('d-m-Y', strtotime($sit_k))}}</td>
                                        <td class="text-right">{{number_format($date_Cash,3)}}</td>
                                        <td class="text-right">{{number_format($date_CreditCard,3)}}</td>
                                        <td class="text-right">{{number_format($date_LoyaltyPoints,3)}}</td>
                                        <td class="text-right">{{number_format($date_GrossAmount,3)}}</td>
                                        <td class="text-right">{{number_format($date_Discount,3)}}</td>
                                        <td class="text-right">{{number_format($date_InvoiceTotal,3)}}</td>
                                    </tr>
                                    @php
                                        $grand_Cash += $date_Cash;
                                        $grand_CreditCard += $date_CreditCard;
                                        $grand_LoyaltyPoints += $date_LoyaltyPoints;
                                        $grand_GrossAmount += $date_GrossAmount;
                                        $grand_Discount += $date_Discount;
                                        $grand_InvoiceTotal += $date_InvoiceTotal;
                                    @endphp
                                @endforeach
                            @endforeach
                            <tr class="outer_total">
                                <td colspan="7">Grand Total:</td>
                                <td class="text-right">{{number_format($grand_Cash,3)}}</td>
                                <td class="text-right">{{number_format($grand_CreditCard,3)}}</td>
                                <td class="text-right">{{number_format($grand_LoyaltyPoints,3)}}</td>
                                <td class="text-right">{{number_format($grand_GrossAmount,3)}}</td>
                                <td class="text-right">{{number_format($grand_Discount,3)}}</td>
                                <td class="text-right">{{number_format($grand_InvoiceTotal,3)}}</td>
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
                $("#rep_sale_invoice_datatable").table2excel({
                    // exclude: ".noExport",
                    filename: "report.xls",
                });
            });
        </script>
    @endif
@endsection
