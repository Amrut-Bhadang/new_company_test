@yield('css')
<link type="text/css" rel="stylesheet"  href="{{ URL::asset('assets/jsgrid/jsgrid.min.css')}}" />
<link type="text/css" rel="stylesheet" href="{{ URL::asset('assets/jsgrid/jsgrid-theme.min.css')}}" />
<link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/custom.css') }}">

<link rel="stylesheet" href="{{ asset('plugins/toastr/toastr.min.css') }}">
<script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
<!-- <script src="{{ asset('dist/js/jquery-1.11.1.min.js') }}"></script> -->
<script src="{{ asset('plugins/moment/moment.min.js') }}"></script>
<script src="{{ asset('plugins/pace-progress/pace.min.js') }}"></script>
<!-- Custom CSS -->

<link href="{{ URL::asset('dist/css/style.min.css')}}" rel="stylesheet">

<?php if (app()->getLocale() == 'ar') { ?>
	<link href="{{ URL::asset('dist/css/style-rtl.min.css')}}" rel="stylesheet">
<?php } ?>
<link href="{{ URL::asset('dist/css/bootstrap-tagsinput.css')}}" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('plugins/toastr/toastr.min.css') }}">

<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.css"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>