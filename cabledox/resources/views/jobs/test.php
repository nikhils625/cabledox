
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<title>Drag and drop Image Uploader Example</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/4.3.1/flatly/bootstrap.min.css">
<link href="https://www.jqueryscript.net/demo/drag-drop-image-uploader/dist/image-uploader.min.css" rel="stylesheet">
<style>
  .container { margin: 150px auto; max-width: 960px; }
  .modal {
            position: fixed;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            background: rgba(0, 0, 0, .5);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal .content {
            background: #fff;
            display: inline-block;
            padding: 2rem;
            position: relative;
        }

        .modal .content h4 {
            margin-top: 0;
        }

        .modal .content a.close {
            position: absolute;
            top: 1rem;
            right: 1rem;
            color: inherit;
        }

</style>
</head>

<body>
  <div class="container">
    <h1>Drag and drop Image Uploader Example</h1>
    <div class="jquery-script-ads" style="margin:50px auto"><script type="text/javascript"><!--
google_ad_client = "ca-pub-2783044520727903";
/* jQuery_demo */
google_ad_slot = "2780937993";
google_ad_width = 728;
google_ad_height = 90;
//-->
</script>
<script type="text/javascript"
src="https://pagead2.googlesyndication.com/pagead/show_ads.js">
</script></div>
    <form method="POST" name="form-example-2" id="form-example-2" enctype="multipart/form-data">

            <div class="input-field">
              <label for="name-2" class="active">Name</label>
                <input type="text" name="name-2" id="name-2" value="John Doe" class="form-control">

            </div>

            <div class="input-field">
              <label for="description-2" class="active">Description</label>
                <input type="text" name="description-2" id="description-2"
                       value="This form is already filed with some data, including images!" class="form-control">

            </div>

            <div class="input-field">
                <label class="active">Photos</label>
                <div class="input-images-2" style="padding-top: .5rem;padding-bottom: .5rem;"></div>
            </div>

            <button class="btn btn-danger">Submit and display data</button>

        </form>

<div id="show-submit-data" class="modal" style="visibility: hidden;">
        <div class="content">
            <h4>Submitted data:</h4>
            <p id="display-name"><strong>Name:</strong> <span></span></p>
            <p id="display-description"><strong>Description:</strong> <span></span></p>
            <p><strong>Uploaded images:</strong></p>
            <ul id="display-new-images"></ul>
            <p><strong>Preloaded images:</strong></p>
            <ul id="display-preloaded-images"></ul>
            <a href="javascript:$('#show-submit-data').css('visibility', 'hidden')" class="close"><i
                    class="material-icons">close</i></a>
        </div>
    </div>

  </div>
<script src="https://code.jquery.com/jquery-1.12.4.min.js" integrity="sha384-nvAa0+6Qg9clwYCGGPpDQLVpLNn0fRaROjHqs13t4Ggj3Ez50XnGQqc/r8MhnRDZ" crossorigin="anonymous"></script>
<script src="https://www.jqueryscript.net/demo/drag-drop-image-uploader/dist/image-uploader.min.js"></script>
<script>
  let preloaded = [
            {id: 1, src: 'https://picsum.photos/500/500?random=1'},
            {id: 2, src: 'https://picsum.photos/500/500?random=2'},
            {id: 3, src: 'https://picsum.photos/500/500?random=3'},
            {id: 4, src: 'https://picsum.photos/500/500?random=4'},
            {id: 5, src: 'https://picsum.photos/500/500?random=5'},
            {id: 6, src: 'https://picsum.photos/500/500?random=6'},
        ];

        $('.input-images-2').imageUploader();

        $('form').on('submit', function (event) {

            // Stop propagation
            event.preventDefault();
            event.stopPropagation();

            // Get some vars
            let $form = $(this),
                $modal = $('.modal');

            // Set name and description
            $modal.find('#display-name span').text($form.find('input[id^="name"]').val());
            $modal.find('#display-description span').text($form.find('input[id^="description"]').val());

            // Get the input file
            let $inputImages = $form.find('input[name^="images"]');
            if (!$inputImages.length) {
                $inputImages = $form.find('input[name^="photos"]')
            }

            // Get the new files names
            let $fileNames = $('<ul>');
            for (let file of $inputImages.prop('files')) {
                $('<li>', {text: file.name}).appendTo($fileNames);
            }

            // Set the new files names
            $modal.find('#display-new-images').html($fileNames.html());

            // Get the preloaded inputs
            let $inputPreloaded = $form.find('input[name^="old"]');
            if ($inputPreloaded.length) {

                // Get the ids
                let $preloadedIds = $('<ul>');
                for (let iP of $inputPreloaded) {
                    $('<li>', {text: '#' + iP.value}).appendTo($preloadedIds);
                }

                // Show the preloadede info and set the list of ids
                $modal.find('#display-preloaded-images').show().html($preloadedIds.html());

            } else {

                // Hide the preloaded info
                $modal.find('#display-preloaded-images').hide();

            }

            // Show the modal
            $modal.css('visibility', 'visible');
        });

        // Input and label handler
        $('input').on('focus', function () {
            $(this).parent().find('label').addClass('active')
        }).on('blur', function () {
            if ($(this).val() == '') {
                $(this).parent().find('label').removeClass('active');
            }
        });

</script>
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-36251023-1']);
  _gaq.push(['_setDomainName', 'jqueryscript.net']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
</body>
</html>
