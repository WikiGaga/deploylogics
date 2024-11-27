@extends('prints.partial.thermal_template')
@section('pageCSS')
    <style>
        body{
            font-size: 10px;
        }
        .thermal_print_body{
            width: 275px !important;
            margin:0 auto;
        }
        .main_heading{
            text-align: center;
            font-weight: 800;
            border: 1px solid;
            padding: 3px;
            margin: 4px 0;
            border-bottom: 2px solid;
            font-size: 12px;
        }
        .cls_shift{
            font-size:14px;
        }
        .basic_info{
            font-weight: 800;
            border: 1px dashed;
            font-size: 11px;
            padding: 0px 3px;
        }
        .basic_info>div {
            margin: 6px 0px;
            border-bottom: 1px dashed;
        }
        .basic_info>div:last-child {
            border-bottom: 0px dashed;
        }

        .pos_document_activity td:first-child{
            border-left: 1px dotted;
        }
        .pos_document_activity td{
            border-top: 1px dotted;
            border-right: 1px dotted;
            padding-top: 3px;
            padding-bottom: 3px;
        }
        .pos_document_activity tr:last-child td{
            border-bottom: 1px dotted;
        }

        .cash_received_summary td:first-child{
            border-left: 1px dotted;
        }
        .cash_received_summary td{
            border-top: 1px dotted;
            padding-top: 3px;
            padding-bottom: 3px;
        }
        .cash_received_summary tr:last-child td{
            border-bottom: 1px dotted;
        }
        .cash_received_summary tr td:last-child{
            border-right: 1px dotted;
        }
        .total_amount{
            font-size: 14px;
            font-weight: 800;
        }
        .voucher_title{
            font-size: 12px;
            font-weight: 800;
            background: #d8d8d8;
        }
        .br-none{
            border-right: none !important;
        }
    </style>
@endsection
@permission($data['permission'])
@php
   // dd($data);
    $type = $data['type'];
    $heading = strtoupper($data['title']);
    $shift= isset($data['shift']->shift_name)?$data['shift']->shift_name:'';
    $date = date('d-M-Y', strtotime(trim(str_replace('/','-',$data['current']->day_date))));
    $day_name = date('D', strtotime(trim(str_replace('/','-',$data['current']->day_date))));
    $from_time =  date('h:i A', strtotime(trim(str_replace('/','-',$data['current']->day_date))));
    $to_day_name = date('D', strtotime(trim(str_replace('/','-',$data['current']->day_date))));
    $to_date = date('d-M-Y', strtotime(trim(str_replace('/','-',$data['current']->to_date))));
    $to_time =  date('h:i A', strtotime(trim(str_replace('/','-',$data['current']->to_date))));
    $user_name= isset($data['users']->name)?$data['users']->name:'';

    $day_calc = [];
    $day_pos = [];
    $day_pad = [];
    $new_dtls = isset($data['current']->dtl)? $data['current']->dtl:[];
    foreach ($new_dtls as $new_dtl){
        if($new_dtl['day_case_type'] == 'day_calc'){
            $day_calc = $new_dtl;
        }
        if($new_dtl['day_case_type'] == 'day_payment'){
            $day_pad[] = $new_dtl;
        }
        if($new_dtl['day_case_type'] == 'day_pos'){
            $day_pos[] = $new_dtl;
        }
    }

    $dtls = isset($data['dtl'])? $data['dtl']:[];
