@php
//essential for header
    $heading = strtoupper($data['title']);
    $businessName = strtoupper(auth()->user()->business->business_short_name);
    $pdf_link = $data['print_link'];
    $print_type = $data['type'];
    $document_type = $data['current']->sales_type;
    $code= $data['current']->sales_code;
    $date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->sales_date))));
    $customer_name = isset($data['current']->customer_view->customer_name)?$data['current']->customer_view->customer_name:'';
    $customer_code = isset($data['current']->customer->customer_code)?$data['current']->customer->customer_code:'';
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
    // Hide The Default 
    $hide_default_header = true;
@endphp
@permission($data['permission'])
    @extends('layouts.print_layout_laundry')
    @section('title', $heading)

    @section('pageCSS')
    <style>
        #Top-Header{
            width: 72mm;
        }
        body{
            margin: 0 auto;
            width: 72mm;
        }
        .d-inline-block{
            display: inline-block;
        }
        @media print{
            @page {
                margin-top: 0mm;
                margin-bottom: 0mm;
                margin-left: -2.645mm;
                margin-right: 0mm;
            }
            body{
                /* max-width: 270px; */
                width: 100%;
                transform: scale(0.85);
                margin:none;
            }
            .headerHeight{height: 10px !important;}
        }
    </style>
    @endsection

    @section('content')
    <table class="tab" style="width:72mm;">
        <tr>
            <td>
                <tr>
                    @php
                        $path = asset('/images/' . auth()->user()->branch->branch_logo);
                        if(file_exists($path)){
                            $base64 =  $path;
                            $type = pathinfo($path, PATHINFO_EXTENSION);
                            $data = file_get_contents($path);
                            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                            $path = $base64;
                        }
                    @endphp
                    <td class="textleft" style="text-align: left;"><img src="{{ $path }}" width="110px" height="90px" /></td>
                    <td class="title aligncenter">{{$heading}}</td>
                </tr>
            </td>
        </tr>
        <table>
            <tbody>
                <tr>
                    <td class="title aligncenter" style="font-weight:normal;font-size: 16px;width: 272px;font-weight: bold;">النورس لغسيل السجاد والفرش</td>
                </tr>
                <tr>
                    <td class="title aligncenter" style="font-weight:normal; font-size:14px;text-transform:capitalize;">{{auth()->user()->branch->branch_name}}</td>
                </tr>
                <tr>
                    <td class="title aligncenter" style="font-weight:normal; font-size:14px;">TEL : 90900212</td>
                </tr>
                <tr>
                    <td class="title aligncenter" style="font-weight:normal; font-size:14px;">C.R. No : س ت.  1387261</td>
                </tr>
            </tbody>
        </table>
        <tr>
            <tr>
                <table class="tabData" style="width: auto !important;">
                    <tbody style="width: 272px;display:inline-block;">
                        <tr class="alignleft">
                            <td class="normal alignleft" style="width:55%">Bill # {{isset($code)?$code:''}}</td>
                            <td class="normal alignleft">Date: {{isset($date)?$date:''}}</td>
                        </tr>
                        <tr>
                            <td class="normal alignleft">Customer Code: {{isset($customer_code)?$customer_code:''}}</td>
                        </tr>
                        <tr>
                            <td class="normal alignleft">Customer: {{isset($customer_name)?$customer_name:''}} </td>
                        </tr>
                        @if(isset($days))
                            <tr>
                                <td class="normal alignleft">Payment Terms: {{isset($days)?$days:''}}{{isset($payment_term)?$payment_term:''}}</td>
                            </tr>
                        @endif
                        @if(isset($mobile))
                            <tr>
                                <td class="normal alignleft">Mobile No: {{isset($mobile)?$mobile:''}}</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </td>
        </tr>
        <tr><td>&nbsp;</td></tr>
        <tr>
            <td>
                <table class="tabData" style="width: 272px;border:none;">
                    <thead style="border-top:2px solid #000;border-bottom:2px solid #000;">
                        <tr style="width: 272px;display:inline-flex;">
                            <th style="font-weight: bold;font-size:14px;width:53px;text-align:left;">الإجمالي</th>
                            <th style="font-weight: bold;font-size:14px;width:40px;">الضريبة</th>
                            <th style="font-weight: bold;font-size:14px;width:45px;">السعر</th>
                            <th style="font-weight: bold;font-size:14px;width:45px;">الكمية</th>
                            <th style="font-weight: bold;font-size:14px;width:89px;text-align: right;">العمادة</th>
                        </tr>
                        <tr style="width: 272px;display:inline-flex;">
                            <th style="font-weight: bold;font-size:10px;width:53px;">AMOUNT</th>
                            <th style="font-weight: bold;font-size:10px;width:40px;">VAT</th>
                            <th style="font-weight: bold;font-size:10px;width:45px;text-align: center;">PRICE</th>
                            <th style="font-weight: bold;font-size:10px;width:45px;text-align: center;">QTY</th>
                            <th style="font-weight: bold;font-size:10px;width:89px;text-align: right;">ITEM</th>
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
                                    $totAmt += $data->sales_dtl_amount;
                                    $totDiscAmt += $data->sales_dtl_disc_amount;
                                    $totVatAmt += $data->sales_dtl_vat_amount;
                                    $totGrossAmt += $data->sales_dtl_total_amount;
                                @endphp
                                <tr style="width:262px;display:inline-block;padding:0px 5px;">
                                    <td style="width:54.4px;font-size:11px;text-align:left;">{{number_format($data->sales_dtl_total_amount,3)}}</td>
                                    <td style="width:108.8px;font-size:14px;text-align:left;">{{$data->product->product_name}}</td>
                                    <td style="width:108.8px;font-size:14px;text-align:right;">{{$data->product->product_arabic_name}}</td>
                                </tr>
                                <tr style="width:262px;display:inline-block;padding:0px 5px;">
                                    <td style="width:136px;font-size:14px;text-align:right;">{{$data->sales_dtl_width}} X {{$data->sales_dtl_length}}</td>
                                    <td style="width:136px;font-size:14px;text-align:right;">{{$data->uom->uom_name}}</td>
                                </tr>
                                <tr style="width:262px;display:inline-block;padding:0px 5px;border-bottom:1px dotted;">
                                    <td style="font-weight: normal;font-size:12px;width:53px;"></td>
                                    <td style="font-weight: normal;font-size:11px;width:40px;">{{number_format($data->sales_dtl_vat_amount,3)}}</td>
                                    <td style="font-weight: normal;font-size:11px;width:45px;text-align: center;">{{number_format($data->sales_dtl_rate,3)}}</td>
                                    <td style="font-weight: normal;font-size:11px;width:45px;text-align: center;">{{$data->sales_dtl_quantity}}</td>
                                    <td style="font-weight: normal;font-size:14px;width:89px;text-align: right;">{{$data->sales_dtl_barcode}}</td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </td>
        </tr>
        <tr style="width:262px;display:inline-block;padding:0px 5px;">
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr style="width:262px;display:inline-block;padding:0px 5px;">
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr style="width:262px;display:inline-block;padding:0px 5px;border-bottom:1px solid;">
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td>
                <table class="tab" style="width:272px;">
                    <tr>
                        <td width="50%" valign="top">
                            <table class="tab">
                                @if(isset($notes))
                                    <tr>
                                        <th class="heading alignleft">Notes:</th>
                                    </tr>
                                    <tr>
                                        <td class="normal alignleft">{{$notes}}</td>
                                    </tr>
                                @endif
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
    </table>
    @endsection

    @section('customJS')
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
    @endsection
@endpermission
