@php
//essential for header
    $heading = strtoupper($data['title']);
    $businessName = strtoupper(auth()->user()->business->business_short_name);
    $pdf_link = $data['print_link'];
    $print_type = $data['type'];
// dd($data['current']);
    $id= $data['current']->purchase_order_id;
    $code= $data['current']->purchase_order_code;
    $date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->purchase_order_entry_date))));
    $delivery_date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->purchase_order_delivery_date))));
    $supplier_name = isset($data['current']->supplier->supplier_name)?$data['current']->supplier->supplier_name:'';
    $phon_no = isset($data['current']->supplier->supplier_phone_1)?$data['current']->supplier->supplier_phone_1:'';
    $fax_no = isset($data['current']->supplier->supplier_fax)?$data['current']->supplier->supplier_fax:'';
    $supplier_gst_no = isset($data['current']->supplier->supplier_gst_no)?$data['current']->supplier->supplier_gst_no:'';
    $supplier_ntn_no = isset($data['current']->supplier->supplier_ntn_no)?$data['current']->supplier->supplier_ntn_no:'';
    $tax_no = isset($data['current']->supplier->supplier_tax_no)?$data['current']->supplier->supplier_tax_no:'';
    $email = isset($data['current']->supplier->supplier_email)?$data['current']->supplier->supplier_email:'';
    $address = isset($data['current']->supplier->supplier_address)?$data['current']->supplier->supplier_address:'';
    $currency = $data['currency']->currency_name;
    $exchange_rate = $data['current']->purchase_order_exchange_rate;
    $bill_no = $data['current']->grn_bill_no;
    $store = isset($data['store']->store_name)?$data['store']->store_name:'';
    $payment_term = isset($data['payment_terms']->payment_term_name)?$data['payment_terms']->payment_term_name:'';
    $days = $data['current']->purchase_order_credit_days;
    $notes = $data['current']->purchase_order_remarks;
    $dtls = isset($data['current']->po_details)? $data['current']->po_details:[];

    $last_purch_date = isset($data['purchase']->grn_date)?$data['purchase']->grn_date:'';
    $last_purch_qty = isset($data['purchase']->tbl_purc_grn_dtl_quantity)?$data['purchase']->tbl_purc_grn_dtl_quantity:'';

    $last_sale_date = isset($data['sale']->sales_date)?$data['sale']->sales_date:'';
    $last_sale_qty = isset($data['sale']->sales_dtl_quantity)?$data['sale']->sales_dtl_quantity:'';
@endphp
@permission($data['permission'])
@extends('layouts.po_print_layout')
@section('title', $heading) 
@section('heading', $heading)

@section('pageCSS')
<style>
    .center{
        text-align: center;
    }
</style>
@endsection
@section('content')
<table class="tableData" style="margin-top: 5px">
    <tbody>
        <tr>
            <td width="33.33%">
                <div>
                    <span class="heading heading-block">Entry Date:</span>
                    <span class="normal normal-block">{{isset($date)?$date:''}}</span>
                </div>
                <div>
                    <span class="heading heading-block">Delivery Date:</span>
                    <span class="normal normal-block">{{isset($delivery_date)?$delivery_date:''}}</span>
                </div>
                <div>
                    <span class="heading heading-block">Code :</span>
                    <span class="normal normal-block">{{isset($code)?$code:''}}</span>
                </div>
                <div>
                    <span class="heading heading-block">Vendor:</span>
                    <span class="normal normal-block">{{isset($supplier_name)?$supplier_name:''}}</span>
                </div>
                <div>
                    <span class="heading heading-block">Vendor Address:</span>
                    <span class="normal normal-block">{{isset($address)?$address:''}}</span>
                </div>
            </td>
            <td width="33.33%"></td>
            <td width="33.33%">
                <div>
                    <span class="heading heading-block">Credit Period:</span>
                    <span class="normal normal-block">{{isset($days)?$days:''}}</span>
                </div>
                <div>
                    <span class="heading heading-block">Invoice To:</span>
                    <span class="normal normal-block">Risen Cash & Carry</span>
                </div>
            </td>
        </tr>
    </tbody>
