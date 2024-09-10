@extends('layouts.master')
@section('title', 'Edit Job')

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
            <div class="header"><h2>{{ __('Job details') }}</h2></div>
            <div class="body">
                @php
                    $currentRoute = \Route::currentRouteName();
                @endphp
                <ul class="nav nav-tabs3 white job-menu">
                    <li class="nav-item"><a class="nav-link active show" data-toggle="tab" href="#edit-job">Edit Job</a></li>

                    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#main-cable-list">Main Cable List</a></li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle drawings-dropdown" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Drawings</a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item drawing-image" data-id="1" data-toggle="tab" href="#single-line-drawing">Single Line Drawing</a>
                            <a class="dropdown-item drawing-image" data-id="2" data-toggle="tab" href="#schematic-drawing">Schematic Drawing</a>
                            <a class="dropdown-item drawing-image" data-id="3" data-toggle="tab" href="#location-drawing">Location Drawing</a>
                        </div>
                    </li>
                    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#termination-details">Termination details</a></li>

                    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#area">Area</a></li>

                    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#track-chart">Track Chart</a></li>

                    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#reported-issues">Issues</a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane show active" id="edit-job">
                        @include('jobs.edit-job')
                    </div>

                    <div class="tab-pane" id="main-cable-list">
                        @include('jobs.job-cables.index')
                    </div>

                    <div class="tab-pane" id="single-line-drawing"> 
                        <h6>Single Line Drawing</h6>      
                        @php 
                            $drawingType = 1;
                        @endphp                     
                        @include('jobs.drawings.add-single-line', ['getJobDrawing' =>$getJobDrawing])
                    </div>

                    <div class="tab-pane" id="schematic-drawing">
                        <h6>Schematic Drawing</h6>
                        @php 
                            $drawingType = 2;
                        @endphp        
                        @include('jobs.drawings.add-schematic', ['getJobDrawing' =>$getJobDrawing])
                    </div>

                    <div class="tab-pane" id="location-drawing">
                        <h6>Location Drawing</h6>
                        @php 
                            $drawingType = 3;
                        @endphp 
                        @include('jobs.drawings.add-location', ['getJobDrawing' =>$getJobDrawing])
                    </div>

                    <div class="tab-pane" id="termination-details">
                        @include('jobs.termination-details.add') 
                    </div>

                    <div class="tab-pane" id="area">
                        @include('jobs.area-of-work.index')
                    </div>

                    <div class="tab-pane" id="track-chart">
                        <div class="row clearfix">
                            <div class="col-md-12">
                                <div id="job-trackchart-container"></div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane" id="reported-issues">
                        @include('jobs.reported-issue.index', ['job' => $job])
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
                                <form method="POST" action="{{ route ('test-results.save',$job->id) }}" data-parsley-validate novalidate> 
                                @csrf
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
                                        <button type="submit" class="btn btn-primary theme-bg">Submit</button>
                                    </div>
                                </form>
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
                            <form method="POST" action="{{route('job.save-job-checklist', $job->id)}}" data-parsley-validate novalidate> 
                                <div class="modal-body">
                                    @csrf
                                    @include('jobs.checklist.add', ['job' => $job, 'jobCableOptions' => $jobCableOptions])
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary theme-bg">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
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

                <!--reported issue comment  list -->
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
                                <div class="form-group">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--button show -->
                <div class="form-group m-t-10 jobs-test">
                    @if($role_name == 'Admin' || $role_name == 'Manager' ||$role_name == 'Supervisor')
                    <button type="button" class="btn btn-outline-info" data-toggle="modal" data-target="#fill-checklist">{{ __('Fill Checklist') }}</button>
                    @endif
                    <button type="button" class="btn btn-outline-info" data-toggle="modal" data-target="#fill-test-results">{{ __('Fill Test Results') }}</button>
                    <button type="button" class="btn btn-outline-info" data-toggle="modal" data-target="#report-an-issue">{{ __('Report An Issue') }}</button>

                    @if($role_name == 'Admin' || $role_name == 'Manager')
                    <a href ="{{ route('job.add-final-check-sheet', $job->id) }}">
                    <button type="button" class="btn btn-outline-info">{{ __('Fill Final Check Sheet') }}</button> </a>
                    <button type="button" class="btn btn-outline-success" data-toggle="modal" data-target="#close-job">{{ __('Close Job') }}</button>
                    @endif
                </div>
                <!-- Vertically centered finalCheckSheetDocumentModalCenter -->
            </div>      
        </div>
    </div>
