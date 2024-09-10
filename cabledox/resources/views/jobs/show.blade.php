@extends('layouts.master')
@section('title', 'View Job')

@section('page-styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/multi-select/css/bootstrap-select.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/dropify/css/dropify.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/sweetalert/sweetalert.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/light-gallery/css/lightgallery.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/jquery-datatable/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/jquery-datatable/fixedeader/dataTables.fixedcolumns.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/jquery-datatable/fixedeader/dataTables.fixedheader.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/jquery-ui/jquery-ui.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/vendor/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}">

    <link rel="stylesheet" href="{{asset('assets/highchart/css/highcharts.css')}}">
    <style>
        #job-trackchart-container {
            /*max-width: 900px;*/
            margin: 1em auto;
        }

        .highcharts-treegrid-node-level-1 {
            font-size: 13px;
            font-weight: bold;
            fill: black;
        }
    </style>
@stop

@section('content')
<div class="row clearfix">
    <div class="col-md-12">
        <div class="card">  
            @include('layouts.messages')
            <div class="header"><h2>{{ __('View Job') }}</h2></div>
            <div class="body">
                @php
                    $currentRoute = \Route::currentRouteName();
                @endphp
                <ul class="nav nav-tabs3 white job-menu">
                    <li class="nav-item"><a class="nav-link active show" data-toggle="tab" href="#view-job">View Job</a></li>

                    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#main-cable-list">Main Cable List</a></li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle drawings-dropdown" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Drawings</a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item drawing-image"  data-id="1" data-toggle="tab" href="#single-line-drawing">Single Line Drawing</a>
                            <a class="dropdown-item drawing-image"  data-id="2" data-toggle="tab" href="#schematic-drawing">Schematic Drawing</a>
                            <a class="dropdown-item drawing-image"  data-id="3" data-toggle="tab" href="#location-drawing">Location Drawing</a>
                        </div>
                    </li>
                    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#termination-details">Termination details</a></li>

                    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#area">Area</a></li>

                    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#track-chart">Track Chart</a></li>

                    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#reported-issues">Issues</a></li>
                    @if($job->status == 2)
                        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#download-pdf">Download</a></li>
                    @endif
                </ul>
                <div class="tab-content">
                    <div class="tab-pane show active" id="view-job">
                        <div class="form-group row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-md-4 col-form-label text-md-left"><strong>{{ __('Job Number') }}</strong></label>
                                    <div class="col-md-6 view-details">
                                        <span>{{ $job->job_number }}</span>
                                    </div>
                                </div>
                                 <div class="form-group row">
                                    <label class="col-md-4 col-form-label text-md-left"><strong>{{ __('Postcode') }}</strong></label>
                                    <div class="col-md-6 view-details">
                                        <span>{{ $job->post_code }}</span>
                                    </div>
                                </div>
                                @php
                                    $userIdArr = [];
                                    if(!@$job->jobUsers->isEmpty()) {
                                        $userIdArr = $job->jobUsers->pluck('user_id')->toArray();
                                    }
                                @endphp
                                @if($roleName == 'Admin') 
                                    <div class="form-group row">
                                        <label class="col-md-4 col-form-label text-md-left"><strong>{{ __('Managers') }}</strong></label>
                                        <div class="col-md-6 view-details">
                                            @php
                                                $managerNameArr = [];
                                            @endphp
                                            @foreach($managers as $key => $manager)
                                                @if(in_array($manager->id, $userIdArr))
                                                    @php
                                                        $managerNameArr[] = $manager->first_name .' '. $manager->last_name;
                                                    @endphp
                                                @endif
                                            @endforeach
                                            @php
                                                $managerNames = implode(', ', $managerNameArr);
                                            @endphp
                                            <span>{{$managerNames}}</span>
                                        </div>
                                    </div>
                                @endif

                                @if(in_array($roleName, ['Admin', 'Manager']))
                                    <div class="form-group row">
                                        <label for="supervisors_user_id" class="col-md-4 col-form-label text-md-left"><strong>{{ __('Supervisor') }}</strong></label>
                                        <div class="col-md-6">
                                            @php
                                                $superVisorNameArr = [];
                                            @endphp
                                            @foreach($superVisors as $key => $superVisor)
                                                @if(in_array($superVisor->id, $userIdArr))
                                                    @php
                                                        $superVisorNameArr[] = $superVisor->first_name .' '. $superVisor->last_name;
                                                    @endphp
                                                @endif
                                            @endforeach
                                            @php
                                                $superVisorNames = implode(', ', $superVisorNameArr);
                                            @endphp
                                            <span>{{$superVisorNames}}</span>
                                        </div>
                                    </div>
                                @endif

                                @if(in_array($roleName, ['Admin', 'Manager', 'Supervisor'])) 
                                    <div class="form-group row">
                                        <label for="supervisors_user_id" class="col-md-4 col-form-label text-md-left"><strong>{{ __('Electrician') }}</strong></label>
                                        <div class="col-md-6">
                                            @php
                                                $electricianNameArr = [];
                                            @endphp
                                            @foreach($electricians as $key => $electrician)
                                                @if(in_array($electrician->id, $userIdArr))
                                                    @php
                                                        $electricianNameArr[] = $electrician->first_name .' '. $electrician->last_name;
                                                    @endphp
                                                @endif
                                            @endforeach
                                            @php
                                                $electricianNames = implode(', ', $electricianNameArr);
                                            @endphp
                                            <span>{{$electricianNames}}</span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-md-4 col-form-label text-md-left"><strong>{{ __('Site Name') }}</strong></label>
                                    <div class="col-md-6 view-details">
                                        <span>{{ $job->site_name }}</span>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-4 col-form-label text-md-left"><strong>{{ __('Address') }}</strong></label>
                                    <div class="col-md-6 view-details">
                                        <span>{{ $job->address }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <a href="{{route('jobs.index')}}" class="btn btn-default" title="Back">{{ __('Back') }}</a>
                            @if($job->status != 2)
                                @can('jobs.edit')
                                    <a class="btn btn-primary" href="{{ route('jobs.edit', $job->id) }}"> Edit
                                    </a>
                                @endcan
                            @endif
                        </div> 
                    </div>

                    <div class="tab-pane" id="main-cable-list">
                        @php 
                            $isEditable = 0;
                        @endphp
                        @include('jobs.job-cables.index', ['isEditable' => $isEditable])
                    </div>

                    <div class="tab-pane" id="single-line-drawing"> 
                        <h6>Single Line Drawing</h6>      
                        @php 
                            $drawingType = 1;
                        @endphp                     
                        @include('jobs.drawings.view-single-line', ['getJobDrawing' => $getJobDrawing])
                    </div>

                    <div class="tab-pane" id="schematic-drawing">
                        <h6>Schematic Drawing</h6>
                        @php 
                            $drawingType = 2;
                        @endphp        
                        @include('jobs.drawings.view-schematic', ['getJobDrawing' => $getJobDrawing])
                    </div>

                    <div class="tab-pane" id="location-drawing">
                        <h6>Location Drawing</h6>
                        @php 
                            $drawingType = 3;
                        @endphp 
                        @include('jobs.drawings.view-location', ['getJobDrawing' => $getJobDrawing])
                    </div>

                    <div class="tab-pane" id="termination-details">
                        @include('jobs.termination-details.add', ['isEditable' => 0]) 
                    </div>

                    <div class="tab-pane" id="area">
                        @include('jobs.area-of-work.index', ['isEditable' => 0])
                    </div>

                    <div class="tab-pane" id="track-chart">
                        <div class="row clearfix">
                            @php
                                // Creates DateTime objects
                                $startDate = date_create($job->created_at);
                                $endDate   = date_create($job->closed_at);
                                  
                                // Calculates the difference between DateTime objects
                                $interval = date_diff($startDate, $endDate);

                                // Display the result
                                $dateDiff = $interval->format('%a');
                            @endphp
                            <div class="col-md-12">
                                <div id="job-trackchart-container"></div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane" id="reported-issues">
                        @include('jobs.reported-issue.index', ['job' => $job])
                    </div>

                    @if($job->status == 2)
                        @php
                            $cables = collect($job->jobCables);

                            $jobsDir   = \Config::get('constants.uploadPaths.viewJobs');
                            $pdfPath   = \Config::get('constants.uploadPaths.jobCablePdf');
                            $jobNumber = $job->job_number;
                            $pdfDir    = $jobsDir . $jobNumber.DIRECTORY_SEPARATOR.$pdfPath;
                        @endphp
                        <div class="tab-pane" id="download-pdf">
                            <div class="row clearfix">
                                <div class="col-md-12">
                                    <label class="col-md-12 col-form-label text-md-left"><strong>{{ __('Click to download') }}</strong></label>
                                    @foreach($cables as $key => $cable)
                                        <div class="form-group">
                                            @php
                                                $cableName = $cable->cable_id;
                                                if($cable->custom_id) {
                                                    $cableName .= "/".$cable->custom_id;
                                                }
                                                if($cable->unique_code) {
                                                    $cableName .= "/".$cable->unique_code;
                                                }

                                                $cableFrom = ' From: "'.$cable->jobCableFrom->jobLocation->location_name.'"';

                                                $cableTo = ' To: "'.$cable->jobCableTo->jobLocation->location_name.'"';

                                                $cablePdfName = $cableName . $cableTo . $cableFrom;

                                                $pathToImage = base64_encode($pdfDir.$cable->file_name);
                                            @endphp
                                            <div class="col-md-10">
                                                <a href="{{route('job.download-job-asset', [$pathToImage])}}" class="text-info">
                                                    <i class="fa fa-file-pdf-o"></i> &nbsp;&nbsp;{{$cablePdfName}}
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
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
                        <a href ="{{ route('job.view-final-check-sheet', $job->id) }}" target="_blank"> <button type="button" class="btn btn-outline-info">{{ __('Fill Final Check Sheet') }}</button> </a>
                        @if($job->status != 2)
                            <button type="button" class="btn btn-outline-success" data-toggle="modal" data-target="#close-job">{{ __('Close Job') }}</button>
                        @endif
                    @endif
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
<!--reported issue comment list -->
<div class="modal fade" id="report-issue-comment" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-modal="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><strong>Report Issue Comment</strong></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <div class="modal-body">
                <div class="htmlToAppendComment">
                    
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
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
    <script src="{{ asset('assets/jquery-ui/jquery-ui.js') }}"></script>

    <script src="{{ asset('assets/vendor/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>

    <script src="{{ asset('assets/vendor/multi-select/js/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('assets/bundles/datatablescripts.bundle.js') }}"></script>
    <script src="{{ asset('assets/vendor/dropify/js/dropify.js') }}"></script>
    <script src="{{ asset('assets/vendor/sweetalert/sweetalert.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/light-gallery/js/lightgallery-all.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/light-gallery/js/mousewheel.min.js') }}"></script>
@stop

@section('page-script')
    <script src="{{ asset('assets/js/pages/forms/dropify.js') }}"></script>   
    <script src="{{ asset('assets/js/pages/medias/image-gallery.js') }}"></script>
    <script src="{{asset('assets/highchart/js/highcharts-gantt.js')}}"></script>

    <script type="text/javascript">
        /* listing of main job cables */
        var listingsTable = drawTable();

        function drawTable() {
            return $('.job-cables-datatable').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                /*ajax: "{{ route('job-cables.index', $job->id) }}",*/
                ajax: {
                    url: "{{route('job-cables.index', $job->id)}}",
                    data: function (d) {
                        d.is_editable = 0;
                    }
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'cable_id', name: 'cable_id'},
                    {data: 'to', name: 'to'},
                    {data: 'from', name: 'from'},
                    {data: 'size', name: 'size'},
                    {data: 'cores', name: 'cores'},
                    {data: 'cable_type', name: 'cable_type'},
                    {data: 'cable_id_type', name: 'cable_id_type'},
                    {data: 'description', name: 'description'},
                    {
                        data: 'action', 
                        name: 'action', 
                        orderable: true, 
                        searchable: true
                    },
                ]
            });
        }

        /*delete main job cable*/
        $(function () {
            $(document).on('click', '.job-cables', function() {
                var job_cables_id = $(this).data('id');
                var url           = $(this).data('url');
                var _token        = '{{csrf_token()}}';
                var _job_id       = '{{$job->id}}';

                if(job_cables_id != '') {
                    $.ajax({
                        type: "GET",
                        url : url,
                        data: {'_token' : _token, 'job_id': _job_id},
                            success: function(res) {
                                if(res.status == 1) {
                                    $('.htmlToAppend').html(res.data.content);

                                    setTimeout(() => {
                                        $('.selectpicker').selectpicker();
                                    }, 150);
                                } else {
                                    $('.htmlToAppend').html('');
                                    swal("Error:", res.message, "error");
                                }
                            }
                        });
                } else {
                    $('.htmlToAppend').html('');
                }
            });
        });

        /* area of work list*/
        var listingsAreaOfWork = drawTableArea(); 

        var status_type = '';

        var status_val = '';

        function drawTableArea() {
            return $('.job-area-datatable').DataTable({
                processing: true,
                serverSide: true,
                //ajax: "{{ route('area-of-work.list') }}",
                ajax: {
                    url: "{{ route('area-of-work.list',$job->id) }}",
                    data: function (d) {
                        d.to_location = $('#to-work-area').val();
                        d.from_location  = $('#from-work-area').val();
                        d.status = status_val;
                        d.type = status_type;
                    }
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'cable_id', name: 'cable_id'},
                    {data: 'to', name: 'to'},
                    {data: 'from', name: 'from'},
                    {data: 'size', name: 'size'},
                    {data: 'cores', name: 'cores'},
                    {data: 'installed-status', name: 'installed-status',
                        render: function( installed, type, row ){
                            var status_checked = 'fa-times-circle text-red';
                            if(installed == 1) {
                                status_checked = 'fa-check-circle text-green';
                            }
                            return '<span class="fa '+status_checked+'"></span>';
                        }
                    },

                    {data: 'checklist-status', name: 'checklist-status',
                        render: function( checklist, type, row ){
                            var status_checked = 'fa-times-circle text-red';
                            if(checklist == 1) {
                                status_checked = 'fa-check-circle text-green';
                            }
                            return '<span class="fa '+status_checked+'"></span>';
                        }
                    },

                    {data: 'test_result-status', name: 'test_result-status',
                        render: function( test_result, type, row ){
                            var status_checked = 'fa-times-circle text-red';
                            if(test_result == 1) {
                                status_checked = 'fa-check-circle text-green';
                            }
                            return '<span class="fa '+status_checked+'"></span>';
                        }
                    },
                   
                ]
            });
        }

        $(document).on("change",'#to-work-area',function(){
            listingsAreaOfWork.draw();
        });

        $(document).on("change",'#from-work-area',function(){
            listingsAreaOfWork.draw();
        });

        $(document).on("change",'.status',function(){
            status_type = $(this).children(":selected").data("type");
            status_val  = $(this).val();
            listingsAreaOfWork.draw();
        });

        /*termination deatils */
        $(function () {

            $(document).on('change', '#cable_id', function() {
                var _val = $(this).val();   
                var _token  = '{{csrf_token()}}';

                $('.core-details-container').html('');
                if(_val != '') {
                    $.ajax({
                        type: "POST",
                        url: "{{ route('job.get-job-cable-locations') }}",
                        data: {'_token' : _token, 'job_cable_id': _val},
                        success: function(res){
                            console.log(res);
                            if(res.status == 1) {
                                var _html = '<option value="">-Select-</option>';

                                $.each(res.locations, function(k, v) {
                                    _html += '<option value="'+v.id+'">'+v.job_location.location_name+'</option>'; 

                                });

                                $('#termination_location_id').html(_html);
                            } else {
                                swal("Error:", res.message, "error");
                            }
                        }
                    });            
                } else {
                    $('#termination_location_id').html('<option value="">-Select-</option>');
                }
            });

            $(document).on('change', '#termination_location_id', function() {
                var _val         = $(this).val();
                var job_id       = '{{$job->id}}';
                var job_cable_id = $('#cable_id').val();
                var _token       = '{{csrf_token()}}';
                var is_editable  = 0;

                if(_val != '') {
                    $.ajax({
                        type: "POST",
                        url: "{{ route('job.get-termination-details') }}",
                        data: {'_token' : _token, 'job_id': job_id, 'job_cable_id': job_cable_id, 'location_id': _val, 'is_editable': is_editable},
                        success: function(res){
                            if(res.status == 1) {
                                $('.core-details-container').html(res.data.content);
                            } else {
                                $('.core-details-container').html('');
                                swal("Error:", res.message, "error");
                            }
                        }
                    });            
                } else {
                    $('.core-details-container').html('');
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
        });   

        /*reported-issue-listing*/
        var listingReportedIssue = ReportedIssueTable();

        function ReportedIssueTable() {
            return $('.reported-issue-datatable').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                ajax: "{{ route('report-issue.list', $job->id) }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'description', name: 'description'},
                    {data: 'priority', name: 'priority'},
                    {data: 'status', name: 'status',
                        render: function( status, type, row ){
                            var status_checked = 'fa-times-circle text-red';
                            if(row.status == 1) {
                                status_checked = 'fa-check-circle text-green';
                            }
                            return '<span class="fa '+status_checked+'"></span>';
                        }
                    },
                    {data: 'comment', name: 'comment'},
                ]
            });
        }

        /* add comment on reported issue*/
        $(document).on('click', '.report-issue-comment', function() {
            var report_issue_id = $(this).data('id');
            var url           = $(this).data('url');
            var _token        = '{{csrf_token()}}';
            var is_editable   = 0;
 
            if(report_issue_id != '') {

                $.ajax({
                    type: "GET",
                    url : url,
                    data: {'_token' : _token, 'report_issue_id': report_issue_id, 'is_editable': is_editable},
                        success: function(res) {
                            if(res.status == 1) {
                                $('.htmlToAppendComment').html(res.data.content);
                            } else {
                                $('.htmlToAppendComment').html('');
                                swal("Error:", res.message, "error");
                            }
                        }
                    });
            } else {
                $('.htmlToAppendComment').html('');
            }
        });

        $(function() {
            var today = new Date(),
                day = 1000 * 60 * 60 * 24;

            var closeJobDate = "{{$job->closed_at}}";
            var startDate    = new Date("{{$job->created_at}}");
            var endDate      = today;
            if(closeJobDate != '') {
                endDate   = new Date("{{$job->closed_at}}");
                
            }

            // Set to 00:00:00:000 today
            today.setUTCHours(0);
            today.setUTCMinutes(0);
            today.setUTCSeconds(0);
            today.setUTCMilliseconds(0);

            // THE CHART
            Highcharts.ganttChart('job-trackchart-container', {
                chart: {
                    styledMode: true
                },
                xAxis: {
                    min: startDate.getTime() - (5 * day),
                    max: endDate.getTime() + (5 * day)
                },
                tooltip: {
                    xDateFormat: '%A %e %b %Y, %H:%M'
                },
                credits: {
                    enabled: false
                },
                series: [{
                    name: '{{$job->job_number .' + '. $job->site_name}}',
                    data: [{
                        name: '{{$job->job_number .' + '. $job->site_name}}',
                        id: '{{$job->job_number .' + '. $job->site_name}}',
                        start: startDate.getTime(),
                        end: endDate.getTime()
                    }]
                }]
            });
        });

        $(function() {
            var path =  window.location.hash;
            if(path != '') {
                $('.job-menu li a').each(function() {
                  var href = $(this).attr('href');     
                    if (href === path) {
                        $(this).addClass('active');               
                    } else {
                        $(this).removeClass('active')
                    }
                });

                var drawingsMenuArr = ['#single-line-drawing', '#schematic-drawing', '#location-drawing'];
                if(jQuery.inArray(path, drawingsMenuArr) !== -1) {
                    $('a.drawings-dropdown').addClass('active');
                }

                $(".tab-pane").each(function(){
                    var id = '#'+$(this).attr("id"); 
                    if (id === path) {
                        $(this).addClass('active');
                    } else {
                        $(this).removeClass('active');
                    }
                });
            }
        });
    </script>
@stop