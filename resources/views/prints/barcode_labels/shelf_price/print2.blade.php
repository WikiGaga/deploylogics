@permission($data['permission'])
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
<body onload="print_document();" onafterprint="redirectBack();">
{{--<body>--}}
@php
    $dtls = isset($data['current']->dtl)? $data['current']->dtl:[];
    //dd($dtls->toArray());
@endphp
@foreach($dtls as $dtl)
    @for($i=0; $i < $dtl['barcode_labels_dtl_qty']; $i++)
        <div class="bl-table">
            <div class="bl-names">
                <div class="bl-local-name">{{$dtl->product_arabic_name}}</div>
            </div>
            <div class="bl-dtls">
                <div class="bl-bar-rate">
                    <div class="bl-national-name">{{$dtl->product_name}}</div>
                    <div class="bl-barcode">
                        @php
                            $barcode = new \TheUmar98\BarcodeBundle\Utils\BarcodeGenerator();
                            $barcode->setText($dtl->product_barcode_barcode);
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
                        <div class="blb-number">{{$dtl->product_barcode_barcode}}</div>
                    </div>
                    <div class="bl-rate">
                        @if(isset($dtl->barcode_labels_dtl_grs_amt) && !empty($dtl->barcode_labels_dtl_grs_amt))
                            {{number_format($dtl->barcode_labels_dtl_grs_amt,3)}}
                        @else
                            @if($dtl->barcode_labels_dtl_rate != 0)
                                {{number_format($dtl->barcode_labels_dtl_rate,3)}}
                            @endif
                        @endif
                    </div>
                </div>

                <div class="bl-img">
                    <img src="/products/{{$dtl->barcode->product_image_url}}" alt="">
                </div>
            </div>
        </div>
    @endfor
@endforeach
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
