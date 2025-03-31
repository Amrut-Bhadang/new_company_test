
@extends('layouts.web.master')
@section('title',$title)
@section('content')
@php
$userData =  Session::get('AuthUserData') ?? null;
@endphp
<main class="account-page inner_page_space">
    <section class="space-cls">
        <div class="container">
        <div id="overlay">
          <div class="cv-spinner">
            <span class="spinner"></span>
          </div>
        </div>
            <div class="account-page-in">
                @include('layouts.web.include.leftbar_itms')
                <div class="content_sec">
                    <div class="challenges_itms">
                        <div class=" influncer_ajax_llist">
                            @if($data->status == true)
                            @foreach($data->data->court_booking->data as $completed_booking)
                            <div class="challenges_itm">
                                <div class="challenges_itm_img">
                                    <img src="{{$completed_booking->court_image}}">
                                </div>
                                <div class="challenges_itm_con">
                                    <div class="challenges_itm_con_in">
                                        <div class="challenges_itm_title">
                                            <h3><a href="#">{{$completed_booking->court_name}}<span>({{$completed_booking->facility_name ?? ''}})</span></a></h3>
                                        </div>
                                        <!-- <div class="court_contact_dtl">
                                            <span class="call-ic">
                                                <i class="fa fa-phone"></i>
                                            </span>
                                            <span class="phone_no">{{$completed_booking->country_code ?? ''}}-{{$completed_booking->mobile ?? ''}}</span>
                                        </div> -->
                                    <a href="http://maps.google.com/maps?q={{$completed_booking->latitude}},{{$completed_booking->longitude}}" target="_blank">
                                        <div class="challenges_itm_meta">
                                            <div class="court-location">
                                                <span class="court-ic">
                                                    <img src="{{asset('web/images/location-1.png')}}">
                                                </span>
                                                <span class="address">{{$completed_booking->address}}, {{$completed_booking->distance}}</span>
                                            </div>
                                        </div>
                                    </a>
                                        <div class="challenges_itm_meta1">
                                            <div class="challenges_itm_payment">
                                                <div class="challenges_itm_payment_ic"><img src="{{asset('web/images/money-bag.png')}}"></div>
                                                <!-- <span>{{__('backend.'.$completed_booking->payment_type)}}</span> -->
                                                @if($completed_booking->payment_type == 'cash')
                                                    <span>{{__('backend.electronic_payment_Upon_Arrival')}}</span>
                                                @else
                                                    <span>{{__('backend.electronic_payment_Pre_booking')}}</span>
                                                @endif
                                            </div>
                                            <div class="challenges_itm_price">
                                                <h4>{{__('backend.AED')}} {{$completed_booking->hourly_price}}</h4>
                                            </div>
                                        </div>
                                        <div class="challenges_itm_date_time">
                                            <div class="challenges_itm_date">
                                                <div class="challenges_itm_date_ic"><img src="{{asset('web/images/calender.png')}}"></div>
                                                <span>{{isset($completed_booking->booking_date) ? date('d-m-Y',strtotime($completed_booking->booking_date)) :''}}</span>
                                            </div>
                                            @php
                                            if(isset($completed_booking->booking_start_time) && isset($completed_booking->booking_end_time)){
                                            $start = strtotime($completed_booking->booking_start_time);
                                            $end = strtotime($completed_booking->booking_end_time);
                                            $mins = ($end - $start) / 3600;
                                            $difference = round($mins,1);
                                            }
                                            else{
                                            $difference = '';
                                            }
                                            @endphp
                                            <div class="challenges_itm_time">
                                                <div class="challenges_itm_time_cn">{{$difference}}h</div>
                                                <span>{{isset($completed_booking->booking_start_time) ? date('g:i A',strtotime($completed_booking->booking_start_time)) :''}} - {{isset($completed_booking->booking_end_time) ? date('g:i A',strtotime($completed_booking->booking_end_time)) :''}}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="challenges_itm_right">
                                        <a href="{{route('web.court_detail',$completed_booking->court_id)}}" class="btn btn-primary btn-block">{{__('backend.Rebook')}}</a>
                                        @if($completed_booking->is_review == 'no')
                                            <a href="#" class="btn btn-primary btn-block popup_Review" data-order_id='{{$completed_booking->id}}' data-user_id='{{$userData->data->id ??''}}' data-court_id='{{$completed_booking->court_id}}' data-facility_id='{{$completed_booking->facility_id}}'>{{__('backend.Rate_&_Review')}}</a>
                                         @else
                                            <span class="btn btn-primary btn-block" >{{__('backend.Reviewed')}}</span>
                                         @endif   
                                    </div>
                                </div>
                            </div>
                            @endforeach
                            @else
                            <div class="Data_not_found"><img src="{{asset('web/images/Data_not_found.png')}}"></div>
                            @endif
                        </div>
                        @if($data->status == true)
                            @if($data->data->court_booking->current_page != $data->data->court_booking->last_page)
                            <div class="loadmoar-class">
                                <p><a href="javascript:;" class="loadMoareLink">{{__('backend.Load_More')}}</a></p>
                            </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
