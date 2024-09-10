@extends('layouts.master')
@section('title', 'Add Test Parameters')


@section('content')
<div class="row clearfix">
    <div class="col-md-12"> 
        <div class="card"> 
            @include('layouts.messages')

            <div class="header"><h2>{{ __('Add New Parameter') }}</h2></div>
                <div class="body">
                    <form method="POST" action="{{ route('test-parameters.store') }}" data-parsley-validate novalidate> 
                        @csrf 
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label for="parameter_name" class=" col-md-4 col-form-label text-md-right">{{ __('Parameter') }}<span class="text-danger"> *</span></label>
                                        <div class="col-md-6">
                                            <input id="parameter_name" type="text" class="form-control @error('parameter_name') is-invalid @enderror" name="parameter_name" value="{{ old('parameter_name') }}"  autocomplete="parameter_name"  autofocus placeholder="Please enter parameter name" required>
                                             @error('parameter_name')
                                                <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>    
                                    </div>
                                </div> 
                            </div>                         
                            <div class="form-group">
                                <a href="{{route('test-parameters.index')}}" class="btn btn-default" title="Back">{{ __('Back') }}</a>
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



 