@extends('layouts.app')

@section('title', 'Remove Watermark From Photo - Azlaan Tools')
@section('meta_description', 'Remove watermarks, logos and date stamps from your photos with a free in-browser healing brush.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-9">
            <h1 class="mb-3">Remove Watermark From Photo</h1>
            <p class="lead text-muted">Paint over the watermark, logo or date stamp on your photo with the brush, then press Remove — the tool fills that area using nearby pixels. Free healing brush.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="imgFile" class="form-label fw-semibold">Choose a photo</label>
                        <input type="file" class="form-control" id="imgFile" accept="image/*">
                        <div class="form-text">Only use this on your own photos or photos you have permission for. The file stays in your browser.</div>
                    </div>
                    <div id="editorWrap" class="d-none">
                        <div class="mb-2">
                            <label for="brushSize" class="form-label fw-semibold">Brush size: <span id="brushVal">25</span> px</label>
                            <input type="range" class="form-range" id="brushSize" min="5" max="120" value="25">
                            <div class="form-text">Paint over the watermark with the mouse (red marks = the area to remove).</div>
                        </div>
                        <div class="border rounded overflow-hidden mb-3" style="max-height:480px; overflow:auto; background:#f8f9fa;">
                            <canvas id="photoCanvas" style="cursor:crosshair; max-width:100%; height:auto; display:block;"></canvas>
                        </div>
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="button" class="btn btn-primary flex-grow-1" id="goBtn">Remove Watermark</button>
                            <button type="button" class="btn btn-outline-secondary" id="undoMaskBtn">Clear Paint</button>
                            <button type="button" class="btn btn-outline-secondary" id="resetBtn">New Photo</button>
                        </div>
                        <div class="progress mt-3 d-none" id="progressWrap">
                            <div class="progress-bar" id="progressBar" style="width:0%">0%</div>
                        </div>
                    </div>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>
                    <div id="results" class="d-none mt-4"></div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select a photo — it will appear below.</li>
                <li>Set the brush size, then paint over the watermark, logo or date stamp.</li>
                <li>Press "Remove Watermark", then download the clean photo.</li>
            </ol>
            <p class="text-muted small">This tool fills the area using nearby pixels; it works best on small watermarks and date stamps. Results may be weaker on large marks or marks in the middle of the photo. Note: removing a watermark from someone else's copyrighted photo may be misuse.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var imgFile = document.getElementById('imgFile');
    var editorWrap = document.getElementById('editorWrap');
    var canvas = document.getElementById('photoCanvas');
    var ctx = canvas.getContext('2d');
    var brushSize = document.getElementById('brushSize');
    var brushVal = document.getElementById('brushVal');
    var goBtn = document.getElementById('goBtn');
    var undoMaskBtn = document.getElementById('undoMaskBtn');
    var resetBtn = document.getElementById('resetBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var progressWrap = document.getElementById('progressWrap');
    var progressBar = document.getElementById('progressBar');

    var origImageData = null; // backup of clean pixels
    var mask = null;          // Uint8Array: 1 = area to remove
    var painting = false;

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }

    brushSize.addEventListener('input', function () { brushVal.textContent = brushSize.value; });

    function getPos(e) {
        var rect = canvas.getBoundingClientRect();
        var scaleX = canvas.width / rect.width;
        var scaleY = canvas.height / rect.height;
        return {
            x: Math.floor((e.clientX - rect.left) * scaleX),
            y: Math.floor((e.clientY - rect.top) * scaleY)
        };
    }

    function paintAt(x, y) {
        var r = parseInt(brushSize.value, 10) / 2;
        var x0 = Math.max(0, Math.floor(x - r)), x1 = Math.min(canvas.width - 1, Math.ceil(x + r));
        var y0 = Math.max(0, Math.floor(y - r)), y1 = Math.min(canvas.height - 1, Math.ceil(y + r));
        for (var yy = y0; yy <= y1; yy++) {
            for (var xx = x0; xx <= x1; xx++) {
                var dx = xx - x, dy = yy - y;
                if (dx * dx + dy * dy <= r * r) {
                    mask[yy * canvas.width + xx] = 1;
                }
            }
        }
        redraw();
    }

    // redraw with red overlay on the mask
    function redraw() {
        ctx.putImageData(origImageData, 0, 0);
        var img = ctx.getImageData(0, 0, canvas.width, canvas.height);
        var d = img.data;
        for (var i = 0; i < mask.length; i++) {
            if (mask[i]) {
                var o = i * 4;
                d[o] = 255; d[o + 1] = Math.floor(d[o + 1] * 0.5); d[o + 2] = Math.floor(d[o + 2] * 0.5);
            }
        }
        ctx.putImageData(img, 0, 0);
    }

    canvas.addEventListener('mousedown', function (e) { if (!mask) return; painting = true; var p = getPos(e); paintAt(p.x, p.y); });
    canvas.addEventListener('mousemove', function (e) { if (!painting || !mask) return; var p = getPos(e); paintAt(p.x, p.y); });
    window.addEventListener('mouseup', function () { painting = false; });
    canvas.addEventListener('touchstart', function (e) { if (!mask) return; e.preventDefault(); painting = true; var p = getPos(e.touches[0]); paintAt(p.x, p.y); }, { passive: false });
    canvas.addEventListener('touchmove', function (e) { if (!painting || !mask) return; e.preventDefault(); var p = getPos(e.touches[0]); paintAt(p.x, p.y); }, { passive: false });
    window.addEventListener('touchend', function () { painting = false; });

    imgFile.addEventListener('change', function () {
        hideError();
        results.classList.add('d-none');
        var f = imgFile.files[0];
        if (!f) { editorWrap.classList.add('d-none'); return; }
        var reader = new FileReader();
        reader.onload = function () {
            var img = new Image();
            img.onload = function () {
                var maxW = 1400;
                var scale = img.width > maxW ? maxW / img.width : 1;
                canvas.width = Math.round(img.width * scale);
                canvas.height = Math.round(img.height * scale);
                ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
                origImageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                mask = new Uint8Array(canvas.width * canvas.height);
                editorWrap.classList.remove('d-none');
            };
            img.onerror = function () { showError('The photo could not be loaded.'); };
            img.src = reader.result;
        };
        reader.readAsDataURL(f);
    });

    undoMaskBtn.addEventListener('click', function () {
        if (!mask) return;
        mask = new Uint8Array(canvas.width * canvas.height);
        redraw();
        hideError();
    });

    resetBtn.addEventListener('click', function () {
        imgFile.value = '';
        editorWrap.classList.add('d-none');
        origImageData = null;
        mask = null;
        hideError();
        results.classList.add('d-none');
    });

    // Simple inpainting: fill inward from the edges of the mask
    function inpaint(imgData, maskArr, onProgress) {
        var w = imgData.width, h = imgData.height;
        var d = imgData.data;
        var unknown = new Uint8Array(maskArr); // 1 = not filled yet
        var dist = new Float32Array(w * h);
        var count = 0;
        for (var i = 0; i < unknown.length; i++) { if (unknown[i]) { count++; dist[i] = -1; } else { dist[i] = 0; } }
        var passes = 0, maxPasses = Math.max(w, h);
        while (count > 0 && passes < maxPasses) {
            passes++;
            for (var y = 0; y < h; y++) {
                for (var x = 0; x < w; x++) {
                    var idx = y * w + x;
                    if (!unknown[idx]) continue;
                    // take the color of a nearby filled pixel
                    var rSum = 0, gSum = 0, bSum = 0, n = 0;
                    for (var yy = -1; yy <= 1; yy++) {
                        for (var xx = -1; xx <= 1; xx++) {
                            if (xx === 0 && yy === 0) continue;
                            var nx = x + xx, ny = y + yy;
                            if (nx < 0 || ny < 0 || nx >= w || ny >= h) continue;
                            var nidx = ny * w + nx;
                            if (!unknown[nidx]) {
                                var o = nidx * 4;
                                rSum += d[o]; gSum += d[o + 1]; bSum += d[o + 2]; n++;
                            }
                        }
                    }
                    if (n > 0) {
                        var o2 = idx * 4;
                        d[o2] = Math.round(rSum / n);
                        d[o2 + 1] = Math.round(gSum / n);
                        d[o2 + 2] = Math.round(bSum / n);
                        unknown[idx] = 0;
                        count--;
                    }
                }
            }
            if (onProgress) onProgress(1 - count / (maskArr.length || 1));
        }
        return imgData;
    }

    goBtn.addEventListener('click', function () {
        hideError();
        if (!origImageData) { showError('Please select a photo first.'); return; }
        var hasMask = false;
        for (var i = 0; i < mask.length; i++) { if (mask[i]) { hasMask = true; break; } }
        if (!hasMask) { showError('Paint over the watermark with the brush first.'); return; }
        goBtn.disabled = true;
        goBtn.textContent = 'Removing...';
        progressWrap.classList.remove('d-none');
        results.classList.add('d-none');
        setTimeout(function () {
            try {
                // fresh copy so running it again also works correctly
                var work = new ImageData(new Uint8ClampedArray(origImageData.data), origImageData.width, origImageData.height);
                inpaint(work, mask, function (p) {
                    var pct = Math.round(p * 100);
                    progressBar.style.width = pct + '%';
                    progressBar.textContent = pct + '%';
                });
                var out = document.createElement('canvas');
                out.width = work.width; out.height = work.height;
                out.getContext('2d').putImageData(work, 0, 0);
                var url = out.toDataURL('image/png');
                results.innerHTML =
                    '<div class="alert alert-success"><strong>Done!</strong> The watermark area has been filled with nearby colors.</div>' +
                    '<div class="text-center mb-3"><img src="' + url + '" class="img-fluid border rounded" alt="Cleaned photo"></div>' +
                    '<a class="btn btn-success w-100" href="' + url + '" download="watermark-removed.png">Download Photo</a>';
                results.classList.remove('d-none');
                // update the canvas to the cleaned version, reset mask
                origImageData = work;
                mask = new Uint8Array(canvas.width * canvas.height);
                ctx.putImageData(origImageData, 0, 0);
            } catch (e) {
                showError('There was a problem: ' + (e && e.message ? e.message : 'unknown error'));
            }
            goBtn.disabled = false;
            goBtn.textContent = 'Remove Watermark';
        }, 60);
    });
})();
</script>
@endsection
