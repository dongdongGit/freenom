<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport">
    <link href="{{ asset('favicon.ico') }}" rel="shortcut icon" type="image/x-icon">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/fonts/line-icons.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/main.css') }}">
</head>
<body>
    <div class="app header-default side-nav-dark" id="app">
        <div class="layout">
            @yield('content')
        </div>
    </div>
    <!-- Scripts -->
    <script src="{{ mix('js/app.js') }}"></script>
    {{--  <script src="{{ asset('assets/js/jquery-min.js') }}"></script>  --}}
    {{--  <script src="{{ asset('assets/js/popper.min.js') }}"></script>  --}}
    {{--  <script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>  --}}
    {{--  <script src="{{ asset('assets/js/jquery.app.js') }}"></script>  --}}
    {{--  <script src="{{ asset('assets/js/main.js') }}"></script>  --}}

    {{--  <script src="{{ asset('assets/plugins/morris/morris.min.js') }}"></script>  --}}
    {{--  <script src="{{ asset('assets/plugins/raphael/raphael-min.js') }}"></script>  --}}
    {{--  <script src="{{ asset('assets/js/dashborad1.js') }}"></script>  --}}
</body>
</html>
