/**
 * Created by arodriguez on 15/02/17.
 */

function handleUrl(event) {

    moveUploadInputToTop();

    var clipboardData = event.clipboardData || event.originalEvent.clipboardData || window.clipboardData;
    var url = clipboardData.getData('text');

    //@TODO: extract all the appends, validate image and display error
    appendImageToElement('#preview', 'preview-img', url, 'img-responsive center-block img-border', 'Picture you just upload!');

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
            displayLookALike(response.mainImage, response.name);
            displayTryAgainButton();
            getFeaturedImages(response.name);
            getEmbedVideos(response.name);
            NProgress.done();
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

    NProgress.start();

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
            displayLookALike(response.mainImage, response.name);
            displayTryAgainButton();
            getFeaturedImages(response.name);
            getEmbedVideos(response.name);
            NProgress.done();
        },
        error: function(jqXHR, textStatus, errorMessage) {
            console.log(errorMessage); // Optional
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
        '<p>Your picture lookalike is '+ '<b>' + nameToSearch + '</b>' + ' with a 90%</p>' +
        '<p>Maybe you just discover someone doing porn?</p>' +
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
            displayEmbedVideos(response.embedIds, nameToSearch);
        },
        error: function(jqXHR, textStatus, errorMessage) {
            console.log(errorMessage); // Optional
        }
    });
}

function displayEmbedVideos(embedIds, name) {

    $('#embedVideos .description').removeClass('hidden').addClass('show');
    $('#embedVideos .description').append('<b>' + name + '</b>');

    for (var embedId in embedIds) {
        var iframe = '<div class="col-xs-12 col-md-6"><iframe class="embed-responsive-item img-border" src="http://www.pornhub.com/embed/' + embedIds[embedId] + '" scrolling="no"></iframe></div>';
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
