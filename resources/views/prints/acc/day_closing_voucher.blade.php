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
    $saleman = $data['users']->name;
    $notes = $data['current']->voucher_notes;
    $dtls = isset($data['dtl'])? $data['dtl'] :[];
    $add_voucher_payment_mode = false;
@endphp
@section('title', $heading)
@section('page_heading', $heading)
@section('content')
    {{--table content--}}
    <table  class="tabData" >
        <thead>
        <tr>
            <th width="5%" class="dtl-head">Sr No</th>
            <th width="15%" class="dtl-head">Account Code</th>
            <th width="30%" class="dtl-head alignleft">Account Name</th>
            <th width="30%" class="dtl-head alignleft">Description</th>
            <th width="10%" class="dtl-head">Debit</th>
            <th width="10%" class="dtl-head">Credit</th>
        </tr>
        </thead>
        <tbody>
        @php
            $totDebit = 0;
            $totCredit = 0;
            $totFcDebit = 0;
            $totFcCredit = 0;
        @endphp
        @if(isset($dtls))
            @foreach($dtls as $row)
                @php
                    $totDebit += $row->voucher_debit;
                    $totCredit += $row->voucher_credit;
                    $totFcDebit += $row->voucher_fc_debit;
                    $totFcCredit += $row->voucher_fc_credit;
                @endphp
                @if(!in_array($row->voucher_payment_mode,["",'null',null]))
                    @php
                        $add_voucher_payment_mode = true;
                    @endphp
                @endif
                <tr>
                    <td class="dtl-contents aligncenter">{{ $loop->iteration }}</td>
                    <td class="dtl-contents aligncenter">{{isset($row->accounts->chart_code)?$row->accounts->chart_code:$row->chart_code}}</td>
                    <td class="dtl-contents alignleft">{{isset($row->accounts->chart_name)?$row->accounts->chart_name:$row->chart_name}}</td>
                    <td class="dtl-contents alignleft">{{$row->voucher_descrip}}</td>
                    <td class="dtl-contents alignright">{{($row->voucher_debit!=0)?number_format($row->voucher_debit,3):""}}</td>
                    <td class="dtl-contents alignright">{{($row->voucher_credit!=0)?number_format($row->voucher_credit,3):""}}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="4" class="dtl-head alignleft">{{\App\Library\Utilities::AmountWords($totDebit)}}</td>
                <td class="dtl-head alignright">{{number_format($totDebit,3)}}</td>
                <td class="dtl-head alignright">{{number_format($totCredit,3)}}</td>
            </tr>
        @endif
        </tbody>
    </table>
    @if($add_voucher_payment_mode)
        <h4 style="background: #c7c7c7;padding: 10px;width: 100px;">Cheque Detail</h4>
        <table  class="tabData" >
            <thead>
            <tr>
                <th width="5%" class="dtl-head">Sr No</th>
                <th width="10%" class="dtl-head alignleft">Cheque No</th>
                <th width="10%" class="dtl-head alignleft">Date</th>
                <th width="10%" class="dtl-head alignleft">Amount</th>
                <th width="10%" class="dtl-head alignleft">Cheque Type</th>
                <th width="20%" class="dtl-head alignleft">Bank</th>
                <th width="20%" class="dtl-head alignleft">Payee Title</th>
            </tr>
            </thead>
            <tbody>
            @php
                $sumAmount = 0;
                $sr = 0;
            @endphp
            @if(isset($dtls))
                @foreach($dtls as $row)
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
                @endforeach
                <tr>
                    <td colspan="2" class="dtl-head alignright"></td>
                    <td class="dtl-head alignright">Amount:</td>
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
            <th width="33%" class="heading aligncenter">
                <div style="height: 20px;">{{isset($saleman)?$saleman:''}}</div>
                <div class="hr_div_line">Prepared By</div>
            </th>
            <th width="33%" class="heading aligncenter">
                <div style="height: 20px;"></div>
                <div class="hr_div_line">Checked By</div>
            </th>
            <th width="33%" class="heading aligncenter">
                <div style="height: 20px;"></div>
                <div class="hr_div_line">Receiver's Signature</div>
            </th>
        </tr>
    </table>
@endsection
@endpermission
