@extends('layouts.app')

@section('content')
<section id="wrapper">
    <div class="login-register">
        <div class="login-box card">
            <div class="card-body">
                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    <div class="text-center">
                        <div class="logo_sec">
                            <img src="{{ URL::asset('assets/images/logo-light-text.png')}}" alt="homepage" /></span> 
                        </div>
                        <h1 class="h4 text-gray-900 mb-4">{{ __('backend.Welcome_to_Tahadiyaat') }} ! </h1>
                    </div>
                    @csrf

                    <div class="form-group">
                        
                        <div class="col-xs-12">
                            <input id="email" placeholder="Email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
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

                    <div class="form-group text-center m-t-20">
                        <div class="col-xs-12">
                          
                            <button type="submit" class="btn btn-block btn-lg btn-info btn-rounded">
                                {{ __('Forgot Password') }}
                            </button>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <div class="custom-control custom-checkbox">
                                @if (Route::has('admin.login'))
                                <a class="text-dark-cls float-right" href="{{ route('admin.login') }}">
                                    <!-- <i class="fa fa-lock m-r-5"></i>  -->
                                    {{ __('Login') }}
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection
