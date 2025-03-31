@extends('layouts.web.master')
@section('title',$title)
@section('content')
<main>
	@include('web.category_sec')
	<section class="contact-sec space-cls">
		<div class="container">
			<div class="inner-title">
				<h2 class="heading-type-2">{{__('backend.Contact_us')}}</h2>
				<div class="title-line">
					<div class="tl-1"></div>
					<div class="tl-2"></div>
					<div class="tl-3"></div>
				</div>
			</div>

			<div class="contact_in">
				<div class="row">
					<div class="col-md-6">
						<div class="cobtact_frm">
						<form method="POST" action="" enctype="" id="add_contact">
							@csrf
							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<input type="text" name="name" placeholder="{{__('backend.name')}}" data-parsley-required="true" data-parsley-minlength="3" data-parsley-pattern="^[A-Za-z ]+$" data-parsley-pattern-message="{{__('backend.validation_only_alpha_space')}}" class="form-control">
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<input type="text" name="email" placeholder="{{__('backend.email')}}" autocomplete="off" data-parsley-required="true" data-parsley-pattern="^[a-z0-9][-a-z0-9._]+@([-a-z0-9]+[.])+[a-z]{2,5}$" data-parsley-pattern-message="{{__('backend.validation_email_format')}}" class="form-control">
									</div>
								</div>
								<div class="col-md-12">
									<div class="input-group form-group">
										<select name="country_code" class="form-control" data-parsley-required="true">
											@foreach ($country as $country)
											<option value="{{ $country->phonecode }}" {{ '+971' == $country->phonecode?'selected':'' }}>{{ $country->sortname }} {{ $country->phonecode }}</option>
											@endforeach
										</select>
										<input type="text" id="mobile" name="mobile" placeholder="{{ __('backend.Mobile') }}" value="" class="form-control form-control-line" data-parsley-required="true" data-parsley-pattern="^[0-9 ]{8,15}$" data-parsley-pattern-message="{{__('backend.validation_mobile_number')}}">
									</div>
								</div>
								<div class="col-md-12">
									<div class="form-group">
										<textarea name="message" class="form-control" rows="7" data-parsley-required="true" data-parsley-minlength="5" placeholder="{{__('backend.Message')}}"></textarea>
									</div>
								</div>
								<div class="col-md-12">
									<div class="form-group mb-0 submit_btn">
										<input type="submit" name="" value="{{__('backend.Submit')}}" class="btn btn-primary">
									</div>
								</div>
							</div>
						</form>
						</div>
					</div>
					<div class="col-md-6">
						<div class="contact_itms">
						<div class="contact_itm">
								<div class="contact_itm_ic"><i class="fa fa-building"></i></div>
								<div class="contact_itm_cn">
									<h5>{{__('backend.Company')}}</h5>
									<h3>{{ __('backend.company_name') }}</h3>
								</div>
							</div>
							<div class="contact_itm">
								<div class="contact_itm_ic"><i class="fa fa-phone"></i></div>
								<div class="contact_itm_cn">
									<h5>{{__('backend.Mobile_Number')}}</h5>
									<h3>+971 52 379 2371</h3>
								</div>
							</div>
							<div class="contact_itm">
								<div class="contact_itm_ic"><i class="fa fa-envelope"></i></div>
								<div class="contact_itm_cn">
									<h5>{{__('backend.Email_Address')}}</h5>
									<h3>Tahadiyaat@gmail.com</h3>
								</div>
							</div>
							<div class="contact_itm">
								<div class="contact_itm_ic"><i class="fa fa-map-marker"></i></div>
								<div class="contact_itm_cn">
									<h5>{{__('backend.Address')}}</h5>
									<h3>Abu Dhabi - United Arab Emirates</h3>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			
		</div>
	</section>

	<section class="map_sec">
	<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d232565.94888969001!2d54.41853682479946!3d24.386766151661785!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3e5e440f723ef2b9%3A0xc7cc2e9341971108!2sAbu%20Dhabi%20-%20United%20Arab%20Emirates!5e0!3m2!1sen!2sin!4v1655732700974!5m2!1sen!2sin"  style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
	</section>
</main>
<script src="{{ asset('js/parsley.min.js') }}"></script>
<script>
	$('#add_contact').parsley();
	$(document).on('submit', "#add_contact", function(e) {
		e.preventDefault();
		var _this = $(this);
		$('#group_loader').fadeIn();
		var formData = new FormData(this);
		$.ajax({
			url: '{{ route("web.admin.contact") }}',
			dataType: 'json',
			data: formData,
			type: 'POST',
			cache: false,
			contentType: false,
			processData: false,
			complete: function() {
				complete(_this)
			},
			success: function(res) {
				if (res.status === 1) {
					toastr.success(res.message);
					$('#add_contact')[0].reset();
					$('#add_contact').parsley().reset();
				} else {
					toastr.error(res.message);
				}
			},
			error: function(jqXHR, textStatus, textStatus) {
				if (jqXHR.responseJSON.errors) {
					$.each(jqXHR.responseJSON.errors, function(index, value) {
						toastr.error(value)
					});
				} else {
					toastr.error(jqXHR.responseJSON.message)
				}
			}
		});
		return false;
	});
</script>
@endsection