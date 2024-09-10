@section('title', 'Termination Details')
<div class="row clearfix">
    <div class="col-md-12">
       <div class="body">
            <div class="tab-content">
                <div class="tab-pane show active" id="termination-details">
                    @php
                        $route = route('job.termination-details-save', $job->id);
                        if(isset($isEditable) && @$isEditable == 0) {
                            $route = "javascript:void(0);";
                        }
                    @endphp
                    <form method="POST" action="{{$route}}" data-parsley-validate novalidate>
                        @csrf
                        <div class="form-group row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label for="cable_id" class="col-md-2 col-form-label text-md-left"><strong>{{ __('Cable') }}</strong><span class="text-danger"> *</span></label>
                                    <div class="col-md-6">
                                        <select name="cable_id" id="cable_id" class="form-control @error('cable_id') is-invalid @enderror" required>
                                            <option value="">-Select-</option>
                                            @foreach($jobCableOptions as $key => $option)
                                                <option value="{{$option['id']}}">{{$option['cable_id']}}</option>
                                            @endforeach
                                        </select>
                                        @error('cable_id')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label for="termination_location_id" class="col-md-6 col-form-label text-md-right"><strong>{{ __('Location') }}</strong><span class="text-danger"> *</span></label>
                                    <div class="col-md-6">
                                        <select name="location_id" id="termination_location_id" class="form-control @error('location_id') is-invalid @enderror" required>
                                            <option value="">-Select-</option>
                                        </select>
                                        @error('location_id')
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
                                <div class="core-details-container">&nbsp;</div>
                            </div>
                        </div>
                        @if(!isset($isEditable) || @$isEditable != 0)
                            <div class="form-group">
                                <a href="{{route('job-cables.index')}}" class="btn btn-default" title="Back">{{ __('Back') }}</a>

                                <button type="submit" class="btn btn-primary">
                                    {{ __('Submit') }}
                                </button>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>