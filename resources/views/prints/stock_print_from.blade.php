@php
    $heading = strtoupper($data['title']);
    $businessName = strtoupper(auth()->user()->business->business_short_name);
    $data['pdf_link'] = '';
    $print_type = '';

    if(isset($data['current'])){
        $type = $data['stock_code_type'];
        $code = $data['current']->stock_code;
        $date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->stock_date))));
        $store_from = isset($data['store_from']->store_name)?$data['store_from']->store_name:'';
        $store_to = isset($data['store_to']->store_name)?$data['store_to']->store_name:'';
        $branch_from = isset($data['branch_from']->branch_name)?$data['branch_from']->branch_name:'';
        $branch_to = isset($data['branch_to']->branch_name)?$data['branch_to']->branch_name:'';
        $product_name = isset($data['current']->product->product_name)?$data['current']->product->product_name:'';
        $assamble_qty = isset($data['current']->assamble_qty)?$data['current']->assamble_qty:'';
        $stock_location = isset($data['display_location'])?$data['display_location']->display_location_name:'';
        $stock_request_id = $data['current']->stock_request_id;
        if(isset($stock_request_id) && $type == 'st'){
            $demand = \App\Models\TblPurcDemand::where('demand_id',(int)$stock_request_id)->first();
            if($demand != null){
                $stock_request_code = $demand->demand_no;
            }
        }
        if(isset($stock_request_id) && $type == 'str'){
            $stock = \App\Models\TblInveStock::where('stock_id',(int)$stock_request_id)->first();
            if($stock != null){
                $stock_from = $stock->stock_code;
            }
        }
        $notes = $data['current']->stock_remarks;
        $dtls = isset($data['current']->stock_dtls)? $data['current']->stock_dtls :[];
        $supplier_name = isset($data['supplier'])? $data['supplier']->supplier_name :"";
        $qty = ($type == 'sa')?'Stock':'';
    }else{
        abort('404');
    }

@endphp
@extends('layouts.print_layout_from')
@section('title', $heading)
@section('heading', $heading)

@section('pageCSS')
@endsection

