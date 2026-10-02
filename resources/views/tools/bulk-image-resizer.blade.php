@extends('layouts.app')
@section('title', 'Bulk Image Resizer — Free Online Tool')
@section('meta_description', 'Resize many images at once to the same size and download as ZIP')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3">Bulk Image Resizer</h1>
            <p class="lead small text-muted">Select many images, set one target size, and download every resized image together in a single ZIP file.</p>

                    <label class="form-label fw-semibold" for="brFile">Choose images (multiple)</label>
                    <input type="file" id="brFile" class="form-control" accept="image/*" multiple>
                    <div class="row g-3 mt-2">
                        <div class="col-md-3"><label class="form-label" for="brW">Width (px)</label><input type="number" id="brW" class="form-control" value="800" min="1"></div>
                        <div class="col-md-3"><label class="form-label" for="brH">Height (px)</label><input type="number" id="brH" class="form-control" value="600" min="1"></div>
                        <div class="col-md-3"><label class="form-label" for="brMode">Mode</label><select id="brMode" class="form-select"><option value="fit">Fit inside (keep ratio)</option><option value="exact">Exact size (stretch)</option><option value="width">Width only (keep ratio)</option></select></div>
                        <div class="col-md-3"><label class="form-label" for="brFormat">Format</label><select id="brFormat" class="form-select"><option value="image/jpeg">JPG</option><option value="image/png">PNG</option><option value="image/webp">WebP</option></select></div>
                    </div>
                    <button type="button" id="brGo" class="btn btn-primary btn-lg w-100 mt-3">Resize All and Download ZIP</button>
                    <ul id="brList" class="list-group mt-3"></ul>

            <div id="brMsg" class="alert alert-info d-none mt-3" role="alert"></div>
        </div>
    </div>
    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                    <li>Select two or more images.</li>
                    <li>Set the target width and height and choose a resize mode.</li>
                    <li>Click the button and save the ZIP with all resized images.</li>
            </ol>
            <p class="small text-muted mb-0">Uses JSZip in your browser. Very large batches may use a lot of memory on phones, so split huge batches into groups.</p>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/jszip@3.10.1/dist/jszip.min.js"></script>
<script>
(function () {
    'use strict';

    var msgBox = document.getElementById("brMsg");
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

    document.getElementById("brGo").addEventListener("click", function () {
        var files = document.getElementById("brFile").files;
        if (!files || files.length === 0) { showMsg("Choose at least one image.", false); return; }
        if (typeof JSZip === "undefined") { showMsg("ZIP library failed to load. Check your connection and reload.", false); return; }
        var W = parseInt(document.getElementById("brW").value, 10), H = parseInt(document.getElementById("brH").value, 10);
        var mode = document.getElementById("brMode").value, fmt = document.getElementById("brFormat").value;
        if (!W || W < 1) { showMsg("Enter a valid width.", false); return; }
        if (mode !== "width" && (!H || H < 1)) { showMsg("Enter a valid height.", false); return; }
        var ext = fmt === "image/png" ? "png" : (fmt === "image/webp" ? "webp" : "jpg");
        var zip = new JSZip(), list = document.getElementById("brList"); list.innerHTML = "";
        var btn = this; btn.disabled = true;
        var done = 0, total = files.length;
        function next(i) {
            if (i >= total) {
                zip.generateAsync({ type: "blob" }).then(function (blob) { dl(blob, "resized-images.zip"); btn.disabled = false; showMsg("Done. " + done + " of " + total + " images resized and zipped."); });
                return;
            }
            var f = files[i], url = URL.createObjectURL(f), im = new Image();
            im.onload = function () {
                var w = W, h = H;
                if (mode === "width") { h = Math.round(im.naturalHeight * W / im.naturalWidth); }
                else if (mode === "fit") { var r = Math.min(W / im.naturalWidth, H / im.naturalHeight); w = Math.max(1, Math.round(im.naturalWidth * r)); h = Math.max(1, Math.round(im.naturalHeight * r)); }
                var cv = document.createElement("canvas"); cv.width = w; cv.height = h;
                var ctx = cv.getContext("2d"); if (fmt === "image/jpeg") { ctx.fillStyle = "#ffffff"; ctx.fillRect(0, 0, w, h); }
                ctx.drawImage(im, 0, 0, w, h);
                cv.toBlob(function (b) {
                    if (b) { zip.file(f.name.replace(/\.[^.]+$/, "") + "-" + w + "x" + h + "." + ext, b); done++; }
                    var li = document.createElement("li"); li.className = "list-group-item small"; li.textContent = f.name + " -> " + w + " x " + h; list.appendChild(li);
                    URL.revokeObjectURL(url); next(i + 1);
                }, fmt, 0.9);
            };
            im.onerror = function () { URL.revokeObjectURL(url); next(i + 1); };
            im.src = url;
        }
        showMsg("Resizing " + total + " images...");
        next(0);
    });

})();
</script>
@endsection
