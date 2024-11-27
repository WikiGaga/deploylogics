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
        .notes{
            border: 1px solid;
            padding: 3px;
            margin: 4px 0;
            border-bottom: 2px solid;
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
        .voucher_title{
            font-size: 12px;
            font-weight: 800;
            background: #d8d8d8;
        }
    </style>
@endsection
@permission($data['permission'])
@php
    $type = $data['type'];
    $heading = strtoupper($data['title']);
    $businessName = strtoupper(auth()->user()->business->business_short_name);
    $code= $data['current']->voucher_no;
    $date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->voucher_date))));
    $day_name = date('D', strtotime(trim(str_replace('/','-',$data['current']->voucher_date))));
    $time =  date('h:i A', strtotime(trim(str_replace('/','-',$data['current']->voucher_date))));
    $pay_rec_acc = $data['current']->accounts->chart_name;
    $currency = $data['currency']->currency_name;
    $narration = $data['current']->voucher_descrip;
    $saleman = $data['users']->name;
    $exchange_rate = $data['current']->voucher_exchange_rate;
    $notes = $data['current']->voucher_notes;
    $dtls = isset($data['dtl'])? $data['dtl'] :[];
@endphp
@section('title', $heading)
@section('content')
        <div class="thermal_print_body">
            <div class="main_heading voucher_title">
                {{ $heading }} (IPV)
                {{ auth()->user()->branch->branch_name }}
            </div>
            <div class="main_heading cls_shift">
                Code: {{$code}}
            </div>
            <div class="basic_info">
                <div>
                    <span>Date:</span><span style=" display: inline-block;margin-left: 20px; ">{{$date}} {{$day_name}} {{$time}}</span>
                </div>
                <div>
                    <span>Salesman:</span><span style=" display: inline-block;margin-left: 35px; ">{{$saleman}}</span>
                </div>
            </div>
            <div>
                <table width="100%" class="pos_document_activity">
                    <thead>
                    <tr>
                        <th>Account</th>
                        <th>Narration</th>
                        <th>Debit</th>
                        <th>Credit</th>
                    </tr>
                    </thead>
                    <tbody>
                        {{-- <tr>
                            <td colspan="3" class="voucher_title">Internal Payment Voucher (IPV)</td>
                        </tr> --}}
                        @php
                        $totDebit = 0;
                        $totCredit = 0;
                        @endphp
                        @foreach ($dtls as $dtl)
                            @php
                                $bgt_dsc = '';
                                $budget =\App\Models\TblAccBudget::where('budget_id',$dtl->budget_id)->where('budget_branch_id',$dtl->budget_branch_id)->first();
                                if($budget != Null){
                                    $bgt_dsc = $budget->budget_budgetart_position;
                                }
                                $date = date('d-m-Y', strtotime(trim(str_replace('/','-',$dtl->voucher_mode_date))));
                                if($date == '01-01-1970'){
                                    $date = '';
                                }
                                if($dtl->voucher_payment_mode == 'atm'){
                                    $payment_mode = 'ATM Transfer';
                                }else if($dtl->voucher_payment_mode =='cheque'){
                                    $payment_mode = 'Cheque';
                                }else if($dtl->voucher_payment_mode =='online'){
                                    $payment_mode = 'Online Payment';
                                }else{
                                    $payment_mode = '';
                                }
                                $totDebit += $dtl->voucher_debit;
                                $totCredit += $dtl->voucher_credit;
                            @endphp
                            <tr>
                                <td width="40%">
                                    <div>{{isset($dtl->accounts->chart_code)?$dtl->accounts->chart_code:$dtl->chart_code}}</div>
                                    <div style="margin-left: 10px;">{{isset($dtl->accounts->chart_name)?$dtl->accounts->chart_name:$dtl->chart_name}}</div>
                                </td>
                                <td width="20%">{{ $dtl->voucher_descrip }}</td>
                                <td width="20%" class="text-right">{{number_format($dtl->voucher_debit)}}</td>
                                <td width="20%" class="text-right">{{number_format($dtl->voucher_credit)}}</td>
                            </tr>
                        @endforeach

                        <tr>
                            <td colspan="2"><b>Total</b></td>
                            <td class="text-right"><b>{{number_format($totDebit)}}</b></td>
                            <td class="text-right"><b>{{number_format($totCredit)}}</b></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            @if(isset($notes))
                <div class="notes">
                    <b>Notes:</b>
                    <p style="margin-left: 15px;">{{ $notes }}</p>
                </div>
            @endif
            <table width="100%" style="text-align:center;margin-top:10px; border-top: 2px solid #000;">
                <tbody>
                <tr>
                    <td>Prepared By : <span>{{auth()->user()->name}}</span></td>
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