</table>
@if($print_type != 'pdf')
    {{-- <div class="row" style="margin-top: 10px">
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
                    $headings = ['Sr No','Barcode','Product Name','Last Purc Date','Last Purc Qty','Last Sale Date','Last Sale Qty','Cur. Stock',
                                'Qty','Rate','Amount'];
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
    </div> --}}
@endif
<table  class="tableData data_listing main_print" id="document_table_data" style="margin-top: 10px">
    <thead>
        <tr>
            <th width="3%" class="dtl-head">Sr No</th>
            <th width="4%" class="dtl-head">Barcode</th>
            <th width="15%" class="dtl-head alignleft">Product Name</th>
            <th width="3%" class="dtl-head">Last Purc Date</th>
            <th width="3%" class="dtl-head">Last Purc Qty</th>
            <th width="3%" class="dtl-head">Last Sale Date</th>
            <th width="3%" class="dtl-head">Last Sale Qty</th>
            <th width="3%" class="dtl-head">Cur. Stock</th>
            <th width="3%" class="dtl-head">Qty</th>
            <th width="3%" class="dtl-head">Rate</th>
            <th width="3%" class="dtl-head">Amount</th>
        </tr>
    </thead>
    <tbody>
        @php
            $totQty = 0;
            $totAmt = 0;
            $totGstAmt = 0;
            $totDiscAmt = 0;
            $totSpecDiscAmt = 0;
            $i=0;
        @endphp
        @if(isset($dtls))
            @foreach($dtls as $row)
                @php
                $new_dtls[$row->product->group_item->group_item_name_string][] = $row;
                @endphp
                @php
                // dd($row->product->product_id);
                    $i++;
                    $totQty += $row->purchase_order_dtlquantity;
                    $totAmt += $row->purchase_order_dtlrate;
                    $Amount = $row->purchase_order_dtlquantity * $row->purchase_order_dtlsale_rate;
                    $totDiscAmt += $row->purchase_order_dtldisc_amount;
                    $totGstAmt += $row->purchase_order_dtlgst_amount;
                    $totSpecDiscAmt += $row->purchase_order_dtlspec_disc_amount;

                    $totGstInclusive = $Amount + $totGstAmt;
                    $totalDisc = $totDiscAmt + $totSpecDiscAmt;
                    $total_net = $totGstInclusive - $totalDisc;

                    $business_id = "business_id = ".auth()->user()->business_id;
                    $company_id = "company_id = ".auth()->user()->company_id;
                    $branch_id = "branch_id = ".auth()->user()->branch_id;

                    $purc_query = "select grn_date,tbl_purc_grn_dtl_quantity from vw_purc_grn
                    where product_id=".$row->product->product_id." and $business_id and $company_id and $branch_id and grn_type='GRN' order by grn_date desc,grn_code desc";
                    $purc_dtl = \Illuminate\Support\Facades\DB::selectOne($purc_query);
                    // dd($purc_dtl);
                                        
                    $sale_query = "select sales_date,sales_dtl_quantity from vw_sale_sales_invoice where product_id = ".$row->product->product_id." and $business_id and $company_id and $branch_id and sales_type='POS' order by sales_date desc,sales_code desc";
                    $sale_dtl = \Illuminate\Support\Facades\DB::selectOne($sale_query);
                    // dd($sale_dtl);
                @endphp
            @endforeach
            @foreach($new_dtls as $key => $dtl)
                <tr>
                    <td class="dtl-contents alignleft" colspan="100%"><b>{{ $key }}</b></td>
                </tr>
                @foreach ($dtl as $item)
                <tr>
                    <td class="dtl-contents aligncenter">{{ $loop->iteration }}</td>
                    <td class="dtl-contents aligncenter">{{$item->barcode->product_barcode_barcode}}</td>
                    <td class="dtl-contents alignleft">{{$item->product->product_name}}</td>
                    <td class="dtl-contents alignright">{{isset($purc_dtl->grn_date)? date('d-m-Y', strtotime(trim(str_replace('/','-',$purc_dtl->grn_date)))):''}}</td>
                    <td class="dtl-contents alignright">{{isset($purc_dtl->tbl_purc_grn_dtl_quantity)?$purc_dtl->tbl_purc_grn_dtl_quantity:''}}</td>
                    <td class="dtl-contents alignright">{{isset($sale_dtl->sales_date)?date('d-m-Y', strtotime(trim(str_replace('/','-',$sale_dtl->sales_date)))):''}}</td>
                    <td class="dtl-contents alignright">{{isset($sale_dtl->sales_dtl_quantity)?$sale_dtl->sales_dtl_quantity:''}}</td>
                    <td class="dtl-contents aligncenter">{{$item->purchase_order_dtlsys_quantity}}</td>
                    <td class="dtl-contents aligncenter">{{$item->purchase_order_dtlquantity}}</td>
                    <td class="dtl-contents alignright">{{number_format($item->purchase_order_dtlsale_rate,3)}}</td>
                    <td class="dtl-contents alignright">{{number_format($Amount,3)}}</td>
                </tr>
                @endforeach
            @endforeach
        @endif
        <tr>
            <td colspan="8" class="dtl-contents center"></td>
            <td class="dtl-bottom center">{{$totQty}}</td>
            <td colspan="2" class="dtl-bottom left">{{number_format($totAmt,3)}}</td>
        </tr>
        <tr>
            <td colspan="9" class="dtl-bottom"></td>
            <td class="dtl-bottom alignleft">GST</td>
            <td class="dtl-bottom alignright">{{number_format($totGstAmt,3)}}</td>
        </tr>
        <tr>
            <td colspan="8" class="dtl-bottom center"></td>
            <td colspan="2" class="dtl-bottom alignleft">GST Inclusive <br> Discount + Sp. Disc </td>
            <td class="dtl-bottom alignright">{{number_format($totGstInclusive,3)}} <br> {{number_format($totalDisc,3)}}</td>
        </tr>
        <tr>
            <td colspan="8" class="dtl-bottom alignleft">In Words: {{\App\Library\Utilities::AmountWords($total_net,$currency)}}</td>
            <td colspan="2" class="dtl-bottom alignleft">Net Amount</td>
            <td class="dtl-bottom alignright" style="font-size: 13px;font-weight: bold;">{{number_format($total_net,3)}}</td>
        </tr>
    </tbody>
