@extends('layouts.app')
@section('title', 'Black and White Image Converter — Free Online Tool')
@section('meta_description', 'Convert color photos to black and white with contrast control')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3">Black and White Image Converter</h1>
            <p class="lead small text-muted">Convert any color photo to black and white and fine tune the contrast for a classic monochrome look.</p>

                    <label class="form-label fw-semibold" for="bwFile">Choose a photo</label>
                    <input type="file" id="bwFile" class="form-control" accept="image/*">
                    <div class="row g-3 mt-2">
                        <div class="col-md-6"><label class="form-label" for="bwContrast">Contrast: <span id="bwCVal">0</span></label><input type="range" id="bwContrast" class="form-range" min="-100" max="100" value="0"></div>
                        <div class="col-md-6"><label class="form-label" for="bwBright">Brightness: <span id="bwBVal">0</span></label><input type="range" id="bwBright" class="form-range" min="-100" max="100" value="0"></div>
                    </div>
                    <div class="text-center mt-3"><canvas id="bwCanvas" class="img-fluid border rounded d-none"></canvas></div>
                    <button type="button" id="bwGo" class="btn btn-primary btn-lg w-100 mt-3" disabled>Download Black and White Photo</button>

            <div id="bwMsg" class="alert alert-info d-none mt-3" role="alert"></div>
        </div>
    </div>
    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                    <li>Upload a color photo.</li>
                    <li>Slide contrast and brightness until the monochrome look feels right.</li>
                    <li>Download the black and white version as a PNG.</li>
            </ol>
            <p class="small text-muted mb-0">Grayscale uses standard luminance weights (0.299 red, 0.587 green, 0.114 blue), which matches how the eye sees brightness.</p>
        </div>
    </div>
</div>
@endsection
@section('scripts')

<script>
(function () {
    'use strict';

    var msgBox = document.getElementById("bwMsg");
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
        var con = Number(document.getElementById("bwContrast").value), bri = Number(document.getElementById("bwBright").value);
        document.getElementById("bwCVal").textContent = con; document.getElementById("bwBVal").textContent = bri;
        var cv = document.getElementById("bwCanvas"); cv.width = img.naturalWidth; cv.height = img.naturalHeight;
        var ctx = cv.getContext("2d"); ctx.drawImage(img, 0, 0);
        try {
            var id = ctx.getImageData(0, 0, cv.width, cv.height), d = id.data, factor = (259 * (con + 255)) / (255 * (259 - con));
            for (var i = 0; i < d.length; i += 4) {
                var gray = 0.299 * d[i] + 0.587 * d[i + 1] + 0.114 * d[i + 2];
                var v = Math.max(0, Math.min(255, factor * (gray - 128) + 128 + bri));
                d[i] = v; d[i + 1] = v; d[i + 2] = v;
            }
            ctx.putImageData(id, 0, 0);
        } catch (e) { showMsg("This photo is too large to process on this device.", false); return; }
        cv.classList.remove("d-none");
    }
    document.getElementById("bwFile").addEventListener("change", function () {
        var f = this.files && this.files[0]; if (!f) return;
        var im = new Image(); im.onload = function () { img = im; draw(); document.getElementById("bwGo").disabled = false; showMsg("Loaded " + f.name + "."); };
        im.onerror = function () { showMsg("That file could not be read as an image.", false); };
        im.src = URL.createObjectURL(f);
    });
    ["bwContrast", "bwBright"].forEach(function (id) { document.getElementById(id).addEventListener("input", draw); });
    document.getElementById("bwGo").addEventListener("click", function () { if (!img) return; draw(); document.getElementById("bwCanvas").toBlob(function (b) { if (b) dl(b, "black-and-white.png"); }, "image/png"); });

})();
</script>
@endsection
