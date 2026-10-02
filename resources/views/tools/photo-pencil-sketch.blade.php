@extends('layouts.app')
@section('title', 'Photo Pencil Sketch Converter Online Free — Azlaan Tools')
@section('meta_description', 'Turn any photo into a pencil sketch drawing online for free. Upload your image, adjust the sketch effect, and download the artwork. No signup, no upload.')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Photo Pencil Sketch</h1>
            <p class="lead text-muted">Turn your photo into a hand-drawn pencil sketch. Upload your photo, adjust the effect, download the sketch.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="fileInput" class="form-label fw-semibold">Photo</label>
                        <input type="file" class="form-control" id="fileInput" accept="image/*">
                        <div class="form-text">Your image stays in your browser — nothing is uploaded.</div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="softness" class="form-label fw-semibold">Line softness: <span id="softnessVal">4</span></label>
                            <input type="range" class="form-range" id="softness" min="1" max="9" value="4" step="1">
                            <div class="form-text">Low softness = sharp outlines, high softness = soft lines.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="contrast" class="form-label fw-semibold">Contrast: <span id="contrastVal">100</span>%</label>
                            <input type="range" class="form-range" id="contrast" min="40" max="200" value="100" step="5">
                            <div class="form-text">Make the sketch darker or lighter.</div>
                        </div>
                    </div>

                    <button type="button" class="btn btn-primary w-100" id="goBtn">Convert to Sketch</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <canvas id="sketchCanvas" class="img-fluid rounded border w-100"></canvas>
                        <div class="d-flex flex-wrap gap-2 mt-3">
                            <button type="button" class="btn btn-success" id="downloadBtn">Download Sketch (PNG)</button>
                            <button type="button" class="btn btn-outline-secondary" id="clearBtn">Clear</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="alert alert-info">
                <strong>Privacy note:</strong> Your photo never leaves your browser — everything happens on your device.
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select a photo from your device.</li>
                <li>Adjust <strong>line softness</strong> and <strong>contrast</strong> to your liking.</li>
                <li>Click <strong>Convert to Sketch</strong> — your pencil-sketch artwork appears below.</li>
                <li>Click <strong>Download Sketch (PNG)</strong> to save it.</li>
            </ol>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
(function () {
    'use strict';
    var fileInput = document.getElementById('fileInput');
    var softness = document.getElementById('softness');
    var contrast = document.getElementById('contrast');
    var softnessVal = document.getElementById('softnessVal');
    var contrastVal = document.getElementById('contrastVal');
    var goBtn = document.getElementById('goBtn');
    var downloadBtn = document.getElementById('downloadBtn');
    var clearBtn = document.getElementById('clearBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var canvas = document.getElementById('sketchCanvas');
    var ctx = canvas.getContext('2d');

    var srcCanvas = document.createElement('canvas');
    var srcCtx = srcCanvas.getContext('2d');
    var imgLoaded = false;
    var baseName = 'sketch';

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }

    softness.addEventListener('input', function () {
        softnessVal.textContent = softness.value;
        if (imgLoaded) { renderSketch(); }
    });
    contrast.addEventListener('input', function () {
        contrastVal.textContent = contrast.value;
        if (imgLoaded) { renderSketch(); }
    });

    fileInput.addEventListener('change', function () {
        hideError();
        var file = fileInput.files[0];
        if (!file) { return; }
        if (file.type.indexOf('image/') !== 0) {
            showError('Please select an image file.');
            return;
        }
        baseName = file.name.replace(/\.[^.]+$/, '') || 'sketch';
        var url = URL.createObjectURL(file);
        var img = new Image();
        img.onload = function () {
            var scale = Math.min(1, 1600 / Math.max(img.naturalWidth, img.naturalHeight));
            var w = Math.max(1, Math.round(img.naturalWidth * scale));
            var h = Math.max(1, Math.round(img.naturalHeight * scale));
            srcCanvas.width = w;
            srcCanvas.height = h;
            srcCtx.drawImage(img, 0, 0, w, h);
            URL.revokeObjectURL(url);
            imgLoaded = true;
            renderSketch();
            results.classList.remove('d-none');
        };
        img.onerror = function () {
            showError('Could not read this image. Please try another file.');
        };
        img.src = url;
    });

    function renderSketch() {
        var w = srcCanvas.width, h = srcCanvas.height;
        var srcData = srcCtx.getImageData(0, 0, w, h);
        var src = srcData.data;

        // Step 1: grayscale -> invert, drawn on a temp canvas for blurring.
        var invCanvas = document.createElement('canvas');
        invCanvas.width = w;
        invCanvas.height = h;
        var invCtx = invCanvas.getContext('2d');
        var invData = invCtx.createImageData(w, h);
        var inv = invData.data;
        for (var i = 0; i < src.length; i += 4) {
            var g = Math.round(0.299 * src[i] + 0.587 * src[i + 1] + 0.114 * src[i + 2]);
            inv[i] = inv[i + 1] = inv[i + 2] = 255 - g;
            inv[i + 3] = 255;
        }
        invCtx.putImageData(invData, 0, 0);

        // Step 2: blur the inverted copy (soft pencil shading).
        var blurCanvas = document.createElement('canvas');
        blurCanvas.width = w;
        blurCanvas.height = h;
        var blurCtx = blurCanvas.getContext('2d');
        var radius = parseInt(softness.value, 10);
        try {
            blurCtx.filter = 'blur(' + radius + 'px)';
        } catch (e) { /* older browsers: proceed unblurred */ }
        blurCtx.drawImage(invCanvas, 0, 0);
        var blurData = blurCtx.getImageData(0, 0, w, h).data;

        // Step 3: color-dodge the grayscale with the blurred inverted copy.
        canvas.width = w;
        canvas.height = h;
        var out = ctx.createImageData(w, h);
        var px = out.data;
        var cAmt = parseInt(contrast.value, 10) / 100;
        // Linear contrast around 128:
        var k = cAmt;
        for (var j = 0; j < src.length; j += 4) {
            var gray = Math.round(0.299 * src[j] + 0.587 * src[j + 1] + 0.114 * src[j + 2]);
            var b = blurData[j];
            var dodge;
            if (b >= 255) { dodge = 255; }
            else { dodge = Math.min(255, (gray * 255) / (255 - b)); }
            var v = Math.round(128 + k * (dodge - 128));
            if (v < 0) { v = 0; }
            if (v > 255) { v = 255; }
            px[j] = px[j + 1] = px[j + 2] = v;
            px[j + 3] = 255;
        }
        ctx.putImageData(out, 0, 0);
    }

    goBtn.addEventListener('click', function () {
        hideError();
        if (!imgLoaded) { showError('Please select a photo first.'); return; }
        renderSketch();
        results.classList.remove('d-none');
    });

    downloadBtn.addEventListener('click', function () {
        canvas.toBlob(function (blob) {
            if (!blob) { showError('Download failed — please try again.'); return; }
            var url = URL.createObjectURL(blob);
            var a = document.createElement('a');
            a.href = url;
            a.download = baseName + '-pencil-sketch.png';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            setTimeout(function () { URL.revokeObjectURL(url); }, 5000);
        }, 'image/png');
    });

    clearBtn.addEventListener('click', function () {
        fileInput.value = '';
        imgLoaded = false;
        results.classList.add('d-none');
        hideError();
    });
})();
</script>
@endsection
