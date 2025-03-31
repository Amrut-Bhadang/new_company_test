<?php

use App\Models\Country;
use Illuminate\Support\Facades\Route;

$route = Route::currentRouteName();
$AuthUserData = Session::get('AuthUserData') ?? null;
$location = Session::get('AuthUserLocation');
$country = Country::orderby('phonecode', 'asc')->groupBy('phonecode')->get(); ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/parsley.js/2.9.2/parsley.min.js"></script>
<style>
    a.disabled {
        pointer-events: none;
        color: #ccc;
    }
</style>
<!-- The Modal -->
<div class="modal fade" id="send_otp_modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal"><img src="{{asset('web/images/cross.png')}}"></button>
            <!-- Modal body -->
            <div class="modal-body p-0">
                <div class="login_sec">
                    <div class="login_sec_left">
                        <div class="login_sec_left_head">
                            <h3>{{__('backend.Sign_Up')}}</h3>
                            <p>{{__('backend.Lets_Get_Started')}}</p>
                        </div>
                        <div class="login_sec_left_img"><img src="{{asset('web/images/login_img.png')}}"></div>
                    </div>
                    <div class="login_sec_con">
                        <h4>{{__('backend.Create_An_Account')}}</h4>
                        <p>{{__('backend.Create_Account_popup_text')}}</p>
                        <div class="sendOtp_msg_box"></div>
                        <div class="addFollow_msg_box"></div>
                        <form class="signup-popup-cls" data-parsley-validate id="send_otp" method="POST">
                            @csrf
                            <div class="input-group form-group custom-input-select">
                                <select name="country_code" class="form-control" required>
                                    @foreach ($country as $key => $countryData)
                                    <option {{$countryData->phonecode == "+971" ? 'selected' : ''}} value="{{ $countryData->phonecode }}">{{ $countryData->sortname }} {{ $countryData->phonecode }}</option>
                                    @endforeach
                                </select>
                                <input type="text" name="mobile" placeholder="{{__('backend.Mobile_Number')}}" data-parsley-required data-parsley-type="digits" minlength="8" maxlength="10" class="form-control cust-control-cls">
                                <input type="hidden" name="type" value="register">
                            </div>
                            <div class="form-group">
                                <input type="submit" name="submit" value="{{__('backend.Continue')}}" class="btn btn-primary btn-block">
                            </div>
                            <div class="form-group mb-0">
                                <div class="login_sec_link">
                                    <p>{{__('backend.Already_have_an_account')}}<a href="#" data-dismiss="modal" data-toggle="modal" data-target="#login_modal">{{__('backend.Sign_in')}}</a></p>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="login_modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal"><img src="{{asset('web/images/cross.png')}}"></button>
            <!-- Modal body -->
            <div class="modal-body p-0">
                <div class="login_sec">
                    <div class="login_sec_left">
                        <div class="login_sec_left_head">
                            <h3>{{__('backend.Sign_in')}}</h3>
                            <p>{{__('backend.welcome')}}</p>
                        </div>
                        <div class="login_sec_left_img"><img src="{{asset('web/images/login_img.png')}}"></div>
                    </div>
                    <div class="login_sec_con">
                        <h4>{{__('backend.Enter_Your_Credentials')}}</h4>
                        <p>{{__('backend.login_popup_text')}}</p>
                        <div class="web_login_msg_box"></div>
                        <div class="addFollow_msg_box"></div>
                        <form class="signup-popup-cls" data-parsley-validate id="user_login" method="POST">
                            @csrf
                            <div class=" input-group form-group custom-input-select">
                                <select name="country_code" class="form-control" required>
                                    <?php $selected = isset($_COOKIE["web_country_code"]) ? $_COOKIE["web_country_code"] : '+971' ; ?>
                                    @foreach ($country as $key => $countryData)
                                    <option {{$countryData->phonecode == $selected ? "selected" : ""}} value="{{ $countryData->phonecode }}">{{ $countryData->sortname }} {{ $countryData->phonecode }}</option>
                                    @endforeach
                                </select>
                                <input type="text" name="mobile" value="<?php if(isset($_COOKIE["web_mobile"])) { echo $_COOKIE["web_mobile"]; } ?>" placeholder="{{__('backend.Mobile_Number')}}" data-parsley-required="true" data-parsley-pattern="^[0-9 ]{8,15}$" data-parsley-pattern-message="{{__('backend.validation_mobile_number')}}" class="form-control cust-control-cls">
                            </div>
                            <div class="form-group password_sec">
                                <!-- <input type="password" name="password" value="<?php /* if(isset($_COOKIE["web_password"])) { echo $_COOKIE["web_password"]; } */ ?>" placeholder="{{__('backend.Password')}}" class="form-control" data-parsley-required minlength="8" data-parsley-minlength="8" data-parsley-required-message="{{__('backend.Please_enter_your_new_password')}}" data-parsley-pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%^&*()_+=]).*" data-parsley-pattern-message="{{__('backend.strong_password')}}" data-parsley-required class="form-control " id="login_password_id" autocomplete="off"> -->
                                
                                <input type="password" name="password" value="<?php if(isset($_COOKIE["web_password"])) { echo $_COOKIE["web_password"]; } ?>" placeholder="{{__('backend.Password')}}" class="form-control" data-parsley-required minlength="3" data-parsley-required-message="{{__('backend.Please_enter_your_new_password')}}" data-parsley-required class="form-control " id="login_password_id" autocomplete="off">
                                <div class="eye_icon">
                                    <i class="fa fa-eye login_password_id_eye" onclick="showPassword('login_password_id')"></i>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="check_custom">
                                    <div class="check_custom_itm">{{__('backend.Remember_Me')}}
                                        <input type="checkbox" name="remember_me" value="1" {{isset($_COOKIE["web_remember_me"]) ? "checked" : ""}}  >
                                        <span class="checkmark"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <input type="submit" name="submit" value="{{__('backend.Continue')}}" class="btn btn-primary btn-block">
                            </div>
                            <div class="form-group">
                                <div class="login_sec_link">
                                    <p><a href="#" value="{{__('backend.Continue')}}" data-dismiss="modal" data-toggle="modal" data-target="#forgot_modal">{{__('backend.Forgot_Password')}}</a></p>
                                </div>
                            </div>
                            <div class="form-group mb-0">
                                <div class="login_sec_link">
                                    <p>{{__('backend.You_do_not_have_an_account')}} <a href="#" data-dismiss="modal" data-toggle="modal" data-target="#send_otp_modal">{{__('backend.Sign_Up')}}</a></p>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- set password -->