<!-- verify_otp_model for forgot password -->
<div class="modal fade" id="create_review_model">
<div class="modal-dialog modal-lg">
        <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal"><img src="{{asset('web/images/cross.png')}}"></button>
            <!-- Modal body -->
                <div class="modal-body">
                    <div class="invite_player_content" id="invite_player_content">
                    <h4>{{__('backend.Rate_&_Review')}}</h4>
                    <form class="create_review_form" data-parsley-validate id="create_review_form" method="POST">
                            @csrf
                            <input type="hidden" id="user_id" name="user_id" value="">
                            <input type="hidden" id="court_id" name="court_id" value="">
                            <input type="hidden" id="facility_id" name="facility_id" value="">
                            <input type="hidden" id="order_id" name="order_id" value="">
                            <div class=" input-group form-group custom-input-select">
                                <textarea type="text" id="review" name="review" placeholder="{{__('backend.Review')}}" data-parsley-required="true"  class="form-control cust-control-cls"></textarea>
                            </div>
                            <div class="rating">
                                <input class="star star-5" id="star-5" type="radio" name="rating" value="5"/>
                                <label class="star star-5" for="star-5"></label>

                                <input class="star star-4" id="star-4" type="radio" name="rating" value="4"/>

                                <label class="star star-4" for="star-4"></label>

                                <input class="star star-3" id="star-3" type="radio" name="rating" value="3"/>

                                <label class="star star-3" for="star-3"></label>

                                <input class="star star-2" id="star-2" type="radio" name="rating" value="2"/>

                                <label class="star star-2" for="star-2"></label>

                                <input class="star star-1" id="star-1" type="radio" name="rating" value="1"/>

                                <label class="star star-1" for="star-1"></label>
                            </div>
                            <div class="form-group">
                                <input type="submit" name="submit" value="{{__('backend.Submit')}}" class="btn btn-primary btn-block">
                            </div>
                        </form>
                    </div>
                    <div class="search_player_list" id="search_player_list">
                        
                    </div>
                </div>
        </div>
    </div>
</div>
<script>
    var i = 2;
    $(document).on('click', '.loadMoareLink', function() {
        $("#overlay").fadeIn(30);
        var url = "{{route('completed_booking_pagination')}}?page=" + i;
        $('.preload').show();
        $('.influncer_ajax_llist').append($('<div class="row">').load(url, function() {
            $('.preload').hide();
            i++;
            $("#overlay").fadeOut(30);
        }));
    });
  //  create challenge ajax
  $(document).on('click', '.popup_Review', function(e) {
        e.preventDefault();
        $('#view_response').empty();
        user_id = $(this).attr('data-user_id');
        facility_id = $(this).attr('data-user_id');
        court_id = $(this).attr('data-court_id');
        order_id = $(this).attr('data-order_id');
        $("#user_id").val(user_id);
        $("#facility_id").val(facility_id);
        $("#court_id").val(court_id);
        $("#order_id").val(order_id);
        $('#create_review_model').modal('show');
    });
    //  create review ajax code 
    $(document).ready(function() {
        $('#create_review_form').parsley();

        $(document).on('submit', "#create_review_form", function(e) {
            e.preventDefault();
            var formData = new FormData(this);
                var url = "{{ route('web.create_review')}}";
                $('#group_loader').fadeIn();
                // var values = $('#book_court').serialize();
                $.ajax({
                    url: url,
                    dataType: 'json',
                    data: formData,
                    type: 'POST',
                    cache: false,
                    contentType: false,
                    processData: false,
                    
                    complete: function() {
                        complete(_this)
                    },
                    success: function(result) {
                        if (result.status) {
                        toastr.success(result.message);
                      $('#create_review_model').modal('hide');
                      
                    } else {
                        toastr.error(result.message)
                        $('.save').prop('disabled', false);
                        $('.formloader').css("display", "none");
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
        });
    });
    $(':radio').change(function() {
  console.log('New star rating: ' + this.value);
});
</script>
@endsection