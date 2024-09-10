@section('parentPageTitle', 'Edit Job Cable')
@section('title', 'Edit Job Cable')

<form method="POST" id="edit-job-cable-form" action="{{ route('job-cables.update', $jobCable->id) }}" data-parsley-validate novalidate>
    @csrf
    @method('PATCH')
    <div class="form-group row">
        <div class="col-md-6">
            <input type="hidden" name="job_id" value="{{@$jobCable->job->id}}">
            <div class="form-group row">
                <label for="cable_id_type" class="col-md-4 col-form-label text-md-right">{{ __('Cable Id Type') }}<span class="text-danger"> *</span></label>
                <div class="col-md-6">
                    <select name="cable_id_type" id="cable_id_type" class="form-control @error('cable_id_type') is-invalid @enderror" required>
                        <option value="">-Select-</option>
                        @foreach($cableTypesId as $key => $typeId)
                            @php
                                $selectCableIdType = null;
                                if($jobCable->cableType->id == $key) {
                                    $selectCableIdType = 'selected';
                                }
                            @endphp
                            <option value="{{$key}}" {{$selectCableIdType}}>{{$typeId}}</option>
                        @endforeach
                    </select>
                    @error('cable_id_type')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <div class="form-group row">
                <label for="custom_id" class="col-md-4 col-form-label text-md-right">{{ __('Custom Id') }}<span class="text-danger"> *</span></label>
                <div class="col-md-6">
                    <input type="text" name="custom_id" id="custom_id" class="form-control @error('custom_id') is-invalid @enderror" value="{{$jobCable->custom_id}}" required autocomplete="custom_id" placeholder="Please enter custon id">
                    @error('custom_id')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <div class="form-group row">
                <label for="cable_type" class="col-md-4 col-form-label text-md-right">{{ __('Cable Type') }}<span class="text-danger"> *</span></label>

                <div class="col-md-6">

                    <select name="cable_type" id="cable_type" class="form-control @error('cable_type') is-invalid @enderror selectpicker" data-live-search="true" required>
                        <option value="">-Select-</option>
                        @foreach($cableTypes as $key => $type)
                            @php
                                $selectCableType = null;
                                if($jobCable->cableIdType->id == $key) {
                                    $selectCableType = 'selected';
                                }
                            @endphp
                            <option value="{{$key}}" {{$selectCableType}}>{{$type}}</option>
                        @endforeach
                    </select>
                    @error('cable_type')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <div class="form-group row">
                <label for="cores" class="col-md-4 col-form-label text-md-right">{{ __('Cores') }}<span class="text-danger"> *</span></label>
                <div class="col-md-6">
                    @php
                        $cableCores = @$jobCable->cableIdType->cores;
                        if(@$jobCable->cableIdType->no_of_pair_triple_quad > 1) {
                            $cableCores = @$jobCable->cableIdType->cores . "x" . @$jobCable->cableIdType->no_of_pair_triple_quad; 
                        }
                    @endphp
                    <input type="text" name="cores" id="cores" class="form-control @error('cores') is-invalid @enderror" value="{{$cableCores}}" required aucorescomplete="cores" placeholder="Please enter cores" readonly>
                    @error('cores')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <div class="form-group row">
                <label for="size" class="col-md-4 col-form-label text-md-right">{{ __('Size') }}<span class="text-danger"> *</span></label>
                <div class="col-md-6">
                    <input type="number" name="size" id="size" class="form-control @error('size') is-invalid @enderror" value="{{$jobCable->size}}" required ausizecomplete="size" placeholder="Please enter size">
                    @error('size')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <div class="form-group row">
                <label for="additional_information" class="col-md-4 col-form-label text-md-right">{{ __('Additional Information') }}</label>
                <div class="col-md-6">
                    <textarea name="additional_information" id="additional_information" class="form-control @error('additional_information') is-invalid @enderror" cols="30" rows="4" placeholder="Please enter additional description">{{$jobCable->additional_information}}</textarea>
                    @error('additional_information')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group row">
                <label for="cable_id" class="col-md-4 col-form-label text-md-right">{{ __('Cable Id') }}<span class="text-danger"> *</span></label>
                <div class="col-md-6">
                    <input type="cable_id" name="cable_id" id="cable_id" class="form-control @error('cable_id') is-invalid @enderror" value="{{$jobCable->cable_id}}" required autocomplete="cable_id" autofocus placeholder="Please enter cable id" readonly>
                    @error('cable_id')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <div class="form-group row">
                <label for="unique_code" class="col-md-4 col-form-label text-md-right">{{ __('Unique Code') }}<span class="text-danger"> *</span></label>
                <div class="col-md-6">
                    <input type="text" name="unique_code" id="unique_code" class="form-control @error('unique_code') is-invalid @enderror" value="{{$jobCable->unique_code}}" required autocomplete="unique_code" placeholder="Please enter custon id">
                    @error('unique_code')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <div class="form-group row">
                <label for="to" class="col-md-4 col-form-label text-md-right">{{ __('To') }}<span class="text-danger"> *</span></label>
                <div class="col-md-6">
                    <input type="text" name="to" id="to" class="form-control @error('to') is-invalid @enderror" value="{{$jobCable->jobCableTo->jobLocation->location_name}}" required autocomplete="to" placeholder="Please enter to">
                    @error('to')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                    <input type="hidden" name="to_location" value="{{$jobCable->jobCableTo->id}}">
                </div>
            </div>

            <div class="form-group row">
                <label for="from" class="col-md-4 col-form-label text-md-right">{{ __('From') }}<span class="text-danger"> *</span></label>
                <div class="col-md-6">
                    <input type="text" name="from" id="from" class="form-control @error('from') is-invalid @enderror" value="{{$jobCable->jobCableFrom->jobLocation->location_name}}" required autocomplete="from" placeholder="Please enter from">
                    @error('from')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                    <input type="hidden" name="to_location" value="{{$jobCable->jobCableFrom->id}}">
                </div>
            </div>

            <div class="form-group row">
                <label for="description" class="col-md-4 col-form-label text-md-right">{{ __('Description') }}<span class="text-danger"> *</span></label>
                <div class="col-md-6">
                    <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" cols="30" rows="4" placeholder="Please enter description" required>{{$jobCable->description}}</textarea>
                    @error('description')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
          
        </div> 
    </div>                       
