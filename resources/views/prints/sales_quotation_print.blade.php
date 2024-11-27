@php
//essential for header
    $heading = strtoupper($data['title']);
    $businessName = strtoupper(auth()->user()->business->business_short_name);
    $pdf_link = $data['print_link'];
    $print_type = $data['type'];

    $code = $data['current']->sales_order_code;
    $date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->sales_order_date))));
    $credit_days = $data['current']->sales_order_credit_days;
    $payment_term = isset($data['payment_terms']->payment_term_name)?$data['payment_terms']->payment_term_name:'';
    $currency = isset($data['currency']->currency_name)?$data['currency']->currency_name:"Omani Rial";
    $exchange_rate = isset($data['current']->sales_order_exchange_rate) ? $data['current']->sales_order_exchange_rate : 1;
    $customer_name = isset($data['current']->customer)?$data['current']->customer->customer_name:"";
    $users = isset($data['users']->name)?$data['users']->name:"";
    $mobile = $data['current']->sales_order_mobile_no;
    $selected_rate_type = isset($data['rate_types'][$data['current']->sales_order_rate_type]) ? $data['rate_types'][$data['current']->sales_order_rate_type] : '';
    $rate_perc = $data['current']->sales_order_rate_perc;
    $notes = $data['current']->sales_order_remarks;
    $dtls = isset($data['current']->dtls)? $data['current']->dtls :[];
    $Expdtls = isset($data['current']->expense)? $data['current']->expense:[];
