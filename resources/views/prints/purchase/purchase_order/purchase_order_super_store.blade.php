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
                    $headings = ['Sr No','Barcode','Product Name','UOM','Unit Price','Qty','Amount'];
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
{{-- Purchase Order Super Store  --}}
<table  class="tableData data_listing" id="document_table_data">
    <thead>
        <tr>
            <th width="3%" class="dtl-head">Sr No</th>
            <th width="4%" class="dtl-head">Barcode</th>
            <th width="15%" class="dtl-head alignleft">Product Name</th>
            <th width="3%" class="dtl-head">UOM</th>
            <th width="3%" class="dtl-head">Unit Price</th>
            <th width="3%" class="dtl-head">Net TP</th>
            <th width="3%" class="dtl-head">Qty</th>
            <th width="3%" class="dtl-head">Amount</th>
        </tr>
    </thead>
    <tbody>
        @php
        $totQty = 0;
        $totAmt = 0;
        $totNetAmt = 0;
        $totGstAmt = 0;
        $totDisc = 0;
        $totSpecDisc = 0;
        $total_discount = 0;
        $total_net = 0;
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
                @php
                $i++;
                $totQty += $row->purchase_order_dtlquantity;
                $totAmt += $row->purchase_order_dtlrate;
                $totNetAmt += $row->purchase_order_dtlamount;
                $totGstAmt += $row->purchase_order_dtlvat_amount;
                $totDisc += $row->purchase_order_dtldisc_amount;
                $totSpecDisc += $row->purchase_order_dtlspec_disc_amount;
                $total_discount = $totDisc + $totSpecDisc;
                $total_net = ($totNetAmt - $total_discount) + $totGstAmt;
                @endphp
            @endforeach
            @foreach($new_dtls as $key => $dtl)
                <tr>
                    <td class="dtl-contents alignleft" colspan="100%"><b>{{ $key }}</b></td>
                </tr>
                @foreach ($dtl as $item)
                    <tr>
                        <td class="dtl-contents aligncenter">{{ $loop->iteration }}</td>
                        <td class="dtl-contents aligncenter">{{$item->barcode->product_barcode_barcode}}</td>
                        <td class="dtl-contents alignleft">{{$item->product->product_name}}</td>
                        <td class="dtl-contents aligncenter">{{isset($item->uom->uom_name)}}</td>
                        <td class="dtl-contents alignright">{{number_format($item->purchase_order_dtlrate,3)}}</td>
                        <td class="dtl-contents aligncenter">{{$item->purchase_order_dtlnet_tp}}</td>
                        <td class="dtl-contents aligncenter">{{$item->purchase_order_dtlquantity}}</td>
                        <td class="dtl-contents alignright">{{number_format($item->purchase_order_dtlamount,3)}}</td>
                    </tr>
                @endforeach
            @endforeach
        @endif
    </tbody>
</table><br>
<table class="tab" valign="top">    
    <td width="37.5%">
        @if(isset($notes))
            <div>
                <span class="heading heading-block alignleft">Notes:</span>
            </div>
            <div>
                <span class="normal normal-block alignright">{{($notes)}}</span>
            </div>
        @endif        
    </td>    
    <td width="37.5%"></td>
    <td width="25%">
        <div>
            <span class="heading heading-block alignleft">Total Qty: <br></span>
            <span class="normal normal-block alignright">{{number_format($totQty,3)}}</span>
        </div>
        <div>
            <span class="heading heading-block alignleft">Total Amount</span>
            <span class="normal normal-block alignright">{{number_format($totNetAmt,3)}}</span>
        </div>
        <div>
            <span class="heading heading-block alignleft">Disc + Spec.Disc</span>
            <span class="normal normal-block alignright">{{number_format($total_discount,3)}}</span>
        </div>
        <div>
            <span class="heading heading-block alignleft">GST</span>
            <span class="normal normal-block alignright">{{number_format($totGstAmt,3)}}</span>
        </div>
        <div>
            <span class="heading heading-block alignleft">Net Amount</span>
            <span class="normal normal-block alignright">{{number_format($total_net,3)}}</span>
        </div>
    </td>
</table>
{{--Signature--}}
<table class="tab mrgn-top" valign="bottom">
    <tr>
        <th width="33.33%" class="heading aligncenter">
            <div style="height: 20px;">{{auth()->user()->name}}</div>
            <div class="hr_div_line">Prepared By</div>
        </th>
        <th width="33.33%" class="heading aligncenter">
            <div style="height: 20px;"></div>
            <div class="hr_div_line">Checked By</div>
        </th>
        <th width="33.33%" class="heading aligncenter">
            <div style="height: 20px;"></div>
            <div class="hr_div_line">Approved By</div>
        </th>
    </tr>
</table>
@endsection
@endpermission