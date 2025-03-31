@extends('layouts.web.master')
@section('title',$title)
@section('content')
@php
$auth_user = Session::get('AuthUserData');
@endphp
@if($data->status == true)
<main class="facilities-dtl-page inner_page_space">
    <section class="space-cls">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="facilities-dtl-img">
                        <img src="{{$data->data->image}}">
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="facilities-dtl-cont facilitiy_detail">
                        <div class="box-title">
                            <h3>{{$data->data->name}}</h3>
                            <!-- <div class="court-icon">
                                <img src="{{asset('web/images/share.png')}}">
                            </div> -->


                            <div class="share-cls">
                                <div class="share-icon dropdown">
                                    <?php $name = ucwords(str_replace("'", "", $data->data->name)); ?>
                                    <a href="javascript:;" class="nav-link dropdown-toggle" id="navbarDropdownShare" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                        <div class="court-icon">
                                            <img src="{{asset('web/images/share.png')}}">
                                        </div>
                                    </a>
                                    <div class="dropdown-menu share-dropdown" aria-labelledby="navbarDropdownShare">
                                        <ul>
                                            <li class="facebook">
                                                <a href="javascript:;" onclick="window.open('https://facebook.com/sharer.php?u={{route('web.facility_detail',['id'=>$data->data->id])}}&quote={!! $name !!}')">
                                                    <img src="{{ URL::asset('web/images/facebook.png')}}"> {{__('backend.Facebook')}}
                                                </a>
                                            </li>
                                            <hr />
                                            <li class="twiter">
                                                <a href="javascript:;" onclick="window.open('https://twitter.com/share?url={{route('web.facility_detail',['id'=>$data->data->id])}}&text={!! $name !!}&via=Iseehat&hashtags=buyonIseehat')">
                                                    <img src="{{ URL::asset('web/images/twitter.png')}}"> {{__('backend.Twitter')}}
                                                </a>
                                            </li>
                                            <hr />
                                            <?php /*
                                        <li class="insta">
                                        <a href="mailto:%20?subject={!!  ucwords($productDetail->data->name) !!}&body={!!  ucwords($productDetail->data->name) !!}{!!  ucwords( strip_tags(str_replace('|','',$productDetail->data->long_description))) !!}{{route('web.facility_detail',['id'=>$data->data->id])}}">
                                                <img src="{{ URL::asset('web/images/insta.png')}}"> Instagram
                                            </a>
                                        </li> */ ?>
                                            <li class="whatup">
                                                <a href="javascript:;" onclick="window.open('https://api.whatsapp.com/send?text={{route('web.facility_detail',['id'=>$data->data->id])}}')">
                                                    <img src="{{ URL::asset('web/images/whatup.png')}}">
                                                    {{__('backend.whatsapp')}}
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="price-review-sec">
                            <div class="review">
                                <span class="star-ic">
                                    <img src="{{asset('web/images/star.png')}}">
                                </span>
                                <span>{{$data->data->average_rating}} ({{$data->data->total_rating}} {{__('backend.Review')}})</span>
                            </div>
                        </div>
                        <a href="http://maps.google.com/maps?q={{$data->data->latitude}},{{$data->data->longitude}}" target="_blank">
                            <div class="court-location">
                                <span class="court-ic">
                                    <img src="{{asset('web/images/location-1.png')}}">
                                </span>
                                <div class="address-distance">
                                    <span class="address">{{$data->data->address}}</span>
                                    <span class="distance">{{$data->data->distance}}</span>
                                </div>
                            </div>
                        </a>
                        <div class="available-court">
                            <span>{{count($data->data->court_list)}} {{__('backend.Courts_Available')}}</span>
                            <span class="courts_icons">
                                @foreach($data->data->facility_category as $category)
                                <img src="{{$category->category_details->image}}">
                                @endforeach
                            </span>
                        </div>
                        <div class="amenities-cls">
                            <div class="ame-title">
                                <h4>{{__('backend.Amenities')}}</h4>
                            </div>
                            <div class="ameni-row">
                                @foreach($data->data->amenities as $amenity)
                                <div class="ameni-col">
                                    <div class="ameni-box">
                                        <div class="ameni-ic">
                                            <img src="{{$amenity->image}}">
                                        </div>
                                        <div class="ameni-name">
                                            <h5>{{$amenity->name}}</h5>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="rules-cls">
                            <div class="ame-title">
                                <h4>{{__('backend.Rules')}}</h4>
                            </div>
                            <div class="ameni-row">
                                <div class="rules-col">
                                    <ul>
                                        @foreach($data->data->facility_rules as $rule)
                                        <li>
                                            <span class="tick-ic"><img src="{{asset('web/images/tick.png')}}"></span> {{$rule->rules}}
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="court-sec space-cls">
        <div class="container">
            <div class="inner-title">
                <h2 class="heading-type-2">{{__('backend.Courts')}}</h2>
                <div class="title-line">
                    <div class="tl-1"></div>
                    <div class="tl-2"></div>
                    <div class="tl-3"></div>
                </div>
            </div>
            <div class="slider_type1 owl-carousel">
                @foreach($data->data->court_list as $court)
                <div class="item">
                    <div class="court-box slider_type1_item">
                        <div class="court-bg">
                        @if($auth_user != null)
                            <a href="{{route('web.court_detail',$court->id)}}"> <img src="{{$court->image}}"></a>
                        @else
                            <a href="javascript:void(0)" data-toggle="modal" data-target="#login_modal"> <img src="{{$court->image}}"></a>
                        @endif
                            @if(count((array)$court->available_time_slot) > 0)
                            <div class="available-slot">
                                <span>{{__('backend.Available_Slot')}} : <span class="font-weight">{{__('backend.'.$court->available_time_slot->day)}}, {{date('g:i',strtotime($court->available_time_slot->time))}} {{__('backend.'.date('A',strtotime($court->available_time_slot->time)))}}</span></span>
                            </div>
                            @endif
                        </div>
                        <div class="court-cont court_content">
                            <div class="box-title">
                            @if($auth_user != null)
                                <a href="{{route('web.court_detail',$court->id)}}"><h3>{{$court->court_name}}<span>({{$court->facility_name ?? ''}})</h3></a>
                            @else
                            <a href="javascript:void(0)" data-toggle="modal" data-target="#login_modal"><h3>{{$court->court_name}}<span>({{$court->facility_name ?? ''}})</h3></a>
                            @endif
                                <div class="court-icon">
                                    <img src="{{$court->category_image}}">
                                </div>
                            </div>
                            <!-- <div class="court_contact_dtl">
                                <span class="call-ic">
                                    <i class="fa fa-phone"></i>
                                </span>
                                <span class="phone_no">{{$court->country_code ?? ''}}-{{$court->mobile ?? ''}}</span>
                            </div> -->
                            <div class="price-review-sec">
                                <div class="price-wrap">
                                    <h4>{{__('backend.AED')}} {{$court->hourly_price}}/{{__('backend.Slot')}}</h4>
                                </div>
                                <div class="review">
                                    <span class="star-ic">
                                        <img src="{{asset('web/images/star.png')}}">
                                    </span>
                                    <span>{{$court->average_rating}} ({{$court->total_rating}} {{__('backend.Review')}})</span>
                                </div>
                            </div>
                            <a href="http://maps.google.com/maps?q={{$court->latitude}},{{$court->longitude}}" target="_blank">
                                <div class="court-location">
                                    <span class="court-ic">
                                        <img src="{{asset('web/images/location-1.png')}}">
                                    </span>
                                    <div class="address-distance">
                                        <span class="address">{{$court->address}}</span>
                                        <span class="distance">{{$court->distance}}</span>
                                    </div>
                                </div>
                            </a>

                            <div class="book-now">
                                @if($auth_user != null)
                                @if(count((array)$court->available_time_slot) > 0)
                                <a href="{{url('/court_detail').'/'.$court->id.'/booknow'}}"> <button class="btn-primary">{{__('backend.Book_Now')}}</button></a>
                                @else
                                <a href="{{route('web.court_detail',$court->id)}}"> <button class="btn-primary">{{__('backend.Book_Now')}}</button></a>
                                @endif
                                @else
                                <button type="button" class="btn-primary" data-toggle="modal" data-target="#login_modal">{{__('backend.Book_Now')}}</button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="see-all">
                <a href="{{route('web.court_list')}}?facility={{$data->data->id}}"> <button class="btn-primary">{{__('backend.See_All')}} <span class="arrow-ic"><img src="{{asset('web/images/arrow-right.png')}}"></span></button></a>
            </div>
        </div>
    </section>
</main>
@else
{{$data->message}}
@endif
@endsection