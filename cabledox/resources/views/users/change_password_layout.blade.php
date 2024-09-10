@extends('layouts.master')
@section('title', 'Change Password')

@section('content')
<div class="row clearfix">
    <div class="col-md-12">
        <div class="card"> 
            @include('layouts.messages')
            <div class="header"><h2>{{ __('Change Password') }}</h2></div>
            <div class="body">
                @include('users.change_password')
            </div>
        </div>
    </div>
</div>
@stop