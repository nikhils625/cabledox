@extends('layouts.authentication')
@section('title', 'Forgot Password')

@section('page-styles')
<link rel="stylesheet" href="{{ asset('assets/vendor/parsleyjs/css/parsley.css') }}">
@stop

@section('content')
<div class="auth_brand">
    <a class="navbar-brand" href="#"><img src="{{ asset('assets/images/icon.svg') }}" width="50" class="d-inline-block align-top mr-2" alt="">Cabledox</a>
</div>
<div class="card forgot-pass">
    <div class="header">
        <p class="lead"><strong>Oops</strong>,<br> forgot something?</p>                    
    </div>
    <div class="body">
        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @endif

        <form id="email-form" class="form-auth-small" method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="form-group c_form_group">
                <label for="email">{{ __('Type email to recover password.') }}</label>
                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="Enter your email address">

                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <button type="submit" class="btn btn-dark btn-lg btn-block">{{ __('Send Password Reset Link') }}</button>
            <div class="bottom">
                <span class="helper-text">Know your password? <a href="{{route('login')}}">Login</a></span>
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
        $('#email-form').parsley();
    });
</script>
@stop