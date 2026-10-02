@extends('layouts.app')
@section('title', 'Add Border to Photo — Free Online Tool')
@section('meta_description', 'Add a colored border or frame around any photo in seconds')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3">Add Border to Photo</h1>
            <p class="lead small text-muted">Upload a photo, choose a border width and color, and download the framed photo ready for prints or social posts.</p>

                    <label class="form-label fw-semibold" for="bdFile">Choose a photo</label>
                    <input type="file" id="bdFile" class="form-control" accept="image/*">
                    <div class="row g-3 mt-2">
                        <div class="col-md-4"><label class="form-label" for="bdSize">Border width: <span id="bdVal">24</span> px</label><input type="range" id="bdSize" class="form-range" min="1" max="300" value="24"></div>
                        <div class="col-md-4"><label class="form-label" for="bdColor">Border color</label><input type="color" id="bdColor" class="form-control form-control-color w-100" value="#ffffff"></div>
                        <div class="col-md-4"><label class="form-label" for="bdRad">Photo corner radius (px)</label><input type="number" id="bdRad" class="form-control" value="0" min="0" max="200"></div>
                    </div>
                    <div class="text-center mt-3"><canvas id="bdCanvas" class="img-fluid border rounded d-none"></canvas></div>
                    <button type="button" id="bdGo" class="btn btn-primary btn-lg w-100 mt-3" disabled>Download Framed Photo</button>

            <div id="bdMsg" class="alert alert-info d-none mt-3" role="alert"></div>
        </div>
    </div>
    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                    <li>Upload a photo.</li>
                    <li>Set the border width and color, with optional rounded inner corners.</li>
                    <li>Download the framed photo as a PNG.</li>
            </ol>
            <p class="small text-muted mb-0">Border is added around the photo, so the final image is larger than the original by twice the border width.</p>
        </div>
    </div>
</div>
@endsection
@section('scripts')

<script>
(function () {
    'use strict';

    var msgBox = document.getElementById("bdMsg");
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
        var b = Number(document.getElementById("bdSize").value); document.getElementById("bdVal").textContent = b;
        var rad = Math.max(0, Number(document.getElementById("bdRad").value) || 0);
        var cv = document.getElementById("bdCanvas"); cv.width = img.naturalWidth + b * 2; cv.height = img.naturalHeight + b * 2;
        var ctx = cv.getContext("2d");
        ctx.fillStyle = document.getElementById("bdColor").value; ctx.fillRect(0, 0, cv.width, cv.height);
        if (rad > 0) { ctx.save(); ctx.beginPath(); ctx.moveTo(b + rad, b); ctx.arcTo(b + img.naturalWidth, b, b + img.naturalWidth, b + img.naturalHeight, rad); ctx.arcTo(b + img.naturalWidth, b + img.naturalHeight, b, b + img.naturalHeight, rad); ctx.arcTo(b, b + img.naturalHeight, b, b, rad); ctx.arcTo(b, b, b + img.naturalWidth, b, rad); ctx.closePath(); ctx.clip(); }
        ctx.drawImage(img, b, b);
        if (rad > 0) ctx.restore();
        cv.classList.remove("d-none");
    }
    document.getElementById("bdFile").addEventListener("change", function () {
        var f = this.files && this.files[0]; if (!f) return;
        var im = new Image(); im.onload = function () { img = im; draw(); document.getElementById("bdGo").disabled = false; showMsg("Loaded " + f.name + "."); };
        im.onerror = function () { showMsg("That file could not be read as an image.", false); };
        im.src = URL.createObjectURL(f);
    });
    ["bdSize", "bdColor", "bdRad"].forEach(function (id) { document.getElementById(id).addEventListener("input", draw); });
    document.getElementById("bdGo").addEventListener("click", function () { if (!img) return; draw(); document.getElementById("bdCanvas").toBlob(function (bb) { if (bb) dl(bb, "framed-photo.png"); }, "image/png"); });

})();
</script>
@endsection
