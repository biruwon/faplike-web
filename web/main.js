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

    var img = document.createElement("img");
    img.classList.add("obj");
    img.file = file;
    preview.appendChild(img); // Assuming that "preview" is the div output where the content will be displayed.

    var reader = new FileReader();
    reader.onload = (function(aImg) { return function(e) { aImg.src = e.target.result; }; })(img);
    reader.readAsDataURL(file);

    new FileUpload(file);
}

function FileUploadNotWorking(file) {

    var reader = new FileReader();
    var xhr = new XMLHttpRequest();
    this.xhr = xhr;

    xhr.open("POST", "/upload-image");
    xhr.overrideMimeType('image/png');
    reader.onload = function(event) {
        xhr.send(event.target.result);
    };
    reader.readAsBinaryString(file);
}

function FileUpload(file) {

    var form = document.getElementById('upload_form');
    var data = new FormData(form);

    $.ajax({
        url: "/upload-image",
        type: "post",
        data: data,
        processData: false,
        contentType:false,
        success: function(response) {
            // .. do something
        },
        error: function(jqXHR, textStatus, errorMessage) {
            console.log(errorMessage); // Optional
        }
    });
}