</form>


<script type="text/javascript">
$(function () {

    $('#edit-job-cable-form').parsley();
    
    var instance = $('#edit-job-cable-form').parsley();
     $(document).on('click', '#save-cable-btn', function(e) {
       e.preventDefault(); 
        if(!instance.isValid()) {
            instance.validate();
        } else {
            $('#edit-job-cable-form').trigger('submit');
        }
    });

    $("#to").autocomplete({
        minLength: 1,
        source: function( request, response ) {
            var currentRequest = null;
            var _token  = '{{csrf_token()}}';

            currentRequest = $.ajax({
                url: "{{route('job.get-job-locations')}}",
                data: {
                    _token: _token, term: request.term
                },
                type: "POST", 
                beforeSend : function()    {           
                    if(currentRequest != null) {
                        currentRequest.abort();
                    }
                },
                success: function( data ) {
                    response(data);
                }
            });
        },
        select: function( event, ui ) {
            $('input[name="to_location"]').val(ui.item.id);
        }
    });

    $("#from").autocomplete({
        minLength: 1,
        // source: "{{route('job.get-job-locations')}}",
        source: function( request, response ) {
            var _currentRequest = null;
            var _token  = '{{csrf_token()}}';

            _currentRequest = $.ajax({
                url: "{{route('job.get-job-locations')}}",
                data: {
                    _token: _token, term: request.term
                },
                type: "POST", 
                beforeSend : function()    {           
                    if(_currentRequest != null) {
                        _currentRequest.abort();
                    }
                },
                success: function( data ) {
                    response(data);
                }
            });
        },
        select: function( event, ui ) {
            $('input[name="from_location"]').val(ui.item.id);
        }
    });

    $(document).on('change', '#cable_id_type', function() {
        var _val = $(this).val();
        var _token  = '{{csrf_token()}}';

        if(_val != '') {
            $.ajax({
                type: "POST",
                url: "{{ route('job-cables.get-cable-id') }}",
                data: {'_token' : _token, 'cable_type_id': _val},
                success: function(res){
                    if(res.status == 1) {
                        $('#cable_id').val(res.cable_unique_code);
                    } else {
                        swal("Error:", res.message, "error");
                    }
                }
            });            
        }
    });

    $(document).on('change', '#cable_type', function() {
        var _val = $(this).val();
        var _token  = '{{csrf_token()}}';

        if(_val != '') {
            $.ajax({
                type: "POST",
                url: "{{ route('job-cables.get-cable-type-details') }}",
                data: {'_token' : _token, 'cable_id_type': _val},
                success: function(res){
                    if(res.status == 1) {
                        $('#cores').val(res.cores);
                    } else {
                        swal("Error:", res.message, "error");
                    }
                }
            });            
        }
    });
});
</script>