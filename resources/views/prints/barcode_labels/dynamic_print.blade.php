<!DOCTYPE html>
<html>
<head>
    <title></title>
    <style>
        *{font-family:Verdana, Geneva, sans-serif; }
        @font-face {
            font-family: 'NotoSans';
            src: url('../NotoSansArabic/NotoSansArabic-Regular.ttf') format('truetype');
        }

        .nativ-arabic{
            font-family:'NotoSans', Verdana, Geneva, Arial, sans-serif !important;
            font-size: 10px;
            font-weight: normal;
            line-height: 1.5;
        }
        .tab{border-spacing:0; border-collapse:collapse; width:100%; text-align:center;}
        .tab ,td{padding:0; spacing:0;}
        .tabData{border-spacing:0; border-collapse:collapse; padding:5px; width:100%; text-align:center; border:1px solid #000;}
        .tableData{border-spacing:0; border-collapse:collapse; padding:5px; width:100%; border:1px solid #000;}
        .tableData>tbody>tr:first-child>td{vertical-align: top;}
        .company{ color:#000;  font-size:15px; font-weight:bold;}
        .title{font-size:16px; font-weight:bold; padding:1px 0;}
        .heading{font-size:12px; font-weight:bold; padding:2px;}
        .dtl-head{font-weight:bold;font-size:12px;vertical-align:middle;border-right:1px solid #000;border-bottom:1px solid #000; height:22px; padding:0 3px;background-color:#f9f9f9; color:#000;}
        .dtl-contents{border-right:1px solid #000;border-bottom:1px dotted #000;font-weight:normal;font-size:11px; padding:3px 3px;}
        .dtl-bottom{font-size:12px; border-right:1px solid #000;  border-top:1px solid #000; font-weight:bold; padding:3px 3px;}
        .normal{font-weight:normal; font-size:11px; padding:2px;}
        .normal-bold{font-weight:bold; font-size:11px; padding:2px;}
        .alignleft{text-align:left;}
        .alignright{text-align:right;}
        .aligncenter{text-align:center;}
        .paddingNotes{padding-left:45px;}
        .heading-block{width: 40%; display: inline-block; vertical-align: top;}
        .normal-block{width: 50%; display: inline-block;}
        .mrgn-top{margin-top:100px;}
        .sign-line{height:1.4px;border-width:0;color:black;background-color:black;width:70%;text-align:center;}
        .fixed-layout{table-layout:fixed;}
        #Top-Header{
            padding: 5px 16px;
            background: #FFF4DE;
            color: #000000;
            margin-bottom: 10px;
            font-size: 8px;
        }
        .sticky {
            position: fixed;
            top: 0;
            width:1301px;
            box-shadow: 0 5px 5px 0 rgba(0, 0, 0, 0.3);
        }
        button#btn_toggle {
            cursor: pointer;
            background: #d8d8d8;
            position: relative;
            border: 1px solid #eae2e2;
            padding: 0;
            top: 7px;
        }
        button#btn_toggle:focus{
            outline: none;
        }
        button#btn_po {
            cursor: pointer;
            background: #d8d8d8;
            position: relative;
            border: 1px solid #eae2e2;
            padding: 0;
            top: 7px;
        }
        button#btn_po:focus{
            outline: none;
        }
        .toggle_table_column {
            float: right;
            margin-bottom: 15px;
        }
        .table_column_dropdown{
            position: relative;
            box-shadow: 0px 0px 50px 0px rgb(82 63 105);
        }
        .table_column_dropdown_po{
            position: relative;
            box-shadow: 0px 0px 50px 0px rgb(82 63 105);
        }
        .table_column_dropdown_mrp{
            position: relative;
            box-shadow: 0px 0px 50px 0px rgb(82 63 105);
        }
        ul.table_column_dropdown-menu>li {
            list-style: none;
        }

        ul.table_column_dropdown-menu {
            width: 200px;
            height: 200px;
            overflow: auto;
            position: absolute;
            background: #fff;
            padding: 0;
            border: 1px solid lightgrey;
            right: 0;
            top: -13px;
        }

        ul.table_column_dropdown-menu>li>label {
            display: block;
            padding: 2px 10px;
            clear: both;
            font-weight: normal;
            line-height: 1.42857143;
            color: #333;
            white-space: nowrap;
            margin: 0;
            transition: background-color .4s ease;
            font-size: 12px;
        }
        /*****for Thermal print***********/
        .thermal{width:288px;text-align: center;}
        .thermal-title{font-size:11px; font-weight:bold; padding:1px 0;}
        .thermal-dtl-head{font-weight:bold;font-size:11px;vertical-align:middle;border-top:1px solid #000;border-bottom:1px solid #000; height:22px; padding:0 3px;color:#000;}
        .thermal-dtl-contents{font-weight:normal;font-size:10px; padding:3px 3px;}
        .thermal-heading{font-size:11px; font-weight:bold; padding:2px;}
        .thermal-normal{font-weight:normal; font-size:10px; padding:2px;}
        .thermal-print-date{font-weight:normal; font-size:9px; padding:1px;}

        @media print{
            .toggle_table_column,
            #Top-Header,#print_btn{display:none;}
        }

        .phpdebugbar {
            display: none;
        }
    </style>
    <style>
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
        /*.dbl-table{
            !*border: 1px solid red;*!
            width: 142px !important;
            height: 105px !important;
            padding: 3px;
            margin-top:1px;
        }*/
        .dbl-table {
            /* border: 1px solid red; */
            width: 138px !important;
            height: 93px !important;
            padding: 2px;
            /* margin-top: 1px; */
        }

        .barcode_barcode_img>img {
            width: 85px;
        }
        
        .barcode_barcode_align{
            margin-top: 30px;
            margin-right: 160px;
        }

        .address {
            font-size: 6px;
        }
        .barcode_barcode_no {
            font-size: 7px;
            font-weight: bold;
        }

        .product_name {
            font-size: 6px;
            font-weight: bold;
        }
        .rate {
            font-size: 12px;
            font-weight: 800;
        }
        .mfg_bb {
            font-size: 6px;
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

        /* Shelf Barcode Css  */
        .shelf_barcode_img>img {
            width: 180px;
            height: 35px;
        }
        .shelf_barcode_no {
            font-size: 12px;
            font-weight: bold;
        }
        .shelf_product_name {
            font-size: 12px;
            font-weight: bold;
            width: fit-content;
            white-space: nowrap;
        }
        .shelf_rate {
            padding-right: 10px;
            font-size: 25px;
            font-weight: 900;
        }
    </style>
</head>
<body>
@php
    $dtls = isset($data['current']->dtl)? $data['current']->dtl:[];
   // dd($data['current']->toArray());
    //dd($dtls->toArray());
@endphp
<center>
    @foreach($dtls as $dtl)
        @for($i=0; $i < $dtl['barcode_labels_dtl_qty']; $i++)
            @php
                $barcode = new \TheUmar98\BarcodeBundle\Utils\BarcodeGenerator();
                $barcode->setText($dtl->product_barcode_barcode);
                $barcode->setType('CINcode128');
                $barcode->setScale(2);
                $barcode->setLabel('');
                $barcode->setThickness(30);
                $barcode->setFontSize(14);
                $code = $barcode->generate();
            @endphp
            @if ($data['current']->barcode_design == 'shelf_tag')
                <div class="barcode_barcode_align dbl-table">
            @else
                <div class="dbl-table">
            @endif
                @if( $data['current']->barcode_design == 'ex_weight_barcode_prefix_99'
                    || $data['current']->barcode_design == 'barcode_with_expiry')
                    @if($data['current']->supplier_name == 'hashim_and_co')
                        <div class="address">
                            <div class="text-nowrap">
                                Hashim & Co. , H-1 470 Akbar Mandi
                            </div>
                            <div class="text-nowrap">
                                  PKD by {{auth()->user()->branch->branch_name_arabic}}
                            </div>
                            <div class="text-nowrap">
                                0423-7653021
                            </div>
                        </div>
                    @endif

                    @if($data['current']->supplier_name == 'defence_rice')

                    <div class="address">
                        <div class="text-nowrap">
                            Defence Rice , Ghazi Road Lahore
                        </div>
                        <div class="text-nowrap">
                              PKD by {{auth()->user()->branch->branch_name_arabic}}
                        </div>
                        <div class="text-nowrap">
                            0423-5800530,0423-5800540
                        </div>
                    </div>
                @endif
                @endif

                <div class="{{$data['current']->barcode_design == 'shelf_tag'?'shelf_barcode_img':'barcode_barcode_img'}}">
                    @if(isset($code) && $code != '')
                        <img src="data:image/png;base64,{{$code}}"/>
                    @else
                        <div></div>
                    @endif
                </div>
                <div class="{{$data['current']->barcode_design == 'shelf_tag'?'shelf_barcode_no':'barcode_barcode_no'}}">
                    {{$dtl->product_barcode_barcode}}
                </div>
                <div class="{{$data['current']->barcode_design == 'shelf_tag'?'shelf_product_name':'product_name'}} text-ellipsis">
                    {{$dtl->product_name}}
                </div>

                @if( $data['current']->barcode_design == 'barcode_with_price'
                        || $data['current']->barcode_design == 'barcode_with_price_expiry'
                        || $data['current']->barcode_design == 'shelf_tag')
                <div class="{{$data['current']->barcode_design == 'shelf_tag'?'shelf_rate':'rate'}}">
                    @if($dtl->barcode_labels_dtl_weight > 0)
                        Rs.{{number_format($dtl->barcode_labels_dtl_amount,3)}}
                    @else
                        Rs.{{number_format($dtl->barcode_labels_dtl_rate,3)}}
                    @endif
                </div>
                @endif
                @if($data['current']->best_before == 1
                && ($data['current']->barcode_design == 'barcode_with_expiry'
                || $data['current']->barcode_design == 'barcode_with_price_expiry'
                || $data['current']->barcode_design == 'ex_weight_barcode_prefix_99'))
                <div class="mfg_bb">
                    <span>
                        MFG: {{date('d-M-Y', strtotime(trim(str_replace('/','-',$data['current']->mfg_date))))}}
                        <br>
                        BB: {{date('d-M-Y', strtotime(trim(str_replace('/','-',$data['current']->sales_date))))}}
                    </span>
                </div>
                @endif
            </div>
        @endfor
    @endforeach
</center>
</body>
</html>