</div>
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
        /* drag and drop on single line drawing page */
        const INPUT_FILE = document.querySelector('#upload-files');

        const INPUT_CONTAINER = document.querySelector('#upload-container');
        const FILES_LIST_CONTAINER = document.querySelector('#files-list-container');

        const FILE_LIST = [];
        let UPLOADED_FILES = [];

        const multipleEvents = (element, eventNames, listener) => {
            const events = eventNames.split(' ');

            events.forEach(event => {
                element.addEventListener(event, listener, false);
            });
        };

        const previewImages = () => {
            FILES_LIST_CONTAINER.innerHTML = '';
            console.log("preview",FILE_LIST);
            if (FILE_LIST.length > 0) {
                FILE_LIST.forEach((addedFile, index) => {
                    const content = `
                    <div class="form__image-container js-remove-image" data-index="${index}">
                      <img class="form__image" src="${addedFile.url}" alt="${addedFile.name}">
                    </div>
                  `;

                  FILES_LIST_CONTAINER.insertAdjacentHTML('beforeEnd', content);
                });
            } else {
                console.log('empty');
                INPUT_FILE.value = "";
            }
        };

        const fileUpload = () => {
            if (FILES_LIST_CONTAINER) {
                multipleEvents(INPUT_FILE, 'click dragstart dragover', () => {
                INPUT_CONTAINER.classList.add('active');
                });

                multipleEvents(INPUT_FILE, 'dragleave dragend drop change blur', () =>{
                  INPUT_CONTAINER.classList.remove('active');
                });

                INPUT_FILE.addEventListener('change', () => {
                  const files = [...INPUT_FILE.files];
                  console.log("changed");
                  files.forEach(file => {
                    const fileURL = URL.createObjectURL(file);
                    const fileName = file.name;
                    if (!file.type.match("image/")) {
                      alert(file.name + " is not an image");
                      console.log(file.type);
                    } else {
                      const uploadedFiles = {
                        name: fileName,
                        url: fileURL };


                      FILE_LIST.push(uploadedFiles);
                    }
                  });

                  console.log(FILE_LIST); //final list of uploaded files
                  previewImages();
                  UPLOADED_FILES = document.querySelectorAll(".js-remove-image");
                  removeFile();
                });
            }
        };

        const removeFile = () => {
            UPLOADED_FILES = document.querySelectorAll(".js-remove-image");

            if (UPLOADED_FILES) {
                UPLOADED_FILES.forEach(image => {
                    image.addEventListener('click', function () {
                        const fileIndex = this.getAttribute('data-index');

                        FILE_LIST.splice(fileIndex, 1);
                        previewImages();
                        removeFile();
                    });
                });
            } else {
                [...INPUT_FILE.files] = [];
            }
        };

        fileUpload();
        removeFile();

        /* drag and drop on Schematic drawing page */
        const INPUT_FILE_Schematic = document.querySelector('#upload-files-schematic');
        const INPUT_CONTAINER_Schematic= document.querySelector('#upload-container-schematic');
        const FILES_LIST_CONTAINER_Schematic = document.querySelector('#files-list-container-schematic');

        const FILE_LIST_Schematic = [];
        let UPLOADED_FILES_Schematic  = [];

        const multipleEventsSchematic = (element, eventNames, listener) => {
            const events = eventNames.split(' ');

            events.forEach(event => {
                element.addEventListener(event, listener, false);
            });
        };

        const previewImagesSchematic = () => {
            FILES_LIST_CONTAINER_Schematic.innerHTML = '';
            console.log("preview",FILE_LIST_Schematic);
            if (FILE_LIST_Schematic.length > 0) {
                FILE_LIST_Schematic.forEach((addedFile, index) => {
                  const content = `
                    <div class="form__image-container js-remove-image" data-index="${index}">
                      <img class="form__image" src="${addedFile.url}" alt="${addedFile.name}">
                    </div>
                  `;

                  FILES_LIST_CONTAINER_Schematic.insertAdjacentHTML('beforeEnd', content);
                });
            } else {
                console.log('empty');
                INPUT_FILE_Schematic.value = "";
            }
        };

        const fileUploadSchematic = () => {
            if (FILES_LIST_CONTAINER_Schematic) {
                multipleEventsSchematic(INPUT_FILE_Schematic, 'click dragstart dragover', () => {
                INPUT_CONTAINER_Schematic.classList.add('active');
                });

                multipleEventsSchematic(INPUT_FILE_Schematic, 'dragleave dragend drop change blur', () =>{
                  INPUT_CONTAINER_Schematic.classList.remove('active');
                });

                INPUT_FILE_Schematic.addEventListener('change', () => {
                  const files = [...INPUT_FILE_Schematic.files];
                  console.log("changed");
                  files.forEach(file => {
                    const fileURL = URL.createObjectURL(file);
                    const fileName = file.name;
                    if (!file.type.match("image/")) {
                      alert(file.name + " is not an image");
                      console.log(file.type);
                    } else {
                      const uploadedFiles = {
                        name: fileName,
                        url: fileURL };


                      FILE_LIST_Schematic.push(uploadedFiles);
                    }
                  });

                  console.log(FILE_LIST_Schematic); //final list of uploaded files
                  previewImagesSchematic();
                  UPLOADED_FILES_Schematic = document.querySelectorAll(".js-remove-image");
                  removeFileSchematic();
                });
            }
        };

        const removeFileSchematic = () => {
          UPLOADED_FILES_Schematic = document.querySelectorAll(".js-remove-image");

          if (UPLOADED_FILES_Schematic) {
            UPLOADED_FILES_Schematic.forEach(image => {
              image.addEventListener('click', function () {
                const fileIndex = this.getAttribute('data-index');

                FILE_LIST_Schematic.splice(fileIndex, 1);
                previewImagesSchematic();
                removeFileSchematic();
              });
            });
          } else {
            [...INPUT_FILE_Schematic.files] = [];
          }
        };

        fileUploadSchematic();
        removeFileSchematic();

        /* drag and drop on Location drawing page */
       const INPUT_FILE_Location = document.querySelector('#upload-files-location');
        const INPUT_CONTAINER_Location= document.querySelector('#upload-container-location');
        const FILES_LIST_CONTAINER_Location = document.querySelector('#files-list-container-location');

        const FILE_LIST_Location = [];
        let UPLOADED_FILES_Location  = [];

        const multipleEventsLocation = (element, eventNames, listener) => {
            const events = eventNames.split(' ');

            events.forEach(event => {
                element.addEventListener(event, listener, false);
            });
        };

        const previewImagesLocation = () => {
          FILES_LIST_CONTAINER_Location.innerHTML = '';
          console.log("preview",FILE_LIST_Location);
            if (FILE_LIST_Location.length > 0) {
                FILE_LIST_Location.forEach((addedFile, index) => {
                  const content = `
                    <div class="form__image-container js-remove-image" data-index="${index}">
                      <img class="form__image" src="${addedFile.url}" alt="${addedFile.name}">
                    </div>
                  `;

                  FILES_LIST_CONTAINER_Location.insertAdjacentHTML('beforeEnd', content);
                });
            } else {
                console.log('empty');
                INPUT_FILE_Location.value = "";
            }
        };

        const fileUploadLocation = () => {
            if (FILES_LIST_CONTAINER_Location) {
                multipleEventsLocation(INPUT_FILE_Location, 'click dragstart dragover', () => {
                INPUT_CONTAINER_Location.classList.add('active');
                });

                multipleEventsLocation(INPUT_FILE_Location, 'dragleave dragend drop change blur', () =>{
                  INPUT_CONTAINER_Location.classList.remove('active');
                });

                INPUT_FILE_Location.addEventListener('change', () => {
                  const files = [...INPUT_FILE_Location.files];
                  console.log("changed");
                  files.forEach(file => {
                    const fileURL = URL.createObjectURL(file);
                    const fileName = file.name;
                    if (!file.type.match("image/")) {
                      alert(file.name + " is not an image");
                      console.log(file.type);
                    } else {
                      const uploadedFiles = {
                        name: fileName,
                        url: fileURL };


                      FILE_LIST_Location.push(uploadedFiles);
                    }
                  });

                  console.log(FILE_LIST_Location); //final list of uploaded files
                  previewImagesLocation();
                  UPLOADED_FILES_Location = document.querySelectorAll(".js-remove-image");
                  removeFileLocation();
                });
            }
        };

        const removeFileLocation = () => {
            UPLOADED_FILES_Location = document.querySelectorAll(".js-remove-image");

            if (UPLOADED_FILES_Location) {
                UPLOADED_FILES_Location.forEach(image => {
                    image.addEventListener('click', function () {
                        const fileIndex = this.getAttribute('data-index');

                        FILE_LIST_Location.splice(fileIndex, 1);
                        previewImagesLocation();
                        removeFileLocation();
                    });
                });
            } else {
                [...INPUT_FILE_Location.files] = [];
            }
        };

        fileUploadLocation();
        removeFileLocation();

        /* delete drawing images*/
        $(function () {
            $(document).on('click', '.delete-drawing-img', function() {
                var imgId = $(this).attr('data-id');
                let k = $(this);
                var _token  = '{{csrf_token()}}';
               
                swal({
                    title: "Are you sure?",
                    text: "You want to delete it, You will not be able to recover this record!",
                    type: "warning",
                    confirmButtonColor: "#dc3545",
                    confirmButtonText: "Yes, I am sure!",
                    showCancelButton: true,
                    closeOnConfirm: false,
                    showLoaderOnConfirm: true,
                }, function (isConfirm) {

                    if (isConfirm) {
                        $.ajax({
                            method: "POST",
                            url: "{{ route('jobs.removeJobDrawing') }}",
                            data: {_token: _token, imgId: imgId},
                            success: function(res) {
                                if(res.status == 1) {
                                    /**/
                                    setTimeout(function () {
                                        swal("Done!", res.message, "success");

                                        k.parent().remove();
                                    }, 2000);
                                    /**/
                                } else {
                                    swal("Error:", res.message, "error");
                                }
                            }, error: function(xhr, ajaxOptions, thrownError) {
                                var xhrRes = xhr.responseJSON;
                                swal("Error deleting!", "Please try again", "error");
                            }
                        });
                    }
                });
            });
        });

        /* listing of main job cables */
        var listingsTable = drawTable();

        function drawTable() {
            return $('.job-cables-datatable').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                ajax: "{{ route('job-cables.index', $job->id) }}",
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
            $(document).on('click', '.delete', function() {
                var _route = $(this).data('route');
                var _token  = '{{csrf_token()}}';

                swal({
                    title: "Are you sure?",
                    text: "You want to delete it, You will not be able to recover this record!",
                    type: "warning",
                    confirmButtonColor: "#dc3545",
                    confirmButtonText: "Yes, I am sure!",
                    showCancelButton: true,
                    closeOnConfirm: false,
                    showLoaderOnConfirm: true,
                }, function (isConfirm) {

                    if (isConfirm) {
                        $.ajax({
                            method: "POST",
                            url: _route,
                            data: {_token: _token},
                            success: function(res) {
                                if(res.status == 1) {
                                    /**/
                                    setTimeout(function () {
                                        swal("Done!", res.message, "success");

                                        listingsTable.ajax.reload(null, false);
                                    }, 2000);
                                    /**/
                                } else {
                                    swal("Error:", res.message, "error");
                                }
                            }, error: function(xhr, ajaxOptions, thrownError) {
                                var xhrRes = xhr.responseJSON;
                                swal("Error deleting!", "Please try again", "error");
                            }
                        });
                    }
                });
            });
            
            /*add job cable*/
            $(document).on('click', '.job-cables', function() {
                var job_cables_id = $(this).data('id');
                var url           = $(this).data('url');
                var data_type     = $(this).data('type');
                var _token        = '{{csrf_token()}}';
                var _job_id       = '{{$job->id}}';

                if(data_type == 'view'){
                    $('#save-cable-btn').addClass('hidden');
                } else {
                    $('#save-cable-btn').removeClass('hidden');
                }

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

        var role_name = '{{$role_name}}';

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
                    {data: 'cableId', name: 'cableId'},
                    {data: 'to', name: 'to'},
                    {data: 'from', name: 'from'},
                    {data: 'size', name: 'size'},
                    {data: 'cores', name: 'cores'},
                    {data: 'installedStatus', name: 'installed-status',
                        render: function( installed, type, row ){
                            var status_checked = '';
                            if(installed == 1) {                               
                                status_checked = 'checked';
                            }
                        return '<label class="switch"><input data-id="' + row.id + '" class="changeStatus" type="checkbox" data-onstyle="success" data-type= "installed" data-offstyle="danger" data-toggle="toggle" data-on="Active" data-off="InActive" ' + status_checked +'/><span class="slider round"></span></label>';
                        }
                    },                   
                  
                    {data: 'checklistStatus', name: 'checklist-status',
                        render: function( checklist, type, row ){
                            var _disabled = '';
                            if(row.installedStatus == 0){
                                  _disabled = 'disabled';
                                }
                            var status_checked = '';
                            if(checklist == 1) {
                                status_checked = 'checked';
                            }
                        return '<label class="switch"><input data-id="' + row.id + '" class="changeStatus checklist" '+ _disabled +' type="checkbox" data-onstyle="success" data-type= "checklist" data-offstyle="danger" data-toggle="toggle" data-on="Active" data-off="InActive" ' + status_checked +'/><span class="slider round"></span></label>';
                        }
                    },

                    {data: 'testResultStatus', name: 'test_result-status',
                        render: function( test_result, type, row ){
                            var _disabled = '';
                            if(row.installedStatus == 0){
                                  _disabled = 'disabled';
                                }
                            var status_checked = '';
                            if(test_result == 1) {
                                status_checked = 'checked';
                            }
                        return '<label class="switch"><input data-id="' + row.id + '" class="changeStatus testResult"  '+ _disabled +' type="checkbox" data-onstyle="success"  data-type= "test_result" data-offstyle="danger" data-toggle="toggle" data-on="Active" data-off="InActive" ' + status_checked +'/><span class="slider round"></span></label>';
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

        $(document).on('change', '.changeStatus', function() {
            var _this = $(this);
            var data_type= $(this).data('type');
            var status = $(this).prop('checked') == true ? 1 : 0;      
            var job_cable_id = $(this).data('id');              
                $.ajax({
                    type: "GET",
                    dataType: "json",
                    url: "{{ route('change-status-area-of-work') }}",
                    data: {'data_type': data_type, 'status': status, 'job_cable_id':job_cable_id},
                    success: function(res){
                        if(res.status == 1) {
                                /**/
                                setTimeout(function () {
                                    swal("Done!", res.message, "success");
                                }, 100);

                            if(res.data.installed == 1){
                                   _this.parents('tr').find('input.checklist').removeAttr("disabled");

                                   _this.parents('tr').find('input.testResult').removeAttr("disabled");
                                   
                                } else {
                                    _this.parents('tr').find('input.checklist').prop("checked",false).attr("disabled", true);

                                    _this.parents('tr').find('input.testResult').prop("checked", false).attr("disabled", true);
                                }
                                /**/
                            } else {
                                swal("Error:", res.message, "error");
                            }
                    }
                });
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

                if(_val != '') {
                    $.ajax({
                        type: "POST",
                        url: "{{ route('job.get-termination-details') }}",
                        data: {'_token' : _token, 'job_id': job_id, 'job_cable_id': job_cable_id, 'location_id': _val},
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

            $(document).on('change', '#cable', function() {
                var _val    = $(this).val();
                var _job_id = "{{$job->id}}";
                var _token  = '{{csrf_token()}}';

                if(_val != '') {
                    $.ajax({
                        type: "POST",
                        url: "{{ route('job.get-job-checklist-details') }}",
                        data: {'_token' : _token, 'job_id': _job_id, 'cable_id': _val},
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
                            var status_checked = '';
                            if(row.status == 1) {
                                status_checked = 'checked';
                            }
                            return '<label class="switch"><input data-id="' + row.id + '" class="changeReportStatus" type="checkbox" data-onstyle="success" data-offstyle="danger" data-toggle="toggle" data-on="Active" data-off="InActive" ' + status_checked +'/><span class="slider round"></span></label>';
                        }
                    },
                    {data: 'comment', name: 'comment'},
                ]
            });
        }

        /* get comment on reported issue*/
        /*$(document).on('click', '.report-issue-comment', function() {

            var report_issue_id = $(this).data('id');
            var pageUrl         = $(this).data('url');
            var _token          = '{{csrf_token()}}';

           // var ENDPOINT = '{{ url('/') }}';
            var page = 1;
            infinteLoadMore(page);
            $('.chat-history').scroll(function () {
            if ($(window).scrollTop() + $(window).height() >= $(document).height()) {
                page++;
                infinteLoadMore(page);
            }
        });

        function infinteLoadMore(page) {
            $.ajax({
                url: pageUrl + "?page=" + page,
                datatype: "html",
                type: "get",
                data: {'_token' : _token, 'report_issue_id': report_issue_id,'page' : page}, 
                beforeSend: function () {
                    $('.auto-load').show();
                }
            })
            .done(function (res) {

                if(res.status == 1) {

                    if(res.page > 1) {
                        $('.auto-load').hide();
                        //$('.htmlToAppendComment').prepend(res.data.content);
                        $(".message_data").prepend('<li class="right clearfix"><div class="message"><span>'+res.data.content+'</span></div></li>');
                    } else {
                        $('.auto-load').hide();
                        $('.htmlToAppendComment').html(res.data.content); 
                    }
                } else {
                    $('.htmlToAppendComment').html('');
                    swal("Error:", res.message, "error");
                }

                if (response.length == 0) {
                    $('.auto-load').html("We don't have more data to display :(");
                    return;
                }
                $('.auto-load').hide();
                $('.htmlToAppendComment').html(response.data.content);
                $("#data-wrapper").append(response);
            })               
        }
        });*/

        /* add comment on reported issue*/
        $(document).on('click', '.report-issue-comment', function() {
            var report_issue_id = $(this).data('id');
            var url           = $(this).data('url');
            var _token        = '{{csrf_token()}}';

            if(report_issue_id != '') {
                $.ajax({
                    type: "GET",
                    url : url,
                    data: {'_token' : _token, 'report_issue_id': report_issue_id},
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

        /*change status on report issue */
        $(document).on('change', '.changeReportStatus', function() {
            var status = $(this).prop('checked') == true ? 1 : 0;      
            var report_issue_id = $(this).data('id');              
            $.ajax({
                type: "GET",
                dataType: "json",
                url: "{{ route('report-issue.changeReportStatus') }}",
                data: {'status': status, 'report_issue_id': report_issue_id},
                success: function(res){
                    if(res.status == 1) {
                            /**/
                            setTimeout(function () {
                                swal("Done!", res.message, "success");
                            }, 100);
                            /**/
                        } else {
                            swal("Error:", res.message, "error");
                        }
                }
            });
        });

        $(document).ready(function() { 
            var path =  window.location.hash;
            if(path != '') {
                $('.job-menu li a').each(function() {
                  var href = $(this).attr('href');     
                    if (href === path) {
                        $(this).addClass('active');               
                    }else{
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
                    }
                    else{
                        $(this).removeClass('active');
                    }    

                });

                /*$(".dropdown-item").each(function(){
                    var href = $(this).attr('href');
                    $(this).removeClass('active');
                    if (href === path) {
                        $(this).addClass('active');
                        // $(this).closest('div.dropdown-menu').addClass('show');                
                    }  
                });*/
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

</script>
@stop