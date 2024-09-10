@section('parentPageTitle', 'Add Comment')
@section('title', 'Add Comment') 

@php
    $isEditable = ($isEditable == 0)? false : true;
    $readOnly = null;
    if(!$isEditable) {
        $readOnly = 'readonly';
    }
@endphp
<div class="row clearfix">
    <div class="col-md-12">
        <div class="comments-page-body">
            <div class="form-group row">
                <div class="col-md-12">
                    <div class="form-group row">
                        <label for="cable_id_type" class="col-md-4 col-form-label text-md-left"><strong>{{ __('Reported Issue') }}</strong></label>
                        <div class="col-md-8 view-details">
                            <span>{{ $reportedIssue->description }}</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="cable_id_type" class="col-md-4 col-form-label text-md-left"><strong>{{ __('Priority') }}</strong></label>
                        <div class="col-md-8 view-details">                  
                            @php 
                                if($reportedIssue->priority == '0'){ 
                                   $priority =  'Low'; 
                                }
                                elseif($reportedIssue->priority == '1'){
                                    $priority =  'Normal';
                                }
                                elseif($reportedIssue->priority == '2'){
                                    $priority =  'Medium';
                                }
                                else{
                                    $priority =  'High';
                                }
                            @endphp
                            <span>{{ $priority }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data Loader -->
            <!--<div class="auto-load text-center">
                <svg version="1.1" id="L9" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                    x="0px" y="0px" height="60" viewBox="0 0 100 100" enable-background="new 0 0 0 0" xml:space="preserve">
                    <path fill="#000"
                        d="M73,50c0-12.7-10.3-23-23-23S27,37.3,27,50 M30.9,50c0-10.5,8.5-19.1,19.1-19.1S69.1,39.5,69.1,50">
                        <animateTransform attributeName="transform" attributeType="XML" type="rotate" dur="1s"
                            from="0 50 50" to="360 50 50" repeatCount="indefinite" />
                    </path>
                </svg>
            </div>-->   
            <div class="chatapp_body" @if(!$isEditable) style="border-bottom: #ececec solid 1px;" @endif> 
                <div class="chat-history">
                    <ul class="message_data">
                        @foreach($reportedIssueComment as $reportedComment)
                        <li class="@if($reportedComment->user_id == Auth::user()->id) right @else left @endif clearfix">
                            <div class="message">
                                <span>{!! nl2br($reportedComment->comments) !!}</span>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            @if($isEditable)
                <form id="commentForm" data-parsley-validate novalidate>
                    <div class="form-group">
                        <div class="input-group">
                            <textarea class="form-control comment resize-none" placeholder="Enter comment here..." required data-parsley-errors-container="#comment-error-block"></textarea>
                            <div class="input-group-prepend">
                                <button type="submit" class="btn btn-outline-success" id="add_comment">{{ __('Send') }}</button>
                            </div>
                        </div>
                        <div class="margin-top10" id="comment-error-block"></div>
                    </div>
                </form> 
            @endif
        </div>
    </div>
</div> 

<script type="text/javascript">
$(function () {

    var instance = $('#commentForm').parsley();
    $("#add_comment").click(function(e) {
       e.preventDefault(); 

        if(!instance.isValid()) {
            instance.validate();
        } else {
            var comment_val = $('.comment').val();

            var reported_issue_id = '{{$reportedIssue->id}}';

            var _token  = '{{csrf_token()}}';

            $.ajax({
                type: "POST",
                url: "{{ route('report-issue.save_comment') }}",
                data: {'_token' : _token, 'comment_val': comment_val,'reported_issue_id': reported_issue_id},
                    success: function(res){
                    if(res.status == 1) {
                        $(".message_data").append('<li class="right clearfix"><div class="message"><span>'+nl2br(res.comment.comments)+'</span></div></li>');
                         /**/
                            setTimeout(function () {
                                swal("Comment successfully sent!", res.message, "success");
                                $('.comment').val('');
                            }, 100);                        /**/

                    } else {
                        swal("Error:", res.message, "error");
                    }
                }
            });
        }
    });
});
function nl2br(str, is_xhtml) {
    var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br/>' : '<br>';    
    return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1'+ breakTag +'$2');
}
</script>  