@extends('layouts.grn_print_layout')

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
                    $headings = ['Sr No','Barcode','Product Name','Qty','Cost Price','G.Amount',
                    'Disc.(%)','GST.(%)','FED.(%)','Spec.Disc(%)','Net Amount','Net TP','Last Tp',
                    'TP Diff','Sale Price','Old Sale Price','M.R.P','GP Amount & %','Current Stock'];
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
{{-- Purchase Invoice Landscape  --}}
<table  class="tableData data_listing" id="document_table_data">
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
        </tr>
    </thead>
    <tbody>
        @php
            $i=0;
            $totQty = 0;
            $totGrossAmt = 0;
            $totDisc = 0;
            $totGST = 0;
            $totSpecDisc = 0;
            $totNetAmt = 0;
            $net_retail = 0;
            $totgpamnt=0;
            $totnettp=0;
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
                    $totSpecDisc += $pro->tbl_purc_grn_dtl_spec_disc_amount;
                    $totNetAmt += $pro->tbl_purc_grn_dtl_total_amount;
                    $totgpamnt += $pro->tbl_purc_grn_dtl_gp_amount;
                    $totnettp += $pro->tbl_purc_grn_dtl_net_tp;
                    @endphp
                    <tr>
                        <td class="dtl-contents aligncenter">{{ $loop->iteration }}</td>
                        <td class="dtl-contents aligncenter">{{$pro->barcode->product_barcode_barcode}}</td>
                        <td class="dtl-contents alignleft">{{$pro->product->product_name}}</td>
                        <td class="dtl-contents aligncenter">{{$pro->tbl_purc_grn_dtl_quantity}}</td>
                        <td class="dtl-contents alignright">{{number_format($pro->tbl_purc_grn_dtl_rate,3)}}</td>
                        <td class="dtl-contents alignright">{{number_format($pro->tbl_purc_grn_dtl_gross_amount,3)}}</td>
                        <td class="dtl-contents alignright">{{number_format($pro->tbl_purc_grn_dtl_disc_amount,2)}}({{$pro->tbl_purc_grn_dtl_disc_percent,2}})</td>
                        <td class="dtl-contents alignright">{{number_format($pro->tbl_purc_grn_dtl_vat_amount,2)}} ({{$pro->tbl_purc_grn_dtl_vat_percent,2}})</td>
                        <td class="dtl-contents alignright">{{number_format($pro->tbl_purc_grn_dtl_fed_amount,2)}} ({{$pro->tbl_purc_grn_dtl_fed_percent,2}})</td>
                        <td class="dtl-contents alignright">{{number_format($pro->tbl_purc_grn_dtl_spec_disc_amount,2)}} ({{$pro->tbl_purc_grn_dtl_spec_disc_perc,2}})</td>
                        <td class="dtl-contents alignright">{{number_format($pro->tbl_purc_grn_dtl_total_amount,2)}}</td>
                        <td class="dtl-contents alignright">{{number_format($pro->tbl_purc_grn_dtl_net_tp,2)}}</td>
                        <td class="dtl-contents alignright">{{number_format($pro->tbl_purc_grn_dtl_last_tp,2)}}</td>
                        <td class="dtl-contents alignright">{{number_format($pro->tbl_purc_grn_dtl_tp_diff,2)}}</td>
                        <td class="dtl-contents alignright">{{number_format($pro->tbl_purc_grn_dtl_sale_rate,2)}}</td>
                        <td class="dtl-contents alignright">{{number_format($pro->tbl_purc_grn_dtl_sale_rate,2)}}</td>
                        <td class="dtl-contents alignright">{{number_format($pro->tbl_purc_grn_dtl_mrp,2)}}</td>
                        <td class="dtl-contents alignright">{{number_format($pro->tbl_purc_grn_dtl_gp_amount,2)}} ({{$pro->tbl_purc_grn_dtl_gp_perc,2}})</td>
                        <td class="dtl-contents alignright">{{number_format($pro->tbl_purc_grn_dtl_sys_quantity)}}</td>
                    </tr>
                @endforeach
            @endforeach
            <tr>
                <td class="dtl-head alignright border-right"></td>
                <td class="dtl-head alignright border-right"></td>
                <td class="dtl-head aligncenter"></td>
                <td class="dtl-head aligncenter">{{$totQty}}</td>
                <td class="dtl-head"></td>
                <td class="dtl-head alignright">{{number_format($totGrossAmt,3)}}</td>
                <td class="dtl-head alignright">{{number_format($totDisc,3)}}</td>
                <td class="dtl-head alignright">{{number_format($totGST,3)}}</td>
                <td class="dtl-head alignright"></td>
                <td class="dtl-head alignright">{{number_format($totSpecDisc,3)}}</td>
                <td class="dtl-head alignright">{{number_format($totNetAmt,3)}}</td>
                <td class="dtl-head alignright border-right"></td>
                <td class="dtl-head alignright border-right"></td>
                <td class="dtl-head alignright border-right"></td>
                <td class="dtl-head alignright border-right"></td>
                <td class="dtl-head alignright border-right"></td>
                <td class="dtl-head alignright border-right"></td>
                <td class="dtl-head alignright border-right"></td>
                <td class="dtl-head alignright border-right"></td>
            </tr>
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
</table><br>
<table class="tab" valign="top">
    <td width="49%">
    </td>
    <td width="25%">
        {{-- Invoice Calculations --}}
        <table class="tableData" style="margin-top: 20px;">
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
    </td>
    <td width="1%"></td>
    <td width="25%">
        {{-- GP Calculations --}}
        <table class="tableData">
            <thead>
                <tr>
                    <th colspan="100%" class="dtl-head headingcolor">GP Calculations</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $gp_amount = 0;
                    $gp_perc= 0;

                    $totSaleRate = $data->tbl_purc_grn_dtl_quantity * $data->tbl_purc_grn_dtl_sale_rate;
                    $net_retail += $totSaleRate;
                    $gp_amount = $net_retail - $totNetAmt;

                    //$gp_perc = $gp_amount / $totNetAmt * 100;

                    $gp_perc = @(($totgpamnt / $totnettp) * 100);

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
                    <td class="dtl-contents alignright">{{number_format($totgpamnt,3)}}</td>
                </tr>
                <tr>
                    <td class="dtl-contents" style="font-weight: bolder;">GP %:</td>
                    <td class="dtl-contents alignright" style="font-weight: bolder;">{{number_format($gp_perc,3)}}</td>
                </tr>
            </tbody>
        </table>
    </td>
</table>
{{--Signature--}}
<table class="tab mrgn-top" valign="bottom">
    <tr>
        <th width="25%" class="heading aligncenter">
            <div style="height: 20px;">{{auth()->user()->name}}</div>
            <div class="hr_div_line">Prepared By</div>
        </th>
        <th width="25%" class="heading aligncenter">
            <div style="height: 20px;"></div>
            <div class="hr_div_line">Purchaser</div>
        </th>
        <th width="25%" class="heading aligncenter">
            <div style="height: 20px;"></div>
            <div class="hr_div_line">Checked By</div>
        </th>
        <th width="25%" class="heading aligncenter">
            <div style="height: 20px;"></div>
            <div class="hr_div_line">C.E.O</div>
        </th>
    </tr>
</table>
@endsection
@endpermission
