@extends('layouts.app')
@section('title', 'Image Grid Splitter — Free Online Tool')
@section('meta_description', 'Split one photo into a grid of tiles for Instagram carousel posts')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3">Image Grid Splitter</h1>
            <p class="lead small text-muted">Split one photo into equal tiles, for example a 3 by 3 grid, and download all tiles in posting order as a ZIP.</p>

                    <label class="form-label fw-semibold" for="gsFile">Choose a photo</label>
                    <input type="file" id="gsFile" class="form-control" accept="image/*">
                    <div class="row g-3 mt-2">
                        <div class="col-md-4"><label class="form-label" for="gsRows">Rows</label><input type="number" id="gsRows" class="form-control" value="3" min="1" max="10"></div>
                        <div class="col-md-4"><label class="form-label" for="gsCols">Columns</label><input type="number" id="gsCols" class="form-control" value="3" min="1" max="10"></div>
                        <div class="col-md-4 d-flex align-items-end"><button type="button" id="gsGo" class="btn btn-primary w-100" disabled>Split and Download ZIP</button></div>
                    </div>
                    <div class="text-center mt-3"><img id="gsPrev" class="img-fluid rounded border d-none" style="max-height:260px" alt="Preview"></div>
                    <p class="small text-muted mt-2 mb-0">Tiles are numbered left to right, top to bottom. For an Instagram profile grid, post them in reverse order so the big picture lines up.</p>

            <div id="gsMsg" class="alert alert-info d-none mt-3" role="alert"></div>
        </div>
    </div>
    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                    <li>Upload the photo you want to split.</li>
                    <li>Set rows and columns, for example 3 and 3 for a classic profile grid.</li>
                    <li>Download the ZIP and post the numbered tiles.</li>
            </ol>
            <p class="small text-muted mb-0">Square photos split most evenly. Non-square photos still work, the tiles simply keep the same rectangle shape.</p>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/jszip@3.10.1/dist/jszip.min.js"></script>
<script>
(function () {
    'use strict';

    var msgBox = document.getElementById("gsMsg");
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

    var img = null, fname = "grid";
    document.getElementById("gsFile").addEventListener("change", function () {
        var f = this.files && this.files[0]; if (!f) return;
        fname = f.name.replace(/\.[^.]+$/, "") || "grid";
        var url = URL.createObjectURL(f), im = new Image();
        im.onload = function () { img = im; var pv = document.getElementById("gsPrev"); pv.src = url; pv.classList.remove("d-none"); document.getElementById("gsGo").disabled = false; showMsg("Loaded " + f.name + " (" + im.naturalWidth + " x " + im.naturalHeight + ")."); };
        im.onerror = function () { showMsg("That file could not be read as an image.", false); };
        im.src = url;
    });
    document.getElementById("gsGo").addEventListener("click", function () {
        if (!img) return;
        if (typeof JSZip === "undefined") { showMsg("ZIP library failed to load. Check your connection and reload.", false); return; }
        var rows = Math.min(10, Math.max(1, parseInt(document.getElementById("gsRows").value, 10) || 3));
        var cols = Math.min(10, Math.max(1, parseInt(document.getElementById("gsCols").value, 10) || 3));
        var tw = Math.floor(img.naturalWidth / cols), th = Math.floor(img.naturalHeight / rows);
        if (tw < 1 || th < 1) { showMsg("This image is too small for that grid.", false); return; }
        var zip = new JSZip(), btn = this; btn.disabled = true; var idx = 0, total = rows * cols, made = 0;
        function step() {
            if (idx >= total) { zip.generateAsync({ type: "blob" }).then(function (b) { dl(b, fname + "-grid.zip"); btn.disabled = false; showMsg("Done. " + made + " tiles zipped."); }); return; }
            var r = Math.floor(idx / cols), c = idx % cols;
            var cv = document.createElement("canvas"); cv.width = tw; cv.height = th;
            cv.getContext("2d").drawImage(img, c * tw, r * th, tw, th, 0, 0, tw, th);
            cv.toBlob(function (b) { if (b) { zip.file("tile-" + (idx + 1) + ".png", b); made++; } idx++; step(); }, "image/png");
        }
        showMsg("Splitting into " + total + " tiles..."); step();
    });

})();
</script>
@endsection
