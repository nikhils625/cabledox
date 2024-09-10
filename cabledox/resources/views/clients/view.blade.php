@extends('layouts.master')
@section('title', 'View Client')

@section('content')
<div class="row clearfix">
    <div class="col-md-12">
        <div class="card"> 
            <div class="header"><h2>{{ __('View Client') }}</h2></div>
            <div class="body">
                <div class="form-group row">
                    @php
                        $companyLogo = null;

                        if(isset($data->company_logo)  && !empty($data->company_logo)) {
                            $companyLogo = asset(\Config::get('constants.uploadPaths.viewCompanyLogo') . $data->company_logo);
                        }
                    @endphp
                    <div class="col-md-4">                                   
                        <div class="form-group row">
                            <div class="col-md-12">
                                <img src="{{$companyLogo}}" class="rounded border border-success img-thumbnail">
                            </div>   
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="form-group row">
                            <label class="col-md-5 col-form-label text-md-left"><strong>{{ __('Company Name') }}</strong></label>
                            <div class="col-md-7 view-details">
                                <span>{{ $data->company_name }}</span>
                            </div>
                        </div>                     
                        <div class="form-group row">
                            <label for="company_phone" class="col-md-5 col-form-label text-md-left"><strong>{{ __('Company Phone') }}</strong></label>
                            <div class="col-md-7 view-details">
                                {{ $data->company_phone}}              
                            </div>
                        </div>                       
                        <div class="form-group row">
                            <label for="company_email" class="col-md-5 col-form-label text-md-left"><strong>{{ __('Company Email') }}</strong></label>
                            <div class="col-md-7 view-details">
                                {{ $data->company_email }}
                            </div>
                        </div>                       
                        <div class="form-group row">
                            <label for="no_of_jobs_allocated" class="col-md-5 col-form-label text-md-left"><strong>{{ __('No. Of jobs allocated') }}</strong></label>
                            <div class="col-md-7 view-details">
                                {{ $data->no_of_jobs_allocated}}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="contact_person_name" class="col-md-5 col-form-label text-md-left"><strong>{{ __('Contact Person Name') }}</strong></label>
                            <div class="col-md-7 view-details">
                                {{ $data->users->first_name .' '.$data->users->last_name}}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="phone" class="col-md-5 col-form-label text-md-left"><strong>{{ __('Contact Person Phone') }}</strong></label>
                            <div class="col-md-7 view-details">
                                {{ $data->users->phone }}
                            </div>
                        </div>
                    
                        <div class="form-group row">
                            <label for="email" class="col-md-5 col-form-label text-md-left"><strong>{{ __('Contact Person Email') }}</strong></label>
                            <div class="col-md-7 view-details">
                               {{ $data->users->email }}
                            </div>
                        </div>                      
                    </div> 
                    <div class="form-group">
                        <a href="{{route('clients.index')}}" class="btn btn-default" title="Back">{{ __('Back') }}</a>
                         @can('clients.edit')
                            <a class="btn btn-primary" href="{{ route('clients.edit', $data->id) }}"> Edit </a>
                        @endcan
                    </div>
                </div>                       
           </div>
        </div>
   </div>
</div>
@stop


 