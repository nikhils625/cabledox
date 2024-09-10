@extends('layouts.master')
@section('parentPageTitle', 'List Jobs')
@section('title', 'List Jobs')

@section('page-styles')
<link rel="stylesheet" href="{{ asset('assets/vendor/jquery-datatable/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendor/jquery-datatable/fixedeader/dataTables.fixedcolumns.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendor/jquery-datatable/fixedeader/dataTables.fixedheader.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendor/sweetalert/sweetalert.css') }}">
@stop

@section('content')

<div class="block-header">
    <div class="row clearfix">          
        <div class="col-xl-12 col-md-12 col-sm-12 text-md-right" style="text-align: right; ">
            @can('jobs.create')
                <a href="{{route('jobs.create')}}" class="btn btn-primary text-white"><i class="icon-plus"></i> Add Job</a>
            @endcan
        </div>
    </div>
</div>

<!-- Page header section  -->
<div class="row clearfix">
    <div class="col-lg-12">
        <div class="card">
            @include('layouts.messages')
            <div class="header">
                <h2>{{ __('List Jobs') }}</h2>
                <ul class="header-dropdown dropdown">
                    <li><a href="javascript:void(0);" class="full-screen"><i class="icon-frame"></i></a></li>
                </ul>
            </div>      
                       
            <div class="body">
                <div class="form-group row"> 
                    <div class="col-md-4">
                        <div class="form-group">
                            <select id='job-status' class="form-control">
                                <option value="">Select Status</option>
                                <option value="0">Pending</option>
                                <option value="1">In-progress</option>
                                <option value="2">Completed</option>
                            </select>
                        </div>
                    </div>
                </div>        
                <div class="table-responsive">
                    <table class="table table-hover jobs-datatable">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Job Number</th>
                                <th>Site Name</th>
                                <th>Address</th>
                                <th>Postcode</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    <tbody>
                    </tbody>
                    </table>
                </div>
            </div>
        </div>   
    </div>
</div>    
@stop

@section('vendor-script')
<script src="{{ asset('assets/bundles/datatablescripts.bundle.js') }}"></script>
<script src="{{ asset('assets/vendor/sweetalert/sweetalert.min.js') }}"></script>
@stop

@section('page-script')

<script type="text/javascript">

    var listingsTable = drawTable();

    function drawTable() {
        return $('.jobs-datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
              url: "{{ route('jobs.index') }}",
              data: function (d) {                  
                    d.status  = $('#job-status').val();                             
                }
            },
             
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'job_number', name: 'job_number'},
                {data: 'site_name', name: 'site_name'},
                {data: 'address', name: 'address'},
                {data: 'post_code', name: 'post_code'},
                {data: 'status', name: 'status',
                    render: function( status, type, row ){
                        var status_name = '';
                        if(row.status == 2) {
                            status_name = 'Completed';
                        }
                        else if(row.status == 1) {
                            status_name = 'In-progress';
                        }
                        else{
                            status_name = 'Pending';
                        }
                    return '<p> '+ status_name +' </p>';
                    }
                },
                {
                    data: 'action', 
                    name: 'action', 
                    orderable: true, 
                    searchable: true
                },
            ]
        });
    }
    

    $(document).on("change",'#job-status',function(){
        listingsTable.draw();
    });
 
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
});
</script>
@stop