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
            margin:0 auto;
            width: 21cm;
        }
        @media print {
            .bl-table {display: block; page-break-inside: avoid !important;}
        }
        @page {
            size: 21cm 29.7cm; /* DIN A4 standard, Europe */
            margin:14.4px 0;
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
        .print-container{
            background-color: #fff;
            min-height: 259px;
            box-sizing: border-box;
            position:relative;
            background-image: url('/coupons/print/images/ramadan_coupon_2022.jpg');
            background-size: 100% 100%;
        }
    </style>
</head>
<body onload="print_document();" onafterprint="redirectBack();">
{{--<body onload="print_document();">--}}
@php
    $dtls = isset($data['current']->coupon_dtl)? $data['current']->coupon_dtl:[];
        // Put Page Break After 4 Divs
@endphp
    @if(isset($dtls))
        @php $number = 0; @endphp
        @foreach($dtls as $coupon)
            @if(isset($coupon->coupon_benificery))
                @foreach($coupon->coupon_benificery as $benifi)
                    @php 
                        $number++;
                        $valid_branch = "";
                        $branch_ids = isset($data['current']->coupon_valid_branches) ? explode("," ,$data['current']->coupon_valid_branches) : [];
                        $branch_lists = \Illuminate\Support\Facades\DB::table('tbl_soft_branch')->whereIn('branch_id',$branch_ids)->get('branch_short_name');
                        foreach($branch_lists as $branch_list){
                            $valid_branch .= $branch_list->branch_short_name . ',';
                        }
                        $valid_branch = substr($valid_branch, 0, -1);
                        $validaity_date = explode(" / " , $coupon->validity_date);
                        if($data['current']->show_donater_name == 1){
                            $account = $data['current']->customer->customer_name;
                        }else{
                            $account = $data['current']->customer->customer_id;
                        }
                        $customer_code = $data['current']->customer->customer_code;
                    @endphp
                    <div class="print-container">
                        <span style="position:absolute;font-size: 16px;left: 49%;top: 17%;font-weight:bold;color: #fb4240;transform: translateX(-50%);">{{ $benifi->coupon_identifier }}</span>
                        <span style="position:absolute;font-size: 18px;left: 12%;top: 25%;font-weight:normal;color: #fb4240;">{{ $benifi->coupon_benificery }}</span>
                        <span style="position:absolute;font-size: 18px;left: 52%;top: 25%;font-weight:normal;color: #fb4240;">{{ $benifi->coupon_benificery }}</span>
                        <span style="position:absolute;font-size: 14px;left: 12.5%;top: 79%;font-weight:bold;color: #fb4240;">{{ $validaity_date[0] }}</span>
                        <span style="position:absolute;font-size: 14px;left: 28%;top: 79%;font-weight:bold;color: #fb4240;">{{ $validaity_date[1] }}</span>
                        <span style="position:absolute;font-size: 14px;left: 73%;top: 79%;font-weight:bold;color: #fb4240;">{{ $validaity_date[0] }}</span>
                        <span style="position:absolute;font-size: 14px;left: 57%;top: 79%;font-weight:bold;color: #fb4240;">{{ $validaity_date[1] }}</span>
                        <span style="position:absolute;font-size: 20px;left: 25%;top: 69%;font-weight:bold;color: blue;">ALL NON FOOD, READYMADE, HOUSEHOLD</span>
                        <span style="position:absolute;font-size: 20px;left: 66%;top: 67%;font-weight:bold;color: blue;"></span>
                        <span style="position:absolute;font-size: 27px;left: 39%;top: 39%;/* font-weight:bold; */color: blue;transform: translateX(-50%);">{{ number_format($benifi->coupon_value,3) }}</span>
                        <span style="position:absolute;font-size: 27px;left: 60%;top: 39%;/* font-weight:bold; */color: blue;transform: translateX(-50%);">{{ number_format($benifi->coupon_value,3) }}</span>
                        <span style="position:absolute;font-size: 14px;left: 38%;top: 52%;/* font-weight:bold; */color: #fb4240;">{{ $valid_branch }}</span>
                        <span style="position:absolute;font-size: 17px;left: 30%;top: 88%;/* font-weight:bold; */color: #fb4240;transform: translateX(-50%);">{{ $customer_code }}</span>
                        <span style="position:absolute;font-size: 17px;left: 67%;top: 88%;/* font-weight:bold; */color: #fb4240;transform: translateX(-50%);">{{ $account }}</span>
                    </div>
                    @php if($number != 4){ echo '<hr class="hr" style="border-style: dashed;border-color:grey;margin-top: 8.5px;margin-bottom: 8.5px;">';}else{$number = 0; echo '<div style="page-break-after: always;"></div>';}  @endphp
                @endforeach
            @endif
        @endforeach
    @endif
<script>
    function print_document(){
        window.print();
    }
    function redirectBack(){
        window.close();
        history.back();
    }
    // paginateDivs();
    function paginateDivs() {
        var pageHeight = 900; // Experiment with different values here, this
                        // is 800 pixels.
        var lastPage = 0;

        var divs = document.getElementsByTagName("div");
        for (var i = 0; i < divs.length; i++) {
            var divBottom = divs[i].offsetTop + divs[i].offsetHeight;
            if (divBottom - lastPage > pageHeight) {
                lastPage = divs[i].offsetTop;
                divs[i].style.pageBreakBefore = "always";
            }
        }
    }
</script>
</body>
</html>
@endpermission
