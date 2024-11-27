@php
//essential for header
    $heading = strtoupper($data['title']);
    $businessName = strtoupper(auth()->user()->business->business_short_name);
    $pdf_link = $data['print_link'];
    $print_type = $data['type'];

    $code= $data['current']->grn_code;
    $id= $data['current']->grn_id;
    $document_type = $data['current']->grn_type;
    $overall_disc_amount = $data['current']->grn_overall_disc_amount;
    $overall_tax_amount = $data['current']->grn_advance_tax_amount;
    $overall_total_amount = $data['current']->grn_total_amount;
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
    $perc_return_ref = isset($data['current']->refPurcReturn->grn_code) ?  $data['current']->refPurcReturn->grn_code : "";
@endphp
@permission($data['permission'])
    @extends('layouts.grn_print_layout')
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
                        <span class="heading heading-block">Date:</span>
                        <span class="normal normal-block">{{isset($date)?$date:''}}</span>
                    </div>
                    <div>
                        <span class="heading heading-block">Vendor:</span>
                        <span class="normal normal-block">{{isset($supplier_name)?$supplier_name:''}}</span>
                    </div>
                    {{-- <div>
                        <span class="heading heading-block">Vendor Tax No:</span>
                        <span class="normal normal-block">{{isset($tax_no)?$tax_no:''}}</span>
                    </div> --}}
                    <div>
                        <span class="heading heading-block">Vendor Phone No:</span>
                        <span class="normal normal-block">{{isset($phon_no)?$phon_no:''}}</span>
                    </div>
                    {{-- <div>
                        <span class="heading heading-block">Vendor Fax No:</span>
                        <span class="normal normal-block">{{isset($fax_no)?$fax_no:''}}</span>
                    </div> --}}
                    <div>
                        <span class="heading heading-block">Vendor Email:</span>
                        <span class="normal normal-block">{{isset($email)?$email:''}}</span>
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
                    {{-- @if($document_type == 'GRN')
                    <div>
                        <span class="heading heading-block">PO:</span>
                        <span class="normal normal-block">{{isset($PO)?$PO:''}}</span>
                    </div>
                    @endif
                    @if($document_type == 'PR')
                    <div>
                        <span class="heading heading-block">Reference No:</span>
                        <span class="normal normal-block">{{$perc_return_ref}}</span>
                    </div>
                    @endif --}}
                </td>
            </tr>
        </tbody>
    </table>
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
                            $headings = ['Sr No','Barcode','Product Name','Qty','Rate','Sys Qty','M.R.P','Amount',
                            'Disc%','Disc Amt','After Disc Amt','Tax On','GST%','GST Amt','FED%','FED Amt','Disc On',
                            'Spec Disc%','Spec Disc Amount','Gross Amt','Net Amt','Net Tp','Last Tp','Vend Last Tp',
                            'Tp Diff','GP%','GP Amount','Notes','FC Rate','UOM','Packing'];
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
    <table  class="tableData data_listing main_print" id="document_table_data" style="margin-top: 10px">
        <thead>
            <tr>
                <th width="3%" class="dtl-head">Sr No</th>
                <th width="9%" class="dtl-head alignleft">Barcode</th>
                <th width="12%" class="dtl-head alignleft">Product Name</th>
                <th width="5%" class="dtl-head">Qty</th>
                <th width="5%" class="dtl-head">Rate</th>
                @if($document_type == 'GRN')
                <th width="5%" class="dtl-head">Sale Rate</th>
                @endif
                <th width="5%" class="dtl-head">Sys Qty</th>
                <th width="5%" class="dtl-head">M.R.P</th>
                <th width="5%" class="dtl-head">Amount</th>
                <th width="5%" class="dtl-head">Disc %</th>
                <th width="5%" class="dtl-head">Disc Amt</th>
                <th width="5%" class="dtl-head">After Disc Amt</th>
                <th width="5%" class="dtl-head">Tax On</th>
                <th width="5%" class="dtl-head">GST %</th>
                <th width="5%" class="dtl-head">GST Amt</th>
                <th width="5%" class="dtl-head">FED %</th>
                <th width="5%" class="dtl-head">FED Amt</th>
                <th width="5%" class="dtl-head">Disc on</th>
                <th width="5%" class="dtl-head">Spec Disc %</th>
                <th width="5%" class="dtl-head">Spec Disc Amount</th>
                <th width="7%" class="dtl-head">Gross Amt</th>
                <th width="7%" class="dtl-head">Net Amt</th>
                <th width="7%" class="dtl-head">Net Tp</th>
                <th width="7%" class="dtl-head">Last Tp</th>
                <th width="7%" class="dtl-head">Vend Last Tp</th>
                <th width="7%" class="dtl-head">Tp Diff</th>
                <th width="7%" class="dtl-head">GP %</th>
                <th width="7%" class="dtl-head">GP Amount</th>
                <th width="7%" class="dtl-head">Notes</th>
                <th width="7%" class="dtl-head">FC Rate</th>
                <th width="3%" class="dtl-head">UOM</th>
                <th width="3%" class="dtl-head">Packing</th>
            </tr>
        </thead>
        <tbody>
        @php
            $net_retail = 0;
            $totSaleRate = 0;
            $totQty = 0;
            $totAmt = 0;
            $totGstAmt = 0;
            $totDiscAmt = 0;
            $totGrossAmt = 0;
            $totFedAmt = 0;
            $totSpecDiscAmt = 0;
            $totNetAmt = 0;
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
                        $totGstAmt += $data->tbl_purc_grn_dtl_vat_amount;
                        $totFedAmt += $data->tbl_purc_grn_dtl_fed_amount;
                        $totSpecDiscAmt += $data->tbl_purc_grn_dtl_spec_disc_amount;
                        $totNetAmt += $data->tbl_purc_grn_dtl_net_amount;

                        $totSaleRate = $data->tbl_purc_grn_dtl_quantity * $data->tbl_purc_grn_dtl_sale_rate;
                        $net_retail += $totSaleRate;
                    @endphp
                    <tr>
                        <td class="dtl-contents aligncenter">{{ $loop->iteration }}</td>
                        <td class="dtl-contents alignleft">{{$data->product_barcode_barcode}}</td>
                        <td class="dtl-contents alignleft">{{$data->product->product_name}}</td>
                        <td class="dtl-contents aligncenter">{{$data->tbl_purc_grn_dtl_quantity}}</td>
                        <td class="dtl-contents alignright">{{number_format($data->tbl_purc_grn_dtl_rate,3)}}</td>
                        @if($document_type == 'GRN')
                        <td class="dtl-contents aligncenter">{{number_format($data->tbl_purc_grn_dtl_sale_rate,3)}}</td>
                        @endif
                        <td class="dtl-contents aligncenter">{{$data->tbl_purc_grn_dtl_sys_quantity}}</td>
                        <td class="dtl-contents aligncenter">{{$data->tbl_purc_grn_dtl_mrp}}</td>
                        <td class="dtl-contents alignright">{{number_format($data->tbl_purc_grn_dtl_amount,3)}}</td>
                        <td class="dtl-contents alignright">{{number_format($data->tbl_purc_grn_dtl_disc_percent,3)}}</td>
                        <td class="dtl-contents alignright">{{number_format($data->tbl_purc_grn_dtl_disc_amount,3)}}</td>
                        <td class="dtl-contents alignright">{{number_format($data->tbl_purc_grn_dtl_after_dis_amount,3)}}</td>
                        <td class="dtl-contents aligncenter">{{$data->tbl_purc_grn_dtl_tax_on}}</td>
                        <td class="dtl-contents alignright">{{number_format($data->tbl_purc_grn_dtl_vat_percent,3)}}</td>
                        <td class="dtl-contents alignright">{{number_format($data->tbl_purc_grn_dtl_vat_amount,3)}}</td>
                        <td class="dtl-contents alignright">{{number_format($data->tbl_purc_grn_dtl_fed_percent,3)}}</td>
                        <td class="dtl-contents alignright">{{number_format($data->tbl_purc_grn_dtl_fed_amount,3)}}</td>
                        <td class="dtl-contents aligncenter">{{$data->tbl_purc_grn_dtl_disc_on}}</td>
                        <td class="dtl-contents alignright">{{number_format($data->tbl_purc_grn_dtl_spec_disc_perc,3)}}</td>
                        <td class="dtl-contents alignright">{{number_format($data->tbl_purc_grn_dtl_spec_disc_amount,3)}}</td>
                        <td class="dtl-contents alignright">{{number_format($data->tbl_purc_grn_dtl_total_amount,3)}}</td>
                        <td class="dtl-contents alignright">{{number_format($data->tbl_purc_grn_dtl_total_amount,3)}}</td>
                        <td class="dtl-contents alignright">{{number_format($data->tbl_purc_grn_dtl_net_tp,3)}}</td>
                        <td class="dtl-contents alignright">{{number_format($data->tbl_purc_grn_dtl_last_tp,3)}}</td>
                        <td class="dtl-contents alignright">{{number_format($data->tbl_purc_grn_dtl_vend_last_tp,3)}}</td>
                        <td class="dtl-contents alignright">{{number_format($data->tbl_purc_grn_dtl_tp_diff,3)}}</td>
                        <td class="dtl-contents alignright">{{number_format($data->tbl_purc_grn_dtl_gp_perc,3)}}</td>
                        <td class="dtl-contents alignright">{{number_format($data->tbl_purc_grn_dtl_gp_amount,3)}}</td>
                        <td class="dtl-contents alignright">{{number_format($data->tbl_purc_grn_dtl_remarks,3)}}</td>
                        <td class="dtl-contents alignright">{{number_format($data->tbl_purc_grn_dtl_fc_rate,3)}}</td>
                        <td class="dtl-contents aligncenter">{{isset($data->uom->uom_name)?$data->uom->uom_name:''}}</td>
                        <td class="dtl-contents aligncenter">{{$data->barcode->product_barcode_packing}}</td>
                        {{-- @if($document_type == 'PR')
                        <td class="dtl-contents aligncenter">{{$data->tbl_purc_grn_dtl_retpend_qty}}</td>
                        <td class="dtl-contents aligncenter">{{$data->tbl_purc_grn_dtl_returnable_qty}}</td>
                        @endif
                        <td class="dtl-contents aligncenter">{{$data->tbl_purc_grn_dtl_foc_quantity}}</td>
                        <td class="dtl-contents aligncenter">{{number_format($data->tbl_purc_grn_dtl_fc_rate,3)}}</td>
                        <td class="dtl-contents alignright">{{$data->tbl_purc_grn_dtl_batch_no}}</td> --}}
                        {{-- <td class="dtl-contents aligncenter">{{$Proddate}}</td> --}}
                        {{-- <td class="dtl-contents aligncenter">{{$Expdate}}</td> --}}
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
                        @endif
                    </tr>
                @endfor
            @endif
        </tbody>
    </table>

    {{-- Purchase Invoice --}}
    <div class="pi_table grn_prints">
        <table class="tableData data_listing pi" id="document_table_pi" style="margin-top: 10px">
            <thead>
                <tr>
                    <th width="3%" class="dtl-head">S.No</th>
                    <th width="4%" class="dtl-head">PO #</th>
                    <th width="4%" class="dtl-head">Barcode</th>
                    <th width="15%" class="dtl-head alignleft">Product Name</th>
                    <th width="3%" class="dtl-head">PI Qty</th>
                    <th width="3%" class="dtl-head">Cost Price</th>
                    <th width="3%" class="dtl-head">Amount</th>
                    {{-- <th width="7%" class="dtl-head">Vat Amt</th> --}}
                </tr>
            </thead>
            <tbody>
                @php
                    $i=0;
                    $totQty = 0;
                    $totAmt = 0;
                    $totGST = 0;
                    $totFED = 0;
                    $totGstIncl = 0;
                    $totDisc = 0;
                    $totSpecDisc = 0;
                    $total_discount = 0;
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
                            $totQty += $pro->tbl_purc_grn_dtl_quantity;
                            $totAmt += $pro->tbl_purc_grn_dtl_total_amount;
                            $totGST += $pro->tbl_purc_grn_dtl_vat_amount;
                            $totGstIncl = $totAmt + $totGST;

                            $totDisc += $pro->tbl_purc_grn_dtl_disc_amount;
                            $totSpecDisc += $pro->tbl_purc_grn_dtl_spec_disc_amount;
                            $total_discount = $totDisc + $totSpecDisc;
                            $totFED += $pro->tbl_purc_grn_dtl_fed_amount;

                            $totNetAmt = $totGstIncl - $total_discount + $totFED;
                            @endphp
                            <tr>
                                <td class="dtl-contents aligncenter">{{ $loop->iteration }}</td>
                                <td class="dtl-contents aligncenter">{{isset($pro->purchase_order->purchase_order_code)?$pro->purchase_order->purchase_order_code:''}}</td>
                                <td class="dtl-contents aligncenter">{{$pro->barcode->product_barcode_barcode}}</td>
                                <td class="dtl-contents alignleft">{{$pro->product->product_name}}</td>
                                <td class="dtl-contents aligncenter">{{$pro->tbl_purc_grn_dtl_quantity}}</td>
                                <td class="dtl-contents alignright">{{number_format($pro->tbl_purc_grn_dtl_rate,3)}}</td>
                                <td class="dtl-contents alignright">{{number_format($pro->tbl_purc_grn_dtl_total_amount,3)}}</td>
                            </tr>
                        @endforeach
                    @endforeach
                @endif
                <tr>
                    <td class="dtl-bottom" colspan="4"></td>
                    <td class="dtl-bottom aligncenter">{{$totQty}}</td>
                    <td class="dtl-bottom"></td>
                    <td class="dtl-bottom alignright">{{number_format($totAmt,3)}}</td>
                </tr>
            </tbody>
        </table>
        <table style="float: right;">
            <tbody>
                <tr>
                    <td style="font-size: 12px;" class="alignleft" colspan="2">GST</td>
                    <td style="font-size: 12px;" class="alignright">{{number_format($totGST,3)}}</td>
                </tr>
                <tr>
                    <td style="font-size: 12px;" class="alignleft" colspan="2">GST Inclusive Value</td>
                    <td style="font-size: 12px;" class="alignright">{{number_format($totGstIncl,3)}}</td>
                </tr>
                <tr>
                    <td style="font-size: 12px;" class="alignleft" colspan="2">Discount + Spe Disc</td>
                    <td style="font-size: 12px;" class="alignright">{{number_format($total_discount,3)}}</td>
                </tr>
                <tr>
                    <td style="font-size: 12px;" class="alignleft" colspan="2">Advance Tax</td>
                    <td style="font-size: 12px;" class="alignright">{{number_format($totFED,3)}}</td>
                </tr>
                <tr>
                    <td class="dtl-bottom alignleft" colspan="2">Gross Amount Payable</td>
                    <td style="font-size: 12px;" class="dtl-bottom alignright">{{number_format($totNetAmt,3)}}</td>
                </tr>
            </tbody>
        </table>
        <table>
            <tr>
                <td class="normal-bold">
                    {{\App\Library\Utilities::AmountWords($totNetAmt,$currency)}}
                </td>
            </tr>
            <tr>
                <td width="45%" valign="top">
                    <table class="tab">
                        <tr>
                            <td class="heading alignleft"></td>
                        </tr>
                        <tr>
                            <th class="heading alignleft">Remarks:</th>
                        </tr>
                        <tr>
                            <td class="normal alignleft paddingNotes">{{$notes}}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <table class="tab mrgn-top">
            <tr>
                <th width="25%" class="heading aligncenter">{{auth()->user()->name}}<hr class="sign-line"> Prepared By: </th>
                <th width="25%" class="heading aligncenter"><hr class="sign-line"> Checked By: </th>
                <th width="25%" class="heading aligncenter"><hr class="sign-line"> Approved By: </th>
                <th width="25%" class="heading aligncenter"><hr class="sign-line"> Received By: </th>
            </tr>
        </table>
    </div>

    {{-- Ex Purchase Invoice with inventory  --}}
    <div class="expi_table grn_prints">
        <table  class="tableData data_listing expi" id="document_table_expi" style="margin-top: 10px">
            <thead>
                <tr>
                    <th width="3%" class="dtl-head">S.No</th>
                    <th width="4%" class="dtl-head">PO #</th>
                    <th width="4%" class="dtl-head">Barcode</th>
                    <th width="15%" class="dtl-head alignleft">Product Name</th>
                    <th width="3%" class="dtl-head">Pre.Stock</th>
                    <th width="3%" class="dtl-head">PI Qty</th>
                    <th width="3%" class="dtl-head">Cost Price</th>
                    <th width="3%" class="dtl-head">Amount</th>
                    {{-- <th width="7%" class="dtl-head">Vat Amt</th> --}}
                </tr>
            </thead>
            <tbody>
                @php
                    $i=0;
                    $totQty = 0;
                    $totAmt = 0;
                    $totGST = 0;
                    $totFED = 0;
                    $totGstIncl = 0;
                    $totDisc = 0;
                    $totSpecDisc = 0;
                    $total_discount = 0;
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
                            $totQty += $pro->tbl_purc_grn_dtl_quantity;
                            $totAmt += $pro->tbl_purc_grn_dtl_total_amount;
                            $totGST += $pro->tbl_purc_grn_dtl_vat_amount;
                            $totGstIncl = $totAmt + $totGST;

                            $totDisc += $pro->tbl_purc_grn_dtl_disc_amount;
                            $totSpecDisc += $pro->tbl_purc_grn_dtl_spec_disc_amount;
                            $total_discount = $totDisc + $totSpecDisc;
                            $totFED += $pro->tbl_purc_grn_dtl_fed_amount;

                            $totNetAmt = $totGstIncl - $total_discount + $totFED;
                            @endphp
                            <tr>
                                <td class="dtl-contents aligncenter">{{ $loop->iteration }}</td>
                                <td class="dtl-contents aligncenter">{{isset($pro->purchase_order->purchase_order_code)?$pro->purchase_order->purchase_order_code:''}}</td>
                                <td class="dtl-contents aligncenter">{{$pro->barcode->product_barcode_barcode}}</td>
                                <td class="dtl-contents alignleft">{{$pro->product->product_name}}</td>
                                <td class="dtl-contents aligncenter">{{$pro->tbl_purc_grn_dtl_sys_quantity}}</td>
                                <td class="dtl-contents aligncenter">{{$pro->tbl_purc_grn_dtl_quantity}}</td>
                                <td class="dtl-contents alignright">{{number_format($pro->tbl_purc_grn_dtl_rate,3)}}</td>
                                <td class="dtl-contents alignright">{{number_format($pro->tbl_purc_grn_dtl_total_amount,3)}}</td>
                            </tr>
                        @endforeach
                    @endforeach
                @endif
                <tr>
                    <td class="dtl-bottom" colspan="5"></td>
                    <td class="dtl-bottom aligncenter">{{$totQty}}</td>
                    <td class="dtl-bottom"></td>
                    <td class="dtl-bottom alignright">{{number_format($totAmt,3)}}</td>
                </tr>
            </tbody>
        </table>
        <table style="float: right;">
            <tbody>
                <tr>
                    <td style="font-size: 12px;" class="alignleft" colspan="2">GST</td>
                    <td style="font-size: 12px;" class="alignright">{{number_format($totGST,3)}}</td>
                </tr>
                <tr>
                    <td style="font-size: 12px;" class="alignleft" colspan="2">GST Inclusive Value</td>
                    <td style="font-size: 12px;" class="alignright">{{number_format($totGstIncl,3)}}</td>
                </tr>
                <tr>
                    <td style="font-size: 12px;" class="alignleft" colspan="2">Discount + Spe Disc</td>
                    <td style="font-size: 12px;" class="alignright">{{number_format($total_discount,3)}}</td>
                </tr>
                <tr>
                    <td style="font-size: 12px;" class="alignleft" colspan="2">Advance Tax</td>
                    <td style="font-size: 12px;" class="alignright">{{number_format($totFED,3)}}</td>
                </tr>
                <tr>
                    <td class="dtl-bottom alignleft" colspan="2">Gross Amount Payable</td>
                    <td style="font-size: 12px;" class="dtl-bottom alignright">{{number_format($totNetAmt,3)}}</td>
                </tr>
            </tbody>
        </table>
        <table>
            <tr>
                <td class="normal-bold">
                    {{\App\Library\Utilities::AmountWords($totNetAmt,$currency)}}
                </td>
            </tr>
            <tr>
                <td width="45%" valign="top">
                    <table class="tab">
                        <tr>
                            <td class="heading alignleft"></td>
                        </tr>
                        <tr>
                            <th class="heading alignleft">Remarks:</th>
                        </tr>
                        <tr>
                            <td class="normal alignleft paddingNotes">{{$notes}}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <table class="tab mrgn-top">
            <tr>
                <th width="25%" class="heading aligncenter">{{auth()->user()->name}}<hr class="sign-line"> Prepared By: </th>
                <th width="25%" class="heading aligncenter"><hr class="sign-line"> Checked By: </th>
                <th width="25%" class="heading aligncenter"><hr class="sign-line"> Approved By: </th>
                <th width="25%" class="heading aligncenter"><hr class="sign-line"> Received By: </th>
            </tr>
        </table>
    </div>

    {{-- Purchase Invoice UK  --}}
    <div class="piuk_table grn_prints">
        <table  class="tableData data_listing piuk" id="document_table_piuk" style="margin-top: 10px">
            <thead>
                <tr>
                    <th width="3%" class="dtl-head">S.No</th>
                    <th width="4%" class="dtl-head">PO #</th>
                    <th width="4%" class="dtl-head">Barcode</th>
                    <th width="15%" class="dtl-head alignleft">Product Name</th>
                    <th width="3%" class="dtl-head">Last TP</th>
                    <th width="3%" class="dtl-head">Qty</th>
                    <th width="3%" class="dtl-head">Rate</th>
                    <th width="3%" class="dtl-head">Amount</th>
                    {{-- <th width="7%" class="dtl-head">Vat Amt</th> --}}
                </tr>
            </thead>
            <tbody>
                @php
                    $i=0;
                    $totQty = 0;
                    $totAmt = 0;
                    $totGST = 0;
                    $totGstIncl = 0;
                    $totDisc = 0;
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
                            $totQty += $pro->tbl_purc_grn_dtl_quantity;
                            $totAmt += $pro->tbl_purc_grn_dtl_total_amount;
                            $totGST += $pro->tbl_purc_grn_dtl_vat_amount;
                            $totGstIncl = $totAmt + $totGST;

                            $totDisc += $pro->tbl_purc_grn_dtl_disc_amount;

                            $totNetAmt = $totGstIncl - $totDisc;
                            @endphp
                            <tr>
                                <td class="dtl-contents aligncenter">{{ $loop->iteration }}</td>
                                <td class="dtl-contents aligncenter">{{isset($pro->purchase_order->purchase_order_code)?$pro->purchase_order->purchase_order_code:''}}</td>
                                <td class="dtl-contents aligncenter">{{$pro->barcode->product_barcode_barcode}}</td>
                                <td class="dtl-contents alignleft">{{$pro->product->product_name}}</td>
                                <td class="dtl-contents aligncenter">{{$pro->tbl_purc_grn_dtl_last_tp}}</td>
                                <td class="dtl-contents aligncenter">{{$pro->tbl_purc_grn_dtl_quantity}}</td>
                                <td class="dtl-contents alignright">{{number_format($pro->tbl_purc_grn_dtl_rate,3)}}</td>
                                <td class="dtl-contents alignright">{{number_format($pro->tbl_purc_grn_dtl_total_amount,3)}}</td>
                            </tr>
                        @endforeach
                    @endforeach
                @endif
                <tr>
                    <td class="dtl-bottom" colspan="5"></td>
                    <td class="dtl-bottom aligncenter">{{$totQty}}</td>
                    <td class="dtl-bottom"></td>
                    <td class="dtl-bottom alignright">{{number_format($totAmt,3)}}</td>
                </tr>
            </tbody>
        </table>
        <table style="float: right;">
            <tbody>
                <tr>
                    <td style="font-size: 12px;" class="alignleft" colspan="2">GST</td>
                    <td style="font-size: 12px;" class="alignright">{{number_format($totGST,3)}}</td>
                </tr>
                <tr>
                    <td style="font-size: 12px;" class="alignleft" colspan="2">GST Inclusive Value</td>
                    <td style="font-size: 12px;" class="alignright">{{number_format($totGstIncl,3)}}</td>
                </tr>
                <tr>
                    <td style="font-size: 12px;" class="alignleft" colspan="2">Discount</td>
                    <td style="font-size: 12px;" class="alignright">{{number_format($totDisc,3)}}</td>
                </tr>
                <tr>
                    <td class="dtl-bottom alignleft" colspan="2">Gross Amount Payable</td>
                    <td style="font-size: 12px;" class="dtl-bottom alignright">{{number_format($totNetAmt,3)}}</td>
                </tr>
            </tbody>
        </table>
        <table>
            <tr>
                <td class="normal-bold">
                    {{\App\Library\Utilities::AmountWords($totNetAmt,$currency)}}
                </td>
            </tr>
            <tr>
                <td width="45%" valign="top">
                    <table class="tab">
                        <tr>
                            <td class="heading alignleft"></td>
                        </tr>
                        <tr>
                            <th class="heading alignleft">Remarks:</th>
                        </tr>
                        <tr>
                            <td class="normal alignleft paddingNotes">{{$notes}}</td>
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
    </div>

    {{-- Purchase Invoice Landscape  --}}
    <div class="pil_table grn_prints">
        <table  class="tableData data_listing pil" id="document_table_pil" style="margin-top: 10px">
            <thead>
                <tr>
                    <th width="3%" class="dtl-head">S.No</th>
                    <th width="4%" class="dtl-head">Barcode</th>
                    <th width="15%" class="dtl-head alignleft">Product Name</th>
                    <th width="3%" class="dtl-head">Qty</th>
                    <th width="3%" class="dtl-head">Cost Price</th>
                    <th width="3%" class="dtl-head">G. Amount</th>
                    <th width="5%" class="dtl-head">Disc.(%)</th>
                    <th width="4%" class="dtl-head">GST(%)</th>
                    <th width="4%" class="dtl-head">FED(%)</th>
                    <th width="4%" class="dtl-head">Spe.Disc(%)</th>
                    <th width="3%" class="dtl-head">Net Amount</th>
                    <th width="3%" class="dtl-head">Net TP</th>
                    <th width="3%" class="dtl-head">Last TP</th>
                    <th width="3%" class="dtl-head">TP Diff</th>
                    <th width="3%" class="dtl-head">Sale Price</th>
                    <th width="3%" class="dtl-head">Old Sale Price</th>
                    <th width="3%" class="dtl-head">M.R.P</th>
                    <th width="5%" class="dtl-head">GP Amount & (%)</th>
                    <th width="3%" class="dtl-head">Current Stock</th>
                    {{-- <th width="7%" class="dtl-head">Vat Amt</th> --}}
                </tr>
            </thead>
            <tbody>
                @php
                    $i=0;
                    $totQty = 0;
                    $totGrossAmt = 0;
                    $totDisc = 0;
                    $totGST = 0;
                    $totFed = 0;
                    $totSpecDisc = 0;
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
                            $totQty += $pro->tbl_purc_grn_dtl_quantity;
                            $totGrossAmt += $pro->tbl_purc_grn_dtl_gross_amount;
                            $totDisc += $pro->tbl_purc_grn_dtl_disc_amount;
                            $totGST += $pro->tbl_purc_grn_dtl_vat_amount;
                            $totFed += $pro->tbl_purc_grn_dtl_fed_amount;
                            $totSpecDisc += $pro->tbl_purc_grn_dtl_spec_disc_amount;
                            $totNetAmt += $pro->tbl_purc_grn_dtl_total_amount;
                            @endphp
                            <tr>
                                <td class="dtl-contents aligncenter">{{ $loop->iteration }}</td>
                                <td class="dtl-contents aligncenter">{{$pro->barcode->product_barcode_barcode}}</td>
                                <td class="dtl-contents alignleft">{{$pro->product->product_name}}</td>
                                <td class="dtl-contents alignright">{{$pro->tbl_purc_grn_dtl_quantity}}</td>
                                <td class="dtl-contents alignright">{{number_format($pro->tbl_purc_grn_dtl_rate,3)}}</td>
                                <td class="dtl-contents alignright">{{number_format($pro->tbl_purc_grn_dtl_gross_amount,3)}}</td>
                                <td class="dtl-contents alignright">{{number_format($pro->tbl_purc_grn_dtl_disc_amount,3)}}({{$pro->tbl_purc_grn_dtl_disc_percent,2}})</td>
                                <td class="dtl-contents alignright">{{number_format($pro->tbl_purc_grn_dtl_vat_amount,3)}} ({{$pro->tbl_purc_grn_dtl_vat_percent,2}})</td>
                                <td class="dtl-contents alignright">{{number_format($pro->tbl_purc_grn_dtl_fed_amount,3)}} ({{$pro->tbl_purc_grn_dtl_fed_percent,2}})</td>
                                <td class="dtl-contents alignright">{{number_format($pro->tbl_purc_grn_dtl_spec_disc_amount,3)}} ({{$pro->tbl_purc_grn_dtl_spec_disc_perc,2}})</td>
                                <td class="dtl-contents alignright">{{number_format($pro->tbl_purc_grn_dtl_total_amount,3)}}</td>
                                <td class="dtl-contents alignright">{{number_format($pro->tbl_purc_grn_dtl_net_tp,3)}}</td>
                                <td class="dtl-contents alignright">{{number_format($pro->tbl_purc_grn_dtl_last_tp,3)}}</td>
                                <td class="dtl-contents alignright">{{number_format($pro->tbl_purc_grn_dtl_tp_diff,3)}}</td>
                                <td class="dtl-contents alignright">{{number_format($pro->tbl_purc_grn_dtl_sale_rate,3)}}</td>
                                <td class="dtl-contents alignright">{{number_format($pro->tbl_purc_grn_dtl_sale_rate,3)}}</td>
                                <td class="dtl-contents alignright">{{number_format($pro->tbl_purc_grn_dtl_mrp,3)}}</td>
                                <td class="dtl-contents alignright">{{number_format($pro->tbl_purc_grn_dtl_gp_amount,3)}} ({{$pro->tbl_purc_grn_dtl_gp_perc,2}})</td>
                                <td class="dtl-contents alignright">{{number_format($pro->tbl_purc_grn_dtl_sys_quantity)}}</td>
                            </tr>
                        @endforeach
                    @endforeach
                @endif
                <tr>
                    <td class="dtl-bottom" colspan="3">Total</td>
                    <td class="dtl-bottom alignright">{{$totQty}}</td>
                    <td class="dtl-bottom alignright"></td>
                    <td class="dtl-bottom alignright">{{number_format($totGrossAmt,3)}}</td>
                    <td class="dtl-bottom alignright">{{number_format($totDisc,3)}}</td>
                    <td class="dtl-bottom alignright">{{number_format($totGST,3)}}</td>
                    <td class="dtl-bottom alignright">{{number_format($totFed,3)}}</td>
                    <td class="dtl-bottom alignright">{{number_format($totSpecDisc,3)}}</td>
                    <td class="dtl-bottom alignright">{{number_format($totNetAmt,3)}}</td>
                </tr>
            </tbody>
        </table>
        <table>
            <tr>
                <td width="45%" valign="top">
                    <table class="tab">
                        <tr>
                            <td class="heading alignleft"></td>
                        </tr>
                        <tr>
                            <th class="heading alignleft">Remarks:</th>
                        </tr>
                        <tr>
                            <td class="normal alignleft paddingNotes">{{$notes}}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        {{-- GP Calculations --}}
        <div class="table-div" style="float: right; width: 30%; padding: 5px;">
            <table class="tableData" style="margin-top: 5px; width:100% !important">
                <thead>
                    <tr>
                        <th colspan="100%" class="dtl-head headingcolor">GP Calculations</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $gp_amount = 0;
                        $gp_perc= 0;
                        $gp_amount = $net_retail - $totNetAmt;

                        $gp_perc = $gp_amount / $totNetAmt * 100;
                    @endphp
                    <tr>
                        <td class="heading dtl-contents">Net Retail Amount:</td>
                        <td class="dtl-contents alignright">{{number_format($net_retail,3)}}</td>
                    </tr>
                    <tr>
                        <td class="heading dtl-contents">Net Purchase Amount:</td>
                        <td class="dtl-contents alignright">{{number_format($totNetAmt,3)}}</td>
                    </tr>
                    <tr>
                        <td class="heading dtl-contents">GP Amount:</td>
                        <td class="dtl-contents alignright">{{number_format($gp_amount,3)}}</td>
                    </tr>
                    <tr>
                        <td class="dtl-contents" style="font-weight: bolder;">GP %:</td>
                        <td class="dtl-contents alignright" style="font-weight: bolder;">{{number_format($gp_perc,3)}}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        {{-- Invoice Calculations --}}
        <div class="table-div" style="float: right; width: 30%; padding: 5px;">
            <table class="tableData" style="margin-top: 5px; width:100% !important">
                <thead>
                    <tr>
                        <th colspan="100%" class="dtl-head headingcolor">Invoice Calculations</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $Invoice_amount_after_disc = $totNetAmt - $overall_disc_amount;
                    $Invoice_net_amount = $Invoice_amount_after_disc + $overall_tax_amount;
                    @endphp
                    <tr>
                        <td class="heading dtl-contents">Net Amount Vef. Inv. Disc:</td>
                        <td class="dtl-contents alignright">{{number_format($totNetAmt,3)}}</td>
                    </tr>
                    <tr>
                        <td class="heading dtl-contents">(Less) Invoice Discount:</td>
                        <td class="dtl-contents alignright">{{number_format($overall_disc_amount,3)}}</td>
                    </tr>
                    <tr>
                        <td class="heading dtl-contents">Include in TP:</td>
                        <td class="dtl-contents alignright"></td>
                    </tr>
                    <tr>
                        <td class="dtl-contents">Advance Tax:</td>
                        <td class="dtl-contents alignright">{{number_format($overall_tax_amount,3)}}</td>
                    </tr>
                    <tr>
                        <td class="dtl-contents" style="font-weight: bold">Net Amount:</td>
                        <td class="dtl-contents alignright" style="font-weight: bold">{{number_format($Invoice_net_amount,3)}}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>


        {{-- signature section --}}
        <table class="tab mrgn-top">
            <tr>
                <th width="25%" class="heading aligncenter">{{auth()->user()->name}}<hr class="sign-line"> Prepared by: </th>
                <th width="25%" class="heading aligncenter"><hr class="sign-line"> Purchaser: </th>
                <th width="25%" class="heading aligncenter"><hr class="sign-line"> Checked By: </th>
                <th width="25%" class="heading aligncenter"><hr class="sign-line"> C.E.O </th>
            </tr>
        </table>
    </div>

    {{-- Stock Direct Delovery Invoice  --}}
    <div class="sddi_table grn_prints">
        <table  class="tableData data_listing sddi" id="document_table_sddi" style="margin-top: 10px">
            <thead>
                <tr>
                    <th width="3%" class="dtl-head">S.No</th>
                    <th width="4%" class="dtl-head">Barcode</th>
                    <th width="15%" class="dtl-head alignleft">Product Name</th>
                    <th width="3%" class="dtl-head">Sale Rate</th>
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
                            $totQty += $pro->tbl_purc_grn_dtl_quantity;
                            $totAmt += $pro->tbl_purc_grn_dtl_total_amount;
                            @endphp
                            <tr>
                                <td class="dtl-contents aligncenter">{{ $loop->iteration }}</td>
                                <td class="dtl-contents aligncenter">{{$pro->barcode->product_barcode_barcode}}</td>
                                <td class="dtl-contents alignleft">{{$pro->product->product_name}}</td>
                                <td class="dtl-contents alignright">{{number_format($pro->tbl_purc_grn_dtl_sale_rate,3)}}</td>
                                <td class="dtl-contents aligncenter">{{$pro->tbl_purc_grn_dtl_quantity}}</td>
                                <td class="dtl-contents alignright">{{number_format($pro->tbl_purc_grn_dtl_total_amount,3)}}</td>
                            </tr>
                        @endforeach
                    @endforeach
                @endif
                <tr>
                    <td class="dtl-bottom" colspan="4">Total</td>
                    <td class="dtl-bottom aligncenter">{{$totQty}}</td>
                    <td class="dtl-bottom alignright">{{number_format($totAmt,3)}}</td>
                </tr>
            </tbody>
        </table>
        <table>
            <tr>
                <td class="normal-bold">
                    {{\App\Library\Utilities::AmountWords($totNetAmt,$currency)}}
                </td>
            </tr>
            <tr>
                <td width="45%" valign="top">
                    <table class="tab">
                        <tr>
                            <td class="heading alignleft"></td>
                        </tr>
                        <tr>
                            <th class="heading alignleft">Remarks:</th>
                        </tr>
                        <tr>
                            <td class="normal alignleft paddingNotes">{{$notes}}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <table class="tab mrgn-top">
            <tr>
                <th width="50%" class="heading aligncenter">{{auth()->user()->name}}<hr class="sign-line"> Prepared by main office: </th>
                <th width="50%" class="heading aligncenter"><hr class="sign-line"> Stock received by outlet: </th>
            </tr>
        </table>
    </div>

@endsection

@section('customJS')
@endsection
@endpermission
