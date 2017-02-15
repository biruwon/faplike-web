/**
 * Created by arodriguez on 15/02/17.
 */
function sendFile(files) {
    new FileUpload()
}

function FileUpload(img, file) {
    var reader = new FileReader();
    this.ctrl = createThrobber(img);
    var xhr = new XMLHttpRequest();
    this.xhr = xhr;

    var self = this;
    this.xhr.upload.addEventListener("progress", function(e) {
        if (e.lengthComputable) {
            var percentage = Math.round((e.loaded * 100) / e.total);
            self.ctrl.update(percentage);
        }
    }, false);

    xhr.upload.addEventListener("load", function(e){
        self.ctrl.update(100);
        var canvas = self.ctrl.ctx.canvas;
        canvas.parentNode.removeChild(canvas);
    }, false);
    xhr.open("POST", "http://demos.hacks.mozilla.org/paul/demos/resources/webservices/devnull.php");
    xhr.overrideMimeType('text/plain; charset=x-user-defined-binary');
    reader.onload = function(evt) {
        xhr.send(evt.target.result);
    };
    reader.readAsBinaryString(file);
}


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
}
