@php
//essential for header
    $heading = strtoupper($data['title']);
    $businessName = strtoupper(auth()->user()->business->business_short_name);
    $pdf_link = $data['print_link'];
    $print_type = $data['type'];
$document_type = $data['current']->sales_type;
    $code= $data['current']->purchase_order_code;
    $date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->purchase_order_entry_date))));
    $supplier_name = isset($data['current']->supplier->supplier_name)?$data['current']->supplier->supplier_name:'';
    $phon_no = isset($data['current']->supplier->supplier_phone_1)?$data['current']->supplier->supplier_phone_1:'';
    $cr_no = isset($data['current']->supplier->supplier_cr_no)?$data['current']->supplier->supplier_cr_no:'';
    $fax_no = isset($data['current']->supplier->supplier_fax)?$data['current']->supplier->supplier_fax:'';
    $tax_no = isset($data['current']->supplier->supplier_tax_no)?$data['current']->supplier->supplier_tax_no:'';
    $email = isset($data['current']->supplier->supplier_email)?$data['current']->supplier->supplier_email:'';
    $lpo_code = isset($data['current']->lpo)?$data['current']->lpo->lpo_code:"";
    $comparative_quotation_code = isset($data['current']->comparative_quotation)?$data['current']->comparative_quotation->comparative_quotation_code:"";
    $currency = $data['currency']->currency_name;
    $exchange_rate = $data['current']->purchase_order_exchange_rate;
    $bill_no = $data['current']->grn_bill_no;
    $store = isset($data['store']->store_name)?$data['store']->store_name:'';
    $payment_term = isset($data['payment_terms']->payment_term_name)?$data['payment_terms']->payment_term_name:'';
    $days = $data['current']->purchase_order_credit_days;
    $notes = $data['current']->purchase_order_remarks;
    $dtls = isset($data['current']->po_details)? $data['current']->po_details:[];
{{dd($document_type)}}
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
                        <span class="heading heading-block">Supplier:</span>
                        <span class="normal normal-block">{{isset($supplier_name)?$supplier_name:''}}</span>
                    </div>
                    <div>
                        <span class="heading heading-block">Supplier Tax No:</span>
                        <span class="normal normal-block">{{isset($tax_no)?$tax_no:''}}</span>
                    </div>
                    <div>
                        <span class="heading heading-block">Supplier Phone No:</span>
                        <span class="normal normal-block">{{isset($phon_no)?$phon_no:''}}</span>
                    </div>
                    <div>
                        <span class="heading heading-block">Supplier CR No:</span>
                        <span class="normal normal-block">{{isset($cr_no)?$cr_no:''}}</span>
                    </div>
                    <div>
                        <span class="heading heading-block">Supplier Fax No:</span>
                        <span class="normal normal-block">{{isset($fax_no)?$fax_no:''}}</span>
                    </div>
                    <div>
                        <span class="heading heading-block">Supplier Email:</span>
                        <span class="normal normal-block">{{isset($email)?$email:''}}</span>
                    </div>
                </td>
                <td width="33.33%"></td>
                <td width="33.33%">
                    <div>
                        <span class="heading heading-block">Date:</span>
                        <span class="normal normal-block">{{isset($date)?$date:''}}</span>
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
                        <span class="heading heading-block">Payment Terms:</span>
                        <span class="normal normal-block">{{isset($days)?$days:''}}{{isset($payment_term)?$payment_term:''}}</span>
                    </div>
                    <div>
                        <span class="heading heading-block">LPO Generation No:</span>
                        <span class="normal normal-block">{{isset($lpo_code)?$lpo_code:''}}</span>
                    </div>
                    <div>
                        <span class="heading heading-block">Comparative Quotation:</span>
                        <span class="normal normal-block">{{isset($comparative_quotation_code)?$comparative_quotation_code:''}}</span>
                    </div>
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
                            $headings = ['Sr No','Barcode','Product Name','UOM','Notes','Qty',
                                        'FOC Qty','Rate','Amount','Disc %','Disc Amt','VAT %','Vat Amt',
                                        'Gross Amt',];
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
    <table  class="tableData data_listing" id="document_table_data" style="margin-top: 10px">
        <thead>
            <tr>
                <th width="4%" class="dtl-head">Sr No</th>
                <th width="10%" class="dtl-head">Barcode</th>
                <th width="12%" class="dtl-head alignleft">Product Name</th>
                <th width="5%" class="dtl-head">UOM</th>
                <th width="7%" class="dtl-head alignleft">Notes</th>
                <th width="6%" class="dtl-head">Qty</th>
                <th width="6%" class="dtl-head">FOC</th>
                <th width="7%" class="dtl-head">Rate</th>
                <th width="8%" class="dtl-head">Amount</th>
                <th width="6%" class="dtl-head">Disc %</th>
                <th width="7%" class="dtl-head">Disc Amt</th>
                <th width="6%" class="dtl-head">VAT %</th>
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
                        $totQty += $data->purchase_order_dtlquantity;
                        $totAmt += $data->purchase_order_dtlamount;
                        $totDiscAmt += $data->purchase_order_dtldisc_amount;
                        $totVatAmt += $data->purchase_order_dtlvat_amount;
                        $totGrossAmt += $data->purchase_order_dtltotal_amount;
                    @endphp
                    <tr>
                        <td class="dtl-contents aligncenter">{{ $loop->iteration }}</td>
                        <td class="dtl-contents aligncenter">{{$data->barcode->product_barcode_barcode}}</td>
                        <td class="dtl-contents alignleft">{{$data->product->product_name}}</td>
                        <td class="dtl-contents aligncenter">{{$data->uom->uom_name}}</td>
                        <td class="dtl-contents alignleft">{{$data->purchase_order_dtl_remarks}}</td>
                        <td class="dtl-contents aligncenter">{{$data->purchase_order_dtlquantity}}</td>
                        <td class="dtl-contents aligncenter">{{$data->purchase_order_dtlfoc_quantity}}</td>
                        <td class="dtl-contents alignright">{{number_format($data->purchase_order_dtlrate,3)}}</td>
                        <td class="dtl-contents alignright">{{number_format($data->purchase_order_dtlamount,3)}}</td>
                        <td class="dtl-contents alignright">{{number_format($data->purchase_order_dtldisc_percent,3)}}</td>
                        <td class="dtl-contents alignright">{{number_format($data->purchase_order_dtldisc_amount,3)}}</td>
                        <td class="dtl-contents alignright">{{number_format($data->purchase_order_dtlvat_percent,3)}}</td>
                        <td class="dtl-contents alignright">{{number_format($data->purchase_order_dtlvat_amount,3)}}</td>
                        <td class="dtl-contents alignright">{{number_format($data->purchase_order_dtltotal_amount,3)}}</td>
                    </tr>
                @endforeach
            @endif
            <!--<tr>
                <td colspan="5" class="dtl-bottom alignright">Total</td>
                <td class="dtl-bottom aligcenter">{{$totQty}}</td>
                <td colspan="2" class="dtl-bottom"></td>
                <td class="dtl-bottom alignright">{{number_format($totAmt,3)}}</td>
                <td class="dtl-bottom alignright"></td>
                <td class="dtl-bottom alignright">{{number_format($totDiscAmt,3)}}</td>
                <td class="dtl-bottom alignright"></td>
                <td class="dtl-bottom alignright">{{number_format($totVatAmt,3)}}</td>
                <td class="dtl-bottom alignright">{{number_format($totGrossAmt,3)}}</td>
            </tr>-->
        </tbody>
    </table>
    <table>
        <tr>
            <td class="normal-bold">
                {{\App\Library\Utilities::AmountWords($totGrossAmt)}}
            </td>
        </tr>
    </table>
    <table class="tab fixed-layout">
        <tr>
            <td width="45%" valign="top">
                <table class="tab">
                    <tr>
                        <td class="heading alignleft"></td>
                    </tr>
                    <tr>
                        <td class="heading alignleft">Minimum Expiry Date Should Be 6  Months.</td>
                    </tr>
                    <tr>
                        <td class="heading alignleft">Goods Return Should Be Taken By The Supplier On Delivery.</td>
                    </tr>
                    <tr>
                        <td class="heading alignleft"></td>
                    </tr>
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
            <td width="25%"></td>
            <td width="30%">
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
            </td>
        </tr>
    </table>
    <table class="tab mrgn-top">
        <tr>
            <th width="20%" class="heading aligncenter"><hr class="sign-line">Signature</th>
            <th width="80%" class="heading aligncenter"></th>
        </tr>
    </table>
    @endsection

    @section('customJS')
    @endsection
@endpermission
