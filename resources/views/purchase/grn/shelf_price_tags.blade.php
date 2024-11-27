<!DOCTYPE html>
<html>
<head>
    <title></title>
    <link href="{{ asset('css/print.css') }}" rel="stylesheet" type="text/css" />
    <style>
        /*  Heigh = 3.5 cm = 132.28px
            Width = 5 cm = 188.97px
        */
        body{
            margin:0;
        }
        @media print {
            .bl-table {display: block; page-break-inside: avoid !important;}
        }
        .bl-table{
            /*border: 2px solid red;*/
            width: 190px !important;
            height: 130px !important;
            padding: 0 3px;
        }
        .bpt-name {
            text-align: center;
            font-size: 20px;
            font-weight: 600;
        }
        .bl-rate {
            font-weight: 600;
            font-size: 32px;
            height: 40px;
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
            font-size: 12px;
            font-weight: 600;
        }
        .bl-national-name {
            font-size: 12px;
            font-weight: 600;
        }
    </style>
</head>
<body onload="print_document();" onafterprint="redirectBack();">
{{--<body>--}}
<center>
@foreach($data['barcodes'] as $dtl)
    @for($i=0; $i < $dtl['qty']; $i++)
        <div class="bl-table">
            <div class="bpt-name">
                {{auth()->user()->branch->branch_name_arabic}}
            </div>
            <div class="bl-rate">
                @if($dtl['rate'] != 0)
                    <span style="float: left;margin-left: 20px;"> رع</span>
                    <span>
                        @if(isset($dtl['barcode_labels_dtl_grs_amt']) && !empty($dtl['barcode_labels_dtl_grs_amt']))
                            {{number_format($dtl['barcode_labels_dtl_grs_amt'],3)}}
                        @else
                            {{-- This Rate Includes the VAT & Discount --}}
                            @if($dtl['rate'] != 0)
                                {{number_format($dtl['rate'],3)}}
                            @endif
                        @endif
                    </span>
                @endif
            </div>
            <div class="bl-barcode">
                <div class="blb-number">{{$dtl['barcode']}}</div>
            </div>
            <div class="bl-names">
                <div class="bl-local-name">{{$dtl['arabic_name']}}</div>
                <div class="bl-national-name">{{ucwords(strtolower(strtoupper($dtl['name'])))}}</div>
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
