@extends('layouts.master')
@section('title', 'List Clients')
 
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
            @can('clients.create')
                <a href="{{route('clients.create')}}" class="btn btn-primary text-white"><i class="icon-plus"></i> Add Client</a>
            @endcan
        </div>
    </div>
</div>

<div class="row clearfix"> 
    <div class="col-lg-12">
        <div class="card"> 
            <div class="header">
                <div class="float-left">
                    <h2>{{ __('List Client') }}</h2>
                    <div class="filter">
                        <select id='status' class="form-control">
                            <option value="">Select Status</option>
                            <option value="1">Active</option>
                            <option value="0">InActive</option>
                        </select>
                    </div>    
                </div>                                                          
                <ul class="header-dropdown dropdown">
                    <li><a href="javascript:void(0);" class="full-screen"><i class="icon-frame"></i></a></li>
                </ul>                                                     
            </div>
            <div class="body">
                @php
                    $companyLogoPath = asset(\Config::get('constants.uploadPaths.viewCompanyLogo'))
                @endphp
                <div class="table-responsive">
                    <table class="table table-hover clients-datatable">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Company Logo</th>
                                <th>Company Name</th>
                                <th>Company Email</th>
                                <th>Company Phone</th>
                                <th>Contact Person Name</th>
                                <th>Contact Person Email</th>
                                <th>Contact Person Phone</th>
                                <th>No of jobs allocated</th>
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
        var imagePath = '{{$companyLogoPath}}';

        return $('.clients-datatable').DataTable({   
            processing: true,
            serverSide: true,
            //ajax: "{{ route('clients.index') }}",
            ajax: {
              url: "{{ route('clients.index') }}",
              data: function (d) {                  
                    d.status  = $('#status').val();                             
                }
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                { data: 'company_logo', name: 'company_logo',
                    render: function( data, type, full, meta ) {
                    return "<img src=\"" + imagePath  + "/"  + data + "\" height=\"50\"/>";
                    }
                },
                {data: 'company_name', name: 'company_name'},
                {data: 'company_email', name: 'company_email'},
                {data: 'company_phone', name: 'company_phone'},
                {data: 'contactPersonName', name: 'contactPersonName'},
                {data: 'contactPersonEmail', name: 'contact_person_email'},
                {data: 'contactPersonPhone', name: 'contact_person_phone'},
                {data: 'no_of_jobs_allocated', name: 'no_of_jobs_allocated'},
                {data: 'status', name: 'status',
                    render: function( status, type, row ){
                        var status_checked = '';
                        if(row.status == 1) {
                            status_checked = 'checked';
                        }
                    return '<label class="switch"><input data-id="' + row.id + '" class="changeStatus" type="checkbox" data-onstyle="success" data-offstyle="danger" data-toggle="toggle" data-on="Active" data-off="InActive" ' + status_checked +'/><span class="slider round"></span></label>';
                    }
                },
                {
                    data: 'action', 
                    name: 'action', 
                    orderable: false, 
                    searchable: false,
                },
            ]
        });
        
    } 
    
    $(document).on("change",'#status',function(){
        listingsTable.draw();
    });

    

$(document).on('change', '.changeStatus', function() {
    var status = $(this).prop('checked') == true ? 1 : 0;      
    var client_id = $(this).data('id');              
    $.ajax({
        type: "GET",
        dataType: "json",
        url: "{{ route('client.changeStatus') }}",
        data: {'status': status, 'client_id': client_id},
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

