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
    $dtls = isset($data['dtl'])? $data['dtl'] :[];
@endphp
@permission($data['permission'])
<!DOCTYPE html>
<html>
<head>
<title>{{$heading}}</title>
<link href="{{ asset('css/print.css') }}" rel="stylesheet" type="text/css" />
</head>
<body>
    <table class="tab">
        <tr>
            <td>
                <table class="tab">
                    <tr>
                        <td width="30%">
                            <table class="tab">
                                <tr>
                                    <td>
                                        @php
                                            $QrCode = new \TheUmar98\BarcodeBundle\Utils\QrCode();
                                            $QrCode->setText($code);
                                            $QrCode->setExtension('jpg');
                                            $QrCode->setSize(40);
                                            $image = $QrCode->generate();
                                        @endphp
                                        @if(isset($image) && $image != '')
                                            <img src="data:image/png;base64,{{$image}}" />

                                        @else
                                            <div></div>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td width="25%"></td>
                        <td width="45%">
                            <table class="tab">
                                <tr>
                                    <td class="title aligncenter">{{$heading}}</td>
                                </tr>
                                <tr>
                                    <td class="title aligncenter" style="font-weight:normal; font-size:14px;">{{auth()->user()->branch->branch_name}}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr><td>&nbsp;</td></tr>
        <tr>
            <td>
                <table class="tabData">
                    <tr>
                        <td width="20%" class="heading alignleft">Document No:</td>
                        <td width="20%" class="normal alignleft">{{isset($code)?$code:''}}</td>
                        <td width="33%" class="data alignleft"></td>
                        <td width="17%" class="heading alignleft">Currency:</td>
                        <td width="10%" class="normal alignleft">{{isset($currency)?$currency:''}}</td>
                    </tr>
                    <tr>
                        <td class="heading alignleft">Document Date:</td>
                        <td class="normal alignleft">{{isset($date)?$date:''}}</td>
                        <td class="data alignleft"></td>
                        <td class="heading alignleft">Exchange Rate:</td>
                        <td class="normal alignleft">{{isset($exchange_rate)?$exchange_rate:''}}</td>
                    </tr>
                    <tr>
                        @if($type == 'crv')
                            <td class="heading alignleft">Received Account:</td>
                        @else
                            <td class="heading alignleft">Payment Account:</td>
                        @endif
                        <td class="normal alignleft">{{isset($pay_rec_acc)?$pay_rec_acc:''}}</td>
                        <td class="data alignleft"></td>
                        <td class="heading alignleft">Salesman :</td>
                        {{-- <td class="normal alignleft">{{isset($saleman)?$saleman:''}}</td> --}}
                    </tr>
                </table>
            </td>
        </tr>
        <tr><td>&nbsp;</td></tr>
        <tr>
            <td>
                <table  class="tabData" >
                    <thead>
                        <tr>
                            <th width="7%" class="dtl-head">Sr No</th>
                            <th width="15%" class="dtl-head">Account Code</th>
                            <th width="50%" class="dtl-head alignleft">Account Name</th>
                            {{--<th width="10%" class="dtl-head">Budget</th>--}}
                            <th width="14%" class="dtl-head">Reference No</th>
                            @if($type == 'cpv' || $type == 'lfv')
                                <th width="14%" class="dtl-head">Debit</th>
                            @endif
                            @if($type == 'crv')
                                <th width="14%" class="dtl-head">Credit</th>
                            @endif
                            {{--<th width="10%" class="dtl-head">FC Debit</th>
                            <th width="10%" class="dtl-head">FC Credit</th>--}}
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
                            @foreach($dtls as $data)
                                @php
                                    $bgt_dsc = '';
                                    $budget =\App\Models\TblAccBudget::where('budget_id',$data->budget_id)->where('budget_branch_id',$data->budget_branch_id)->first();
                                    if($budget != Null){
                                        $bgt_dsc = $budget->budget_budgetart_position;
                                    }
                                    $totDebit += $data->voucher_debit;
                                    $totCredit += $data->voucher_credit;
                                    $totFcDebit += $data->voucher_fc_debit;
                                    $totFcCredit += $data->voucher_fc_credit;
                                @endphp
                                <tr>
                                    <td class="dtl-contents aligncenter">{{ $loop->iteration }}</td>
                                    <td class="dtl-contents aligncenter">{{isset($data->accounts->chart_code)?$data->accounts->chart_code:$data->chart_code}}</td>
                                    <td class="dtl-contents alignleft">{{isset($data->accounts->chart_name)?$data->accounts->chart_name:$data->chart_name}}<br><span style="font-size:10px; padding-left: 25px;">{{" ".$data->voucher_descrip}}</span></td>
                                    {{--<td class="dtl-contents alignleft">{{$bgt_dsc}}</td>--}}
                                    <td class="dtl-contents aligncenter">{{$data->voucher_chqno}}</td>
                                    @if($type == 'cpv' || $type == 'lfv')
                                        <td class="dtl-contents alignright">{{number_format($data->voucher_debit,3)}}</td>
                                    @endif
                                    @if($type == 'crv')
                                        <td class="dtl-contents alignright">{{number_format($data->voucher_credit,3)}}</td>
                                    @endif
                                    {{--<td class="dtl-contents alignright">{{number_format($data->voucher_fc_debit,3)}}</td>
                                    <td class="dtl-contents alignright">{{number_format($data->voucher_fc_credit,3)}}</td>--}}
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </td>
        </tr>
        <tr><td>&nbsp;</td></tr>
        <tr>
            <td>
                <table class="tab">
                    <tr>
                        <td width="60%" class="heading alignleft">Total:</td>
                        @if($type == 'cpv' || $type == 'lfv')
                            @php $totAmount = $totDebit; @endphp
                            <td width="10%" class="heading alignright">{{number_format($totDebit,3)}}</td>
                        @endif
                        @if($type == 'crv')
                            @php $totAmount = $totCredit; @endphp
                            <td width="10%" class="heading alignright">{{number_format($totCredit,3)}}</td>
                        @endif
                        {{--<td width="10%" class="heading alignright">{{number_format($totFcDebit,3)}}</td>
                        <td width="10%" class="heading alignright">{{number_format($totFcCredit,3)}}</td>--}}
                    </tr>
                    <tr>
                        <td colspan="5">
                            <hr style="height:2px;border-width:0;color:#000;background-color:#000">
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td>
                <table>
                    <tr>
                        <td class="normal-bold">
                            {{\App\Library\Utilities::AmountWords($totAmount)}}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td>
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
            </td>
        </tr>
        <tr>
            <td>
                <table class="tab mrgn-top" valign="bottom">
                        <tr>
                            <th class="heading aligncenter">{{$saleman}}<hr class="sign-line">@if($type == 'cpv') Paid By @else Prepared By @endif</th>
                            <th class="heading aligncenter"><hr class="sign-line">Checked By</th>
                            <th class="heading aligncenter"><hr class="sign-line">Approved By</th>
                            <th class="heading aligncenter"><hr class="sign-line">@if($type == 'cpv') Paid To @else Paid By @endif</th>
                        </tr>
                </table>
            </td>
        </tr>
        <tr><td>&nbsp;</td></tr>
        <tr>
            <td>
                <table class="tab">
                    <tr>
                        <td class="alignright"><span style="font-size:10px;">Print Date & Time: {{date("d-m-Y h:i:s")}} User Name: {{auth()->user()->name}}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
@endpermission
