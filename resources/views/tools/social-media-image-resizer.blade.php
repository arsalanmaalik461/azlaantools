@extends('layouts.app')
@section('title', 'Social Media Image Resizer — Free Online Tool')
@section('meta_description', 'Resize images to exact sizes for Instagram Facebook YouTube and X posts')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3">Social Media Image Resizer</h1>
            <p class="lead small text-muted">Resize any image to the exact pixel size each platform wants, for Instagram, Facebook, YouTube, X and more.</p>

                    <label class="form-label fw-semibold" for="smFile">Choose an image</label>
                    <input type="file" id="smFile" class="form-control" accept="image/*">
                    <div class="row g-3 mt-2">
                        <div class="col-md-5"><label class="form-label" for="smPreset">Platform size</label><select id="smPreset" class="form-select">
                            <option value="1080,1080">Instagram Post (1080 x 1080)</option>
                            <option value="1080,1350">Instagram Portrait (1080 x 1350)</option>
                            <option value="1080,1920">Instagram / TikTok Story (1080 x 1920)</option>
                            <option value="1200,630">Facebook Post (1200 x 630)</option>
                            <option value="1640,924">Facebook Cover (1640 x 924)</option>
                            <option value="1280,720">YouTube Thumbnail (1280 x 720)</option>
                            <option value="1600,900">X / Twitter Post (1600 x 900)</option>
                            <option value="1200,627">LinkedIn Post (1200 x 627)</option>
                            <option value="1000,1500">Pinterest Pin (1000 x 1500)</option>
                        </select></div>
                        <div class="col-md-4"><label class="form-label" for="smMode">Fit mode</label><select id="smMode" class="form-select"><option value="cover">Fill (crop edges)</option><option value="fit">Fit (pad with color)</option></select></div>
                        <div class="col-md-3"><label class="form-label" for="smBg">Pad color</label><input type="color" id="smBg" class="form-control form-control-color w-100" value="#000000"></div>
                    </div>
                    <div class="text-center mt-3"><canvas id="smCanvas" class="img-fluid border rounded d-none" style="max-height:320px"></canvas></div>
                    <button type="button" id="smGo" class="btn btn-primary btn-lg w-100 mt-3" disabled>Download Resized Image</button>

            <div id="smMsg" class="alert alert-info d-none mt-3" role="alert"></div>
        </div>
    </div>
    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                    <li>Upload your image.</li>
                    <li>Pick the platform size and choose fill to crop edges or fit to pad with a color.</li>
                    <li>Download the image at the exact platform size.</li>
            </ol>
            <p class="small text-muted mb-0">Platform size recommendations are the commonly published values. Platforms occasionally change them, so check the platform help pages for critical work.</p>
        </div>
    </div>
</div>
@endsection
@section('scripts')

<script>
(function () {
    'use strict';

    var msgBox = document.getElementById("smMsg");
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
        var parts = document.getElementById("smPreset").value.split(",");
        var W = Number(parts[0]), H = Number(parts[1]);
        var cv = document.getElementById("smCanvas"); cv.width = W; cv.height = H;
        var ctx = cv.getContext("2d");
        var mode = document.getElementById("smMode").value;
        if (mode === "fit") {
            ctx.fillStyle = document.getElementById("smBg").value; ctx.fillRect(0, 0, W, H);
            var r = Math.min(W / img.naturalWidth, H / img.naturalHeight);
            var w = img.naturalWidth * r, h = img.naturalHeight * r;
            ctx.drawImage(img, (W - w) / 2, (H - h) / 2, w, h);
        } else {
            var r2 = Math.max(W / img.naturalWidth, H / img.naturalHeight);
            var w2 = img.naturalWidth * r2, h2 = img.naturalHeight * r2;
            ctx.drawImage(img, (W - w2) / 2, (H - h2) / 2, w2, h2);
        }
        cv.classList.remove("d-none");
    }
    document.getElementById("smFile").addEventListener("change", function () {
        var f = this.files && this.files[0]; if (!f) return;
        var im = new Image(); im.onload = function () { img = im; draw(); document.getElementById("smGo").disabled = false; showMsg("Loaded " + f.name + "."); };
        im.onerror = function () { showMsg("That file could not be read as an image.", false); };
        im.src = URL.createObjectURL(f);
    });
    ["smPreset", "smMode", "smBg"].forEach(function (id) { document.getElementById(id).addEventListener("change", draw); document.getElementById(id).addEventListener("input", draw); });
    document.getElementById("smGo").addEventListener("click", function () { if (!img) return; draw(); document.getElementById("smCanvas").toBlob(function (b) { if (b) dl(b, "social-resized.png"); }, "image/png"); });

})();
</script>
@endsection
