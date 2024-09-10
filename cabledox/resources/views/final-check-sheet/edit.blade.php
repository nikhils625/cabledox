@extends('layouts.master')
@section('title', 'Edit Final Check Sheet Questionnaire')


@section('content')
<div class="block-header"></div>
<div class="row clearfix">
    <div class="col-md-12"> 
        <div class="card">  
            @include('layouts.messages')
            <div class="header"><h2>{{ __('Edit Question') }}</h2></div>
 
                <div class="body">
                    <form method="POST" action="{{ route('final-check-sheet.update',$data->id) }}"  data-parsley-validate novalidate> 
                    @csrf
                    @method('PUT') 
                        <div class="form-group row">
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label for="question_name" class="col-md-2 col-form-label text-md-right">{{ __('Question') }}<span class="text-danger"> *</span></label>
                                    <div class="col-md-8">
                                        <textarea class="form-control @error('question_name') is-invalid @enderror"" name="question_name"  id= "question_name" rows="4" required>@if($data->question_name){{$data->question_name}}@endif</textarea>
                                    </div>
                                    @error('question_name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div> 
                        </div>    
                        <div class="form-group">
                            <a href="{{route('final-check-sheet.index')}}" class="btn btn-default" title="Back">{{ __('Back') }}</a>
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



