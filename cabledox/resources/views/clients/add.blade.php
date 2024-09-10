@extends('layouts.master')
@section('title', 'Add Client')

@section('page-styles')
<link rel="stylesheet" href="{{ asset('assets/vendor/dropify/css/dropify.min.css') }}">
@stop
@section('content')
<div class="row clearfix">
    <div class="col-md-12"> 
        <div class="card"> 
            @include('layouts.messages')

            <div class="header"><h2>{{ __('Add Client') }}</h2></div>
                <div class="body">
                    <form method="POST" action="{{ route('clients.store') }}"  enctype="multipart/form-data" data-parsley-validate novalidate> 
                        @csrf 
                        <div class="form-group row">
                            <div class="col-md-6">    
                                <div class="form-group row">
                                    <label for="company_name" class="col-md-4 col-form-label text-md-right">{{ __('Company Name') }}<span class="text-danger"> *</span></label>
                                    <div class="col-md-6">
                                         <input id="company_name" type="text" class="form-control @error('company_name') is-invalid @enderror" name="company_name" value="{{ old('company_name') }}" required autocomplete="company_name"  autofocus placeholder="Please enter company name">
                                        @error('company_name')
                                            <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                     
                                <div class="form-group row">
                                    <label for="company_phone" class="col-md-4 col-form-label text-md-right">{{ __('Company Phone') }}<span class="text-danger"> *</span></label>
                                    <div class="col-md-6">
                                        <input id="company_phone" type="tel" class="form-control @error('company_phone') is-invalid @enderror" name="company_phone" value="{{ old('company_phone') }}" 
                                        pattern="[0-9]{10}"  data-parsley-type="digits" required autocomplete="company_phone" data-parsley-type-message="Please enter valid phone number" autofocus placeholder="Please enter company phone">
                                        @error('company_phone')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                        
                                <div class="form-group row">
                                    <label for="company_email" class="col-md-4 col-form-label text-md-right">{{ __('Company Email') }}<span class="text-danger"> *</span></label>
                                    <div class="col-md-6">
                                        <input id="company_email" type="email" class="form-control @error('company_email') is-invalid @enderror" name="company_email" value="{{ old('company_email') }}" required autocomplete="company_email" autofocus placeholder="Please enter company email">
                                        @error('company_email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                        
                                <div class="form-group row">
                                    <label for="no_of_jobs_allocated" class="col-md-4 col-form-label text-md-right">{{ __('No. Of jobs allocated') }}<span class="text-danger"> *</span></label>
                                    <div class="col-md-6">
                                        <input id="no_of_jobs_allocated" type="text" class="form-control @error('no_of_jobs_allocated') is-invalid @enderror" name="no_of_jobs_allocated" value="{{ old('no_of_jobs_allocated') }}" required autocomplete="no_of_jobs_allocated" placeholder="Please enter no of jobs allocated">
                                        @error('no_of_jobs_allocated')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label for="contact_person_name" class="col-md-4 col-form-label text-md-right">{{ __('Contact Person Name') }}<span class="text-danger"> *</span></label>
                                    <div class="col-md-6">
                                        <input type="text" id="contact_person_name" class="form-control  @error('contact_person_name') is-invalid @enderror" name="contact_person_name" value="{{ old('contact_person_name')}}" required autocomplete="first_name" placeholder="Please enter contact name">
                                        @error('contact_person_name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                       
                                <div class="form-group row">
                                    <label for="phone" class="col-md-4 col-form-label text-md-right">{{ __('Contact Person Phone') }}<span class="text-danger"> *</span></label>
                                    <div class="col-md-6">
                                        <input type="tel" id="phone" class="form-control  @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone')}}" required autocomplete="phone" pattern="[0-9]{10}"  data-parsley-type="digits" data-parsley-type-message="Please enter valid phone number" placeholder="Please enter contact phone">
                                        @error('phone')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                        
                                <div class="form-group row">
                                    <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('Contact Person Email') }}<span class="text-danger"> *</span></label>
                                    <div class="col-md-6">
                                        <input id="email" type="email" class="form-control  @error('email') is-invalid @enderror" name="email" value="{{ old('email')}}" required autocomplete="email" placeholder="Please enter contact email">
                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                       
                                <div class="form-group row">
                                    <label for="file" class="col-md-4 col-form-label text-md-right">{{ __('Company Logo') }}<span class="text-danger"> *</span></label>
                                    <div class="col-md-6">
                                        <input type="file" class="dropify" data-allowed-file-extensions="jpeg png jpg" name="company_logo" required>
                                    </div>
                                        @error('company_logo')
                                          <span class="invalid-feedback" role="alert">
                                             <strong>{{ $message }}</strong>
                                          </span>
                                        @enderror
                                </div>
                            </div> 
                        </div>                       
                        <div class="form-group">
                            <a href="{{route('clients.index')}}" class="btn btn-default" title="Back">{{ __('Back') }}</a>
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


 