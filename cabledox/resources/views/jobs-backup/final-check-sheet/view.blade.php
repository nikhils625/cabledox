@extends('layouts.master')
@section('title', 'Final Check Sheet') 

@section('page-styles')
<link rel="stylesheet" href="{{ asset('assets/vendor/sweetalert/sweetalert.css') }}">
<!--[if lt IE 9]><script src="{{ asset('assets/vendor/signature-pad/flashcanvas.js') }}"></script><![endif]-->
<style>
    .hidden {
        display: none !important;
    }
    .textarea{
        width: 100%;
        resize: none;
    }
</style>
@stop

@section('content')
<div class="row clearfix">
    <div class="col-md-12">
        <div class="card"> 
            @include('layouts.messages')

            <div class="header"><h2>{{ __('Final Check Sheet') }}</h2></div>

            <div class="body">
                <div class="tab-content">
                    <div class="tab-pane show active" id="final-check-sheet-details">
                        <div class="form-group row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label for="job_number" class="col-md-4 col-form-label text-md-right">{{ __('Job Number') }}<span class="text-danger"> *</span></label>
                                    <div class="col-md-6">
                                        <input type="text" name="job_number" id="job_number" class="form-control @error('job_number') is-invalid @enderror" value="{{ $job->job_number }}" required autocomplete="job_number" readonly>
                                        @error('job_number')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="area_inspected" class="col-md-4 col-form-label text-md-right">{{ __('Area Inspected') }}<span class="text-danger"> *</span></label>
                                    <div class="col-md-6">
                                        <select name="area_inspected" id="area_inspected" class="form-control @error('area_inspected') is-invalid @enderror" required>
                                            <option value="">-Select-</option>
                                            @foreach($jobCableLocations as $key => $cableLocation)
                                                <option value="{{$cableLocation->id}}">{{$cableLocation->jobLocation->location_name}}</option>
                                            @endforeach
                                        </select>
                                        @error('area_inspected')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label for="site_name" class="col-md-6 col-form-label text-md-right">{{ __('Site Name') }}<span class="text-danger"> *</span></label>
                                    <div class="col-md-6">
                                        <input type="text" name="site_name" id="site_name" class="form-control @error('site_name') is-invalid @enderror" value="{{ $job->site_name }}" required autocomplete="site_name" readonly>
                                        @error('site_name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="cable_id" class="col-md-6 col-form-label text-md-right">{{ __('Cable Id Used In Area') }}<span class="text-danger"> *</span></label>
                                    <div class="col-md-6">
                                        <select name="cable_id" id="cable_id" class="form-control @error('cable_id') is-invalid @enderror" required>
                                            <option value="">-Select-</option>
                                        </select>
                                        @error('cable_id')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-12">
                                <div class="final-check-sheet-container"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <a href="{{route('jobs.index')}}" class="btn btn-default" title="Back">{{ __('Back') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('vendor-script')
<script src="{{ asset('assets/vendor/sweetalert/sweetalert.min.js') }}"></script>
@stop

@section('page-script')
<script type="text/javascript">
$(function () {

    $(document).on('change', '#area_inspected', function() {
        var _val    = $(this).val();
        var job_id  = "{{$job->id}}";
        var _token  = '{{csrf_token()}}';

        $('.final-check-sheet-container').html('');
        if(_val != '') {
            $.ajax({
                type: "POST",
                url: "{{ route('job.get-cable-details') }}",
                data: {'_token' : _token, 'job_id': job_id, 'cable_location_id': _val},
                success: function(res){
                    if(res.status == 1) {
                        var _html = '<option value="">-Select-</option>';
                        $.each(res.job_cables, function(k, v) {
                            _html += '<option value="'+v.id+'">'+v.cable_id+'</option>'; 
                        });

                        $('#cable_id').html(_html);
                    } else {
                        swal("Error:", res.message, "error");
                    }
                }
            });            
        } else {
            $('#cable_id').html('<option value="">-Select-</option>');
        }
    });

    $(document).on('change', '#cable_id', function() {
        var _val           = $(this).val();
        var area_inspected = $('#area_inspected').val();
        var job_id         = "{{$job->id}}";
        var _token         = '{{csrf_token()}}';
        var is_editable    = 0;

        $('.final-check-sheet-container').html('');
        if(_val != '') {
            $.ajax({
                type: "POST",
                url: "{{ route('job.get-check-sheet-details') }}",
                data: {'_token' : _token, 'job_id': job_id, 'area_inspected': area_inspected, 'cable_id': _val, 'is_editable': is_editable},
                success: function(res) {
                    if(res.status == 1) {
                        $('.final-check-sheet-container').html(res.data.content);
                    } else {
                        $('.final-check-sheet-container').html('');
                        swal("Error:", res.message, "error");
                    }
                }, error: function(xhr, ajaxOptions, thrownError) {
                    var xhrRes = xhr.responseJSON;
                    swal("Error downloading!", xhrRes.message, "error");
                }
            });            
        } else {
            $('.final-check-sheet-container').html('');
        }
    });
});
</script>
@stop