@extends('layouts.app')

@section('title', 'Document Scan Enhancer - Azlaan Tools')
@section('meta_description', 'Clean up scanned documents: contrast fix, paper whitening, rotate — free online, no upload.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Document Scan Enhancer</h1>
            <p class="lead text-muted">Clean up scanned or photographed documents — contrast fix, paper whitening, grayscale. Works 100% in your browser, your file is never uploaded.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="scanFile" class="form-label fw-semibold">Choose a scanned image (JPG/PNG)</label>
                        <input type="file" class="form-control" id="scanFile" accept="image/*">
                    </div>

                    <div id="controlsBox" class="d-none">
                        <div class="row g-3">
                            <div class="col-6 col-md-3">
                                <label for="brightRange" class="form-label">Brightness <span id="brightVal" class="text-muted">0</span></label>
                                <input type="range" class="form-range" id="brightRange" min="-60" max="60" value="0">
                            </div>
                            <div class="col-6 col-md-3">
                                <label for="contrastRange" class="form-label">Contrast <span id="contrastVal" class="text-muted">30</span></label>
                                <input type="range" class="form-range" id="contrastRange" min="0" max="100" value="30">
                            </div>
                            <div class="col-6 col-md-3">
                                <label for="whiteRange" class="form-label">Paper whitening <span id="whiteVal" class="text-muted">40</span></label>
                                <input type="range" class="form-range" id="whiteRange" min="0" max="100" value="40">
                            </div>
                            <div class="col-6 col-md-3">
                                <label for="sharpRange" class="form-label">Sharpness <span id="sharpVal" class="text-muted">20</span></label>
                                <input type="range" class="form-range" id="sharpRange" min="0" max="100" value="20">
                            </div>
                        </div>
                        <div class="d-flex flex-wrap gap-2 mt-3">
                            <button type="button" class="btn btn-primary" id="autoBtn">Auto Enhance</button>
                            <button type="button" class="btn btn-outline-secondary" id="grayBtn">B&amp;W / Color</button>
                            <button type="button" class="btn btn-outline-secondary" id="rotLBtn">Rotate Left</button>
                            <button type="button" class="btn btn-outline-secondary" id="rotRBtn">Rotate Right</button>
                            <button type="button" class="btn btn-success" id="dlBtn">Download PNG</button>
                        </div>
                    </div>

                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <h6 class="text-muted">Original</h6>
                                <img id="origImg" class="img-fluid border rounded" alt="Original scan">
                            </div>
                            <div class="col-md-6 mb-3">
                                <h6 class="text-muted">Enhanced</h6>
                                <img id="outImg" class="img-fluid border rounded" alt="Enhanced scan">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Upload your scanned image or a photo of the document.</li>
                <li>Click <strong>Auto Enhance</strong> — contrast and paper whitening adjust automatically, or adjust the sliders yourself.</li>
                <li>Click Download PNG to save the clean document.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var fileInput = document.getElementById('scanFile');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var controlsBox = document.getElementById('controlsBox');
    var origImg = document.getElementById('origImg');
    var outImg = document.getElementById('outImg');

    var srcCanvas = document.createElement('canvas');
    var workCanvas = document.createElement('canvas');
    var imgLoaded = false;
    var grayMode = true;
    var brightRange = document.getElementById('brightRange');
    var contrastRange = document.getElementById('contrastRange');
    var whiteRange = document.getElementById('whiteRange');
    var sharpRange = document.getElementById('sharpRange');

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function setLabel(id, v) { document.getElementById(id).textContent = v; }

    function processImage() {
        if (!imgLoaded) return;
        var w = srcCanvas.width, h = srcCanvas.height;
        workCanvas.width = w; workCanvas.height = h;
        var sctx = srcCanvas.getContext('2d', { willReadFrequently: true });
        var wctx = workCanvas.getContext('2d', { willReadFrequently: true });
        var src = sctx.getImageData(0, 0, w, h);
        var out = wctx.createImageData(w, h);
        var d = src.data, o = out.data;
        var brightness = parseInt(brightRange.value, 10);
        var contrast = parseInt(contrastRange.value, 10);
        var white = parseInt(whiteRange.value, 10);
        var whiteThresh = 255 - (white * 1.2); // higher whitening -> lower threshold to push to white
        var cFactor = (259 * (contrast + 255)) / (255 * (259 - contrast));
        var i, r, g, b, gray;
        for (i = 0; i < d.length; i += 4) {
            r = d[i]; g = d[i + 1]; b = d[i + 2];
            gray = 0.299 * r + 0.587 * g + 0.114 * b;
            gray = cFactor * (gray - 128) + 128 + brightness;
            // paper whitening: light pixels go pure white
            var lum = (r + g + b) / 3;
            if (white > 0 && lum > whiteThresh) { gray = 255; }
            gray = Math.max(0, Math.min(255, gray));
            if (grayMode) { o[i] = o[i + 1] = o[i + 2] = gray; }
            else {
                o[i]     = Math.max(0, Math.min(255, cFactor * (r - 128) + 128 + brightness));
                o[i + 1] = Math.max(0, Math.min(255, cFactor * (g - 128) + 128 + brightness));
                o[i + 2] = Math.max(0, Math.min(255, cFactor * (b - 128) + 128 + brightness));
            }
            o[i + 3] = 255;
        }
        wctx.putImageData(out, 0, 0);
        // sharpening via unsharp mask on a copy
        var sharp = parseInt(sharpRange.value, 10);
        if (sharp > 0) {
            var blurred = document.createElement('canvas');
            blurred.width = w; blurred.height = h;
            var bctx = blurred.getContext('2d');
            bctx.filter = 'blur(2px)';
            bctx.drawImage(workCanvas, 0, 0);
            wctx.globalAlpha = sharp / 150;
            wctx.drawImage(workCanvas, 0, 0);
            wctx.globalAlpha = 1;
            wctx.drawImage(blurred, 0, 0);
        }
        outImg.src = workCanvas.toDataURL('image/png');
    }

    function autoEnhance() {
        if (!imgLoaded) return;
        var w = srcCanvas.width, h = srcCanvas.height;
        var ctx = srcCanvas.getContext('2d', { willReadFrequently: true });
        var d = ctx.getImageData(0, 0, w, h).data;
        var sum = 0, n = 0, i;
        for (i = 0; i < d.length; i += 16) { sum += (d[i] + d[i + 1] + d[i + 2]) / 3; n++; }
        var mean = sum / n;
        // map mean brightness to contrast/brightness: dark scans need more lift
        var con = Math.round(Math.max(15, Math.min(70, 60 - (mean - 100) * 0.25)));
        var bri = Math.round(Math.max(-20, Math.min(40, (140 - mean) * 0.4)));
        var whi = Math.round(Math.max(20, Math.min(80, (180 - mean) * 0.5)));
        contrastRange.value = con; brightRange.value = bri; whiteRange.value = whi;
        setLabel('contrastVal', con); setLabel('brightVal', bri); setLabel('whiteVal', whi);
        grayMode = true;
        document.getElementById('grayBtn').textContent = 'Color';
        processImage();
    }

    function rotate(deg) {
        if (!imgLoaded) return;
        var tmp = document.createElement('canvas');
        tmp.width = srcCanvas.height; tmp.height = srcCanvas.width;
        var tctx = tmp.getContext('2d');
        tctx.translate(tmp.width / 2, tmp.height / 2);
        tctx.rotate(deg * Math.PI / 180);
        tctx.drawImage(srcCanvas, -srcCanvas.width / 2, -srcCanvas.height / 2);
        srcCanvas.width = tmp.width; srcCanvas.height = tmp.height;
        srcCanvas.getContext('2d').drawImage(tmp, 0, 0);
        processImage();
    }

    fileInput.addEventListener('change', function () {
        hideError();
        var f = fileInput.files[0];
        if (!f) return;
        if (!f.type.match(/^image\//)) { showError('Please choose an image file (JPG/PNG).'); return; }
        var reader = new FileReader();
        reader.onload = function (e) {
            var img = new Image();
            img.onload = function () {
                var maxDim = 2200;
                var scale = Math.min(1, maxDim / Math.max(img.width, img.height));
                srcCanvas.width = Math.round(img.width * scale);
                srcCanvas.height = Math.round(img.height * scale);
                srcCanvas.getContext('2d').drawImage(img, 0, 0, srcCanvas.width, srcCanvas.height);
                imgLoaded = true;
                origImg.src = e.target.result;
                controlsBox.classList.remove('d-none');
                results.classList.remove('d-none');
                autoEnhance();
            };
            img.onerror = function () { showError('The image could not load. Please try another file.'); };
            img.src = e.target.result;
        };
        reader.readAsDataURL(f);
    });

    [brightRange, contrastRange, whiteRange, sharpRange].forEach(function (r) {
        r.addEventListener('input', function () {
            setLabel('brightVal', brightRange.value);
            setLabel('contrastVal', contrastRange.value);
            setLabel('whiteVal', whiteRange.value);
            setLabel('sharpVal', sharpRange.value);
            processImage();
        });
    });

    document.getElementById('autoBtn').addEventListener('click', function () { hideError(); autoEnhance(); });
    document.getElementById('grayBtn').addEventListener('click', function () {
        hideError();
        grayMode = !grayMode;
        this.textContent = grayMode ? 'Color' : 'B&W';
        processImage();
    });
    document.getElementById('rotLBtn').addEventListener('click', function () { hideError(); rotate(-90); });
    document.getElementById('rotRBtn').addEventListener('click', function () { hideError(); rotate(90); });
    document.getElementById('dlBtn').addEventListener('click', function () {
        hideError();
        if (!imgLoaded) { showError('Please upload an image first.'); return; }
        var a = document.createElement('a');
        a.download = 'enhanced-document.png';
        a.href = workCanvas.toDataURL('image/png');
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    });
})();
</script>
@endsection