<div class="modal fade" id="set_password_modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal"><img src="{{asset('web/images/cross.png')}}"></button>
            <!-- Modal body -->
            <div class="modal-body p-0">
                <div class="login_sec">
                    <div class="login_sec_left">
                        <div class="login_sec_left_head">
                            <h3>{{__('backend.Set_Password')}}</h3>
                            <p>{{__('backend.welcome')}}!</p>
                        </div>
                        <div class="login_sec_left_img"><img src="{{asset('web/images/login_img.png')}}"></div>
                    </div>
                    <div class="login_sec_con">
                        <h4>{{__('backend.Enter_your_Password')}}</h4>
                        <!-- <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p> -->
                        <div class="set_password_msg_box"></div>
                        <div class="addFollow_msg_box"></div>
                        <form class="signup-popup-cls" data-parsley-validate id="set_password" method="POST">
                            @csrf
                            <div class="form-group password_sec">
                                <!-- <input type="password" name="password" id="set_password_id" value="" class="form-control" placeholder="{{ __('backend.new_password') }}" data-parsley-required="true" data-parsley-pattern="/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/" data-parsley-pattern-message="{{__('backend.strong_password')}}" /> -->
                                <input type="password" name="password" id="set_password_id" value="" class="form-control" placeholder="{{ __('backend.new_password') }}" data-parsley-required="true" />
                                <div class="eye_icon">
                                    <i class="fa fa-eye set_password_id_eye" onclick="showPassword('set_password_id')"></i>
                                </div>
                            </div>
                            <div class="form-group password_sec">
                                <input type="password" name="confirm_password" id="confirm_password_id" value="" id="confirm_password" class="form-control" placeholder="{{ __('backend.Confirm_Password') }}" data-parsley-required="true" data-parsley-equalto="#set_password_id" data-parsley-equalto-message="{{__('backend.new_password_equalto')}}" />
                                <div class="eye_icon">
                                    <i class="fa fa-eye confirm_password_id_eye" onclick="showPassword('confirm_password_id')"></i>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="check_custom">
                                    <div class="check_custom_itm">{{__('backend.Remember_Me')}}
                                        <input type="checkbox" name="remember_me" value="1">
                                        <span class="checkmark"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <input type="submit" name="submit" value="{{__('backend.Continue')}}" class="btn btn-primary btn-block">
                            </div>
                            <div class="form-group mb-0">
                                <div class="login_sec_link">
                                    <p>{{__('backend.Sign_up_continuing_you_agree_to_tahadiyaat')}} <a href="{{route('web.terms_and_conditions')}}">{{__('backend.Terms_and_Condition')}}</a></p>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- reset password -->
