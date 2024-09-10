@extends('layouts.master')
@section('title', 'View Test Parameters')

@section('content')

<div class="row clearfix">
    <div class="col-md-12">
        <div class="card"> 
            <div class="header"><h2>{{ __('View Parameter') }}</h2></div>
            <div class="body">
                <div class="col-md-12">
                    <div class="form-group row">
                        <div class="col-md-12">
                            <div class="form-group row">
                                <label class="col-md-2 col-form-label text-md-left"><strong>{{ __('Parameter') }}</strong></label>
                                <div class="col-md-10 view-details">
                                    <span>{{ $data->parameter_name }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <a href="{{route('test-parameters.index')}}" class="btn btn-default" title="Back">{{ __('Back') }}</a>
                    @can('test-parameters.edit')
                        <a class="btn btn-primary" href="{{ route('test-parameters.edit', $data->id) }}"> Edit
                        </a>
                    @endcan
                </div> 
            </div>
        </div>
    </div>
</div>
@stop


 