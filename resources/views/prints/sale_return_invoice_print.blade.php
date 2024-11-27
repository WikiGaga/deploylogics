@php
    $heading = strtoupper($data['title']);
    $businessName = strtoupper(auth()->user()->business->business_short_name);
    $document_type = $data['current']->sales_type;
    $code= $data['current']->sales_code;
    $date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->sales_date))));
    $customer_name = isset($data['current']->customer_view->customer_name)?$data['current']->customer_view->customer_name:'';
    $currency = isset($data['currency']->currency_name)?$data['currency']->currency_name:"";
    $exchange_rate = $data['current']->sales_exchange_rate;
    $booking_no = isset($data['current']->SO->sales_order_code)?$data['current']->SO->sales_order_code:'';
    $delivery_no = $data['current']->sales_delivery_id;
    $sale_type = isset($data['payment_type']->payment_type_name)?$data['payment_type']->payment_type_name:'';
    $payment_term = isset($data['payment_terms']->payment_term_name)?$data['payment_terms']->payment_term_name:'';
    $days = $data['current']->sales_credit_days;
    $mobile = $data['current']->sales_mobile_no;
    $notes = $data['current']->sales_remarks;
    $NetTotal = $data['current']->sales_net_amount;
    $dtls = isset($data['current']->dtls)? $data['current']->dtls:[];
    $Expdtls = isset($data['current']->expense)? $data['current']->expense:[];