<div class="modal fade" id="reset_password_modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal"><img src="{{asset('web/images/cross.png')}}"></button>
            <!-- Modal body -->
            <div class="modal-body p-0">
                <div class="login_sec">
                    <div class="login_sec_left">
                        <div class="login_sec_left_head">
                            <h3>{{__('backend.Reset_Password')}}</h3>
                            <p>{{__('backend.welcome')}}!</p>
                        </div>
                        <div class="login_sec_left_img"><img src="{{asset('web/images/login_img.png')}}"></div>
                    </div>
                    <div class="login_sec_con">
                        <h4>{{__('backend.Forgot_Your_Password')}}</h4>
                        <p>{{__('backend.forgot_password_popup_text')}}</p>
                        <div class="reset_password_msg_box"></div>
                        <div class="addFollow_msg_box"></div>
                        <form class="signup-popup-cls" data-parsley-validate id="reset_password" method="POST">
                            @csrf
                            <div class="form-group password_sec">
                                <!-- <input type="password" name="password" id="reset_password_id" value="" class="form-control" placeholder="{{ __('backend.new_password') }}" data-parsley-required="true" data-parsley-pattern="/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/" data-parsley-pattern-message="{{__('backend.strong_password')}}" /> -->
                                <input type="password" name="password" id="reset_password_id" value="" class="form-control" placeholder="{{ __('backend.new_password') }}" data-parsley-required="true" />
                                <div class="eye_icon">
                                    <i class="fa fa-eye reset_password_id_eye" onclick="showPassword('reset_password_id')"></i>
                                </div>
                            </div>
                            <div class="form-group password_sec">
                                <input type="password" name="confirm_password" id="reconfirm_password_id" value="" id="confirm_password" class="form-control" placeholder="{{ __('backend.Confirm_Password') }}" data-parsley-required="true" data-parsley-equalto="#reset_password_id" data-parsley-equalto-message="{{__('backend.new_password_equalto')}}" />
                                <div class="eye_icon">
                                    <i class="fa fa-eye reconfirm_password_id_eye" onclick="showPassword('reconfirm_password_id')"></i>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="check_custom">
                                    <div class="check_custom_itm">{{__('backend.Remember_Me')}}
                                        <input type="checkbox" name="remember_me" value="1">
                                        <span class="checkmark"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <input type="submit" name="submit" value="{{__('backend.Continue')}}" class="btn btn-primary btn-block">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Forgot password -->
