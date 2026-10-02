@extends('layouts.app')
@section('title', 'Add Text to Image — Free Online Tool')
@section('meta_description', 'Add styled text captions and headings onto any photo easily')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3">Add Text to Image</h1>
            <p class="lead small text-muted">Upload a photo and add clean styled text with your choice of font, size, color and position, then download it.</p>

                    <label class="form-label fw-semibold" for="atFile">Choose a photo</label>
                    <input type="file" id="atFile" class="form-control" accept="image/*">
                    <label class="form-label mt-3" for="atText">Your text</label>
                    <input type="text" id="atText" class="form-control" value="Your caption here">
                    <div class="row g-3 mt-1">
                        <div class="col-md-3"><label class="form-label" for="atFont">Font</label><select id="atFont" class="form-select"><option value="Arial">Arial</option><option value="Georgia">Georgia</option><option value="Verdana">Verdana</option><option value="Courier New">Courier New</option><option value="Impact">Impact</option><option value="Trebuchet MS">Trebuchet MS</option></select></div>
                        <div class="col-md-2"><label class="form-label" for="atSize">Size (px)</label><input type="number" id="atSize" class="form-control" value="64" min="8" max="600"></div>
                        <div class="col-md-2"><label class="form-label" for="atColor">Color</label><input type="color" id="atColor" class="form-control form-control-color w-100" value="#ffffff"></div>
                        <div class="col-md-3"><label class="form-label" for="atPos">Position</label><select id="atPos" class="form-select"><option value="bottom">Bottom center</option><option value="top">Top center</option><option value="center">Middle center</option></select></div>
                        <div class="col-md-2"><label class="form-label" for="atStyle">Style</label><select id="atStyle" class="form-select"><option value="">Normal</option><option value="bold">Bold</option><option value="italic">Italic</option><option value="bold italic">Bold italic</option></select></div>
                    </div>
                    <div class="form-check mt-2"><input type="checkbox" id="atStroke" class="form-check-input" checked><label class="form-check-label" for="atStroke">Dark outline so text stays readable</label></div>
                    <div class="text-center mt-3"><canvas id="atCanvas" class="img-fluid border rounded d-none"></canvas></div>
                    <button type="button" id="atGo" class="btn btn-primary btn-lg w-100 mt-3" disabled>Download Image With Text</button>

            <div id="atMsg" class="alert alert-info d-none mt-3" role="alert"></div>
        </div>
    </div>
    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                    <li>Upload a photo.</li>
                    <li>Type your text and style it with font, size, color and position controls.</li>
                    <li>Download the finished image as a PNG.</li>
            </ol>
            <p class="small text-muted mb-0">Font size is in pixels at full image resolution, so large photos need larger sizes than the preview suggests.</p>
        </div>
    </div>
</div>
@endsection
@section('scripts')

<script>
(function () {
    'use strict';

    var msgBox = document.getElementById("atMsg");
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

    var img = null;
    function draw() {
        if (!img) return;
        var cv = document.getElementById("atCanvas"); cv.width = img.naturalWidth; cv.height = img.naturalHeight;
        var ctx = cv.getContext("2d"); ctx.drawImage(img, 0, 0);
        var size = Math.max(8, Number(document.getElementById("atSize").value) || 64);
        var style = document.getElementById("atStyle").value;
        ctx.font = (style ? style + " " : "") + size + "px " + document.getElementById("atFont").value;
        ctx.textAlign = "center"; ctx.textBaseline = "middle";
        var txt = document.getElementById("atText").value, pos = document.getElementById("atPos").value;
        var y = pos === "top" ? size : (pos === "center" ? cv.height / 2 : cv.height - size);
        if (document.getElementById("atStroke").checked) { ctx.lineWidth = Math.max(2, size / 12); ctx.strokeStyle = "rgba(0,0,0,0.85)"; ctx.strokeText(txt, cv.width / 2, y); }
        ctx.fillStyle = document.getElementById("atColor").value; ctx.fillText(txt, cv.width / 2, y);
        cv.classList.remove("d-none");
    }
    document.getElementById("atFile").addEventListener("change", function () {
        var f = this.files && this.files[0]; if (!f) return;
        var im = new Image(); im.onload = function () { img = im; draw(); document.getElementById("atGo").disabled = false; showMsg("Loaded " + f.name + "."); };
        im.onerror = function () { showMsg("That file could not be read as an image.", false); };
        im.src = URL.createObjectURL(f);
    });
    ["atText", "atFont", "atSize", "atColor", "atPos", "atStyle", "atStroke"].forEach(function (id) { document.getElementById(id).addEventListener("input", draw); document.getElementById(id).addEventListener("change", draw); });
    document.getElementById("atGo").addEventListener("click", function () { if (!img) return; draw(); document.getElementById("atCanvas").toBlob(function (b) { if (b) dl(b, "text-on-image.png"); }, "image/png"); });

})();
</script>
@endsection
