@section('parentPageTitle', 'Basic Edit Job')
@section('title', 'Basic Edit Job')

<div class="row clearfix">
    <div class="col-lg-12">
        <div class="card">
            <form method="POST" action="{{ route('jobs.update', $job->id) }}"data-parsley-validate novalidate> 
            @csrf
            @method('PATCH')
                <div class="form-group row">
                    <div class="col-md-6">
                        <div class="form-group row">
                            <label for="job_number" class="col-md-4 col-form-label text-md-right">{{ __('Job Number') }}<span class="text-danger"> *</span></label>
                            <div class="col-md-6">
                                <input type="text" name="job_number" id="job_number" class="form-control  @error('job_number') is-invalid @enderror"  value="{{ $job->job_number }}" required autocomplete="job_number"  @if($role_name == "Electrician") readonly @endif autofocus placeholder="Please enter job number" >
                                @error('job_number')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="post_code" class="col-md-4 col-form-label text-md-right">{{ __('Postcode') }}<span class="text-danger"> *</span></label>
                            <div class="col-md-6">
                                <input type="text" name="post_code" id="post_code" class="form-control @error('post_code') is-invalid @enderror" value="{{ $job->post_code }}" required @if($role_name == "Electrician") readonly @endif autocomplete="post_code" autofocus placeholder="Please enter Post Code">
                                @error('post_code')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        @if($role_name == 'Admin') 
                        <div class="form-group row">
                            <label for="managers_user_id" class="col-md-4 col-form-label text-md-right">{{ __('Managers') }}<span class="text-danger"> *</span></label>
                            <div class="col-md-6 multiselect_div">
                                @php 
                                    $userIdArr = [];
                                    if(!@$job->jobUsers->isEmpty()) {
                                        $userIdArr = $job->jobUsers->pluck('user_id')->toArray();
                                    }
                                @endphp 
                                <select id="managers_user_id" data-live-search="true" name="user_id[]" class="selectpicker form-control" multiple required multiple title="Please select"  data-style="btn-info">
                                    @foreach($manager as$key => $manager)
                                        @php
                                        $selected = null;
                                        if(in_array($manager->id, $userIdArr)) {
                                            $selected = "selected";
                                        }
                                        @endphp
                                        <option value="{{$manager->id}}" {{$selected}}>{{$manager->first_name}} {{$manager->last_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @endif
                       
                        @if($role_name == 'Admin' || $role_name == 'Manager')
                        <div class="form-group row">
                            <label for="supervisors_user_id" class="col-md-4 col-form-label text-md-right">{{ __('Supervisor') }}<span class="text-danger"> *</span></label>
                            <div class="col-md-6 multiselect_div">
                                @php 
                                    $userIdArr = [];
                                    if(!@$job->jobUsers->isEmpty()) {
                                        $userIdArr = $job->jobUsers->pluck('user_id')->toArray();
                                    }
                                @endphp 
                                <select id="supervisors_user_id" data-live-search="true" name="user_id[]" class="selectpicker form-control" multiple required multiple title="Please select"  data-style="btn-info">
                                    @foreach($superVisor as$key => $superVisors)
                                        @php
                                        $selected = null;
                                        if(in_array($superVisors->id, $userIdArr)) {
                                            $selected = "selected";
                                        }
                                        @endphp
                                        <option value="{{$superVisors->id}}" {{$selected}}>{{$superVisors->first_name}} {{$superVisors->last_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @endif

                        @if($role_name == 'Admin' || $role_name == 'Manager' ||$role_name == 'Supervisor') 
                        <div class="form-group row">
                            <label for="electricians_user_id" class="col-md-4 col-form-label text-md-right">{{ __('Electrician') }}<span class="text-danger"> *</span></label>
                            <div class="col-md-6 multiselect_div">
                                @php 
                                    $userIdArr = [];
                                    if(!@$job->jobUsers->isEmpty()) {
                                        $userIdArr = $job->jobUsers->pluck('user_id')->toArray();
                                    }
                                @endphp 
                                <select id="electricians_user_id" data-live-search="true" name="user_id[]" class="selectpicker form-control" multiple required multiple title="Please select"  data-style="btn-info">
                                    @foreach($electrician as$key => $electricians)
                                        @php
                                        $selected = null;
                                        if(in_array($electricians->id, $userIdArr)) {
                                            $selected = "selected";
                                        }
                                        @endphp
                                        <option value="{{$electricians->id}}" {{$selected}}>{{$electricians->first_name}} {{$electricians->last_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @endif
                    </div>
                    <div class="col-md-6">  
                        <div class="form-group row">
                            <label for="site_name" class="col-md-4 col-form-label text-md-right">{{ __('Site Name') }}<span class="text-danger"> *</span></label>
                            <div class="col-md-6">
                                <input type="text" name="site_name" id="site_name" class="form-control @error('site_name') is-invalid @enderror" value="{{ $job->site_name }}" required autocomplete="site_name" @if($role_name == "Electrician") readonly @endif autofocus placeholder="Please enter site name">
                                @error('site_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>                               
                        </div>
                        <div class="form-group row">
                            <label for="address" class="col-md-4 col-form-label text-md-right">{{ __('Address') }}<span class="text-danger"> *</span></label>
                                <div class="col-md-6">
                                   <textarea name="address" class="form-control" id="address" @if($role_name == "Electrician") readonly @endif placeholder="Please enter address" rows="3" required>{{$job->address}}</textarea>
                                    @error('address')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>                               
                        </div>
                        <input type="hidden" name="job_id" value="{{$job->id}}"/>
                    </div> 
                </div>                       
                <div class="form-group">
                    <a href="{{route('jobs.index')}}" class="btn btn-default" title="Back">{{ __('Back') }}</a>
                    <button type="submit" class="btn btn-primary">
                        {{ __('Submit') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

