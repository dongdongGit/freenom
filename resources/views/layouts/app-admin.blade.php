<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    

    <!-- Fonts -->
    {{--  <link rel="dns-prefetch" href="//fonts.gstatic.com">  --}}
    {{--  <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">  --}}

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/fonts/line-icons.css') }}">

    {{--  <link rel="stylesheet" href="{{ asset('assets/plugins/morris/morris.css') }}">  --}}

    {{--  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/main.css') }}">  --}}

    {{--  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/responsive.css') }}">  --}}
</head>
<body>
    <div class="app header-default side-nav-dark" id="app">
        <div class="layout">
            @yield('content')
        </div>
    </div>
    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
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
