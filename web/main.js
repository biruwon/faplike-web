/**
 * Created by arodriguez on 15/02/17.
 */

function handleUrl(event) {

    var clipboardData = event.clipboardData || event.originalEvent.clipboardData || window.clipboardData;
    var url = clipboardData.getData('text');

    //@TODO: extract all the appends, validate image and display error
    appendImageToElement('#preview', 'preview-img', url, 'img-thumbnail img-responsive', 'Picture you just upload!');

    var formData = new FormData();
    formData.append('upload_form[url]', url);

    $.ajax({
        url: "/upload-from-url",
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            displayLookALike(response.mainImage);
        },
        error: function(jqXHR, textStatus, errorMessage) {
            console.log(errorMessage); // Optional
        }
    });
}

function handleFiles(files) {

    var file = files[0];
    var imageType = /^image\//;

    if (!imageType.test(file.type)) {
        console.log('Wrong type')
    }

    displayPreview(file);
    fileUpload(file);
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
            displayLookALike(response.mainImage);
        },
        error: function(jqXHR, textStatus, errorMessage) {
            console.log(errorMessage); // Optional
        }
    });
}

function displayPreview(file) {

    var reader = new FileReader();

    reader.onload = function (e) {
        appendImageToElement('#preview', 'preview-img', e.target.result, 'img-thumbnail img-responsive', 'Picture you just upload!');
    }

    reader.readAsDataURL(file);
}

function displayLookALike(imagePath) {

    appendImageToElement('#look-a-like', 'look-a-like-img', imagePath, 'img-thumbnail img-responsive', 'Picture of the look a like person');

    $.ajax({
        url: "/embed/" + 'aaliyah ca pelle',
        type: "GET",
        contentType: false,
        success: function(response) {
            displayEmbedVideos(response.embedIds);
        },
        error: function(jqXHR, textStatus, errorMessage) {
            console.log(errorMessage); // Optional
        }
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

function displayEmbedVideos(embedIds) {
    console.log(embedIds);
    for (var embedId in embedIds) {
        var iframe = '<iframe class="embed-responsive-item" src="http://www.pornhub.com/embed/' + embedIds[embedId] + '" scrolling="no"></iframe>';
        $('#embedVideos').append(iframe);
    }
}
