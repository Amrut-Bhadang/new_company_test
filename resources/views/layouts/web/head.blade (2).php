<link href="{{URL::asset('web/css/bootstrap.css')}}" rel="stylesheet">
<link href="{{URL::asset('web/css/owl.carousel.min.css')}}" type="text/css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="{{URL::asset('web/css/daterangepicker.css')}}" />
<link href="{{URL::asset('web/css/custom.css')}}" type="text/css" rel="stylesheet">
<link href="{{URL::asset('web/css/responsive.css')}}" type="text/css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<script type="text/javascript" src="{{ asset('web/js/moment.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('web/js/jquery.min.js')}}"></script>
<link rel="stylesheet" href="{{ asset('plugins/toastr/toastr.min.css') }}">
<?php if (app()->getLocale() == 'ar') { ?>
	<link href="{{ URL::asset('web/css/rtl_responsive.css')}}" rel="stylesheet">
<?php } ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/js/bootstrap-datepicker.js"></script>