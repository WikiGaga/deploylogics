@php
//essential for header
$heading = strtoupper($data['title']);
$businessName = strtoupper(auth()->user()->business->business_short_name);
$pdf_link = $data['print_link'];
$print_type = $data['type'];

$id = $data['current']->brochure_id;
$date = date('d-m-Y', strtotime(trim(str_replace('/', '-', $data['current']->brochure_date))));
$brochure_name = isset($data['current']->brochure_name) ? $data['current']->brochure_name : '';
$dtls = isset($data['current']->brochures_dtl) ? $data['current']->brochures_dtl : [];

$selectedBranches = explode("," , $data['current']->branches);
$branches = $data['branches'];
@endphp
@permission($data['permission'])
    @extends('layouts.print_layout_fromNew')
    @section('title', $heading)
@section('heading', $heading)

@section('pageCSS')
<link href="https://fonts.googleapis.com/css2?family=Amiri&family=Markazi+Text&family=Noto+Nastaliq+Urdu&family=Tajawal:wght@200&display=swap" rel="stylesheet">
    <style>
         @page {
            size: A4;
            margin: 0;
         }

         @media print {
            html, body {
               width: 210mm;
               height: 297mm;
            }
         }

        .prprt_elp,.prprt_elp2{overflow:hidden;white-space:nowrap;text-overflow:ellipsis;display:inline-block}*{font-family: 'Verdana' !important;}body{width:100%;margin:0;padding:0;-webkit-font-smoothing:antialiased}.w3logo img{width:179px!important}.footer_logo img{width:200px!important}html{width:100%}table{font-size:14px;border:0}.scale2{width:50%!important}.location-anchr a{color:#b1b6bc;text-decoration:none}.agent_imagem{text-align:center!important;padding:0 0 0 13px}.prprt_elp{max-width:200px}.prprt_elp2{max-width:64%}.text_anchr_lft{width:60%;text-align:left;float:left}.text_anchr_rht{width:30%;text-align:right;float:right}.p_biotext{text-align:justify!important;padding:0 13px 0 0!important}.email_agntwtd{max-width:210px;word-wrap:break-word}.agnt_emltop{max-width:280px;word-wrap:break-word}.listng_img img{max-width:100%;max-height:230px;object-fit:contain}@media only screen and (max-width:667px){.listng_img img{width:657px!important;max-width:100%!important;height:100%!important}.clearbtm{clear:both}.margintop10{margin-top:10px!important}td[class=scale-center-top],td[class=scale-left-all],td[class=scale-left-top],td[class=scale-right-all],td[class=scale-right-top]{padding-top:25px!important}td[class=scale-center-bottom],td[class=scale-left-all],td[class=scale-left-bottom],td[class=scale-right-all],td[class=scale-right-bottom]{padding-bottom:25px!important}.agnt_imgmrgn{margin:0 auto!important}.agnt_emltop,.email_agntwtd{max-width:100%}.agent_imagem{text-align:center!important}.w3logo,.w3logo img{display:block;text-align:center}.width_1oo{width:100%!important}.listng_img a img{width:667px;max-width:100%}.tdhndr_p{background:#999!important}.w3logo img{margin:0 auto}td.fontSmall{font-size:28px!important}.main_classn table.scale{margin:0 auto 10px!important}.fonrlwy{font-size:18px!important;font-weight:300!important;font-family:Verdana !important}td.agilebnr-hgt{height:60px!important}td.agilebnr-hgt1{height:50px}.w3logo{width:100%;font-size:2.5em!important}.scale-center.w3slid-text{font-size:27px!important;line-height:30px}.textCenter{text-align:center!important}td[class=scale-right-all],td[class=scale-right-bottom],td[class=scale-right-top],td[class=scale-right]{text-align:right!important;width:100%!important}.font_15{font-size:16px!important}.prprt_elp{max-width:200px}.prprt_elp2{max-width:100%}.copy-right{text-align-last:center}.listng_img{height:200px!important;width:100%!important}table[class=scale]{width:95%!important;float:none!important}table[class=scale90]{width:90%!important}table[class=scale85]{width:85%!important}table[class=scale80]{width:80%!important}table[class=scale75]{width:55%!important}table[class=scale-reset]{width:100%!important;height:auto!important}td[class=scale-left-all],td[class=scale-left-bottom],td[class=scale-left-top],td[class=scale-left]{width:100%!important;text-align:left!important}td[class=scale-center-both],td[class=scale-center]{width:100%!important;text-align:center!important}td[class=scale-center-both-top]{width:100%!important;text-align:center!important;padding-left:20px!important;padding-right:20px!important;padding-top:25px!important}td[class=scale-center-bottom],td[class=scale-center-top]{width:100%!important;text-align:center!important}td[class=scale-center-all]{width:100%!important;text-align:center!important;padding:25px 20px!important}td[class=scale-center-extraall]{width:100%!important;text-align:center!important;padding-top:54px!important;padding-bottom:25px!important}td[class=scale-center-extratop]{width:100%!important;text-align:center!important;padding-top:60px!important}td[class=reset]{height:0!important}p[class=reset]{margin-left:0!important;margin-right:0!important}p[class=reset-top]{margin-top:0!important}img[class=reset]{display:inline!important}}@media only screen and (max-width:600px){.width_1oo{width:100%!important}.full.top-nav{width:95%}.full{width:100%}td.abt-pad{height:40px}.border_btm_dv{width:93%!important}}@media only screen and (max-width:568px){.agent_imagem,.center_res,.fullCenter,.fullcenter1{text-align:center!important}.p_biotext{padding:0!important}.border_btm_dv{width:280px!important;margin:0 auto}.fullcenter-left{width:inherit}.fullCenter,.fullcenter1{width:100%!important}.fullcenter1 td{width:100%}.fullcenter1 td a{width:100%;text-align:center}.title{font-size:2.8em!important}.scale2{width:100%!important;text-align:center!important}.view_prprty{padding:5px!important}}@media only screen and (min-device-width:375px) and (max-device-width:413px){.clearbtm{clear:both}.margintop10{margin-top:10px!important}td[class=scale-center-top],td[class=scale-left-all],td[class=scale-left-top],td[class=scale-right-all],td[class=scale-right-top]{padding-top:25px!important}td[class=scale-center-bottom],td[class=scale-left-all],td[class=scale-left-bottom],td[class=scale-right-all],td[class=scale-right-bottom]{padding-bottom:25px!important}.agnt_imgmrgn{margin:0 auto!important}.agnt_emltop,.email_agntwtd{max-width:100%}.agent_imagem{text-align:center!important}.w3logo,.w3logo img{display:block;text-align:center}.width_1oo{width:100%!important}.listng_img a img{width:667px;max-width:100%}.tdhndr_p{background:#999!important}.w3logo img{margin:0 auto}td.fontSmall{font-size:28px!important}.main_classn table.scale{margin:0 auto 10px!important}.fonrlwy{font-size:18px!important;font-weight:300!important;font-family:Verdana !important}td.agilebnr-hgt{height:60px!important}td.agilebnr-hgt1{height:50px}.w3logo{width:100%;font-size:2.5em!important}.scale-center.w3slid-text{font-size:27px!important;line-height:30px}.textCenter{text-align:center!important}td[class=scale-right-all],td[class=scale-right-bottom],td[class=scale-right-top],td[class=scale-right]{text-align:right!important;width:100%!important}.font_15{font-size:16px!important}.prprt_elp{max-width:200px}.prprt_elp2{max-width:100%}.copy-right{text-align-last:center}.listng_img{height:200px!important;width:100%!important}table[class=scale]{width:95%!important;float:none!important}table[class=scale90]{width:90%!important}table[class=scale85]{width:85%!important}table[class=scale80]{width:80%!important}table[class=scale75]{width:55%!important}table[class=scale-reset]{width:100%!important;height:auto!important}td[class=scale-left-all],td[class=scale-left-bottom],td[class=scale-left-top],td[class=scale-left]{width:100%!important;text-align:left!important}td[class=scale-center-both],td[class=scale-center]{width:100%!important;text-align:center!important}td[class=scale-center-both-top]{width:100%!important;text-align:center!important;padding-left:20px!important;padding-right:20px!important;padding-top:25px!important}td[class=scale-center-bottom],td[class=scale-center-top]{width:100%!important;text-align:center!important}td[class=scale-center-all]{width:100%!important;text-align:center!important;padding:25px 20px!important}td[class=scale-center-extraall]{width:100%!important;text-align:center!important;padding-top:54px!important;padding-bottom:25px!important}td[class=scale-center-extratop]{width:100%!important;text-align:center!important;padding-top:60px!important}td[class=reset]{height:0!important}p[class=reset]{margin-left:0!important;margin-right:0!important}p[class=reset-top]{margin-top:0!important}img[class=reset]{display:inline!important}}@media only screen and (max-width:480px){.fullCenter,.scale2{width:100%}.nav-pad{height:10px}.scale-center.w3slid-text{font-size:24px!important}.scale-center.w3p-text{font-size:14px!important;text-align:center!important}}@media only screen and (max-width:414px){.fullCenter td,.fullcenter-left{text-align:center}.fullcenter-left{width:100%}.fullCenter{width:95%;margin:0 auto;float:none}td.fontSmall{font-size:25px!important;line-height:37px}td.agilebnr-hgt{height:46px!important}td.agilebnr-hgt1{height:40px}.copy-right{text-align-last:center;padding:0 2em}a.wthree-more{padding:10px 18px!important}.scale-center.w3slid-text{font-size:21px!important}.scale-center.w3p-text{font-size:14px!important;text-align:center!important}.cpyrghtxt{font-size:8px!important}.listng_img{height:166px!important}}@media only screen and (max-width:320px){.w3logo{font-size:2.3em!important}.fullCenter td{height:40px}.fullcenter{width:100%!important;text-align:center}td.fontSmall{font-size:23px!important}td.abt-pad{height:30px;width:100%}.title{font-size:2.3em!important}.scale-center.w3slid-text{font-size:19px!important}.prprt_elp{max-width:100px}.prprt_elp2{max-width:80px}}.price-tag.was{font-size:16px;background:##1d2f5c;padding:10px;color:#fff;border-radius:0px 3px 3px 0px;}.price-tag .new{font-size:18px;font-weight:700}
        span.discount-tag {
            position: absolute;
            width: 120px;
            bottom: -20px;
            right: -12px;
        }
        span.discount-tag .discount-amount{
            font-size: 16px;
            position: relative;
            top: -78px;
            left: 42px;
            color: #fff;
            font-weight: bold;
        }
        span.line-through::after{
            content: "";
            width: 40px;
            height: 3px;
            background-color: rgb(250, 77, 86);
            position: absolute;
            border-radius: 5px;
            left: 4px;
            transform: rotate(12deg);
            top: 20px;
        }
         .item-container {
            display: grid;
            grid-column: 3;
            width: 33%;
            float: left;
            position: relative;
            margin: 21px 0px;
         }
         .item-container-inner{
            border-radius: 15px 15px 0 0;
            overflow: hidden;
            width: 95%;
            margin: 0px auto;
         }
         .branch_name_arabic{
            padding: 0 18px;
            border-right: 2px solid;
            width: 100%;
            text-align: center;
         }
         .branch_name_arabic:last-child{
            border-right:none !important;
         }
    </style>
@endsection

@section('content')
    
   {{-- THIS IS WHERE TABLE WILL START --}}
   @php
      $background = isset($data['current']->background_image)?'/uploads/'.$data['current']->background_image:"";
   @endphp
   @if($data['current']->background_type == 1) 
      <div class="paper-a4" style="background-color:{{$data['current']->background_color}};">
   @else
      @php $background = isset($data['current']->background_image)?'/uploads/'.$data['current']->background_image:""; @endphp
      <div class="paper-a4" style="background-image: url({{$background}});">
   @endif
      <span style="background-color:#fff;width: 205mm;font-size:18px;padding:10px;transform:rotate(270deg);margin-top: 10px;position: absolute;top: 500px;margin:0px;z-index: 9999;left: -100mm;border-radius:0 0 15px 15px;text-align: center;">يسري العرض من تاريخ <span style="color: rgb(250, 77, 86);">{{ $data['current']->start_date }}</span> م الى تاريخ <span style="color:rgb(250, 77, 86);">{{ $data['current']->end_date }}</span> م</span>
      @if(isset($data['current']->header_heading))
         <span style="background-color:rgb(250, 77, 86);width:25%;font-size:18px;color:#fff;padding:10px;margin-top: 10px;position: absolute;top: 83px;height:15px;margin:0px;z-index: 9999;left: 37%;border-radius:15px;line-height: 12px;text-align: center;">{{ $data['current']->header_heading }}</span>
      @endif
      <table align="center" border="0" cellpadding="0" cellspacing="0" class="mobile" width="100%" data-module="navigation">
         <tr>
            <td class="nav-pad" height="20" >
               <table align="center" width="100%" cellspacing="0" cellpadding="0" border="0">
                  <tr>
                     <td>
                        <table align="center" border="0" cellpadding="0" cellspacing="0" class="scale" width="800">
                           <tbody>
                              <tr>
                                 <td>
                                    <table align="center" border="0" cellpadding="0" cellspacing="0" class="" width="55%" style="background:rgb(250, 77, 86);border-radius: 0 0 55px 55px;">
                                       <tbody>
                                          <tr style="display:flex;justify-content:center;height:100px;">
                                             <td>
                                                <img src="{{ asset('uploads') }}/{{ $data['current']->branch_logo }}" width="100">
                                             </td>
                                             <td height="100" align="center">
                                             <h1 style="color:#fff;margin:0;padding:0 12px;font-size:34px;"><span style="font-size: 40px;text-shadow: -1px -1px 0 #fff, 1px -1px 0 #fff, -1px 1px 0 #fff, 1px 1px 0 #fff;color:#008037;"> الطيبات </span><span style="color:#1d2f5c;font-size: 28px;text-shadow: -1px -1px 0 #fff, 1px -1px 0 #fff, -1px 1px 0 #fff, 1px 1px 0 #fff;">هايبر ماركيت</span> </h1>
                                                <h1 style="margin:0;padding:0 12px;font-size:32px;text-shadow: -1px -1px 0 #fff, 1px -1px 0 #fff, -1px 1px 0 #fff, 1px 1px 0 #fff;color:#008037;""><span >A'tayebat</span> <span style="color:#1d2f5c;font-size:18px;">Hypermarket</span></h1>
                                             </td>
                                          </tr>
                                       </tbody>
                                    </table>
                                 </td>
                              </tr>
                           </tbody>
                        </table>
                     </td>
                  </tr>
               </table>
            </td>
         </tr>
      </table>

      <!-- End Header -->
      <table align="center" border="0" cellpadding="0" cellspacing="0" class="scale" data-module="3 Col" width="800">
         <tbody class="main_classn">
            <tr>
               <td>
                  <table class="title" border="0" cellspacing="0" cellpadding="0" align="center" style="font-family: Verdana; font-size: 3em; color: #000;">
                     <tbody>
                        <tr>
                           <td class="title-scale" height="10"></td>
                        </tr>
                     </tbody>
                  </table>
               </td>
            </tr>
            <tr>
               <td>
                  <table align="center" border="0" cellpadding="0" cellspacing="0" class="scale90" width="800">
                     <tbody>
                        <tr style="height: 885px;">
                           <td style="display: block;width:85%;margin:15px auto;">
                              @if (isset($dtls))
                                 {{-- PUT LOOP HERE --}}
                                 @foreach ($dtls as $key => $data)
                                    @php
                                       $priceWas = $data->brochure_dtl_amount + $data->brochure_dtl_vat_amount;
                                       $priceNew = $data->brochure_dtl_gross_amount;
                                       // Product Image
                                       $base64 = isset($data->barcode->product_image_url) ? public_path('products/'.$data->barcode->product_image_url):"";
                                        dd($base64);
                                       $path =  $base64;
                                       if(!empty($path)){
                                          if(file_exists($path)){
                                             $base64 =  $path;
                                          }else{
                                             $base64 = asset('/products/noimage.png');
                                          }
                                          // ===== Dont Convert The Image To Base64
                                          // $type = pathinfo($path, PATHINFO_EXTENSION);
                                          // $data = file_get_contents($path);
                                          // $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);;
                                       }else{
                                          $base64 = asset('/products/noimage.png');
                                       }
                                       
                                    @endphp
                                    <div class="item-container">
                                       <div class="item-container-inner">
                                          <span class="price-tag was" style="position:absolute; top: 10px;background-color:#008037;">
                                             <span style="text-decoration: line-through;">{{ number_format($priceWas, 3) }}</span>
                                             <span class="new" style="width:100%;display: block">{{ number_format($priceNew, 3) }} ر.ع.</span>
                                          </span>   
                                          <span class="discount-tag">
                                          <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                                             <path fill="#FA4D56" d="M62.9,1.6C62.9,32.6,31.5,65.2,0.4,65.2C-30.7,65.2,-61.4,32.6,-61.4,1.6C-61.4,-29.4,-30.7,-58.7,0.4,-58.7C31.5,-58.7,62.9,-29.4,62.9,1.6Z" transform="translate(100 100)" />
                                          </svg>
                                          <span class="discount-amount">{{ number_format($data->brochure_dtl_disc_percent, 1) }}% <br/>OFF</span></span>
                                          </span>
                                          <a href="#"  class="listng_img" style="display:inline-block; width: 100%;overflow:hidden;">
                                             <img src="{{ $base64 }}" style="display:block;" width="100%"  >
                                          </a>
                                       </div>
                                       <div class="item-container-inner" style="background-color: #008037;text-align: center;border-radius: 0 0 10px 10px;margin-top: -3px;">
                                          <p class="font_15 prprt_elp" style="padding-top:0!important;width:100%;color: #fff;margin: 0;padding: 5px 0 0 0;line-height: normal;display:inline-block;box-sizing:border-box;font-size: 14px;padding: 3px 5px 0 5px;">
                                             {{ $data->barcode->product->product_arabic_name }} </br> {{ $data->barcode->product->product_name }}
                                          </p>
                                       </div>
                                    </div>
                                 @endforeach
                              @endif
                           </td>
                        </tr>
                     </tbody>
                  </table>
               </td>
            </tr>
         </tbody>
      </table>
      <!-- END BODY -->
      <div style="background-color: #fff;width:180mm;margin:0 auto;border-radius:40px 40px 0px 0px;">
         <ul class="" style="margin: 0px;list-style: none;display: flex;font-size: 22px;padding: 10px 30px;justify-content: space-between;">
            @foreach($branches as $bran)
               @if(in_array($bran->branch_id , $selectedBranches))
                  <li class="branch_name_arabic">{{ $bran->branch_short_name_arabic }}</li>
               @endif
            @endforeach
         </ul>
      </div>
      <table align="center" border="0" cellpadding="0" cellspacing="0" class="scale nomrgn" data-module="Left Right" width="100%">
         <tbody>
            <tr>
               <td>
                  <table align="center" border="0" style="background:#1d2f5c" cellpadding="0" cellspacing="0" class="scale" width="800">
                     <tbody>
                        <tr>
                           <td height="11"></td>
                        </tr>
                        <tr style="height: 80px;">
                           <td style="text-align: center;width:800;">
                              <table align="center" border="0" cellpadding="0" cellspacing="0" class="scale" width="800">
                                 <tbody>
                                    <tr style="color:#fff;">
                                       <td align="left" style="padding:0px 3%;width:20%;border-right:1px solid #fff">
                                          <div style="display:flex;margin-left:-10px;">
                                             <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px"
                                             width="25" height="25"
                                             viewBox="0 0 172 172"
                                             style=" fill:#000000;width:75px;margin-right:3px;"><g fill="none" fill-rule="nonzero" stroke="none" stroke-width="1" stroke-linecap="butt" stroke-linejoin="miter" stroke-miterlimit="10" stroke-dasharray="" stroke-dashoffset="0" font-family="none" font-weight="none" font-size="none" text-anchor="none" style="mix-blend-mode: normal"><path d="M0,172v-172h172v172z" fill="none"></path><g fill="#ffffff"><path d="M117.7125,18.8125c-20.57281,0 -37.3025,16.72969 -37.3025,37.3025c0,1.23625 0.30906,2.44563 0.43,3.655c-25.43719,-2.43219 -47.93156,-14.68719 -63.21,-33.4325c-0.71219,-0.90031 -1.81406,-1.38406 -2.96969,-1.30344c-1.14219,0.08062 -2.16344,0.73906 -2.72781,1.73344c-3.21156,5.52281 -5.0525,11.87875 -5.0525,18.705c0,8.26406 2.95625,15.82938 7.525,22.0375c-0.88687,-0.38969 -1.85437,-0.60469 -2.6875,-1.075c-1.06156,-0.56437 -2.33812,-0.5375 -3.37281,0.08063c-1.03469,0.61812 -1.66625,1.73344 -1.67969,2.92937v0.43c0,12.67156 6.5575,23.67688 16.2325,30.4225c-0.1075,-0.01344 -0.215,0.02688 -0.3225,0c-1.1825,-0.20156 -2.37844,0.215 -3.17125,1.11531c-0.79281,0.90031 -1.04812,2.15 -0.69875,3.29219c3.84313,11.94594 13.6525,21.07 25.8,24.4025c-9.675,5.75125 -20.89531,9.1375 -33.0025,9.1375c-2.62031,0 -5.13312,-0.13437 -7.6325,-0.43c-1.6125,-0.215 -3.15781,0.72563 -3.69531,2.2575c-0.55094,1.53188 0.05375,3.23844 1.43781,4.085c15.52031,9.95719 33.94313,15.8025 53.75,15.8025c32.10219,0 57.28406,-13.41062 74.175,-32.5725c16.89094,-19.16187 25.6925,-44.04812 25.6925,-67.295c0,-0.98094 -0.08062,-1.935 -0.1075,-2.9025c6.30219,-4.82406 11.9325,-10.48125 16.34,-17.0925c0.87344,-1.27656 0.77938,-2.98312 -0.22844,-4.16562c-0.99438,-1.1825 -2.67406,-1.54531 -4.07156,-0.88688c-1.77375,0.79281 -3.84312,0.87344 -5.6975,1.505c2.44563,-3.26531 4.54188,-6.78594 5.805,-10.75c0.43,-1.35719 -0.04031,-2.84875 -1.15562,-3.73562c-1.11531,-0.87344 -2.67406,-0.98094 -3.89688,-0.24188c-5.87219,3.48031 -12.37594,5.92594 -19.2425,7.4175c-6.665,-6.235 -15.43969,-10.4275 -25.2625,-10.4275zM117.7125,25.6925c8.77469,0 16.70281,3.74906 22.2525,9.675c0.83313,0.86 2.05594,1.22281 3.225,0.9675c4.48813,-0.88687 8.74781,-2.19031 12.9,-3.87c-2.39187,3.225 -5.34812,5.97969 -8.815,8.0625c-1.57219,0.76594 -2.31125,2.58 -1.73344,4.23281c0.56437,1.63937 2.28437,2.59344 3.99094,2.21719c3.44,-0.41656 6.50375,-1.81406 9.7825,-2.6875c-2.94281,3.18469 -6.16781,6.06031 -9.675,8.6c-0.95406,0.69875 -1.47812,1.8275 -1.3975,3.01c0.05375,1.3975 0.1075,2.78156 0.1075,4.1925c0,21.5 -8.25062,44.84094 -23.9725,62.6725c-15.72187,17.83156 -38.8075,30.315 -69.015,30.315c-13.71969,0 -26.67344,-3.03687 -38.3775,-8.385c14.5125,-1.11531 27.89625,-6.24844 38.7,-14.7275c1.12875,-0.90031 1.57219,-2.40531 1.11531,-3.77594c-0.45688,-1.37063 -1.72,-2.31125 -3.15781,-2.35156c-11.34125,-0.20156 -20.84156,-6.79937 -25.9075,-16.125c0.18813,0 0.34938,0 0.5375,0c3.39969,0 6.75906,-0.43 9.89,-1.29c1.505,-0.44344 2.53969,-1.84094 2.48594,-3.41312c-0.05375,-1.57219 -1.16906,-2.91594 -2.70094,-3.25188c-12.24156,-2.4725 -21.41937,-12.44312 -23.5425,-24.8325c3.46688,1.19594 7.01438,2.13656 10.8575,2.2575c1.57219,0.09406 2.99656,-0.88687 3.48031,-2.37844c0.48375,-1.49156 -0.1075,-3.13094 -1.43781,-3.96406c-8.17,-5.46906 -13.545,-14.78125 -13.545,-25.37c0,-3.92375 1.02125,-7.525 2.365,-10.965c17.2,18.87969 41.28,31.41688 68.4775,32.7875c1.075,0.05375 2.12313,-0.38969 2.82188,-1.20937c0.69875,-0.83313 0.9675,-1.935 0.72562,-2.98313c-0.52406,-2.23062 -0.86,-4.59562 -0.86,-6.9875c0,-16.85062 13.57188,-30.4225 30.4225,-30.4225z"></path></g></g>
                                             </svg>
                                             <span style="line-height: 25px;">atayebat_group</span>
                                          </div>
                                          <div style="display:flex;position:relative;left:-7px;">
                                             <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px"
                                                width="25" height="25"
                                                viewBox="0 0 172 172"
                                                style=" fill:#000000;width:75px;margin-right:3px;"><g fill="none" fill-rule="nonzero" stroke="none" stroke-width="1" stroke-linecap="butt" stroke-linejoin="miter" stroke-miterlimit="10" stroke-dasharray="" stroke-dashoffset="0" font-family="none" font-weight="none" font-size="none" text-anchor="none" style="mix-blend-mode: normal"><path d="M0,172v-172h172v172z" fill="none"></path><g fill="#ffffff"><path d="M55.04,10.32c-24.65626,0 -44.72,20.06374 -44.72,44.72v61.92c0,24.65626 20.06374,44.72 44.72,44.72h61.92c24.65626,0 44.72,-20.06374 44.72,-44.72v-61.92c0,-24.65626 -20.06374,-44.72 -44.72,-44.72zM55.04,17.2h61.92c20.9375,0 37.84,16.9025 37.84,37.84v61.92c0,20.9375 -16.9025,37.84 -37.84,37.84h-61.92c-20.9375,0 -37.84,-16.9025 -37.84,-37.84v-61.92c0,-20.9375 16.9025,-37.84 37.84,-37.84zM127.28,37.84c-3.79972,0 -6.88,3.08028 -6.88,6.88c0,3.79972 3.08028,6.88 6.88,6.88c3.79972,0 6.88,-3.08028 6.88,-6.88c0,-3.79972 -3.08028,-6.88 -6.88,-6.88zM86,48.16c-20.85771,0 -37.84,16.98229 -37.84,37.84c0,20.85771 16.98229,37.84 37.84,37.84c20.85771,0 37.84,-16.98229 37.84,-37.84c0,-20.85771 -16.98229,-37.84 -37.84,-37.84zM86,55.04c17.13948,0 30.96,13.82052 30.96,30.96c0,17.13948 -13.82052,30.96 -30.96,30.96c-17.13948,0 -30.96,-13.82052 -30.96,-30.96c0,-17.13948 13.82052,-30.96 30.96,-30.96z"></path></g></g>
                                             </svg>
                                             <span style="line-height: 25px;">atayebatgroup</span>
                                          </div>
                                       </td>
                                       <td align="left" style="padding:0px 3%;width:78%;">
                                          <div style="font-size:18px;">لمعرفة عروضنا الحالية ، يرجى إرسال كلمة (عرض) إلينا على الواتس اب 96090344</div>
                                          <div style="font-size:14px;">To know our current offers, please send us the word (offer) to WhatsApp 96090344</div>
                                       </td>
                                       <td align="left" style="width:2%;">
                                          <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px"
                                             width="50" height="50"
                                             viewBox="0 0 172 172"
                                             style=" fill:#000000;position:relative;left:-15px;"><g fill="none" fill-rule="nonzero" stroke="none" stroke-width="1" stroke-linecap="butt" stroke-linejoin="miter" stroke-miterlimit="10" stroke-dasharray="" stroke-dashoffset="0" font-family="none" font-weight="none" font-size="none" text-anchor="none" style="mix-blend-mode: normal"><path d="M0,172v-172h172v172z" fill="none"></path><g fill="#ffffff"><path d="M86,6.88c-43.6552,0 -79.12,35.4648 -79.12,79.12c0,14.03209 3.84829,27.12743 10.26625,38.5589l-10.13859,36.19391c-0.32961,1.17862 -0.00828,2.44361 0.84387,3.32204c0.85215,0.87843 2.1068,1.23803 3.29488,0.94436l37.73922,-9.3525c11.0895,5.92016 23.67289,9.45328 37.11437,9.45328c43.6552,0 79.12,-35.4648 79.12,-79.12c0,-43.6552 -35.4648,-79.12 -79.12,-79.12zM86,13.76c39.9368,0 72.24,32.3032 72.24,72.24c0,39.9368 -32.3032,72.24 -72.24,72.24c-12.80359,0 -24.7951,-3.33806 -35.21969,-9.1711c-0.76244,-0.42603 -1.65837,-0.54613 -2.5061,-0.33594l-33.04953,8.19016l8.86203,-31.61844c0.25624,-0.90292 0.13246,-1.87134 -0.34266,-2.68078c-6.3353,-10.74275 -9.98406,-23.24194 -9.98406,-36.6239c0,-39.9368 32.3032,-72.24 72.24,-72.24zM57.25047,44.72c-2.20517,0 -5.35447,0.82041 -7.94156,3.60797c-1.5539,1.67432 -8.0289,7.98315 -8.0289,19.07453c0,11.56363 8.01999,21.54162 8.98969,22.8236h0.00672v0.00672c-0.09199,-0.12088 1.23082,1.79472 2.99656,4.09172c1.76575,2.29699 4.2349,5.31383 7.33688,8.57984c6.20394,6.53203 14.92665,14.08115 25.75297,18.69828c4.98566,2.12306 8.91892,3.40494 11.8989,4.34031c5.52255,1.7341 10.55261,1.4705 14.3311,0.91375c2.82823,-0.4165 5.93885,-1.77449 9.01656,-3.72219c3.07772,-1.9477 6.09406,-4.36949 7.42422,-8.04906c0.9529,-2.63791 1.43732,-5.07565 1.6125,-7.08156c0.08758,-1.00296 0.09871,-1.88815 0.03359,-2.70765c-0.06526,-0.8195 0.00447,-1.44725 -0.75922,-2.70094c-1.60156,-2.62959 -3.41532,-2.69822 -5.30781,-3.63485c-1.0515,-0.52041 -4.04526,-1.9823 -7.04797,-3.41312c-2.99933,-1.42922 -5.5964,-2.69503 -7.19578,-3.26531c-1.01048,-0.36355 -2.24435,-0.8869 -4.02453,-0.68531c-1.78018,0.20158 -3.53839,1.48601 -4.56203,3.00328c-0.97027,1.43816 -4.87621,6.04872 -6.06703,7.40406c-0.01582,-0.00963 0.08751,0.03797 -0.38297,-0.19485c-1.47277,-0.72889 -3.27396,-1.34866 -5.93938,-2.75469c-2.66541,-1.40603 -5.99961,-3.48227 -9.64812,-6.6986v-0.00672c-5.43043,-4.78075 -9.23423,-10.78301 -10.43422,-12.79922c0.08084,-0.09618 -0.00961,0.0203 0.16125,-0.14781l0.00672,-0.00672c1.22641,-1.20797 2.31331,-2.65072 3.23172,-3.70875c1.30217,-1.50014 1.87683,-2.82258 2.49937,-4.05812c1.24072,-2.46244 0.54988,-5.17212 -0.16797,-6.59781v-0.00672c0.04957,0.09862 -0.38831,-0.86752 -0.86,-1.98203c-0.47303,-1.11769 -1.076,-2.5667 -1.72,-4.11188c-1.288,-3.09035 -2.72595,-6.55646 -3.58109,-8.58656v-0.00672c-1.00739,-2.39124 -2.37031,-4.11391 -4.15219,-4.945c-1.78188,-0.83109 -3.35616,-0.59481 -3.41984,-0.59797h-0.00672c-1.27158,-0.05866 -2.66694,-0.0739 -4.0514,-0.0739zM57.25047,51.6c1.32626,0 2.63379,0.01617 3.7289,0.06719c1.12658,0.05614 1.05651,0.06075 0.83985,-0.04031c-0.22013,-0.10265 0.07854,-0.13598 0.71891,1.38406c0.83733,1.98782 2.28238,5.46648 3.57437,8.56641c0.646,1.54996 1.25058,3.00458 1.73344,4.14547c0.48286,1.14089 0.74449,1.77788 1.04813,2.38515v0.00672l0.00672,0.00672c0.2977,0.58738 0.27137,0.21132 0.16797,0.41656c-0.72594,1.44077 -0.82444,1.79453 -1.55875,2.64047c-1.11807,1.28805 -2.25852,2.72447 -2.86219,3.31906c-0.52814,0.51882 -1.48109,1.32709 -2.0761,2.90922c-0.59592,1.58455 -0.31779,3.7586 0.63828,5.38172c1.27291,2.16099 5.46756,8.98838 11.98625,14.7275c4.10524,3.61896 7.92985,6.01407 10.98515,7.62578c3.05531,1.61171 5.54379,2.55301 6.10063,2.8286c1.32226,0.6544 2.76742,1.16255 4.44781,0.96078c1.68039,-0.20176 3.12916,-1.22133 4.0514,-2.26422l0.00672,-0.00672c1.2274,-1.39152 4.87455,-5.55605 6.62469,-8.12297c0.07417,0.02614 0.04988,0.00585 0.63156,0.215v0.00672h0.00672c0.26557,0.09457 3.59181,1.58337 6.5575,2.99656c2.96569,1.41319 5.97581,2.88202 6.95391,3.3661c1.41155,0.6986 2.07852,1.15339 2.25078,1.15562c0.01164,0.30267 0.02341,0.63065 -0.02016,1.12875c-0.12064,1.38163 -0.48995,3.29404 -1.22953,5.34141c-0.36231,1.00226 -2.24937,3.06941 -4.62922,4.57547c-2.37984,1.50606 -5.2765,2.56983 -6.34922,2.72781c-3.22536,0.47525 -7.05485,0.64875 -11.26063,-0.67188c-2.91618,-0.91535 -6.55236,-2.10022 -11.26062,-4.10515c-9.5488,-4.07231 -17.66452,-11.00199 -23.46188,-17.10594c-2.89867,-3.05197 -5.22106,-5.89303 -6.87328,-8.04234c-1.64914,-2.1453 -2.36926,-3.26169 -2.96969,-4.05141l-0.00672,-0.00672c-1.06581,-1.40946 -7.59219,-10.48746 -7.59219,-18.66469c0,-8.65422 4.01963,-12.04796 6.19469,-14.39156c1.14187,-1.23035 2.39024,-1.41094 2.89578,-1.41094z"></path></g></g>
                                          </svg>
                                       </td>
                                    </tr>
                                    <tr>
                                       <td height="5"></td>
                                    </tr>
                                 </tbody>
                              </table>
                           </td>
                        </tr>
                     </tbody>
                  </table>
               </td>
            </tr>
         </tbody>
      </table>
   </div>
@endsection

@section('customJS')
@endsection
@endpermission
