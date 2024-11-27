@extends('layouts.print_layout')
@section('pageCSS')
@endsection
@php
    $heading = strtoupper($data['title']); 
    $businessName = strtoupper(auth()->user()->business->business_short_name);
    $data['pdf_link'] = '';
    $print_type = '';
    $print = $data['type'];

    if(isset($data['current'])){
        //$type = $data['stock_code_type'];
        $code = $data['current']->stock_code;
        $date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->stock_date))));
        $branch_to = isset($data['branch_to']->branch_name)?$data['branch_to']->branch_name:'';
        //$stock_location = isset($data['display_location'])?$data['display_location']->display_location_name:'';
        $notes = '';//$data['current']->stock_remarks;
        //$dtls = isset($data['current']->audit_stock_dtls)? $data['current']->audit_stock_dtls :[];
        $dtls = isset($data['current_dtl'])? $data['current_dtl'] :[];
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
                <span class="heading heading-block">Store :</span>
                <span class="normal normal-block">{{isset($branch_to)?$branch_to:''}}</span>
            </div>
            <div>
                <span class="heading heading-block">Stock Location :</span>
                <span class="normal normal-block">{{isset($stock_request_code)?$stock_request_code:''}}</span>
            </div>
        </td>
        <td width="33.33%"></td>
        <td width="33.33%">
            <div>
                <span class="heading heading-block">Date :</span>
                <span class="normal normal-block">{{isset($date)?$date:''}}</span>
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
                    $headings = ['Sr No','Barcode','Product Name','Unit Name','Package','Physical Stock Qty',
                    'Stock Qty','Adjustment Qty'];
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
            <th width="37%" class="dtl-head alignleft">Product Name</th>
            <th width="5%" class="dtl-head alignleft">Unit Name</th>        
            <th width="9%" class="dtl-head">Physical Stock Qty</th>         
            <th width="9%" class="dtl-head">Stock Qty</th>
            <th width="9%" class="dtl-head">Adjustment Qty</th>       
            <th width="5%" class="dtl-head">Cost Rate</th>           
            <th width="12%" class="dtl-head">Cost Amount</th>       
        </tr>
    </thead>
    <tbody>
        @php
            $totphyQty = 0;
            $totstockQty = 0;
            $totadjustQty = 0;
            $totalcost_amount = 0;
            $i=0;
        @endphp
        @if(isset($dtls))
            @foreach ($dtls as $dtl)
                @php
                    $totphyQty += $dtl->stock_dtl_physical_quantity;
                    $totstockQty += $dtl->stock_dtl_stock_quantity;
                    $totadjustQty += $dtl->stock_dtl_quantity;
                    $totalcost_amount += $dtl->cost_amount;
                @endphp
                <tr>
                    <td class="dtl-contents aligncenter">{{ $loop->iteration }}</td>
                    <td class="dtl-contents alignleft">{{$dtl->product_barcode_barcode}}</td>
                    <td class="dtl-contents alignleft">{{$dtl->product_name}}</td>
                    <td class="dtl-contents alignleft">{{$dtl->uom_name}}</td>
                    <td class="dtl-contents alignright">{{number_format($dtl->stock_dtl_physical_quantity,3)}}</td>
                    <td class="dtl-contents alignright">{{number_format($dtl->stock_dtl_stock_quantity,3)}}</td>                  
                    <td class="dtl-contents alignright">{{number_format($dtl->stock_dtl_quantity,3)}}</td>
                    <td class="dtl-contents alignright">{{$dtl->cost_rate}}</td>
                    <td class="dtl-contents alignright">{{number_format($dtl->cost_amount,3)}}</td>
                </tr>
            @endforeach
            <tr>
                <td class="dtl-head alignright border-right"></td>
                <td class="dtl-head alignright border-right"></td>
                <td class="dtl-head alignright border-right"></td>
                <td class="dtl-head alignright"></td>
                <td class="dtl-head alignright">{{number_format($totphyQty,0)}}</td>
                <td class="dtl-head alignright">{{number_format($totstockQty,0)}}</td>
                <td class="dtl-head alignright">{{number_format($totadjustQty,0)}}</td>
                <td class="dtl-head alignright"></td>
                <td class="dtl-head alignright">{{number_format($totalcost_amount,0)}}</td>
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