<!DOCTYPE html>
<html>
<head>
    <title>Price Tags</title>
    <link href="{{ asset('css/print.css') }}" rel="stylesheet" type="text/css" />

    <style>
        body{
            margin:0;
        }
        .bpt-table{
            height: 130px;
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
            font-size: 20px;
            height: 20px;
        }
        .bpt-date {
            height: 14px;
            padding: 1px 0;
            margin-top: 15px;
            width: 170px;
        }
        .bpt-date-packing {
            float: left;
            font-size: 6px;
        }
        .bpt-date-expire {
            float: right;
            font-size: 6px;
        }
        @media print {
            .bpt-table {display: block; page-break-inside: avoid !important;}
        }
    </style>
</head>
<body onload="print_document();" onafterprint="redirectBack();">
{{--<body>--}}

<center>
    @foreach($data['barcodes'] as $dtl)
        @for($i=0; $i < $dtl['qty']; $i++)
            <div class="bpt-table">
                <div>
                    <div class="bpt-name">
                        {{auth()->user()->branch->branch_name}}
                    </div>
                </div>
                <div>
                    @php
                    $barcode = new \TheUmar98\BarcodeBundle\Utils\BarcodeGenerator();
                    $barcode->setText($dtl['barcode']);
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
                        {{$dtl['barcode']}}
                    </div>
                </div>
                <div>
                    <div class="bpt-product_name">
                        {{$dtl['name']}}
                    </div>
                </div>
                <div>
                    <div class="bpt-rate">
                        @if($dtl['rate'] != 0)
                        RO:{{number_format($dtl['rate'] ,3)}}
                        @endif
                    </div>
                </div>
                <div class="bpt-date">
                    <div class="bpt-date-packing">
                        @if(isset($dtl['packing_date']) && $dtl['packing_date'] != '')
                            <span>
                                <b>Packing Date:</b>
                            </span>
                            <span>
                                {{ $dtl['packing_date'] }}
                            </span>
                        @endif
                    </div>
                    <div class="bpt-date-expire">
                        @if(isset($dtl['expiry_date']) && $dtl['expiry_date'] != '')
                            <span>
                                <b>Expiry Date:</b>
                            </span>
                            <span>
                                {{ $dtl['expiry_date'] }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        @endfor
    @endforeach
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
