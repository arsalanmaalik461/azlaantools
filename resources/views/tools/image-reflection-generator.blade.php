@extends('layouts.app')
@section('title', 'Image Reflection Generator — Free Online Tool')
@section('meta_description', 'Add a glossy mirror reflection below any image or logo')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3">Image Reflection Generator</h1>
            <p class="lead small text-muted">Upload an image or logo and add a glossy fading mirror reflection below it, exported as a transparent PNG.</p>

                    <label class="form-label fw-semibold" for="irFile">Choose an image</label>
                    <input type="file" id="irFile" class="form-control" accept="image/*">
                    <div class="row g-3 mt-2">
                        <div class="col-md-4"><label class="form-label" for="irSize">Reflection size: <span id="irSizeVal">40</span>%</label><input type="range" id="irSize" class="form-range" min="5" max="100" value="40"></div>
                        <div class="col-md-4"><label class="form-label" for="irFade">Fade strength: <span id="irFadeVal">70</span>%</label><input type="range" id="irFade" class="form-range" min="10" max="100" value="70"></div>
                        <div class="col-md-4"><label class="form-label" for="irGap">Gap (px)</label><input type="number" id="irGap" class="form-control" value="4" min="0" max="100"></div>
                    </div>
                    <div class="text-center mt-3"><canvas id="irCanvas" class="img-fluid border rounded d-none"></canvas></div>
                    <button type="button" id="irGo" class="btn btn-primary btn-lg w-100 mt-3" disabled>Download PNG With Reflection</button>

            <div id="irMsg" class="alert alert-info d-none mt-3" role="alert"></div>
        </div>
    </div>
    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                    <li>Upload an image or logo.</li>
                    <li>Adjust reflection size, fade and gap until it looks right.</li>
                    <li>Download the result as a PNG with a transparent background.</li>
            </ol>
            <p class="small text-muted mb-0">The reflection is a flipped copy of the bottom part of your image with a gradient fade, drawn entirely on canvas.</p>
        </div>
    </div>
</div>
@endsection
@section('scripts')

<script>
(function () {
    'use strict';

    var msgBox = document.getElementById("irMsg");
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
        var pct = Number(document.getElementById("irSize").value), fade = Number(document.getElementById("irFade").value), gap = Math.max(0, Number(document.getElementById("irGap").value) || 0);
        document.getElementById("irSizeVal").textContent = pct; document.getElementById("irFadeVal").textContent = fade;
        var w = img.naturalWidth, h = img.naturalHeight, rh = Math.round(h * pct / 100);
        var cv = document.getElementById("irCanvas"); cv.width = w; cv.height = h + gap + rh;
        var ctx = cv.getContext("2d");
        ctx.drawImage(img, 0, 0);
        ctx.save(); ctx.translate(0, h + gap + rh); ctx.scale(1, -1);
        ctx.drawImage(img, 0, 0, w, rh, 0, 0, w, rh); ctx.restore();
        var grad = ctx.createLinearGradient(0, h + gap, 0, h + gap + rh);
        grad.addColorStop(0, "rgba(0,0,0," + (fade / 100) + ")"); grad.addColorStop(1, "rgba(0,0,0,1)");
        ctx.globalCompositeOperation = "destination-out"; ctx.fillStyle = grad; ctx.fillRect(0, h + gap, w, rh);
        ctx.globalCompositeOperation = "source-over";
        cv.classList.remove("d-none");
    }
    document.getElementById("irFile").addEventListener("change", function () {
        var f = this.files && this.files[0]; if (!f) return;
        var im = new Image(); im.onload = function () { img = im; draw(); document.getElementById("irGo").disabled = false; showMsg("Loaded " + f.name + "."); };
        im.onerror = function () { showMsg("That file could not be read as an image.", false); };
        im.src = URL.createObjectURL(f);
    });
    ["irSize", "irFade", "irGap"].forEach(function (id) { document.getElementById(id).addEventListener("input", draw); });
    document.getElementById("irGo").addEventListener("click", function () {
        if (!img) return; draw();
        document.getElementById("irCanvas").toBlob(function (b) { if (b) { dl(b, "reflection.png"); showMsg("Downloaded " + fmtBytes(b.size) + "."); } }, "image/png");
    });

})();
</script>
@endsection
