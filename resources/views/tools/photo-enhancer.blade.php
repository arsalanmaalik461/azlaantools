@extends('layouts.app')
@section('title', 'Photo Enhancer - Free Online | Azlaan Tools')
@section('meta_description', 'Auto-enhance any photo online for free - fix light, colour and contrast in one click.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Photo Enhancer</h1>
            <p class="lead text-muted">Improve your photo in one click — auto fix for light, colour and contrast, plus manual sliders. Free, no signup.</p>

            <div class="alert alert-danger d-none" id="alertBox" role="alert"></div>
            <div class="alert alert-success d-none" id="successBox" role="alert"></div>

            <div id="dropZone" class="border border-2 border-dashed rounded-3 p-4 p-md-5 text-center mb-3" style="border-style: dashed !important; cursor: pointer; background: #f8f9fa;">
                <div class="fs-1 mb-2">✨</div>
                <p class="mb-1 fw-semibold">Drag &amp; drop an image here</p>
                <p class="text-muted mb-3">or click to select a photo (JPG / PNG / WebP)</p>
                <button type="button" class="btn btn-primary">Select Photo</button>
                <input type="file" id="fileInput" accept="image/*" class="d-none">
            </div>

            <div id="editorWrap" class="d-none">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8 mb-3 mb-md-0">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="fw-semibold">Preview</span>
                                    <div class="btn-group btn-group-sm" role="group" aria-label="view">
                                        <button type="button" class="btn btn-outline-secondary active" id="viewAfterBtn">After</button>
                                        <button type="button" class="btn btn-outline-secondary" id="viewBeforeBtn">Before</button>
                                    </div>
                                </div>
                                <canvas id="photoCanvas" class="img-fluid rounded border w-100"></canvas>
                                <p class="text-muted small mt-2 mb-0"><span id="imgInfo"></span></p>
                            </div>
                            <div class="col-md-4">
                                <button type="button" id="autoBtn" class="btn btn-primary w-100 btn-lg mb-3">✨ Auto Enhance</button>
                                <div class="mb-3">
                                    <label for="brightnessRange" class="form-label fw-semibold">Brightness <span class="badge bg-secondary" id="brightnessVal">0</span></label>
                                    <input type="range" class="form-range" id="brightnessRange" min="-100" max="100" value="0" step="1">
                                </div>
                                <div class="mb-3">
                                    <label for="contrastRange" class="form-label fw-semibold">Contrast <span class="badge bg-secondary" id="contrastVal">0</span></label>
                                    <input type="range" class="form-range" id="contrastRange" min="-100" max="100" value="0" step="1">
                                </div>
                                <div class="mb-3">
                                    <label for="saturationRange" class="form-label fw-semibold">Saturation <span class="badge bg-secondary" id="saturationVal">0</span></label>
                                    <input type="range" class="form-range" id="saturationRange" min="-100" max="100" value="0" step="1">
                                </div>
                                <div class="mb-3">
                                    <label for="warmthRange" class="form-label fw-semibold">Warmth <span class="badge bg-secondary" id="warmthVal">0</span></label>
                                    <input type="range" class="form-range" id="warmthRange" min="-100" max="100" value="0" step="1">
                                    <div class="form-text">Minus = cool (blue), plus = warm (orange).</div>
                                </div>
                                <div class="d-grid gap-2">
                                    <button type="button" id="resetBtn" class="btn btn-outline-secondary">Reset</button>
                                    <button type="button" id="downloadBtn" class="btn btn-success">⬇ Download Enhanced Photo</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="alert alert-info mt-4">
                <strong>Privacy note:</strong> Your photo is never uploaded to any server — everything happens in your browser.
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Drop your photo in the box above or click to select it.</li>
                <li>Press <strong>Auto Enhance</strong> for a one-click fix, or tune the sliders yourself.</li>
                <li>Use the <strong>Before / After</strong> buttons to compare.</li>
                <li>Click <strong>Download Enhanced Photo</strong> to save the result as a PNG.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var dropZone = document.getElementById('dropZone');
    var fileInput = document.getElementById('fileInput');
    var alertBox = document.getElementById('alertBox');
    var successBox = document.getElementById('successBox');
    var editorWrap = document.getElementById('editorWrap');
    var canvas = document.getElementById('photoCanvas');
    var ctx = canvas.getContext('2d');
    var imgInfo = document.getElementById('imgInfo');
    var autoBtn = document.getElementById('autoBtn');
    var resetBtn = document.getElementById('resetBtn');
    var downloadBtn = document.getElementById('downloadBtn');
    var viewAfterBtn = document.getElementById('viewAfterBtn');
    var viewBeforeBtn = document.getElementById('viewBeforeBtn');

    var originalCanvas = document.createElement('canvas');
    var originalCtx = originalCanvas.getContext('2d');
    var enhancedCanvas = document.createElement('canvas');
    var enhancedCtx = enhancedCanvas.getContext('2d');
    var hasImage = false;
    var showingAfter = true;
    var storedName = 'photo';

    var sliders = [
        { range: 'brightnessRange', val: 'brightnessVal' },
        { range: 'contrastRange', val: 'contrastVal' },
        { range: 'saturationRange', val: 'saturationVal' },
        { range: 'warmthRange', val: 'warmthVal' }
    ];

    function showError(msg) {
        alertBox.textContent = msg;
        alertBox.classList.remove('d-none');
        successBox.classList.add('d-none');
    }
    function showSuccess(msg) {
        successBox.textContent = msg;
        successBox.classList.remove('d-none');
        alertBox.classList.add('d-none');
    }
    function hideAlerts() {
        alertBox.classList.add('d-none');
        successBox.classList.add('d-none');
    }
    function getSlider(id) {
        return parseInt(document.getElementById(id).value, 10) || 0;
    }

    function drawPreview() {
        var src = showingAfter ? enhancedCanvas : originalCanvas;
        canvas.width = src.width;
        canvas.height = src.height;
        ctx.drawImage(src, 0, 0);
    }
    function setView(after) {
        showingAfter = after;
        viewAfterBtn.classList.toggle('active', after);
        viewBeforeBtn.classList.toggle('active', !after);
        if (hasImage) { drawPreview(); }
    }

    // Core pipeline: auto color/levels correction -> manual brightness/contrast/saturation/warmth
    function render() {
        if (!hasImage) { return; }
        var w = originalCanvas.width, h = originalCanvas.height;
        var src = originalCtx.getImageData(0, 0, w, h);
        var d = src.data;
        var i, r, g, b;

        // Step 1: gray-world white balance
        var sumR = 0, sumG = 0, sumB = 0, n = d.length / 4;
        for (i = 0; i < d.length; i += 4) {
            sumR += d[i]; sumG += d[i + 1]; sumB += d[i + 2];
        }
        var avg = (sumR + sumG + sumB) / (3 * n);
        var gR = avg / Math.max(1, sumR / n);
        var gG = avg / Math.max(1, sumG / n);
        var gB = avg / Math.max(1, sumB / n);
        // clamp gains to avoid crazy casts
        gR = Math.min(1.6, Math.max(0.6, gR));
        gG = Math.min(1.6, Math.max(0.6, gG));
        gB = Math.min(1.6, Math.max(0.6, gB));

        // Step 2: histogram stretch per channel using luma percentiles
        var luma = new Array(256).fill(0);
        for (i = 0; i < d.length; i += 4) {
            r = Math.min(255, d[i] * gR); g = Math.min(255, d[i + 1] * gG); b = Math.min(255, d[i + 2] * gB);
            var y = Math.round(0.299 * r + 0.587 * g + 0.114 * b);
            luma[y]++;
        }
        var lo = 0, hi = 255, acc = 0;
        var loCut = n * 0.01, hiCut = n * 0.99;
        for (i = 0; i < 256; i++) { acc += luma[i]; if (acc >= loCut) { lo = i; break; } }
        acc = 0;
        for (i = 0; i < 256; i++) { acc += luma[i]; if (acc >= hiCut) { hi = i; break; } }
        var range = Math.max(1, hi - lo);

        var bright = getSlider('brightnessRange') * 1.27;
        var contrast = getSlider('contrastRange');
        var satAmt = 1 + getSlider('saturationRange') / 100;
        var warmth = getSlider('warmthRange');
        var cFactor = (259 * (contrast + 255)) / (255 * (259 - contrast));

        enhancedCanvas.width = w;
        enhancedCanvas.height = h;
        var out = enhancedCtx.createImageData(w, h);
        var od = out.data;
        for (i = 0; i < d.length; i += 4) {
            r = Math.min(255, d[i] * gR);
            g = Math.min(255, d[i + 1] * gG);
            b = Math.min(255, d[i + 2] * gB);
            // levels stretch on luma, applied per channel proportionally
            var yn = (0.299 * r + 0.587 * g + 0.114 * b - lo) / range;
            yn = Math.min(1, Math.max(0, yn));
            var ratio = yn / Math.max(0.001, (0.299 * r + 0.587 * g + 0.114 * b) / 255);
            r = r * ratio; g = g * ratio; b = b * ratio;
            // saturation
            var gray = 0.299 * r + 0.587 * g + 0.114 * b;
            r = gray + (r - gray) * satAmt;
            g = gray + (g - gray) * satAmt;
            b = gray + (b - gray) * satAmt;
            // contrast + brightness
            r = cFactor * (r - 128) + 128 + bright;
            g = cFactor * (g - 128) + 128 + bright;
            b = cFactor * (b - 128) + 128 + bright;
            // warmth: shift red up/blue down (or reverse)
            r += warmth * 0.5;
            b -= warmth * 0.5;
            od[i] = Math.min(255, Math.max(0, r));
            od[i + 1] = Math.min(255, Math.max(0, g));
            od[i + 2] = Math.min(255, Math.max(0, b));
            od[i + 3] = d[i + 3];
        }
        enhancedCtx.putImageData(out, 0, 0);
        setView(true);
    }

    function loadImage(file) {
        hideAlerts();
        if (!file || !file.type || file.type.indexOf('image/') !== 0) {
            showError('Please select a valid image file (JPG, PNG, WebP).');
            return;
        }
        var reader = new FileReader();
        reader.onload = function (e) {
            var img = new Image();
            img.onload = function () {
                var maxDim = 2048;
                var scale = Math.min(1, maxDim / Math.max(img.width, img.height));
                var w = Math.round(img.width * scale);
                var h = Math.round(img.height * scale);
                originalCanvas.width = w;
                originalCanvas.height = h;
                originalCtx.drawImage(img, 0, 0, w, h);
                enhancedCanvas.width = w;
                enhancedCanvas.height = h;
                enhancedCtx.drawImage(img, 0, 0, w, h);
                storedName = file.name.replace(/\.[a-z0-9]+$/i, '') || 'photo';
                imgInfo.textContent = file.name + ' — ' + w + ' × ' + h + ' px';
                hasImage = true;
                editorWrap.classList.remove('d-none');
                resetSliders();
                render();
                showSuccess('Photo loaded — press Auto Enhance or tune the sliders.');
            };
            img.onerror = function () {
                showError('Could not read this image. Please try a different file.');
            };
            img.src = e.target.result;
        };
        reader.onerror = function () {
            showError('Could not read this file. Please try a different photo.');
        };
        reader.readAsDataURL(file);
    }

    function resetSliders() {
        sliders.forEach(function (s) {
            document.getElementById(s.range).value = 0;
            document.getElementById(s.val).textContent = '0';
        });
    }

    sliders.forEach(function (s) {
        document.getElementById(s.range).addEventListener('input', function () {
            document.getElementById(s.val).textContent = this.value;
            render();
        });
    });

    dropZone.addEventListener('click', function () { fileInput.click(); });
    fileInput.addEventListener('change', function () {
        if (fileInput.files && fileInput.files[0]) { loadImage(fileInput.files[0]); }
        fileInput.value = '';
    });
    dropZone.addEventListener('dragover', function (e) {
        e.preventDefault();
        dropZone.style.background = '#e9f2ff';
    });
    dropZone.addEventListener('dragleave', function () {
        dropZone.style.background = '#f8f9fa';
    });
    dropZone.addEventListener('drop', function (e) {
        e.preventDefault();
        dropZone.style.background = '#f8f9fa';
        if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files[0]) {
            loadImage(e.dataTransfer.files[0]);
        }
    });

    autoBtn.addEventListener('click', function () {
        if (!hasImage) {
            showError('Please select a photo first.');
            return;
        }
        resetSliders();
        render();
        showSuccess('Auto Enhance applied! Compare with Before / After.');
    });
    resetBtn.addEventListener('click', function () {
        if (!hasImage) { return; }
        resetSliders();
        // reset = render with sliders at 0 (still applies auto levels+white balance)
        render();
        hideAlerts();
    });
    viewAfterBtn.addEventListener('click', function () { setView(true); });
    viewBeforeBtn.addEventListener('click', function () { setView(false); });

    downloadBtn.addEventListener('click', function () {
        if (!hasImage) {
            showError('Please select a photo first.');
            return;
        }
        enhancedCanvas.toBlob(function (blob) {
            if (!blob) {
                showError('Could not create the download. Please try again.');
                return;
            }
            var url = URL.createObjectURL(blob);
            var a = document.createElement('a');
            a.href = url;
            a.download = storedName + '-enhanced.png';
            document.body.appendChild(a);
            a.click();
            a.remove();
            setTimeout(function () { URL.revokeObjectURL(url); }, 4000);
            showSuccess('Enhanced photo downloaded.');
        }, 'image/png');
    });
})();
</script>
@endsection
