@extends('layouts.app')
@section('title', 'SVG to PNG Converter — Free Online Tool')
@section('meta_description', 'Convert SVG vector files to high quality PNG images at any size')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3">SVG to PNG Converter</h1>
            <p class="lead small text-muted">Upload an SVG vector and export a crisp PNG at exactly the size you need, with an optional background color.</p>

                    <label class="form-label fw-semibold" for="spFile">Choose an SVG file</label>
                    <input type="file" id="spFile" class="form-control" accept="image/svg+xml,.svg">
                    <div class="row g-3 mt-2">
                        <div class="col-md-3"><label class="form-label" for="spW">Output width (px)</label><input type="number" id="spW" class="form-control" value="1024" min="1" max="8000"></div>
                        <div class="col-md-3"><label class="form-label" for="spH">Output height (px)</label><input type="number" id="spH" class="form-control" value="1024" min="1" max="8000"></div>
                        <div class="col-md-3"><label class="form-label" for="spScale">Quick scale</label><select id="spScale" class="form-select"><option value="1">1x</option><option value="2">2x</option><option value="3">3x</option><option value="4">4x</option></select></div>
                        <div class="col-md-3"><label class="form-label" for="spBg">Background</label><select id="spBg" class="form-select"><option value="">Transparent</option><option value="#ffffff">White</option><option value="#000000">Black</option></select></div>
                    </div>
                    <div class="form-check mt-2"><input type="checkbox" id="spKeep" class="form-check-input" checked><label class="form-check-label" for="spKeep">Keep aspect ratio (height follows width)</label></div>
                    <div class="text-center mt-3"><img id="spPrev" class="img-fluid rounded border d-none" style="max-height:240px" alt="SVG preview"></div>
                    <button type="button" id="spGo" class="btn btn-primary btn-lg w-100 mt-3" disabled>Convert and Download PNG</button>

            <div id="spMsg" class="alert alert-info d-none mt-3" role="alert"></div>
        </div>
    </div>
    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                    <li>Upload an SVG file and check the preview and its natural size.</li>
                    <li>Set the output width and scale, keeping aspect ratio on for logos and icons.</li>
                    <li>Click convert to download a high quality PNG.</li>
            </ol>
            <p class="small text-muted mb-0">Vector graphics scale without quality loss, so exporting at 3x or 4x gives print-ready PNG files from a tiny SVG.</p>
        </div>
    </div>
</div>
@endsection
@section('scripts')

<script>
(function () {
    'use strict';

    var msgBox = document.getElementById("spMsg");
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

    var svgUrl = null, natW = 0, natH = 0;
    document.getElementById("spFile").addEventListener("change", function () {
        var f = this.files && this.files[0]; if (!f) return;
        if (svgUrl) URL.revokeObjectURL(svgUrl);
        svgUrl = URL.createObjectURL(f);
        var prev = document.getElementById("spPrev"); prev.src = svgUrl; prev.classList.remove("d-none");
        var im = new Image();
        im.onload = function () { natW = im.naturalWidth || 300; natH = im.naturalHeight || 300; document.getElementById("spW").value = natW; document.getElementById("spH").value = natH; document.getElementById("spGo").disabled = false; showMsg("Loaded " + f.name + " (natural size " + natW + " x " + natH + ")."); };
        im.onerror = function () { showMsg("That SVG could not be rendered. Check that it is a valid SVG file.", false); };
        im.src = svgUrl;
    });
    document.getElementById("spGo").addEventListener("click", function () {
        if (!svgUrl) { showMsg("Choose an SVG file first.", false); return; }
        var scale = Number(document.getElementById("spScale").value) || 1;
        var w = Math.max(1, (parseInt(document.getElementById("spW").value, 10) || natW)) * scale;
        var h = document.getElementById("spKeep").checked ? Math.round(w * (natH / Math.max(1, natW))) : Math.max(1, (parseInt(document.getElementById("spH").value, 10) || natH)) * scale;
        var im = new Image();
        im.onload = function () {
            var cv = document.createElement("canvas"); cv.width = Math.round(w); cv.height = Math.round(h);
            var ctx = cv.getContext("2d");
            var bg = document.getElementById("spBg").value; if (bg) { ctx.fillStyle = bg; ctx.fillRect(0, 0, cv.width, cv.height); }
            ctx.drawImage(im, 0, 0, cv.width, cv.height);
            cv.toBlob(function (b) { if (b) { dl(b, "svg-converted.png"); showMsg("Exported PNG at " + cv.width + " x " + cv.height + " (" + fmtBytes(b.size) + ")."); } }, "image/png");
        };
        im.onerror = function () { showMsg("Rendering failed for this SVG.", false); };
        im.src = svgUrl;
    });

})();
</script>
@endsection
