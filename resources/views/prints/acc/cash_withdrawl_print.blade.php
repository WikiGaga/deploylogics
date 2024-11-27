@extends('prints.partial.template')
@section('pageCSS')

@endsection
@permission($data['permission'])
@php
    $type = $data['type'];
    $heading = strtoupper($data['title']);
    $businessName = strtoupper(auth()->user()->business->business_short_name);
    $code= $data['current']->voucher_no;
    $date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->voucher_date))));
    $pay_rec_acc = $data['current']->accounts->chart_name;
    $currency = $data['currency']->currency_name;
    $payment_mode = $data['current']->voucher_payment_mode;
    $mode_no = $data['current']->voucher_mode_no;
    $saleman = $data['users']->name;
    $exchange_rate = $data['current']->voucher_exchange_rate;
    $notes = $data['current']->voucher_notes;
    $voucher_user_id = $data['current']->voucher_user_id;
    $user = \App\Models\User::where('id',$voucher_user_id)->first();
    if(isset($user->name)){
        $entry_user_name = $user->name;
    }
    $dtls = isset($data['dtl'])? $data['dtl'] :[];
    $add_voucher_payment_mode = false;
@endphp
@section('title', $heading)
@section('page_heading', $heading)
@section('content')
    {{--form content--}}
    <table class="tableData topHeaderInfo">
        <tr>
            <td width="33.33%">
                <div>
                    <span class="heading heading-block">Document No :</span>
                    <span class="normal normal-block">{{isset($code)?$code:''}}</span>
                </div>
            </td>
            <td width="33.33%"></td>
            <td width="33.33%">
                <div>
                    <span class="heading heading-block">Document Date :</span>
                    <span class="normal normal-block">{{isset($date)?$date:''}}</span>
                </div>
            </td>
        </tr>
        <tr>
            <td width="33.33%">
                <div>
                    <span class="heading heading-block">Currency :</span>
                    <span class="normal normal-block">{{isset($currency)?$currency:''}}</span>
                </div>
            </td>
            <td width="33.33%"></td>
            <td width="33.33%">
                <div>
                    <span class="heading heading-block">Exchange Rate:</span>
                    <span class="normal normal-block">{{isset($exchange_rate)?$exchange_rate:''}}</span>
                </div>
            </td>
        </tr>
    </table>
    {{--table content--}}
    <br>
    <table  class="tabData" >
        <thead>
            <tr>
                <th width="7%" class="dtl-head topHeaderInfo">Sr No</th>
                <th width="15%" class="dtl-head topHeaderInfo">Account Code</th>
                <th width="30%" class="dtl-head alignleft topHeaderInfo">Account Name</th>
                <th width="9%" class="dtl-head topHeaderInfo">Debit</th>
                <th width="9%" class="dtl-head topHeaderInfo">Credit</th>
            </tr>
        </thead>
        <tbody>
        @php
            $totDebit = 0;
            $totCredit = 0;
        @endphp
        @if(isset($dtls))
            @foreach($dtls as $row)
                @php
                    $totDebit += $row->voucher_debit;
                    $totCredit += $row->voucher_credit;
                @endphp
                @if(!in_array($row->voucher_payment_mode,["",'null',null]))
                    @php
                        $add_voucher_payment_mode = true;
                    @endphp
                @endif
                <tr>
                    <td class="dtl-contents aligncenter">{{ $loop->iteration }}</td>
                    <td class="dtl-contents aligncenter">{{isset($row->accounts->chart_code)?$row->accounts->chart_code:$row->chart_code}}</td>
                    <td class="dtl-contents alignleft">{{isset($row->accounts->chart_name)?$row->accounts->chart_name:$row->chart_name}}<br><span style="font-size:10px; padding-left: 25px;">{{" ".$row->voucher_descrip}}</span></td>
                    <td class="dtl-contents alignright">{{($row->voucher_debit!=0)?number_format($row->voucher_debit,3):""}}</td>
                    <td class="dtl-contents alignright">{{($row->voucher_credit!=0)?number_format($row->voucher_credit,3):""}}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="3" class="dtl-head alignleft">{{\App\Library\Utilities::AmountWords($totDebit)}}</td>
                <td class="dtl-head alignright">{{number_format($totDebit,3)}}</td>
                <td class="dtl-head alignright">{{number_format($totCredit,3)}}</td>
            </tr>
        @endif
        </tbody>
    </table><br>
    @if($add_voucher_payment_mode)
        <div class="topHeaderInfo" style="font-weight: 900; width: 90px; margin-top:-5px;">Cheque Details</div>
        <table class="tabData">
            <thead>
                <tr>
                    <th width="5%" class="dtl-head topHeaderInfo">Sr No</th>
                    <th width="10%" class="dtl-head topHeaderInfo alignleft">Cheque No</th>
                    <th width="10%" class="dtl-head topHeaderInfo alignleft">Date</th>
                    <th width="10%" class="dtl-head topHeaderInfo alignleft">Amount</th>
                    <th width="10%" class="dtl-head topHeaderInfo alignleft">Cheque Type</th>
                    <th width="20%" class="dtl-head topHeaderInfo alignleft">Bank</th>
                    <th width="20%" class="dtl-head topHeaderInfo alignleft">Payee Title</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $sumAmount = 0;
                    $sr = 0;
                @endphp
                @if(isset($dtls))
                    @foreach($dtls as $row)
                        @if($row->voucher_credit != 0 && !in_array($row->voucher_payment_mode,["",'null',null]))
                            @php
                                $sumAmount += $row->voucher_credit;
                                $sr += 1;
                            @endphp
                            <tr>
                                <td class="dtl-contents aligncenter">{{ $sr }}</td>
                                <td class="dtl-contents alignleft">{{$row->voucher_mode_no}}</td>
                                <td class="dtl-contents alignright">{{date('d-m-Y', strtotime(trim(str_replace('/','-',$row->voucher_mode_date))))}}</td>
                                <td class="dtl-contents alignright">{{number_format($row->voucher_credit,3)}}</td>
                                <td class="dtl-contents alignleft">{{isset($row->payment_mode->payment_term_name)?$row->payment_mode->payment_term_name:""}}</td>
                                <td class="dtl-contents alignleft">{{isset($row->accounts->chart_name)?$row->accounts->chart_name:$row->chart_name}}</td>
                                <td class="dtl-contents alignleft">{{$row->voucher_payee_title}}</td>
                            </tr>
                        @endif
                    @endforeach
                    <tr>
                        <td colspan="3" class="dtl-head alignleft">Total:</td>
                        <td class="dtl-head alignright">{{number_format($sumAmount,3)}}</td>
                        <td colspan="3" class="dtl-head alignright"></td>
                    </tr>
                @endif
            </tbody>
        </table>
    @endif
    {{--form remarks--}}
    <table class="tab" valign="top">
        @if(isset($notes))
            <tr>
                <th class="heading alignleft">Notes:</th>
            </tr>
            <tr>
                <td class="normal alignleft paddingNotes">{{$notes}}</td>
            </tr>
        @endif
    </table>
    {{--Signature--}}
    <table class="tab mrgn-top" valign="bottom">
        <tr>
            <th width="25%" class="heading aligncenter">
                <div style="height: 20px;">{{isset($entry_user_name)?$entry_user_name:''}}</div>
                <div class="hr_div_line">Prepared By</div>
            </th>
            <th width="25%" class="heading aligncenter">
                <div style="height: 20px;"></div>
                <div class="hr_div_line">Checked By</div>
            </th>
            <th width="25%" class="heading aligncenter">
                <div style="height: 20px;"></div>
                <div class="hr_div_line">Approved By</div>
            </th>
            <th width="25%" class="heading aligncenter">
                <div style="height: 20px;"></div>
                <div class="hr_div_line">Receiver's Signature</div>
            </th>
        </tr>
    </table>
@endsection
@endpermission
