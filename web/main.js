/**
 * Created by arodriguez on 15/02/17.
 */

function handleUrl(event) {

    moveUploadInputToTop();

    var clipboardData = event.clipboardData || event.originalEvent.clipboardData || window.clipboardData;
    var url = clipboardData.getData('text');

    //@TODO: extract all the appends, validate image and display error
    appendImageToElement('#preview', 'preview-img', url, 'img-thumbnail img-responsive grayscale', 'Picture you just upload!');

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
            getFeaturedImages(response.name);
            getEmbedVideos(response.name);
        },
        error: function(jqXHR, textStatus, errorMessage) {
            console.log(errorMessage); // Optional
        }
    });
}

function handleFiles(files) {

    moveUploadInputToTop();

    var file = files[0];
    var imageType = /^image\//;

    if (!imageType.test(file.type)) {
        // @TODO: add proper validation here
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
            getFeaturedImages(response.name);
            getEmbedVideos(response.name);
        },
        error: function(jqXHR, textStatus, errorMessage) {
            console.log(errorMessage); // Optional
        }
    });
}

function displayPreview(file) {

    var reader = new FileReader();

    reader.onload = function (e) {
        appendImageToElement('#preview', 'preview-img', e.target.result, 'img-thumbnail img-responsive grayscale', 'Picture you just upload!');
    }

    reader.readAsDataURL(file);
}

function displayLookALike(imagePath) {

    appendImageToElement('#look-a-like', 'look-a-like-img', imagePath, 'img-thumbnail img-responsive', 'Picture of the look a like person');
}

function getFeaturedImages(name) {

    $.ajax({
        url: "/featured-pictures/" + name,
        type: "GET",
        contentType: false,
        success: function(response) {
            displayFeaturedImages(response.featuredImagePaths);
        },
        error: function(jqXHR, textStatus, errorMessage) {
            console.log(errorMessage); // Optional
        }
    });
}

function displayFeaturedImages(imagePaths) {

    for (var index in imagePaths) {

        var img = $('<img />', {
            src: imagePaths[index],
            class: 'img-thumbnail img-responsive',
            alt: 'Featured image'
        });

        var div = $('<div/>', {
            class: 'col-xs-4',
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
            displayEmbedVideos(response.embedIds);
        },
        error: function(jqXHR, textStatus, errorMessage) {
            console.log(errorMessage); // Optional
        }
    });
}

function displayEmbedVideos(embedIds) {

    for (var embedId in embedIds) {
        var iframe = '<div class="col-xs-6"><iframe class="embed-responsive-item" src="http://www.pornhub.com/embed/' + embedIds[embedId] + '" scrolling="no"></iframe></div>';
        $('#embedVideos').append(iframe);
    }
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

function grayscale(div,millisec,bool){
    if (bool){ /* We want to become grayscale */
        var i = 0;
        timertogray = setInterval(function addgray(){
            if (i < 101){
                document.getElementById(div).style.filter = "grayscale(" + i + "%)";
                i = i + 10;
            }else{
                clearInterval(timertogray); /* once the grayscale is 100%, we stop timer */
            }
        }, millisec);
    }else{ /* We want to give color back */
        var i = 100;
        timerfromgray = setInterval(function addgray(){
            if (i > 0){

                $('#'.div).css({
                    '-webkit-filter': 'grayscale('+i+'%)'
                });

                //document.getElementById(div).style.filter = "grayscale(" + i + "%)";
                i = i - 10;
            }else{
                clearInterval(timerfromgray); /* once the grayscale is 0%, we stop timer */
            }
        }, millisec);
    }
}
