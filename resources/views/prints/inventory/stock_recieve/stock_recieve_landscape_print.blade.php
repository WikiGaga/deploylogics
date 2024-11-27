@extends('layouts.stock_print_layout')

@section('pageCSS')
@endsection
@permission($data['permission'])
@php
    $heading = strtoupper($data['title']);
    $businessName = strtoupper(auth()->user()->business->business_short_name);
    $data['pdf_link'] = '';
    $print_type = '';
    $print = $data['type'];

    if(isset($data['current'])){
        $type = $data['stock_code_type'];
        $code = $data['current']->stock_code;
        $date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->stock_date))));
        $store_from = isset($data['store_from']->store_name)?$data['store_from']->store_name:'';
        $store_to = isset($data['store_to']->store_name)?$data['store_to']->store_name:'';
        $branch_from = isset($data['branch_from']->branch_name)?$data['branch_from']->branch_name:'';
        $branch_to = isset($data['branch_to']->branch_name)?$data['branch_to']->branch_name:'';
        // dd($branch_to);
        $product_name = isset($data['current']->product->product_name)?$data['current']->product->product_name:'';
        $assamble_qty = isset($data['current']->assamble_qty)?$data['current']->assamble_qty:'';
        $stock_location = isset($data['display_location'])?$data['display_location']->display_location_name:'';
        $stock_request_id = $data['current']->stock_request_id;
        if(isset($stock_request_id) && $type == 'str'){
            $stock = \App\Models\TblInveStock::where('stock_id',(int)$stock_request_id)->first();
            $stock_branch = $stock->stock_branch_from_id;
            if($stock != null){
                $stock_from = $stock->stock_code;
            }
        }
        $notes = $data['current']->stock_remarks;
        $dtls = isset($data['current']->stock_dtls)? $data['current']->stock_dtls :[];
        $supplier_name = isset($data['supplier'])? $data['supplier']->supplier_name :"";
        $supplier_phone = isset($data['supplier'])? $data['supplier']->supplier_name :"";
        $supplier_email = isset($data['supplier'])? $data['supplier']->supplier_name :"";
        $supplier_address = isset($data['supplier'])? $data['supplier']->supplier_name :"";
    }else{
        abort('404');
    }

@endphp
@section('title', $heading)
@section('heading', $heading)
@section('content')
<table class="tableData">
    <tr>
        <td width="33.33%">
            <div>
                <span class="heading heading-block">Code :</span>
                <span class="normal normal-block">{{isset($code)?$code:''}}</span>
            </div>
            <div>
                <span class="heading heading-block">From Branch :</span>
                <span class="normal normal-block">{{isset($branch_from)?$branch_from:''}}</span>
            </div>
            <div>
                <span class="heading heading-block">Stock From :</span>
                <span class="normal normal-block">{{isset($stock_from)?$stock_from:''}}</span>
            </div>
        </td>
        <td width="33.33%"></td>
        <td width="33.33%">
            <div>
                <span class="heading heading-block">Date :</span>
                <span class="normal normal-block">{{isset($date)?$date:''}}</span>
            </div>
            <div>
                <span class="heading heading-block">Store :</span>
                <span class="normal normal-block">{{isset($store_to)?$store_to:''}}</span>
            </div>            
        </td>
    </tr>
