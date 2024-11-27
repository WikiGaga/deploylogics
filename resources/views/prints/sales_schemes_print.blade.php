@php
//essential for header
    $heading = strtoupper($data['title']);
    $businessName = strtoupper(auth()->user()->business->business_short_name);
    $pdf_link = $data['print_link'];
    $print_type = $data['type'];

    $code = $data['current']->schemes_code;
    $date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->entry_date))));
    $status = $data['current']->is_active;
    $schemeName = $data['current']->scheme_name;
    $schemeRemarks = $data['current']->remarks;
    $schemeAvail = $data['current']->schemeAvail ?? [];
    $schemeSlab = $data['current']->schemeSlab ?? [];
@endphp
@permission($data['permission'])
    @extends('layouts.print_layout')
    @section('title', $heading)
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
                </td>
                <td width="33.33%">
                    <div>
                        <span class="heading heading-block">Status:</span>
                        <span class="normal normal-block">{{isset($status)?$status:''}}</span>
                    </div>
                    <div>
                        <span class="heading heading-block">Scheme Name:</span>
                        <span class="normal normal-block">{{ $schemeName }}</span>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
    @if(isset($schemeAvail) && count($schemeAvail) > 0)
        <h2>Direct Discount Items</h2>
        <table  class="tableData data_listing" id="document_table_data" style="margin-top: 10px">
            <thead>
                <tr>
                    <th width="4%" class="dtl-head">Sr No</th>
                    <th width="10%" class="dtl-head">Barcode</th>
                    <th width="12%" class="dtl-head alignleft">Product Name</th>
                    <th width="12%" class="dtl-head">Cost Rate</th>
                    <th width="12%" class="dtl-head">Sale Rate</th>
                    <th width="8%" class="dtl-head">Disc %</th>
                    <th width="7%" class="dtl-head">Disc Amt</th>
                    <th width="9%" class="dtl-head">Foc Qty</th>
                </tr>
            </thead>
            <tbody>
                @foreach($schemeAvail as $directDiscount)
                    @php
                        $costRate = App\Models\TblPurcProductBarcodePurchRate::where('product_barcode_id',$directDiscount->product_barcode_id)->where('branch_id',auth()->user()->branch_id)->first('product_barcode_cost_rate');
                        $saleRate = App\Models\TblPurcProductBarcodeSaleRate::where('product_barcode_id',$directDiscount->product_barcode_id)->where('branch_id',auth()->user()->branch_id)->first('product_barcode_sale_rate_rate');
                    @endphp
                    <tr>
                        <td class="dtl-contents">{{ $loop->iteration }}</td>
                        <td class="dtl-contents">{{ $directDiscount->barcode->product_barcode_barcode }}</td>
                        <td class="dtl-contents">{{ $directDiscount->product->product_name }}</td>
                        <td class="dtl-contents alignright">{{ number_format($costRate->product_barcode_cost_rate,3) }}</td>
                        <td class="dtl-contents alignright">{{ number_format($saleRate->product_barcode_sale_rate_rate,3) }}</td>
                        <td class="dtl-contents alignright">{{ $directDiscount->disc_perc }}</td>
                        <td class="dtl-contents alignright">{{ $directDiscount->disc }}</td>
                        <td class="dtl-contents alignright">{{ $directDiscount->foc_qty }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
    @if(isset($schemeSlab) && count($schemeSlab) > 0)
        @foreach($schemeSlab as $slab)
            <h2>Slab # {{ $loop->iteration }}</h2>
            <table  class="tableData data_listing" id="document_table_data" style="margin-top: 10px">
                <thead>
                    <tr>
                        <th colspan="4" class="alignleft dtl-head">Name : {{ $slab->slab_name }}</th>
                        <th colspan="4" class="alignleft dtl-head">Expiry : {{ date('Y-m-d' , strtotime($slab->expiry_date)) }}</th>
                    </tr>
                    <tr>
                        <th colspan="4" class="dtl-head alignleft">Min Sale : {{ $slab->min_sale }}</th>
                        <th colspan="4" class="dtl-head alignleft">Max Sale : {{ $slab->max_sale }}</th>
                    </tr>
                    <tr>
                        <th width="4%" class="dtl-head">Sr No</th>
                        <th width="10%" class="dtl-head">Barcode</th>
                        <th width="12%" class="dtl-head alignleft">Product Name</th>
                        <th width="8%" class="dtl-head">Cost Rate</th>
                        <th width="8%" class="dtl-head">Sale Rate</th>
                        <th width="8%" class="dtl-head">Disc %</th>
                        <th width="7%" class="dtl-head">Disc Amt</th>
                        <th width="9%" class="dtl-head">Foc Qty</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($slab->dtls) && count($slab->dtls) > 0)
                        @foreach($slab->dtls as $dtl)
                            @php
                                $costRate = App\Models\TblPurcProductBarcodePurchRate::where('product_barcode_id',$dtl->product_barcode_id)->where('branch_id',auth()->user()->branch_id)->first('product_barcode_cost_rate');
                                $saleRate = App\Models\TblPurcProductBarcodeSaleRate::where('product_barcode_id',$dtl->product_barcode_id)->where('branch_id',auth()->user()->branch_id)->first('product_barcode_sale_rate_rate');
                            @endphp
                            <tr>
                                <td class="dtl-contents">{{ $loop->iteration }}</td>
                                <td class="dtl-contents">{{ $dtl->barcode->product_barcode_barcode }}</td>
                                <td class="dtl-contents">{{ $dtl->product->product_name }}</td>
                                <td class="dtl-contents alignright">{{ number_format($costRate->product_barcode_cost_rate,3) }}</td>
                                <td class="dtl-contents alignright">{{ number_format($saleRate->product_barcode_sale_rate_rate,3) }}</td>
                                <td class="dtl-contents alignright">{{ $dtl->disc_perc }}</td>
                                <td class="dtl-contents alignright">{{ $dtl->disc }}</td>
                                <td class="dtl-contents alignright">{{ $dtl->foc_qty }}</td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        @endforeach
    @endif
    <table width="100%">
        <tbody>
        <tr>
            <td width="70%">
                <div style="font-weight:bold;font-size: 11px;">
                </div>
                <div style="font-size: 11px;margin-top: 10px">
                    <b>Remarks:</b> {{isset($schemeRemarks)?$schemeRemarks:''}}
                </div>
                <div style="margin-top: 20px;">
                    <table class="tab">
                        <tr>
                            <td width="50%" valign="top">
                                <table class="tab">
                                    <tr>
                                        <th style="padding-top: 70px" colspan="2" class="heading aligncenter"><hr class="sign-line">Signature</th>
                                    </tr>
                                </table>
                            </td>
                            <td width="50%" valign="top">

                            </td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
        </tbody>
    </table>
    @endsection

    @section('customJS')
    @endsection
@endpermission
