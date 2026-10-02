@extends('layouts.app')
@section('title', 'Image Comparison Slider Maker — Free Online Tool')
@section('meta_description', 'Create a before and after image comparison with a draggable slider')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3">Image Comparison Slider Maker</h1>
            <p class="lead small text-muted">Load a before and an after image, drag the slider to compare them, and download a split comparison image.</p>

                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label fw-semibold" for="icBefore">Before image</label><input type="file" id="icBefore" class="form-control" accept="image/*"></div>
                        <div class="col-md-6"><label class="form-label fw-semibold" for="icAfter">After image</label><input type="file" id="icAfter" class="form-control" accept="image/*"></div>
                    </div>
                    <div id="icStage" class="position-relative mt-3 d-none" style="overflow:hidden">
                        <img id="icImgB" class="w-100 d-block" alt="Before">
                        <img id="icImgA" class="position-absolute top-0 start-0 w-100 h-100" alt="After" style="object-fit:cover">
                    </div>
                    <label class="form-label mt-3" for="icRange">Slider position: <span id="icVal">50</span>%</label>
                    <input type="range" id="icRange" class="form-range" min="0" max="100" value="50">
                    <button type="button" id="icGo" class="btn btn-primary w-100 mt-2" disabled>Download Split Comparison PNG</button>

            <div id="icMsg" class="alert alert-info d-none mt-3" role="alert"></div>
        </div>
    </div>
    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                    <li>Upload the before image and the after image.</li>
                    <li>Drag the slider to reveal more of either side.</li>
                    <li>Download a single split-view PNG at the current slider position.</li>
            </ol>
            <p class="small text-muted mb-0">Both images are drawn at the before image size for the download, so matching dimensions gives the cleanest result.</p>
        </div>
    </div>
</div>
@endsection
@section('scripts')

<script>
(function () {
    'use strict';

    var msgBox = document.getElementById("icMsg");
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

    var imB = null, imA = null;
    function loadInto(input, which) {
        var f = input.files && input.files[0]; if (!f) return;
        var url = URL.createObjectURL(f), im = new Image();
        im.onload = function () {
            if (which === "B") { imB = im; document.getElementById("icImgB").src = url; } else { imA = im; document.getElementById("icImgA").src = url; }
            if (imB && imA) { document.getElementById("icStage").classList.remove("d-none"); document.getElementById("icGo").disabled = false; applyClip(); showMsg("Both images loaded. Drag the slider to compare."); }
        };
        im.src = url;
    }
    function applyClip() {
        var v = Number(document.getElementById("icRange").value);
        document.getElementById("icVal").textContent = v;
        document.getElementById("icImgA").style.clipPath = "inset(0 0 0 " + v + "%)";
    }
    document.getElementById("icBefore").addEventListener("change", function () { loadInto(this, "B"); });
    document.getElementById("icAfter").addEventListener("change", function () { loadInto(this, "A"); });
    document.getElementById("icRange").addEventListener("input", applyClip);
    document.getElementById("icGo").addEventListener("click", function () {
        if (!imB || !imA) return;
        var w = imB.naturalWidth, h = imB.naturalHeight, cut = Math.round(w * Number(document.getElementById("icRange").value) / 100);
        var cv = document.createElement("canvas"); cv.width = w; cv.height = h; var ctx = cv.getContext("2d");
        ctx.drawImage(imB, 0, 0, w, h);
        ctx.save(); ctx.beginPath(); ctx.rect(cut, 0, w - cut, h); ctx.clip(); ctx.drawImage(imA, 0, 0, w, h); ctx.restore();
        ctx.fillStyle = "#ffffff"; ctx.fillRect(cut - 2, 0, 4, h);
        cv.toBlob(function (b) { if (b) dl(b, "comparison.png"); }, "image/png");
    });

})();
</script>
@endsection
