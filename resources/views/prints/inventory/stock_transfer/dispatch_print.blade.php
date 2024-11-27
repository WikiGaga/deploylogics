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
        $notes = $data['current']->stock_remarks;
        $dtls = isset($data['current']->stock_dtls)? $data['current']->stock_dtls :[];
        $supplier_name = isset($data['supplier'])? $data['supplier']->supplier_name :"";
        $qty = ($type == 'sa')?'Stock':'';
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
                <span class="heading heading-block">To Transfer Branch :</span>
                <span class="normal normal-block">{{isset($branch_to)?$branch_to:''}}</span>
            </div>
            <div>
                <span class="heading heading-block">Stock Request From :</span>
                <span class="normal normal-block">{{isset($stock_request_code)?$stock_request_code:''}}</span>
            </div>
        </td>
        <td width="33.33%"></td>
        <td width="33.33%">
            <div>
                <span class="heading heading-block">Date :</span>
                <span class="normal normal-block">{{isset($date)?$date:''}}</span>
            </div>
            <div>
                <span class="heading heading-block">Store From :</span>
                <span class="normal normal-block">{{isset($store_from)?$store_from:''}}</span>
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
                    $headings = ['Sr No','Barcode','Product Name','Qty/CTN','CTN','PCS','Qty','M.R.P',
                    'Sale Rate','Purc Rate','Net Amt',];
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
            <th width="4%" class="dtl-head">Sr No</th>
            <th width="10%" class="dtl-head alignleft">Barcode</th>
            <th width="15%" class="dtl-head alignleft">Product Name</th>    
            <th width="7%" class="dtl-head">Qty/CTN</th>      
            <th width="7%" class="dtl-head">CTN</th>      
            <th width="7%" class="dtl-head">PCS</th>     
            <th width="7%" class="dtl-head">Qty</th>
            <th width="5%" class="dtl-head aligncenter">MRP</th>           
            <th width="9%" class="dtl-head">Sale Rate</th>
            <th width="9%" class="dtl-head">Purc Rate</th>            
            <th width="7%" class="dtl-head">Net Amount</th>    
        </tr>
    </thead>
    <tbody>
        @php
            $totQty = 0;
            $totGrossAmt = 0;
            $totVatAmt = 0;
            $totNetAmt = 0;
            $i=0;
        @endphp
        @php
        $new_dtls = [];
        @endphp
        @if(isset($dtls))
            @foreach ($dtls as $dtl)
                @php
                    $pro = App\Models\TblPurcGrnDtl::where('product_id',$dtl->product_id)->where('grn_type','GRN')->where('branch_id',auth()->user()->branch_id)->orderBy('created_at','desc')->first();
                    $i++;
                    $stock_production_date = date('d-m-Y', strtotime(trim(str_replace('/','-',$dtl->stock_dtl_production_date))));
                    $stock_expiry_date = date('d-m-Y', strtotime(trim(str_replace('/','-',$dtl->stock_dtl_expiry_date))));                    
                    $qty = $dtl->stock_dtl_quantity;
                    $totQty += $dtl->stock_dtl_quantity;
                    $totGrossAmt += $dtl->stock_dtl_amount;
                    $totVatAmt += $dtl->stock_dtl_vat_amount;
                    $totNetAmt += $dtl->stock_dtl_total_amount;   
                    
                    if(isset($pro->tbl_purc_grn_dtl_mrp)){
                        $mrp   = $pro->tbl_purc_grn_dtl_mrp;
                    }else{
                        $mrp =0;
                    }

                    $sql = App\Models\TblPurcProductBarcode::where('product_barcode_barcode',$dtl->product_barcode_barcode)->first();
                    $size_id = (int)$sql->size_id;
                    
                    if(!empty($size_id)){
                        $unit_per_ctn = $size_id;
                    }else{
                        $unit_per_ctn = 0;
                    }
                    if($unit_per_ctn > 0)
                    {
                        $convert = @round($qty / $unit_per_ctn,2);
                        $array = explode(".",$convert);
                        $ctn = $array[0];
                        $pcs = $qty - ($array[0] * $unit_per_ctn);
                    }else{
                        $ctn = 0;
                        $pcs = 0;
                    }
                    
                @endphp
                <tr>
                    <td class="dtl-contents aligncenter">{{ $loop->iteration }}</td>
                    <td class="dtl-contents alignleft">{{$dtl->product_barcode_barcode}}</td>
                    <td class="dtl-contents alignleft">{{$dtl->product->product_name}}</td>  <td class="dtl-contents aligncenter">{{$unit_per_ctn}}</td>
                    <td class="dtl-contents aligncenter">{{$ctn}}</td>
                    <td class="dtl-contents aligncenter">{{$pcs}}</td>
                    <td class="dtl-contents aligncenter">{{$qty}}</td>
                    <td class="dtl-contents aligncenter">{{$mrp}}</td>
                    <td class="dtl-contents alignright">{{number_format($dtl->stock_dtl_rate,3)}}</td>
                    <td class="dtl-contents alignright">{{number_format($dtl->stock_dtl_purc_rate,3)}}</td>                  
                    <td class="dtl-contents alignright">{{number_format($dtl->stock_dtl_amount,3)}}</td>
                </tr>
            @endforeach
            <tr>
                <td class="dtl-head alignright border-right"></td>
                <td class="dtl-head alignright border-right"></td>
                <td class="dtl-head alignright border-right"></td>
                <td class="dtl-head alignright border-right"></td>
                <td class="dtl-head alignright border-right"></td>
                <td class="dtl-head alignright border-right"></td>
                <td class="dtl-head aligncenter">{{number_format($totQty,0)}}</td>
                <td class="dtl-head alignright border-right"></td>
                <td class="dtl-head alignright border-right"></td>
                <td class="dtl-head alignright"></td>
                <td class="dtl-head alignright">{{number_format($totGrossAmt,3)}}</td>
            </tr>
        @endif
    </tbody>
</table>
<table class="tab" valign="top">
    @if(isset($notes))
        <tr>
            <th class="heading alignleft">Notes:</th>
        </tr>
        <tr>
            <td class="normal alignleft paddingNotes">{{($notes)}}</td>
        </tr>
    @endif
</table>
@endsection
@endpermission