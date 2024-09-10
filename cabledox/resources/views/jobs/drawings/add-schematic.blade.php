@section('title', 'Add Schematic Drawing Image')

<form method="POST" enctype="multipart/form-data" action="{{ route('jobs.insertJobDrawing',$job->id)}}" data-parsley-validate novalidate> 
    @csrf
      <div class="form-group row">
        <div class="col-md-12">
          <div class="form-group row">
              <label for="file" class="col-md-2 col-form-label text-md-right">{{ __('Upload Image') }}<span class="text-danger"> *</span></label>
                <div class="col-md-6">
                  <label class="form__container" id="upload-container-schematic">Choose or Drag & Drop Files
                   <input class="form__file" id="upload-files-schematic" type="file" accept="image/*" multiple="multiple" name="drawing_name[]" required/>
                  </label>
                  <div class="form__files-container" id="files-list-container-schematic">
                    
                  </div>
                    @error('drawing_name')
                      <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                      </span>
                    @enderror
                </div>
          </div>
          <input type="hidden" name="drawing_type" value="{{$drawingType}}"/>
        </div> 
      </div>                       
      <div class="form-group">
        <a href="{{route('jobs.index')}}" class="btn btn-default" title="Back">{{ __('Back') }}</a>
          <button type="submit" class="btn btn-primary">
            {{ __('Submit') }}
          </button>
      </div>
</form>

<div class="row">
  <div class="col-12">
        <div class="card">
            <div class="body">
                <div id="lightgallery" class="row clearfix lightGallery">

                  @php
                  $jobsDir = \Config::get('constants.uploadPaths.viewJobs');

                  $schematicPath = \Config::get('constants.uploadPaths.schematicDrawing');

                  $jobNumber      = $job->job_number;

                  $schematicDir = $jobsDir . $jobNumber.DIRECTORY_SEPARATOR.$schematicPath;

                  if($getJobDrawing && !empty($getJobDrawing) && $getJobDrawing->count() > 0)
                  {

                  $data = $getJobDrawing->Where('drawing_type',$drawingType)->all();
                
                  foreach($data as $alldata){

                  $pathToDrawingImage = base64_encode($schematicDir . $alldata->drawing_name);

                  @endphp 
                  <div class="col-lg-3 col-md-6 m-b-15 viewImage">
                    <a class="light-link" href="{{asset($schematicDir.$alldata->drawing_name)}}">
                    <img class="img-fluid" src="{{asset($schematicDir.$alldata->drawing_name)}}" alt="">
                    </a>
                    <div class="download-drawing-img">
                        <a href="{{route('drawing.download-drawing-image', $pathToDrawingImage)}}"> 
                          <i class="fa fa-download" data-toggle="tooltip" title="Download"></i>
                         </a> 
                    </div>
                    <div class="delete-drawing-img" data-id="{{$alldata->id}}">
                        <i class="fa fa-times fa-lg" data-toggle="tooltip" title="Delete"></i>
                      </div>
                  </div>

                    @php
                      }
                       
                      }

                    @endphp                     
                </div>
            </div>
        </div>
    </div>
</div> 
