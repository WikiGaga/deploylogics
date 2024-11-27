<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Brochure</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
{{--    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Amiri&display=swap" rel="stylesheet">--}}
    <style>

        body {
            font-family: DejaVu Sans, sans-serif,arabic;
            -webkit-print-color-adjust: exact;
        }

        .row {
            width: 100%;
            clear: both;
        }
        .col-lg-1, .col-lg-2, .col-lg-3, .col-lg-4, .col-lg-5, .col-lg-6, .col-lg-7, .col-lg-8, .col-lg-9, .col-lg-10, .col-lg-11, .col-lg-12 {
            float: left;
        }
        .col-lg-1 {  width: 8.33333333%; }
        .col-lg-2 { width: 16.66666667%; }
        .col-lg-3 {  width: 25%;  }
        /*.col-lg-4 { width: 33.33333%; }*/
        .col-lg-4 { width: 33.111111%;border:1px dotted #000; position: relative;}
        .col-lg-5 {  width: 41.66666667%; }
        .col-lg-6 {  width: 50%; }
        .col-lg-7 { width: 58.33333333%; }
        .col-lg-8 { width: 66.66666667%; }
        .col-lg-9 {  width: 75%; }
        .col-lg-10 { width: 83.33333333%; }
        .col-lg-11 { width: 91.66666667%; }
        .col-lg-12 { width: 100%; }
        div{display: block}
        .text-center { text-align: center}
        .title{
            font-size: 12px;
            font-weight: 400;
        }
        .local_title{
            font-size: 12px;
            font-weight: 400;
        }
        .footer {font-size:11px;background: #ffffff; position: fixed; bottom: 0px; left: 0; right: 0; height: 100px;z-index: 9; }
        .footer .footer_table {background:#fff;border: 1px solid #fff !important; }
        @page {
            margin: 5px;
        }
        .price_block{
            position: absolute;
            top:140px;
            left: 150px;
            width: 100px;
            height: 50px;
            border: 1px solid red;
            background: red;
            border-radius: 5px;
            color: #fff;

        }
        .page_break {
            page-break-before: always;
        }
    </style>
</head>
<body>
<div class="footer">
    @php
       $bfi_path =  base_path().'/public/assets/media/custom/brochure_footer_img.jpg';
       $bfi_type = pathinfo($bfi_path, PATHINFO_EXTENSION);
       $bfi_data = file_get_contents($bfi_path);
       $bfi_base64 = 'data:image/'.$bfi_type.';base64,'.base64_encode($bfi_data);
    @endphp
    <img src="{{$bfi_base64}}" width="100%" height="100px" alt="">
</div>
<div class="container">
    @php
        $d = $data['current']->brochures_dtl;
        $cols = 3;
        $dRowLen = ceil(count($d) / $cols);
        $dRowLen =  (int)$dRowLen;
    @endphp

    @for($i=0;$i<$dRowLen;$i++)
        <div class="row">
            @php $index = $i*$cols; @endphp
            @for($v=0;$v<$cols;$v++)
                @if(isset($d[$index+$v]))
                    <div class="col-lg-4" style="padding-bottom:10px;background:  {{$d[$index+$v]['brochure_dtl_bg_color']}}">
                        @php
                            if($d[$index+$v]['barcode']['product_image_url'] == null){
                                $path =  base_path().'/public/products/noimage.png';
                            }else{
                                $path =  base_path().'/public/products/'.$d[$index+$v]['barcode']['product_image_url'];
                            }
                            $type = pathinfo($path, PATHINFO_EXTENSION);
                            $data = file_get_contents($path);
                            $base64 = 'data:image/'.$type.';base64,'.base64_encode($data);
                        @endphp
                        <div class="img-block text-center">
                            <img src="{{$base64}}" width="200px" height="200px" />
                        </div>
                        <div class="local_title text-center"  style="direction: rtl; ">
                            {{$d[$index+$v]['product']['product_arabic_name']}}
                        </div>
                        <div class="title text-center">
                            {{$d[$index+$v]['product']['product_name']}}
                        </div>
                        <div class="price_block">
                            <div style="float: left;width: 50px;text-align:center;position:relative;top:3px;">
                                <div style="border-radius:5px;color:#000;padding-right:2px;padding-left:2px;padding-bottom:3px;font-weight: bold;font-size: 14px; background: yellow">{{$d[$index+$v]['brochure_dtl_disc_percent']}}%</div>
                                <div style="position:relative;top:-5px;font-size: 14px">OFF</div>
                            </div>
                            <div style="text-align:center;float: left;margin-left: 50px;">
                                <div style="position:relative;top:-5px;font-weight: bold;font-size: 20px">{{$d[$index+$v]['brochure_dtl_rate']}}</div>
                                @php
                                    $overRate = (float)$d[$index+$v]['brochure_dtl_rate'] + ((float)$d[$index+$v]['brochure_dtl_rate']*(float)$d[$index+$v]['brochure_dtl_disc_percent'] /100);
                                @endphp
                                <div style="position:relative;top:-5px;text-decoration: line-through;">{{$overRate}}</div>
                            </div>
                        </div>
                    </div>
                @endif
            @endfor
        </div>
        @if((($i+1)/4) == 1)
         <div class="page_break"></div>
        @endif
    @endfor
</div>
</body>
</html>
