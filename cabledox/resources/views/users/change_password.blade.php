<form method="POST" action="{{ route('users.change-password') }}" enctype="multipart/form-data" data-parsley-validate novalidate>
    @csrf
    <div class="form-group row">
        <div class="col-md-8">
            <div class="form-group row">
                <label for="old_password" class="col-md-4 col-form-label text-md-right">{{ __('Old Password') }}<span class="text-danger"> *</span></label>
                <div class="col-md-6">
                    <input type="password" name="old_password" id="old_password" class="form-control @error('old_password') is-invalid @enderror" value="" required autocomplete="old_password"  autofocus placeholder="Please enter old password">
                    @error('old_password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
            <div class="form-group row">
                <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('New Password') }}<span class="text-danger"> *</span></label>
                <div class="col-md-6">
                    <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" value="" required autocomplete="password" autofocus data-parsley-length="[8, 15]" data-parsley-equalto="#confirm_password" placeholder="Please enter new password">
                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
            <div class="form-group row">
                <label for="confirm_password" class="col-md-4 col-form-label text-md-right">{{ __('Confirm Password') }}<span class="text-danger"> *</span></label>
                <div class="col-md-6">
                    <input type="password" name="confirm_password" id="confirm_password" class="form-control @error('confirm_password') is-invalid @enderror" value="" required autocomplete="confirm_password" data-parsley-equalto="#password" autofocus placeholder="Please enter confirm password">
                    @error('confirm_password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
        </div>
    </div>                       
    <div class="form-group">
        <a href="{{route('dashboard.index')}}" class="btn btn-default" title="Back">{{ __('Back') }}</a>
        <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
    </div>
</form>