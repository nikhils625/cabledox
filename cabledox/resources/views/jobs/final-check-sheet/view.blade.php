@extends('layouts.master')
@section('title', 'Final Check Sheet') 

@section('page-styles')
<link rel="stylesheet" href="{{ asset('assets/vendor/sweetalert/sweetalert.css') }}">
<!--[if lt IE 9]><script src="{{ asset('assets/vendor/signature-pad/flashcanvas.js') }}"></script><![endif]-->
<style>
    .hidden {
        display: none !important;
    }
    .textarea{
        width: 100%;
        resize: none;
    }
</style>
@stop

@section('content')
<div class="row clearfix">
    <div class="col-md-12">
        <div class="card"> 
            @include('layouts.messages')

            <div class="header"><h2>{{ __('Final Check Sheet') }}</h2></div>
            <div class="body">
                <ul class="nav nav-tabs3 white job-menu">
                    <li class="nav-item"><a class="nav-link"  href="{{ route('jobs.show', $job->id) }}/#view-job">View Job</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('jobs.show', $job->id) }}/#main-cable-list">Main Cable List</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Drawings</a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item drawing-image"  data-id="1"  href="{{ route('jobs.show', $job->id) }}/#single-line-drawing">Single Line Drawing</a>
                            <a class="dropdown-item drawing-image"  data-id="2" href="{{ route('jobs.show', $job->id) }}/#schematic-drawing">Schematic Drawing</a>
                            <a class="dropdown-item drawing-image"  data-id="3"  href="{{ route('jobs.show', $job->id) }}/#location-drawing">Location Drawing</a>
                        </div>
                    </li>

                    <li class="nav-item"><a class="nav-link" href="{{ route('jobs.show', $job->id) }}/#termination-details">Termination details</a></li>

                    <li class="nav-item"><a class="nav-link" href="{{ route('jobs.show', $job->id) }}/#area">Area</a></li>

                    <li class="nav-item"><a class="nav-link" href="{{ route('jobs.show', $job->id) }}/#track-chart">Track Chart</a></li>

                    <li class="nav-item"><a class="nav-link" href="{{ route('jobs.show', $job->id) }}/#reported-issues">Issues</a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane show active" id="final-check-sheet-details">
                        <div class="form-group row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label for="job_number" class="col-md-4 col-form-label text-md-right">{{ __('Job Number') }}<span class="text-danger"> *</span></label>
                                    <div class="col-md-6">
                                        <input type="text" name="job_number" id="job_number" class="form-control @error('job_number') is-invalid @enderror" value="{{ $job->job_number }}" required autocomplete="job_number" readonly>
                                        @error('job_number')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="area_inspected" class="col-md-4 col-form-label text-md-right">{{ __('Area Inspected') }}<span class="text-danger"> *</span></label>
                                    <div class="col-md-6">
                                        <select name="area_inspected" id="area_inspected" class="form-control @error('area_inspected') is-invalid @enderror" required>
                                            <option value="">-Select-</option>
                                            @foreach($jobCableLocations as $key => $cableLocation)
                                                <option value="{{$cableLocation->id}}">{{$cableLocation->jobLocation->location_name}}</option>
                                            @endforeach
                                        </select>
                                        @error('area_inspected')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label for="site_name" class="col-md-6 col-form-label text-md-right">{{ __('Site Name') }}<span class="text-danger"> *</span></label>
                                    <div class="col-md-6">
                                        <input type="text" name="site_name" id="site_name" class="form-control @error('site_name') is-invalid @enderror" value="{{ $job->site_name }}" required autocomplete="site_name" readonly>
                                        @error('site_name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="cable_id" class="col-md-6 col-form-label text-md-right">{{ __('Cable Id Used In Area') }}<span class="text-danger"> *</span></label>
                                    <div class="col-md-6">
                                        <select name="cable_id" id="cable_id" class="form-control @error('cable_id') is-invalid @enderror" required>
                                            <option value="">-Select-</option>
                                        </select>
                                        @error('cable_id')
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
                                <div class="final-check-sheet-container"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <a href="{{route('jobs.index')}}" class="btn btn-default" title="Back">{{ __('Back') }}</a>
                        </div>
                    </div>
                    <div class="form-group m-t-10 jobs-test">
                        @if($roleName == 'Admin' || $roleName == 'Manager' ||$roleName == 'Supervisor')
                            <button type="button" class="btn btn-outline-info" data-toggle="modal" data-target="#fill-checklist">{{ __('Fill Checklist') }}</button>
                        @endif

                        <button type="button" class="btn btn-outline-info" data-toggle="modal" data-target="#fill-test-results">{{ __('Fill Test Results') }}</button>

                        @if($job->status != 2)
                            <button type="button" class="btn btn-outline-info" data-toggle="modal" data-target="#report-an-issue">{{ __('Report An Issue') }}</button>
                        @endif

                        @if($roleName == 'Admin' || $roleName == 'Manager')
                            @if($job->status != 2)
                                <button type="button" class="btn btn-outline-success" data-toggle="modal" data-target="#close-job">{{ __('Close Job') }}</button>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--Check list -->
<div class="modal fade" id="fill-checklist" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-modal="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><strong>Check List</strong></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <div class="modal-body">
                @include('jobs.checklist.add', ['job' => $job, 'jobCableOptions' => $jobCableOptions])
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!--Test Results -->
<div class="modal fade" id="fill-test-results" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-modal="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><strong>Test Results</strong></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <div class="modal-body">
                <div class="form-group row">
                    <div class="col-md-12">
                        <div class="form-group row">
                            <label for="job_cable_id" class="col-md-3 col-form-label text-md-left">{{ __('Cable') }}<span class="text-danger"> *</span></label>
                            <div class="col-md-6">
                                <select name="job_cable_id" id="job_cable_id" class="form-control @error('job_cable_id') is-invalid @enderror" required>
                                    <option value="">-Select-</option>
                                    @foreach($jobCableOptions as $key => $option)
                                        <option value="{{$option['id']}}">{{ $option['cable_id'] }} / {{ $option['custom_id'] }}</option>
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
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
@if($job->status != 2)
<!--report-an-issue -->
<div class="modal fade" id="report-an-issue" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-modal="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><strong>Report an Issue</strong></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route ('report-issue.save',$job->id) }}" data-parsley-validate novalidate> 
                @csrf
                    <div class="form-group row">
                        <div class="col-md-12">
                            <div class="form-group row">
                                <label for="job_cable_id" class="col-md-4 col-form-label text-md-left">{{ __('Job Number:') }}</label>

                                <div class="col-md-8">
                                    <input type="text" class="form-control" value="{{ $job->job_number }} + {{ $job->site_name }}" readonly> 
                                    
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="location_id" class="col-md-4 col-form-label text-md-left">{{ __('Location:') }}</label>
                                <div class="col-md-8">
                                    <select name="location_id" id="location_id" class="form-control @error('location_id') is-invalid @enderror" required>
                                    <option value="">-Select-</option>
                                    @foreach($jobCableLocations as$key => $cableLocation)
                                    <option value="{{$cableLocation->id}}">{{$cableLocation->jobLocation->location_name}}</option>
                                    @endforeach         
                                    </select>
                                    @error('location_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="description" class="col-md-4 col-form-label text-md-left">{{ __('Describe
                                your issue:') }}</label>
                                <div class="col-md-8">
                                    <textarea class="form-control"name="description" id="description" required placeholder="Please enter description "></textarea>
                                    @error('description')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="priority" class="col-md-4 col-form-label text-md-left">{{ __('Priority:') }}</label>
                                <div class="col-md-8">
                                    <select name="priority" id="priority" class="form-control @error('priority') is-invalid @enderror" required>
                                        <option value="">-Select-</option>
                                        <option value="0">Low</option>
                                        <option value="1">Normal</option>
                                        <option value="2">Medium</option>
                                        <option value="3">High</option>
                                    </select>
                                    @error('priority')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary theme-bg">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endif
@if($job->status != 2)
<!--Close Job Modal -->
<div class="modal fade" id="close-job" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-modal="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><strong>Close Job</strong></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <form method="POST" action="{{route('job.close', $job->id)}}" data-parsley-validate novalidate> 
                <div class="modal-body">
                    @csrf
                    <div class="form-group row">
                        <div class="col-md-12">
                            <div class="form-group row">
                                <label for="job_number" class="col-md-4 col-form-label text-md-right">{{ __('Job Number') }}<span class="text-danger"> *</span></label>
                                <div class="col-md-8">
                                    <input type="text" name="job_number" class="form-control" value="{{$job->job_number}} + {{$job->site_name}}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <div class="form-group row">
                                <label for="checklist" class="col-md-4 col-form-label text-md-right">{{ __('Checklist') }}<span class="text-danger"> *</span></label>
                                <div class="col-md-8">
                                    <label class="fancy-checkbox">
                                        <input type="checkbox" name="checklist" id="checklist" value="1" required>
                                        <span>&nbsp;</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <div class="form-group row">
                                <label for="final-check-sheet" class="col-md-4 col-form-label text-md-right">{{ __('Final Check Sheet') }}<span class="text-danger"> *</span></label>
                                <div class="col-md-8">
                                    <label class="fancy-checkbox">
                                        <input type="checkbox" name="final_check_sheet" id="final-check-sheet" value="1" required>
                                        <span>&nbsp;</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <div class="form-group row">
                                <label for="termination-details-box" class="col-md-4 col-form-label text-md-left">{{ __('Termination Details') }}<span class="text-danger"> *</span></label>
                                <div class="col-md-8">
                                    <label class="fancy-checkbox">
                                        <input type="checkbox" name="termination_details" id="termination-details-box" value="1" required>
                                        <span>&nbsp;</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary theme-bg">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@stop

@section('vendor-script')
<script src="{{ asset('assets/vendor/sweetalert/sweetalert.min.js') }}"></script>
@stop

@section('page-script')
<script type="text/javascript">
$(function () {

    $(document).on('change', '#area_inspected', function() {
        var _val    = $(this).val();
        var job_id  = "{{$job->id}}";
        var _token  = '{{csrf_token()}}';

        $('.final-check-sheet-container').html('');
        if(_val != '') {
            $.ajax({
                type: "POST",
                url: "{{ route('job.get-cable-details') }}",
                data: {'_token' : _token, 'job_id': job_id, 'cable_location_id': _val},
                success: function(res){
                    if(res.status == 1) {
                        var _html = '<option value="">-Select-</option>';
                        $.each(res.job_cables, function(k, v) {
                            _html += '<option value="'+v.id+'">'+v.cable_id+'</option>'; 
                        });

                        $('#cable_id').html(_html);
                    } else {
                        swal("Error:", res.message, "error");
                    }
                }
            });            
        } else {
            $('#cable_id').html('<option value="">-Select-</option>');
        }
    });

    $(document).on('change', '#cable_id', function() {
        var _val           = $(this).val();
        var area_inspected = $('#area_inspected').val();
        var job_id         = "{{$job->id}}";
        var _token         = '{{csrf_token()}}';
        var is_editable    = 0;

        $('.final-check-sheet-container').html('');
        if(_val != '') {
            $.ajax({
                type: "POST",
                url: "{{ route('job.get-check-sheet-details') }}",
                data: {'_token' : _token, 'job_id': job_id, 'area_inspected': area_inspected, 'cable_id': _val, 'is_editable': is_editable},
                success: function(res) {
                    if(res.status == 1) {
                        $('.final-check-sheet-container').html(res.data.content);
                    } else {
                        $('.final-check-sheet-container').html('');
                        swal("Error:", res.message, "error");
                    }
                }, error: function(xhr, ajaxOptions, thrownError) {
                    var xhrRes = xhr.responseJSON;
                    swal("Error downloading!", xhrRes.message, "error");
                }
            });            
        } else {
            $('.final-check-sheet-container').html('');
        }
    });

    $(document).on('change', '#cable', function() {
        var _val    = $(this).val();
        var _job_id = "{{$job->id}}";
        var _token  = '{{csrf_token()}}';
        var is_editable = 0;

        if(_val != '') {
            $.ajax({
                type: "POST",
                url: "{{ route('job.get-job-checklist-details') }}",
                data: {'_token' : _token, 'job_id': _job_id, 'cable_id': _val, 'is_editable': is_editable},
                success: function(res) {
                    if(res.status == 1) {
                        $('.checklist-detail-container').html(res.data.content);
                    } else {
                        swal("Error:", res.message, "error");
                        $('.checklist-detail-container').html('');
                    }
                }, error: function(xhr, ajaxOptions, thrownError) {
                    var xhrRes = xhr.responseJSON;
                    swal("Error:", xhrRes.message, "error");
                }
            });            
        } else {
            $('.checklist-detail-container').html('');
        }
    });

    /* test result */
    $(document).on('change', '#job_cable_id', function() {
        var job_cable_id = $(this).val();
        var job_id       = '{{$job->id}}';
        var _token       = '{{csrf_token()}}';
        var is_editable  = 0;

        if(job_cable_id != '') {
            $.ajax({
                type: "POST",
                url: "{{ route('jobs.get-test-results') }}",
                data: {'_token' : _token, 'job_id': job_id, 'job_cable_id': job_cable_id, 'is_editable': is_editable},
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