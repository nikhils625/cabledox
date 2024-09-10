@extends('layouts.master')
@section('title', 'List Final Check Sheet Questionnaire')

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
            @can('final-check-sheet.create')
                <a href="{{route('final-check-sheet.create')}}" class="btn btn-primary text-white"><i class="icon-plus"></i> Add New</a>
            @endcan 
        </div>
    </div>
</div>

<div class="row clearfix"> 
    <div class="col-lg-12">
        <div class="card"> 
            <div class="header">
                <h2>{{ __('List Final Check Sheet Questionnaire') }}</h2>
                <div class="float-right">                               
                    <ul class="header-dropdown dropdown">
                        <li><a href="javascript:void(0);" class="full-screen"><i class="icon-frame"></i></a></li>
                    </ul>
                </div>                                       
            </div>
            <div class="body">
                <div class="table-responsive">
                    <table class="table table-hover final-check-sheet-datatable">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Questions</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
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
        return $('.final-check-sheet-datatable').DataTable({   
            processing: true,
            serverSide: true,
            ajax: "{{ route('final-check-sheet.index') }}",
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},               
                {data: 'question_name', name: 'question_name'},               
                {
                    data: 'action', 
                    name: 'action', 
                    orderable: false, 
                    searchable: false,
                },
            ]
        });
    } 


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

