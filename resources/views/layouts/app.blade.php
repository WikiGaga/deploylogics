<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="ltr" id="htmlRoot">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <!-- RTL CSS - loaded dynamically based on user preference -->
    <link href="{{ asset('css/rtl.css') }}" rel="stylesheet" type="text/css" id="rtlStylesheet" disabled />

    <script>
        (function() {
            var rtlEnabled = localStorage.getItem('rtl_enabled') === 'true';
            if (rtlEnabled) {
                document.documentElement.setAttribute('dir', 'rtl');
                document.getElementById('rtlStylesheet').disabled = false;
            }
        })();

        function toggleRTL(enabled) {
            localStorage.setItem('rtl_enabled', enabled);
            document.documentElement.setAttribute('dir', enabled ? 'rtl' : 'ltr');
            document.getElementById('rtlStylesheet').disabled = !enabled;
        }

        document.addEventListener('DOMContentLoaded', function() {
            var rtlToggle = document.getElementById('rtlToggle');
            if (rtlToggle) rtlToggle.checked = localStorage.getItem('rtl_enabled') === 'true';
        });
    </script>
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>
<script>
// RTL Form Layout - Swap labels and inputs, reverse column order
document.addEventListener('DOMContentLoaded', function() {
    function applyRTLFormLayout() {
        if (document.documentElement.getAttribute('dir') === 'rtl') {
            const mainFormRows = document.querySelectorAll('.form-group-block.row, .kt-portlet__body > .form-group-block.row');
            mainFormRows.forEach(function(row) {
                const columns = Array.from(row.querySelectorAll(':scope > [class*="col-lg-"], :scope > [class*="col-md-"]'));
                if (columns.length > 1) {
                    columns.reverse().forEach(function(col) {
                        row.appendChild(col);
                    });
                }
            });
            
            const innerRows = document.querySelectorAll('.form-group-block .col-lg-4 > .row, .form-group-block .col-lg-6 > .row, .form-group-block .col-md-4 > .row, .form-group-block .col-md-6 > .row, .kt-portlet__body .col-lg-4 > .row, .kt-portlet__body .col-lg-6 > .row');
            innerRows.forEach(function(row) {
                const label = row.querySelector('label[class*="col-"]');
                const inputDiv = row.querySelector('div[class*="col-"]');
                if (label && inputDiv && label.nextElementSibling === inputDiv) {
                    row.insertBefore(inputDiv, label);
                }
            });
        }
    }
    applyRTLFormLayout();
});
</script>
</body>
</html>
