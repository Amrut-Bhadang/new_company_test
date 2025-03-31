@extends('layouts.web.master')
@section('title',$title)
@section('content')
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
                            @foreach($data->data->court_booking->data as $cancelled_booking)
                            <div class="challenges_itm">
                                <div class="challenges_itm_img">
                                    <img src="{{$cancelled_booking->court_image}}">
                                </div>
                                <div class="challenges_itm_con">
                                    <div class="challenges_itm_con_in">
                                        <div class="challenges_itm_title">
                                            <h3><a href="#">{{$cancelled_booking->court_name}}<span>({{$cancelled_booking->facility_name ?? ''}})</span></a></h3>
                                        </div>
                                        <!-- <div class="court_contact_dtl">
                                            <span class="call-ic">
                                                <i class="fa fa-phone"></i>
                                            </span>
                                            <span class="phone_no">{{$cancelled_booking->country_code ?? ''}}-{{$cancelled_booking->mobile ?? ''}}</span>
                                        </div> -->
                                    <a href="http://maps.google.com/maps?q={{$cancelled_booking->latitude}},{{$cancelled_booking->longitude}}" target="_blank">
                                        <div class="challenges_itm_meta">
                                            <div class="court-location">
                                                <span class="court-ic">
                                                    <img src="{{asset('web/images/location-1.png')}}">
                                                </span>
                                                <span class="address">{{$cancelled_booking->address}}, {{$cancelled_booking->distance}}</span>
                                            </div>
                                        </div>
                                    </a>
                                        <div class="challenges_itm_meta1">
                                            <div class="challenges_itm_payment">
                                                <div class="challenges_itm_payment_ic"><img src="{{asset('web/images/money-bag.png')}}"></div>
                                                <!-- <span>{{__('backend.'.$cancelled_booking->payment_type)}}</span> -->
                                                @if($cancelled_booking->payment_type == 'cash')
                                                    <span>{{__('backend.electronic_payment_Upon_Arrival')}}</span>
                                                @else
                                                    <span>{{__('backend.electronic_payment_Pre_booking')}}</span>
                                                @endif
                                            </div>
                                            <div class="challenges_itm_price">
                                                <h4>{{__('backend.AED')}} {{$cancelled_booking->hourly_price}}</h4>
                                            </div>
                                        </div>
                                        <div class="challenges_itm_date_time">
                                            <div class="challenges_itm_date">
                                               
                                               {{__('backend.Cancelled_Booking')}}
                                            </div>
                                            
                                            <div class="challenges_itm_time">
                                                <div class="challenges_itm_date_ic"><img src="{{asset('web/images/calender.png')}}"></div>
                                                    <span>{{isset($cancelled_booking->booking_date) ? date('d-m-Y H:i:s',strtotime($cancelled_booking->updated_at)) :''}}</span>
                                            </div>
                                        </div>
                                        <div class="challenges_itm_date_time">
                                            <div class="challenges_itm_date">
                                                <div class="challenges_itm_date_ic"><img src="{{asset('web/images/calender.png')}}"></div>
                                                <span>{{isset($cancelled_booking->booking_date) ? date('d-m-Y',strtotime($cancelled_booking->booking_date)) :''}}</span>
                                            </div>
                                            @php
                                            if(isset($cancelled_booking->booking_start_time) && isset($cancelled_booking->booking_end_time)){
                                            $start = strtotime($cancelled_booking->booking_start_time);
                                            $end = strtotime($cancelled_booking->booking_end_time);
                                            $mins = ($end - $start) / 3600;
                                            $difference = round($mins,1);
                                            }
                                            else{
                                            $difference = '';
                                            }
                                            @endphp
                                            <div class="challenges_itm_time">
                                                <div class="challenges_itm_time_cn">{{$difference}}h</div>
                                                <span>{{isset($cancelled_booking->booking_start_time) ? date('g:i A',strtotime($cancelled_booking->booking_start_time)) :''}} - {{isset($cancelled_booking->booking_end_time) ? date('g:i A',strtotime($cancelled_booking->booking_end_time)) :''}}</span>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="challenges_itm_right">
                                        <a href="{{route('web.court_detail',$cancelled_booking->court_id)}}" class="btn btn-primary btn-block">{{__('backend.Rebook')}}</a>
                                        <span class="btn btn-primary btn-block">{{__('backend.Cancelled_Booking')}}</span>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                            @else
                            <div class="Data_not_found"><img src="{{asset('web/images/Data_not_found.png')}}"></div>
                            @endif
                            @if($data->status == true)
                           
                        </div>
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
<script>
    var i = 2;
    $(document).on('click', '.loadMoareLink', function() {
        $("#overlay").fadeIn(30);
        var url = "{{route('cancelled_booking_pagination')}}?page=" + i;
        $('.preload').show();
        $('.influncer_ajax_llist').append($('<div class="row">').load(url, function() {
            $('.preload').hide();
            i++;
            $("#overlay").fadeOut(30);
        }));
    });
</script>
@endsection