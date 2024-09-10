<div class="col-md-12">
    <table class="table table-bordered table-stripped table-condensed">
        <thead>
            <tr>
                <td>No.</td>
                <td width="20%">Item</td>
                <td width="40%">Completed (Y, N, N/A)</td>
                <td width="30%">Comment</td>
            </tr>
        </thead>
        <tbody>
            @php
                $jobsDir         = \Config::get('constants.uploadPaths.viewJobs');
                $inspectorPath   = \Config::get('constants.uploadPaths.inspectorSignatureImage');
                $pcInspectorPath = \Config::get('constants.uploadPaths.pcInspectorSignatureImage');
                $finalCheckSeetDocumentPath = \Config::get('constants.uploadPaths.finalCheckSeetDocument');

                $jobNumber      = $job->job_number;

                $inspectorDir   = $jobsDir . $jobNumber.DIRECTORY_SEPARATOR.$inspectorPath;
                $pcInspectorDir = $jobsDir . $jobNumber.DIRECTORY_SEPARATOR.$pcInspectorPath;

                $finalCheckSeetDocumentDir = $jobsDir . $jobNumber.DIRECTORY_SEPARATOR.$finalCheckSeetDocumentPath;
            @endphp

            @if(!$questionnaire->isEmpty())
                @foreach($questionnaire as $key => $question)
                    @php
                        $completedValue = $commentValue = null;

                        if(isset($jobFinalCheckSheet->jobFinalCheckSheetDetails[$key])) {
                            $completedValue = $jobFinalCheckSheet->jobFinalCheckSheetDetails[$key]->completed; 
                            $commentValue   = $jobFinalCheckSheet->jobFinalCheckSheetDetails[$key]->comment;
                        }
                    @endphp
                    <tr>
                        <td>{{$loop->iteration}}</td>
                        <td width="20%">
                            <div title="{{$question->question_name}}">
                                @php
                                    $questionName = nl2br($question->question_name);
                                    $questionLength = strlen($questionName);
                                    if($questionLength > 80) {
                                        $questionName = substr($questionName, 0, 70). '...';
                                    }
                                @endphp
                                {!! $questionName !!}
                            </div>
                        </td>
                        <td width="40%">
                            <input type="text" name="questionnaire[completed][]" id="completed_{{$key}}" class="form-control" value="{{ $completedValue }}" readonly>
                        </td>
                        <td width="30%">
                            <textarea name="questionnaire[comment][]" id="comment_{{$key}}" class="form-control textarea"  readonly cols="50" rows="3">{{ $commentValue }}</textarea>
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="4">No Questionnaire Found.</td>
                </tr>
            @endif
        </tbody>
    </table>
</div>

@php
    $inspectorName = $inspectorSignatureDate = null;
    $pcInspectorName = $pcInspectorSignatureDate = null;

    if(isset($jobFinalCheckSheet) && !empty($jobFinalCheckSheet)) {
        $inspectorName          = $jobFinalCheckSheet->inspector_name;
        $inspectorSignatureDate = $jobFinalCheckSheet->inspector_signature_date;

        $pcInspectorName          = $jobFinalCheckSheet->pc_inspector_name;
        $pcInspectorSignatureDate = $jobFinalCheckSheet->pc_inspector_signature_date;
    }
@endphp

