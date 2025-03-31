@extends('layouts.web.master')
@section('title',$title)
@section('content')
@php
$auth_user = Session::get('AuthUserData');
@endphp
<style>
    .arrow-down {display: block;}
</style>
<main>
    @include('web.category_sec')
    <!-- <section class="about-sec space-cls" id="about-us">
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
                        @if($about_us->status == true)
                        {!!$about_us->data!!}
                        @else
                        {{$about_us->message}}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section> -->
@if(count($data->data->upcoming_match))
    <section class="challenge-sec space-cls">
        <div class="container">
            <div class="row">
                <div class="col-md-12 col-lg-6">
                    <div class="about-img">
                        <img src="{{asset('web/images/foootball-challenge.jpg')}}" alt="About US">
                    </div>
                </div>
                <div class="col-md-12 col-lg-6 bg-cls">
                    <div class="challenge-cont">
                        <div class="inner-title">
                            <h3 class="heading-type-3">{{__('backend.Challenges_With_50_Booking')}}</h3>
                            <div class="title-line">
                                <div class="tl-1"></div>
                                <div class="tl-2"></div>
                                <div class="tl-3"></div>
                            </div>
                        </div>
                        @if($data->status == true)
                         @foreach($data->data->upcoming_match as $key => $upcoming_match)
                        <?php
                            if ($key >= 3) {
                                break;
                            }
                        ?>
                        <div class="single-challenge-booking">
                            <div class="date-sec">
                                <div class="date-inner">
                                    <h3 class="heading-type-3">{{date('d ',strtotime($upcoming_match->booking_date))}} {{__('backend.'.date('M',strtotime($upcoming_match->booking_date)))}}</h3>
                                    <P>{{date('g:i',strtotime($upcoming_match->booking_time_slot[0]->booking_start_time))}} {{__('backend.'.date('A',strtotime($upcoming_match->booking_time_slot[0]->booking_start_time)))}}</P>
                                </div>
                            </div>
                            <div class="challenge_dtl">
                            <a href="http://maps.google.com/maps?q={{$upcoming_match->latitude}},{{$upcoming_match->longitude}}" target="_blank">
                                <div class="address-wrap">
                                    <div class="address-left">
                                        <div class="icon">
                                            <img src="{{asset('web/images/location-1.png')}}">
                                        </div>
                                        <div class="address-dtl">
                                            <h5>{{$upcoming_match->address}}, {{$upcoming_match->destance}}</h5>
                                        </div>
                                    </div>
                                    <!-- <div class="time-wrap">
                                        <h5>{{__('backend.Min_Time_to_Book')}}: </h5>
                                    </div> -->
                                </div>
                            </a>
                                <div class="progress-dtl">
                                    <span class="black-bg"></span>
                                    <span class="gray-bg"></span>
                                </div>
                                <div class="address-wrap">
                                    <div class="profile-left">
                                        <div class="user-img">
                                            <img src="{{$upcoming_match->booking_challenges ? $upcoming_match->booking_challenges[0]->user_details->image : ''}}">
                                        </div>
                                        <div class="user-name">
                                            <h5>{{$upcoming_match->booking_challenges ? $upcoming_match->booking_challenges[0]->user_details->name : ''}}</h5>
                                        </div>
                                    </div>
                                    <div class="time-wrap">
                                        @if($auth_user != null)
                                        <a href="{{route('web.challenges_detail',$upcoming_match->id)}}" type="button" class="btn-white">{{__('backend.AED')}} {{$upcoming_match->paid_amount }}<span class="line-br"></span> <span class="join-cls">{{__('backend.Join_us')}}</span></a>
                                        @else
                                        <button type="button" class="btn-white" data-toggle="modal" data-target="#login_modal">{{__('backend.AED')}} {{$upcoming_match->paid_amount }}<span class="line-br"></span> <span class="join-cls">{{__('backend.Join_us')}}</span></button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif
    <section class="court-sec space-cls">
        <div class="container">
            <div class="inner-title">
                <h2 class="heading-type-2">{{__('backend.Courts')}}</h2>
                <div class="title-line">
                    <div class="tl-1"></div>
                    <div class="tl-2"></div>
                    <div class="tl-3"></div>
                </div>
                <h4 class="sub-head">{{__('backend.web_dashboard_court_section_text')}}</h4>
            </div>
            <div class="slider_type1 owl-carousel">
                @if($data->status == true)
                @foreach($data->data->courts as $courts)
                <div class="item">
                    <div class="court-box slider_type1_item">
                        <div class="court-bg">
                        @if($auth_user != null)
                             <a href="{{route('web.court_detail',$courts->id)}}">  <img src="{{$courts->image}}"></a>
                        @else
                            <a href="javascript:void(0)" data-toggle="modal" data-target="#login_modal">  <img src="{{$courts->image}}"></a>
                        @endif
                            @if(count((array)$courts->available_time_slot) > 0)
                            <div class="available-slot">
                                <span>{{__('backend.Available_Slot')}}: <span class="font-weight">{{__('backend.'.$courts->available_time_slot->day)}}, {{date('g:i',strtotime($courts->available_time_slot->time))}} {{__('backend.'.date('A',strtotime($courts->available_time_slot->time)))}}</span></span>
                            </div>
                            @endif
                        </div>
                        <div class="court-cont court_content">
                            <div class="box-title">
                            @if($auth_user != null)
                                 <a href="{{route('web.court_detail',$courts->id)}}"> <h3>{{$courts->court_name}}<span>({{$courts->facility_name ?? ''}})</span></h3></a>
                            @else
                             <a href="javascript:void(0)" data-toggle="modal" data-target="#login_modal"> <h3>{{$courts->court_name}}<span>({{$courts->facility_name ?? ''}})</span></h3></h3></a>
                             @endif
                                <a onclick="change_to_court_favourate('{{$courts->id}}')" id="courtID{{$courts->id}}" >
                                    <span class='btn btn-xs' >
                                        <span class="fa fa-heart-o"></span>
                                    </span>
                                </a>
                                <div class="court-icon">
                                    <img src="{{$courts->category_image}}">
                                </div>
                            </div>
                            <div class="court_size">
                                <p>{{__('backend.Court_Size')}} - {{$courts->court_size ?? __('backend.Not_Available')}}</p>
                            </div>
                            <div class="price-review-sec">
                                <div class="price-wrap">
                                    <h4>{{__('backend.AED')}} {{$courts->hourly_price}}/{{__('backend.Slot')}}</h4>
                                </div>
                                <div class="review">
                                    <span class="star-ic">
                                        <img src="{{asset('web/images/star.png')}}">
                                    </span>
                                    <span>{{$courts->average_rating}} ({{$courts->total_rating}} {{__('backend.Review')}})</span>
                                </div>
                            </div>
                            <a href="http://maps.google.com/maps?q={{$courts->latitude}},{{$courts->longitude}}" target="_blank">
                            <div class="court-location">
                                <span class="court-ic">
                                    <img src="{{asset('web/images/location-1.png')}}">
                                </span>
                                <div class="address-distance">
                                    <span class="address">{{$courts->address}}</span>
                                    <span class="distance">{{$courts->destance}}</span>
                                </div>
                            </div>
                            </a>
                            <div class="book-now">
                                @if($auth_user != null)
                                    @if(count((array)$courts->available_time_slot) > 0)
                                        <a href="{{url('/court_detail').'/'.$courts->id.'/booknow'}}"> <button class="btn-primary">{{__('backend.Book_Now')}}</button></a>
                                    @else
                                        <a href="{{route('web.court_detail',$courts->id)}}"> <button class="btn-primary">{{__('backend.Book_Now')}}</button></a>
                                    @endif
                                @else
                                    <button type="button" class="btn-primary" data-toggle="modal" data-target="#login_modal">{{__('backend.Book_Now')}}</button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
                @endif
            </div>
            <div class="see-all">
                <a href="{{route('web.court_list')}}"> <button class="btn-primary">{{__('backend.See_All')}} <span class="arrow-ic"><img src="{{asset('web/images/arrow-right.png')}}"></span></button></a>
            </div>
        </div>
    </section>
    <section class="facilitie-sec space-cls">
        <div class="container">
            <div class="inner-title">
                <h2 class="heading-type-2">{{__('backend.Facilities')}}</h2>
                <div class="title-line">
                    <div class="tl-1"></div>
                    <div class="tl-2"></div>
                    <div class="tl-3"></div>
                </div>
                <h4 class="sub-head">{{__('backend.web_dashboard_facility_section_text')}}</h4>
            </div>
            <div class="slider_type1 owl-carousel">
                @if($data->status == true)
                @foreach($data->data->facilities as $facilities)
                    <div class="item">
                        <div class="court-box slider_type1_item">
                            <a href="{{route('web.facility_detail',$facilities->id)}}">
                                <div class="court-bg">
                                    <img src="{{$facilities->image}}">
                                </div>
                            </a>
                            <div class="court-cont court_content">
                                <a href="{{route('web.facility_detail',$facilities->id)}}">
                                    <div class="box-title">
                                        <h3>{{$facilities->name}}</h3>
                                    </div>
                                </a>
                                <div class="available-court">
                                    <span>{{$facilities->available_court}} {{__('backend.Courts_Available')}} </span>
                                    <a onclick="change_to_facility_favourate('{{$facilities->id}}')" id="facilityID{{$facilities->id}}" >
                                        <span class='btn btn-xs' >
                                            <span class="fa fa-heart-o"></span>
                                        </span>
                                    </a>
                                    <span class="courts_icons">
                                        @foreach($facilities->available_category as $available_category)
                                        <img src="{{$available_category->image}}">
                                        @endforeach
                                    </span>
                                </div>
                                <div class="price-review-sec">
                                    <a href="http://maps.google.com/maps?q={{$facilities->latitude}},{{$facilities->longitude}}" target="_blank">
                                        <div class="court-location">
                                            <span class="court-ic">
                                                <img src="{{asset('web/images/location-1.png')}}">
                                            </span>
                                            <div class="address-distance">
                                                <span class="address">{{$facilities->address}}</span>
                                                <span class="distance">{{$facilities->destance}}</span>
                                            </div>
                                        </div>
                                    </a>
                                    <div class="review">
                                        <span class="star-ic">
                                            <img src="{{asset('web/images/star.png')}}">
                                        </span>
                                        <span>{{$facilities->average_rating}} ({{$facilities->total_rating}} {{__('backend.Review')}})</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                @endif
            </div>
            <div class="see-all">
                <a href="{{route('web.facility')}}"> <button class="btn-primary">{{__('backend.See_All')}} <span class="arrow-ic"><img src="{{asset('web/images/arrow-right.png')}}"></span></button></a>
            </div>
        </div>
    </section>
    <section class="testimonial-sec space-cls" style="background-image: url(web/images/testimon_bg.png);">
        <div class="container">
            <div class="testi-inner">
                <div class="challenge-cont">
                    <div class="inner-title">
                        <h3 class="heading-type-3">{{__('backend.Testimonials')}}</h3>
                        <div class="title-line">
                            <div class="tl-1"></div>
                            <div class="tl-2"></div>
                            <div class="tl-3"></div>
                        </div>
                    </div>
                    <div class="quote-ic">
                        <img src="{{asset('web/images/quote.png')}}">
                    </div>
                </div>
                <div class="testi-carousel owl-carousel">
                    <div class="item">
                        <p>{{__('backend.web_testimonials_text1')}}</p>
                        <h4>{{__('backend.web_testimonials_user1')}}</h4>
                    </div>
                    <div class="item">
                        <p>{{__('backend.web_testimonials_text2')}}</p>
                        <h4>{{__('backend.web_testimonials_user2')}}</h4>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection
<script src="{{ asset('js/parsley.min.js') }}"></script>
<script>

function change_to_court_favourate(id){
    var id=id;
    var method= 'court';
    var csrf= '{{csrf_token()}}';
    var url = "{{ route('web.set_to_court_favourate')}}";
    $.ajax({
        type:'post',
        url:url,
        data:{"Courtid":id ,"method":method,"_token":csrf},
        cache:false,
        success:function (response){
           var obj = JSON.parse(response)
            if(obj.status == 'success'){
                $("#courtID"+id).html(obj.html);
            }
           
        }
    });
}

function change_to_facility_favourate(id){
    var id=id;
    var method= 'facilities';
    var csrf= '{{csrf_token()}}';
    var url = "{{ url('set_to_facility_favourate')}}";
    $.ajax({
        type:'post',
        url:url,
        data:{"Facilityid":id ,"method":method,"_token":csrf},
        cache:false,
        success:function (response){
           var obj = JSON.parse(response)
            if(obj.status == 'success'){
                  
                $("#facilityID"+id).html(obj.html);
                
            }
        }
    });
}
</script>