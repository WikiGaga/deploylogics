@php
    $heading = strtoupper($data['title']);
    $businessName = strtoupper(auth()->user()->business->business_short_name);
   
    if(isset($data['current'])){
        $id = $data['current']->lpo_id;
        $code = $data['current']->lpo_code;
        $date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->lpo_date))));
        $currency_name = $data['currency']->currency_name;
        $exchange_rate = $data['current']->lpo_exchange_rate;
        $notes = $data['current']->lpo_remarks;
        $dtls = $data['current']->dtls;
    }else{
        abort('404'); 
    }

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
                        <span class="heading heading-block">Currency :</span>
                        <span class="normal normal-block">{{isset($currency_name)?$currency_name:''}}</span>
                    </div>
                </td>
                <td width="33.33%"></td>
                <td width="33.33%">
                    <div>
                        <span class="heading heading-block">Date :</span>
                        <span class="normal normal-block">{{isset($date)?$date:''}}</span>
                    </div>
                    <div>
                        <span class="heading heading-block">Exchange Rate :</span>
                        <span class="normal normal-block">{{isset($exchange_rate)?$exchange_rate:''}}</span>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
    <table class="tableData" style="margin-top: 10px">
        <thead>
            <tr>
                <th class="dtl-head">Sr No</th>
                <th class="dtl-head alignleft">Branch Name</th>
                <th class="dtl-head alignleft">Barcode</th>
                <th class="dtl-head alignleft">Product Name</th>
                <th class="dtl-head alignleft">Sup. Name</th>
                <th class="dtl-head alignleft">Payment mode</th>
                <th class="dtl-head aligncenter">Qty</th>
                <th class="dtl-head aligncenter">FC Rate</th>
                <th class="dtl-head aligncenter">Rate</th>
                <th class="dtl-head aligncenter">Amount</th>
                <th class="dtl-head aligncenter">Disc %</th>
                <th class="dtl-head aligncenter">Disc Amt</th>
                <th class="dtl-head aligncenter">VAT %</th>
                <th class="dtl-head aligncenter">VAT Amt</th>
                <th class="dtl-head aligncenter">Gross Amt</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totGrossAmt = 0;
                $i=0;
            @endphp
            @if(isset($dtls))
                @foreach($dtls as $dtl)
                    @php 
                        $totGrossAmt += $dtl->lpo_dtl_gross_amount;
                    @endphp
                    <tr>
                        <td class="dtl-contents aligncenter">{{ $loop->iteration }}</td>
                        <td class="dtl-contents alignleft">{{$dtl->branch->branch_name}}</td>
                        <td class="dtl-contents alignleft">{{$dtl->product_barcode_barcode}}</td>
                        <td class="dtl-contents alignleft">{{$dtl->product->product_name}}</td>
                        <td class="dtl-contents alignleft">{{isset($dtl->supplier->supplier_name)?$dtl->supplier->supplier_name:''}}</td>
                        <td class="dtl-contents alignleft">{{isset($dtl->payment_mode_id)?$dtl->payment_mode_id:''}}</td>
                        <td class="dtl-contents aligncenter">{{$dtl->lpo_dtl_quantity}}</td>
                        <td class="dtl-contents alignright">{{number_format($dtl->lpo_dtl_fc_rate,3)}}</td>
                        <td class="dtl-contents alignright">{{number_format($dtl->lpo_dtl_rate,2)}}</td>
                        <td class="dtl-contents alignright">{{number_format($dtl->lpo_dtl_amount,3)}}</td>
                        <td class="dtl-contents alignright">{{number_format($dtl->lpo_dtl_disc_percent,2)}}</td>
                        <td class="dtl-contents alignright">{{number_format($dtl->lpo_dtl_disc_amount,3)}}</td>
                        <td class="dtl-contents alignright">{{number_format($dtl->lpo_dtl_vat_percent,2)}}</td>
                        <td class="dtl-contents alignright">{{number_format($dtl->lpo_dtl_vat_amount,3)}}</td>
                        <td class="dtl-contents alignright">{{number_format($dtl->lpo_dtl_gross_amount,3)}}</td>
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
                    </tr>
                @endfor
            @endif
        </tbody>
    </table>
    <table class="tab">
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
                        <td width="60%" class="heading alignleft" >NetTotal</td>
                        <td width="40%" class="heading alignright">{{number_format($totGrossAmt,3)}}</td>
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
    @endsection

    @section('customJS')
    @endsection
@endpermission
