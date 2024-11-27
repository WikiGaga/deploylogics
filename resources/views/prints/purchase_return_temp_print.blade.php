@php
//essential for header
    $heading = strtoupper($data['title']);
    $businessName = strtoupper(auth()->user()->business->business_short_name);
    $pdf_link = $data['print_link'];
    $print_type = $data['type'];

    $code= $data['current']->grn_code;
    $document_type = $data['current']->grn_type;
    $date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->grn_date))));
    $supplier_name = isset($data['current']->supplier->supplier_name)?$data['current']->supplier->supplier_name:'';
    $phon_no = isset($data['current']->supplier->supplier_phone_1)?$data['current']->supplier->supplier_phone_1:'';
    $fax_no = isset($data['current']->supplier->supplier_fax)?$data['current']->supplier->supplier_fax:'';
    $tax_no = isset($data['current']->supplier->supplier_tax_no)?$data['current']->supplier->supplier_tax_no:'';
    $email = isset($data['current']->supplier->supplier_email)?$data['current']->supplier->supplier_email:'';
    $address = isset($data['current']->supplier->supplier_address)?$data['current']->supplier->supplier_address:'';
    $PO = isset($data['current']->PO->purchase_order_code)?$data['current']->PO->purchase_order_code:'';
    $currency = $data['currency']->currency_name;
    $exchange_rate = $data['current']->grn_exchange_rate;
    $bill_no = $data['current']->grn_bill_no;
    $store = isset($data['store']->store_name)?$data['store']->store_name:'';
    $payment_term = isset($data['payment_terms']->payment_term_name)?$data['payment_terms']->payment_term_name:'';
    $days = $data['current']->grn_ageing_term_value;
    $notes = $data['current']->grn_remarks;
    $NetTotal = $data['current']->grn_total_net_amount;
    $dtls = isset($data['current']->grn_dtl)? $data['current']->grn_dtl:[];
    $Expdtls = isset($data['current']->grn_expense)? $data['current']->grn_expense:[];