@endphp
@permission($data['permission'])
<!DOCTYPE html>
<html>
<head>
<title>{{$heading}}</title>
<link href="{{ asset('css/print.css') }}" rel="stylesheet" type="text/css" />
</head>
@if($document_type == 'SI')
{{--<body onload="print_document();" onafterprint="redirectBack();">--}}
<body>
@else
<body>
@endif
    <div id="HideShow" style="font-size: 8px">
        <span style="display: block;position: absolute;">Header </span><span>show:</span>
        <input type="checkbox" onclick="HeaderHide();" id="HeaderHide">
        <div id="styleCss">
            <style>
                @media print{
                    .head{display:none;}
                    .headerHeight{height: 97.33px !important;}
                }
            </style>
        </div>
    </div>
    <div class="headerHeight"></div>
    <table class="tab">
        <tr>
            <td>
                <table class="tab head">
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
            <tr>
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
                        <td class="heading alignleft">Sales Type:</td>
                        <td class="normal alignleft">{{isset($sale_type)?$sale_type:''}}</td>
                    </tr>
                    <!---<tr>
                        <td class="heading alignleft">Booking No:</td>
                        <td class="normal alignleft">{{isset($booking_no)?$booking_no:''}}</td>
                        <td class="data alignleft" colspan='2'></td>
                        <td class="heading alignleft">Delivery No:</td>
                        <td class="normal alignleft">{{isset($delivery_no)?$delivery_no:''}}</td>
                    </tr>--->
                    <tr>
                        <td class="heading alignleft">Payment Terms:</td>
                        <td class="normal alignleft">{{isset($days)?$days:''}}{{isset($payment_term)?$payment_term:''}}</td>
                        <td class="data alignleft" colspan='2'></td>
                        @if($document_type == 'SI')
                        <td class="heading alignleft">Mobile No:</td>
                        <td class="normal alignleft">{{isset($mobile)?$mobile:''}}</td>
                        @else
                        <td class="data alignleft" colspan='2'></td>
                        @endif
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
                            <th width="10%" class="dtl-head">Barcode</th>
                            <th width="15%" class="dtl-head alignleft">Product Name</th>
                            <th width="15%" class="dtl-head">Arabic Name</th>
                            <th width="5%" class="dtl-head">UOM</th>
                            <th width="5%" class="dtl-head">Qty</th>
                            <th width="5%" class="dtl-head">Rate</th>
                            <th width="5%" class="dtl-head">Amount</th>
                            <th width="5%" class="dtl-head">Disc Amt</th>
                            <th width="5%" class="dtl-head">Vat Amt</th>
                            <th width="5%" class="dtl-head">Gross Amt</th>
                            @if($document_type == 'LFS')
                                <th width="5%" class="dtl-head">Start Date</th>
                                <th width="5%" class="dtl-head">End Date</th>
                                <th width="5%" class="dtl-head">Notes</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                    @php
                        $totAmt = 0;
                        $totVatAmt = 0;
                        $totDiscAmt = 0;
                        $totGrossAmt = 0;
                        $i=0;
                    @endphp
                        @if(isset($dtls))
                            @foreach($dtls as $data)
                                @php
                               // dd($data);
                                    $start_date= date('d-m-Y', strtotime(trim(str_replace('/','-',$data->sales_dtl_start_date))));
                                    $end_date= date('d-m-Y', strtotime(trim(str_replace('/','-',$data->sales_dtl_end_date))));
                                    if($start_date == '01-01-1970'){
                                        $start_date = '';
                                    }
                                    if($end_date == '01-01-1970'){
                                        $end_date = '';
                                    }

                                    $i++;
                                    $totAmt += abs($data->sales_dtl_amount);
                                    $totDiscAmt += abs($data->sales_dtl_disc_amount);
                                    $totVatAmt += abs($data->sales_dtl_vat_amount);
                                    $totGrossAmt += abs($data->sales_dtl_total_amount);
                                @endphp
                                <tr>
                                    <td class="dtl-contents aligncenter">{{ $loop->iteration }}</td>
                                    <td class="dtl-contents alignleft">{{$data->sales_dtl_barcode}}</td>
                                    <td class="dtl-contents alignleft">{{$data->product->product_name}}</td>
                                    <td class="dtl-contents alignright">{{$data->product->product_arabic_name}}</td>
                                    <td class="dtl-contents alignleft">{{$data->uom->uom_name}}</td>
                                    <td class="dtl-contents aligncenter">{{abs($data->sales_dtl_quantity)}}</td>
                                    <td class="dtl-contents alignright">{{number_format(abs($data->sales_dtl_rate),3)}}</td>
                                    <td class="dtl-contents alignright">{{number_format(abs($data->sales_dtl_amount),3)}}</td>
                                    <td class="dtl-contents alignright">{{number_format(abs($data->sales_dtl_disc_amount),3)}}</td>
                                    <td class="dtl-contents alignright">{{number_format(abs($data->sales_dtl_vat_amount),3)}}</td>
                                    <td class="dtl-contents alignright">{{number_format(abs($data->sales_dtl_total_amount),3)}}</td>
                                    @if($document_type == 'LFS')
                                    <td class="dtl-contents aligncenter">{{$start_date}}</td>
                                    <td class="dtl-contents aligncenter">{{$end_date}}</td>
                                    <td class="dtl-contents alignleft">{{$data->sales_dtl_notes}}</td>
                                    @endif
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
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    @if($document_type == 'LFS')
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                    @endif
                                </tr>
                            @endfor
                        @endif
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td>
                <table>
                    <tr>
                        <td class="normal-bold">
                            {{\App\Library\Utilities::AmountWords($NetTotal)}}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td>
                <table class="tab">
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
                            <table class="tab" style="margin-top: 20px;">
                                <tr>
                                    <td width="50%" valign="top">
                                        <table class="tab">
                                            <tr>
                                                <th class="heading alignleft">Customer Signature:</th>
                                            </tr>
                                        </table>
                                    </td>
                                    <td width="50%" valign="top">
                                        <table class="tab">
                                            <tr>
                                                <th class="heading alignleft">Store Signature:</th>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td width="20%"></td>
                        <td width="30%">
                            <table class="tab">
                                @php
                                    $totExpAmt = 0;
                                @endphp
                                    @if(isset($Expdtls))
                                        @foreach($Expdtls as $expense)
                                            @php
                                                $totExpAmt += $expense->sales_expense_amount;
                                            @endphp
                                            <tr>
                                                <td width="60%" class="heading alignleft">{{$expense->accounts->chart_name}}</td>
                                                <td width="40%" class="heading alignright">{{number_format($expense->sales_expense_amount,3)}}</td>
                                            </tr>
                                        @endforeach
                                    @endif
                                    @php
                                        $netTot = $totExpAmt+$totGrossAmt;
                                    @endphp
                                    <tr>
                                        <td colspan="2">
                                            <hr style="height:1px;border-width:0;color:#000;background-color:#000">
                                            <hr style="height:2px;border-width:0;color:#000;background-color:#000">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="heading alignleft" >AmtTotal</td>
                                        <td class="heading alignright">{{number_format($totAmt,3)}}</td>
                                    </tr>
                                    @if($totDiscAmt !='' || $totDiscAmt !=0)
                                        <tr>
                                            <td class="heading alignleft" >DiscTotal</td>
                                            <td class="heading alignright">{{number_format($totDiscAmt,3)}}</td>
                                        </tr>
                                    @endif
                                    @if($totVatAmt !='' || $totVatAmt !=0)
                                        <tr>
                                            <td class="heading alignleft" >VatTotal</td>
                                            <td class="heading alignright">{{number_format($totVatAmt,3)}}</td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td class="heading alignleft" >NetTotal</td>
                                        <td class="heading alignright">{{number_format($netTot,3)}}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <hr style="height:2px;border-width:0;color:#000;background-color:#000">
                                        </td>
                                    </tr>
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

    <script>
        function HeaderHide(){
            // if checked
            if(document.getElementById('HeaderHide').checked == true){
                document.getElementById('styleCss').innerHTML = '';
            }
            // if unchecked
            if(document.getElementById('HeaderHide').checked == false){
                var styleCss = document.getElementById('styleCss');
                var css = '@media print{ .head{display:none;}.headerHeight{height: 97.33px !important;} }';
                var style = document.createElement('style');
                styleCss.appendChild(style);

                style.type = 'text/css';
                if (style.styleSheet){
                    // This is required for IE8 and below.
                    style.styleSheet.cssText = css;
                } else {
                    style.appendChild(document.createTextNode(css));
                }
            }
        }
        function print_document(){
            window.print();
        }
        function redirectBack(){
            window.close();
            history.back();
        }
    </script>
</body>
</html>
@endpermission
