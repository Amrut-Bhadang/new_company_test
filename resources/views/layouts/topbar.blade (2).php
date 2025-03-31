<?php

use App\User;

$login_user_data = auth()->user();
$userId = $login_user_data->id;
$restaurant_detail = '';
$brandRestroList = getBrandRestros();

$data = getNotificationList($userId, $login_user_data->type);
// dd($data);
$notificaiton_list = $data['notificationData'];
$notificaiton_count = $data['count'];
// dd(app()->getLocale());
?>
<style>
    .switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 34px;
        margin-top: 10px;
        margin-right: 10px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        -webkit-transition: .4s;
        transition: .4s;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 26px;
        width: 26px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
    }

    input:checked+.slider {
        background-color: #2196F3;
    }

    input:focus+.slider {
        box-shadow: 0 0 1px #2196F3;
    }

    input:checked+.slider:before {
        -webkit-transform: translateX(26px);
        -ms-transform: translateX(26px);
        transform: translateX(26px);
    }

    /* Rounded sliders */
    .slider {
        border-radius: 34px;
    }

    .slider:before {
        border-radius: 50%;
    }
</style>
<header class="topbar">
    <nav class="navbar top-navbar navbar-expand-md navbar-dark">
        <!-- ============================================================== -->
        <!-- Logo -->
        <!-- ============================================================== -->
        <!-- <div class="navbar-header">
            <a class="navbar-brand" href="{{ url('') }}">
              <span><img src="{{ URL::asset('assets/images/platorya-logo-white.png')}}"     width="200" class="light-logo" alt="homepage" /></span> 
            </a>
        </div> -->
        <!-- ============================================================== -->
        <!-- End Logo -->
        <!-- ============================================================== -->
        <div class="navbar-collapse">
            <!-- ============================================================== -->
            <!-- toggle and nav items -->
            <!-- ============================================================== -->
            <div class="admin_logo">
                <img src="{{ URL::asset('assets/images/logo.svg')}}">
            </div>
            <ul class="navbar-nav mr-auto">
                <!-- This is  -->
                <li class="nav-item hidden-sm-up"> <a class="nav-link nav-toggler waves-effect waves-light" href="javascript:void(0)"><i class="ti-menu"></i></a></li>
                <!-- ============================================================== -->
                <!-- Search -->
                <!-- ============================================================== -->
                <!-- <li class="nav-item search-box"> <a class="nav-link waves-effect waves-dark" href="javascript:void(0)"><i class="ti-search"></i></a>
                    <form class="app-search">
                        <input type="text" class="form-control" placeholder="Search &amp; enter"> <a class="srh-btn"><i class="ti-close"></i></a>
                    </form>
                </li> -->
            </ul>
            <ul class="navbar-nav my-lg-0">
                <li class="nav-item dropdown opendrop1">
                    <a class="nav-link" data-toggle="dropdown" href="#">
                        <i class="fa fa-language text-white"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right flipInY1">
                        <a href="{{url('locale/en')}}" class="dropdown-item {{ (app()->getLocale()=='en'?'active':'') }}">{{__('backend.English')}}</a>
                        <a href="{{url('locale/ar')}}" class="dropdown-item {{ (app()->getLocale()=='ar'?'active':'') }}">{{__('backend.Arabic')}}</a>
                    </div>
                </li>

                <?php if ($login_user_data->type == 4) { ?>
                    <label class="switch">
                        <input onclick="changeRestroOnOff(this)" data-restroId="{{$userId}}" type="checkbox" <?php if ($restaurant_detail) {
                                                                                                                    echo $restaurant_detail->is_on == 1 ? 'checked' : '';
                                                                                                                } ?>>
                        <span class="slider"></span>
                    </label>
                <?php } ?>

                <?php if ($brandRestroList && !empty($brandRestroList)) { ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle btn btn-info" style="color: #fff;font-weight: bold;line-height: unset;margin-top: 11px;padding: 5px 20px !important;" href="javascript:void(0);" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Outlets <i class="fa fa-angle-down "></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right user-dd animated flipInY">
                            <span class="with-arrow"><span class="bg-primary"></span></span>
                            <?php foreach ($brandRestroList as $k => $v) { ?>
                                <a href="javascript:void(0);" onclick="accountSwitch(this)" data-userId="{{$v->user_id}}" class="dropdown-item {{ ($v->user_id == $login_user_data->id?'active':'') }}">{{ $v->name }}</a>
                            <?php } ?>
                        </div>
                    </li>
                <?php } ?>

                <li class="nav-item dropdown opendrop2 realTimeNotificationDataUpdate">
                <a class="nav-link" data-toggle="dropdown" href="javascript:void(0)">
                <i class="fa fa-bell"></i>
                <span class="notificaiton_count">{{$notificaiton_count}}</span>
                </a>
                <div class="dropdown-menu dropdown-menu-right notification_main flipInY2">
                    <div class="notification_inner_scroll ">
                      <div class="clear-all-cls">
                          <a href="javascript:void(0)" onclick="clearAllNotification()">{{__('backend.Clear_All')}}</a>
                      </div>

                        <?php if ($notificaiton_list && count($notificaiton_list)) { foreach ($notificaiton_list as $key => $value) { ?>
                            <div class="notification_wrap <?php echo ($value->is_read == 0) ? 'active' : ''; ?>">
                                <?php
                                    if($value->notification_for == 'create_challenge'){
                                        $redirectUrl = 'orders';
                                    }
                                    elseif($value->notification_for == 'book_court'){
                                        $redirectUrl = 'orders';
                                    }
                                    elseif($value->notification_for == 'booking_cancel'){
                                        $redirectUrl = 'orders';
                                    }
                                    elseif($value->notification_for == 'create_user'){
                                        $redirectUrl = 'players';
                                    }
                                    else{
                                        $redirectUrl = '';
                                    }
                                    
                                ?>
                                <p class=" m-b-0"><a href="javascript:void(0);" data-id="{{$value->id}}" data-url="{{url('').'/admin/'.$redirectUrl}}" onclick="readNotification(this)"># {{$value->order_id}} {!!$value->message!!}</a></p>
                                <!-- <a href="#"><i class="fa fa-trash"></i></a> -->
                            </div>
                        <?php } } else { ?>
                            <div class="notification_wrap">
                                <p class="m-b-0">{{__('backend.No_data_found')}}</p>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </li>


                <!-- ============================================================== -->
                <!-- User profile and search -->
                <!-- ============================================================== -->
                <li class="nav-item dropdown opendrop">
                    <a class="nav-link dropdown-toggle text-muted waves-effect waves-dark" href="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img src="{{Auth::user()->image}}" alt="user" class="img-circle" width="30"></a>
                    <div class="dropdown-menu dropdown-menu-right user-dd animated flipInY">
                        <span class="with-arrow"><span class="bg-primary"></span></span>
                        <div class="d-flex no-block align-items-center p-15 bg-primary text-white m-b-10">
                            <div class="">
                                <img src="{{Auth::user()->image}}" alt="user" class="img-circle" width="60">
                            </div>
                            <div class="m-l-10">
                                <h4 class="m-b-0">{{ucwords(Auth::user()->name)}}</h4>
                                <p class=" m-b-0">{{ucwords(Auth::user()->email)}}</p>
                            </div>
                        </div>
                        <a class="dropdown-item" href="{{ route('admin.settings') }}"><i class="ti-user m-r-5 m-l-5"></i> {{ __('backend.My_Profile') }}</a>
                        <!-- <a class="dropdown-item" href="javascript:void(0)"><i class="ti-wallet m-r-5 m-l-5"></i> My Balance</a>
                        <a class="dropdown-item" href="javascript:void(0)"><i class="ti-email m-r-5 m-l-5"></i> Inbox</a>
                        <div class="dropdown-divider"></div> -->
                        <!-- <a class="dropdown-item" href="#"><i class="ti-settings m-r-5 m-l-5"></i> Account Setting</a> -->
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="{{ route('admin.logout') }}" onclick="event.preventDefault();
                                            document.getElementById('logout-form').submit();">
                            <i class="fas fa-sign-out-alt"></i>
                            {{ __('backend.Logout') }}
                        </a>

                        <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                        <!-- <div class="dropdown-divider"></div>
                        <div class="p-l-30 p-10"><a href="javascript:void(0)" class="btn btn-sm btn-success btn-rounded">View Profile</a></div> -->
                    </div>
                </li>
                <!-- ============================================================== -->
                <!-- User profile and search -->
                <!-- ============================================================== -->
            </ul>
        </div>
    </nav>
</header>
<script>
    $('.opendrop').click(function(){
        $('.opendrop').addClass('show');
        $('.flipInY').addClass('show');
    })
    $('.opendrop1').click(function(){
        $('.opendrop1').addClass('show');
        $('.flipInY1').addClass('show');
    })
    $('.opendrop2').click(function(){
        $('.opendrop2').addClass('show');
        $('.flipInY2').addClass('show');
    })
</script>