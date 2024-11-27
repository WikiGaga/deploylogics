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
    <style>
        * { font-family : Verdana, Geneva, sans-serif; }
        @font-face {
            font-family: 'NotoSans';
            src: url('../NotoSansArabic/NotoSansArabic-Regular.ttf') format('truetype');
        }
        body{
            color:#000000;
        }
        .text-right{text-align: right;}
        @media print{

        }
    </style>
    @yield('pageCSS')
</head>

<!-- end::Head -->

<!-- begin::Body -->

<body id="bodyId">
@yield('content')
</body>
<!-- end::Body -->
</html>
