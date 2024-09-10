@section('parentPageTitle', 'Main Cables List')
@section('title', 'Main Cables List')

<div class="row clearfix">
    <div class="col-lg-12">
        <div class="form-group text-md-right">
            @if(!isset($isEditable) || @$isEditable != 0)
                @can('job-cables.create')
                    <button type="button" class="btn btn-primary text-white job-cables" data-toggle="modal" data-type="add" data-url="{{ route ('job-cables.create') }}" data-target="#job-cables">{{ __('Add Job Cables') }}</button>
                    <!--<a href="{{route('job-cables.create')}}" class="btn btn-primary text-white"><i class="icon-plus"></i> Add Job Cables</a>-->
                @endcan
            @endif
        </div>
        <!--add job cable modal-->
        <div class="modal fade" id="job-cables" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-modal="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><strong>Job Cables</strong></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    </div>
                    <div class="modal-body">                        
                        <div class="row clearfix htmlToAppend">
                                
                        </div>
                    </div>        
                    <div class="modal-footer">
                        <div class="form-group">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                        <button type="submit" class="btn btn-primary" id="save-cable-btn">
                        {{ __('Submit') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover job-cables-datatable">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Cable-Id</th>
                        <th>To</th>
                        <th>From</th>
                        <th>Size (mm sq)</th>
                        <th>Cores</th>
                        <th>Cable Type</th>
                        <th>Cable Id Type</th>
                        <th>Description</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div> 