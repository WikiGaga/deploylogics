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
        body{
            margin:0;
        }
        @media print {
            .bl-table {display: block; page-break-inside: avoid !important;}
            @print{
                @page{
                    size : 7cm 4cm;
                }
            }
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
            font-size: 16px;
            font-weight: 900;
            display: flex;
            justify-content: space-between;
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
        .bl{
            height: 0.9cm;
        }
    </style>
</head>
{{-- onafterprint="redirectBack();" --}}
<body onload="print_document();" onafterprint="redirectBack();">
{{--<body>--}}
@php
        //dd($data);
    $dtls = isset($data['current']->dtl)? $data['current']->dtl:[];
    //dd($dtls->toArray());
@endphp
<center>
@foreach($dtls as $dtl)
    @for($i=0; $i < $dtl['barcode_labels_dtl_qty']; $i++)
        <div class="bl-table">
            <div class="bl-rate bl" style="width: 230px;position:relative;margin-left:-15px;">
                <span style="width: 10%;">
                    @php $logo = auth()->user()->branch->branch_logo;  @endphp
                    <img src="{{ asset('images') }}/{{ $logo }}" alt="" height="100%" style="width: 40px;height: 30px;margin-top: 0px;">
                </span>
                <span style="font-size: 9px;padding-top:1%;width:40%;">
                    @if($dtl->barcode_labels_dtl_vat_per > 0)
                        {{ number_format($dtl->barcode_labels_dtl_rate,3) }} + <br/> VAT {{ $dtl->barcode_labels_dtl_vat_per }}% ضريبة
                    @else
                    صفر ضريبة    <br/>
                        VAT 0%
                    @endif
                </span>
                @if($dtl->barcode_labels_dtl_rate != 0)
                   <span style="width: 50%;">
                        @if(isset($dtl->barcode_labels_dtl_grs_amt) && !empty($dtl->barcode_labels_dtl_grs_amt)) 
                            رع {{number_format($dtl->barcode_labels_dtl_grs_amt,3)}} 
                        @else
                            @if($dtl->barcode_labels_dtl_rate != 0)
                                رع {{number_format($dtl->barcode_labels_dtl_rate,3)}} 
                            @endif
                        @endif
                   </span>
                @endif
            </div>
            <div class="bl-names bl" style="margin-top: -3px;margin-bottom:3px;">
                @php if(strlen($dtl->product_arabic_name) < 30) { $fontSize = '16px'; $marginSize = '3px'; }else{ $fontSize = '13px'; $marginSize = '0px'; } @endphp
                <div class="bl-local-name bl" style="font-size: {{ $fontSize }};margin-top:{{ $marginSize }};">{{$dtl->product_arabic_name}}</div>
            </div>
            <div class="bl-names bl">
                <div class="bl-national-name" style="font-size: 11px;">{{ucwords(strtolower(strtoupper($dtl->product_name)))}}</div>
            </div>
            <div class="blb-number bl" style="font-size: 14px;margin-top:3px;">{{$dtl->product_barcode_barcode}}</div>
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
@endpermission
