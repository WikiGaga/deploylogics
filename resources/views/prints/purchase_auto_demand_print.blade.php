@php
    $heading = strtoupper($data['title']);
    $businessName = strtoupper(auth()->user()->business->business_short_name);
    $code= $data['current'][0]->ad_code;
    $date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current'][0]->ad_date))));
    $ad_type = isset($data['current'][0]->ad_type)?$data['current'][0]->ad_type:'';
    $supplier_name = isset($data['current']->supplier->supplier_name)?$data['current']->supplier->supplier_name:'';
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
                    </tr>
                    <tr>
                        <td class="heading alignleft">Date :</td>
                        <td class="normal alignleft">{{isset($date)?$date:''}}</td>
                        <td class="data alignleft"></td>
                        <td class="data alignleft"></td>
                    </tr>
                    <tr>
                        <td class="heading alignleft">AD Type :</td>
                        <td class="normal alignleft">{{isset($ad_type)?$ad_type:''}}</td>
                        <td class="data alignleft"></td>
                        <td class="data alignleft"></td>
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
                            <th width="10%" class="dtl-head">Branch</th>
                            <th width="10%" class="dtl-head">Barcode</th>
                            <th width="15%" class="dtl-head alignleft">Product Name</th>
                            <th width="10%" class="dtl-head">Unit</th>
                            <th width="9%" class="dtl-head">Packing</th>
                            <th width="9%" class="dtl-head">Stock Qty</th>
                            <th width="9%" class="dtl-head">Reorder Qty</th>
                            <th width="8%" class="dtl-head">Approve Qty</th>
                            <th width="8%" class="dtl-head">Consumption Qty</th>
                            <th width="10%" class="dtl-head">Rate</th>
                            <th width="10%" class="dtl-head">Amount</th>
                            <th width="10%" class="dtl-head">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $approveQty = 0;
                            $rate = 0;
                            $ammount = 0;
                        @endphp
                        @foreach($dtls as $dtl)
                            @php
                                if($dtl->is_approve == 'approved'){
                                    $approveQty += $dtl->qty;
                                    $rate += $dtl->amount;
                                    $ammount += $dtl->gross_amount;
                                }
                            @endphp
                            <tr>
                                <td class="dtl-contents aligncenter">{{ $loop->iteration }}</td>
                                <td class="dtl-contents aligncenter">{{ $dtl->branch_short_name }}</td>
                                <td class="dtl-contents aligncenter">{{ $dtl->product_barcode_barcode }}</td>
                                <td class="dtl-contents aligncenter">{{ $dtl->product_name }}</td>
                                <td class="dtl-contents aligncenter">{{ $dtl->uom_name }}</td>
                                <td class="dtl-contents aligncenter">{{ $dtl->packing }}</td>
                                <td class="dtl-contents aligncenter">{{ number_format($dtl->stock_qty,3) }}</td>
                                <td class="dtl-contents aligncenter">{{ number_format($dtl->reorder_qty,3) }}</td>
                                <td class="dtl-contents aligncenter">{{ $dtl->qty }}</td>
                                <td class="dtl-contents aligncenter">{{ number_format($dtl->consumption_qty,3) }}</td>
                                <td class="dtl-contents aligncenter">{{ number_format($dtl->amount,3) }}</td>
                                <td class="dtl-contents aligncenter">{{ number_format($dtl->gross_amount,3) }}</td>
                                <td class="dtl-contents aligncenter">{{ ucfirst($dtl->is_approve) }}</td>

                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th class="dtl-head alignleft" colspan="3">
                                Total:
                            </th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th class="dtl-head aligncenter" style="border:1px solid;">{{ number_format($approveQty , 3) }}</th>
                            <th>&nbsp;</th>
                            <th class="dtl-head aligncenter" style="border:1px solid;">{{ number_format($rate , 3) }}</th>
                            <th class="dtl-head aligncenter" style="border:1px solid;">{{ number_format($ammount , 3) }}</th>
                            <th>&nbsp;</th>
                        </tr>
                    </tfoot>
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
