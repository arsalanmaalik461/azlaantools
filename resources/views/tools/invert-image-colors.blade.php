@extends('layouts.app')
@section('title', 'Invert Image Colors — Free Online Tool')
@section('meta_description', 'Invert all colors of an image to create a negative effect photo')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3">Invert Image Colors</h1>
            <p class="lead small text-muted">Upload any image and invert every color to create a photo negative effect, then download the result.</p>

                    <label class="form-label fw-semibold" for="ivFile">Choose an image</label>
                    <input type="file" id="ivFile" class="form-control" accept="image/*">
                    <div class="row g-3 mt-3">
                        <div class="col-md-6 text-center"><p class="fw-semibold small">Original</p><img id="ivOrig" class="img-fluid rounded border d-none" style="max-height:280px" alt="Original"></div>
                        <div class="col-md-6 text-center"><p class="fw-semibold small">Inverted</p><canvas id="ivCanvas" class="img-fluid rounded border d-none" style="max-height:280px"></canvas></div>
                    </div>
                    <button type="button" id="ivGo" class="btn btn-primary btn-lg w-100 mt-3" disabled>Download Inverted Image</button>

            <div id="ivMsg" class="alert alert-info d-none mt-3" role="alert"></div>
        </div>
    </div>
    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                    <li>Upload an image to see the original and inverted versions side by side.</li>
                    <li>Check the negative effect preview.</li>
                    <li>Download the inverted image as a PNG.</li>
            </ol>
            <p class="small text-muted mb-0">Inversion replaces each color channel value v with 255 minus v. Transparency is kept as it is.</p>
        </div>
    </div>
</div>
@endsection
@section('scripts')

<script>
(function () {
    'use strict';

    var msgBox = document.getElementById("ivMsg");
    function showMsg(t, ok) { msgBox.textContent = t; msgBox.className = "alert mt-3 " + (ok === false ? "alert-danger" : "alert-info"); msgBox.classList.remove("d-none"); }

    function dl(blob, name) {
        var a = document.createElement("a");
        a.href = URL.createObjectURL(blob);
        a.download = name;
        document.body.appendChild(a);
        a.click();
        a.remove();
        setTimeout(function () { URL.revokeObjectURL(a.href); }, 3000);
    }
    function fmtBytes(b) {
        if (!b && b !== 0) return "-";
        if (b < 1024) return b + " B";
        if (b < 1048576) return (b / 1024).toFixed(1) + " KB";
        return (b / 1048576).toFixed(2) + " MB";
    }

    document.getElementById("ivFile").addEventListener("change", function () {
        var f = this.files && this.files[0]; if (!f) return;
        var url = URL.createObjectURL(f), im = new Image();
        im.onload = function () {
            var orig = document.getElementById("ivOrig"); orig.src = url; orig.classList.remove("d-none");
            var cv = document.getElementById("ivCanvas"); cv.width = im.naturalWidth; cv.height = im.naturalHeight;
            var ctx = cv.getContext("2d"); ctx.drawImage(im, 0, 0);
            try {
                var id = ctx.getImageData(0, 0, cv.width, cv.height), d = id.data;
                for (var i = 0; i < d.length; i += 4) { d[i] = 255 - d[i]; d[i + 1] = 255 - d[i + 1]; d[i + 2] = 255 - d[i + 2]; }
                ctx.putImageData(id, 0, 0);
            } catch (e) { showMsg("This image is too large to process pixel by pixel on this device.", false); return; }
            cv.classList.remove("d-none"); document.getElementById("ivGo").disabled = false;
            showMsg("Inverted " + f.name + " (" + cv.width + " x " + cv.height + ").");
        };
        im.onerror = function () { showMsg("That file could not be read as an image.", false); };
        im.src = url;
    });
    document.getElementById("ivGo").addEventListener("click", function () {
        document.getElementById("ivCanvas").toBlob(function (b) { if (b) dl(b, "inverted.png"); }, "image/png");
    });

})();
</script>
@endsection
