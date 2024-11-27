@php
   // dd($data['invoice_headings']->toArray());

    $invoice_heading = $data['invoice_headings'];
    $a = $invoice_heading['date'];
  //  dd($data['current']->toArray());
    $DateH = $invoice_heading['date'];
    $TypeH = $invoice_heading['payment_method'];
    $RemarksH =  $invoice_heading['phone_note'];
    $ItmNameH = $invoice_heading['item_name'];
    $QtyH = $invoice_heading['qty'];
    $RateH = $invoice_heading['price'];
    $AmountH = $invoice_heading['amount'];
    $TotalH= $invoice_heading['total_items'];
    $PaidH = $invoice_heading['total_bill'];
    $RemainingH = $invoice_heading['change'];
    $amount_receiveH = $invoice_heading['cash_received'];
    $userNameH = $invoice_heading['user'];


    $heading = strtoupper($data['title']);
    $businessName = strtoupper(auth()->user()->business->business_short_name);
    $code= $data['current']->sales_code;
    $date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->sales_date))));
    $customer_name = isset($data['current']->customer->customer_name)?$data['current']->customer->customer_name:'';
    $currency = $data['currency']->currency_name;
    $exchange_rate = $data['current']->sales_exchange_rate;
    $booking_no = $data['current']->sales_order_booking_id;
    $delivery_no = $data['current']->sales_delivery_id;
    $sale_type = isset($data['payment_type']->payment_type_name)?$data['payment_type']->payment_type_name:'';
    $payment_term = isset($data['payment_terms']->payment_term_name)?$data['payment_terms']->payment_term_name:'';
    $days = $data['current']->sales_credit_days;
    $notes = $data['current']->sales_remarks;
    $cashreceived = $data['current']->cashreceived;
    $change = $data['current']->change;
    $dtls = isset($data['current']->dtls)? $data['current']->dtls:[];
    $Expdtls = isset($data['current']->expense)? $data['current']->expense:[];
   // dump(auth()->user()->branch->branch_cr_no);
@endphp
@permission($data['permission'])
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
<title>{{$heading}}</title>
<link href="{{ asset('css/print.css') }}" rel="stylesheet" type="text/css" />
<style>
    @media print {
        html, body {
            width: 275px !important;
        }
    /* ... the rest of the rules ... */
    }
</style>
</head>
<body onload="print_document();" onafterprint="redirectBack();">
    <center>
        <div class="thermal">
            <table class="tab">
                <tr>
                    <td>
                        <table class="tab">
                            <tr>
                                <td>
                                    <img src="/images/{{ auth()->user()->business->business_profile }}" width="110px" height="90px" /><br>
                                    <span class="company aligncenter">{{auth()->user()->business->business_name}}</span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span class="thermal-normal aligncenter">{{auth()->user()->branch->branch_cr_no}}</span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span class="thermal-normal aligncenter">{{auth()->user()->branch->branch_intro}}</span>
                                </td>
                            </tr>
                            <tr>
                                <td colspan = '2'>
                                    <hr style="height:1px;border-width:0;color:#000;background-color:#000">
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <tr>
                        <table class="tab">
                            <tr>
                                <td width="30%" class="thermal-normal alignright">{{isset($code)?$code:''}}</td>
                                <td width="25%" class="thermal-heading alignright">  </td>
                                <td width="25%" class="thermal-normal alignright">{{isset($date)?$date:''}}</td>
                                <td width="15%" class="thermal-heading alignright">: {{$DateH}} </td>
                            </tr>
                            <tr>
                                <td width="25%" class="thermal-normal alignright">{{auth()->user()->name}}</td>
                                <td width="15%" class="thermal-heading alignright">: {{$userNameH}} </td>
                                <td width="20%" class="thermal-normal alignright">{{isset($sale_type)?$sale_type:''}}</td>
                                <td width="30%" class="thermal-heading alignright">: {{$TypeH }}</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="thermal-normal alignright">{{isset($notes)?$notes:''}}</td>
                                <td class="thermal-heading alignright">: {{$RemarksH }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td>
                        <table  class="tab" >
                            <thead>
                                <tr>
                                    <th width="20%" class="thermal-dtl-head">{{$AmountH}}</th>
                                    <th width="15%" class="thermal-dtl-head">{{$RateH}}</th>
                                    <th width="15%" class="thermal-dtl-head">{{$QtyH}}</th>
                                    <th width="50%" class="thermal-dtl-head">{{$ItmNameH}}</th>
                                </tr>
                            </thead>
                            <tbody>
                            @php
                                $totGrossAmt = 0;
                                $totQty = 0;
                                $i=0;
                            @endphp
                                @if(isset($dtls))
                                    @foreach($dtls as $data)
                                        @php
                                            $i++;
                                            $totGrossAmt += $data->sales_dtl_amount;
                                            $totQty += $data->sales_dtl_quantity;
                                        @endphp
                                        <tr style="border-bottom: 1px dashed #9a9a9a;">
                                            <td class="thermal-dtl-contents aligncenter">{{number_format($data->sales_dtl_amount,3)}}</td>
                                            <td class="thermal-dtl-contents aligncenter">{{number_format($data->sales_dtl_rate,3)}}</td>
                                            <td class="thermal-dtl-contents aligncenter">{{$data->sales_dtl_quantity}}</td>
                                            <td class="thermal-dtl-contents aligncenter">{{$data->product->product_arabic_name}}<br>{{$data->product->product_name}}<br>{{$data->barcode->product_barcode_barcode}}</td>
                                        </tr>
                                    @endforeach
                                @endif
                                <tr>
                                    <td class="thermal-dtl-head aligncenter">{{number_format($totGrossAmt,3)}}</td>
                                    <td class="thermal-dtl-head aligncenter"></td>
                                    <td class="thermal-dtl-head aligncenter">{{$totQty}}</td>
                                    <td class="thermal-dtl-head alignright">: {{$TotalH}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td>
                        <table class="tab">

                            <tr>
                                <td width="50%" class="thermal-normal alignright"> {{number_format($totGrossAmt,3)}}</td>
                                <td width="50%" class="thermal-heading alignright">: {{$PaidH }}</td>
                            </tr>
                            <tr>
                                <td width="50%" class="thermal-normal alignright">{{number_format($cashreceived,3)}}</td>
                                <td class="thermal-heading alignright">: {{$amount_receiveH }}</td>
                            </tr>
                            <tr>
                                <td width="50%" class="thermal-normal alignright">{{number_format($change,3)}}</td>
                                <td class="thermal-heading alignright">: {{$RemainingH }}</td>
                            </tr>
                            <tr>
                                <td colspan = '2'>
                                    <hr style="height:1px;border-width:0;color:#000;background-color:#000">
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" class="thermal-heading aligncenter">Thank You For Shopping With Us</td>
                            </tr>
                            <tr>
                                <td colspan="2" class="thermal-print-date aligncenter">{{date("d-m-Y h:i:s")}}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
    </center>
    <script>
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
