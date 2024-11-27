<!DOCTYPE html>
<html>
<head>
    <title>Barcode with Price</title>
    <style>
        * {font-family:Verdana, Geneva, sans-serif; }
        @font-face {
            font-family: 'NotoSans';
            src: url('../NotoSansArabic/NotoSansArabic-Regular.ttf') format('truetype');
        }
        /*
            Width = 1.5 inch = 144 px
            Height = 1 inch = 96px

        */
        body{
            margin:2px 0 0 0;
            color:#000000;
        }
        @media print {
            .dbl-table {display: block; page-break-after: always !important; page-break-inside: avoid !important;}
            .dbl-table:last-child {page-break-after: avoid !important;}
        }
        .dbl-table {
            /*border: 1px solid red;*/
            width: 138px !important;
            height: 93px !important;
            padding: 3px;
            /* margin-top: 1px; */
        }

        .barcode_barcode_img>img {
            width: 122px;
        }

        .address {
            font-size: 6px;
        }
        .barcode_barcode_no {
            font-size: 7px;
        }

        .product_name {
            font-size: 7px;
        }
        .rate {
            font-size: 12px;
            font-weight: 800;
        }
        .mfg_bb {
            font-size: 7px;
        }
        .text-ellipsis{
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }
        .text-nowrap{
            display: block;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>
</head>
<body>
@php
    $dtls = (isset($data['current']->grn_dtl_smpl_data) && count($data['current']->grn_dtl_smpl_data) != 0)? $data['current']->grn_dtl_smpl_data:[];
@endphp
<center>
@foreach($dtls as $dtl)
        @if(isset($dtl->product_smpl_data->product_name) && isset($dtl->barcode_smpl_data->product_barcode_barcode))
            @php
                $barcode_code = $dtl->barcode_smpl_data->product_barcode_barcode;
                $product_name = $dtl->product_smpl_data->product_name;
                $barcode = new \TheUmar98\BarcodeBundle\Utils\BarcodeGenerator();
                $barcode->setText($barcode_code);
                $barcode->setType('CINcode128');
                $barcode->setScale(2);
                $barcode->setLabel('');
                $barcode->setThickness(20);
                $barcode->setFontSize(14);
                $code = $barcode->generate();
            @endphp
            <div class="dbl-table">
                <div class="barcode_barcode_img">
                    @if(isset($code) && $code != '')
                        <img src="data:image/png;base64,{{$code}}" />
                    @else
                        <div></div>
                    @endif
                </div>
                <div class="barcode_barcode_no">
                    {{$barcode_code}}
                </div>
                <div class="product_name text-ellipsis">
                    {{$product_name}}
                </div>
                <div class="rate">
                    Rs.{{number_format($dtl['tbl_purc_grn_dtl_sale_rate'],3)}}
                </div>
            </div>
        @endif
    @endforeach
</center>
</body>
</html>
