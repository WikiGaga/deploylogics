@php
    $heading = strtoupper($data['title']);
    $businessName = strtoupper(auth()->user()->business->business_short_name);
    if(isset($data['current'])){
        $id = $data['current']->sales_order_id;
        $code = $data['current']->sales_order_code;
        $date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->sales_order_date))));
        $customer_id = Auth::user()->id;
        $customer_name = Auth::user()->name;
        $remarks = $data['current']->sales_order_remarks;
        $dtls = isset($data['current']->dtls)? $data['current']->dtls :[];
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
                </td>
                <td width="33.33%">
                </td>
                <td width="33.33%">
                    <div>
                        <span class="heading heading-block">Date :</span>
                        <span class="normal normal-block">{{isset($date)?$date:''}}</span>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
        <table class="tableData" style="margin-top: 10px">
            <thead>
            <tr>
                <th width="5%" class="dtl-head">Sr No</th>
                <th width="10%" class="dtl-head alignleft">Barcode</th>
                <th width="20%" class="dtl-head alignleft">Product Name</th>
                <th width="20%" class="dtl-head">Arabic Name</th>
                <th width="5%" class="dtl-head">UOM</th>
                <th width="5%" class="dtl-head">Packing</th>
                <th width="5%" class="dtl-head">Rate</th>
                <th width="5%" class="dtl-head">Amount</th>
                <th width="5%" class="dtl-head">Disc Amt</th>
                <th width="5%" class="dtl-head">Vat Amt</th>
                <th width="5%" class="dtl-head">Gross Amt</th>
            </tr>
            </thead>
            <tbody>
            @if(isset($dtls))
                @php
                    $row_count=0;
                @endphp
                @foreach($dtls as $data)
                    @php
                        $row_count += 1;
                    @endphp
                    <tr>
                        <td class="dtl-contents aligncenter">{{ $loop->iteration }}</td>
                        <td class="dtl-contents">{{$data->sales_order_dtl_barcode}}</td>
                        <td class="dtl-contents">{{$data->product->product_name}}</td>
                        <td class="dtl-contents alignright">{{$data->product->product_arabic_name}}</td>
                        <td class="dtl-contents aligncenter">{{$data->uom->uom_name}}</td>
                        <td class="dtl-contents aligncenter">{{$data->sales_order_dtl_packing}}</td>
                        <td class="dtl-contents alignright">{{number_format($data->sales_order_dtl_rate,3)}}</td>
                        <td class="dtl-contents alignright">{{number_format($data->sales_order_dtl_amount,3)}}</td>
                        <td class="dtl-contents alignright">{{number_format($data->sales_order_dtl_disc_amount,3)}}</td>
                        <td class="dtl-contents alignright">{{number_format($data->sales_order_dtl_vat_amount,3)}}</td>
                        <td class="dtl-contents alignright">{{number_format($data->sales_order_dtl_total_amount,3)}}</td>
                    </tr>
                @endforeach
            @endif
            @if($row_count <= 9)
                @for ($z = 0; $z < (9 - $row_count); $z++)
                    <tr>
                        <td>&nbsp</td>
                        <td>&nbsp</td>
                        <td>&nbsp</td>
                        <td>&nbsp</td>
                        <td>&nbsp</td>
                        <td>&nbsp</td>
                        <td>&nbsp</td>
                        <td>&nbsp</td>
                        <td>&nbsp</td>
                        <td>&nbsp</td>
                        <td>&nbsp</td>
                    </tr>
                @endfor
            @endif
            </tbody>
        </table>
    @endsection

    @section('customJS')
    @endsection
@endpermission
