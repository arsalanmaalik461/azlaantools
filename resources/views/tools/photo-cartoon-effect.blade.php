@extends('layouts.app')

@section('title', 'Photo Cartoon Effect - Azlaan Tools')
@section('meta_description', 'Turn your photo into cartoon style, fully free and online. No signup needed.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Photo Cartoon Effect</h1>
            <p class="lead text-muted">Upload your photo and turn it into cartoon style. Great for a fun avatar or profile picture. Everything happens in your browser — the photo is never uploaded anywhere.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="photoInput" class="form-label fw-semibold">Select photo</label>
                        <input type="file" class="form-control" id="photoInput" accept="image/*">
                        <div class="form-text">JPG, PNG or WebP. The photo is processed only in your browser.</div>
                    </div>

                    <div id="controlsArea" class="d-none">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edgeRange" class="form-label fw-semibold">Edge / Outline <span class="badge bg-secondary" id="edgeVal">80</span></label>
                                <input type="range" class="form-range" id="edgeRange" min="20" max="160" value="80">
                                <div class="form-text">Higher value = light outlines, lower value = dark outlines.</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="colorRange" class="form-label fw-semibold">Color levels <span class="badge bg-secondary" id="colorVal">10</span></label>
                                <input type="range" class="form-range" id="colorRange" min="4" max="20" value="10">
                                <div class="form-text">Fewer levels = flatter cartoon look.</div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <h5>Original</h5>
                                <img id="origImg" class="img-fluid rounded border" alt="Original photo">
                            </div>
                            <div class="col-md-6">
                                <h5>Cartoon</h5>
                                <canvas id="cartoonCanvas" class="img-fluid rounded border"></canvas>
                            </div>
                        </div>
                        <a href="#" class="btn btn-primary w-100 mt-3" id="downloadBtn">Download Cartoon Photo (PNG)</a>
                        <div class="form-text mt-2">Applying the effect can take a few seconds on a large photo.</div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select your photo using "Choose File" above.</li>
                <li>The effect applies instantly — adjust outlines and colors with the sliders.</li>
                <li>Press "Download Cartoon Photo" to save the PNG.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var photoInput = document.getElementById('photoInput');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var controlsArea = document.getElementById('controlsArea');
    var origImg = document.getElementById('origImg');
    var canvas = document.getElementById('cartoonCanvas');
    var edgeRange = document.getElementById('edgeRange');
    var colorRange = document.getElementById('colorRange');
    var edgeVal = document.getElementById('edgeVal');
    var colorVal = document.getElementById('colorVal');
    var downloadBtn = document.getElementById('downloadBtn');
    var srcCanvas = document.createElement('canvas');
    var srcCtx = srcCanvas.getContext('2d');
    var hasImage = false;
    var renderTimer = null;

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }

    function scheduleRender() {
        if (renderTimer) { clearTimeout(renderTimer); }
        renderTimer = setTimeout(renderCartoon, 250);
    }

    function renderCartoon() {
        if (!hasImage) { return; }
        var w = srcCanvas.width, h = srcCanvas.height;
        canvas.width = w;
        canvas.height = h;
        var ctx = canvas.getContext('2d');
        var srcData = srcCtx.getImageData(0, 0, w, h).data;

        // 1. simple 3x3 box blur for smooth flat regions
        var blur = new Float32Array(srcData.length);
        for (var y = 0; y < h; y++) {
            for (var x = 0; x < w; x++) {
                var r = 0, g = 0, b = 0, n = 0;
                for (var dy = -1; dy <= 1; dy++) {
                    for (var dx = -1; dx <= 1; dx++) {
                        var nx = x + dx, ny = y + dy;
                        if (nx < 0 || ny < 0 || nx >= w || ny >= h) { continue; }
                        var p = (ny * w + nx) * 4;
                        r += srcData[p]; g += srcData[p + 1]; b += srcData[p + 2]; n++;
                    }
                }
                var o = (y * w + x) * 4;
                blur[o] = r / n; blur[o + 1] = g / n; blur[o + 2] = b / n; blur[o + 3] = srcData[o + 3];
            }
        }

        // 2. color quantization (flat cartoon fills)
        var levels = parseInt(colorRange.value, 10);
        var step = 256 / levels;
        var gray = new Float32Array(w * h);
        var out = ctx.createImageData(w, h);
        for (var i = 0; i < w * h; i++) {
            var q = i * 4;
            var qr = Math.min(255, Math.round(Math.floor(blur[q] / step) * step + step / 2));
            var qg = Math.min(255, Math.round(Math.floor(blur[q + 1] / step) * step + step / 2));
            var qb = Math.min(255, Math.round(Math.floor(blur[q + 2] / step) * step + step / 2));
            out.data[q] = qr; out.data[q + 1] = qg; out.data[q + 2] = qb; out.data[q + 3] = 255;
            gray[i] = 0.299 * blur[q] + 0.587 * blur[q + 1] + 0.114 * blur[q + 2];
        }

        // 3. Sobel edge detection -> dark outlines
        var threshold = parseInt(edgeRange.value, 10);
        for (var ey = 1; ey < h - 1; ey++) {
            for (var ex = 1; ex < w - 1; ex++) {
                var gx = -gray[(ey - 1) * w + ex - 1] - 2 * gray[ey * w + ex - 1] - gray[(ey + 1) * w + ex - 1]
                         + gray[(ey - 1) * w + ex + 1] + 2 * gray[ey * w + ex + 1] + gray[(ey + 1) * w + ex + 1];
                var gy = -gray[(ey - 1) * w + ex - 1] - 2 * gray[(ey - 1) * w + ex] - gray[(ey - 1) * w + ex + 1]
                         + gray[(ey + 1) * w + ex - 1] + 2 * gray[(ey + 1) * w + ex] + gray[(ey + 1) * w + ex + 1];
                if (Math.sqrt(gx * gx + gy * gy) > threshold) {
                    var e = (ey * w + ex) * 4;
                    out.data[e] = 20; out.data[e + 1] = 20; out.data[e + 2] = 20;
                }
            }
        }
        ctx.putImageData(out, 0, 0);
    }

    photoInput.addEventListener('change', function () {
        hideError();
        var file = photoInput.files[0];
        if (!file) { return; }
        if (!file.type || file.type.indexOf('image/') !== 0) {
            showError('Please select a valid image file.');
            return;
        }
        var reader = new FileReader();
        reader.onload = function (e) {
            var img = new Image();
            img.onload = function () {
                var maxDim = 800;
                var scale = Math.min(1, maxDim / Math.max(img.width, img.height));
                var w = Math.max(1, Math.round(img.width * scale));
                var h = Math.max(1, Math.round(img.height * scale));
                srcCanvas.width = w; srcCanvas.height = h;
                srcCtx.drawImage(img, 0, 0, w, h);
                origImg.src = e.target.result;
                hasImage = true;
                controlsArea.classList.remove('d-none');
                results.classList.remove('d-none');
                renderCartoon();
            };
            img.onerror = function () {
                showError('The photo could not be loaded. Try another file.');
            };
            img.src = e.target.result;
        };
        reader.onerror = function () {
            showError('There was a problem reading the file.');
        };
        reader.readAsDataURL(file);
    });

    edgeRange.addEventListener('input', function () {
        edgeVal.textContent = edgeRange.value;
        scheduleRender();
    });
    colorRange.addEventListener('input', function () {
        colorVal.textContent = colorRange.value;
        scheduleRender();
    });

    downloadBtn.addEventListener('click', function (e) {
        e.preventDefault();
        if (!hasImage) { return; }
        canvas.toBlob(function (blob) {
            var url = URL.createObjectURL(blob);
            var a = document.createElement('a');
            a.href = url;
            a.download = 'cartoon-photo.png';
            document.body.appendChild(a);
            a.click();
            setTimeout(function () {
                document.body.removeChild(a);
                URL.revokeObjectURL(url);
            }, 500);
        }, 'image/png');
    });
})();
</script>
@endsection