@endphp
@php
    $date_filter = " and ( created_at between to_date('".date('Y-m-d',strtotime($date)).' '.$from_time."','yyyy/mm/dd HH:MI AM')";
    $date_filter .= " AND to_date('".date('Y-m-d',strtotime($to_date)).' '.$to_time."','yyyy/mm/dd HH:MI AM') )";

    $where = $date_filter;

    $where .= " and sales_sales_man = ".$data['current']->saleman_id;
    $where .= " and business_id = ".auth()->user()->business_id;
    $where .= " and company_id = ".auth()->user()->company_id;
    $where .= " and branch_id = ".auth()->user()->branch_id;
    $qry = "select abc.terminal_id,abc.terminal_name,abc.merchant_id,m.MERCHANT_NAME,count(SALES_ID)as no_of_documents,sum(amount)as amount from (
                select DISTINCT
                BRANCH_ID,
                BRANCH_NAME,
                SALES_TYPE DOCUMENT_TYPE,
                SALES_ID,
                MERCHANT_ID,
                terminal_id,
                terminal_name,
                VISA_AMOUNT  amount
                FROM  VW_SALE_SALES_INVOICE
                WHERE SALES_TYPE = 'POS' AND  NVL(VISA_AMOUNT,0) <> 0
                $where
                ) abc
                       join tbl_defi_merchant m on m.MERCHANT_ID = abc.MERCHANT_ID group  by abc.MERCHANT_ID,m.MERCHANT_NAME,abc.terminal_id,abc.terminal_name";
   //dd($qry);
                       $merchants_data = \Illuminate\Support\Facades\DB::select($qry);
    $terminal = count($merchants_data) != 0 ? current($merchants_data):"";


                $qry = "select DISTINCT BRANCH_ID, BRANCH_NAME, SALES_DATE,   DOCUMENT_NAME, DOCUMENT_TYPE, sum(SALES_DTL_AMOUNT)  AMOUNT, count(distinct SALES_ID) no_of_documents, sum(gst_amount) gst_amount
                    from (
                     select DISTINCT    BRANCH_ID, BRANCH_NAME, SALES_DATE, 'Sales Invoice' DOCUMENT_NAME, SALES_TYPE DOCUMENT_TYPE, SALES_ID, sum(SALES_DTL_AMOUNT ) SALES_DTL_AMOUNT,   sum(nvl(SALES_DTL_VAT_AMOUNT,0)) gst_amount
                    FROM  VW_SALE_SALES_INVOICE WHERE SALES_TYPE = 'POS'
                    $where
                     group by  BRANCH_ID, BRANCH_NAME, SALES_DATE, SALES_TYPE , SALES_ID
                    UNION ALL
                    select DISTINCT BRANCH_ID, BRANCH_NAME, SALES_DATE, 'Sales Return' DOCUMENT_NAME, SALES_TYPE DOCUMENT_TYPE, SALES_ID,  sum(ABS(SALES_DTL_AMOUNT)) * -1     SALES_DTL_AMOUNT,  sum(nvl(SALES_DTL_VAT_AMOUNT,0)) * -1 gst_amount
                    FROM  VW_SALE_SALES_INVOICE WHERE SALES_TYPE = 'RPOS'
                     $where
                       group by  BRANCH_ID, BRANCH_NAME, SALES_DATE, SALES_TYPE , SALES_ID
                    ) gaga group by BRANCH_ID, BRANCH_NAME, SALES_DATE, DOCUMENT_NAME, DOCUMENT_TYPE order by  SALES_DATE , DOCUMENT_TYPE";

                $gst_tax_sum = \Illuminate\Support\Facades\DB::select($qry);


