@section('parentPageTitle', 'Area Of Work')
@section('title', 'Area Of Work')

<!-- Page header section  -->
<div class="row clearfix">
    <div class="col-lg-12">
        <div class="body">
            <div class="form-group row"> 
                <div class="col-md-4">
                    <div class="form-group"> 
                        <select id="to-work-area" class="form-control">
                            <option value="">Select To Work Area</option>
                            @foreach($jobLocationTo as $key => $location)
                                <option value="{{ $location->id }}">{{ $location->jobLocation->location_name}}</option>
                            @endforeach
                        </select>
                    </div> 
                </div> 
                <div class="col-md-4">   
                    <div class="form-group">     
                        <select id="from-work-area" class="form-control">
                            <option value="">Select From Work Area</option>
                            @foreach($jobLocationFrom as $key => $location)
                                <option value="{{ $location->id }}">{{ $location->jobLocation->location_name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">        
                    <div class="form-group">  
                        <select class="status form-control">
                            <option value="" >Select Status</option>   
                            <option value="1" data-type= "installed">Installed Completed</option>      
                            <option value="0" data-type= "installed">Installed Pending</option>
                            <option value="1" data-type= "checklist">Checklist Completed</option>   
                            <option value="0" data-type= "checklist">Checklist Pending</option>
                            <option value="1" data-type= "test-result">Test Result Completed</option>      
                            <option value="0" data-type= "test-result">Test Result Pending</option>
                        </select>
                    </div>
                </div>    
            </div>
                     

            <div class="table-responsive">
                <table class="table table-hover job-area-datatable">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>Cable-Id</th>
                            <th>To</th>
                            <th>From</th>
                            <th>Size (mm sq)</th>
                            <th>Cores</th>
                            <th>Installed</th>
                            <th>Check List</th>
                            <th>Test Result</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>   
</div>