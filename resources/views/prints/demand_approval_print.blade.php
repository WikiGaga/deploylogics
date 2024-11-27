@php
    $heading = strtoupper($data['title']);
    $businessName = strtoupper(auth()->user()->business->business_short_name);
    $code= $data['code_date']->demand_approval_dtl_code;
    $date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['code_date']->demand_approval_dtl_date))));
    $notes = $data['code_date']->demand_approval_dtl_entry_notes;
    $dtls = isset($data['current'])? $data['current']:[];
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
                        <td width="17%" class="heading alignleft">Date:</td>
                        <td width="10%" class="normal alignleft">{{isset($date)?$date:''}}</td>
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
                            <th width="3%" class="dtl-head">Sr No</th>
                            <th width="6%" class="dtl-head">Demand No</th>
                            <th width="10%" class="dtl-head">Branch</th>
                            <th width="10%" class="dtl-head alignleft">Product Name</th>
                            <th width="7%" class="dtl-head">Physical Stock</th>
                            <th width="6%" class="dtl-head">Store Stock</th>
                            <th width="6%" class="dtl-head">Stock Match</th>
                            <th width="5%" class="dtl-head">Suggest Qty 1</th>
                            <th width="5%" class="dtl-head">Suggest Qty 2</th>
                            <th width="8%" class="dtl-head">Demand Qty</th>
                            <th width="6%" class="dtl-head">WIP LPO Stock</th>
                            <th width="6%" class="dtl-head">Pur.Ret in Waiting</th>
                            <th width="8%" class="dtl-head">Approve Qty</th>
                            <th width="7%" class="dtl-head">Notes</th>
                            <th width="7%" class="dtl-head">Remarks</th>
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
                                    $totQty += $data->demand_approval_dtl_approve_qty;
                                @endphp
                                <tr>
                                    <td class="dtl-contents aligncenter">{{ $loop->iteration }}</td>
                                    <td class="dtl-contents aligncenter">{{$data->demand->demand_no}}</td>
                                    <td class="dtl-contents aligncenter">{{$data->branch->branch_name}}</td>
                                    <td class="dtl-contents alignleft">{{$data->product->product_name}}</td>
                                    <td class="dtl-contents aligncenter">{{$data->demand_approval_dtl_physical_stock}}</td>
                                    <td class="dtl-contents aligncenter">{{$data->demand_approval_dtl_store_stock}}</td>
                                    <td class="dtl-contents aligncenter">{{$data->demand_approval_dtl_stock_match}}</td>
                                    <td class="dtl-contents aligncenter">{{$data->demand_approval_dtl_suggest_quantity1}}</td>
                                    <td class="dtl-contents aligncenter">{{$data->demand_approval_dtl_suggest_quantity2}}</td>
                                    <td class="dtl-contents aligncenter">{{$data->demand_approval_dtl_demand_qty}}</td>
                                    <td class="dtl-contents aligncenter">{{$data->demand_approval_dtl_wip_lpo_stock}}</td>
                                    <td class="dtl-contents aligncenter">{{$data->demand_approval_dtl_pur_ret_in_waiting}}</td>
                                    <td class="dtl-contents aligncenter">{{$data->demand_approval_dtl_approve_qty}}</td>
                                    <td class="dtl-contents aligncenter">{{$data->demand_approval_dtl_notes}}</td>
                                    <td class="dtl-contents aligncenter">{{$data->demand_approval_dtl_remarks}}</td>
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
                                    <td width="60%" class="heading alignleft">Total Approve Qty</td>
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
