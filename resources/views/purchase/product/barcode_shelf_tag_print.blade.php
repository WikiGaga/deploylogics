@permission($data['permission_create'],$data['permission_edit'])
<!DOCTYPE html>
<html>
<head>
    <title></title>
    <link href="{{ asset('css/print.css') }}" rel="stylesheet" type="text/css" />
    <style>
        /*  Heigh = 3.5 cm = 132.28px
            Width = 5 cm = 188.97px
        */
        @media print {
            .bl-table {display: block; page-break-inside: avoid !important;}
        }
        .bl-table{
            border: 2px solid #3786d8;
            width: 237px !important;
            height: 124px !important;
            float: left;
            margin: 15px;
            padding: 2px;
        }
        .bl-names {
            height: 44px;
            background: #fbfbfb;
            line-height: 36px;
            text-align: right;
        }
        .bl-local-name {
            font-size: 18px;
            text-align: right;
            padding: 0 4px;
            display: inline-block;
            vertical-align: middle;
            line-height: normal;
        }
        .bl-national-name {
            font-size: 10px;
            text-align: left;
            height: 25px;
        }
        .bl-dtls {
            height: 80px;
        }
        .bl-bar-rate {
            width: 150px;
            padding: 0 2px;
            float: left;
            height: 100%;
        }
        .bl-table:nth-child(odd)>.bl-dtls>.bl-bar-rate{
            background: #f0f8ff;
        }
        .bl-barcode>img {
            width: 100%;
            height: 22px;
            object-fit: contain;
        }
        .bl-img {
            width: 79px;
            padding: 0 2px;
            float: left;
            text-align: center;
            background: #fff
        }
        .bl-img>img{
            width: 73%;
        }
        .blb-number {
            font-size: 9px;
        }
        .bl-rate {
            font-size: 18px;
            text-align: center;
            margin-top: -3px;
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
        $arabic_product_name = $label_data['arabic_product_name'];
        $rate = $label_data['rate'];
        $qty = abs($label_data['qty']);
    }else{
        abort('404');
    }
@endphp
@if(isset($qty))
    @for($i=0; $i < $qty; $i++)
        <div class="bl-table">
            <div class="bl-names">
                <div class="bl-local-name">{{$arabic_product_name}}</div>
            </div>
            <div class="bl-dtls">
                <div class="bl-bar-rate">
                    <div class="bl-national-name">{{$product_name}}</div>
                    <div class="bl-barcode">
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
                            <img src="data:image/png;base64,{{$code}}" />
                        @else
                            <div></div>
                        @endif
                        <div class="blb-number">{{$product_barcode_barcode}}</div>
                    </div>
                    <div class="bl-rate">
                        @if($rate != 0)
                            {{number_format($rate,3)}}
                        @endif
                    </div>
                </div>
                @php
                    $img = \App\Models\TblPurcProductBarcode::where('product_barcode_barcode',$product_barcode_barcode)->first();
                @endphp
                <div class="bl-img">
                    @if($img != null)
                        <img src="/products/{{$img->product_image_url}}" alt="img">
                    @else
                        <img src="/products/noimage.jpg" alt="noimg">
                    @endif

                </div>
            </div>
        </div>
    @endfor
@endif
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
