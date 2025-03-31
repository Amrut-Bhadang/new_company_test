@php
$auth_user = Session::get('AuthUserData');
@endphp
<div class="col-12">
@if($data->status == true)
    @foreach($data->data->Join_challenge->data as $Join_challenge)
    <div class="challenges_itm">
        <div class="challenges_itm_img">
            <img src="{{$Join_challenge->court_image}}">
            @if($Join_challenge->challenge_type == 'private')
                <div class="available-slot">
                    <span>{{__('backend.Private_Booking')}}</span>
                </div>
            @endif
        </div>
        <div class="challenges_itm_con">
            <div class="challenges_itm_con_in">
                <div class="challenges_itm_title">
                    <h3><a href="#">{{$Join_challenge->court_name}}<span>({{$Join_challenge->facility_name ?? ''}})</span></a></h3>
                </div>
                <!-- <div class="court_contact_dtl">
                    <span class="call-ic">
                        <i class="fa fa-phone"></i>
                    </span>
                    <span class="phone_no">{{$Join_challenge->country_code ?? ''}}-{{$Join_challenge->mobile ?? ''}}</span>
                </div> -->
                <div class="challenges_itm_meta">
                    <div class="opponent_s">{{__('backend.Opponent')}}: {{$Join_challenge->user_name}}</div>
                    <a href="http://maps.google.com/maps?q={{$Join_challenge->latitude}},{{$Join_challenge->longitude}}" target="_blank">
                        <div class="court-location">
                            <span class="court-ic">
                                <img src="{{asset('web/images/location-1.png')}}">
                            </span>
                            <span class="address">{{$Join_challenge->address}}, {{$Join_challenge->distance}}</span>
                        </div>
                    </a>
                </div>
                <div class="challenges_itm_meta1">
                    <div class="challenges_itm_payment">
                        <div class="challenges_itm_payment_ic"><img src="{{asset('web/images/money-bag.png')}}"></div>
                        <!-- <span>{{__('backend.'.$Join_challenge->payment_type)}}</span> -->
                        @if($Join_challenge->payment_type == 'cash')
                            <span>{{__('backend.electronic_payment_Upon_Arrival')}}</span>
                        @else
                            <span>{{__('backend.electronic_payment_Pre_booking')}}</span>
                        @endif
                    </div>
                    <div class="challenges_itm_price">
                        <h4>{{__('backend.AED')}} {{$Join_challenge->total_amount}} <span>({{__('backend.50_Booking')}})</span></h4>
                    </div>
                </div>
                <div class="challenges_itm_date_time">
                    <div class="challenges_itm_date">
                        <div class="challenges_itm_date_ic"><img src="{{asset('web/images/calender.png')}}"></div>
                        <span>{{isset($Join_challenge->booking_date) ? date('d-m-Y',strtotime($Join_challenge->booking_date)) :''}}</span>
                    </div>
                    @php
                    if(isset($Join_challenge->booking_start_time) && isset($Join_challenge->booking_end_time)){
                    $start = strtotime($Join_challenge->booking_start_time);
                    $end = strtotime($Join_challenge->booking_end_time);
                    $mins = ($end - $start) / 3600;
                    $difference = round($mins,1);
                    }
                    else{
                    $difference = '';
                    }
                    @endphp
                    <div class="challenges_itm_time">
                        <div class="challenges_itm_time_cn">{{$difference}}h</div>
                        <span>{{isset($Join_challenge->booking_start_time) ? date('g:i A',strtotime($Join_challenge->booking_start_time)) :''}} - {{isset($Join_challenge->booking_end_time) ? date('g:i A',strtotime($Join_challenge->booking_end_time)) :''}}</span>
                    </div>
                </div>
            </div>
            <div class="challenges_itm_right">
                @if($Join_challenge->challenge_type == 'private')
                <!-- <a href="#" class="invite_friens_btn invite_player" data-challenge-id ={{$Join_challenge->id}}>
                    <img src="{{asset('web/images/invite.png')}}">
                    <span>{{__('backend.Invite_Player')}}</span>
                </a> -->
                @else
                <!-- <a href="#" class="invite_friens_btn" data-challenge-id ={{$Join_challenge->id}}>
                    <img src="{{asset('web/images/share.png')}}">
                    <span>{{__('backend.Share')}}</span>
                </a> -->
                <div class="share-cls">
                    <div class="share-icon dropdown">
                        <?php $name = ucwords(str_replace("'","",$Join_challenge->court_name)); ?>
                        <a href="javascript:;" class="nav-link dropdown-toggle invite_friens_btn" id="navbarDropdownShare" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" data-challenge-id ={{$Join_challenge->id}}>
                            <img src="{{asset('web/images/share.png')}}">
                            <span>{{__('backend.Share')}}</span>
                        </a>
                        <div class="dropdown-menu share-dropdown" aria-labelledby="navbarDropdownShare">
                            <ul>
                            <li class="facebook">      
                                <a href="javascript:;" onclick="window.open('https://facebook.com/sharer.php?u={{route('web.challenges')}}&quote={!! $name !!}')">
                                    <img src="{{ URL::asset('web/images/facebook.png')}}"> {{__('backend.Facebook')}}
                                </a>
                            </li>
                            <hr/>
                            <li class="twiter">
                                <a href="javascript:;" onclick="window.open('https://twitter.com/share?url={{route('web.challenges')}}&text={!! $name !!}&via=Iseehat&hashtags=buyonIseehat')">
                                    <img src="{{ URL::asset('web/images/twitter.png')}}"> {{__('backend.Twitter')}}
                                </a>
                            </li>
                            <hr/>
                            <?php /*
                            <li class="insta">
                            <a href="mailto:%20?subject={!!  ucwords($productDetail->data->name) !!}&body={!!  ucwords($productDetail->data->name) !!}{!!  ucwords( strip_tags(str_replace('|','',$productDetail->data->long_description))) !!}{{route('web.challenges'a->id])}}">
                                    <img src="{{ URL::asset('web/images/insta.png')}}"> Instagram
                                </a>
                            </li> */?>
                            <li class="whatup">
                                <a href="javascript:;" onclick="window.open('https://api.whatsapp.com/send?text={{route('web.challenges')}}')">
                                    <img src="{{ URL::asset('web/images/whatup.png')}}"> 
                                    {{__('backend.whatsapp')}}
                                </a>
                            </li>
                            </ul>
                        </div>
                    </div>
                </div>
                @endif
                @if($Join_challenge->is_challenge == false)
                <a href="{{route('web.challenges_detail',$Join_challenge->id)}}" class="btn btn-primary btn-block">{{__('backend.Join_Challenge')}}</a>
                @else
                    <a href="{{route('web.challenges_detail',$Join_challenge->id)}}" class="btn btn-primary btn-block">{{__('backend.Joined')}}</a>
                @endif    
            </div>
        </div>
    </div>
    @endforeach
    @else
    {{$data->message}}
    @endif
    @if($data->data->Join_challenge->current_page == $data->data->Join_challenge->last_page)
<style>
    .loadmoar-class
    {
        display: none !important;
    }
</style>
    @endif
</div>
