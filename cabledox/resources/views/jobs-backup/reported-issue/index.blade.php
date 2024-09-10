@section('parentPageTitle', 'Reported Issues')
@section('title', 'Reported Issues')

<!-- Page header section  -->
<div class="row clearfix">
    <div class="col-lg-12">
        <div class="body">
            <div class="form-group row"> 
                <div class="col-md-6">
                    <div class="form-group"> 
                        <label for="cable_id_type" class="col-md-"><strong>{{ __('Job Number') }}:</strong></label>                      
                        <span>{{ $job->job_number }} + {{ $job->site_name }}</span>                 
                    </div> 
                </div>                    
            </div>
            <div class="table-responsive">
                <table class="table table-hover reported-issue-datatable">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>Reported Issue</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Comment</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>   
</div>
