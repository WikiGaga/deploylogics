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
@permission($data['permission'])
    @extends('layouts.po_print_layout')
    @section('title', $heading) 
    @section('heading', $heading)

    @section('pageCSS')
    <style>
        .center{
            text-align: center;
        }
    </style>
    @endsection

    @section('content')
    <table class="tableData" style="margin-top: 5px">
        <tbody>
            <tr>
                <td width="33.33%">
                    <div>
                        <span class="heading heading-block">Entry Date:</span>
                        <span class="normal normal-block">{{isset($date)?$date:''}}</span>
                    </div>
                    <div>
                        <span class="heading heading-block">Delivery Date:</span>
                        <span class="normal normal-block">{{isset($delivery_date)?$delivery_date:''}}</span>
                    </div>
                    <div>
                        <span class="heading heading-block">Code :</span>
                        <span class="normal normal-block">{{isset($code)?$code:''}}</span>
                    </div>
                    <div>
                        <span class="heading heading-block">Vendor:</span>
                        <span class="normal normal-block">{{isset($supplier_name)?$supplier_name:''}}</span>
                    </div>
                    <div>
                        <span class="heading heading-block">Vendor Contact#:</span>
                        <span class="normal normal-block">{{isset($phon_no)?$phon_no:''}}</span>
                    </div>
                    <div>
                        <span class="heading heading-block">Vendor GST:</span>
                        <span class="normal normal-block">{{isset($supplier_gst_no)?$supplier_gst_no:''}}</span>
                    </div>
                    <div>
                        <span class="heading heading-block">Vendor NTN:</span>
                        <span class="normal normal-block">{{isset($supplier_ntn_no)?$supplier_ntn_no:''}}</span>
                    </div>
                    <div>
                        <span class="heading heading-block">Vendor Address:</span>
                        <span class="normal normal-block">{{isset($address)?$address:''}}</span>
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
                        <span class="heading heading-block">Payment Terms:</span>
                        <span class="normal normal-block">{{isset($payment_term)?$payment_term:''}}</span>
                    </div>
                    <div>
                        <span class="heading heading-block">Credit Period:</span>
                        <span class="normal normal-block">{{isset($days)?$days:''}}</span>
                    </div>
                    <div>
                        <span class="heading heading-block">Invoice To:</span>
                        <span class="normal normal-block">Risen Cash & Carry</span>
                    </div>
                    {{-- <div>
                        <span class="heading heading-block">LPO Generation No:</span>
                        <span class="normal normal-block">{{isset($lpo_code)?$lpo_code:''}}</span>
                    </div>
                    <div>
                        <span class="heading heading-block">Comparative Quotation:</span>
                        <span class="normal normal-block">{{isset($comparative_quotation_code)?$comparative_quotation_code:''}}</span>
                    </div> --}}
                </td>
            </tr>
        </tbody>
    </table>
    @if($print_type != 'pdf')
        <div class="row" style="margin-top: 10px">
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
                                    'Amount','Disc %','Disc Amt','After Disc Amt','Tax on','GST %','GST Amt','FED %',
                                    'FED Amt','Disc On','Spec Disc %','Spec Disc Amt','Gross Amt','Net Amount',
                                    'Net TP','Last TP','Vend Last TP','TP Diff','GP %','GP Amt','Notes',
                                    'FC Rate','UOM','Packing'];
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
                    
                    <div class="table_column_dropdown_po">
                        @php
                        $headings = ['Sr No','Barcode','Product Name','UOM','Unit Price','Qty',
                                    'Amount'];
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

                    <div class="table_column_dropdown_mrp">
                        @php
                        $headings = ['Sr No','Barcode','Product Name','Qty','Unit Price','M.R.P',
                                    'Amount'];
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
    {{-- Main Print  --}}
    <table  class="tableData data_listing main_print" id="document_table_data" style="margin-top: 10px">
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
                {{-- <th width="7%" class="dtl-head">Vat Amt</th> --}}
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
            <tr>
                {{-- <td colspan="5" class="dtl-bottom text-center">Total</td>
                <td class="dtl-bottom aligcenter">{{$totQty}}</td>
                <td colspan="2" class="dtl-bottom"></td>
                <td class="dtl-bottom alignright">{{number_format($totAmt,3)}}</td>
                <td class="dtl-bottom alignright"></td>
                <td class="dtl-bottom alignright">{{number_format($totDiscAmt,3)}}</td>
                <td class="dtl-bottom alignright"></td>
                <td class="dtl-bottom alignright">{{number_format($totVatAmt,3)}}</td>
                <td class="dtl-bottom alignright">{{number_format($totGrossAmt,3)}}</td> --}}
            </tr>
        </tbody>
    </table>

    {{-- Purchase Order  --}}
    <table  class="tableData data_listing print" id="document_table_po" style="margin-top: 10px">
        <thead>
            <tr>
                <th width="3%" class="dtl-head">Sr No</th>
                <th width="4%" class="dtl-head">Barcode</th>
                <th width="15%" class="dtl-head alignleft">Product Name</th>
                <th width="3%" class="dtl-head">UOM</th>
                <th width="3%" class="dtl-head">Unit Price</th>
                <th width="3%" class="dtl-head">Qty</th>
                <th width="3%" class="dtl-head">Amount</th>
                {{-- <th width="7%" class="dtl-head">Vat Amt</th> --}}
            </tr>
        </thead>
        <tbody>
            @php
                $i=0;
                $totQty = 0;
                $totAmt = 0;
                $totNetAmt = 0;
            @endphp
            @php
            $new_dtls = [];
            @endphp
            @if(isset($dtls))
                @foreach($dtls as $data)
                    @php
                    $new_dtls[$data->product->group_item->group_item_name_string][] = $data;
                    @endphp
                @endforeach
                @foreach($new_dtls as $key => $dtl)
                    <tr>
                        <td class="dtl-contents alignleft" colspan="100%"><b>{{ $key }}</b></td>
                    </tr>
                    @foreach ($dtl as $pro)
                        @php
                        $i++;
                        $totQty += $pro->purchase_order_dtlquantity;
                        $totAmt += $pro->purchase_order_dtlrate;
                        $totNetAmt += $pro->purchase_order_dtlamount;
                        @endphp
                        <tr>
                            <td class="dtl-contents aligncenter">{{ $loop->iteration }}</td>
                            <td class="dtl-contents aligncenter">{{$pro->barcode->product_barcode_barcode}}</td>
                            <td class="dtl-contents alignleft">{{$pro->product->product_name}}</td>
                            <td class="dtl-contents aligncenter">{{isset($pro->uom->uom_name)}}</td>
                            <td class="dtl-contents alignright">{{number_format($pro->purchase_order_dtlrate,3)}}</td>
                            <td class="dtl-contents aligncenter">{{$pro->purchase_order_dtlquantity}}</td>
                            <td class="dtl-contents alignright">{{number_format($pro->purchase_order_dtlamount,3)}}</td>
                        </tr>
                    @endforeach
                @endforeach
            @endif
            <tr>
                <td class="dtl-bottom center">Total</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="dtl-bottom center">{{$totQty}}</td>
                <td class="dtl-bottom alignright">{{number_format($totNetAmt,3)}}</td>
            </tr>
        </tbody>
    </table>
    
    {{-- Purchase Order Super Store  --}}
    <table  class="tableData data_listing print" id="document_table_poss" style="margin-top: 10px">
        <thead>
            <tr>
                <th width="3%" class="dtl-head">Sr No</th>
                <th width="4%" class="dtl-head">Barcode</th>
                <th width="15%" class="dtl-head alignleft">Product Name</th>
                <th width="3%" class="dtl-head">UOM</th>
                <th width="3%" class="dtl-head">Unit Price</th>
                <th width="3%" class="dtl-head">Qty</th>
                <th width="3%" class="dtl-head">Amount</th>
            </tr>
        </thead>
        <tbody>
            @php
            $totQty = 0;
            $totAmt = 0;
            $totNetAmt = 0;
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
                    $totDisc += $row->dis_amount;
                    $totSpecDisc += $row->spec_disc_amount;
                    $total_discount = $totDisc + $totSpecDisc;
                    $total_net = $totNetAmt - $total_discount
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
                            <td class="dtl-contents aligncenter">{{$item->purchase_order_dtlquantity}}</td>
                            <td class="dtl-contents alignright">{{number_format($item->purchase_order_dtlamount,3)}}</td>
                        </tr>
                    @endforeach
                @endforeach
            @endif
            <tr>
                <td class="dtl-bottom center"></td>
                <td class="dtl-bottom center"></td>
                <td class="dtl-bottom center"></td>
                <td class="dtl-bottom center"></td>
                <td class="dtl-bottom center">Total</td>
                <td class="dtl-bottom center">{{$totQty}}</td>
                <td class="dtl-bottom alignright">{{number_format($totNetAmt,3)}}</td>
            </tr>
            <tr>
                <td class="dtl-bottom center"></td>
                <td class="dtl-bottom center"></td>
                <td class="dtl-bottom center"></td>
                <td class="dtl-bottom center"></td>
                <td class="dtl-bottom center"></td>
                <td class="dtl-bottom center">Disc + Spec.Disc <hr> Net Amount</td>
                <td class="dtl-bottom center">{{number_format($total_discount,3)}} <hr> {{number_format($total_net,3)}}</td>
            </tr>
            <tr>
                <td class="dtl-bottom alignleft" colspan="100%">
                    In Words: {{\App\Library\Utilities::AmountWords($total_net,$currency)}}
                </td>
            </tr>
        </tbody>
    </table>

    {{-- Purchase Order With MRP  --}}
    <table  class="tableData data_listing print" id="document_table_pom" style="margin-top: 10px" style="border-collapse:collapse;">
        <thead>
            <tr>
                <th width="3%" class="dtl-head">Sr No</th>
                <th width="4%" class="dtl-head">Barcode</th>
                <th width="15%" class="dtl-head alignleft">Product Name</th>
                <th width="3%" class="dtl-head">Qty</th>
                <th width="3%" class="dtl-head">Unit Price</th>
                <th width="3%" class="dtl-head">M.R.P</th>
                <th width="3%" class="dtl-head">Amount</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totQty = 0;
                $totAmt = 0;
                $totNetAmt = 0;
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
                    $totDisc += $row->purchase_order_dtldisc_amount;
                    $totSpecDisc += $row->purchase_order_dtlspec_disc_amount;

                    $total_discount = $totDiscAmt + $totSpecDiscAmt;
                    $total_net = $totNetAmt - $total_discount
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
                            <td class="dtl-contents aligncenter">{{$item->purchase_order_dtlquantity}}</td>
                            <td class="dtl-contents alignright">{{number_format($item->purchase_order_dtlrate,3)}}</td>
                            <td class="dtl-contents aligncenter">{{isset($item->purchase_order_dtlmrp)}}</td>
                            <td class="dtl-contents alignright">{{number_format($item->purchase_order_dtlamount,3)}}</td>
                        </tr>
                    @endforeach
                @endforeach
            @endif
            <tr>
                
                <td class="dtl-bottom center"></td>
                <td class="dtl-bottom center"></td>
                <td class="dtl-bottom center"></td>
                <td class="dtl-bottom center">{{$totQty}}</td>
                <td class="dtl-bottom alignright">{{number_format($totAmt,3)}}</td>
                <td class="dtl-bottom center"></td>
                <td class="dtl-bottom center"></td>
            </tr>
            <tr>
                <td class="dtl-bottom center"></td>
                <td class="dtl-bottom center"></td>
                <td class="dtl-bottom center"></td>
                <td class="dtl-bottom center"></td>
                <td class="dtl-bottom center"></td>
                <td class="dtl-bottom center">Disc + Spec.Disc <hr> Net Amount</td>
                <td class="dtl-bottom alignright">{{number_format($total_discount,3)}} <hr> {{number_format($total_net,3)}}</td>
            </tr>
            <tr>
                <td class="dtl-bottom" colspan="100%">
                    In Words: {{\App\Library\Utilities::AmountWords($total_net,$currency)}}
                </td>
            </tr>
        </tbody>
    </table>
    <table>
        {{-- <tr>
            <td class="normal-bold">
                {{\App\Library\Utilities::AmountWords($totNetAmt,$currency)}}
            </td>
        </tr> --}}
        <tr>
            <td width="45%" valign="top">
                <table class="tab">
                    <tr>
                        <td class="heading alignleft"></td>
                    </tr>
                    <tr>
                        <th class="heading alignleft">Terms And Condition :</th>
                    </tr>
                    <tr>
                        <td class="normal alignleft paddingNotes">{{$notes}}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <table class="tab fixed-layout print">
        <tr>
            <td width="25%"></td>
            {{-- <td width="30%">
                <table class="tab" valign="top">
                    <tr>
                        <td colspan="2">
                            <hr style="height:1px;border-width:0;color:#000;background-color:#000">
                            <hr style="height:2px;border-width:0;color:#000;background-color:#000">
                        </td>
                    </tr>
                    <tr>
                        <td class="heading alignleft" >Amt Total</td>
                        <td class="heading alignright">{{number_format($totAmt,3)}}</td>
                    </tr>
                    @if($totDiscAmt !='' || $totDiscAmt !=0)
                        <tr>
                            <td class="heading alignleft" >Disc Total</td>
                            <td class="heading alignright">{{number_format($totDiscAmt,3)}}</td>
                        </tr>
                    @endif
                    @if($totVatAmt !='' || $totVatAmt !=0)
                        <tr>
                            <td class="heading alignleft" >Vat Total</td>
                            <td class="heading alignright">{{number_format($totVatAmt,3)}}</td>
                        </tr>
                    @endif
                    <tr>
                        <td class="heading alignleft" >Net Total</td>
                        <td class="heading alignright">{{number_format($totGrossAmt,3)}}</td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <hr style="height:2px;border-width:0;color:#000;background-color:#000">
                        </td>
                    </tr>
                </table>
            </td> --}}
            <td width="30%">
                <table class="tab">
                        <tr>
                            <td colspan="2">
                                <hr style="height:1px;border-width:0;color:#000;background-color:#000">
                                <hr style="height:2px;border-width:0;color:#000;background-color:#000">
                            </td>
                        </tr>
                        <tr>
                            <td width="60%" class="heading alignleft">Total Qty</td>
                            <td width="40%" class="heading alignright">{{number_format($totQty,3)}}</td>
                        </tr>
                        <tr>
                            <td width="60%" class="heading alignleft">Rate Total</td>
                            <td width="40%" class="heading alignright">{{number_format($totAmt,3)}}</td>
                        </tr>
                        @if($totDiscAmt !='' || $totDiscAmt !=0)
                            <tr>
                                <td class="heading alignleft" >Disc Total</td>
                                <td class="heading alignright">{{number_format($totDiscAmt,3)}}</td>
                            </tr>
                        @endif
                        {{-- @if($totGstAmt !='' || $totGstAmt !=0)
                            <tr>
                                <td class="heading alignleft" >GST Total</td>
                                <td class="heading alignright">{{number_format($totGstAmt,3)}}</td>
                            </tr>
                        @endif --}}
                        {{-- @if($totFedAmt !='' || $totFedAmt !=0)
                            <tr>
                                <td class="heading alignleft" >FED Total</td>
                                <td class="heading alignright">{{number_format($totFedAmt,3)}}</td>
                            </tr>
                        @endif --}}
                        @if($totSpecDiscAmt !='' || $totSpecDiscAmt !=0)
                            <tr>
                                <td class="heading alignleft" >Special. Discount. Total</td>
                                <td class="heading alignright">{{number_format($totSpecDiscAmt,3)}}</td>
                            </tr>
                        @endif
                        <tr>
                            <td class="heading alignleft" >Net Total</td>
                            <td class="heading alignright">{{number_format($totNetAmt,3)}}</td>
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
    <table class="tab mrgn-top">
        <tr>
            <th width="33.33%" class="heading aligncenter">{{auth()->user()->name}}<hr class="sign-line"> Prepared By: </th>
            <th width="33.33%" class="heading aligncenter"><hr class="sign-line"> Checked By: </th>
            <th width="33.33%" class="heading aligncenter"><hr class="sign-line"> Approved By: </th>
        </tr>
    </table>
    @endsection

    @section('customJS')
    @endsection
@endpermission
