@extends('layouts.po_print_layout')
@section('pageCSS')

@endsection
@permission($data['permission'])
@php
    //essential for header
    $heading = strtoupper($data['title']);
    $businessName = strtoupper(auth()->user()->business->business_short_name);
    $pdf_link = $data['print_link'];
    $print_type = $data['type'];
    // dd($data['current']);
    $id= $data['current']->purchase_order_id;
    $code= $data['current']->purchase_order_code;
    $date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->purchase_order_entry_date))));
    $delivery_date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->purchase_order_delivery_date))));
    $supplier_name = isset($data['current']->supplier->supplier_name)?$data['current']->supplier->supplier_name:'';
    $phon_no = isset($data['current']->supplier->supplier_phone_1)?$data['current']->supplier->supplier_phone_1:'';
    $fax_no = isset($data['current']->supplier->supplier_fax)?$data['current']->supplier->supplier_fax:'';
    $supplier_gst_no = isset($data['current']->supplier->supplier_gst_no)?$data['current']->supplier->supplier_gst_no:'';
    $supplier_ntn_no = isset($data['current']->supplier->supplier_ntn_no)?$data['current']->supplier->supplier_ntn_no:'';
    $tax_no = isset($data['current']->supplier->supplier_tax_no)?$data['current']->supplier->supplier_tax_no:'';
    $email = isset($data['current']->supplier->supplier_email)?$data['current']->supplier->supplier_email:'';
    $address = isset($data['current']->supplier->supplier_address)?$data['current']->supplier->supplier_address:'';
    // $lpo_code = isset($data['current']->lpo)?$data['current']->lpo->lpo_code:"";
    // $comparative_quotation_code = isset($data['current']->comparative_quotation)?$data['current']->comparative_quotation->comparative_quotation_code:"";
    $currency = $data['currency']->currency_name;
    $exchange_rate = $data['current']->purchase_order_exchange_rate;
    $bill_no = $data['current']->grn_bill_no;
    $store = isset($data['store']->store_name)?$data['store']->store_name:'';
    $payment_term = isset($data['payment_terms']->payment_term_name)?$data['payment_terms']->payment_term_name:'';
    $days = $data['current']->purchase_order_credit_days;
    $notes = $data['current']->purchase_order_remarks;
    $dtls = isset($data['current']->po_details)? $data['current']->po_details:[];
@endphp
@section('title', $heading)
@section('page_heading', $heading)
@section('headings')
@if($print_type != 'pdf')
<div class="row table_column_dropdown_dots" style="margin-top: 10px">
    <div class="col-lg-12">
        <div class="toggle_table_column">
            <div class="hiddenFiledsCount" style="display: inline-block;font-size: 11px;color: #7b7b7b;"><span>0</span> fields hide</div>
            <button type="button" class="btn " id="btn_toggle">
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <rect x="0" y="0" width="24" height="24"/>
                        <circle fill="#5d78ff" cx="12" cy="5" r="2"/>
                        <circle fill="#5d78ff" cx="12" cy="12" r="2"/>
                        <circle fill="#5d78ff" cx="12" cy="19" r="2"/>
                    </g>
                </svg>
            </button>
            <div class="table_column_dropdown">
                @php
                    $headings = ['Sr No','Barcode','Product Name','Unit Price','Sale Rate','Qty','Sys Qty','M.R.P',
                    'Amount','Dis%','Disc Amt','After Disc Amt','Tax On','GST%','GST Amt','FED%','FED Amt',
                    'Disc On','Spec Disc%','Spec Disc Amt','Gross Amt','Net Amount','Net Tp','Last Tp','Vend Last Tp',
                    'Tp Diff','GP%','GP Amt','Notes','FC Rate','UOM','Packing'];
                @endphp
                <ul class="table_column_dropdown-menu listing_dropdown" style="display: none;">
                    @foreach($headings as $key=>$heading)
                        <li >
                            <label>
                                <input value="{{$key}}" name="{{trim($key)}}" type="checkbox" checked> {{$heading}}
                            </label>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
