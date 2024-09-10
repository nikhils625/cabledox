@extends('layouts.authentication')
@section('title', 'Login')

@section('page-styles')
<link rel="stylesheet" href="{{ asset('assets/vendor/parsleyjs/css/parsley.css') }}">
@stop

@section('content')
<div class="auth_brand">
    <a class="navbar-brand" href="#"><img src="{{ asset('assets/images/icon.svg') }}" width="50" class="d-inline-block align-top mr-2" alt="">Cabledox</a>
</div>
<div class="card">
    <div class="header">
        <p class="lead">Login to your account</p>
    </div>
    <div class="body">
        @include('layouts.messages')
        <form id="login-form" class="form-auth-small" method="POST" action="{{ route('login') }}">
            @csrf
            <div class="form-group c_form_group">
                <label for="email">{{ __('E-Mail Address') }}</label>
                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="Enter your email address">

                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="form-group c_form_group">
                <label for="password">{{ __('Password') }}</label>
                <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" required autocomplete="current-password" placeholder="Enter your password">

                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="form-group clearfix">
                <label class="fancy-checkbox element-left">
                    <input type="checkbox" class="form-check-input" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <span>{{ __('Remember Me') }}</span>
                </label>                                
            </div>
            <button type="submit" class="btn btn-dark btn-lg btn-block">{{ __('LOGIN') }}</button>
            <div class="bottom">
                @if (Route::has('password.request'))
                    <span class="helper-text m-b-10"><i class="fa fa-lock"></i> <a href="{{ route('password.request') }}">{{ __('Forgot Password?') }}</a></span>
                @endif
            </div>
        </form>
    </div>
</div>
@endsection

@section('vendor-script')
<script src="{{ asset('assets/vendor/parsleyjs/js/parsley.min.js') }}"></script>
@stop

@section('page-script')
<script>
    $(function() {
        // validation needs name of the element
        $('#login-form').parsley();
    });
</script>
@stop