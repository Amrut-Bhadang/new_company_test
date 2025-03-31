@extends('layouts.web.master')
@section('title',$title)
@section('content')
@php
$auth_user = Session::get('AuthUserData');
@endphp
@if($data->status == true)
<style>
    .arrow-down {display: block;}
</style>
<main>
	@include('web.category_sec')
	<section class="about-sec space-cls static_page" id="about-us">
		<div class="container">
			<div class="row align-items-center">
				<div class="col-md-6">
					<div class="about-img">
						<img src="{{asset('web/images/about-img.png')}}" alt="About US">
					</div>
				</div>
				<div class="col-md-6">
					<div class="about-cont">
						<div class="inner-title">
							<h2 class="heading-type-2">{{__('backend.About_US')}}</h2>
							<div class="title-line">
								<div class="tl-1"></div>
								<div class="tl-2"></div>
								<div class="tl-3"></div>
							</div>
						</div>
						@if($data->status == true)
						{!!$data->data!!}
						@else
						{{$data->message}}
						@endif
					</div>

				</div>
			</div>
		</div>
	</section>
</main>
@else
{{$data->message}}
@endif
@endsection