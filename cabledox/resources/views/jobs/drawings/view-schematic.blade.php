@section('title', 'Add Schematic Drawing Image')
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

            if($getJobDrawing && !empty($getJobDrawing) && $getJobDrawing->count() > 0) {

              $data = $getJobDrawing->Where('drawing_type',$drawingType)->all();
          
              foreach($data as $alldata) {

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