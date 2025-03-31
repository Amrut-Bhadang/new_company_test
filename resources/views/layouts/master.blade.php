<!DOCTYPE html>
<html dir="{{app()->getLocale()=='ar'?'rtl':''}}" lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>{{ (isset($page_title)?$page_title.' - ':'').config('app.name', 'Laravel') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="shortcut icon" href="{{ URL::asset('assets/images/favicon.ico')}}">
    @include('layouts.head')
</head>
<style>
    .pac-container {
        z-index: 10000 !important;
    }
</style>

<body class="skin-red-dark fixed-layout lock-nav">
    <div id="main-wrapper">
        @include('layouts.topbar')
        @include('layouts.sidebar')

        <div class="container-fluid">
            @include('layouts.errors')
            <div class="page-wrapper">
                <!-- ============================================================== -->
                <!-- Container fluid  -->
                <!-- ============================================================== -->
                <div class="container-fluid">
                    @yield('content')
                </div>
            </div>
        </div>
        <footer class="footer">
            @include('layouts.footer')
        </footer>
    </div>
    @include('layouts.footer-script')
</body>

</html>