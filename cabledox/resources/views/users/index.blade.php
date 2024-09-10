@extends('layouts.master')
@section('parentPageTitle', 'List Users')
@section('title', 'List Users')

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
            @can('users.create')
                <a href="{{route('users.create')}}" class="btn btn-primary text-white"><i class="icon-plus"></i> Add User</a>
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
                <h2>{{ __('List User') }}</h2>                   
                <ul class="header-dropdown dropdown">
                    <li><a href="javascript:void(0);" class="full-screen"><i class="icon-frame"></i></a></li>
                </ul>
            </div>    
            
            <div class="body">
                <div class="form-group row">
                    <div class="col-md-4"> 
                        <div class="form-group">
                            <select id='users-status' class="form-control">
                                <option value="">Select Status</option>
                                <option value="1">Active</option>
                                <option value="0">InActive</option>
                            </select>
                        </div>    
                    </div>
                </div>    
                <div class="table-responsive">
                    <table class="table table-hover users-datatable"> <!-- table-bordered -->
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Firt Name</th>
                                <th>Last Name</th>
                                <th>Email</th>
                                <th>Role</th>
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
        return $('.users-datatable').DataTable({
            processing: true,
            serverSide: true,
            //ajax: "{{ route('users.index') }}",
             ajax: {
              url: "{{ route('users.index') }}",
              data: function (d) {                  
                    d.status  = $('#users-status').val();                             
                }
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'first_name', name: 'first_name'},
                {data: 'last_name', name: 'last_name'},
                {data: 'email', name: 'email'},
                {data: 'role', name: 'role'},
                {data: 'status', name: 'status',
                    render: function( status, type, row ){
                        if(row.status == 0){
                            var status_checked = '';
                        }
                        else{
                            var status_checked = 'checked';
                        }
                    return '<label class="switch"><input data-id="' + row.id + '" class="changeStatus" type="checkbox" data-onstyle="success" data-offstyle="danger" data-toggle="toggle" data-on="Active" data-off="InActive" ' +  status_checked +'/><span class="slider round"></span></label>';
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

    $(document).on("change",'#users-status',function(){
        listingsTable.draw();
    });

$(function () {

    $(document).on('change', '.changeStatus', function() {
        var status = $(this).prop('checked') == true ? 1 : 0;      
        var id = $(this).data('id');              
        $.ajax({
            type: "GET",
            dataType: "json",
            url: "{{ route('users.change-status') }}",
            data: {'status': status, 'id': id},
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