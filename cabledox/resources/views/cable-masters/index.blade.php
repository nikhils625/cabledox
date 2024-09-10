@extends('layouts.master')
@section('parentPageTitle', 'List Cable Masters')
@section('title', 'List Cable Masters')

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
             @can('cable-masters.create')
                <a href="{{route('cable-masters.create')}}" class="btn btn-primary text-white"><i class="icon-plus"></i> Add cable masters</a>
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
                <h2>{{ __('List Cable Masters') }}</h2>
                <ul class="header-dropdown dropdown">
                    <li><a href="javascript:void(0);" class="full-screen"><i class="icon-frame"></i></a></li>
                </ul>
            </div>
            <div class="body">
                <div class="table-responsive">
                    <table class="table table-hover cable-masters-datatable"> <!-- table-bordered -->
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Cable Type</th>
                                <th>Cores</th>
                                <!-- <th>Status</th> -->
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
        return $('.cable-masters-datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('cable-masters.index') }}",
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'cable_type', name: 'cable_type'},
                {data: 'cores', name: 'cores'},
                /*{data: 'status', name: 'status',
                    render: function( status, type, row ){
                    return '<label class="switch"><input data-id="' + row.id + '" class="changeStatus" type="checkbox" data-onstyle="success" data-offstyle="danger" data-toggle="toggle" data-on="Active" data-off="InActive" ' +  (row.status ? 'checked' : '') +'/><span class="slider round"></span></label>';
                    }
                },*/
                {
                    data: 'action', 
                    name: 'action', 
                    orderable: true, 
                    searchable: true
                },
            ]
        });
    }
$(function () {

    $(document).on('change', '.changeStatus', function() {
        var status = $(this).prop('checked') == true ? 1 : 0;      
        var id = $(this).data('id');              
        $.ajax({
            type: "GET",
            dataType: "json",
            url: "{{ route('cable-masters.change-status') }}",
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