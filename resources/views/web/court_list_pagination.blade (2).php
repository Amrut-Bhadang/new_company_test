@php
$auth_user = Session::get('AuthUserData');
@endphp
@if(isset($data->status) && $data!='' )
        @if($data->status == true)
            @foreach($data->data->court->data as $court)
            <div class="single-court">
                <div class="court-img">
                <!-- <a href="{{route('web.court_detail',$court->id)}}">  <img src="{{$court->image}}"></a> -->
                @if($auth_user != null)
                    <a href="{{route('web.court_detail',$court->id)}}">  <img src="{{$court->image}}"></a>
                @else
                    <a href="javascript:void(0)" data-toggle="modal" data-target="#login_modal">  <img src="{{$court->image}}"></a>
                @endif
                </div>
                <div class="courtpage-cont">
                    <div class="court-cont-inner">
                        <div class="court-cont">
                            <div class="box-title">
                            @if($auth_user != null)
                                <a href="{{route('web.court_detail',$court->id)}}"> <h3>{{$court->court_name}}<span>({{$court->facility_name ?? ''}})</span></h3></a>
                            @else
                                <a href="javascript:void(0)" data-toggle="modal" data-target="#login_modal"> <h3>{{$court->court_name}}<span>({{$court->facility_name ?? ''}})</span></h3></a>
                            @endif
                            </div>
                            <!-- <div class="court_contact_dtl">
                                <span class="call-ic">
                                    <i class="fa fa-phone"></i>
                                </span>
                                <span class="phone_no">{{$court->country_code ?? ''}}-{{$court->mobile ?? ''}}</span>
                            </div> -->
                            <div class="price-review-sec">
                                <div class="review">
                                    <span class="star-ic">
                                        <img src="{{asset('web/images/star.png')}}">
                                    </span>
                                    <span>{{$court->average_rating}} ({{$court->total_rating}} {{__('backend.Review')}})</span>
                                </div>
                                @if(isset($court->court_size))
                                    <div class="court_size">
                                        <p>{{__('backend.Court_Size')}} - {{$court->court_size ?? ''}}</p>
                                    </div>
                                @endif
                            </div>
                        <a href="http://maps.google.com/maps?q={{$court->latitude}},{{$court->longitude}}" target="_blank">
                            <div class="court-location">
                                <span class="court-ic">
                                    <img src="{{asset('web/images/location-1.png')}}">
                                </span>
                                <div class="address-distance ">
                                    <span class="address">{{$court->address}}</span>
                                    <span class="distance">{{$court->distance}}</span>
                                </div>
                            </div>
                        </a>
                        @if(count((array)$court->available_time_slot) > 0)
                            <div class="available-slot">
                            <span>{{__('backend.Available_Slot')}}: <span class="font-weight">{{__('backend.'.$court->available_time_slot->day)}}, {{date('g:i',strtotime($court->available_time_slot->time))}} {{__('backend.'.date('A',strtotime($court->available_time_slot->time)))}}</span></span>
                            </div>
                        @endif
                        </div>
                    </div>
                    <div class="court-price-inner">
                        <div class="box-title">
                            <div class="court-icon">
                                <img src="{{$court->category_image}}">
                            </div>
                        </div>
                        <div class="price-wrap">
                            <h4>{{__('backend.AED')}} {{$court->hourly_price}}/{{__('backend.Slot')}}</h4>
                        </div>
                        <div class="book-now">
                            @if($auth_user != null)
                                @if(count((array)$court->available_time_slot) > 0)
                                    <a href="{{url('/court_detail').'/'.$court->id.'/booknow'}}"> <button type="button" class="btn-primary">{{__('backend.Book_Now')}}</button></a>
                                @else
                                    <a href="{{route('web.court_detail',$court->id)}}"> <button type="button" class="btn-primary">{{__('backend.Book_Now')}}</button></a>
                                @endif
                            @else
                                <button type="button" class="btn-primary" data-toggle="modal" data-target="#login_modal">{{__('backend.Book_Now')}}</button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        @else
   
        @endif
        @if($data->status == 'true')
            @if($data->data->court->current_page == $data->data->court->last_page)
                <style>
                    .loadmoar-class
                    {
                        display: none !important;
                    }

                </style>
            @endif
        @else
                <style>
                .loadmoar-class
                {
                    display: none !important;
                }

            </style>
        @endif
@endif