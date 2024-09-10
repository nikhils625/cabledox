@php
    $isEditable = ($isEditable == 0)? false : true;
    $readOnly = null;
    if(!$isEditable) {
        $readOnly = 'readonly';
    }
@endphp

@if(!$jobCable->cableIdType->cableMasterCoreDetails->isEmpty())
    <table class="table table-bordered table-stripped table-condensed">
        <thead>
            <tr>
                <th>
                    <strong>{{ __('Cores') }}</strong>
                    @if(isset($jobTermination))
                        <input type="hidden" name="termination_id" value="{{$jobTermination->id}}">
                    @endif
                </th>
                <th><strong>{{ __('Core Id') }}</strong></th>
                <th><strong>{{ __('Termination Location') }}</strong></th>
            </tr>
        </thead>
        <tbody>
            @foreach($jobCable->cableIdType->cableMasterCoreDetails as $coreKey => $core)
                @php
                    $coreIdValue = $terminationLocationValue = null;
                    if(isset($jobTermination->jobTerminationDetails[$coreKey])) {
                        $coreIdValue = $jobTermination->jobTerminationDetails[$coreKey]->core_id; 
                        $terminationLocationValue = $jobTermination->jobTerminationDetails[$coreKey]->termination_location;
                    }
                @endphp
                <tr>
                    <td>
                        {{$core->core_name}}

                        @if(isset($jobTermination->jobTerminationDetails[$coreKey]))
                            <input type="hidden" name="termination_detail[id][]" value="{{$jobTermination->jobTerminationDetails[$coreKey]->id}}">
                        @endif
                        <input type="hidden" name="termination_detail[cable_master_detail_id][]" value="{{$core->id}}" {{$readOnly}}>
                    </td>
                    <td>
                        <input type="text" name="termination_detail[core_id][]" id="core_id_{{$coreKey}}" class="form-control @error('core_id') is-invalid @enderror" value="{{ $coreIdValue }}" required data-parsley-type="number" autocomplete="core_id" autofocus placeholder="Please enter core id" {{$readOnly}}>
                        @error('core_id')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </td>
                    <td>
                        <input type="text" name="termination_detail[termination_location][]" id="termination_location_{{$coreKey}}" class="form-control @error('termination_location') is-invalid @enderror" value="{{ $terminationLocationValue }}" required autocomplete="termination_location" autofocus placeholder="Please enter termination location" {{$readOnly}}>
                        @error('termination_location')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif