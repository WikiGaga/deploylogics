@php

    $heading = strtoupper($data['title']);
    $businessName = strtoupper(auth()->user()->business->business_short_name);
    $code= $data['current']->sales_contract_code;
    $date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->sales_contract_date))));
    $customer_name = isset($data['current']->customer->customer_name)?$data['current']->customer->customer_name:'';
    $currency = $data['currency']->currency_name;
    $exchange_rate = $data['current']->sales_contract_exchange_rate;
    $payment_term = isset($data['payment_terms']->payment_term_name)?$data['payment_terms']->payment_term_name:'';
    $days = $data['current']->sales_contract_credit_days;
    $payment_mode = $data['current']->payment_mode_id;
    $start_date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->sales_contract_start_date))));
    $end_date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->sales_contract_end_date))));
    $sales_contract_rate_type = $data['current']->sales_contract_rate_type;
    $sales_contract_perc = isset($data['current']->sales_contract_perc)?$data['current']->sales_contract_perc.'%':'';
    $rate_type = ucwords(str_replace("_", " ",$sales_contract_rate_type)).' + '.$sales_contract_perc;
    $notes = $data['current']->sales_contract_remarks;
    $dtls = isset($data['current']->dtls)? $data['current']->dtls:[];
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
                        <td width="20%" class="heading alignleft">Code :</td>
                        <td width="20%" class="normal alignleft">{{isset($code)?$code:''}}</td>
                        <td width="13%" class="data alignleft"></td>
                        <td width="20%" class="data alignleft"></td>
                        <td width="17%" class="heading alignleft">Currency:</td>
                        <td width="10%" class="normal alignleft">{{isset($currency)?$currency:''}}</td>
                    </tr>
                    <tr>
                        <td class="heading alignleft">Date :</td>
                        <td class="normal alignleft">{{isset($date)?$date:''}}</td>
                        <td class="data alignleft" colspan='2'></td>
                        <td class="heading alignleft">Exchange Rate:</td>
                        <td class="normal alignleft">{{isset($exchange_rate)?$exchange_rate:''}}</td>
                    </tr>
                    <tr>
                        <td class="heading alignleft">Customer:</td>
                        <td class="normal alignleft">{{isset($customer_name)?$customer_name:''}}</td>
                        <td class="data alignleft" colspan='2'></td>
                        <td class="heading alignleft">Price Type:</td>
                        <td class="normal alignleft">{{isset($rate_type)?$rate_type:''}}</td>
                    </tr>
                    <tr>
                        <td class="heading alignleft">Payment Terms:</td>
                        <td class="normal alignleft">{{isset($days)?$days:''}} {{isset($payment_term)?$payment_term:''}}</td>
                        <td class="data alignleft" colspan='4'></td>
                    </tr>
                    <tr>
                        <td class="heading alignleft">Start Date:</td>
                        <td class="normal alignleft">{{($start_date =='01-01-1970')?'':$start_date}}</td>
                        <td class="data alignleft" colspan='2'></td>
                        <td class="heading alignleft">End Date:</td>
                        <td class="normal alignleft">{{($end_date =='01-01-1970')?'':$end_date}}</td>
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
                            <th width="5%" class="dtl-head">Sr No</th>
                            <th width="10%" class="dtl-head alignleft">Barcode</th>
                            <th width="20%" class="dtl-head alignleft">Product Name</th>
                            <th width="20%" class="dtl-head">Arabic Name</th>
                            <th width="9%" class="dtl-head">Rate</th>
                            <th width="9%" class="dtl-head">VAT %</th>
                            <th width="9%" class="dtl-head">Vat Amt</th>
                            <th width="9%" class="dtl-head">Net Rate</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totGrossAmt = 0;
                            $i=0;
                        @endphp
                        @if(isset($dtls))
                            @foreach($dtls as $data)
                                @php
                                    $i++;
                                    $totGrossAmt += $data->sales_dtl_total_amount;
                                @endphp
                                <tr>
                                    <td class="dtl-contents aligncenter">{{ $loop->iteration }}</td>
                                    <td class="dtl-contents alignleft">{{$data->sales_contract_dtl_barcode}}</td>
                                    <td class="dtl-contents alignleft">{{$data->product->product_name}}</td>
                                    <td class="dtl-contents alignright">{{$data->product->product_arabic_name}}</td>
                                    <td class="dtl-contents alignright">{{number_format($data->sales_contract_dtl_rate,3)}}</td>
                                    <td class="dtl-contents alignright">{{number_format($data->sales_dtl_vat_per,3)}}</td>
                                    <td class="dtl-contents alignright">{{number_format($data->sales_dtl_vat_amount,3)}}</td>
                                    <td class="dtl-contents alignright">{{number_format($data->sales_contract_dtl_net_rate,3)}}</td>
                                </tr>
                            @endforeach
                        @endif
                        @if($i<=8)
                            @for ($z = 0; $z <= 8; $z++)
                                <tr>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                </tr>
                            @endfor
                        @endif
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td>
                <table class="tab">
                    <tr>
                        <td width="100%" valign="top">
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
                    </tr>
                </table>
            </td>
        </tr>
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
