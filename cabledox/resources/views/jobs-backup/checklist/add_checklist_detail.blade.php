<div class="col-md-12">
    <div class="form-group row">
        @foreach($checklistMaster as $key => $checklist)
            <div class="col-md-6 ">
                @if(!$jobChecklistDetails->isEmpty())
                    <input type="hidden" name="checklist[{{$key}}][id]" value="{{@$jobChecklistDetails[$key]->id}}">
                @endif
                <label for="checklist_name-{{$key}}" class="row col-md-12 col-form-label text-md-left">{{ __($checklist->checklist_name) }}:<span class="text-danger"> *</span>
                    <input type="hidden" name="checklist[{{$key}}][checklist_master_id]" id="checklist_master-{{$key}}" value="{{$checklist->id}}">
                </label>
                <div class="form-group row">
                    <label for="checklist-{{$key}}" class="col-md-4 col-form-label text-md-left">{{ __('Name') }}:<span class="text-danger"> *</span></label>
                    <div class="col-md-6">
                        @php
                            $checkListName = null;
                            if(!$jobChecklistDetails->isEmpty() && $jobChecklistDetails[$key]->name) {
                                $checkListName = $jobChecklistDetails[$key]->name;
                            }
                        @endphp
                        <input type="text" name="checklist[{{$key}}][name]" id="checklist_name-{{$key}}" class="form-control" value="{{$checkListName}}" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="checklist_date-{{$key}}" class="col-md-4 col-form-label text-md-left">{{ __('Date') }}:<span class="text-danger"> *</span></label>
                    <div class="col-md-6">
                        @php
                            $checkListDate = null;
                            if(!$jobChecklistDetails->isEmpty() && $jobChecklistDetails[$key]->submit_date) {
                                $checkListDate = $jobChecklistDetails[$key]->submit_date;
                            }
                        @endphp
                        <input type="text" name="checklist[{{$key}}][date]" id="checklist_date-{{$key}}" class="form-control" value="{{$checkListDate}}" required data-date-format="yyyy/mm/dd" data-provide="datepicker" data-date-today-highlight="true" data-date-autoclose="true" data-date-container="#fill-checklist" placeholder="Select date" autocomplete="false">
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>