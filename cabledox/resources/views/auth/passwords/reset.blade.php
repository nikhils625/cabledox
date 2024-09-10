@extends('layouts.authentication')
@section('title', 'Forgot Password')

@section('page-styles')
<link rel="stylesheet" href="{{ asset('assets/vendor/parsleyjs/css/parsley.css') }}">
@stop

@section('content')
<div class="auth_brand">
    <a class="navbar-brand" href="#"><img src="{{ asset('assets/images/icon.svg') }}" width="50" class="d-inline-block align-top mr-2" alt="">Cabledox</a>
</div>
<div class="card">
    <div class="header">
        <p class="lead">{{ __('Reset Password') }}</p>
    </div>
    <div class="body">
        <form id="reset-form" class="form-auth-small" method="POST" action="{{ route('password.update') }}">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">

            <div class="form-group c_form_group">
                <label for="email">{{ __('E-Mail Address') }}</label>
                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus placeholder="Enter your email address">

                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="form-group c_form_group">
                <label for="password">{{ __('Password') }}</label>
                <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" required autocomplete="new-password" placeholder="Enter your new password">

                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="form-group c_form_group">
                <label for="password-confirm">{{ __('Confirm Password') }}</label>
                <input type="password" name="password_confirmation" id="password-confirm" class="form-control" required autocomplete="new-password" placeholder="Enter your confirm password">
            </div>
            <button type="submit" class="btn btn-dark btn-lg btn-block">{{ __('Reset Password') }}</button>
            <div class="bottom"></div>
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
        $('#reset-form').parsley();
    });
</script>
@stop