@endphp
@section('title', $heading)
@section('content')
        <div class="thermal_print_body">
            <div class="main_heading">
                POS Session Activity Report
            </div>
            <div class="main_heading cls_shift">
                <div>Counter</div>
                <div>{{isset($terminal->terminal_name)?$terminal->terminal_name:""}}</div>
            </div>
            <div class="main_heading cls_shift">
                {{$shift}}
            </div>
            <div class="basic_info">
                <div>
                    <span>Opening Date:</span><span style=" display: inline-block;margin-left: 10px; ">{{$date}} {{$day_name}} {{$from_time}}</span>
                </div>
                <div>
                    <span>Closing Date:</span><span style=" display: inline-block;margin-left: 17px; ">{{$to_date}} {{$to_day_name}} {{$to_time}}</span>
                </div>
                <div>
                    <span>Salesman:</span><span style=" display: inline-block;margin-left: 35px; ">{{$user_name}}</span>
                </div>
            </div>
            <div>
                <div class="main_heading">
                    POS DOCUMENT ACTIVITY
                </div>
                <table width="100%" class="pos_document_activity">
                    <thead>
                    <tr>
                        <th>Document Type</th>
                        <th># of Doc.</th>
                        <th>Amount</th>
                        <th>Discount</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($day_pos as $pos_row)
                        <tr>
                            <td>{{$pos_row['document_name']}}</td>
                            <td class="text-right">{{$pos_row['no_of_documents']}}</td>
                            <td class="text-right">{{number_format($pos_row['total_amount'])}}</td>
                            <td class="text-right">{{number_format($pos_row['total_discount'])}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div>
                <div class="main_heading">
                    CASH RECEIVED SUMMARY
                </div>
                @php
                    $opening_amount = 0;
                    $cash_amount = 0;

                    $sale_return_amount = 0;
                    $paid_to_office = 0;
                @endphp
                @foreach ($day_pad as $pad_row)
                    @if($pad_row['payment_mode'] == 'Cash')
                        @php
                            $cash_amount = $pad_row['in_flow'];
                            $sale_return_amount = $pad_row['out_flow'];
                        @endphp
                    @endif
                    @if($pad_row['payment_mode'] == 'Internal Voucher')
                        @php
                            $paid_to_office = $pad_row['out_flow'];
                        @endphp
                    @endif
                @endforeach
                @php
                    $total_amount = $opening_amount + $cash_amount;
                @endphp
                <table width="100%" class="cash_received_summary">
                    <tbody>
                    <tr>
                        <td>Opening Amount</td>
                        <td class="text-right">{{number_format($opening_amount)}}</td>
                    </tr>
                    <tr>
                        <td>Sale -Cash</td>
                        <td class="text-right">{{number_format($cash_amount)}}</td>
                    </tr>
                    <tr>
                        <td>Total Amount</td>
                        <td class="text-right total_amount">{{number_format($total_amount)}}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div>
                <div class="main_heading">
                    CASH PAID SUMMARY
                </div>
                @php
                    $paid_total_amount = $sale_return_amount + $paid_to_office;
                @endphp
                <table width="100%" class="cash_received_summary">
                    <tbody>
                    <tr>
                        <td>Cash Back/Sale Return</td>
                        <td class="text-right">{{number_format($sale_return_amount)}}</td>
                    </tr>
                    <tr>
                        <td>Paid To Office</td>
                        <td class="text-right">{{number_format($paid_to_office)}}</td>
                    </tr>
                    <tr>
                        <td>Total Amount</td>
                        <td class="text-right total_amount">{{number_format($paid_total_amount)}}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div>
                <div class="main_heading">
                    CURRENCY DENOMINATION
                </div>
                @php
                    $total_denomination_amount  = 0;
                    $as_per_system_amount =  $total_amount - $paid_total_amount
                @endphp
                <table width="100%" class="cash_received_summary">
                    <tbody>
                        @foreach ($dtls as $dtl)
                            <tr>
                                <td class="text-right" width="33.33%">{{$dtl->denomination_name}}/=</td>
                                <td class="text-right" width="33.33%">{{isset($dtl->day_qty)?$dtl->day_qty:'0'}}</td>
                                <td class="text-right" width="33.33%">{{isset($dtl->day_amount)?number_format($dtl->day_amount):'0'}}</td>
                            </tr>
                            @php
                                $total_denomination_amount += isset($dtl->day_amount)?$dtl->day_amount:0;
                            @endphp
                        @endforeach
                        @php
                            $short_amount = $total_denomination_amount - $as_per_system_amount
                        @endphp
                        <tr>
                            <td>Total Amount</td>
                            <td></td>
                            <td class="text-right total_amount">{{number_format($total_denomination_amount)}}</td>
                        </tr>
                        <tr>
                            <td>Short/Excess Amount</td>
                            <td></td>
                            <td class="text-right total_amount">{{number_format($short_amount)}}</td>
                        </tr>
                        <tr>
                            <td>As Per Software</td>
                            <td></td>
                            <td class="text-right total_amount">{{number_format($as_per_system_amount)}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div>
                <div class="main_heading">
                    CREDIT CARD MERCHANT SUMMARY
                </div>
                @php
                    $merchant_total_amount = 0;
                    $sum_no_of_documents = 0;
                @endphp
                <table width="100%" class="pos_document_activity">
                    <thead>
                    <tr>
                        <th>Merchant Name</th>
                        <th># of Doc.</th>
                        <th>Amount</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($merchants_data as $merchent)
                        <tr>
                            <td>{{$merchent->merchant_name}}</td>
                            <td class="text-right">{{$merchent->no_of_documents}}</td>
                            <td class="text-right">{{number_format($merchent->amount)}}</td>
                        </tr>
                        @php
                            $merchant_total_amount += $merchent->amount;
                            $sum_no_of_documents += $merchent->no_of_documents;
                        @endphp
                    @endforeach

                    <tr>
                        <td><b>Total</b></td>
                        <td class="text-right"><b>{{number_format($sum_no_of_documents)}}</b></td>
                        <td class="text-right"><b>{{number_format($merchant_total_amount)}}</b></td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div>
                <div class="main_heading">
                    POS ACTIVITY DETAIL
                </div>
                @php
                    $date_filter = " and ( VOUCHER_DATE between to_date('".date('Y-m-d',strtotime($date))."','yyyy/mm/dd')";
                    $date_filter .= " AND to_date('".date('Y-m-d',strtotime($to_date))."','yyyy/mm/dd') )";

                    $where = $date_filter;

                    $where .= " and saleman_id = ".$data['current']->saleman_id;
                    $where .= " and business_id = ".auth()->user()->business_id;
                    $where .= " and company_id = ".auth()->user()->company_id;
                    $where .= " and branch_id = ".auth()->user()->branch_id;

                    $qry = "select DISTINCT CHART_NAME,VOUCHER_NO,VOUCHER_DATE,VOUCHER_CREDIT
                        FROM  VW_ACCO_VOUCHER
                        WHERE lower(VOUCHER_TYPE) = 'ipv' AND NVL(VOUCHER_CREDIT,0) <> 0
                        $where order by VOUCHER_DATE";
                    $internal_voucher = \Illuminate\Support\Facades\DB::select($qry);
                    $ipv_total_amount = 0
                @endphp
                <table width="100%" class="pos_document_activity">
                    <thead>
                    <tr>
                        <th>Date</th>
                        <th>Voucher #</th>
                        <th>Amount</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td colspan="3" class="voucher_title">Internal Payment Voucher (IPV)</td>
                    </tr>
                    @foreach ($internal_voucher as $ipv)
                        <tr>
                            <td>{{date('d-m-Y',strtotime($ipv->voucher_date))}}</td>
                            <td>
                                <div>{{$ipv->voucher_no}}</div>
                                <div>{{$ipv->chart_name}}</div>
                            </td>
                            <td class="text-right">{{number_format($ipv->voucher_credit)}}</td>
                        </tr>
                        @php
                            $ipv_total_amount += $ipv->voucher_credit;
                        @endphp
                    @endforeach

                    <tr>
                        <td colspan="2"><b>(IPV) Total</b></td>
                        <td class="text-right"><b>{{number_format($ipv_total_amount)}}</b></td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div>
                <div class="main_heading">
                    DISCOUNT SUMMARY
                </div>
                <table width="100%" class="pos_document_activity">
                    <thead>
                    <tr>
                        <th>Communicate Type</th>
                        <th>Sale</th>
                        <th>Discount</th>
                    </tr>
                    </thead>
                    <tbody>
                    @php
                        $disc_summ_discount = 0;
                        $disc_summ_amount = 0;
                    @endphp
                    @foreach ($day_pos as $pos_row)
                        @if($pos_row['document_name'] == 'Sales Invoice')
                            @php
                                $disc_summ_discount = $pos_row['total_discount'];
                                $disc_summ_amount = $pos_row['total_amount'] + $disc_summ_discount;
                            @endphp
                            <tr>
                                <td>None</td>
                                <td class="text-right">{{number_format($disc_summ_amount)}}</td>
                                <td class="text-right">{{number_format($disc_summ_discount)}}</td>
                            </tr>
                        @endif
                    @endforeach
                    <tr>
                        <td class="br-none">Total Amount</td>
                        <td colspan="2" class="text-right total_amount">{{number_format($disc_summ_amount)}}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div>
                <div class="main_heading">
                    TAX SUMMARY
                </div>
                <table width="100%" class="pos_document_activity">
                    <thead>
                    <tr>
                        <th>Document Type</th>
                        <th># of Doc.</th>
                        <th>Amount</th>
                        <th>GST</th>
                    </tr>
                    </thead>
                    <tbody>
                    @php
                        $tax_summ_amount = 0;
                        $tax_summ_gst = 0;
                    @endphp
                    @foreach ($gst_tax_sum as $gst_tax_sum_item)
                        @php
                            $exclusive_tax = $gst_tax_sum_item->amount - $gst_tax_sum_item->gst_amount;
                        @endphp
                        <tr>
                            <td>{{$gst_tax_sum_item->document_name}}</td>
                            <td class="text-right">{{number_format($gst_tax_sum_item->no_of_documents)}}</td>
                            <td class="text-right">{{number_format($exclusive_tax)}}</td>
                            <td class="text-right">{{number_format($gst_tax_sum_item->gst_amount)}}</td>
                        </tr>
                        @php
                            $tax_summ_amount += $exclusive_tax;
                            $tax_summ_gst += $gst_tax_sum_item->gst_amount;
                        @endphp
                    @endforeach
                    <tr>
                        <td colspan="2" class="br-none">Total</td>
                        <td class="text-right total_amount">{{number_format($tax_summ_amount)}}</td>
                        <td class="text-right total_amount">{{number_format($tax_summ_gst)}}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <table width="100%" style="text-align:center;margin-top:10px; border-top: 2px solid #000;">
                <tbody>
                <tr>
                    <td>Operator : <span>{{auth()->user()->name}}</span></td>
                </tr>
                <tr>
                    <td>Software Developed By: <b>Royalsoft</b></td>
                </tr>
                <tr>
                    <td>Print Date & Time: {{date("d-m-Y h:i:s")}}</td>
                </tr>
                </tbody>
            </table>
        </div>
@endsection
@endpermission
