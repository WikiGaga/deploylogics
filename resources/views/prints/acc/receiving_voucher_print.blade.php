@extends('prints.partial.template')
@section('pageCSS')

@endsection
@permission($data['permission'])
@php
    $heading = strtoupper($data['title']);
    $businessName = strtoupper(auth()->user()->business->business_short_name);
    $id= $data['current']->voucher_id;
    $code= $data['current']->voucher_no;
    $type= $data['current']->voucher_type;
    $voucher_user_id = $data['current']->voucher_user_id;
    $user = \App\Models\User::where('id',$voucher_user_id)->first();
    if(isset($user->name)){
        $entry_user_name = $user->name;
    }
    $date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->voucher_date))));
    $currency = isset($data['currency']->currency_name)?$data['currency']->currency_name:'';
    $exchange_rate = $data['current']->voucher_exchange_rate;
    $notes = $data['current']->voucher_notes;
    $dtls = isset($data['dtl'])? $data['dtl'] :[];

    $vendors = isset($data['vendor'])? $data['vendor'] :[];
    $deducs = isset($data['deduction'])? $data['deduction'] :[];
    $actual = isset($data['actual'])? $data['actual'] :[];


    $vendor_name = $data['vendor_name']->accounts->chart_name;
    $chq_dtls = isset($data['chq_dtl'])? $data['chq_dtl'] :[];
    $voucher_bills = isset($data['voucher_bill'])? $data['voucher_bill'] :[];
    $on_account_voucher = $data['current']->on_account_voucher;
    $voucher_payment_mode = (isset($data['current']->voucher_payment_mode))? $data['current']->voucher_payment_mode :[];
