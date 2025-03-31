@extends('layouts.web.master')
@section('title',$title)
@section('content')
@if($data->status == true)
<main>
	@include('web.category_sec')
	<section class="about-sec space-cls static_page">
		<div class="container">
			<div class="row align-items-center">
				<div class="col-md-12">
					<div class="about-cont">
						<div class="inner-title">
							<h2 class="heading-type-2">{{__('backend.How_it_works')}}</h2>
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