@endphp
@permission($data['permission'])
    @extends('layouts.print_layout')
    @section('title', $heading)
    @if($document_type == "PDS")
        @section('heading_tax', 'Tax Invoice')
    @endif
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
                        <span class="heading heading-block">Supplier Fax No:</span>
                        <span class="normal normal-block">{{isset($fax_no)?$fax_no:''}}</span>
                    </div>
                    <div>
                        <span class="heading heading-block">Supplier Email:</span>
                        <span class="normal normal-block">{{isset($email)?$email:''}}</span>
                    </div>
                    <div>
                        <span class="heading heading-block">Supplier Address:</span>
                        <span class="normal normal-block">{{isset($address)?$address:''}}</span>
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
                        <span class="heading heading-block">Store:</span>
                        <span class="normal normal-block">{{isset($store)?$store:''}}</span>
                    </div>
                    <div>
                        <span class="heading heading-block">Payment Terms:</span>
                        <span class="normal normal-block">{{isset($days)?$days:''}}{{isset($payment_term)?$payment_term:''}}</span>
                    </div>
                    <div>
                        <span class="heading heading-block">Bill No:</span>
                        <span class="normal normal-block">{{isset($bill_no)?$bill_no:''}}</span>
                    </div>
                    @if($document_type == 'GRN')
                    <div>
                        <span class="heading heading-block">PO:</span>
                        <span class="normal normal-block">{{isset($PO)?$PO:''}}</span>
                    </div>
                    @endif
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
                            $headings = ['Sr No','Barcode','Product Name','UOM','Packing','Qty',
                                                  'FOC Qty','Sale Rate','FC Rate','Rate','Amount','Disc%','Disc Amt','VAT%','Vat Amt',
                                                  'Batch #','Production Date','Expiry Date','Gross Amt',];
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
                <th width="3%" class="dtl-head">Sr No</th>
                <th width="9%" class="dtl-head alignleft">Barcode</th>
                <th width="12%" class="dtl-head alignleft">Product Name</th>
                <th width="3%" class="dtl-head">UOM</th>
                <th width="3%" class="dtl-head">Packing</th>
                <th width="5%" class="dtl-head">Qty</th>
                <th width="3%" class="dtl-head">FOC Qty</th>
                @if($document_type == 'GRN')
                <th width="5%" class="dtl-head">Sale Rate</th>
                @endif
                <th width="5%" class="dtl-head">FC Rate</th>
                <th width="5%" class="dtl-head">Rate</th>
                <th width="5%" class="dtl-head">Amount</th>
                <th width="5%" class="dtl-head">Disc %</th>
                <th width="5%" class="dtl-head">Disc Amt</th>
                <th width="5%" class="dtl-head">VAT %</th>
                <th width="5%" class="dtl-head">Vat Amt</th>
                <th width="5%" class="dtl-head">Batch #</th>
                <th width="5%" class="dtl-head">Production Date</th>
                <th width="5%" class="dtl-head">Expiry Date</th>
                <th width="7%" class="dtl-head">Gross Amt</th>
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
                        $Proddate= date('d-m-Y', strtotime(trim(str_replace('/','-',$data->tbl_purc_grn_dtl_production_date))));
                        $Expdate= date('d-m-Y', strtotime(trim(str_replace('/','-',$data->tbl_purc_grn_dtl_expiry_date))));
                        if($Proddate == '01-01-1970'){
                            $Proddate = '';
                        }
                        if($Expdate == '01-01-1970'){
                            $Expdate = '';
                        }
                        $i++;
                        $totQty += $data->tbl_purc_grn_dtl_quantity;
                        $totAmt += $data->tbl_purc_grn_dtl_amount;
                        $totDiscAmt += $data->tbl_purc_grn_dtl_disc_amount;
                        $totVatAmt += $data->tbl_purc_grn_dtl_vat_amount;
                        $totGrossAmt += $data->tbl_purc_grn_dtl_total_amount;
                    @endphp
                    <tr>
                        <td class="dtl-contents aligncenter">{{ $loop->iteration }}</td>
                        <td class="dtl-contents alignleft">{{$data->product_barcode_barcode}}</td>
                        <td class="dtl-contents alignleft">{{$data->product->product_name}}</td>
                        <td class="dtl-contents aligncenter">{{$data->uom->uom_name}}</td>
                        <td class="dtl-contents aligncenter">{{$data->barcode->product_barcode_packing}}</td>
                        <td class="dtl-contents aligncenter">{{$data->tbl_purc_grn_dtl_quantity}}</td>
                        <td class="dtl-contents aligncenter">{{$data->tbl_purc_grn_dtl_foc_quantity}}</td>
                        @if($document_type == 'GRN')
                        <td class="dtl-contents aligncenter">{{number_format($data->tbl_purc_grn_dtl_sale_rate,3)}}</td>
                        @endif
                        <td class="dtl-contents aligncenter">{{number_format($data->tbl_purc_grn_dtl_fc_rate,3)}}</td>
                        <td class="dtl-contents alignright">{{number_format($data->tbl_purc_grn_dtl_rate,3)}}</td>
                        <td class="dtl-contents alignright">{{number_format($data->tbl_purc_grn_dtl_amount,3)}}</td>
                        <td class="dtl-contents alignright">{{number_format($data->tbl_purc_grn_dtl_disc_percent,3)}}</td>
                        <td class="dtl-contents alignright">{{number_format($data->tbl_purc_grn_dtl_disc_amount,3)}}</td>
                        <td class="dtl-contents alignright">{{number_format($data->tbl_purc_grn_dtl_vat_percent,3)}}</td>
                        <td class="dtl-contents alignright">{{number_format($data->tbl_purc_grn_dtl_vat_amount,3)}}</td>
                        <td class="dtl-contents alignright">{{$data->tbl_purc_grn_dtl_batch_no}}</td>
                        <td class="dtl-contents aligncenter">{{$Proddate}}</td>
                        <td class="dtl-contents aligncenter">{{$Expdate}}</td>
                        <td class="dtl-contents alignright">{{number_format($data->tbl_purc_grn_dtl_total_amount,3)}}</td>
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
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        @if($document_type == 'GRN')
                        <td>&nbsp;</td>
                        @endif
                    </tr>
                @endfor
            @endif
        </tbody>
    </table>
    <table>
        <tr>
            <td class="normal-bold">
                {{\App\Library\Utilities::AmountWords($NetTotal)}}
            </td>
        </tr>
    </table>
    <table class="tab fixed-layout">
        <tr>
            <td width="45%" valign="top">
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
            <td width="25%"></td>
            <td width="30%">
                <table class="tab">
                        <tr>
                            <td colspan="2">
                                <hr style="height:1px;border-width:0;color:#000;background-color:#000">
                                <hr style="height:2px;border-width:0;color:#000;background-color:#000">
                            </td>
                        </tr>
                        <tr>
                            <td width="60%" class="heading alignleft">Amt Total</td>
                            <td width="40%" class="heading alignright">{{number_format($totAmt,3)}}</td>
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
                        @php
                            $totExpAmt = 0;
                        @endphp
                        {{-- @if(isset($Expdtls))
                            @foreach($Expdtls as $expense)
                                @php
                                    $plus_minus = '';
                                    if($expense->exp_acc_dtl->expense_accounts_plus_minus == '+'){
                                        $totExpAmt += $expense->grn_expense_amount;
                                    }else{
                                        $totExpAmt -= $expense->grn_expense_amount;
                                        $plus_minus = '-';
                                    }
                                @endphp
                                <tr>
                                    <td width="60%" class="heading alignleft">{{$expense->accounts->chart_name }}</td>
                                    <td width="40%" class="heading alignright">{{$plus_minus.number_format($expense->grn_expense_amount,3)}}</td>
                                </tr>
                            @endforeach
                        @endif --}}
                        @php
                            $netTot = $totAmt - $totDiscAmt + $totVatAmt + $totExpAmt;
                        @endphp
                        <tr>
                            <td class="heading alignleft" >Net Total</td>
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
