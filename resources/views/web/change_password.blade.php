@extends('layouts.web.master')
@section('title',$title)
@section('content')
<?php
$userData =  Session::get('AuthUserData') ?? null;
$access_token = $userData->token ?? null;
// $header = json_encode(array('Accept:application/json','Authorization:Bearer ' . $access_token));
$header = $access_token;
// dd($header);
?>
<main class="account-page inner_page_space">
    <section class="space-cls">
        <div class="container">
            <div class="account-page-in">
                @include('layouts.web.include.leftbar_itms')
                <div class="content_sec">
                    <div class="content_sec_in">
                        <div class="content_sec_profileinfo">
                            <h4>{{__('backend.Change_Password')}}</h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <form method="POST" enctype="" id="change_password">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>{{__('backend.Old_Password')}}</label>
                                                    <div class="password_sec">
                                                        <input type="password" name="old_password" id="password" placeholder="{{ __('backend.Old_Password') }}" data-parsley-required="true" class="form-control">
                                                        <div class="eye_icon">
                                                            <i class="fa fa-eye password_eye" onclick="showPassword('password')"></i>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>{{__('backend.New_Password')}}</label>
                                                    <div class="password_sec">
                                                        <!-- <input type="password" name="new_password" id="new_password" placeholder="{{ __('backend.New_Password') }}" data-parsley-required="true" data-parsley-pattern="/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/" data-parsley-pattern-message="{{__('backend.strong_password')}}" class="form-control"> -->
                                                        <input type="password" name="new_password" id="new_password" placeholder="{{ __('backend.New_Password') }}" data-parsley-required="true" class="form-control">
                                                        <div class="eye_icon">
                                                            <i class="fa fa-eye new_password_eye" onclick="showPassword('new_password')"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>{{__('backend.Confirm_Password')}}</label>
                                                    <div class="password_sec">
                                                        <input type="password" name="confirm_password" id="confirm_password" placeholder="{{ __('backend.Confirm_Password') }}" data-parsley-required="true" data-parsley-equalto="#new_password" data-parsley-equalto-message="{{__('backend.new_password_equalto')}}" class="form-control">
                                                        <div class="eye_icon">
                                                            <i class="fa fa-eye confirm_password_eye" onclick="showPassword('confirm_password')"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group mb-0 submit_btn">
                                                    <input type="submit" name="" value="{{__('backend.Submit')}}" class="btn btn-primary">
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
<script src="{{ asset('js/parsley.min.js') }}"></script>
<script>
    $(document).ready(function() {
        $('#change_password').parsley();

        $(document).on('submit', "#change_password", function(e) {
            e.preventDefault();

            var _this = $(this);
            var formData = new FormData(this);
            formData.append('_method', 'post');
            var url = "{{ route('web.change_password_submit')}}";
            var header = "{{ $header }}";
            // alert(header);
            // alert(url);
            $('#group_loader').fadeIn();
            // var values = $('#change_password').serialize();
            $.ajax({
                url: url,
                headers: {
                    "Accept": "application/json",
                    "Authorization": "Bearer {{$header ?? null}}"
                },
                dataType: 'json',
                data: formData,
                contentType: 'application/json; charset=utf-8',
                type: 'POST',
                cache: false,
                contentType: false,
                processData: false,
                // beforeSend: function() {
                //     before(_this)
                // },
                // hides the loader after completion of request, whether successfull or failor.
                complete: function() {
                    complete(_this)
                },
                success: function(result) {
                    // console.log(result, 'success');
                    $('#change_password').parsley().reset();
                    if (result.status) {
                        toastr.success(result.message);
                        setTimeout(function(){
                            window.location.href = "{{route('web.my_account')}}";
                        }, 800);
                    } else {
                        toastr.error(result.message)
                        $('.save').prop('disabled', false);
                        $('.formloader').css("display", "none");
                    }
                },
                error: function(jqXHR, textStatus, textStatus) {
                    // console.log(textStatus, 'error');

                    if (jqXHR.responseJSON.errors) {
                        $.each(jqXHR.responseJSON.errors, function(index, value) {
                            toastr.error(value)
                        });
                    } else {
                        toastr.error(jqXHR.responseJSON.message)
                    }
                }
            });
            return false;
        });
    });

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
</script>
@endsection