<div class="form-group row">
    <div class="col-md-12">
        <label for="inspector_name" class="col-md-12 col-form-label text-md-left">{{ __('Inspector:') }}</label>
    </div>
    <div class="col-md-12">
        <div class="form-group row">
            <div class="col-md-4">
                <div class="form-group row">
                    <label for="inspector_name" class="col-md-4 col-form-label text-md-right">{{ __('Name') }}<span class="text-danger"> *</span></label>
                    <div class="col-md-8">
                        <input type="text" name="inspector_name" id="inspector_name" class="form-control" value="{{ $inspectorName }}" readonly>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group row">
                    <label for="inspector_signature" class="col-md-4 col-form-label text-md-right">{{ __('Signature') }}<span class="text-danger"> *</span></label>
                    <div class="col-md-8">
                        @php
                            $inspectorSignatureImage = null;
                            $inspectorImageName = null;
                            if(isset($jobFinalCheckSheet) && !empty($jobFinalCheckSheet) && !empty($jobFinalCheckSheet->inspector_signature)) {

                                $inspectorSignatureImage = asset($inspectorDir . $jobFinalCheckSheet->inspector_signature);

                                $inspectorImageName = $jobFinalCheckSheet->inspector_signature;
                        @endphp
                                <div class="signature-image-div @if(empty($inspectorImageName)) hidden @endif">
                                    <img src="{{ $inspectorSignatureImage }}" class="border border-info" alt="" style="width: 100%;">
                                </div>
                        @php
                            }
                        @endphp
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group row">
                    <label for="inspector_signature_date" class="col-md-4 col-form-label text-md-right">{{ __('Date') }}<span class="text-danger"> *</span></label>
                    <div class="col-md-8">
                        <input type="text" name="inspector_signature_date" id="inspector_signature_date" class="form-control" value="{{$inspectorSignatureDate}}" readonly>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="form-group row">
    <div class="col-md-12">
        <label for="pc_inspector_name" class="col-md-12 col-form-label text-md-left">{{ __('Principle Contractor Inspector:') }}</label>
    </div>
    <div class="col-md-12">
        <div class="form-group row">
            <div class="col-md-4">
                <div class="form-group row">
                    <label for="pc_inspector_name" class="col-md-4 col-form-label text-md-right">{{ __('Name') }}<span class="text-danger"> *</span></label>
                    <div class="col-md-8">
                        <input type="text" name="pc_inspector_name" id="pc_inspector_name" class="form-control" value="{{$pcInspectorName}}" readonly>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group row">
                    <label for="pc_inspector_signature" class="col-md-4 col-form-label text-md-right">{{ __('Signature') }}<span class="text-danger"> *</span></label>
                    <div class="col-md-8">
                        @php
                            $pcInspectorSignatureImage = null;
                            $pcInspectorImageName = null;
                            if(isset($jobFinalCheckSheet) && !empty($jobFinalCheckSheet) && !empty($jobFinalCheckSheet->pc_inspector_signature)) {
                                $pcInspectorSignatureImage = asset($pcInspectorDir . $jobFinalCheckSheet->pc_inspector_signature);

                                $pcInspectorImageName = $jobFinalCheckSheet->pc_inspector_signature;
                        @endphp
                                <div class="signature-image-div @if(empty($pcInspectorImageName)) hidden @endif">
                                    <img src="{{ $pcInspectorSignatureImage }}" width="auto" class="border border-info" alt="" style="width: 100%;">
                                </div>
                        @php
                            }
                        @endphp
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group row">
                    <label for="pc_inspector_signature_date" class="col-md-4 col-form-label text-md-right">{{ __('Date') }}<span class="text-danger"> *</span></label>
                    <div class="col-md-8">
                        <input type="text" name="pc_inspector_signature_date" id="pc_inspector_signature_date" class="form-control" value="{{$pcInspectorSignatureDate}}" readonly>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@php 
    $filePath = $finalCheckSeetDocumentDir;
    $viewFinalCheckSheetDocument = null;

    if(isset($jobFinalCheckSheet->upload_image) && !empty($jobFinalCheckSheet->upload_image)) {
        $viewFinalCheckSheetDocument = asset($filePath . $jobFinalCheckSheet->upload_image);

        $pathToImage = base64_encode($filePath . $jobFinalCheckSheet->upload_image);
@endphp

        <div class="form-group row">
            <div class="col-md-12">
                <label for="pc_inspector_name" class="col-md-12 col-form-label text-md-left">{{ __('Final Check Sheet Document:') }}</label>
            </div>
            <div class="col-md-12">
                <div class="form-group row">
                    <div class="col-md-12">
                        <div class="form-group row">
                            <div class="col-md-12">
                                <a href="{{route('job.download-job-asset', $pathToImage)}}">
                                    <img class="dropify" data-default-file="{{$viewFinalCheckSheetDocument}}" data-show-remove="false"data-allowed-file-extensions="jpeg png jpg">
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

@php
    }
