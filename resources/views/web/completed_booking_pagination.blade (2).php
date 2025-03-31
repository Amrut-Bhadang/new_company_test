@php
$auth_user = Session::get('AuthUserData');
@endphp
<div class="col-12">
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
                    <a href="#" class="btn btn-primary btn-block">{{__('backend.Rate_&_Review')}}</a>
                </div>
            </div>
        </div>
        @endforeach
        @else
        {{$data->message}}
        @endif
    @if($data->data->court_booking->current_page == $data->data->court_booking->last_page)
<style>
    .loadmoar-class
    {
        display: none !important;
    }
</style>
    @endif
</div>