</table>
<table>
    <tr>
        <td width="45%" valign="top">
            <table class="tab">
                <tr>
                    <td class="heading alignleft"></td>
                </tr>
                <tr>
                    <td class="heading alignleft">
                        <u>Terms And Condition :</u>
                    </td>
                </tr>
                {{-- <tr>
                    <td class="heading alignleft">Goods Return Should Be Taken By The Vendor On Delivery.</td>
                </tr> --}}
                <tr>
                    <td class="heading alignleft"></td>
                </tr>
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
    </tr>
</table>
<table class="tab mrgn-top">
    <tr>
        <th width="25%" class="heading aligncenter">{{auth()->user()->name}}<hr class="sign-line"> Prepared By: </th>
        <th width="25%" class="heading aligncenter"><hr class="sign-line"> Checked By: </th>
        <th width="25%" class="heading aligncenter"><hr class="sign-line"> Approved By: </th>
        <th width="25%" class="heading aligncenter"><hr class="sign-line"> Received By: </th>
    </tr>
</table>
@endsection

@section('customJS')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="{{ asset('/js/jquery.table2excel.min.js') }}"></script>
<script>
    $(document).ready(function(){
        // $("#document_table_po").hide();
        $(".po").hide();
        $(".poss").hide();
        $(".pom").hide();
        $(".expo").prop("checked", true);
    });
</script>

@endsection
@endpermission