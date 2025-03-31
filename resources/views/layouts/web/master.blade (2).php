<!DOCTYPE html>
<html dir="{{app()->getLocale()=='ar'?'rtl':''}}" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ URL::asset('assets/images/favicon.ico')}}">
    <title> @yield('title') {{ __('backend.'.config('app.name'))}}</title>
    @include('layouts.web..head')
</head>

<body>
    <!-- @include('layouts.web..header') -->
    @include('layouts.web.header')
    @yield('content')
    @include('layouts.web.footer')
    <!-- The Modal -->
    @include('layouts.web.footer-script')
    @include('layouts.web.include.model')
</body>

</html>