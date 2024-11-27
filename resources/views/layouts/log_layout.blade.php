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
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/responsive.css') }}" rel="stylesheet" type="text/css" />

    <!--end::Layout Skins -->
    <link rel="shortcut icon" href="/assets/media/logos/favicon.ico" />

    <script src="/js/pages/js/lang/en.js" type="text/javascript"></script>
</head>

<!-- end::Head -->

<!-- begin::Body -->

<body id="bodyId">
@yield('content')
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
<script src="{{ asset('js/pages/js/custom.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/pages/js/constants.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/pages/js/validateInputFields.js') }}" type="text/javascript"></script>
@yield('customJS')


@include('layouts.commonJSFunc')
</body>

<!-- end::Body -->
</html>