@endphp
@section('title', $heading)
@section('page_heading', $heading)
@section('content')
    {{--form content--}}
    <table class="tableData topHeaderInfo">
        <tr>
            <td width="33.33%">
                <div>
                    <span class="heading heading-block">Document No:</span>
                    <span class="normal normal-block">{{isset($code)?$code:''}}</span>
                </div>
            </td>
            <td width="33.33%"></td>
            <td width="33.33%">
                <div>
                    <span class="heading heading-block">Document Date:</span>
                    <span class="normal normal-block">{{isset($date)?$date:''}}</span>
                </div>
            </td>
        </tr>
    </table>
    {{--table content--}}
    <br>
    <table class="tabData" >
        <thead>
            <tr>
                <th width="7%" class="dtl-head topHeaderInfo">Sr No</th>
                <th width="15%" class="dtl-head topHeaderInfo">Account Code</th>
                <th width="30%" class="dtl-head topHeaderInfo alignleft">Account Name</th>
                <th width="9%" class="dtl-head topHeaderInfo">Debit</th>
                <th width="9%" class="dtl-head topHeaderInfo">Credit</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totDebit = 0;
                $totCredit = 0;
                $totFcDebit = 0;
                $totFcCredit = 0;
                $sr_num = 0;
                $sr_no = 0;
                $sr = 0;
            @endphp

            @if(isset($actual))
                @foreach($actual as $row)
                    @php
                        $date = date('d-m-Y', strtotime(trim(str_replace('/','-',$row->voucher_mode_date))));
                        if($date == '01-01-1970'){
                                $date = '';
                        }
                        $totDebit += $row->debit;
                        $totCredit += $row->credit;
                        $totFcDebit += $row->voucher_fc_debit;
                        $totFcCredit += $row->voucher_fc_credit;
                        $sr_num = $loop->iteration;
                    @endphp
                    <tr>
                        <td class="dtl-contents aligncenter">{{ $sr_num }}</td>
                        <td class="dtl-contents aligncenter">{{isset($row->accounts->chart_code)?$row->accounts->chart_code:$row->chart_code}}</td>
                        <td class="dtl-contents alignleft">{{isset($row->accounts->chart_name)?$row->accounts->chart_name:$row->chart_name}}<br><span style="font-size:10px; padding-left: 25px;">{{" ".$row->voucher_descrip}}</span></td>
                        <td class="dtl-contents alignright">{{($row->debit!=0)?number_format($row->debit,3):""}}</td>
                        <td class="dtl-contents alignright">{{($row->credit!=0)?number_format($row->credit,3):""}}</td>
                    </tr>
                @endforeach
            @endif
            @if(isset($deducs))
                @foreach($deducs as $row)
                    @php
                        if($sr_num>=1){
                            $sr_no = $sr_num+$loop->iteration;
                        }
                        $date = date('d-m-Y', strtotime(trim(str_replace('/','-',$row->voucher_mode_date))));
                        if($date == '01-01-1970'){
                                $date = '';
                        }
                        $totDebit += $row->debit;
                        $totCredit += $row->credit;
                        $totFcDebit += $row->voucher_fc_debit;
                        $totFcCredit += $row->voucher_fc_credit;
                    @endphp
                    <tr>
                        <td class="dtl-contents aligncenter">{{ $sr_no }}</td>
                        <td class="dtl-contents aligncenter">{{isset($row->accounts->chart_code)?$row->accounts->chart_code:$row->chart_code}}</td>
                        <td class="dtl-contents alignleft">{{isset($row->accounts->chart_name)?$row->accounts->chart_name:$row->chart_name}}<br><span style="font-size:10px; padding-left: 25px;">{{" ".$row->voucher_descrip}}</span></td>
                        <td class="dtl-contents alignright">{{($row->debit!=0)?number_format($row->debit,3):""}}</td>
                        <td class="dtl-contents alignright">{{($row->credit!=0)?number_format($row->credit,3):""}}</td>
                    </tr>
                @endforeach
            @endif
            @if(isset($vendors))
                @foreach($vendors as $row)
                @php
                    if ($sr_no == 0) {
                        $sr = $sr_num+$loop->iteration;
                    } else {
                        $sr = $sr_no+$loop->iteration;
                    }
                    $date = date('d-m-Y', strtotime(trim(str_replace('/','-',$row->voucher_mode_date))));
                    if($date == '01-01-1970'){
                            $date = '';
                    }
                    $totDebit += $row->debit;
                    $totCredit += $row->credit;
                    $totFcDebit += $row->voucher_fc_debit;
                    $totFcCredit += $row->voucher_fc_credit;
                @endphp
                <tr>
                    <td class="dtl-contents aligncenter">{{ $sr}}</td>
                    <td class="dtl-contents aligncenter">{{isset($row->accounts->chart_code)?$row->accounts->chart_code:$row->chart_code}}</td>
                    <td class="dtl-contents alignleft">{{isset($row->accounts->chart_name)?$row->accounts->chart_name:$row->chart_name}}<br><span style="font-size:10px; padding-left: 25px;">{{" ".$row->voucher_descrip}}</span></td>
                    <td class="dtl-contents alignright">{{($row->debit!=0)?number_format($row->debit,3):""}}</td>
                    <td class="dtl-contents alignright">{{($row->credit!=0)?number_format($row->credit,3):""}}</td>
                </tr>
                @endforeach
            @endif
            <tr>
                <td colspan="3" class="dtl-head alignleft">{{\App\Library\Utilities::AmountWords($totDebit)}}</td>
                <td class="dtl-head alignright">{{number_format($totDebit,3)}}</td>
                <td class="dtl-head alignright">{{number_format($totCredit,3)}}</td>
            </tr>

        </tbody>
    </table>
    {{--form remarks--}}
    <table class="tab" valign="top">
        @if(isset($notes))
            <tr>
                <th width="10%" class="heading alignleft">Notes:</th>
                <td width="90%" class="alignleft">{{$notes}}</td>
            </tr>
        @endif
    </table><br>

    <div class="topHeaderInfo" style="font-weight: 900; width: 90px; margin-top:-5px;">Cheque Details</div>
    <table class="tabData">
        <thead>
            <tr>
                <th width="7%" class="dtl-head topHeaderInfo">No</th>
                <th width="15%" class="dtl-head topHeaderInfo">Cheque #</th>
                <th width="9%" class="dtl-head topHeaderInfo">Date</th>
                <th width="9%" class="dtl-head topHeaderInfo">Amount</th>
                <th width="9%" class="dtl-head topHeaderInfo">Cheque Type</th>
                <th width="12%" class="dtl-head topHeaderInfo">Bank</th>
                <th width="15%" class="dtl-head topHeaderInfo">Payment Branch</th>
                <th width="12%" class="dtl-head topHeaderInfo">Payee Title</th>
                <th width="12%" class="dtl-head topHeaderInfo">Cheque Status</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totDebit = 0;
                $totCredit = 0;
                $totFcDebit = 0;
                $totFcCredit = 0;
            @endphp
            @if(isset($chq_dtls))
                @foreach($chq_dtls as $row)
                    @php
                        $date = date('d-m-Y', strtotime(trim(str_replace('/','-',$row->voucher_mode_date))));
                        if($date == '01-01-1970'){
                                $date = '';
                        }
                        $totDebit += $row->voucher_debit;
                        $totCredit += $row->voucher_credit;
                        $totFcDebit += $row->voucher_fc_debit;
                        $totFcCredit += $row->voucher_fc_credit;
                    @endphp
                    <tr>
                        <td class="dtl-contents aligncenter">{{ $loop->iteration }}</td>
                        <td class="dtl-contents aligncenter">{{$row->voucher_mode_no}}</td>
                        <td class="dtl-contents aligncenter">{{$date}}</td>
                        <td class="dtl-contents alignright">{{($row->voucher_debit!=0)?number_format($row->voucher_debit,3):""}}</td>
                        <td class="dtl-contents aligncenter">{{isset($row->payment_mode->payment_term_name)?$row->payment_mode->payment_term_name:''}}</td>
                        <td class="dtl-contents aligncenter">{{$row->accounts->chart_name}}</td>
                        <td class="dtl-contents alignright">{{$row->bank_branch_code}}</td>
                        <td class="dtl-contents aligncenter">{{isset($vendor_name)?$vendor_name:''}}</td>
                        <td class="dtl-contents aligncenter">Issue</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="3" class="dtl-head alignright border-right">Total:</td>
                    <td class="dtl-head alignright border-right">{{number_format($totDebit,3)}}</td>
                    <td colspan="6" class="dtl-head alignright"></td>
                </tr>
            @endif
        </tbody>
    </table><br>
    @if(count($voucher_bills) != 0)
    <div class="topHeaderInfo" style="font-weight: 900; width: 70px; margin-top:-5px;">GRN Details</div>
    <table class="tabData">
        <thead>
            <tr>
                <th width="7%" class="dtl-head topHeaderInfo">Sr.</th>
                <th width="9%" class="dtl-head topHeaderInfo">Invoice No.</th>
                <th width="15%" class="dtl-head topHeaderInfo">Invoice Date</th>
                <th width="12%" class="dtl-head topHeaderInfo">Invoice Amount</th>
                <th width="12%" class="dtl-head topHeaderInfo">Paid Amount</th>
                <th width="12%" class="dtl-head topHeaderInfo">Inv. Balance</th>
                <th width="12%" class="dtl-head topHeaderInfo">Current Amt.</th>
                <th width="12%" class="dtl-head topHeaderInfo">Current Bal.</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totDebit = 0;
                $totCredit = 0;
                $totFcDebit = 0;
                $totFcCredit = 0;
                $sum_voucher_bill_amount = 0;
                $sum_voucher_bill_bal_amount = 0;
                $sum_voucher_bill_rec_amount = 0;
            @endphp
            @foreach($voucher_bills as $bill)
                @php
                    $bi = $loop->iteration;
                    $sum_voucher_bill_amount += $bill->voucher_bill_amount;
                    $sum_voucher_bill_bal_amount += $bill->voucher_bill_bal_amount;
                    $sum_voucher_bill_rec_amount += $bill->voucher_bill_rec_amount;
                    $bra = \App\Models\TblSoftBranch::where('branch_id',$bill->branch_id)->first();
                    $voucher_document_date = date('d-m-Y', strtotime(trim(str_replace('/','-',$bill->voucher_document_date))));
                @endphp
                <tr>
                    <td class="dtl-contents aligncenter">{{ $bi }}</td>
                    <td class="dtl-contents aligncenter">{{$bill->voucher_document_code}}</td>
                    <td class="dtl-contents aligncenter">{{$voucher_document_date}}</td>
                    <td class="dtl-contents alignright">{{($bill->voucher_bill_amount!=0)?number_format($bill->voucher_bill_amount,3):""}}</td>
                    <td class="dtl-contents alignright">{{($bill->voucher_bill_paid_amount!=0)?number_format($bill->voucher_bill_paid_amount,3):""}}</td>
                    <td class="dtl-contents alignright">{{($bill->voucher_bill_bal_amount!=0)?number_format($bill->voucher_bill_bal_amount,3):""}}</td>
                    <td class="dtl-contents alignright">{{($bill->voucher_bill_rec_amount!=0)?number_format($bill->voucher_bill_rec_amount,3):""}}</td>
                    <td class="dtl-contents alignright">{{($bill->voucher_bill_net_bal_amount!=0)?number_format($bill->voucher_bill_net_bal_amount,3):""}}</td>
                </tr>
            @endforeach
            <tr>
                <td class="dtl-head border-right">Total:</td>
                <td class="dtl-head alignright border-right"></td>
                <td class="dtl-head alignright"></td>
                <td class="dtl-head alignright">{{number_format($sum_voucher_bill_amount,3)}}</td>
                <td class="dtl-head alignright"></td>
                <td class="dtl-head alignright">{{number_format($sum_voucher_bill_bal_amount,3)}}</td>
                <td class="dtl-head alignright">{{number_format($sum_voucher_bill_rec_amount,3)}}</td>
                <td class="dtl-head alignright"></td>
            </tr>
        </tbody>
    </table>
    @endif
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
