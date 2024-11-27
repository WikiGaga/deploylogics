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

    <link href="{{ asset('css/print.css') }}" rel="stylesheet" type="text/css" />
    @yield('pageCSS')
</head>

<!-- end::Head -->

<!-- begin::Body -->

<body id="bodyId">
@include('prints.partial.header')
@yield('content')
@include('prints.partial.footer')
</body>
<!-- end::Body -->
</html>