@endphp
@permission($data['permission'])
    @extends('layouts.print_layout')
    @section('title', $heading)
    @section('heading', $heading)

    @section('pageCSS')
    @endsection

    @section('content')
    <table class="tableData" style="margin-top: 5px">
        <tbody>
            <tr>
                <td width="33.33%">
                    <div>
                        <span class="heading heading-block">Code :</span>
                        <span class="normal normal-block">{{isset($code)?$code:''}}</span>
                    </div>
                    <div>
                        <span class="heading heading-block">Date:</span>
                        <span class="normal normal-block">{{isset($date)?$date:''}}</span>
                    </div>
                    <div>
                        <span class="heading heading-block">Customer:</span>
                        <span class="normal normal-block">{{isset($customer_name)?$customer_name:''}}</span>
                    </div>
                    <div>
                        <span class="heading heading-block">Payment Terms:</span>
                        <span class="normal normal-block">{{isset($credit_days)?$credit_days:''}} {{isset($payment_term)?$payment_term:''}}</span>
                    </div>
                    <div>
                        <span class="heading heading-block">Mobile No:</span>
                        <span class="normal normal-block">{{isset($mobile)?$mobile:''}}</span>
                    </div>
                </td>
                <td width="33.33%"></td>
                <td width="33.33%">
                    <div>
                        <span class="heading heading-block">Currency:</span>
                        <span class="normal normal-block">{{isset($currency)?$currency:''}}</span>
                    </div>
                    <div>
                        <span class="heading heading-block">Exchange Rate:</span>
                        <span class="normal normal-block">{{isset($exchange_rate)?$exchange_rate:''}}</span>
                    </div>
                    <div>
                        <span class="heading heading-block">Salesman:</span>
                        <span class="normal normal-block">{{isset($users)?$users:''}}</span>
                    </div>
                    <div>
                        <span class="heading heading-block">Rate Type:</span>
                        <span class="normal normal-block">{{isset($rate_perc)?$rate_perc.'%':''}} {{isset($selected_rate_type)?$selected_rate_type:''}} </span>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
    <table  class="tableData data_listing" id="document_table_data" style="margin-top: 10px">
        <thead>
            <tr>
                <th width="4%" class="dtl-head">Sr No</th>
                <th width="10%" class="dtl-head">Barcode</th>
                <th width="12%" class="dtl-head alignleft">Product Name</th>
                <th width="5%" class="dtl-head">UOM</th>
                <th width="5%" class="dtl-head">Packing</th>
                <th width="6%" class="dtl-head">Qty</th>
                <th width="6%" class="dtl-head">FOC</th>
                <th width="6%" class="dtl-head">FC Rate</th>
                <th width="7%" class="dtl-head">Cost Rate</th>
                <th width="7%" class="dtl-head">Sale Rate</th>
                <th width="8%" class="dtl-head">Amount</th>
                <th width="7%" class="dtl-head">Disc Amt</th>
                <th width="7%" class="dtl-head">Vat Amt</th>
                <th width="9%" class="dtl-head">Gross Amt</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totQty = 0;
                $totAmt = 0;
                $totVatAmt = 0;
                $totDiscAmt = 0;
                $totGrossAmt = 0;
                $i=0;
            @endphp
            @if(isset($dtls))
                @foreach($dtls as $data)
                    @php
                        $i++;
                        $totQty += $data->sales_order_dtl_quantity;
                        $totAmt += $data->sales_order_dtl_amount;
                        $totDiscAmt += $data->sales_order_dtl_disc_amount;
                        $totVatAmt += $data->sales_order_dtl_vat_amount;
                        $totGrossAmt += $data->sales_order_dtl_total_amount;
                        $costRate = App\Models\TblPurcProductBarcodePurchRate::where('product_barcode_id',$data->product_barcode_id)->where('branch_id',auth()->user()->branch_id)->first('product_barcode_cost_rate');
                    @endphp
                    <tr>
                        <td class="dtl-contents aligncenter">{{ $loop->iteration }}</td>
                        <td class="dtl-contents aligncenter">{{$data->barcode->product_barcode_barcode}}</td>
                        <td class="dtl-contents alignleft">{{$data->product->product_name}}</td>
                        <td class="dtl-contents aligncenter">{{ isset($data->uom->uom_name) ? $data->uom->uom_name : $data->barcode->uom->uom_name }}</td>
                        <td class="dtl-contents aligncenter">{{$data->barcode->product_barcode_packing}}</td>
                        <td class="dtl-contents aligncenter">{{$data->sales_order_dtl_quantity}}</td>
                        <td class="dtl-contents aligncenter">{{$data->sales_order_dtl_foc_qty}}</td>
                        <td class="dtl-contents aligncenter">{{$data->sales_order_dtl_fc_rate}}</td>
                        <td class="dtl-contents alignright">{{number_format($costRate->product_barcode_cost_rate,3)}}</td>
                        <td class="dtl-contents alignright">{{number_format($data->sales_order_dtl_rate,3)}}</td>
                        <td class="dtl-contents alignright">{{number_format($data->sales_order_dtl_amount,3)}}</td>
                        <td class="dtl-contents alignright">{{number_format($data->sales_order_dtl_disc_amount,3)}}</td>
                        <td class="dtl-contents alignright">{{number_format($data->sales_order_dtl_vat_amount,3)}}</td>
                        <td class="dtl-contents alignright">{{number_format($data->sales_order_dtl_total_amount,3)}}</td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
    <table width="100%">
        <tbody>
        <tr>
            <td width="70%">
                <div style="font-weight:bold;font-size: 11px;">
                    {{\App\Library\Utilities::AmountWords($totGrossAmt)}}
                </div>
                <div style="font-size: 11px;margin-top: 10px">
                    <b>Notes:</b> {{isset($notes)?$notes:''}}
                </div>
                <div style="margin-top: 20px;">
                    <table class="tab">
                        <tr>
                            <td width="50%" valign="top">
                                <table class="tab">
                                    <tr>
                                        <th style="padding-top: 70px" colspan="2" class="heading aligncenter"><hr class="sign-line">Signature</th>
                                    </tr>
                                </table>
                            </td>
                            <td width="50%" valign="top">

                            </td>
                        </tr>
                    </table>
                </div>
            </td>
            <td width="30%">
                <table class="tab">
                    @php $totExpAmt = 0; @endphp
                    @if(isset($Expdtls))
                        @foreach($Expdtls as $expense)
                            @php $totExpAmt += $expense->sales_order_expense_amount; @endphp
                            <tr>
                                <td width="60%" class="heading alignleft">{{$expense->accounts->chart_name}}</td>
                                <td width="40%" class="heading alignright">{{number_format($expense->sales_order_expense_amount,3)}}</td>
                            </tr>
                        @endforeach
                    @endif
                    @php $netTot = $totExpAmt+$totGrossAmt; @endphp
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
        </tbody>
    </table>
    @endsection

    @section('customJS')
    @endsection
@endpermission
