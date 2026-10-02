@extends('layouts.app')
@section('title', 'Round Image Corners — Free Online Tool')
@section('meta_description', 'Round the corners of any image with a transparent background')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3">Round Image Corners</h1>
            <p class="lead small text-muted">Upload an image, choose how round the corners should be, and download a PNG with transparent rounded corners.</p>

                    <label class="form-label fw-semibold" for="rcFile">Choose an image</label>
                    <input type="file" id="rcFile" class="form-control" accept="image/*">
                    <label class="form-label mt-3" for="rcRad">Corner radius: <span id="rcVal">40</span> px</label>
                    <input type="range" id="rcRad" class="form-range" min="0" max="400" value="40">
                    <div class="form-check"><input type="checkbox" id="rcCircle" class="form-check-input"><label class="form-check-label" for="rcCircle">Maximum rounding (pill or circle look)</label></div>
                    <div class="text-center mt-3"><canvas id="rcCanvas" class="img-fluid border rounded d-none"></canvas></div>
                    <button type="button" id="rcGo" class="btn btn-primary btn-lg w-100 mt-3" disabled>Download Rounded PNG</button>

            <div id="rcMsg" class="alert alert-info d-none mt-3" role="alert"></div>
        </div>
    </div>
    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                    <li>Upload any image.</li>
                    <li>Drag the radius slider, or tick maximum rounding for a circle or pill shape.</li>
                    <li>Download the PNG with transparent corners.</li>
            </ol>
            <p class="small text-muted mb-0">The corners outside the rounded path stay transparent in the PNG, so the image blends onto any background.</p>
        </div>
    </div>
</div>
@endsection
@section('scripts')

<script>
(function () {
    'use strict';

    var msgBox = document.getElementById("rcMsg");
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
        var cv = document.getElementById("rcCanvas"), w = img.naturalWidth, h = img.naturalHeight;
        cv.width = w; cv.height = h;
        var rad = document.getElementById("rcCircle").checked ? Math.min(w, h) / 2 : Math.min(Number(document.getElementById("rcRad").value), Math.min(w, h) / 2);
        document.getElementById("rcVal").textContent = Math.round(rad);
        var ctx = cv.getContext("2d");
        ctx.beginPath();
        ctx.moveTo(rad, 0); ctx.lineTo(w - rad, 0); ctx.arcTo(w, 0, w, rad, rad); ctx.lineTo(w, h - rad); ctx.arcTo(w, h, w - rad, h, rad);
        ctx.lineTo(rad, h); ctx.arcTo(0, h, 0, h - rad, rad); ctx.lineTo(0, rad); ctx.arcTo(0, 0, rad, 0, rad); ctx.closePath();
        ctx.clip(); ctx.drawImage(img, 0, 0);
        cv.classList.remove("d-none");
    }
    document.getElementById("rcFile").addEventListener("change", function () {
        var f = this.files && this.files[0]; if (!f) return;
        var im = new Image(); im.onload = function () { img = im; draw(); document.getElementById("rcGo").disabled = false; showMsg("Loaded " + f.name + "."); };
        im.onerror = function () { showMsg("That file could not be read as an image.", false); };
        im.src = URL.createObjectURL(f);
    });
    document.getElementById("rcRad").addEventListener("input", draw);
    document.getElementById("rcCircle").addEventListener("change", draw);
    document.getElementById("rcGo").addEventListener("click", function () {
        if (!img) return; draw();
        document.getElementById("rcCanvas").toBlob(function (b) { if (b) dl(b, "rounded.png"); }, "image/png");
    });

})();
</script>
@endsection
