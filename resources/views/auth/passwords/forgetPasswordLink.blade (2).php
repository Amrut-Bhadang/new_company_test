@extends('layouts.app')

@section('content')
<section id="wrapper">
    <div class="login-register">
            <div class="login-box card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.change.password.post') }}">
                        <div class="text-center">
                            <div class="logo_sec">
                                <img src="{{ URL::asset('assets/images/logo-light-text.png')}}" alt="homepage" /></span> 
                            </div>
                            <h1 class="h4 text-gray-900 mb-4">{{ __('backend.Welcome_to_Tahadiyaat') }} ! </h1>
                        </div>
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">                    
                        <div class="form-group">
                            <div  class="col-xs-12">
                                <input id="email" placeholder="Email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                @if (Session::has('error_msg'))
                                    <span style="width: 100%; margin-top: 0.25rem;font-size: 80%;color: #f62d51;"><strong>{{ Session::get('error_msg') }}</strong></span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-xs-12">
                                <input id="password" placeholder="Password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-xs-12">
                                <input id="password-confirm" placeholder="Confirm Password" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-info btn-rounded">
                                    {{ __('Reset Password') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
    </div>
</section>
@endsection
