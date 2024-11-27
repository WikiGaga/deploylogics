@php
    $heading = strtoupper($data['title']);
    $businessName = strtoupper(auth()->user()->business->business_short_name);
    $id= $data['current']->voucher_id;
    $code= $data['current']->voucher_no;
    $type= $data['current']->voucher_type;
    $date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->voucher_date))));
    $currency = isset($data['currency']->currency_name)?$data['currency']->currency_name:'';
    $exchange_rate = $data['current']->voucher_exchange_rate;
    $notes = $data['current']->voucher_notes;
    $dtls = isset($data['dtl'])? $data['dtl'] :[];
    $chq_dtls = isset($data['chq_dtl'])? $data['chq_dtl'] :[];
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
                        <td width="13%" class="data alignleft"></td>
                        <td width="20%" class="data alignleft"></td>
                        <td width="17%" class="heading alignleft">Document Date:</td>
                        <td width="10%" class="normal alignleft">{{isset($date)?$date:''}}</td>
                    </tr>
                    @if($type == 'jv' || $type == 'obv')
                        <tr>
                            <td class="heading alignleft">Currency :</td>
                            <td class="normal alignleft">{{isset($currency)?$currency:''}}</td>
                            <td class="data alignleft"></td>
                            <td class="data alignleft"></td>
                            <td class="heading alignleft">Exchange Rate:</td>
                            <td class="normal alignleft">{{isset($exchange_rate)?$exchange_rate:''}}</td>
                        </tr>
                    @endif
                </table>
            </td>
        </tr>
        <tr><td>&nbsp;</td></tr>
        <tr>
            <td>
                <table  class="tabData" >
                    {{-- <thead>
                        <tr>
                            <th width="7%" class="dtl-head">Sr No</th>
                            <th width="15%" class="dtl-head">Account Code</th>
                            <th width="30%" class="dtl-head alignleft">Account Name</th>
                            <th width="9%" class="dtl-head">Payment Mode</th>
                            <th width="9%" class="dtl-head">Mode No</th>
                            <th width="9%" class="dtl-head">Mode Date</th>
                            <th width="12%" class="dtl-head alignleft">Budget</th>
                            <th width="9%" class="dtl-head">Payee Title</th>
                            <th width="9%" class="dtl-head">Debit</th>
                            <th width="9%" class="dtl-head">Credit</th>
                            <th width="12%" class="dtl-head">FC Debit</th>
                            <th width="12%" class="dtl-head">FC Credit</th>
                        </tr>
                    </thead> --}}
                    <thead>
                        <tr>
                            <th width="7%" class="dtl-head">Sr No</th>
                            <th width="15%" class="dtl-head">Account Code</th>
                            <th width="30%" class="dtl-head alignleft">Account Name</th>
                            <th width="9%" class="dtl-head">Debit</th>
                            <th width="9%" class="dtl-head">Credit</th>
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

                                    $date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data->voucher_mode_date))));
                                    if($date == '01-01-1970'){
                                            $date = '';
                                    }
                                    if($data->voucher_payment_mode == 'atm'){
                                        $payment_mode = 'ATM Transfer';
                                    }else if($data->voucher_payment_mode =='cheque'){
                                        $payment_mode = 'Cheque';
                                    }else if($data->voucher_payment_mode =='online'){
                                        $payment_mode = 'Online Payment';
                                    }else{
                                        $payment_mode = '';
                                    }
                                    $totDebit += $data->voucher_debit;
                                    $totCredit += $data->voucher_credit;
                                    $totFcDebit += $data->voucher_fc_debit;
                                    $totFcCredit += $data->voucher_fc_credit;
                                @endphp
                                {{-- <tr>
                                    <td class="dtl-contents aligncenter">{{ $loop->iteration }}</td>
                                    <td class="dtl-contents aligncenter">{{$data->accounts->chart_code}}</td>
                                    <td class="dtl-contents alignleft">{{$data->accounts->chart_name}}<br><span style="font-size:10px; padding-left: 25px;">{{" ".$data->voucher_descrip}}</span></td>
                                    <td class="dtl-contents alignleft">{{$bgt_dsc}}</td>
                                    <td class="dtl-contents aligncenter">{{$payment_mode}}</td>
                                    <td class="dtl-contents aligncenter">{{$data->voucher_mode_no}}</td>
                                    <td class="dtl-contents aligncenter">{{$date}}</td>
                                    <td class="dtl-contents">{{$data->voucher_payee_title}}</td>
                                    <td class="dtl-contents alignright">{{number_format($data->voucher_debit,3)}}</td>
                                    <td class="dtl-contents alignright">{{number_format($data->voucher_credit,3)}}</td>
                                    <td class="dtl-contents alignright">{{number_format($data->voucher_fc_debit,3)}}</td>
                                    <td class="dtl-contents alignright">{{number_format($data->voucher_fc_credit,3)}}</td>
                                </tr> --}}

                                <tr>
                                    <td class="dtl-contents aligncenter">{{ $loop->iteration }}</td>
                                    <td class="dtl-contents aligncenter">{{$data->accounts->chart_code}}</td>
                                    <td class="dtl-contents alignleft">{{$data->accounts->chart_name}}<br><span style="font-size:10px; padding-left: 25px;">{{" ".$data->voucher_descrip}}</span></td>
                                    <td class="dtl-contents alignright">{{number_format($data->voucher_debit,3)}}</td>
                                    <td class="dtl-contents alignright">{{number_format($data->voucher_credit,3)}}</td>
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
                        <td width="76%" class="heading alignleft">Total:</td>
                        <td width="12%" class="heading alignright">{{number_format($totDebit,3)}}</td>
                        <td width="12%" class="heading alignright">{{number_format($totCredit,3)}}</td>
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
                            {{\App\Library\Utilities::AmountWords($totDebit, $currency)}}
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
                <h6 style="float: left;">Cheque Details</h6>
                <table class="tabData" style="margin-top: 20px;">
                    <thead>
                        <tr>
                            <th width="7%" class="dtl-head">No</th>
                            <th width="15%" class="dtl-head">Cheque #</th>
                            <th width="9%" class="dtl-head">Cheque Date</th>
                            <th width="9%" class="dtl-head">Amount</th>
                            <th width="15%" class="dtl-head">Payee Title</th>
                            <th width="12%" class="dtl-head">Payee Account</th>
                            <th width="12%" class="dtl-head">Payee Bank</th>
                            <th width="12%" class="dtl-head">Payee Branch Code</th>
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
                            @foreach($chq_dtls as $data)
                            {{-- @dd($data->toArray()); --}}
                                @php
                                    $bgt_dsc = '';
                                    $budget =\App\Models\TblAccBudget::where('budget_id',$data->budget_id)->where('budget_branch_id',$data->budget_branch_id)->first();
                                    if($budget != Null){
                                        $bgt_dsc = $budget->budget_budgetart_position;
                                    }

                                    $date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data->voucher_mode_date))));
                                    if($date == '01-01-1970'){
                                            $date = '';
                                    }                                    
                                    $totDebit += $data->voucher_debit;
                                    $totCredit += $data->voucher_credit;
                                @endphp
                                <tr>
                                    <td class="dtl-contents aligncenter">{{ $loop->iteration }}</td>
                                    <td class="dtl-contents aligncenter">{{$data->voucher_mode_no}}</td>
                                    <td class="dtl-contents aligncenter">{{$date}}</td>
                                    <td class="dtl-contents alignright">{{number_format($totCredit,3)}}</td>
                                    <td class="dtl-contents alignleft">{{$data->voucher_payee_title}}</td>
                                    <td class="dtl-contents alignleft">{{$data->accounts->chart_code}}</td>
                                    <td class="dtl-contents aligncenter">{{($data->bank_id)?$data->bank->bank_name. '-' .$data->bank->bank_branch_name:""}}</td>
                                    <td class="dtl-contents alignright">{{$data->bank_branch_code}}</td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td>
                <table class="tab mrgn-top" valign="bottom">
                        <tr>
                            <th class="heading aligncenter">{{ auth()->user()->name }}<hr class="sign-line">Prepared By</th>
                            <th class="heading aligncenter"><hr class="sign-line">Checked By</th>
                            <th class="heading aligncenter"><hr class="sign-line">Approved By</th>
                            <th class="heading aligncenter"><hr class="sign-line">Received By</th>
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
