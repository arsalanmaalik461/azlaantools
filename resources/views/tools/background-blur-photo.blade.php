@extends('layouts.app')

@section('title', 'Background Blur Photo - Azlaan Tools')
@section('meta_description', 'Blur your photo background for a portrait-mode look - free online, 100% private.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Background Blur Photo</h1>
            <p class="lead text-muted">Upload your photo — the subject stays sharp and the background gets blurred, just like portrait mode. Your photo is processed in your browser only; it is never uploaded.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="photoInput" class="form-label fw-semibold">Select photo</label>
                        <input type="file" class="form-control" id="photoInput" accept="image/*">
                        <div class="form-text">JPG or PNG, max about 10 MB.</div>
                    </div>

                    <div id="editorWrap" class="d-none">
                        <div class="mb-3">
                            <label for="blurRange" class="form-label fw-semibold">Blur intensity: <span id="blurVal">12</span>px</label>
                            <input type="range" class="form-range" id="blurRange" min="0" max="30" value="12">
                        </div>
                        <div class="mb-3">
                            <label for="radiusRange" class="form-label fw-semibold">Sharp area (focus radius): <span id="radiusVal">30</span>%</label>
                            <input type="range" class="form-range" id="radiusRange" min="5" max="60" value="30">
                            <div class="form-text">Click the preview to move the focus point.</div>
                        </div>
                        <canvas id="previewCanvas" class="img-fluid w-100 rounded border" style="cursor:crosshair; max-height:480px;"></canvas>
                        <div class="d-flex gap-2 mt-3">
                            <button type="button" class="btn btn-primary flex-fill" id="downloadBtn">Download Blur Photo</button>
                            <button type="button" class="btn btn-outline-secondary" id="resetFocusBtn">Center focus</button>
                        </div>
                    </div>

                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4"></div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select your photo.</li>
                <li>Adjust the blur intensity and focus radius sliders.</li>
                <li>Click the preview to set the focus point on the subject.</li>
                <li>Press "Download Blur Photo" and save the result.</li>
            </ol>
            <p class="text-muted small">Note: this is a simple blur-mask tool — it does not auto-detect the subject. For the best result, keep the focus point at the center of the subject.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var photoInput = document.getElementById('photoInput');
    var editorWrap = document.getElementById('editorWrap');
    var blurRange = document.getElementById('blurRange');
    var radiusRange = document.getElementById('radiusRange');
    var blurVal = document.getElementById('blurVal');
    var radiusVal = document.getElementById('radiusVal');
    var canvas = document.getElementById('previewCanvas');
    var ctx = canvas.getContext('2d');
    var downloadBtn = document.getElementById('downloadBtn');
    var resetFocusBtn = document.getElementById('resetFocusBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var img = null;
    var focusX = 0.5, focusY = 0.5;

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }

    function render() {
        if (!img) return;
        var maxW = 1200;
        var scale = Math.min(1, maxW / img.width);
        var w = Math.round(img.width * scale);
        var h = Math.round(img.height * scale);
        canvas.width = w;
        canvas.height = h;
        var blur = parseInt(blurRange.value, 10);
        var radiusPct = parseInt(radiusRange.value, 10);
        var r = Math.min(w, h) * radiusPct / 100;
        var fx = focusX * w, fy = focusY * h;

        // Layer 1: blurred background
        ctx.save();
        try { ctx.filter = 'blur(' + blur + 'px)'; } catch (e) { ctx.filter = 'none'; }
        ctx.drawImage(img, 0, 0, w, h);
        ctx.restore();

        // Layer 2: sharp ellipse around focus point with soft feathered edge
        ctx.save();
        ctx.beginPath();
        ctx.ellipse(fx, fy, r * 1.25, r, 0, 0, Math.PI * 2);
        ctx.clip();
        ctx.drawImage(img, 0, 0, w, h);
        ctx.restore();

        // Layer 3: feather ring (blend sharp core into blur with gradient)
        var grad = ctx.createRadialGradient(fx, fy, r * 0.7, fx, fy, r * 1.35);
        grad.addColorStop(0, 'rgba(0,0,0,0)');
        grad.addColorStop(1, 'rgba(0,0,0,1)');
        var off = document.createElement('canvas');
        off.width = w; off.height = h;
        var octx = off.getContext('2d');
        try { octx.filter = 'blur(' + blur + 'px)'; } catch (e2) { octx.filter = 'none'; }
        octx.drawImage(img, 0, 0, w, h);
        octx.save();
        octx.globalCompositeOperation = 'destination-in';
        octx.fillStyle = grad;
        octx.fillRect(0, 0, w, h);
        octx.restore();
        ctx.drawImage(off, 0, 0);

        // Marker for focus point
        ctx.save();
        ctx.strokeStyle = '#0d6efd';
        ctx.lineWidth = 2;
        ctx.beginPath();
        ctx.arc(fx, fy, 8, 0, Math.PI * 2);
        ctx.stroke();
        ctx.restore();
    }

    photoInput.addEventListener('change', function () {
        hideError();
        var file = photoInput.files[0];
        if (!file) return;
        if (!/^image\//.test(file.type)) { showError('Select an image file only (JPG/PNG).'); return; }
        if (file.size > 10 * 1024 * 1024) { showError('The file is bigger than 10 MB — try a smaller photo.'); return; }
        var reader = new FileReader();
        reader.onload = function (e) {
            img = new Image();
            img.onload = function () {
                focusX = 0.5; focusY = 0.5;
                editorWrap.classList.remove('d-none');
                render();
            };
            img.onerror = function () { showError('The photo did not load — try another file.'); };
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
    });

    blurRange.addEventListener('input', function () { blurVal.textContent = blurRange.value; render(); });
    radiusRange.addEventListener('input', function () { radiusVal.textContent = radiusRange.value; render(); });

    canvas.addEventListener('click', function (e) {
        if (!img) return;
        var rect = canvas.getBoundingClientRect();
        focusX = (e.clientX - rect.left) / rect.width;
        focusY = (e.clientY - rect.top) / rect.height;
        render();
    });

    resetFocusBtn.addEventListener('click', function () {
        focusX = 0.5; focusY = 0.5; render();
    });

    downloadBtn.addEventListener('click', function () {
        hideError();
        if (!img) { showError('Select a photo first.'); return; }
        canvas.toBlob(function (blob) {
            if (!blob) { showError('The download could not be prepared.'); return; }
            var a = document.createElement('a');
            a.href = URL.createObjectURL(blob);
            a.download = 'background-blur-photo.png';
            document.body.appendChild(a);
            a.click();
            setTimeout(function () { URL.revokeObjectURL(a.href); a.remove(); }, 2000);
            results.classList.remove('d-none');
            results.innerHTML = '<div class="alert alert-success">Photo downloaded! Portrait-style blur is ready.</div>';
        }, 'image/png');
    });
})();
</script>
@endsection
