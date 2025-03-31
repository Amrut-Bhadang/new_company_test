<section class="checkout_page space-cls">

	<div class="checkout_page_in">
		<form data-parsley-validate id="join_challenge_checkout_form" method="POST">
			@csrf
			<div class="row checkout_page_row">
				<div class="col-lg-6">
					<div class="checkout_con">
						<h3 class="checkout_heading">{{__('backend.Checkout')}}</h3>
						<div class="row">
							<div class="col-md-12">
								<div class="form-group">
									<label class="mb-3">{{__('backend.Select_Payment_Method')}}</label>
									<div class="radio_custom_itms">
										<div class="radio_custom_itm">{{__('backend.electronic_payment_Pre_booking')}}
											<input type="radio" checked="checked" onchange="change_payment_type('online')" value="online" name="payment_type">
											<span class="checkmark"></span>
										</div>
										<div class="radio_custom_itm">{{__('backend.electronic_payment_Upon_Arrival')}}
											<input type="radio" onchange="change_payment_type('cash')" value="cash" name="payment_type">
											<span class="checkmark"></span>
										</div>
									</div>
								</div>
							</div>
							<!-- <div class="row card_detail" style="display:none;">
								<div class="col-md-12">
									<div class="form-group mb-0">
										<label class="mb-3">{{__('backend.Enter_Card_Detail')}}</label>
									</div>
								</div>
								<div class="col-md-12">
									<div class="form-group">
										<input type="text" name="card_name" placeholder="{{__('backend.Card_Name')}}" class="form-control">
									</div>
								</div>
								<div class="col-md-12">
									<div class="form-group">
										<input type="text" name="card_number" placeholder="{{__('backend.Enter_Card_Number')}}" class="form-control">
									</div>
								</div>
								<div class="col-md-7">
									<div class="form-group">
										<input type="text" name="exp_date" placeholder="{{__('backend.Expiry_Date')}}" class="datepicker form-control">
									</div>
								</div>
								<div class="col-md-5">
									<div class="form-group">
										<input type="text" name="cvv" placeholder="{{__('backend.CVV')}}" class="form-control">
									</div>
								</div>
							</div> -->
							<div class="col-md-12">
								<div class="form-group mb-0">
									<input type="submit" name="" id="submit_button" value="{{__('backend.Pay_Now')}}" class="btn btn-primary">
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-6">
					<div class="checkout_sidebar">
						<div class="checkout_sidebar_head">
							<h3>{{__('backend.Join_Challenge')}}</h3>
						</div>
						<div class="checkout_sidebar_in">
							<div class="checkout_sidebar_itm">
								<span class="checkout_sidebar_lable">{{__('backend.Name')}}:</span><span class="checkout_sidebar_cn">{{$join_challenge_data['court_name'] ?? ''}}</span>
							</div>
							<!-- <div class="checkout_sidebar_itm">
                                    <span class="checkout_sidebar_lable">Artificial1:</span><span class="checkout_sidebar_cn">Full Pitch</span>
                                </div> -->
							<div class="checkout_sidebar_itm">
								<span class="checkout_sidebar_lable">{{__('backend.Booking_Date')}}:</span><span class="checkout_sidebar_cn">{{date('d-m-Y', strtotime($join_challenge_data['booking_date']))?? 'No Date'}}</span>
							</div>
							<div class="checkout_sidebar_itm">
								<span class="checkout_sidebar_lable">{{__('backend.Booking_Time')}}:</span><span class="checkout_sidebar_cn">{{date('g:i',strtotime($join_challenge_data['booking_time_slot']))}} {{__('backend.'.date('A',strtotime($join_challenge_data['booking_time_slot'])))}}</span>
							</div>
						</div>
						<div class="checkout_sidebar_prc_itms">
							<div class="checkout_sidebar_prc_itm">
								<span class="checkout_sidebar_prc_lable">{{__('backend.Total_Amount')}}:</span>
								<span class="checkout_sidebar_price_prc">{{__('backend.AED')}} {{$join_challenge_data['amount'] ?? ''}}</span>
							</div>
						</div>
						<!-- <div class="checkout_sidebar_btn">
                            	<a href="" class="btn btn-primary btn-block">{{__('backend.Edit_Booking')}}</a>
                            </div> -->
					</div>
				</div>
			</div>
		</form>
	</div>

</section>
<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.js"></script>
<script>
	$(document).ready(function() {
		$('.datepicker').datepicker({
			todayBtn: 'linked',
			format: 'yyyy-mm-dd',
			autoclose: true,
		});
	});

	function change_payment_type($booking_type) {
		if ($booking_type == 'online') {
			var valu = "{{__('backend.Pay_Now')}}";
			$('#submit_button').val(valu)
		} else {
			var valu = "{{__('backend.Continue')}}";
			$('#submit_button').val(valu)
		}
	};
	// function change_payment_type($booking_type) {
	// 	if ($booking_type == 'online') {
	// 		console.log($booking_type);
	// 		$('.card_detail').css("display", "contents");
	// 		$('#join_challenge_checkout_form').validate({
	// 			rules: {
	// 				card_name: {
	// 					required: true,
	// 				},
	// 				card_number: {
	// 					required: true,
	// 					digits: true,
	// 				},
	// 				exp_date: {
	// 					required: true,
	// 					date: true,
	// 				},
	// 				cvv: {
	// 					required: true,
	// 				}
	// 			}
	// 		});
	// 	} else {
	// 		console.log($booking_type);
	// 		$('.card_detail').css("display", "none");
	// 	}
	// };
</script>