<div class="modal fade" id="forgot_modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal"><img src="{{asset('web/images/cross.png')}}"></button>
            <!-- Modal body -->
            <div class="modal-body p-0">
                <div class="login_sec">
                    <div class="login_sec_left">
                        <div class="login_sec_left_head">
                            <h3>{{__('backend.Forgot_Password')}}</h3>
                            <p>{{__('backend.Verify_Your_Number')}}</p>
                        </div>
                        <div class="login_sec_left_img"><img src="{{asset('web/images/login_img.png')}}"></div>
                    </div>
                    <div class="login_sec_con">
                        <h4>{{__('backend.Forgot_Your_Password')}}</h4>
                        <p>{{__('backend.forgot_password_popup_text')}}</p>
                        <div class="forgotOtp_msg_box"></div>
                        <div class="addFollow_msg_box"></div>
                        <form class="signup-popup-cls" data-parsley-validate id="forgot_send_otp" method="POST">
                            @csrf
                            <div class="input-group form-group custom-input-select">
                                <select name="country_code" class="form-control" required>
                                    @foreach ($country as $key => $countryData)
                                    <option {{$countryData->phonecode == "+971" ? 'selected' : ''}} value="{{ $countryData->phonecode }}"> {{ $countryData->sortname }} {{ $countryData->phonecode }} </option>
                                    @endforeach
                                </select>
                                <input type="text" name="mobile" placeholder="{{__('backend.Mobile_Number')}}" data-parsley-required data-parsley-type="digits" minlength="8" maxlength="10" class="form-control cust-control-cls">
                                <input type="hidden" name="type" value="forgot">
                            </div>
                            <div class="form-group">
                                <input type="submit" name="submit" value="{{__('backend.Continue')}}" class="btn btn-primary btn-block">
                            </div>
                            <div class="form-group mb-0">
                                <div class="login_sec_link">
                                    <p>{{__('backend.Already_have_an_account')}}<a href="#" data-dismiss="modal" data-toggle="modal" data-target="#login_modal">{{__('backend.Sign_in')}}</a></p>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="verify_otp_model">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal"><img src="{{asset('web/images/cross.png')}}"></button>
            <!-- Modal body -->
            <div class="modal-body p-0">
                <div class="login_sec">
                    <div class="login_sec_left">
                        <div class="login_sec_left_head">
                            <h3>{{__('backend.OTP')}}</h3>
                            <p>{{__('backend.Verify_Your_Number')}}</p>
                        </div>
                        <div class="login_sec_left_img"><img src="{{asset('web/images/otp.png')}}"></div>
                    </div>
                    <div class="login_sec_con">
                        <h4>{{__('backend.Enter_your_OTP_here')}}</h4>
                        @php
                        $userData = Session::get('userData') ?? null;
                        @endphp

                        @if(isset($userData))
                        <p> {{__('backend.verify_otp_popup_text')}} {{$userData['country_code']}}-{{$userData['mobile']}}</p>
                        @else
                        <p> {{__('backend.verify_otp_popup_text')}}</p>
                        @endif
                        <div class="verifyOtp_msg_box"></div>
                        <div class="addFollow_msg_box"></div>
                        <form action="" data-parsley-validate id="verify_otp" method="POST">
                            @csrf
                            <div class="form-group">
                                <input type="text" name="otp" data-parsley-required="true" data-parsley-type="digits" class="form-control">
                            </div>
                            <div class="form-group">
                                <input type="submit" name="" value="{{__('backend.Submit_OTP')}}" class="btn btn-primary btn-block">
                            </div>
                            <div class="form-group mb-0">
                                <div class="otp_link Resend_OTP" id="Resend_OTP"><a href="javascript:void(0);" class="resend_otp">{{__('backend.Resend_OTP')}}?</a><span class=" time_left js-timeout">00:52s</span></div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- verify_otp_model for forgot password -->
