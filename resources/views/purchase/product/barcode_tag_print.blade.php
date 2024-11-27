@permission($data['permission_create'],$data['permission_edit'])
<!DOCTYPE html>
<html>
<head>
<title></title>
<link href="{{ asset('css/print.css') }}" rel="stylesheet" type="text/css" />
<style>
    .bpt-table{
        height: 96px;
        width: 190px;
        padding: 0 3px;
    }
    .bpt-name {
        font-weight: 600;
        font-size: 12px;
        height: 14px;
    }
    .pbt-img{
        max-width: 170px !important;
        height: 30px !important;
        margin-top: 2px;
        object-fit: contain;
    }
    .bpt-barcode {
        font-weight: 600;
        font-size: 10px;
        height: 10px;
    }
    .bpt-product_name {
        font-size: 7px;
        font-weight: 600;
        text-align: left !important;
        width: 170px;
        overflow: hidden;
        white-space: nowrap;
        height: 8px;
        margin-top: 3px;
    }
    .bpt-rate {
        font-weight: 600;
        font-size: 14px;
        height: 14px;
     }
    .bpt-date{
        height: 6px;
        padding: 1px 0;
        margin-top: 2px;
        width: 170px;
    }
    .bpt-date-packing {
        float: left;
        font-size: 5px;
     }
    .bpt-date-expire {
        float: right;
        font-size: 5px;
     }
    @media print {
        .bpt-table {display: block; page-break-inside: avoid !important;}
    }
</style>
</head>
{{--<body onload="print_document();" onafterprint="redirectBack();">--}}
<body>
    @php
        $label_data = isset($data['barcode'])? $data['barcode']:[];
        if(count($label_data) != 0){
            $product_barcode_barcode = $label_data['barcode'];
            $product_name = $label_data['product_name'];
            $rate = $label_data['rate'];
            $qty = abs($label_data['qty']);
        }
    @endphp
    <center>
        @if(isset($qty))
            @for($i=0;$i < $qty;$i++ )
                <div class="bpt-table">
                    <div>
                        <div class="bpt-name">
                            {{auth()->user()->branch->branch_name}}
                        </div>
                    </div>
                    <div>
                        @php
                            $barcode = new \TheUmar98\BarcodeBundle\Utils\BarcodeGenerator();
                            $barcode->setText($product_barcode_barcode);
                            $barcode->setType('CINcode128');
                            $barcode->setScale(2);
                            $barcode->setLabel('');
                            $barcode->setThickness(20);
                            $barcode->setFontSize(14);
                            $code = $barcode->generate();
                        @endphp
                        @if(isset($code) && $code != '')
                            <img class="pbt-img" src="data:image/png;base64,{{$code}}" />
                        @else
                            <div class="pbt-img"></div>
                        @endif
                    </div>
                    <div>
                        <div class="bpt-barcode">
                            {{$product_barcode_barcode}}
                        </div>
                    </div>
                    <div>
                        <div class="bpt-product_name">
                            {{$product_name}}
                        </div>
                    </div>
                    <div>
                        <div class="bpt-rate">
                            @if($rate != 0)
                                RO:{{number_format($rate,3)}}
                            @endif
                        </div>
                    </div>
                </div>
            @endfor
        @endif
    </center>
    <script>
        function print_document(){
            window.print();
        }
        function redirectBack(){
            window.close();
            history.back();
        }
    </script>
</body>
</html>
@endpermission
