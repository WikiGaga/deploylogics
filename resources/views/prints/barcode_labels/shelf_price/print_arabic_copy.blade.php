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
            /*border: 2px solid #3786d8;*/
            width: 144px !important;
            height: 96px !important;
            float: left;
            margin: 15px;
            padding: 2px;
        }
        .bpt-name {
            text-align: center;
            font-size: 10px;
            font-weight: 600;
        }
        .bl-rate {
             text-align: center;
             font-size: 14px;
             font-weight: 600;
         }
        .bl-barcode>img {
            width: 100%;
            height: 22px;
            object-fit: contain;
        }
        .blb-number {
            font-size: 10px;
            text-align: center;
            font-weight: 600;
        }
        .bl-local-name {
            text-align: right;
            font-size: 10px;
            font-weight: 600;
        }
        .bl-national-name {
            text-align: left;
            font-size: 10px;
            font-weight: 600;
        }
    </style>
</head>
{{--<body onload="print_document();" onafterprint="redirectBack();">--}}
<body>
@php
    $dtls = isset($data['current']->dtl)? $data['current']->dtl:[];
    //dd($dtls->toArray());
@endphp
@foreach($dtls as $dtl)
    @for($i=0; $i < $dtl['barcode_labels_dtl_qty']; $i++)
        <div class="bl-table">
            <div class="bpt-name">
                {{auth()->user()->branch->branch_name}}
            </div>
            <div class="bl-rate">
                @if($dtl->barcode_labels_dtl_rate != 0)
                    <span style="float: left;margin-left: 20px;"> رع</span>
                   <span>{{number_format($dtl->barcode_labels_dtl_rate,3)}}</span>
                @endif
            </div>
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
            <div class="bl-names">
                <div class="bl-local-name">{{$dtl->product_arabic_name}}</div>
                <div class="bl-national-name">{{ucwords(strtolower(strtoupper($dtl->product_name)))}}</div>
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