<div class="modal fade" id="verify_otp_model_forgot">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal"><img src="{{asset('web/images/cross.png')}}"></button>
            <!-- Modal body -->
            <div class="modal-body p-0">
                <div class="login_sec">
                    <div class="login_sec_left">
                        <div class="login_sec_left_head">
                            <h3>{{__('backend.OTP')}}</h3>
                            <p>{{__('backend.Verify_Your_Number')}}</p>
                        </div>
                        <div class="login_sec_left_img"><img src="{{asset('web/images/otp.png')}}"></div>
                    </div>
                    <div class="login_sec_con">
                        <h4>{{__('backend.Enter_your_OTP_here')}}</h4>
                        @php
                        $userData = Session::get('userData') ?? null;
                        @endphp

                        @if(isset($userData))
                        <p> {{__('backend.verify_otp_popup_text')}} {{$userData['country_code']}}-{{$userData['mobile']}}</p>
                        @else
                        <p> {{__('backend.verify_otp_popup_text')}} </p>
                        @endif
                        <div class="verifyOtpForgot_msg_box"></div>
                        <div class="addFollow_msg_box"></div>
                        <form action="" data-parsley-validate id="verify_otp_forgot" method="POST">
                            @csrf
                            <div class="form-group">
                                <input type="text" name="otp" data-parsley-required="true" data-parsley-type="digits" class="form-control">
                            </div>
                            <div class="form-group">
                                <input type="submit" name="" value="{{__('backend.Submit_OTP')}}" class="btn btn-primary btn-block">
                            </div>
                            <div class="form-group mb-0">
                                <div class="otp_link Resend_OTP" id="Resend_OTP"><a href="javascript:void(0);" class="resend_otp">{{__('backend.Resend_OTP')}}?</a><span class=" time_left js-timeout">00:52s</span></div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="invite_player_Modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal"><img src="{{asset('web/images/cross.png')}}"></button>
            <!-- Modal body -->
            <div class="modal-body">
                <div class="invite_player_content" id="invite_player_content">
                    <h4>{{__('backend.Invite_Player')}}</h4>
                    <form class="invite_player_form" data-parsley-validate id="invite_player_form" method="POST">
                        @csrf
                        <input type="hidden" id="popup_challenge_id" name="challenge_id" value="">
                        <div class=" input-group form-group custom-input-select">
                            <select name="country_code" class="form-control" id="country_code" required>
                                @foreach ($country as $key => $countryData)
                                <option {{$countryData->phonecode == "+971" ? 'selected' : ''}} value="{{ $countryData->phonecode }}">{{ $countryData->sortname }} {{ $countryData->phonecode }}</option>
                                @endforeach
                            </select>
                            <input type="text" id="mobile" name="mobile" placeholder="{{__('backend.Mobile_Number')}}" data-parsley-required="true" data-parsley-pattern="^[0-9 ]{8,15}$" data-parsley-pattern-message="{{__('backend.validation_mobile_number')}}" class="form-control cust-control-cls">
                        </div>
                        <div class="form-group">
                            <input type="submit" name="submit" value="{{__('backend.Search')}}" class="btn btn-primary btn-block">
                        </div>
                    </form>
                </div>
                <div class="search_player_list" id="search_player_list">

                </div>
            </div>
        </div>
    </div>