@endphp
<!-- Vertically centered finalCheckSheetDocumentModalCenter -->
<div class="modal fade" id="finalCheckSheetDocumentModalCenter" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-modal="true" aria-labelledby="finalCheckSheetDocumentModalCenterTitle">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="finalCheckSheetDocumentModalCenterTitle">Final Check Sheet Document</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="$('.dropify-clear').trigger('click');"><span aria-hidden="true">×</span></button>
            </div>
            <div class="modal-body">
                @php
                    $imageName = null; $finalCheckSheetDocument = null;
                    $filePath = $finalCheckSeetDocumentDir;

                    if(isset($jobFinalCheckSheet->upload_image) && !empty($jobFinalCheckSheet->upload_image)) {
                        $finalCheckSheetDocument = asset($filePath . $jobFinalCheckSheet->upload_image);

                        $imageName = $jobFinalCheckSheet->upload_image;
                    }
                @endphp
                <div class="form-group row">
                    <label for="file" class="col-md-3 col-form-label text-md-right">{{ __('Upload Image') }}<span class="text-danger"> *</span></label>
                    <div class="col-md-8">
                        <input type="file" name="upload_image" class="dropify"data-allowed-file-extensions="jpeg png jpg" data-default-file="{{$finalCheckSheetDocument}}">
                        <input type="hidden" value="{{$imageName}}" name="upload_image_old"/>
                    </div>
                    @error('upload_image')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" onclick="$('.dropify-clear').trigger('click');">Close</button>
                <button type="submit" class="btn btn-primary theme-bg">{{ __('Save changes') }}</button>
            </div>
        </div>
    </div>
</div>
<script src="{{ asset('assets/js/pages/forms/dropify.js') }}"></script>
<script type="text/javascript">
$(function () {

    var inspectorSignatureImage = "{{$pcInspectorSignatureImage}}";
    var validateInspectorSigPad = false;
    if(inspectorSignatureImage == '') {
        validateInspectorSigPad = true;
    }

    var pcInspectorSignatureImage = "{{$pcInspectorSignatureImage}}";
    var validatePcInspectorSigPad = false;
    if(pcInspectorSignatureImage == '') {
        validatePcInspectorSigPad = true;
    }

    initSigPad(validateInspectorSigPad, validatePcInspectorSigPad);

    $(document).on('click', "span.remove", function() {
        var _this    = $(this);
        var _target  = $(_this).data('init_target');
        var _parent  = $(_this).parent();
        var _sibling = $(_this).parent().siblings('div.sigPad');

        swal({
            title: "Are you sure?",
            text: "You want to edit this signature!",
            type: "warning",
            confirmButtonColor: "#dc3545",
            confirmButtonText: "Yes, I am sure!",
            showCancelButton: true,
            closeOnConfirm: true,
        }, function (isConfirm) {

            if (isConfirm) {
                $(_parent).addClass('hidden');
                $(_sibling).removeClass('hidden');

                var inspectorSigPadVisible = (!$('.inspectorSigPad').hasClass('hidden'));
                var pcSigPadVisible        = (!$('.pcSigPad').hasClass('hidden'));

                initSigPad(inspectorSigPadVisible, pcSigPadVisible, _target);     
            }
        });
    });
});

function initSigPad(validateInspectorSigPad = true, validatePcInspectorSigPad = true, _target) 
{
    var signaturePadoptions = {
      bgColour : '#fff',
      lineTop:45,
      drawOnly : true,
      validateFields: validateInspectorSigPad
    };

    var signaturePadoptions = {
      bgColour : '#fff',
      lineTop:45,
      drawOnly : true,
      validateFields: validatePcInspectorSigPad
    };

    if(_target == 'inspectorSigPad') {
        $('.inspectorSigPad').signaturePad(signaturePadoptions).init();
    } else if(_target == 'pcSigPad') {
        $('.pcSigPad').signaturePad(signaturePadoptions).init(); 
    } else {
        $('.inspectorSigPad').signaturePad(signaturePadoptions).init();
        $('.pcSigPad').signaturePad(signaturePadoptions).init(); 
    }
}
</script>