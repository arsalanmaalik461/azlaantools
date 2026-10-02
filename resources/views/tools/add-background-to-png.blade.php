@extends('layouts.app')
@section('title', 'Add Background to PNG — Free Online Tool')
@section('meta_description', 'Add a solid color background behind transparent PNG images')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3">Add Background to PNG</h1>
            <p class="lead small text-muted">Upload a transparent PNG, pick a background color, and download a flat image with no transparency.</p>

                    <label class="form-label fw-semibold" for="abFile">Choose a PNG image</label>
                    <input type="file" id="abFile" class="form-control" accept="image/png,image/*">
                    <div class="row g-3 mt-2">
                        <div class="col-md-4"><label class="form-label" for="abColor">Background color</label><input type="color" id="abColor" class="form-control form-control-color w-100" value="#ffffff"></div>
                        <div class="col-md-4"><label class="form-label" for="abFormat">Download format</label><select id="abFormat" class="form-select"><option value="image/png">PNG</option><option value="image/jpeg">JPG</option></select></div>
                        <div class="col-md-4 d-flex align-items-end"><div class="btn-group w-100"><button type="button" class="btn btn-outline-secondary" id="abWhite">White</button><button type="button" class="btn btn-outline-secondary" id="abBlack">Black</button></div></div>
                    </div>
                    <div class="text-center mt-3"><canvas id="abCanvas" class="img-fluid border rounded d-none"></canvas></div>
                    <button type="button" id="abGo" class="btn btn-primary btn-lg w-100 mt-3" disabled>Download Image With Background</button>

            <div id="abMsg" class="alert alert-info d-none mt-3" role="alert"></div>
        </div>
    </div>
    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                    <li>Upload a transparent PNG image.</li>
                    <li>Pick a background color or use the White and Black quick buttons.</li>
                    <li>Click the download button to save the flattened image.</li>
            </ol>
            <p class="small text-muted mb-0">Everything runs in your browser with the Canvas API. Your image is never uploaded to a server.</p>
        </div>
    </div>
</div>
@endsection
@section('scripts')

<script>
(function () {
    'use strict';

    var msgBox = document.getElementById("abMsg");
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

    var img = null, fileName = "image";
    document.getElementById("abFile").addEventListener("change", function () {
        var f = this.files && this.files[0]; if (!f) return;
        fileName = f.name.replace(/\.[^.]+$/, "") || "image";
        var url = URL.createObjectURL(f), im = new Image();
        im.onload = function () { img = im; draw(); document.getElementById("abGo").disabled = false; showMsg("Loaded " + f.name + " (" + im.naturalWidth + " x " + im.naturalHeight + ")."); };
        im.onerror = function () { showMsg("That file could not be read as an image.", false); };
        im.src = url;
    });
    function draw() {
        if (!img) return;
        var cv = document.getElementById("abCanvas");
        cv.width = img.naturalWidth; cv.height = img.naturalHeight;
        var ctx = cv.getContext("2d");
        ctx.fillStyle = document.getElementById("abColor").value;
        ctx.fillRect(0, 0, cv.width, cv.height);
        ctx.drawImage(img, 0, 0);
        cv.classList.remove("d-none");
    }
    document.getElementById("abColor").addEventListener("input", draw);
    document.getElementById("abWhite").addEventListener("click", function () { document.getElementById("abColor").value = "#ffffff"; draw(); });
    document.getElementById("abBlack").addEventListener("click", function () { document.getElementById("abColor").value = "#000000"; draw(); });
    document.getElementById("abGo").addEventListener("click", function () {
        if (!img) { showMsg("Choose an image first.", false); return; }
        draw();
        var fmt = document.getElementById("abFormat").value;
        document.getElementById("abCanvas").toBlob(function (b) { if (b) { dl(b, fileName + "-background." + (fmt === "image/png" ? "png" : "jpg")); showMsg("Downloaded " + fmtBytes(b.size) + "."); } }, fmt, 0.92);
    });

})();
</script>
@endsection
