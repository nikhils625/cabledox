@extends('layouts.master')
@section('title', 'Edit User')

@section('content')
<div class="row clearfix">
    <div class="col-md-12">
        <div class="card">  
            @include('layouts.messages')
            <div class="header"><h2>{{ __('View User') }}</h2></div>
            <div class="body">
                <form method="POST" action="javascript:void(0);" data-parsley-validate novalidate>
                    <div class="form-group row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label for="first_name" class="col-md-4 col-form-label text-md-left"><strong>{{ __('First Name') }}</strong></label>
                                <div class="col-md-6 view-details">
                                    <span>{{ $user->first_name }}</span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="email" class="col-md-4 col-form-label text-md-left"><strong>{{ __('Email') }}</strong></label>
                                <div class="col-md-6 view-details">
                                    <span>{{ $user->email }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label for="last_name" class="col-md-4 col-form-label text-md-left"><strong>{{ __('Last Name') }}</strong></label>
                                <div class="col-md-6 view-details">
                                    <span>{{ $user->last_name }}</span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="role_id" class="col-md-4 col-form-label text-md-left"><strong>{{ __('Role') }}</strong></label>
                                <div class="col-md-6 view-details">
                                    @php
                                        $roleName = '';
                                        if(!@$user->roles->isEmpty() && @$user->roles->count() == 1) {
                                            $roleName = @$user->roles[0]->name;
                                        }
                                    @endphp
                                    <span>{{ @$roleName }}</span>
                                </div>
                            </div>
                       </div> 
                    </div>                       
                    <div class="form-group">
                        <a href="{{route('users.index')}}" class="btn btn-default" title="Back">{{ __('Back') }}</a>

                        @can('users.edit')
                            <a href="{{route('users.edit', $user->id)}}" class="btn btn-primary">{{__('Edit')}}</a>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@stop