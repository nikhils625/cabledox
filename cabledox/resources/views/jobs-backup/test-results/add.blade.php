@extends('layouts.master')
@section('title', 'Test Results')

@section('page-styles')
<link rel="stylesheet" href="{{ asset('assets/vendor/sweetalert/sweetalert.css') }}">
@stop

@section('content')
    <div class="body">
        <ul class="nav nav-tabs3 white">
            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#edit-job">Edit Job</a></li>

            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#main-cable-list">Main Cable List</a></li>

            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Drawings</a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item"  data-toggle="tab" href="#single-line-drawing">Single Line Drawing</a></li>
                    <li><a class="dropdown-item" data-toggle="tab" href="javascript:void(0);">Schematic Drawing</a></li>
                    <li><a class="dropdown-item" data-toggle="tab" href="javascript:void(0);">Location Drawing</a></li>   
                </ul>
            </li>

            <li class="nav-item"><a class="nav-link active show" data-toggle="tab" href="#termination-details">Termination details</a></li>

            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#area">Area</a></li>

            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#track-chart">Track Chart</a></li>

            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#issues">Issues</a></li>

            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#test-result">Test Result</a></li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane show active" id="test-result">
                <form method="POST" action="{{ route ('test-results.save',$job->id) }}" data-parsley-validate novalidate> 
                    @csrf
                    <div class="form-group row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label for="job_cable_id" class="col-md-2 col-form-label text-md-left">{{ __('Cable') }}<span class="text-danger"> *</span></label>
                                <div class="col-md-6">
                                    <select name="job_cable_id" id="job_cable_id" class="form-control @error('job_cable_id') is-invalid @enderror" required>
                                        <option value="">-Select-</option>
                                        @foreach($jobCableOptions as $key => $option)
                                            <option value="{{$option['id']}}">{{$option['cable_id']}}</option>
                                        @endforeach
                                    </select>
                                    @error('job_cable_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <div class="details-container">&nbsp;</div>
                        </div>
                    </div>
                    <div class="form-group">
                        <a href="{{route('job-cables.index')}}" class="btn btn-default" title="Back">{{ __('Back') }}</a>

                        <button type="submit" class="btn btn-primary">
                            {{ __('Submit') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('vendor-script')
<script src="{{ asset('assets/vendor/sweetalert/sweetalert.min.js') }}"></script>
@stop

@section('page-script')
<script type="text/javascript">
$(function () {
    $(document).on('change', '#job_cable_id', function() {
        var job_cable_id = $(this).val();
        var job_id       = '{{$job->id}}';
        var _token       = '{{csrf_token()}}';

        if(job_cable_id != '') {
            $.ajax({
                type: "POST",
                url: "{{ route('jobs.get-test-results') }}",
                data: {'_token' : _token, 'job_id': job_id, 'job_cable_id': job_cable_id},
                success: function(res){
                    console.log(res);
                    if(res.status == 1) {
                        $('.details-container').html(res.data.content);
                    } else {
                        $('.details-container').html('');
                        swal("Error:", res.message, "error");
                    }
                }
            });            
        } else {
            $('.details-container').html('');
        }
    });

});
</script>
@stop