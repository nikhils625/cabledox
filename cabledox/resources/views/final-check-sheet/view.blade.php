@extends('layouts.master')
@section('title', 'View Final Check Sheet Questionnaire')

@section('content')

<div class="row clearfix">
    <div class="col-md-12">
        <div class="card"> 
            <div class="header"><h2>{{ __('View Question') }}</h2>
            </div>
            <div class="body">
                <div class="col-md-12">
                    <div class="form-group row">
                        <div class="col-md-12">
                            <div class="form-group row">
                                <label class="col-md-2 col-form-label text-md-left"><strong>{{ __('Question') }}</strong></label>
                                <div class="col-md-10 view-details">
                                    <span>{{ $data->question_name }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <a href="{{route('final-check-sheet.index')}}" class="btn btn-default" title="Back">{{ __('Back') }}</a>
                    @can('final-check-sheet.edit')
                        <a class="btn btn-primary" href="{{ route('final-check-sheet.edit', $data->id) }}"> Edit
                        </a>
                    @endcan
                </div> 
            </div>
        </div>
    </div>
</div>
@stop


 