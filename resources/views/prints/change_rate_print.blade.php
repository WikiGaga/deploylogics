@php
    $heading = strtoupper($data['title']);
    $businessName = strtoupper(auth()->user()->business->business_short_name);
    if(isset($data['current'])){
       $date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->change_rate_date))));
        $code= $data['current']->change_rate_code;
        $name = $data['current']->change_rate_name;
        $category = $data['current']->change_rate_category;
        $notes = $data['current']->change_rate_notes;
        $dtls = isset($data['current']->change_rate_dtl)? $data['current']->change_rate_dtl:[];
    }else{
        abort('404');
    }
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
            <td>
                <table class="tabData">
                    <tr>
                        <td width="20%" class="heading alignleft">Code :</td>
                        <td width="20%" class="normal alignleft">{{isset($code)?$code:''}}</td>
                        <td width="13%" class="data alignleft"></td>
                        <td width="20%" class="data alignleft"></td>
                        <td width="17%" class="heading alignleft">Name:</td>
                        <td width="10%" class="normal alignleft">{{isset($name)?$name:''}}</td>
                    </tr>
                    <tr>
                        <td class="heading alignleft">Date :</td>
                        <td class="normal alignleft">{{isset($date)?$date:''}}</td>
                        <td class="data alignleft" colspan='2'></td>
                        <td class="heading alignleft">Category:</td>
                        <td class="normal alignleft">{{isset($category)?$category:''}}</td>
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
                            <th width="5%" class="dtl-head">Sr No</th>
                            <th width="17%" class="dtl-head alignleft">Barcode</th>
                            <th width="22%" class="dtl-head alignleft">Product Name</th>
                            <th width="10%" class="dtl-head aligncenter">UOM</th>
                            <th width="10%" class="dtl-head aligncenter">Packing</th>
                            <th width="12%" class="dtl-head">New TP</th>
                            <th width="12%" class="dtl-head">New Sale Rate</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($dtls))
                            @foreach($dtls as $data)
                                <tr>
                                    <td class="dtl-contents aligncenter">{{ $loop->iteration }}</td>
                                    <td class="dtl-contents alignleft">{{$data->product_barcode_barcode}}</td>
                                    <td class="dtl-contents alignleft">{{isset($data->product->product_name)?$data->product->product_name:''}}</td>
                                    <td class="dtl-contents aligncenter">{{isset($data->uom->uom_name)?$data->uom->uom_name:''}}</td>
                                    <td class="dtl-contents aligncenter">{{isset($data->barcode->product_barcode_packing)?$data->barcode->product_barcode_packing:''}}</td>
                                    <td class="dtl-contents alignright">{{number_format($data->sale_rate,3)}}</td>
                                    <td class="dtl-contents alignright">{{number_format($data->current_tp,3)}}</td>
                                </tr>
                            @endforeach
                        @endif
                        @for ($z = 0; $z <= 8; $z++)
                            <tr>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                        @endfor
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td>
                <table class="tab">
                    <tr>
                        <td width="100%" valign="top">
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