</div>
<!-- <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<  JQUIRY FUNCTION   >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> -->
<script>
    $('#user_login').parsley();
    $('#set_password').parsley();
    $('#send_otp').parsley();
    $('#verify_otp').parsley();
    $('#verify_otp_forgot').parsley();
    $('#forgot_send_otp').parsley();
    $('#reset_password').parsley();

    //login user
    $("#user_login").on('submit', async function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        $('.preload').show();
        var result = await ajaxFunction('web_login', formData);
        $('.preload').hide();
        if (result.status == true) {
            alertmessage('web_login', result.message, 'success');
            setTimeout(function() {
                location.reload();
            }, 1000);
        } else {
            if (typeof result.message == 'object') {
                var errors = result.message;

                $.each(errors, function(i, error) {
                    $("." + i).after("<div class='errors'>" + error + "</div>");
                });
            } else {
                alertmessage('web_login', result.message, 'warning');
            }
        }
        $('#user_login').trigger("reset");
        return false;
    });
    // ----------------------------------------  AJAX AND MESSAGE FUNCTION  -----------------------------------------------------------
    function alertmessage(method, message, type) {
        $("." + method + "_msg_box").html('<div class="alert alert-' + type +
            '"><a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a><strong style="text-transform: capitalize;">' +
            type + '!</strong> ' + message + '.</div>');
    }

    function ajaxFunction(method, formdata, header) {
        $('div.errors').remove();
        return new Promise((resolve, reject) => {
            var currency = "{{ Session::get('currentCurrency') ?? '' }}";
            var url = '{{url("/")}}/' + method;
            var settings = {
                "url": url,
                "method": "POST",
                "timeout": 0,
                "headers": {
                    "Accept": "application/json",
                    "Authorization": "Bearer {{$AuthUserData->access_token ?? null}}",
                    "currency": currency
                },
                "processData": false,
                "mimeType": "multipart/form-data",
                "contentType": false,
                "data": formdata,
                error: function(error) {
                    console.log(error, '----------------------');
                }
            };
            $.ajax(settings).done(function(response) {
                resolve(JSON.parse(response));
            });
        });
    }

    $(document).on('click', '.logout_web', function() {
        if (confirm("{{__('backend.confirm_box_logout')}}") == true) {
            window.location.href = "{{route('web.logout')}}";
        }
    })

    function showPassword(ids) {
        var x = document.getElementById(ids);
        if (x.type === "password") {
            x.type = "text";
            $('.' + ids + '_eye').removeClass('fa-eye');
            $('.' + ids + '_eye').addClass('fa-eye-slash');
        } else {
            x.type = "password";
            $('.' + ids + '_eye').removeClass('fa-eye-slash');
            $('.' + ids + '_eye').addClass('fa-eye');
        }
    }
    // ----------------------------------------  Send OTP  -----------------------------------------------------------
    $("#send_otp").on('submit', async function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        $('.preload').show();
        var mobile = $('.mobile').val();
        var country_code = $('.country_code').val();
        /**
         * SEND OTP POP VALUE SET
         */
        var result = await ajaxFunction('send-otp', formData);
        $('.preload').hide();

        if (result.status == true) {
            alertmessage('sendOtp', result.message, 'success');
            setTimeout(function() {
                $('#send_otp_modal').modal('hide');
                $('#verify_otp_model').modal('show');
                $('.js-timeout').text("0:60");
                countdown();
            }, 1000);

        } else {
            if (typeof result.message == 'object') {
                var errors = result.message;
                $.each(errors, function(i, error) {
                    $("." + i).after("<div class='errors'>" + error + "</div>");
                });
            } else {
                alertmessage('sendOtp', result.message, 'warning');
            }
        }
        $('#send_otp').trigger("reset");
        return false;
    });
    // ---------------------------------------- forgot password Send OTP  -----------------------------------------------------------
    $("#forgot_send_otp").on('submit', async function(e) {
        e.preventDefault();
        // alert('ddddddddddd');
        // return false;
        var formData = new FormData(this);
        $('.preload').show();
        var mobile = $('.mobile').val();
        var country_code = $('.country_code').val();
        /**
         * SEND OTP POP VALUE SET
         */
        var result = await ajaxFunction('send-otp', formData);
        $('.preload').hide();

        if (result.status == true) {
            alertmessage('forgotOtp', result.message, 'success');
            setTimeout(function() {
                $('#forgot_modal').modal('hide');
                $('#verify_otp_model_forgot').modal('show');
                $('.js-timeout').text("0:60");
                countdown();
            }, 1000);

        } else {
            if (typeof result.message == 'object') {
                var errors = result.message;
                $.each(errors, function(i, error) {
                    $("." + i).after("<div class='errors'>" + error + "</div>");
                });
            } else {
                alertmessage('forgotOtp', result.message, 'warning');
            }
        }
        $('#send_otp').trigger("reset");
        return false;
    });
    // ----------------------------------------  ReSend OTP  -----------------------------------------------------------
    // $("#Resend_OTP").on('submit', async function(e) {
    $(document).on('click', '#Resend_OTP', async function(e) {
        e.preventDefault();

        if ($('.resend_otp').hasClass("disabled")) {
            // toastr.error('Please wait');
            return false;
        }
        $.ajax({
            type: 'post',
            data: {
                _method: 'post',
                _token: "{{ csrf_token() }}",
                type: "resend",
            },
            dataType: 'json',
            url: "{!! url('resend-otp' )!!}",
            success: function(result) {
                if (result.status == 1) {
                    setTimeout(function() {
                        toastr.success(result.message);
                        $('.js-timeout').text("0:60");
                        countdown();
                    }, 1000);
                } else {
                    toastr.error(result.message);
                }
            },
            error: function(jqXHR, textStatus, textStatus) {
                console.log(jqXHR);
                toastr.error(jqXHR.statusText)
            }
        });


        $('#send_otp').trigger("reset");
        return false;
    });
    // ----------------------------------------  verify OTP  -----------------------------------------------------------
    $("#verify_otp").on('submit', async function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        $('.preload').show();

        /**
         * SEND OTP POP VALUE SET
         */
        var result = await ajaxFunction('verify-otp', formData);
        $('.preload').hide();

        if (result.status == true) {
            alertmessage('verifyOtp', result.message, 'success');
            setTimeout(function() {
                $('#verify_otp_model').modal('hide');
                $('#set_password_modal').modal('show');
                $('.js-timeout').text("0:60");
                // countdown();
            }, 1000);

        } else {
            if (typeof result.message == 'object') {
                var errors = result.message;
                $.each(errors, function(i, error) {
                    $("." + i).after("<div class='errors'>" + error + "</div>");
                });
            } else {
                alertmessage('verifyOtp', result.message, 'warning');
            }
        }
        $('#verify_otp').trigger("reset");
        return false;
    });
    // ----------------------------------------  verify OTP forgot password -----------------------------------------------------------
    $("#verify_otp_forgot").on('submit', async function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        $('.preload').show();

        /**
         * SEND OTP POP VALUE SET
         */
        var result = await ajaxFunction('verify-otp', formData);
        $('.preload').hide();

        if (result.status == true) {
            alertmessage('verifyOtpForgot', result.message, 'success');
            setTimeout(function() {
                $('#verify_otp_model_forgot').modal('hide');
                $('#reset_password_modal').modal('show');
                $('.js-timeout').text("0:60");
                // countdown();
            }, 1000);

        } else {
            if (typeof result.message == 'object') {
                var errors = result.message;
                $.each(errors, function(i, error) {
                    $("." + i).after("<div class='errors'>" + error + "</div>");
                });
            } else {
                alertmessage('verifyOtpForgot', result.message, 'warning');
            }
        }
        $('#verify_otp_forgot').trigger("reset");
        return false;
    });
    // ----------------------------------------  set password OTP  -----------------------------------------------------------
    $("#set_password").on('submit', async function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        $('.preload').show();

        /**
         * SEND OTP POP VALUE SET
         */
        var result = await ajaxFunction('set-password', formData);
        $('.preload').hide();

        if (result.status == true) {
            alertmessage('set_password', result.message, 'success');
            setTimeout(function() {
                location.reload();
            }, 1000);

        } else {
            if (typeof result.message == 'object') {
                var errors = result.message;
                $.each(errors, function(i, error) {
                    $("." + i).after("<div class='errors'>" + error + "</div>");
                });
            } else {
                alertmessage('set_password', result.message, 'warning');
            }
        }
        $('#set_password').trigger("reset");
        return false;
    });
    // ----------------------------------------  reset password OTP  -----------------------------------------------------------
    $("#reset_password").on('submit', async function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        $('.preload').show();

        /**
         * SEND OTP POP VALUE SET
         */
        var result = await ajaxFunction('reset-password', formData);
        $('.preload').hide();

        if (result.status == true) {
            alertmessage('reset_password', result.message, 'success');
            toastr.success(result.message)
            setTimeout(function() {
                location.reload();
            }, 1000);

        } else {
            if (typeof result.message == 'object') {
                var errors = result.message;
                $.each(errors, function(i, error) {
                    $("." + i).after("<div class='errors'>" + error + "</div>");
                });
            } else {
                alertmessage('set_password', result.message, 'warning');
            }
        }
        $('#set_password').trigger("reset");
        return false;
    });
    var interval;

    function countdown() {

        clearInterval(interval);
        interval = setInterval(function() {
            var timer = $('.js-timeout').html();
            timer = timer.split(':');
            var minutes = timer[0];
            var seconds = timer[1];
            seconds -= 1;
            if (minutes < 0) return;
            else if (seconds < 0 && minutes != 0) {
                minutes -= 1;
                seconds = 10;

            } else if (seconds < 10 && length.seconds != 2) seconds = '0' + seconds;

            $('.js-timeout').html(minutes + ':' + seconds);

            if (minutes == 0 && seconds == 0) {
                $('.resend_otp').removeClass("disabled");
                $('.time_left').hide();
                clearInterval(interval);
            } else {
                $('.resend_otp').addClass("disabled");
                $('.time_left').show();
            }




        }, 1000);
    }


    $('#js-resetTimer').click(function() {
        $('.js-timeout').text("0:60");
        clearInterval(interval);
    });
    $(document).ready(function() {
        $(".js-example-templating").select2({
            search: true
        });

    });
</script>