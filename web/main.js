/**
 * Created by arodriguez on 15/02/17.
 */


function handleFiles(files) {

    var preview = document.getElementById("preview");

    var file = files[0];
    var imageType = /^image\//;

    if (!imageType.test(file.type)) {
        console.log('Wrong type')
    }
    
    new FileUpload(file);
}

function FileUpload(file) {

    var form = document.getElementById('upload_form');
    var data = new FormData(form);

    $.ajax({
        url: "/upload-image",
        type: "POST",
        data: data,
        processData: false,
        contentType:false,
        success: function(response) {
            displayLookALike(response.mainImage);
        },
        error: function(jqXHR, textStatus, errorMessage) {
            console.log(errorMessage); // Optional
        }
    });
}

function displayLookALike(imagePath) {

    var lookALikeDiv = document.getElementById('look-a-like');
    var lookALikeImg = lookALikeDiv.firstElementChild;

    lookALikeImg.setAttribute('src', imagePath);
}
