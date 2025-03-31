@php
$auth_user = Session::get('AuthUserData');
$url = $_SERVER['REQUEST_URI'];
if(strpos($url,'court_detail')){
$header_class = 'main-header inner-header';
}
else if(strpos($url,'challenges_detail')){
$header_class = 'main-header inner-header';
}
else if(strpos($url,'challenges')){
$header_class = 'main-header inner-header';
}
else if(strpos($url,'change_password')){
$header_class = 'main-header inner-header';
}
else if(strpos($url,'completed_booking')){
$header_class = 'main-header inner-header';
}
else if(strpos($url,'facility_detail')){
$header_class = 'main-header inner-header';
}
else if(strpos($url,'my_account')){
$header_class = 'main-header inner-header';
}
else if(strpos($url,'upcoming_booking')){
$header_class = 'main-header inner-header';
}
else if(strpos($url,'cancelled_booking')){
$header_class = 'main-header inner-header';
}
else if(strpos($url,'thank-you')){
$header_class = 'main-header inner-header';
}
else{
$header_class = 'main-header';
}


@endphp
<?php

use App\User;

// $login_user_data = auth()->user();
if (isset($auth_user)) {
    $userId = $auth_user->data->id;
    $data = getNotificationPlayerList($userId, 3);
    // dd($data);
    $notificaiton_list = $data['notificationData'];
    $notificaiton_count = $data['count'];
    // dd(app()->getLocale())
}
?>
<header>
    <div class="{{$header_class}}">
        <div class="container">
            <div class="header_cls">
                <div class="logo">
                    <a href="{{route('web.home')}}"><img src="{{asset('web/images/web_logo.svg')}}"></a>
                </div>
                <div class="menu">
                    <nav class="navbar navbar-expand-lg p-0">
                        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                            <img src="{{asset('web/images/menu.png')}}" alt="">
                        </button>
                        <div class="collapse navbar-collapse" id="navbarSupportedContent">
                            <div class="remove_bg"><button type="button" class="btn_cross"><img src="{{asset('web/images/cross.png')}}"></button></div>
                            <ul class="navbar-nav mr-auto">
                                <li class="nav-item">
                                    <a href="{{route('web.about_us')}}" class="nav-link {{ (Request::is('about-us') ? 'active':'') }}">{{__('backend.About_US')}}</a>
                                </li>

                                <li class="nav-item">
                                    <a href="{{route('web.court_list')}}" class="nav-link {{ (Request::is('court') ? 'active':'') }}">{{__('backend.Courts')}}</a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{route('web.facility')}}" class="nav-link {{ (Request::is('facility') ? 'active':'') }}">{{__('backend.Facilities')}}</a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{route('web.contact_us')}}" class="nav-link {{ (Request::is('contact-us') ? 'active':'') }}">{{__('backend.Contact_us')}}</a>
                                </li>
                            </ul>
                        </div>
                    </nav>
                </div>
                @if($auth_user != null)
                <div class="notification_main_sec nav-item dropdown realTimeNotificationDataUpdate">
                </div>
                <div class="notification_main_sec nav-item dropdown realTimeNotificationDataUpdate">
                    <a class="nav-link" data-toggle="dropdown" href="#">
                        <i class="fa fa-bell"></i>
                        @if($notificaiton_count > 0)
                        <span class="notificaiton_count">{{$notificaiton_count}}</span>
                        @endif
                    </a>
                    <div class="dropdown-menu dropdown-menu-right notification_main">
                        <div class="notification_inner_scroll">
                            <div class="clear-all-cls">
                                <a href="javascript:void(0)" onclick="clearAllNotification()">{{__('backend.Clear_All')}}</a>
                            </div>
                            <?php if ($notificaiton_list && count($notificaiton_list)) {
                                foreach ($notificaiton_list as $key => $value) { ?>
                                    <div class="notification_wrap <?php echo ($value->is_read == 0) ? 'active' : ''; ?>">
                                        <?php
                                        if ($value->notification_for == 'create_challenge') {
                                            $redirectUrl = 'upcoming_booking';
                                        } elseif ($value->notification_for == 'book_court') {
                                            $redirectUrl = 'upcoming_booking';
                                        } elseif ($value->notification_for == 'booking_cancel') {
                                            $redirectUrl = 'cancelled_booking';
                                        } elseif ($value->notification_for == 'create_user') {
                                            $redirectUrl = 'my_account';
                                        } elseif ($value->notification_for == 'invite_player') {
                                            $redirectUrl = 'challenges_detail/' . $value->order_id;
                                        } elseif ($value->notification_for == 'accepted_challenge') {
                                            $redirectUrl = 'challenges_detail/' . $value->order_id;
                                        } elseif ($value->notification_for == 'join_challenge') {
                                            $redirectUrl = 'challenges_detail/' . $value->order_id;
                                        }
                                        elseif ($value->notification_for == 'post_payment_not_received') {
                                            $redirectUrl = 'cancelled_booking';
                                        }
                                        elseif ($value->notification_for == 'booking_accepted_by_admin') {
                                            $redirectUrl = 'upcoming_booking';
                                        }
                                        else {
                                            $redirectUrl = '';
                                        }
                                        ?>
                                        <p class=" m-b-0"><a href="javascript:void(0);" data-id="{{$value->id}}" data-url="{{url('').'/'.$redirectUrl}}" onclick="readNotification(this)"># {{$value->order_id}} {!!$value->message!!}</a></p>
                                    </div>
                                <?php }
                            } else { ?>
                                <div class="notification_wrap">
                                    <p class="m-b-0">{{__('backend.No_data_found')}}</p>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                @endif
                <?php /*
                <div class="account_sec dropdown account_sec_language">
                    <select class="form-control" id="lang" onchange="localchange()">
                        <option value="en" {{ (app()->getLocale()=='en'?'selected':'') }}>{{__('backend.English')}}</option>
                        <option value="ar" {{ (app()->getLocale()=='ar'?'selected':'') }}>{{__('backend.Arabic')}}</option>
                    </select>
                    <div class="dropdown-menu dropdown-menu-right flipInY1">
                        <a href="{{url('locale/en')}}" class="dropdown-item {{ (app()->getLocale()=='en'?'active':'') }}"></a>
                        <a href="{{url('locale/ar')}}" class="dropdown-item {{ (app()->getLocale()=='ar'?'active':'') }}"></a>
                    </div>
                </div>
                */ ?>
                <div class="account_sec dropdown account_sec_language">
                    <a class="btn btn-primary dropdown-toggle" data-toggle="dropdown" href="javascript:void(0);" aria-expanded="false">
                        <i class="fa fa-language text-white"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right flipInY1">
                        <a href="{{url('locale/en')}}" class="dropdown-item {{ (app()->getLocale()=='en'?'active':'') }}">English</a>
                        <a href="{{url('locale/ar')}}" class="dropdown-item {{ (app()->getLocale()=='ar'?'active':'') }}">عربي</a>
                    </div>
                </div>

                @if($auth_user != null)
                <div class="account_sec dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="head-user-img"><img src="{{$auth_user->data->image}}"></span> <span class="user-name-cls">{{$auth_user->data->name ? $auth_user->data->name:$auth_user->data->country_code.'-'.$auth_user->data->mobile}}</span></button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item" href="{{route('web.my_account')}}"><span class="drop-ic"><img src="{{asset('web/images/icon-1.png')}}"></span>{{__('backend.My_Account')}}</a>
                        <a class="dropdown-item" href="{{route('web.challenges')}}"><span class="drop-ic"><img src="{{asset('web/images/icon-2.png')}}"></span>{{__('backend.Challenges')}}</a>
                        <a class="dropdown-item" href="{{route('web.upcoming_booking')}}"><span class="drop-ic"><img src="{{asset('web/images/icon-3.png')}}"></span>{{__('backend.Upcoming_Bookings')}}</a>
                        <a class="dropdown-item" href="{{route('web.completed_booking')}}"><span class="drop-ic"><img src="{{asset('web/images/icon-4.png')}}"></span>{{__('backend.Completed_Bookings')}}</a>
                        <a class="dropdown-item" href="{{route('web.cancelled_booking')}}"><span class="drop-ic"><img src="{{asset('web/images/icon-4.png')}}"></span>{{__('backend.Cancelled_Bookings')}}</a>
                        <a class="dropdown-item" href="{{route('web.favourate_court')}}"><span class="drop-ic"><img src="{{asset('web/images/icon-4.png')}}"></span>{{__('backend.Favourate_court')}}</a>
                        <a class="dropdown-item" href="{{route('web.favourate_facility')}}"><span class="drop-ic"><img src="{{asset('web/images/icon-4.png')}}"></span>{{__('backend.Favourate_facility')}}</a>
                        <a class="dropdown-item" href="{{route('web.change_password')}}"><span class="drop-ic"><img src="{{asset('web/images/icon-5.png')}}"></span>{{__('backend.Change_Password')}} </a>
                        <a class="dropdown-item logout_web" href="javascript:void(0);"><span class="drop-ic"><img src="{{asset('web/images/icon-6.png')}}"></span>{{__('backend.Log_Out')}}</a>
                    </div>
                </div>

                @else
                <div class="account_sec">
                    <button type="button" class="btn-primary" data-toggle="modal" data-target="#login_modal">{{__('backend.Login')}}</button>
                    <button type="button" class="btn-primary" data-toggle="modal" data-target="#send_otp_modal">{{__('backend.Register')}}</button>
                </div>
                @endif
            </div>
        </div>
    </div>
</header>
<script>
    function localchange() {
        lang = $('#lang').find(":checked").val();
        if (lang == 'en') {
            window.location.href = "{{url('locale/en')}}";
        } else {
            window.location.href = "{{url('locale/ar')}}";
        }
    }
</script>