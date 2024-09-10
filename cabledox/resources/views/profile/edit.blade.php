@extends('layouts.master')
@section('title', 'User Profile')

@section('page-styles')
<link rel="stylesheet" href="{{ asset('assets/vendor/dropify/css/dropify.min.css') }}">
@stop

@section('content')
<div class="row clearfix">
    <div class="col-md-12">
        <div class="card">  
            @include('layouts.messages')
            <div class="header"><h2>{{ __('User Profile') }}</h2></div>
            <div class="body">
                <form method="POST" action="{{ route('users.update-profile', $user->id) }}" enctype="multipart/form-data" data-parsley-validate novalidate> 
                    @csrf
                    <div class="form-group row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label for="first_name" class="col-md-4 col-form-label text-md-right">{{ __('First Name') }}<span class="text-danger"> *</span></label>
                                <div class="col-md-6">
                                    <input type="text" name="first_name" id="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ $user->first_name }}" required autocomplete="first_name"  autofocus placeholder="Please enter first name">
                                    @error('first_name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('Email') }}<span class="text-danger"> *</span></label>
                                <div class="col-md-6">
                                    <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ $user->email }}" required autocomplete="email" autofocus placeholder="Please enter email">
                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="phone" class="col-md-4 col-form-label text-md-right">{{ __('Phone') }}<span class="text-danger"> *</span></label>
                                <div class="col-md-6">
                                    <input type="tel" id="phone" class="form-control  @error('phone') is-invalid @enderror" name="phone" value="{{ $user->phone }}" required autocomplete="phone" pattern="[0-9]{10}"  data-parsley-type="digits" data-parsley-type-message="Please enter valid phone number" placeholder="Please enter phone number">
                                    @error('phone')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label for="last_name" class="col-md-4 col-form-label text-md-right">{{ __('Last Name') }}<span class="text-danger"> *</span></label>
                                <div class="col-md-6">
                                    <input type="text" name="last_name" id="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ $user->last_name }}" required autocomplete="last_name" placeholder="Please enter last name">
                                    @error('last_name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            @php
                                $profileImage = null;

                                $imageName = null;
                                if(!empty($user->user_profile)) {
                                    $profileImage = asset(\Config::get('constants.uploadPaths.viewProfileImage') . $user->user_profile);

                                    $imageName = $user->user_profile;
                                }
                            @endphp
                            <div class="form-group row">
                                <label for="file" class="col-md-4 col-form-label text-md-right">{{ __('User Profile') }}<span class="text-danger"> *</span></label>
                                <div class="col-md-6">
                                 <input type="file" class="dropify"data-allowed-file-extensions="jpeg png jpg" name="user_profile" data-default-file="{{$profileImage}}">
                                 <input type="hidden" value="{{$imageName}}" name="user_profile_old"/>
                             </div>
                             @error('user_logo')
                             <span class="invalid-feedback" role="alert">
                                 <strong>{{ $message }}</strong>
                             </span>
                             @enderror
                       </div> 
                    </div>                       
                    <div class="form-group">
                        <a href="{{route('users.index')}}" class="btn btn-default" title="Back">{{ __('Back') }}</a>

                        <button type="submit" class="btn btn-primary">
                            {{ __('Submit') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@stop
@section('vendor-script')
<script src="{{ asset('assets/vendor/dropify/js/dropify.js') }}"></script>
@stop
@section('page-script')
<script src="{{ asset('assets/js/pages/forms/dropify.js') }}"></script>
@stop