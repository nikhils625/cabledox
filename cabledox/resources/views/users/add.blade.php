@extends('layouts.master')
@section('title', 'Add User')

@section('content')
<div class="row clearfix">
    <div class="col-md-12">
        <div class="card"> 
            @include('layouts.messages')

            <div class="header"><h2>{{ __('Add User') }}</h2></div>

            <div class="body">
                <form method="POST" action="{{ route('users.store') }}" data-parsley-validate novalidate> 
                    @csrf
                    <div class="form-group row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label for="first_name" class="col-md-4 col-form-label text-md-right">{{ __('First Name') }}<span class="text-danger"> *</span></label>
                                <div class="col-md-6">
                                    <input type="text" name="first_name" id="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name') }}" required autocomplete="first_name"  autofocus placeholder="Please enter first name">
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
                                    <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="Please enter email">
                                    @error('email')
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
                                    <input type="text" name="last_name" id="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name')}}" required autocomplete="last_name" placeholder="Please enter last name">
                                    @error('last_name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="role_id" class="col-md-4 col-form-label text-md-right">{{ __('Role') }}<span class="text-danger"> *</span></label>
                                <div class="col-md-6">
                                    <select name="role_id" id="role_id" class="form-control @error('role_id') is-invalid @enderror" required>
                                        <option value="">-Select-</option>
                                        @foreach($roles as $key => $role)
                                            <option value="{{$key}}">{{$role}}</option>
                                        @endforeach
                                    </select>
                                    @error('role_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
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