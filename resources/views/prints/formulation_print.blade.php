@php
    $heading = strtoupper($data['title']);
    $businessName = strtoupper(auth()->user()->business->business_short_name);
    $code = $data['current']->item_formulation_code;
    $date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->item_formulation_date))));
    $product_name = isset($data['current']->product->product_name)?$data['current']->product->product_name:"";
    $product_barcode = $data['current']->product_barcode_barcode;
    $qty = $data['current']->item_formulation_qty;
    $notes = $data['current']->item_formulation_remarks;
    $dtls = isset($data['current']->dtls)? $data['current']->dtls :[];
@endphp
@permission($data['permission'])
<!DOCTYPE html>
<html>
<head>
<title>{{$heading}}</title>
<link href="{{ asset('css/print.css') }}" rel="stylesheet" type="text/css" />
</head>
<body>
    <table class="tab">
        <tr>
            <td>
                <table class="tab">
                    <tr>
                        <td width="30%">
                            <table class="tab">
                                <tr>
                                    <td>
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
                                </tr>
                            </table>
                        </td>
                        <td width="25%"></td>
                        <td width="45%">
                            <table class="tab">
                                <tr>
                                    <td class="title aligncenter">{{$heading}}</td>
                                </tr>
                                <tr>
                                    <td class="title aligncenter" style="font-weight:normal; font-size:14px;">{{auth()->user()->branch->branch_name}}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr><td>&nbsp;</td></tr>
        <tr>
            <tr>
                <table class="tabData">
                    <tr>
                        <td width="20%" class="heading alignleft">Code:</td>
                        <td width="20%" class="normal alignleft">{{isset($code)?$code:''}}</td>
                        <td width="13%" class="data alignleft"></td>
                        <td width="15%" class="data alignleft"></td>
                        <td width="17%" class="heading alignleft">Date:</td>
                        <td width="15%" class="normal alignleft">{{isset($date)?$date:''}}</td>
                    </tr>
                    <tr>
                        <td class="heading alignleft">Product:</td>
                        <td class="normal alignleft">{{isset($product_name)?$product_name:''}}</td>
                        <td class="normal alignleft"></td>
                        <td class="data alignleft"></td>
                        <td class="heading alignleft">Bag Qty:</td>
                        <td class="normal alignleft">{{isset($qty)?$qty:''}}</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr><td>&nbsp;</td></tr>
        <tr>
            <td>
                <table  class="tabData" >
                    <thead>
                        <tr>
                            <th width="6%" class="dtl-head">Sr No</th>
                            <th width="14%" class="dtl-head">Barcode</th>
                            <th width="24%" class="dtl-head alignleft">Product Name</th>
                            <th width="17%" class="dtl-head">UOM</th>
                            <th width="17%" class="dtl-head">Packing</th>
                            <th width="17%" class="dtl-head">Qty</th>
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
                                    $i++;
                                    $totGrossAmt += $dtl->item_formulation_dtl_quantity;
                                @endphp
                                <tr>
                                    <td class="dtl-contents aligncenter">{{ $loop->iteration }}</td>
                                    <td class="dtl-contents aligncenter">{{$dtl->barcode->product_barcode_barcode}}</td>
                                    <td class="dtl-contents alignleft">{{$dtl->product->product_name}}</td>
                                    <td class="dtl-contents aligncenter">{{$dtl->uom->uom_name}}</td>
                                    <td class="dtl-contents aligncenter">{{$dtl->item_formulation_dtl_packing}}</td>
                                    <td class="dtl-contents aligncenter">{{number_format($dtl->item_formulation_dtl_quantity,3)}}</td>
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
                                </tr>
                            @endfor
                        @endif
                    </tbody>
                </table>
            </td>
        </tr>
        <tr><td>&nbsp;</td></tr>
        <tr>
            <td>
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
            </td>
        </tr>
        <tr>
            <td>
                <table class="tab">
                    <tr>
                        <td class="alignright"><span style="font-size:10px;">Print Date & Time: {{date("d-m-Y h:i:s")}} User Name: {{auth()->user()->name}}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
@endpermission
