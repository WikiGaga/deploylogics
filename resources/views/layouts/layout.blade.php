<!DOCTYPE html>
<html lang="en">

<!-- begin::Head -->
<head>
    <base href="">
    <meta charset="utf-8" />
    <title>@yield('title')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!--begin::Fonts -->
    {{--<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700|Roboto:300,400,500,600,700">--}}
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">

    <!--end::Fonts -->

    <!--begin::Page Vendors Styles(used by this page) -->

    @yield('pageCSS')
    <!--end::Page Vendors Styles -->

    <!--begin::Global Theme Styles(used by all pages) -->
    <link href="/assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
    <link href="/assets/css/style.bundle.css" rel="stylesheet" type="text/css" />

    <!--end::Global Theme Styles -->

    <!--begin::Layout Skins(used by all pages) -->
    <link href="/assets/css/skins/header/base/light.css" rel="stylesheet" type="text/css" />
    <link href="/assets/css/skins/header/menu/light.css" rel="stylesheet" type="text/css" />
    <link href="/assets/css/skins/brand/dark.css" rel="stylesheet" type="text/css" />
    <link href="/assets/css/skins/aside/dark.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/custom.css?v=2') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/responsive.css') }}" rel="stylesheet" type="text/css" />

    <!--end::Layout Skins -->
    <link rel="shortcut icon" href="/assets/media/logos/favicon.ico" />
    <script>
        function bodyFunc(){
            if(sessionStorage.getItem("sidebar_toggle") == 'kt-aside--minimize'){
                var elementBody = document.getElementById("bodyId");
                elementBody.classList.add(sessionStorage.getItem("sidebar_toggle"))
            }
        }
        var cd = console.log;
    </script>
    <script src="/js/pages/js/lang/en.js" type="text/javascript"></script>
</head>

<!-- end::Head -->

<!-- begin::Body -->

<body id="bodyId" class="pointerEventsNone kt-quick-panel--right kt-demo-panel--right kt-offcanvas-panel--right kt-header--fixed kt-header-mobile--fixed kt-subheader--enabled kt-subheader--fixed kt-subheader--solid kt-aside--enabled kt-aside--fixed kt-page--loading">
<script> bodyFunc(); </script>
<!-- begin:: Page -->

<!-- begin:: Header Mobile -->
<div id="kt_header_mobile" class="kt-header-mobile  kt-header-mobile--fixed ">
    <div class="kt-header-mobile__logo">
        <a href="/home">
            {{ auth()->user()->business->business_short_name }}
        </a>
    </div>
    <div class="kt-header-mobile__toolbar">
        <button class="kt-header-mobile__toggler kt-header-mobile__toggler--left" id="kt_aside_mobile_toggler"><span></span></button>
        {{--<button class="kt-header-mobile__toggler" id="kt_header_mobile_toggler"><span></span></button>--}}
        <button class="kt-header-mobile__topbar-toggler" id="kt_header_mobile_topbar_toggler"><i class="flaticon-more"></i></button>
    </div>
</div>

<!-- end:: Header Mobile -->
<div class="kt-grid kt-grid--hor kt-grid--root">
    <div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--ver kt-page">

        <!-- begin:: Aside -->

        <!-- Uncomment this to display the close button of the panel
<button class="kt-aside-close " id="kt_aside_close_btn"><i class="la la-close"></i></button>
-->
        @include('elements/sidebar')
        <!-- end:: Aside -->
        <div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-wrapper" id="kt_wrapper">

            <!-- begin:: Header -->
            @include('elements/header')

            <!-- end:: Header -->
            <div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">

                @yield('content')
            </div>

            <!-- begin:: Footer -->
            <div class="kt-footer  kt-grid__item kt-grid kt-grid--desktop kt-grid--ver-desktop" id="kt_footer">
                <div class="kt-container  kt-container--fluid ">
                    <div class="kt-footer__copyright">
                        Login From: &nbsp;<b> {{auth()->user()->branch->branch_name}} </b>
                    </div>
                    <div class="kt-footer__menu">
                        {{ Date('Y') }}&nbsp;&copy;&nbsp;<a href="/" target="_blank" class="kt-link">Royal ERP</a>
                    </div>
                </div>
            </div>

            <!-- end:: Footer -->
        </div>
    </div>
</div>

<!-- end:: Page -->

<!-- begin::Scrolltop -->
<div id="kt_scrolltop" class="kt-scrolltop">
    <i class="fa fa-arrow-up"></i>
</div>
@include('elements/popup')
<!-- end::Scrolltop -->

<!-- begin::Global Config(global config for global JS sciprts) -->
<script>
    var dataSession = <?php echo json_encode(Session::get('dataSession')); ?>;
   // var dataSession = json_decode(dataSession);
    var spinner = '<tr id="spinner"><td colspan="19"><div class="kt-spinner kt-spinner--lg kt-spinner--success kt-spinner-center"> <span>loading..</span></div></div></td></tr>';
    var KTAppOptions = {
        "colors": {
            "state": {
                "brand": "#5d78ff",
                "dark": "#282a3c",
                "light": "#ffffff",
                "primary": "#5867dd",
                "success": "#34bfa3",
                "info": "#36a3f7",
                "warning": "#ffb822",
                "danger": "#fd3995"
            },
            "base": {
                "label": [
                    "#c5cbe3",
                    "#a1a8c3",
                    "#3d4465",
                    "#3e4466"
                ],
                "shape": [
                    "#f0f3ff",
                    "#d9dffa",
                    "#afb4d4",
                    "#646c9a"
                ]
            }
        }
    };
    var emptyArr = ["",undefined,'NaN',NaN,null,"0",0,'Infinity'];
    function valueEmpty(val){
        if(val == 'Infinity' || val == 0 || val == undefined || val == "" || val == null || val == NaN || val == 'NaN' || !val){
            return true;
        }
        return false;
    }
    function funcJsDate(patt,dval){
        patt = patt.replace(" ", "");
        const d = new Date(dval);
        let day = d.getDate();
        day = (day < 10)? '0'+day.toString() :day ;

        let month = parseInt(d.getMonth()) + 1 ;
        month = (month < 10)? '0'+month.toString() :month ;

        let year = d.getFullYear();

        const pattren = patt.split("-");
        var pattrenDate = '';
        var len = pattren.length;
        for(var i = 0; i < len; i++){
            if('d' == pattren[i]){ pattrenDate += day; }
            if('m' == pattren[i]){ pattrenDate += month; }
            if('y' == pattren[i]){ pattrenDate += year; }
            if((len-1) != i){ pattrenDate += '-'; }
        }
        return pattrenDate;
    }

    function funcJsDateFormat(oldPatt,newPatt,dval){
        var oldPattern = oldPatt.split("-");
        var oldPatternLen = oldPattern.length;

        var val = dval.split("-");
        var valLen = val.length;

        var newVal = "";
        var newPattern = newPatt.split("-");
        var newPatternLen = newPattern.length;
        if(oldPatternLen == newPatternLen){
            for(var i = 0; i < newPatternLen; i++){
                var pp = newPattern[i];
                for(var v = 0; v < oldPatternLen; v++){
                    if(pp == oldPattern[v]){
                        newVal += val[v];
                    }

                }
                if((newPatternLen-1) != i){ newVal += '-'; }
            }
        }
        return newVal;
    }
    function funcNumberFloat(num,fixed=3){
        var number = num;
        if(valueEmpty(number)){
            return parseFloat(0).toFixed(fixed);
        }
        if(typeof num == 'string'){
            number = num.toString();
            number = number.replaceAll(',', '')
        }
        if(valueEmpty(fixed)) {
            return parseFloat(number);
        }else{
            return parseFloat(number).toFixed(fixed);
        }
    }
    function funcCalcNumberFloat(num,fixed=3){
        var number = num;
        if(valueEmpty(number)){
            return parseFloat(0);
        }
        if(typeof num == 'string'){
            number = num.toString();
            number = number.replaceAll(',', '')
        }
        return parseFloat(number);
    }
</script>

<!-- end::Global Config -->

<!--begin::Global Theme Bundle(used by all pages) -->
<script src="/assets/plugins/global/plugins.bundle.js" type="text/javascript"></script>
<script src="/assets/js/scripts.bundle.js" type="text/javascript"></script>
<script src="{{ asset('js/math.js') }}" type="text/javascript"></script>
<!--end::Global Theme Bundle -->

<!--begin::Page Vendors(used by this page) -->


@yield('pageJS')
<!--end::Page Vendors -->

<!--begin::Page Scripts(used by this page) -->
<script src="/assets/js/pages/crud/forms/widgets/select2.js" type="text/javascript"></script>
<script src="/assets/js/pages/components/extended/toastr.js" type="text/javascript"></script>
<script src="{{ asset('js/pages/js/language.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/pages/js/select2.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/pages/js/shortcuts.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/pages/js/custom_new.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/pages/js/constants.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/pages/js/validateInputFields.js') }}" type="text/javascript"></script>
@yield('customJS')

@include('layouts.pageSetting')
@include('layouts.commonJSFunc')

@yield('customJSEnd')

</body>

<!-- end::Body -->
</html>
