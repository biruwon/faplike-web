/**
 * Created by arodriguez on 15/02/17.
 */

function handleUrl(url) {

    if (!url) {
        showAlertError('There is no valid url');
        return;
    }

    moveUploadInputToTop();

    //@TODO: encapsulate the nprogress for both and configure it with better steps
    NProgress.start();

    var formData = new FormData();
    formData.append('upload_form[url]', url);

    $.ajax({
        url: "/upload-from-url",
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {

            //@TODO: extract all the appends, validate image and display error
            appendImageToElement('#preview', 'preview-img', url, 'img-responsive center-block img-border', 'Picture you just upload!');

            displayLookALike(response.mainImage, response.name);
            displayTryAgainButton();
            getFeaturedImages(response.name);
            getEmbedVideos(response.name);
            NProgress.done();
        },
        error: function(jqXHR) {
            handleError(jqXHR);
            NProgress.done();
        }
    });
}

function checkOnUrlPaste(event) {

    var clipboardData = event.clipboardData || event.originalEvent.clipboardData || window.clipboardData;
    var url = clipboardData.getData('text');

    handleUrl(url);
}

function checkIfUrlEnter(event) {

    if(event && event.keyCode == 13) {
        var url = $('#input_url').val();
        handleUrl(url);
    }
}

function handleFiles(files) {

    moveUploadInputToTop();

    var file = files[0];

    if (isValidImage(file)) {

        NProgress.start();

        fileUpload(file);

    } else {

        showAlertError('You need to upload a valid image');
    }
}

function isValidImage(file) {

    var imageType = /^image\//;

    return imageType.test(file.type);
}

function handleError(jqXHR) {

    switch (jqXHR.status) {
        case 500:
            showAlertError('An internal error happen occurred, please try again later');
            break;
        case 400:
            showAlertError(jqXHR.responseJSON.message);
            break;
        default:
            showAlertError('An internal error happen occurred, please try again later');
    }
}

function showAlertError(message) {

    var alert = '' +
        '<div id="uploadError" class="alert alert-danger alert-dismissible" role="alert">' +
        '<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>' +
        '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
        message + '</div>' +
    '';

    $('.container').prepend(alert);
    $(".alert-dismissible").delay(5000).slideUp(500, function(){
        $(".alert-dismissible").alert('close');
    });
}

function fileUpload(file) {

    var formData = new FormData();
    formData.append('upload_form[image]', file);

    $.ajax({
        url: "/upload-image",
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            displayPreview(file);
            displayLookALike(response.mainImage, response.name);
            displayTryAgainButton();
            getFeaturedImages(response.name);
            getEmbedVideos(response.name);
            NProgress.done();
        },
        error: function(jqXHR) {
            handleError(jqXHR);
            NProgress.done();
        }
    });
}

function displayPreview(file) {

    var reader = new FileReader();

    reader.onload = function (e) {
        appendImageToElement('#preview', 'preview-img', e.target.result, 'img-responsive center-block img-border', 'Picture you just upload!');
    }

    reader.readAsDataURL(file);
}

function displayLookALike(imagePath, name) {

    nameToSearch = name.replace(/-/g, " ");

    $('#matchingInfo').append('' +
        '<p>Your picture lookalike is </p>'+ '<p><b>' + nameToSearch + '</b></p>' +
    '');

    appendImageToElement('#look-a-like', 'look-a-like-img', imagePath, 'img-responsive center-block img-border', 'Picture of the look a like person');
}

function getFeaturedImages(name) {

    $.ajax({
        url: "/featured-pictures/" + name,
        type: "GET",
        contentType: false,
        success: function(response) {
            displayFeaturedImages(response.featuredImagePaths, name);
        },
        error: function(jqXHR, textStatus, errorMessage) {
            console.log(errorMessage); // Optional
        }
    });
}

function displayFeaturedImages(imagePaths, name) {

    nameToSearch = name.replace(/-/g, " ");

    $('#actressPictures .description').removeClass('hidden').addClass('show');
    $('#actressPictures .description').append('<b>' + nameToSearch + '</b>');

    for (var index in imagePaths) {

        var img = $('<img />', {
            src: imagePaths[index],
            class: 'img-responsive center-block img-border',
            alt: 'Featured image'
        });

        var div = $('<div/>', {
            class: 'col-xs-12 col-md-4',
            html: img
        });

        div.appendTo($('#actressPictures'));
    }
}

function getEmbedVideos(name) {

    nameToSearch = name.replace(/-/g, " ");

    $.ajax({
        url: "/embed/" + nameToSearch,
        type: "GET",
        contentType: false,
        success: function(response) {
            displayEmbedVideos(response.videoInfoList, nameToSearch);
        },
        error: function(jqXHR, textStatus, errorMessage) {
            console.log(errorMessage); // Optional
        }
    });
}

function displayEmbedVideos(videoInfoList, name) {

    $('#embedVideos .description').removeClass('hidden').addClass('show');
    $('#embedVideos .description').append('<b>' + name + '</b>');

    videoInfoList.forEach(function (videoInfo) {
        var video = '' +
            '<div class="col-xs-12 col-md-6"><img src="' + videoInfo.default_thumb + '" class="img-responsive img-border">' +
            '<a href="' + videoInfo.url + '" target="_blank"><span class="glyphicon glyphicon-play-circle" aria-hidden="true"></span></a>' +
            '</div>';

        $('#embedVideos').append(video);
    });
}

function appendImageToElement(elementId, imgId, imgSrc, imgClasses, imgAlt) {

    var img = $('<img />', {
        id: imgId,
        src: imgSrc,
        class: imgClasses,
        alt: imgAlt
    });

    img.appendTo($(elementId));
}

function moveUploadInputToTop() {

    $('#centerRow').removeClass('vertical-center-row').addClass('vertical-top-row');
}

function displayTryAgainButton() {

    $('.upload-form').replaceWith('' +
        '<div class="row text-center">' +
        '<button id="refreshPage" type="button" class="btn button-refresh" onclick="refreshPage()">Try again!</button>' +
        '</div>' +
        '');
}

function refreshPage() {
    location.reload();
}