@section('content')
    <table class="head" width="100%">
        <tbody>
        <tr>
            <td width="50%">
                @php
                    $QrCode = new \TheUmar98\BarcodeBundle\Utils\QrCode();
                    $QrCode->setText($code);
                    $QrCode->setExtension('jpg');
                    $QrCode->setSize(40);
                    $image = $QrCode->generate();
                @endphp
                @if(isset($image) && $image != '')
                    <img src="data:image/png;base64,{{$image}}" />

                @else
                    <div></div>
                @endif
            </td>
            <td width="50%">
                <div class="title aligncenter">@yield('heading')</div>
                <div class="title aligncenter" style="font-weight:normal; font-size:14px;">{{$data['branch_from']->branch_name}}</div>
                <div class="title aligncenter" style="font-weight:normal; font-size:11px;"><b>Tax No:</b>{{$data['branch_from']->branch_tax_certificate_no}}</div>
                <div class="title aligncenter" style="font-weight:normal; font-size:11px;"><b>Phone:</b>{{$data['branch_from']->branch_mobile_no}} <b>Fax:</b>{{$data['branch_from']->branch_fax}}</div>
                <div class="title aligncenter" style="font-weight:normal; font-size:11px;"><b>Email:</b>{{$data['branch_from']->branch_email}}</div>
            </td>
        </tr>
        </tbody>
    </table>
    <table class="tableData" style="margin-top: 5px">
        <tbody>
        <tr>
            <td width="33.33%">
                <div>
                    <span class="heading heading-block">Code :</span>
                    <span class="normal normal-block">{{isset($code)?$code:''}}</span>
                </div>
                @if($type == 'st')
                    <div>
                        <span class="heading heading-block">To Transfer Branch :</span>
                        <span class="normal normal-block">{{isset($branch_to)?$branch_to:''}}</span>
                    </div>
                    <div>
                        <span class="heading heading-block">Stock Request From :</span>
                        <span class="normal normal-block">{{isset($stock_request_code)?$stock_request_code:''}}</span>
                    </div>
                @endif
                @if($type == 'str')
                    <div>
                        <span class="heading heading-block">From Branch :</span>
                        <span class="normal normal-block">{{isset($branch_from)?$branch_from:''}}</span>
                    </div>
                    <div>
                        <span class="heading heading-block">Stock From :</span>
                        <span class="normal normal-block">{{isset($stock_from)?$stock_from:''}}</span>
                    </div>
                @endif
                @if($type == 'ass' || $type == 'dss')
                    <div>
                        <span class="heading heading-block">Product :</span>
                        <span class="normal normal-block">{{isset($product_name)?$product_name:''}}</span>
                    </div>
                @endif
                @if($type == 'os' || $type == 'sa' || $type == 'di' || $type == 'ei' || $type == 'sp' || $type == 'ass' || $type == 'ri' )
                    <div>
                        <span class="heading heading-block">Store :</span>
                        <span class="normal normal-block">{{isset($store_from)?$store_from:''}}</span>
                    </div>
                @endif
                @if($type == 'os')
                    <div>
                        <span class="heading heading-block">Rate By :</span>
                        <span class="normal normal-block"></span>
                    </div>
                @endif
                @if($type == 'ist')
                    <div>
                        <span class="heading heading-block">From Store :</span>
                        <span class="normal normal-block">{{isset($store_from)?$store_from:''}}</span>
                    </div>
                @endif
                @if($type == 'ist')
                    <div>
                        <span class="heading heading-block">Supplier Name :</span>
                        <span class="normal normal-block">{{isset($supplier_name)?$supplier_name:''}}</span>
                    </div>
                @endif
                @if($type == 'dss')
                    <div>
                        <span class="heading heading-block">To Store :</span>
                        <span class="normal normal-block">{{isset($store_to)?$store_to:''}}</span>
                    </div>
                @endif
            </td>
            <td width="33.33%"></td>
            <td width="33.33%">
                <div>
                    <span class="heading heading-block">Date :</span>
                    <span class="normal normal-block">{{isset($date)?$date:''}}</span>
                </div>
                @if($type == 'os')
                    <div>
                        <span class="heading heading-block">Stock Location :</span>
                        <span class="normal normal-block">{{isset($stock_location)?$stock_location:''}}</span>
                    </div>
                @endif
                @if($type == 'st')
                    <div>
                        <span class="heading heading-block">Store From :</span>
                        <span class="normal normal-block">{{isset($store_from)?$store_from:''}}</span>
                    </div>
                @endif
                @if($type == 'str')
                    <div>
                        <span class="heading heading-block">Store :</span>
                        <span class="normal normal-block">{{isset($store_to)?$store_to:''}}</span>
                    </div>
                @endif
                @if($type == 'ass' || $type == 'dss')
                    <div>
                        <span class="heading heading-block">Qty :</span>
                        <span class="normal normal-block">{{isset($assamble_qty)?$assamble_qty:''}}</span>
                    </div>
                @endif
                @if($type == 'ist')
                    <div>
                        <span class="heading heading-block">To Store :</span>
                        <span class="normal normal-block">{{isset($store_to)?$store_to:''}}</span>
                    </div>
                @endif
            </td>
        </tr>
        </tbody>
    </table>
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
    <table class="tableData data_listing" id="document_table_data" style="margin-top: 10px">
        <thead>
        <tr>
            <th width="4%" class="dtl-head">Sr No</th>
            <th width="10%" class="dtl-head alignleft">Barcode</th>
            <th width="15%" class="dtl-head alignleft">Product Name</th>
            <th width="5%" class="dtl-head alignleft">UOM</th>
            <th width="7%" class="dtl-head">{{$qty}} Qty</th>
            @if($type != 'sa' && $type!= 'ass' && $type!= 'dss')
                <th width="9%" class="dtl-head">Sale Rate</th>
                <th width="9%" class="dtl-head">Purc Rate</th>
            @endif
            @if($type == 'os')
                <th width="13%" class="dtl-head">Production Date</th>
            @endif
            @if($type == 'os' || $type == 'ist')
                <th width="13%" class="dtl-head">Expiry Date</th>
            @endif
            @if($type== 'sa')
                <th width="13%" class="dtl-head">Physical Stock Qty</th>
                <th width="13%" class="dtl-head">Adjustment Qty</th>
            @endif
            @if($type== 'ass' || $type== 'dss')
                <th width="10%" class="dtl-head">Store</th>
            @else
                <th width="10%" class="dtl-head">Batch No</th>
            @endif
            @if($type != 'sa' && $type!= 'ass' && $type!= 'dss')
                <th width="7%" class="dtl-head">Amount</th>
            @endif
            @if($type == 'st' || $type == 'str')
                <th width="7%" class="dtl-head">VAT %</th>
                <th width="7%" class="dtl-head">VAT Amt</th>
                <th width="10%" class="dtl-head">Gross Amt</th>
            @endif
        </tr>
        </thead>
        <tbody>
        @php
            $totGrossAmt = 0;
            $totVatAmt = 0;
            $totNetAmt = 0;
            $i=0;
        @endphp
        @if(isset($dtls))
            @foreach($dtls as $dtl)
                @php
                    $i++;
                    $stock_production_date = date('d-m-Y', strtotime(trim(str_replace('/','-',$dtl->stock_dtl_production_date))));
                    $stock_expiry_date = date('d-m-Y', strtotime(trim(str_replace('/','-',$dtl->stock_dtl_expiry_date))));

                    if($type == 'sa'){
                        $qty = $dtl->stock_dtl_stock_quantity;
                        $totGrossAmt += $dtl->stock_dtl_quantity;
                    }else if($type== 'ass' || $type== 'dss'){
                        $store = \App\Models\TblDefiStore::where('store_id',$dtl->stock_dtl_store)->pluck('store_name')->first();
                        $qty = $dtl->stock_dtl_quantity;
                        $totGrossAmt += $dtl->stock_dtl_quantity;
                    }else if($type == 'st' || $type == 'str'){
                        $qty = $dtl->stock_dtl_quantity;
                        $totGrossAmt += $dtl->stock_dtl_amount;
                        $totVatAmt += $dtl->stock_dtl_vat_amount;
                        $totNetAmt += $dtl->stock_dtl_total_amount;
                    }else{
                        $qty = $dtl->stock_dtl_quantity;
                        $totGrossAmt += $dtl->stock_dtl_amount;
                    }
                @endphp
                <tr>
                    <td class="dtl-contents aligncenter">{{ $loop->iteration }}</td>
                    <td class="dtl-contents alignleft">{{$dtl->product_barcode_barcode}}</td>
                    <td class="dtl-contents alignleft">{{$dtl->product->product_name}}</td>
                    <td class="dtl-contents alignleft">{{$dtl->uom->uom_name}}</td>
                    <td class="dtl-contents aligncenter">{{$qty}}</td>
                    @if($type != 'sa' && $type != 'ass' && $type != 'dss')
                        @if($type == 'str')
                            <td class="dtl-contents alignright">{{number_format(0,3)}}</td>
                            <td class="dtl-contents alignright">{{number_format($dtl->stock_dtl_rate,3)}}</td>
                        @else
                            <td class="dtl-contents alignright">{{number_format($dtl->stock_dtl_rate,3)}}</td>
                            <td class="dtl-contents alignright">{{number_format($dtl->stock_dtl_purc_rate,3)}}</td>
                        @endif
                    @endif
                    @if($type == 'os')
                        <td class="dtl-contents aligncenter">{{($stock_production_date !='01-01-1970')?$stock_production_date:''}}</td>
                        <td class="dtl-contents aligncenter">{{($stock_expiry_date !='01-01-1970')?$stock_expiry_date:''}}</td>
                    @endif
                    @if($type == 'ist')
                        <td class="dtl-contents aligncenter">{{($stock_expiry_date !='01-01-1970')?$stock_expiry_date:''}}</td>
                    @endif
                    @if($type == 'sa')
                        <td class="dtl-contents aligncenter">{{$dtl->stock_dtl_physical_quantity}}</td>
                        <td class="dtl-contents aligncenter">{{$dtl->stock_dtl_quantity}}</td>
                    @endif
                    @if($type == 'ass' || $type == 'dss')
                        <td class="dtl-contents aligncenter">{{$store}}</td>
                    @else
                        <td class="dtl-contents aligncenter">{{$dtl->stock_dtl_batch_no}}</td>
                    @endif
                    @if($type != 'sa' && $type != 'ass' && $type != 'dss')
                        <td class="dtl-contents alignright">{{number_format($dtl->stock_dtl_amount,3)}}</td>
                    @endif
                    @if($type == 'st' || $type == 'str')
                        <td class="dtl-contents alignright">{{number_format($dtl->stock_dtl_vat_percent,3)}}</td>
                        <td class="dtl-contents alignright">{{number_format($dtl->stock_dtl_vat_amount,3)}}</td>
                        <td class="dtl-contents alignright">{{number_format($dtl->stock_dtl_total_amount,3)}}</td>
                    @endif
                </tr>
            @endforeach
        @endif
        @if($i<=8)
            @for ($z = 0; $z <= 8; $z++)
                <tr>
                    @if($type == 'os' || $type == 'st' || $type == 'str')
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    @endif
                    @if( $type!= 'ass' && $type!= 'dss' )
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    @endif
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    @if($type == 'st' || $type == 'str' || $type == 'ei' || $type == 'sp')
                        <td>&nbsp;</td>
                    @endif
                    @if($type == 'ist')
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    @endif
                </tr>
            @endfor
        @endif
        </tbody>
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
                    @if($type == 'st' || $type == 'str')
                        @if($totVatAmt !='' || $totVatAmt !=0)
                            <tr>
                                <td width="60%" class="heading alignleft" >Amt Total</td>
                                <td width="40%" class="heading alignright">{{number_format($totGrossAmt,3)}}</td>
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
                            <td class="heading alignright">{{number_format($totNetAmt,3)}}</td>
                        </tr>
                    @else
                        <tr>
                            <td width="60%" class="heading alignleft" >Net Total</td>
                            <td width="40%" class="heading alignright">{{number_format($totGrossAmt,3)}}</td>
                        </tr>
                    @endif
                    <tr>
                        <td colspan="2">
                            <hr style="height:2px;border-width:0;color:#000;background-color:#000">
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
@endsection

@section('customJS')
@endsection
