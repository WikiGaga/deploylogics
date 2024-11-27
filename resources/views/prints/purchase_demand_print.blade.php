@php
    $heading = strtoupper($data['title']);
    $businessName = strtoupper(auth()->user()->business->business_short_name);
    $code= $data['current']->demand_no;
    $date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->demand_date))));
    $supplier_name = isset($data['current']->supplier->supplier_name)?$data['current']->supplier->supplier_name:'';
    $saleman = isset($data['users'])?$data['users']->name:"";
    $notes = $data['current']->demand_notes;
    $dtls = isset($data['current']->dtls)? $data['current']->dtls:[];
    $type = $data['type'];
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
                        <td width="20%" class="heading alignleft">Code :</td>
                        <td width="20%" class="normal alignleft">{{isset($code)?$code:''}}</td>
                        <td width="13%" class="data alignleft"></td>
                        <td width="20%" class="data alignleft"></td>
                        @if($type == 'purchase_demand')
                            <td width="17%" class="heading alignleft">Supplier:</td>
                            <td width="10%" class="normal alignleft">{{isset($supplier_name)?$supplier_name:''}}</td>
                        @endif
                        @if($type == 'stock_request')
                            <td class="heading alignleft">Branch from:</td>
                            <td class="normal alignleft">{{isset($data['branch_from'])?$data['branch_from']->branch_name:''}}</td>
                        @endif
                    </tr>
                    <tr>
                        <td class="heading alignleft">Date :</td>
                        <td class="normal alignleft">{{isset($date)?$date:''}}</td>
                        <td class="data alignleft"></td>
                        <td class="data alignleft"></td>
                        @if($type == 'purchase_demand')
                            <td class="heading alignleft">Demand By:</td>
                            <td class="normal alignleft">{{isset($saleman)?$saleman:''}}</td>
                        @endif
                        @if($type == 'stock_request')
                            <td class="heading alignleft">Branch To:</td>
                            <td class="normal alignleft">{{isset($data['branch_to'])?$data['branch_to']->branch_name:''}}</td>
                        @endif
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
                            <th width="10%" class="dtl-head">Barcode</th>
                            <th width="15%" class="dtl-head alignleft">Product Name</th>
                            <th width="10%" class="dtl-head">Physical Stock</th>
                            <th width="9%" class="dtl-head">Store Stock</th>
                            <th width="9%" class="dtl-head">Stock Match</th>
                            <th width="8%" class="dtl-head">Suggest Reorder</th>
                            <th width="8%" class="dtl-head">Suggest Consumption</th>
                            <th width="10%" class="dtl-head">Demand Qty</th>
                            @if($type == 'purchase_demand')
                            <th width="8%" class="dtl-head">WIP LPO Stock</th>
                            <th width="8%" class="dtl-head">Pur.Ret in Waiting</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                    @php
                        $totQty = 0;
                        $i=0;
                    @endphp
                        @if(isset($dtls))
                            @foreach($dtls as $data)
                                @php
                                    $i++;
                                    $totQty += $data->demand_dtl_demand_quantity;
                                @endphp
                                <tr>
                                    <td class="dtl-contents aligncenter">{{ $loop->iteration }}</td>
                                    <td class="dtl-contents aligncenter">{{$data->barcode->product_barcode_barcode}}</td>
                                    <td class="dtl-contents alignleft">{{$data->product->product_name}}</td>
                                    <td class="dtl-contents aligncenter">{{$data->demand_dtl_physical_stock}}</td>
                                    <td class="dtl-contents aligncenter">{{$data->demand_dtl_store_stock}}</td>
                                    <td class="dtl-contents aligncenter">{{$data->demand_dtl_stock_match}}</td>
                                    <td class="dtl-contents aligncenter">{{$data->demand_dtl_suggest_quantity1}}</td>
                                    <td class="dtl-contents aligncenter">{{$data->demand_dtl_suggest_quantity2}}</td>
                                    <td class="dtl-contents aligncenter">{{$data->demand_dtl_demand_quantity}}</td>
                                    @if($type == 'purchase_demand')
                                    <td class="dtl-contents aligncenter">{{$data->demand_dtl_wip_lpo_stock}}</td>
                                    <td class="dtl-contents aligncenter">{{$data->demand_dtl_pur_ret_in_waiting}}</td>
                                    @endif
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
                                    @if($type == 'purchase_demand')
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    @endif
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
                                    <td width="60%" class="heading alignleft">Total Demand Qty</td>
                                    <td width="40%" class="heading alignright">{{$totQty}}</td>
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
