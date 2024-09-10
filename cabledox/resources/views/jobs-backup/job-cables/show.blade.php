@section('title', 'View Job Cable')

<div class="col-md-12">
    <div class="body-">
        <div class="form-group row">
            <div class="col-md-6">
                <div class="form-group row">
                    <label for="cable_id_type" class="col-md-6 col-form-label text-md-left"><strong>{{ __('Cable Id Type') }}</strong></label>
                    <div class="col-md-6 view-details">
                        <span>{{$jobCable->cableType->cable_name}}</span>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="custom_id" class="col-md-6 col-form-label text-md-left"><strong>{{ __('Custom Id') }}</strong></label>
                    <div class="col-md-6 view-details">
                        <span>{{$jobCable->custom_id}}</span>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="cable_type" class="col-md-6 col-form-label text-md-left"><strong>{{ __('Cable Type') }}</strong></label>

                    <div class="col-md-6 view-details">
                        <span>{{$jobCable->cableIdType->cable_type_id}}</span>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="cores" class="col-md-6 col-form-label text-md-left"><strong>{{ __('Cores') }}</strong></label>
                    <div class="col-md-6 view-details">
                        <span>{{$jobCable->cableIdType->cores}}</span>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="size" class="col-md-6 col-form-label text-md-left"><strong>{{ __('Size') }}</strong></label>
                    <div class="col-md-6 view-details">
                        <span>{{$jobCable->size}}</span>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="additional_information" class="col-md-6 col-form-label text-md-left"><strong>{{ __('Additional Information') }}</strong></label>
                    <div class="col-md-6 view-details">
                        <span>{{$jobCable->additional_information??'--'}}</span>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group row">
                    <label for="cable_id" class="col-md-6 col-form-label text-md-left"><strong>{{ __('Cable Id') }}</strong></label>
                    <div class="col-md-6 view-details">
                        <span>{{$jobCable->cable_id}}</span>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="unique_code" class="col-md-6 col-form-label text-md-left"><strong>{{ __('Unique Code') }}</strong></label>
                    <div class="col-md-6 view-details">
                        <span>{{$jobCable->unique_code}}</span>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="to" class="col-md-6 col-form-label text-md-left"><strong>{{ __('To') }}</strong></label>
                    <div class="col-md-6 view-details">
                        <span>{{$jobCable->jobCableTo->jobLocation->location_name}}</span>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="from" class="col-md-6 col-form-label text-md-left"><strong>{{ __('From') }}</strong></label>
                    <div class="col-md-6 view-details">
                        <span>{{$jobCable->jobCableFrom->jobLocation->location_name}}</span>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="description" class="col-md-6 col-form-label text-md-left"><strong>{{ __('Description') }}</strong></label>
                    <div class="col-md-6 view-details">
                        <span>{{$jobCable->description}}</span>
                    </div>
                </div>
            </div> 
        </div>
    </div>
</div>