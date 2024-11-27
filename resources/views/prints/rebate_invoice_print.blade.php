@php
    $heading = strtoupper($data['title']);
    $businessName = strtoupper(auth()->user()->business->business_short_name);
    $pdf_link = $data['print_link'];
    $print_type = $data['type'];

    $document_type = $data['current']->sales_type;
    $code= $data['current']->sales_code;
    $date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->sales_date))));
    $customer_name = isset($data['current']->supplier_view->supplier_name)?$data['current']->supplier_view->supplier_name:'';
    $customer_tax_no = isset($data['current']->supplier_view->supplier_tax_no)?$data['current']->supplier_view->supplier_tax_no:'';
    $cr_no = isset($data['current']->supplier_view->supplier_cr_no)?$data['current']->supplier_view->supplier_cr_no:'';
    $currency = isset($data['currency']->currency_name)?$data['currency']->currency_name:"";
    $exchange_rate = $data['current']->sales_exchange_rate;
    $delivery_no = $data['current']->sales_delivery_id;
    $sale_type = isset($data['payment_type']->payment_type_name)?$data['payment_type']->payment_type_name:'';
    $payment_term = isset($data['payment_terms']->payment_term_name)?$data['payment_terms']->payment_term_name:'';
    $days = $data['current']->sales_credit_days;
    $sales_contract_person = $data['current']->sales_contract_person;
    $notes = $data['current']->sales_remarks;
    $NetTotal = $data['current']->sales_net_amount;
    $dtls = isset($data['current']->dtls)? $data['current']->dtls:[];
    $Expdtls = isset($data['current']->expense)? $data['current']->expense:[];

// dd($data['current']->toArray());
@endphp
@permission($data['permission'])
@extends('layouts.print_layout')
@section('title', $heading)
@section('heading_tax', 'Tax Invoice')
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
                    <span class="heading heading-block">Date :</span>
                    <span class="normal normal-block">{{isset($date)?$date:''}}</span>
                </div>
                <div>
                    <span class="heading heading-block">Customer :</span>
                    <span class="normal normal-block">{{isset($customer_name)?$customer_name:''}}</span>
                </div>
                <div>
                    <span class="heading heading-block">Customer Tax No :</span>
                    <span class="normal normal-block">{{isset($customer_tax_no)?$customer_tax_no:''}}</span>
                </div>
                <div>
                    <span class="heading heading-block">CR No :</span>
                    <span class="normal normal-block">{{isset($cr_no)?$cr_no:''}}</span>
                </div>
                <div>
                    <span class="heading heading-block">Contract Person :</span>
                    <span class="normal normal-block">{{isset($sales_contract_person)?$sales_contract_person:''}}</span>
                </div>
            </td>
            <td width="33.33%"></td>
            <td width="33.33%">
                <div>
                    <span class="heading heading-block">Payment Terms:</span>
                    <span class="normal normal-block">{{isset($days)?$days:''}} {{isset($payment_term)?$payment_term:''}}</span>
                </div>
                <div>
                    <span class="heading heading-block">Currency:</span>
                    <span class="normal normal-block">{{isset($currency)?$currency:''}}</span>
                </div>
                <div>
                    <span class="heading heading-block">Exchange Rate:</span>
                    <span class="normal normal-block">{{isset($exchange_rate)?$exchange_rate:''}}</span>
                </div>
                <div>
                    <span class="heading heading-block">Sales Type:</span>
                    <span class="normal normal-block">{{isset($sale_type)?$sale_type:''}}</span>
                </div>
            </td>
        </tr>
        </tbody>
    </table>
    <table class="tableData" style="margin-top: 10px">
        <thead>
        <tr>
            <th width="5%" class="dtl-head">Sr No</th>
            <th width="20%" class="dtl-head">Item Description</th>
            <th width="10%" class="dtl-head">Purc Amount</th>
            <th width="5%" class="dtl-head">Start Date</th>
            <th width="5%" class="dtl-head">End Date</th>
            <th width="5%" class="dtl-head">Amount</th>
            <th width="5%" class="dtl-head">Vat Perc</th>
            <th width="5%" class="dtl-head">Vat Amt</th>
            <th width="5%" class="dtl-head">Gross Amt</th>
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
                         $i++;
                         $totAmt += $data->sales_dtl_amount;
                         $totVatAmt += $data->sales_dtl_vat_amount;
                         $totGrossAmt += $data->sales_dtl_total_amount;
                @endphp
                <tr>
                    <td class="dtl-contents aligncenter">{{ $loop->iteration }}</td>
                    <td class="dtl-contents alignleft">{{$data->item_description}}</td>
                    <td class="dtl-contents alignright">{{number_format($data->purc_amount,3)}}</td>
                    <td class="dtl-contents alignleft">{{(date('d-m-Y',strtotime($data->sales_dtl_start_date)) == '01-01-1970')?'':date('d-m-Y',strtotime($data->sales_dtl_start_date))}}</td>
                    <td class="dtl-contents alignleft">{{(date('d-m-Y',strtotime($data->sales_dtl_end_date)) == '01-01-1970')?'':date('d-m-Y',strtotime($data->sales_dtl_end_date))}}</td>
                    <td class="dtl-contents alignright">{{number_format($data->sales_dtl_amount,3)}}</td>
                    <td class="dtl-contents alignright">{{number_format($data->sales_dtl_vat_per,2)}}</td>
                    <td class="dtl-contents alignright">{{number_format($data->sales_dtl_vat_amount,3)}}</td>
                    <td class="dtl-contents alignright">{{number_format($data->sales_dtl_total_amount,3)}}</td>
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
                    {{\App\Library\Utilities::AmountWords($NetTotal)}}
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
                                        <th  colspan="2" class="heading alignleft">For:</th>
                                    </tr>
                                    <tr>
                                        <th style="padding-top: 40px" colspan="2" class="heading alignleft">{{auth()->user()->branch->branch_name}}</th>
                                    </tr>
                                    <tr>
                                        <th style="padding-top: 70px" colspan="2" class="heading aligncenter"><hr class="sign-line">Signature</th>
                                    </tr>
                                </table>
                            </td>
                            <td width="50%" valign="top">
                                <table class="tab">
                                    <tr>
                                        <th  colspan="2" class="heading alignleft">For:</th>
                                    </tr>
                                    <tr>
                                        <th style="padding-top: 40px"  class="heading alignleft">Salesman:</th>
                                        <th style="padding-top: 40px"  class="heading alignleft">______________________</th>
                                    </tr>
                                    <tr>
                                        <th style="padding-top: 70px" colspan="2" class="heading aligncenter"><hr class="sign-line">Signature</th>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
            <td width="30%" style="vertical-align: top;padding-top: 10px;">
                <table class="tab">
                    @php $totExpAmt = 0; @endphp
                    @if(isset($Expdtls))
                        @foreach($Expdtls as $expense)
                            @php $totExpAmt += $expense->sales_expense_amount; @endphp
                            <tr>
                                <td width="60%" class="heading alignleft">{{$expense->accounts->chart_name}}</td>
                                <td width="40%" class="heading alignright">{{number_format($expense->sales_expense_amount,3)}}</td>
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
