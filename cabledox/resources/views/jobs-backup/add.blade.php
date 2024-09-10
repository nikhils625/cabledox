@extends('layouts.master')
@section('title', 'Add Job')

@section('page-styles')
<link rel="stylesheet" href="{{ asset('assets/vendor/multi-select/css/bootstrap-select.min.css') }}">
@stop

@section('content') 
<div class="row clearfix">
    <div class="col-md-12">
        <div class="card">  
            @include('layouts.messages')
            <div class="header"><h2>{{ __('Add Job') }}</h2></div>

            <div class="body">
                <form method="POST" action="{{ route('jobs.store') }}" data-parsley-validate novalidate> 
                    @csrf
                    <div class="form-group row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label for="job_number" class="col-md-4 col-form-label text-md-right">{{ __('Job Number') }}<span class="text-danger"> *</span></label>
                                <div class="col-md-6">
                                    <input type="text" name="job_number" id="job_number" class="form-control @error('job_number') is-invalid @enderror" value="{{ old('job_number') }}" required autocomplete="job_number"  autofocus placeholder="Please enter job number">
                                    @error('job_number')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="post_code" class="col-md-4 col-form-label text-md-right">{{ __('Postcode') }}<span class="text-danger"> *</span></label>
                                <div class="col-md-6">
                                    <input type="text" name="post_code" id="post_code" class="form-control @error('post_code') is-invalid @enderror" value="{{ old('post_code') }}" required autocomplete="post_code" autofocus placeholder="Please enter post code">
                                    @error('post_code')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            
                            @if($role_name == 'Admin')
                            <div class="form-group row">
                                <label for="managers_user_id" class="col-md-4 col-form-label text-md-right">{{ __('Managers') }}<span class="text-danger"> *</span></label>
                                <div class="col-md-6 multiselect_div">
                                        <select id="managers_user_id" data-live-search="true" name="user_id[]" class="selectpicker form-control" multiple required title="Please select"  data-style="btn-info">

                                        @foreach($manager as $managers)
                                            <option value="{{$managers->id}}">{{$managers->first_name}} {{$managers->last_name}}</option>
                                       @endforeach                                       
                                    </select>
                                    
                                    @error('user_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            @endif
                            
                            @if($role_name == 'Admin' || $role_name == 'Manager')
                            <div class="form-group row">
                                <label for="supervisors_user_id" class="col-md-4 col-form-label text-md-right">{{ __('Supervisors') }}<span class="text-danger">*</span></label>
                                <div class="col-md-6 multiselect_div">
                                        <select id="supervisors_user_id" data-live-search="true" name="user_id[]" class="selectpicker form-control" multiple title="Please select" required  data-style="btn-info">

                                        @foreach($superVisor as $superVisors)
                                            <option value="{{$superVisors->id}}">{{$superVisors->first_name}} {{$superVisors->last_name}}</option>
                                       @endforeach                                      
                                    </select>                                    
                                    @error('user_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            @endif
                              
                            @if($role_name == 'Admin' || $role_name == 'Manager' ||$role_name == 'Supervisor')  
                            <div class="form-group row">
                                <label for="electricians_user_id" class="col-md-4 col-form-label text-md-right">{{ __('Electricians') }}<span class="text-danger"> *</span></label>
                                <div class="col-md-6 multiselect_div">
                                    <select id="electricians_user_id" data-live-search="true" name="user_id[]" class="selectpicker form-control" multiple title="Please select" required  data-style="btn-info">

                                        @foreach($electrician as $electricians)
                                            <option value="{{$electricians->id}}">{{$electricians->first_name}} {{$electricians->last_name}}</option>
                                       @endforeach                                       
                                    </select>

                                    @error('user_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label for="site_name" class="col-md-4 col-form-label text-md-right">{{ __('Site Name') }}<span class="text-danger"> *</span></label>
                                <div class="col-md-6">
                                    <input type="text" name="site_name" id="site_name" class="form-control @error('site_name') is-invalid @enderror" value="{{ old('site_name') }}" required autocomplete="site_name" autofocus placeholder="Please enter site name">
                                    @error('site_name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="address" class="col-md-4 col-form-label text-md-right">{{ __('Address') }}<span class="text-danger"> *</span></label>
                                <div class="col-md-6">
                                   <textarea name="address" class="form-control" id="address" placeholder="Please enter address" rows="3" required></textarea>
                                    @error('address')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div> 
                    </div>                       
                                    
                    <div class="form-group">
                        <a href="{{route('jobs.index')}}" class="btn btn-default" title="Back">{{ __('Back') }}</a>
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
<script src="{{ asset('assets/vendor/multi-select/js/bootstrap-select.min.js') }}"></script>
@stop