</table>
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
            $totGrossAmt = 0;
            $totVatAmt = 0;
            $totNetAmt = 0;
            $totDisc = 0;
            $totGST = 0;
            $totSpecDisc = 0;
            $net_retail = 0;
            $totSaleRate = 0;
            $totQty = 0;
            $net_amount = 0;
            $overall_discount = 0;
            $overall_tax = 0;
            $total_net_amount = 0;
        @endphp
        @php
        $new_dtls = [];
        @endphp
        @if(isset($dtls))
            @foreach ($dtls as $dtl)
                @php                
                $pro = App\Models\TblPurcGrnDtl::where('product_id',$dtl->product_id)->where('grn_type','GRN')->where('branch_id',$stock_branch)->orderBy('created_at','desc')->first();
                    // dd($pro->toArray());
                    // dd($dtl->toArray());
                    $i++;
                    $stock_production_date = date('d-m-Y', strtotime(trim(str_replace('/','-',$dtl->stock_dtl_production_date))));
                    $stock_expiry_date = date('d-m-Y', strtotime(trim(str_replace('/','-',$dtl->stock_dtl_expiry_date))));

                    $qty = $dtl->stock_dtl_quantity;
                    $discount = $pro->tbl_purc_grn_dtl_disc_amount;
                    $gst = $pro->tbl_purc_grn_dtl_vat_amount;
                    $fed = $pro->tbl_purc_grn_dtl_fed_amount;
                    $spec_discount = $pro->tbl_purc_grn_dtl_spec_disc_amount;
                    $GrossAmt = $dtl->stock_dtl_amount;

                    //Net Amount after discount
                    $overall_discount = $discount + $spec_discount;
                    $net_amount = $GrossAmt - $overall_discount;

                    //Including GST
                    $overall_tax = $gst + $fed;
                    $total_net_amount = $net_amount + $overall_tax;
                    
                    $totGrossAmt += $GrossAmt;
                    $totNetAmt += $total_net_amount;

                    $totDisc += $discount;
                    $totGST += $gst;
                    $totSpecDisc += $spec_discount;
                    $totQty += $qty;
                @endphp
                <tr>
                    <td class="dtl-contents aligncenter">{{ $loop->iteration }}</td>
                    <td class="dtl-contents alignleft">{{$dtl->product_barcode_barcode}}</td>
                    <td class="dtl-contents alignleft">{{$dtl->product->product_name}}</td>
                    <td class="dtl-contents aligncenter">{{$qty}}</td>
                    <td class="dtl-contents alignright">{{number_format($dtl->stock_dtl_purc_rate,3)}}</td>
                    <td class="dtl-contents alignright">{{number_format($dtl->stock_dtl_amount,3)}}</td>
                    <td class="dtl-contents alignright">{{number_format($pro->tbl_purc_grn_dtl_disc_amount,2)}}({{$pro->tbl_purc_grn_dtl_disc_percent,2}})</td>
                    <td class="dtl-contents alignright">{{number_format($pro->tbl_purc_grn_dtl_vat_amount,2)}} ({{$pro->tbl_purc_grn_dtl_vat_percent,2}})</td>
                    <td class="dtl-contents alignright">{{number_format($pro->tbl_purc_grn_dtl_fed_amount,2)}} ({{$pro->tbl_purc_grn_dtl_fed_percent,2}})</td>
                    <td class="dtl-contents alignright">{{number_format($pro->tbl_purc_grn_dtl_spec_disc_amount,2)}} ({{$pro->tbl_purc_grn_dtl_spec_disc_perc,2}})</td>
                    <td class="dtl-contents alignright">{{number_format($total_net_amount,2)}}</td>
                    <td class="dtl-contents alignright">{{number_format($pro->tbl_purc_grn_dtl_net_tp,2)}}</td>
                    <td class="dtl-contents alignright">{{number_format($pro->tbl_purc_grn_dtl_last_tp,2)}}</td>
                    <td class="dtl-contents alignright">{{number_format($pro->tbl_purc_grn_dtl_tp_diff,2)}}</td>
                    <td class="dtl-contents alignright">{{number_format($dtl->stock_dtl_rate,2)}}</td>
                    <td class="dtl-contents alignright">{{number_format($dtl->stock_dtl_rate,2)}}</td>
                    <td class="dtl-contents alignright">{{number_format($pro->tbl_purc_grn_dtl_mrp,2)}}</td>
                    <td class="dtl-contents alignright">{{number_format($pro->tbl_purc_grn_dtl_gp_amount,2)}} ({{$pro->tbl_purc_grn_dtl_gp_perc,2}})</td>
                    <td class="dtl-contents alignright">{{number_format($pro->tbl_purc_grn_dtl_sys_quantity)}}</td>
                </tr>
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
</table>
<table class="tab" valign="top">
    <td width="49%">
    </td>
    <td width="25%">
        {{-- -- Invoice Calculations -- --}}
        <table class="tableData" style="margin-top: 15px;">
            <thead>
                <tr>
                    <th colspan="100%" class="dtl-head headingcolor">Invoice Calculations</th>
                </tr>
            </thead>
            <tbody>
                @php
                // $Invoice_amount_after_disc = $totNetAmt - $totDisc;
                // $Invoice_net_amount = $Invoice_amount_after_disc + $totGST;
                @endphp
                <tr>
                    <td class="heading dtl-contents">Net Amount Vef. Inv. Disc:</td>
                    <td class="dtl-contents alignright">{{number_format($totNetAmt,3)}}</td>
                </tr>
                <tr>
                    <td class="heading dtl-contents">(Less) Invoice Discount:</td>
                    <td class="dtl-contents alignright">{{number_format(0,3)}}</td>
                </tr>
                <tr>
                    <td class="heading dtl-contents">Include in TP:</td>
                    <td class="dtl-contents alignright"></td>
                </tr>
                <tr>
                    <td class="dtl-contents">Advance Tax:</td>
                    <td class="dtl-contents alignright">{{number_format(0,3)}}</td>
                </tr>
                <tr>
                    <td class="dtl-contents" style="font-weight: bold">Net Amount:</td>
                    <td class="dtl-contents alignright" style="font-weight: bold">{{number_format($totNetAmt,3)}}</td>
                </tr>
            </tbody>
        </table>
    </td>
    <td width="1%"></td>
    <td width="25%">
        {{-- -- GP Calculations -- --}}
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
                    $gp_amount = $totGrossAmt - $totNetAmt;

                    $gp_perc = $gp_amount / $totNetAmt * 100;
                @endphp
                <tr>
                    <td class="heading dtl-contents">Net Retail Amount:</td>
                    <td class="dtl-contents alignright">{{number_format($totNetAmt,3)}}</td>
                </tr>
                <tr>
                    <td class="heading dtl-contents">Net Purchase Amount:</td>
                    <td class="dtl-contents alignright">{{number_format($totGrossAmt,3)}}</td>
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