@endif
@endsection
@section('content')
{{--table content--}}
<table class="tabData data_listing" id="document_table_data">
    <thead>
        <tr>
            <th width="3%" class="dtl-head">Sr No</th>
            <th width="4%" class="dtl-head">Barcode</th>
            <th width="15%" class="dtl-head alignleft">Product Name</th>
            <th width="3%" class="dtl-head">Unit Price</th>
            <th width="3%" class="dtl-head">Sale Rate</th>
            <th width="3%" class="dtl-head">Qty</th>
            <th width="3%" class="dtl-head">Sys Qty</th>
            <th width="3%" class="dtl-head">M.R.P</th>
            <th width="3%" class="dtl-head">Amount</th>
            <th width="3%" class="dtl-head">Disc %</th>
            <th width="3%" class="dtl-head">Disc Amt</th>
            <th width="3%" class="dtl-head">After Disc Amt</th>
            <th width="3%" class="dtl-head">Tax On</th>
            <th width="3%" class="dtl-head">GST %</th>
            <th width="3%" class="dtl-head">GST Amt</th>
            <th width="3%" class="dtl-head">FED %</th>
            <th width="3%" class="dtl-head">FED Amt</th>
            <th width="3%" class="dtl-head">Disc On</th>
            <th width="3%" class="dtl-head">Spec Disc %</th>
            <th width="3%" class="dtl-head">Spec Disc Amt</th>
            <th width="3%" class="dtl-head">Gross Amt</th>
            <th width="3%" class="dtl-head">Net Amount</th>
            <th width="3%" class="dtl-head">Net TP</th>
            <th width="3%" class="dtl-head">Last TP</th>
            <th width="3%" class="dtl-head">Vend Last TP</th>
            <th width="3%" class="dtl-head">TP Diff</th>
            <th width="3%" class="dtl-head">GP %</th>
            <th width="3%" class="dtl-head">GP Amt</th>
            <th width="3%" class="dtl-head alignleft">Notes</th>
            <th width="3%" class="dtl-head">FC Rate</th>
            <th width="3%" class="dtl-head">UOM</th>
            <th width="3%" class="dtl-head">Packing</th>
        </tr>
    </thead>
    <tbody>
        @php
            $totQty = 0;
            $totAmt = 0;
            $totDiscAmt = 0;
            $totGstAmt = 0;
            $totFedAmt = 0;
            $totSpecDiscAmt = 0;
            $totGrossAmt = 0;
            $totNetAmt = 0;
            $totVatAmt = 0;
            $i=0;
        @endphp
        @php
        $new_dtls = [];
        @endphp
        @if(isset($dtls))
            @foreach($dtls as $row)
                @php
                $new_dtls[$row->product->group_item->group_item_name_string][] = $row;
                @endphp
            @endforeach
            @foreach($new_dtls as $key => $dtl)
                @php
                    $i++;
                    $totQty += $row->purchase_order_dtlquantity;
                    $totAmt += $row->purchase_order_dtlrate;
                    $totDiscAmt += $row->purchase_order_dtldisc_amount;
                    $totGstAmt += $row->purchase_order_dtlgst_amount;
                    $totFedAmt += $row->purchase_order_dtlfed_amount;
                    $totSpecDiscAmt += $row->purchase_order_dtlspec_disc_amount;
                    $totNetAmt += $row->purchase_order_dtlamount;
                @endphp
                <tr>
                    <td class="dtl-contents alignleft" colspan="100%"><b>{{ $key }}</b></td>
                </tr>
                @foreach ($dtl as $item)
                    <tr>
                        <td class="dtl-contents aligncenter">{{ $loop->iteration }}</td>
                        <td class="dtl-contents aligncenter">{{$item->barcode->product_barcode_barcode}}</td>
                        <td class="dtl-contents alignleft">{{$item->product->product_name}}</td>
                        <td class="dtl-contents alignright">{{number_format($item->purchase_order_dtlrate,3)}}</td>
                        <td class="dtl-contents alignright">{{number_format($item->purchase_order_dtlsale_rate,3)}}</td>
                        <td class="dtl-contents aligncenter">{{$item->purchase_order_dtlquantity}}</td>
                        <td class="dtl-contents aligncenter">{{$item->purchase_order_dtlsys_quantity}}</td>
                        <td class="dtl-contents alignright">{{number_format($item->purchase_order_dtlmrp,3)}}</td>
                        <td class="dtl-contents alignright">{{number_format($item->purchase_order_dtlamount,3)}}</td>
                        <td class="dtl-contents alignright">{{number_format($item->purchase_order_dtldisc_percent,3)}}</td>
                        <td class="dtl-contents alignright">{{number_format($item->purchase_order_dtldisc_amount,3)}}</td>
                        <td class="dtl-contents alignright">{{number_format($item->purchase_order_dtlafter_dis_amount,3)}}</td>
                        <td class="dtl-contents aligncenter">{{$item->purchase_order_dtltax_on}}</td>
                        <td class="dtl-contents aligncenter">{{$item->purchase_order_dtlvat_percent}}</td>
                        <td class="dtl-contents alignright">{{number_format($item->purchase_order_dtlvat_amount,3)}}</td>
                        <td class="dtl-contents aligncenter">{{$item->purchase_order_dtlfed_perc}}</td>
                        <td class="dtl-contents alignright">{{number_format($item->purchase_order_dtlfed_amount,3)}}</td>
                        <td class="dtl-contents alignright">{{$item->purchase_order_dtldisc_on}}</td>
                        <td class="dtl-contents aligncenter">{{$item->purchase_order_dtlspec_disc_perc}}</td>
                        <td class="dtl-contents alignright">{{number_format($item->purchase_order_dtlspec_disc_amount,3)}}</td>
                        <td class="dtl-contents alignright">{{number_format($item->purchase_order_dtlgross_amount,3)}}</td>
                        <td class="dtl-contents alignright">{{number_format($item->purchase_order_dtltotal_amount,3)}}</td>
                        <td class="dtl-contents aligncenter">{{$item->purchase_order_dtlnet_tp}}</td>
                        <td class="dtl-contents aligncenter">{{$item->purchase_order_dtllast_tp}}</td>
                        <td class="dtl-contents aligncenter">{{$item->purchase_order_dtlvend_last_tp}}</td>
                        <td class="dtl-contents aligncenter">{{$item->purchase_order_dtltp_diff}}</td>
                        <td class="dtl-contents alignright">{{number_format($item->purchase_order_dtlgp_perc,3)}}</td>
                        <td class="dtl-contents alignright">{{number_format($item->purchase_order_dtlgp_amount,3)}}</td>
                        <td class="dtl-contents alignleft">{{$item->purchase_order_dtl_remarks}}</td>
                        <td class="dtl-contents aligncenter">{{$item->purchase_order_dtlfc_rate}}</td>
                        <td class="dtl-contents aligncenter">{{isset($item->uom->uom_name)}}</td>
                        <td class="dtl-contents alignright">{{number_format($item->purchase_order_dtlpacking,3)}}</td>
                    </tr>
                @endforeach
            @endforeach                
        @endif
    </tbody>
</table>
{{--form remarks--}}
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
{{--Signature--}}
<table class="tab mrgn-top" valign="bottom">
    <tr>
        <th width="33%" class="heading aligncenter">
            <div style="height: 20px;">{{auth()->user()->name}}</div>
            <div class="hr_div_line">Prepared By</div>
        </th>
        <th width="33%" class="heading aligncenter">
            <div style="height: 20px;"></div>
            <div class="hr_div_line">Checked By</div>
        </th>
        <th width="33%" class="heading aligncenter">
            <div style="height: 20px;"></div>
            <div class="hr_div_line">Approved By</div>
        </th>
    </tr>
</table>
@endsection
@endpermission