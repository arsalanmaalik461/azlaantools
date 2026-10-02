@extends('layouts.app')

@section('title', 'Image Color Replacer - Azlaan Tools')
@section('meta_description', 'Replace one color in a photo with another color, free online. Perfect for product photos.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Image Color Replacer</h1>
            <p class="lead text-muted">Change one color in your photo and keep everything else the same — best for product photos. Upload an image, pick the old color (or click the photo to pick it), choose a new color, and download it. Everything happens in your browser — your photo is never uploaded.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="fileInput" class="form-label fw-semibold">1. Select a photo</label>
                        <input type="file" class="form-control" id="fileInput" accept="image/*">
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <canvas id="origCanvas" class="img-fluid border rounded w-100" style="cursor:crosshair; max-height:320px; object-fit:contain; background:#f8f9fa;"></canvas>
                            <p class="form-text">Original — click the photo to pick a color.</p>
                        </div>
                        <div class="col-md-6">
                            <canvas id="resultCanvas" class="img-fluid border rounded w-100" style="max-height:320px; object-fit:contain; background:#f8f9fa;"></canvas>
                            <p class="form-text">Result preview — updates as soon as you change the settings below.</p>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="srcColor" class="form-label fw-semibold">2. Old color (the one to replace)</label>
                            <input type="color" class="form-control form-control-color w-100" id="srcColor" value="#ff0000">
                        </div>
                        <div class="col-md-4">
                            <label for="dstColor" class="form-label fw-semibold">3. New color</label>
                            <input type="color" class="form-control form-control-color w-100" id="dstColor" value="#0066ff">
                        </div>
                        <div class="col-md-4">
                            <label for="tolRange" class="form-label fw-semibold d-flex justify-content-between">
                                <span>4. Color tolerance</span>
                                <span><strong id="tolValue">30</strong></span>
                            </label>
                            <input type="range" class="form-range" id="tolRange" min="1" max="120" step="1" value="30">
                            <p class="form-text">Higher value = similar shades will change too.</p>
                        </div>
                    </div>

                    <div class="d-grid d-sm-flex gap-2 mt-3">
                        <button type="button" class="btn btn-primary flex-sm-grow-1" id="goBtn">Replace Color</button>
                        <button type="button" class="btn btn-success" id="downloadBtn" disabled>Download Result</button>
                    </div>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="alert alert-success">
                            <span id="resultInfo"></span>
                        </div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li><strong>Select a photo</strong> — JPG, PNG or WebP. Large images are automatically shrunk to 1200px so the work goes fast.</li>
                <li><strong>Choose the old color</strong> — from the color box, or by clicking that color on the original photo.</li>
                <li><strong>Choose the new color</strong> and set the tolerance. Low tolerance changes only the exact color; high tolerance changes nearby shades too.</li>
                <li>Press <strong>Replace Color</strong> — see the result in the preview. Shading and brightness stay the same; only the color changes.</li>
                <li>Save the PNG file with <strong>Download Result</strong>.</li>
            </ol>

            <h2 class="mt-4">Tips</h2>
            <p class="text-muted">In product photography this tool makes pictures of the same shirt or box in different colors without taking a new photo. For the best results use a photo with a clean background and clear colors. If the wrong area changes color, lower the tolerance.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var fileInput = document.getElementById('fileInput');
    var origCanvas = document.getElementById('origCanvas');
    var resultCanvas = document.getElementById('resultCanvas');
    var srcColor = document.getElementById('srcColor');
    var dstColor = document.getElementById('dstColor');
    var tolRange = document.getElementById('tolRange');
    var tolValue = document.getElementById('tolValue');
    var goBtn = document.getElementById('goBtn');
    var downloadBtn = document.getElementById('downloadBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var resultInfo = document.getElementById('resultInfo');

    var origImgData = null;
    var workW = 0, workH = 0;
    var changedPixels = 0;

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function hexToRgb(hex) {
        return {
            r: parseInt(hex.slice(1, 3), 16),
            g: parseInt(hex.slice(3, 5), 16),
            b: parseInt(hex.slice(5, 7), 16)
        };
    }
    function rgbToHex(r, g, b) {
        function h(n) { var s = n.toString(16); return s.length === 1 ? '0' + s : s; }
        return '#' + h(r) + h(g) + h(b);
    }
    function luminance(c) {
        return 0.299 * c.r + 0.587 * c.g + 0.114 * c.b;
    }

    tolRange.addEventListener('input', function () {
        tolValue.textContent = tolRange.value;
    });

    fileInput.addEventListener('change', function () {
        var file = fileInput.files[0];
        if (!file) return;
        if (!file.type.match(/^image\//)) { showError('Please select an image file.'); return; }
        hideError();
        var reader = new FileReader();
        reader.onload = function (e) {
            var img = new Image();
            img.onload = function () {
                var scale = Math.min(1, 1200 / Math.max(img.width, img.height));
                workW = Math.round(img.width * scale);
                workH = Math.round(img.height * scale);
                origCanvas.width = workW; origCanvas.height = workH;
                resultCanvas.width = workW; resultCanvas.height = workH;
                var octx = origCanvas.getContext('2d');
                octx.drawImage(img, 0, 0, workW, workH);
                origImgData = octx.getImageData(0, 0, workW, workH);
                var rctx = resultCanvas.getContext('2d');
                rctx.drawImage(img, 0, 0, workW, workH);
                downloadBtn.disabled = true;
                results.classList.add('d-none');
            };
            img.onerror = function () { showError('The image could not load. Please try another file.'); };
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
    });

    origCanvas.addEventListener('click', function (ev) {
        if (!origImgData) { showError('First select a photo, then pick a color.'); return; }
        var rect = origCanvas.getBoundingClientRect();
        var x = Math.floor((ev.clientX - rect.left) * (workW / rect.width));
        var y = Math.floor((ev.clientY - rect.top) * (workH / rect.height));
        x = Math.max(0, Math.min(workW - 1, x));
        y = Math.max(0, Math.min(workH - 1, y));
        var d = origImgData.data;
        var i = (y * workW + x) * 4;
        srcColor.value = rgbToHex(d[i], d[i + 1], d[i + 2]);
        hideError();
    });

    goBtn.addEventListener('click', function () {
        hideError();
        if (!origImgData) { showError('Please choose an image first.'); return; }
        var src = hexToRgb(srcColor.value);
        var dst = hexToRgb(dstColor.value);
        var tol = parseInt(tolRange.value, 10);
        var srcLum = luminance(src);
        if (srcLum < 1) srcLum = 1;

        var out = new ImageData(workW, workH);
        var s = origImgData.data, o = out.data;
        changedPixels = 0;
        for (var i = 0; i < s.length; i += 4) {
            var dr = s[i] - src.r, dg = s[i + 1] - src.g, db = s[i + 2] - src.b;
            var dist = Math.sqrt(dr * dr + dg * dg + db * db);
            if (dist <= tol && s[i + 3] > 10) {
                var ratio = luminance({ r: s[i], g: s[i + 1], b: s[i + 2] }) / srcLum;
                if (ratio > 1.6) ratio = 1.6;
                if (ratio < 0.25) ratio = 0.25;
                o[i] = Math.max(0, Math.min(255, Math.round(dst.r * ratio)));
                o[i + 1] = Math.max(0, Math.min(255, Math.round(dst.g * ratio)));
                o[i + 2] = Math.max(0, Math.min(255, Math.round(dst.b * ratio)));
                o[i + 3] = s[i + 3];
                changedPixels++;
            } else {
                o[i] = s[i]; o[i + 1] = s[i + 1]; o[i + 2] = s[i + 2]; o[i + 3] = s[i + 3];
            }
        }
        var rctx = resultCanvas.getContext('2d');
        rctx.putImageData(out, 0, 0);
        var pct = Math.round(changedPixels / (workW * workH) * 100);
        resultInfo.textContent = changedPixels.toLocaleString() + ' pixels (' + pct + '% of the image) were changed.';
        results.classList.remove('d-none');
        downloadBtn.disabled = false;
        if (changedPixels === 0) {
            showError('No pixels changed — try again with higher tolerance or by picking the right color.');
        }
    });

    downloadBtn.addEventListener('click', function () {
        if (downloadBtn.disabled) return;
        var a = document.createElement('a');
        a.href = resultCanvas.toDataURL('image/png');
        a.download = 'color-replaced.png';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    });
})();
</script>
@endsection
