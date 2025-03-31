@extends('layouts.app')

@section('content')
<script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
<section id="wrapper">
    <div class="login-register">
        <div class="login-box card">
            <div class="card-body">
                <form method="POST" class="form-horizontal form-material" id="loginform" action="{{ route('admin.login') }}">
                    <div class="text-center">
                        <div class="logo_sec">
                            <img src="{{ URL::asset('assets/images/logo-light-text.svg')}}" alt="homepage" /></span>
                        </div>
                        <h1 class="h4 text-gray-900 mb-4">{{ __('backend.Welcome_to_Tahadiyaat') }} ! </h1>
                    </div>
                    @csrf
                    @if (Session::has('error_msg'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <h6>{{ Session::get('error_msg') }}</h6> {{ session('danger') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <!-- <span style="width: 100%; margin-top: 0.25rem;font-size: 80%;color: #f62d51;"><strong>{{ Session::get('error_msg') }}</strong></span> -->
                    @endif
                    @if (Session::has('message'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <h6>{{ Session::get('message') }}</h6> {{ session('danger') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <!-- <span style="width: 100%; margin-top: 0.25rem;font-size: 80%;color: #f62d51;"><strong>{{ Session::get('error_msg') }}</strong></span> -->
                    @endif
                    <div class="form-group">
                        <div class="col-xs-12">
                            <input id="email" placeholder="Email" type="text" class="form-control @error('email') is-invalid @enderror" name="email" value="{{isset($_COOKIE["admin_email"]) ? $_COOKIE["admin_email"] :  old('email') }}" autocomplete="email" autofocus>
                            @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group" style="position: relative;">
                        <div class="col-xs-12">
                            <input id="password" placeholder="Password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" value="{{isset($_COOKIE["admin_password"]) ? $_COOKIE["admin_password"] : '' }}" autocomplete="current-password">
                            <i class="fa fa-eye" style="margin-left: -20px; cursor: pointer; position: absolute;z-index: 99;top: 12px;" id="toggleNewPassword"></i>
                            @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-12">
                            <div class="custom-control custom-checkbox">
                                <input class="form-check-input" type="checkbox" name="remember_me" id="remember_me" value="1" {{isset($_COOKIE["admin_remember_me"]) ? "checked" : ""}}>
                                <label class="form-check-label" for="remember_me">
                                    {{ __('Remember Me') }}
                                </label>
                                @if (Route::has('password.request'))
                                <a class="text-dark-cls float-right" href="{{ route('password.request') }}">
                                    <i class="fa fa-lock m-r-5"></i> {{ __('Forgot Password') }}
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="form-group text-center">
                        <div class="col-xs-12 p-b-20">
                            <button type="submit" class="btn btn-block btn-lg btn-info btn-rounded">
                                {{ __('Login') }}
                            </button>
                        </div>
                    </div>
                </form>
                <center>
                    <span>
                        <script>
                            document.write(new Date().getFullYear())
                        </script> © {{ config('app.name')}}.</script>
                    </span>
                </center>
            </div>
        </div>
    </div>
</section>
<script type="text/javascript">
    $(document).ready(function() {
        const toggleNewPassword = document.querySelector('#toggleNewPassword');
        const newPassword = document.querySelector('#password');
        toggleNewPassword.addEventListener('click', function(e) {
            // toggle the type attribute
            const type = newPassword.getAttribute('type') === 'password' ? 'text' : 'password';
            newPassword.setAttribute('type', type);
            // toggle the eye / eye slash icon
            this.classList.toggle('fa-eye-slash');
        });
    });
</script>
@endsection