@section('title', 'Fill Job Check List')
<div class="form-group row">
    <div class="col-md-6">
        <div class="form-group row">
            <label for="job_number" class="col-md-4 col-form-label text-md-left">{{ __('Job Number') }}<span class="text-danger"> *</span></label>
            <div class="col-md-6">
                <input type="text" name="job_number" class="form-control" value="{{$job->job_number}}" readonly>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group row">
            <label for="site_name" class="col-md-4 col-form-label text-md-left">{{ __('Site Name') }}<span class="text-danger"> *</span></label>
            <div class="col-md-6">
                <input type="text" name="site_name" class="form-control" value="{{$job->site_name}}" readonly>
            </div>
        </div>
    </div> 
</div>
<div class="form-group row">
    <div class="col-md-6">
        <div class="form-group row">
            <label for="cable" class="col-md-4 col-form-label text-md-left">{{ __('Cable') }}<span class="text-danger"> *</span></label>
            <div class="col-md-6">
                <select name="cable" id="cable" class="form-control @error('cable') is-invalid @enderror" required>
                    <option value="">-Select-</option>
                    @foreach($jobCableOptions as $key => $option)
                        <option value="{{$option['id']}}">{{$option['cable_id']}}</option>
                    @endforeach
                </select>
                @error('cable')
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
        <div class="checklist-detail-container">&nbsp;</div>
    </div>
</div>