@extends('layouts.app')
@section('title', 'Screenshot Beautifier — Free Online Tool')
@section('meta_description', 'Make screenshots beautiful with padding gradient background and shadow')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3">Screenshot Beautifier</h1>
            <p class="lead small text-muted">Drop in a plain screenshot and turn it into a polished share image with padding, a gradient background and a soft shadow.</p>

                    <label class="form-label fw-semibold" for="sbFile">Choose a screenshot</label>
                    <input type="file" id="sbFile" class="form-control" accept="image/*">
                    <div class="row g-3 mt-2">
                        <div class="col-md-3"><label class="form-label" for="sbPad">Padding: <span id="sbPadVal">64</span> px</label><input type="range" id="sbPad" class="form-range" min="0" max="200" value="64"></div>
                        <div class="col-md-3"><label class="form-label" for="sbRad">Corner radius: <span id="sbRadVal">16</span> px</label><input type="range" id="sbRad" class="form-range" min="0" max="60" value="16"></div>
                        <div class="col-md-3"><label class="form-label" for="sbC1">Gradient start</label><input type="color" id="sbC1" class="form-control form-control-color w-100" value="#6366f1"></div>
                        <div class="col-md-3"><label class="form-label" for="sbC2">Gradient end</label><input type="color" id="sbC2" class="form-control form-control-color w-100" value="#ec4899"></div>
                    </div>
                    <div class="form-check mt-2"><input type="checkbox" id="sbShadow" class="form-check-input" checked><label class="form-check-label" for="sbShadow">Soft drop shadow</label></div>
                    <div class="text-center mt-3"><canvas id="sbCanvas" class="img-fluid border rounded d-none"></canvas></div>
                    <button type="button" id="sbGo" class="btn btn-primary btn-lg w-100 mt-3" disabled>Download Beautified PNG</button>

            <div id="sbMsg" class="alert alert-info d-none mt-3" role="alert"></div>
        </div>
    </div>
    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                    <li>Upload a screenshot.</li>
                    <li>Adjust padding, corner radius, gradient colors and shadow.</li>
                    <li>Download the polished PNG for your post or launch page.</li>
            </ol>
            <p class="small text-muted mb-0">The canvas is rendered at full screenshot resolution, so the download stays sharp even though the preview is scaled down.</p>
        </div>
    </div>
</div>
@endsection
@section('scripts')

<script>
(function () {
    'use strict';

    var msgBox = document.getElementById("sbMsg");
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
    function rr(ctx, x, y, w, h, r) { ctx.beginPath(); ctx.moveTo(x + r, y); ctx.arcTo(x + w, y, x + w, y + h, r); ctx.arcTo(x + w, y + h, x, y + h, r); ctx.arcTo(x, y + h, x, y, r); ctx.arcTo(x, y, x + w, y, r); ctx.closePath(); }
    function draw() {
        if (!img) return;
        var pad = Number(document.getElementById("sbPad").value), rad = Number(document.getElementById("sbRad").value);
        document.getElementById("sbPadVal").textContent = pad; document.getElementById("sbRadVal").textContent = rad;
        var cv = document.getElementById("sbCanvas");
        cv.width = img.naturalWidth + pad * 2; cv.height = img.naturalHeight + pad * 2;
        var ctx = cv.getContext("2d");
        var grad = ctx.createLinearGradient(0, 0, cv.width, cv.height);
        grad.addColorStop(0, document.getElementById("sbC1").value); grad.addColorStop(1, document.getElementById("sbC2").value);
        ctx.fillStyle = grad; ctx.fillRect(0, 0, cv.width, cv.height);
        ctx.save();
        if (document.getElementById("sbShadow").checked) { ctx.shadowColor = "rgba(0,0,0,0.45)"; ctx.shadowBlur = Math.max(10, pad * 0.6); ctx.shadowOffsetY = Math.max(4, pad * 0.18); }
        rr(ctx, pad, pad, img.naturalWidth, img.naturalHeight, rad); ctx.fillStyle = "#ffffff"; ctx.fill();
        ctx.restore();
        ctx.save(); rr(ctx, pad, pad, img.naturalWidth, img.naturalHeight, rad); ctx.clip(); ctx.drawImage(img, pad, pad); ctx.restore();
        cv.classList.remove("d-none");
    }
    document.getElementById("sbFile").addEventListener("change", function () {
        var f = this.files && this.files[0]; if (!f) return;
        var im = new Image(); im.onload = function () { img = im; draw(); document.getElementById("sbGo").disabled = false; showMsg("Loaded " + f.name + "."); };
        im.onerror = function () { showMsg("That file could not be read as an image.", false); };
        im.src = URL.createObjectURL(f);
    });
    ["sbPad", "sbRad", "sbC1", "sbC2", "sbShadow"].forEach(function (id) { document.getElementById(id).addEventListener("input", draw); document.getElementById(id).addEventListener("change", draw); });
    document.getElementById("sbGo").addEventListener("click", function () {
        if (!img) return; draw();
        document.getElementById("sbCanvas").toBlob(function (b) { if (b) dl(b, "beautified-screenshot.png"); }, "image/png");
    });

})();
</script>
@endsection
