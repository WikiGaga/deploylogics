@extends('layouts.pr_print_layout')
@section('pageCSS')

@endsection
@permission($data['permission'])
@php
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
                    $headings = ['Sr No','Barcode','Product Name','Qty','Rate','Sys Qty','M.R.P',
                    'Amount','Dis%','Disc Amt','After Disc Amt','Tax On','GST%','GST Amt','FED%','FED Amt',
                    'Disc On','Spec Disc%','Spec Disc Amt','Gross Amt','Net Amt','Net Tp','Last Tp','Vend Last Tp',
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
@endsection
@section('content')
    {{--table content--}}
    <table class="tabData data_listing" id="document_table_data">
        <thead>
            <tr>
                <th width="3%" class="dtl-head">Sr No</th>
                <th width="9%" class="dtl-head alignleft">Barcode</th>
                <th width="12%" class="dtl-head alignleft">Product Name</th>
                <th width="5%" class="dtl-head">Qty</th>
                <th width="5%" class="dtl-head">Rate</th>
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
                $i=0;

                $totQty = 0;
                $totRate = 0;
                $totSale = 0;
                $totAmt = 0;
                $totDiscAmt = 0;
                $totAfterDiscAmt = 0;
                $totVatAmt = 0;
                $totSpecDiscAmt = 0;
                $totGrossAmt = 0;
                $totNetAmt = 0;
                $totNetTp = 0;
                $totLastTp = 0;
                $totVendTp = 0;
                $totTpDiff = 0;
                $totGpAmt = 0;
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
                        $totRate += $data->tbl_purc_grn_dtl_rate;
                        $totSale += $data->tbl_purc_grn_dtl_sale_rate;
                        $totAmt += $data->tbl_purc_grn_dtl_amount;
                        $totDiscAmt += $data->tbl_purc_grn_dtl_disc_amount;
                        $totAfterDiscAmt += $data->tbl_purc_grn_dtl_after_dis_amount;
                        $totVatAmt += $data->tbl_purc_grn_dtl_vat_amount;
                        $totSpecDiscAmt += $data->tbl_purc_grn_dtl_spec_disc_amount;
                        $totGrossAmt += $data->tbl_purc_grn_dtl_gross_amount;
                        $totNetAmt += $data->tbl_purc_grn_dtl_total_amount;
                        $totNetTp += $data->tbl_purc_grn_dtl_net_tp;
                        $totLastTp += $data->tbl_purc_grn_dtl_last_tp;
                        $totVendTp += $data->tbl_purc_grn_dtl_vend_last_tp;
                        $totTpDiff += $data->tbl_purc_grn_dtl_tp_diff;
                        $totGpAmt += $data->tbl_purc_grn_dtl_gp_amount;
                    @endphp
                    <tr>
                        <td class="dtl-contents aligncenter">{{ $loop->iteration }}</td>
                        <td class="dtl-contents alignleft">{{$data->product_barcode_barcode}}</td>
                        <td class="dtl-contents alignleft">{{$data->product->product_name}}</td>
                        <td class="dtl-contents aligncenter">{{$data->tbl_purc_grn_dtl_quantity}}</td>
                        <td class="dtl-contents alignright">{{@number_format($data->tbl_purc_grn_dtl_rate,3)}}</td>
                        <td class="dtl-contents aligncenter">{{$data->tbl_purc_grn_dtl_sys_quantity}}</td>
                        <td class="dtl-contents aligncenter">{{$data->tbl_purc_grn_dtl_mrp}}</td>
                        <td class="dtl-contents alignright">{{@number_format($data->tbl_purc_grn_dtl_amount,3)}}</td>
                        <td class="dtl-contents alignright">{{@number_format($data->tbl_purc_grn_dtl_disc_percent,3)}}</td>
                        <td class="dtl-contents alignright">{{@number_format($data->tbl_purc_grn_dtl_disc_amount,3)}}</td>
                        <td class="dtl-contents alignright">{{@number_format($data->tbl_purc_grn_dtl_after_dis_amount,3)}}</td>
                        <td class="dtl-contents aligncenter">{{$data->tbl_purc_grn_dtl_tax_on}}</td>
                        <td class="dtl-contents alignright">{{@number_format($data->tbl_purc_grn_dtl_vat_percent,3)}}</td>
                        <td class="dtl-contents alignright">{{@number_format($data->tbl_purc_grn_dtl_vat_amount,3)}}</td>
                        <td class="dtl-contents alignright">{{@number_format($data->tbl_purc_grn_dtl_fed_percent,3)}}</td>
                        <td class="dtl-contents alignright">{{@number_format($data->tbl_purc_grn_dtl_fed_amount,3)}}</td>
                        <td class="dtl-contents aligncenter">{{$data->tbl_purc_grn_dtl_disc_on}}</td>
                        <td class="dtl-contents alignright">{{@number_format($data->tbl_purc_grn_dtl_spec_disc_perc,3)}}</td>
                        <td class="dtl-contents alignright">{{@number_format($data->tbl_purc_grn_dtl_spec_disc_amount,3)}}</td>
                        <td class="dtl-contents alignright">{{@number_format($data->tbl_purc_grn_dtl_gross_amount,3)}}</td>
                        <td class="dtl-contents alignright">{{@number_format($data->tbl_purc_grn_dtl_total_amount,3)}}</td>
                        <td class="dtl-contents alignright">{{@number_format($data->tbl_purc_grn_dtl_net_tp,3)}}</td>
                        <td class="dtl-contents alignright">{{@number_format($data->tbl_purc_grn_dtl_last_tp,3)}}</td>
                        <td class="dtl-contents alignright">{{@number_format($data->tbl_purc_grn_dtl_vend_last_tp,3)}}</td>
                        <td class="dtl-contents alignright">{{@number_format($data->tbl_purc_grn_dtl_tp_diff,3)}}</td>
                        <td class="dtl-contents alignright">{{@number_format($data->tbl_purc_grn_dtl_gp_perc,3)}}</td>
                        <td class="dtl-contents alignright">{{@number_format($data->tbl_purc_grn_dtl_gp_amount,3)}}</td>
                        <td class="dtl-contents alignright">{{@number_format($data->tbl_purc_grn_dtl_remarks)}}</td>
                        <td class="dtl-contents alignright">{{@number_format($data->tbl_purc_grn_dtl_fc_rate,3)}}</td>
                        <td class="dtl-contents aligncenter">{{isset($data->uom->uom_name)?$data->uom->uom_name:''}}</td>
                        <td class="dtl-contents aligncenter">{{$data->barcode->product_barcode_packing}}</td>
                    </tr>
                @endforeach
                <tr>
                    <td class="dtl-head alignright border-right"></td>
                    <td class="dtl-head alignright border-right"></td>
                    <td class="dtl-head alignright"></td>
                    <td class="dtl-head alignright">{{number_format($totQty)}}</td>
                    <td class="dtl-head alignright"></td>
                    <td class="dtl-head alignright"></td>
                    <td class="dtl-head alignright"></td>
                    <td class="dtl-head alignright">{{number_format($totAmt,3)}}</td>
                    <td class="dtl-head alignright"></td>
                    <td class="dtl-head alignright">{{number_format($totDiscAmt,3)}}</td>
                    <td class="dtl-head alignright">{{number_format($totAfterDiscAmt,3)}}</td>
                    <td class="dtl-head alignright border-right"></td>
                    <td class="dtl-head alignright"></td>
                    <td class="dtl-head alignright">{{number_format($totVatAmt,3)}}</td>
                    <td class="dtl-head alignright border-right"></td>
                    <td class="dtl-head alignright border-right"></td>
                    <td class="dtl-head alignright border-right"></td>
                    <td class="dtl-head alignright"></td>
                    <td class="dtl-head alignright">{{number_format($totSpecDiscAmt,3)}}</td>
                    <td class="dtl-head alignright">{{number_format($totGrossAmt,3)}}</td>
                    <td class="dtl-head alignright">{{number_format($totNetAmt,3)}}</td>
                    <td class="dtl-head alignright">{{number_format($totNetTp,3)}}</td>
                    <td class="dtl-head alignright">{{number_format($totLastTp,3)}}</td>
                    <td class="dtl-head alignright">{{number_format($totVendTp,3)}}</td>
                    <td class="dtl-head alignright">{{number_format($totTpDiff,3)}}</td>
                    <td class="dtl-head alignright"></td>
                    <td class="dtl-head alignright">{{number_format($totGpAmt,3)}}</td>
                    <td class="dtl-head alignright border-right"></td>
                    <td class="dtl-head alignright border-right"></td>
                    <td class="dtl-head alignright border-right"></td>
                    <td class="dtl-head alignright"></td>
                </tr>
            @endif
            {{-- @if($i<=8)
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
            @endif --}}
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
                <div class="hr_div_line">Receiver's Signature</div>
            </th>
        </tr>
    </table>
@endsection
@endpermission
