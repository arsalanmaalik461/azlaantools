@extends('layouts.app')
@section('title', 'Blur Image Tool — Free Online Tool')
@section('meta_description', 'Blur a whole image with adjustable strength for privacy and backgrounds')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3">Blur Image Tool</h1>
            <p class="lead small text-muted">Blur a whole image with adjustable strength, ideal for soft backgrounds or hiding everything in a photo at once.</p>

                    <label class="form-label fw-semibold" for="blFile">Choose an image</label>
                    <input type="file" id="blFile" class="form-control" accept="image/*">
                    <label class="form-label mt-3" for="blAmt">Blur strength: <span id="blVal">8</span> px</label>
                    <input type="range" id="blAmt" class="form-range" min="0" max="60" value="8">
                    <div class="text-center mt-3"><canvas id="blCanvas" class="img-fluid border rounded d-none"></canvas></div>
                    <button type="button" id="blGo" class="btn btn-primary btn-lg w-100 mt-3" disabled>Download Blurred Image</button>

            <div id="blMsg" class="alert alert-info d-none mt-3" role="alert"></div>
        </div>
    </div>
    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                    <li>Upload an image.</li>
                    <li>Drag the blur strength slider and watch the preview update.</li>
                    <li>Download the blurred image as a PNG.</li>
            </ol>
            <p class="small text-muted mb-0">For hiding one small area such as a face or number plate, a censor or pixelate tool is sharper. This tool blurs the full frame evenly.</p>
        </div>
    </div>
</div>
@endsection
@section('scripts')

<script>
(function () {
    'use strict';

    var msgBox = document.getElementById("blMsg");
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
        var amt = Number(document.getElementById("blAmt").value); document.getElementById("blVal").textContent = amt;
        var cv = document.getElementById("blCanvas"); cv.width = img.naturalWidth; cv.height = img.naturalHeight;
        var ctx = cv.getContext("2d");
        ctx.filter = "blur(" + amt + "px)";
        var grow = amt * 2;
        ctx.drawImage(img, -grow, -grow, cv.width + grow * 2, cv.height + grow * 2);
        ctx.filter = "none";
        cv.classList.remove("d-none");
    }
    document.getElementById("blFile").addEventListener("change", function () {
        var f = this.files && this.files[0]; if (!f) return;
        var im = new Image(); im.onload = function () { img = im; draw(); document.getElementById("blGo").disabled = false; showMsg("Loaded " + f.name + "."); };
        im.onerror = function () { showMsg("That file could not be read as an image.", false); };
        im.src = URL.createObjectURL(f);
    });
    document.getElementById("blAmt").addEventListener("input", draw);
    document.getElementById("blGo").addEventListener("click", function () { if (!img) return; draw(); document.getElementById("blCanvas").toBlob(function (b) { if (b) dl(b, "blurred.png"); }, "image/png"); });

})();
</script>
@endsection
