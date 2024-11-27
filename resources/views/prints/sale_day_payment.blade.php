@php
    $heading = strtoupper($data['title']);
    $businessName = strtoupper(auth()->user()->business->business_short_name);
    if($data['type'] == 'payment-handover'){
        $payment_handover_received_caption = 'Payment Handover';
    }
    if($data['type'] == 'payment-received'){
        $payment_handover_received_caption = 'Payment Received';
    }
    if($data['type'] == 'day-opening'){
        $payment_handover_received_caption = 'Opening Day';
    }
    if($data['type'] == 'day-closing'){
        $payment_handover_received_caption = 'Closing Day';
    }
    $code= $data['current']->day_code;
    $date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->day_date))));
    $user= isset($data['users']->name)?$data['users']->name:'';
    $shift = $data['current']->day_shift;
    $payment_handover_received = isset($data['payment_person']->name)?$data['payment_person']->name:'';
    $payment_way_type = isset($data['payment_type']->payment_type_name)?$data['payment_type']->payment_type_name:'';
    $reference_no = $data['current']->day_reference_no;
    $notes = $data['current']->day_notes;
    $dtls = isset($data['dtl'])? $data['dtl']:[];
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
                        <td width="17%" class="heading alignleft">Payment Way Type:</td>
                        <td width="10%" class="normal alignleft">{{isset($payment_way_type)?$payment_way_type:''}}</td>
                    </tr>
                    <tr>
                        <td class="heading alignleft">Document Date:</td>
                        <td class="normal alignleft">{{isset($date)?$date:''}}</td>
                        <td class="data alignleft"></td>
                        <td class="data alignleft"></td>
                        <td class="heading alignleft">Reference No:</td>
                        <td class="normal alignleft">{{isset($reference_no)?$reference_no:''}}</td>
                    </tr>
                    <tr>
                        <td class="heading alignleft">{{$payment_handover_received_caption}} To:</td>
                        <td class="normal alignleft">{{isset($payment_handover_received)?$payment_handover_received:''}}</td>
                        <td class="data alignleft"></td>
                        <td class="data alignleft"></td>
                        <td class="heading alignleft"></td>
                        <td class="normal alignleft"></td>
                    </tr>
                    <tr>
                        <td class="heading alignleft">Payment Handover From:</td>
                        <td class="normal alignleft">{{isset($user)?$user:''}}</td>
                        <td class="data alignleft"></td>
                        <td class="data alignleft"></td>
                        <td class="data alignleft"></td>
                        <td class="data alignleft"></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr><td>&nbsp;</td></tr>
        <tr>
            <td>
                <table  class="tabData" >
                    <tr>
                        <td class="dtl-contents alignleft" style="font-weight:bold;">Denomination</td>
                        @if(isset($dtls))
                            @foreach($dtls as $dtl)
                                <td class="dtl-contents aligncenter">{{$dtl->denomination_name}}</td>
                            @endforeach
                        @endif
                    </tr>
                    <tr>
                        <td class="dtl-contents alignleft" style="font-weight:bold;">Qty</td>
                        @if(isset($dtls))
                            @foreach($dtls as $dtl)
                                <td class="dtl-contents aligncenter">{{$dtl->day_qty}}</td>
                            @endforeach
                        @endif
                    </tr>
                    <tr>
                        <td class="dtl-contents alignleft" style="font-weight:bold;">Value</td>
                        @if(isset($dtls))
                            @php $sumVals = 0 @endphp
                            @foreach($dtls as $dtl)
                                <td class="dtl-contents aligncenter"><b>{{number_format($dtl->day_amount,3)}}</b></td>
                                @php $sumVals += $dtl->day_amount @endphp
                            @endforeach
                        @endif
                    </tr>
                </table>
            </td>
        </tr>
        <tr><td></td></tr>
        <tr>
            <td>
                <table class="tab">
                    <tr>
                        <td width="50%" valign="top">
                            <table class="tab">
                                <tr>
                                    <th class="heading alignleft" style="padding: 15px 0;">Total Amount: <span style="padding-left: 10px">{{$sumVals}}</span></th>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td width="50%" valign="top">
                            <table class="tab">
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
                        <td width="50%">
                            <table class="tab">
                                <tr>
                                    <td class="alignright"><span style="font-size:10px;">Print Date & Time: {{date("d-m-Y h:i:s")}} User Name: {{auth()->user()->name}}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
@endpermission
