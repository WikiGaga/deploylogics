@php
//essential for header
    $heading = strtoupper($data['title']);
    $businessName = strtoupper(auth()->user()->business->business_short_name);
    $pdf_link = $data['print_link'];
    $print_type = $data['type'];

    $id = $data['current']->sup_prod_id;
    $code = $data['current']->sup_prod_code;
    $date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->sup_prod_date))));
    $supplier_name = isset($data['current']->supplier->supplier_name)?$data['current']->supplier->supplier_name:'';
    $phon_no = isset($data['current']->supplier->supplier_phone_1)?$data['current']->supplier->supplier_phone_1:'';
    $fax_no = isset($data['current']->supplier->supplier_fax)?$data['current']->supplier->supplier_fax:'';
    $tax_no = isset($data['current']->supplier->supplier_tax_no)?$data['current']->supplier->supplier_tax_no:'';
    $email = isset($data['current']->supplier->supplier_email)?$data['current']->supplier->supplier_email:'';
    $currency = $data['currency']->currency_name;
    $exchange_rate = $data['current']->sup_prod_exchange_rate;
    $notes = $data['current']->sup_prod_remarks;
    $dtls = $data['current']->sub_prod;
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
                        <span class="heading heading-block">Supplier:</span>
                        <span class="normal normal-block">{{isset($supplier_name)?$supplier_name:''}}</span>
                    </div>
                    <div>
                        <span class="heading heading-block">Supplier Tax No:</span>
                        <span class="normal normal-block">{{isset($tax_no)?$tax_no:''}}</span>
                    </div>
                    <div>
                        <span class="heading heading-block">Supplier Phone No:</span>
                        <span class="normal normal-block">{{isset($phon_no)?$phon_no:''}}</span>
                    </div>
                    <div>
                        <span class="heading heading-block">Supplier Fax No:</span>
                        <span class="normal normal-block">{{isset($fax_no)?$fax_no:''}}</span>
                    </div>
                    <div>
                        <span class="heading heading-block">Supplier Email:</span>
                        <span class="normal normal-block">{{isset($email)?$email:''}}</span>
                    </div>
                </td>
                <td width="33.33%"></td>
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
                        <span class="heading heading-block">Currency:</span>
                        <span class="normal normal-block">{{isset($currency)?$currency:''}}</span>
                    </div>
                    <div>
                        <span class="heading heading-block">Exchange Rate:</span>
                        <span class="normal normal-block">{{isset($exchange_rate)?$exchange_rate:''}}</span>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
    @if($print_type != 'pdf')
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
                            $headings = ['Sr No','Code','Product Name','Sup Barcode','Sup Description',
                                        'Sup UOM','Sup Packing','Sup Category','Sup Brand','Sup Pur Price','Sup Sale Price',
                                        'Sup VAT %','Sup HS Code'];
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
    <table  class="tableData data_listing" id="document_table_data" style="margin-top: 10px">
        <thead>
            <tr>
                <th width="4%" class="dtl-head">Sr No</th>
                <th width="10%" class="dtl-head">Code</th>
                <th width="12%" class="dtl-head alignleft">Product Name</th>
                <th width="7%" class="dtl-head">Sup Barcode</th>
                <th width="9%" class="dtl-head alignleft">Sup Description</th>
                <th width="5%" class="dtl-head">Sup UOM</th>
                <th width="6%" class="dtl-head">Sup Packing</th>
                <th width="6%" class="dtl-head">Sup Category</th>
                <th width="7%" class="dtl-head">Sup Brand</th>
                <th width="8%" class="dtl-head">Sup Pur Price</th>
                <th width="6%" class="dtl-head">Sup Sale Price</th>
                <th width="7%" class="dtl-head">Sup VAT %</th>
                <th width="6%" class="dtl-head">'Sup HS Code'</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($dtls))
                @foreach($dtls as $data)
                    <tr>
                        <td class="dtl-contents aligncenter">{{ $loop->iteration }}</td>
                        <td class="dtl-contents aligncenter">{{$data->barcode->product_barcode_barcode}}</td>
                        <td class="dtl-contents alignleft">{{$data->product->product_name}}</td>
                        <td class="dtl-contents aligncenter">{{$data->sup_prod_sup_barcode}}</td>
                        <td class="dtl-contents alignleft">{{$data->sup_prod_sup_description}}</td>
                        <td class="dtl-contents aligncenter">{{$data->sup_prod_sup_uom}}</td>
                        <td class="dtl-contents aligncenter">{{$data->sup_prod_sup_pack}}</td>
                        <td class="dtl-contents aligncenter">{{$data->sup_prod_sup_category}}</td>
                        <td class="dtl-contents aligncenter">{{$data->sup_prod_sup_brand}}</td>
                        <td class="dtl-contents alignright">{{number_format($data->sup_prod_sup_pur_rate,3)}}</td>
                        <td class="dtl-contents alignright">{{number_format($data->sup_prod_sup_sale_rate,3)}}</td>
                        <td class="dtl-contents alignright">{{number_format($data->sup_prod_sup_vat_per,3)}}</td>
                        <td class="dtl-contents aligncenter">{{$data->sup_prod_sup_hs_code}}</td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
    <table>
        @if(isset($notes))
            <tr>
                <th class="heading alignleft">Notes:</th>
            </tr>
            <tr>
                <td class="normal alignleft paddingNotes">{{$notes}}</td>
            </tr>
        @endif
    </table>
    @endsection

    @section('customJS')
    @endsection
@endpermission
