@extends('layouts.app')

@section('title', 'Photo To Pixel Art - Azlaan Tools')
@section('meta_description', 'Turn any photo into retro pixel art online for free. 8-bit gaming style effect with adjustable pixel size.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Photo To Pixel Art</h1>
            <p class="lead text-muted">Turn your photo into retro pixel art — 8-bit gaming style effect, completely free. Everything happens in your browser.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="imgInput" class="form-label fw-semibold">Select a photo</label>
                        <input type="file" class="form-control" id="imgInput" accept="image/*">
                        <div class="form-text">JPG, PNG, WebP — any image works</div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="pixelSize" class="form-label fw-semibold">Pixel size: <span id="pixelVal">12</span> px</label>
                            <input type="range" class="form-range" id="pixelSize" min="4" max="64" value="12">
                            <div class="form-text">Bigger number = more retro look</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="colorLevels" class="form-label fw-semibold">Color style</label>
                            <select class="form-select" id="colorLevels">
                                <option value="0">Full colors (all colors)</option>
                                <option value="16">16 colors (retro)</option>
                                <option value="8" selected>8 colors (classic 8-bit)</option>
                                <option value="4">4 colors (Game Boy style)</option>
                            </select>
                            <div class="form-text">Fewer colors = old game look</div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="renderBtn" disabled>Make Pixel Art</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <p class="fw-semibold mb-1">Original</p>
                                <canvas id="inCanvas" class="img-fluid border rounded" style="max-width:100%;"></canvas>
                            </div>
                            <div class="col-md-6 mb-3">
                                <p class="fw-semibold mb-1">Pixel Art</p>
                                <canvas id="outCanvas" class="img-fluid border rounded" style="max-width:100%;"></canvas>
                            </div>
                        </div>
                        <a class="btn btn-success w-100" id="dlBtn" href="#" download="pixel-art.png">Download Pixel Art (PNG)</a>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select your photo.</li>
                <li>Adjust the retro look with the pixel size slider and choose a color style.</li>
                <li>Press <strong>Make Pixel Art</strong> — then download the PNG.</li>
            </ol>
            <p class="text-muted small">Tip: for photos with faces, 10–16 px pixel size looks best; for landscapes try 20+ px.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var imgInput = document.getElementById('imgInput');
    var pixelSize = document.getElementById('pixelSize');
    var pixelVal = document.getElementById('pixelVal');
    var colorLevels = document.getElementById('colorLevels');
    var renderBtn = document.getElementById('renderBtn');
    var dlBtn = document.getElementById('dlBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var inCanvas = document.getElementById('inCanvas');
    var outCanvas = document.getElementById('outCanvas');
    var img = new Image();
    var imgReady = false;

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }

    pixelSize.addEventListener('input', function () {
        pixelVal.textContent = pixelSize.value;
    });

    imgInput.addEventListener('change', function () {
        hideError();
        results.classList.add('d-none');
        imgReady = false;
        renderBtn.disabled = true;
        var f = imgInput.files[0];
        if (!f) return;
        if (!f.type || f.type.indexOf('image/') !== 0) {
            showError('Please select only an image file (JPG, PNG, WebP).');
            return;
        }
        var r = new FileReader();
        r.onload = function () {
            img.onload = function () {
                imgReady = true;
                var w = img.naturalWidth;
                var h = img.naturalHeight;
                var maxSide = 640;
                var scale = Math.min(1, maxSide / Math.max(w, h));
                var dw = Math.round(w * scale);
                var dh = Math.round(h * scale);
                inCanvas.width = dw;
                inCanvas.height = dh;
                var ctx = inCanvas.getContext('2d');
                ctx.drawImage(img, 0, 0, dw, dh);
                renderBtn.disabled = false;
            };
            img.onerror = function () { showError('Could not read the image.'); };
            img.src = r.result;
        };
        r.onerror = function () { showError('Could not read the file.'); };
        r.readAsDataURL(f);
    });

    function quantize(ctx, w, h, levels) {
        if (!levels || levels <= 0) return;
        var data = ctx.getImageData(0, 0, w, h);
        var px = data.data;
        var steps = levels - 1;
        for (var i = 0; i < px.length; i += 4) {
            px[i] = Math.round(px[i] / 255 * steps) / steps * 255;
            px[i + 1] = Math.round(px[i + 1] / 255 * steps) / steps * 255;
            px[i + 2] = Math.round(px[i + 2] / 255 * steps) / steps * 255;
        }
        ctx.putImageData(data, 0, 0);
    }

    renderBtn.addEventListener('click', function () {
        hideError();
        if (!imgReady) { showError('Please select a photo first.'); return; }
        var ps = parseInt(pixelSize.value, 10) || 12;
        var levels = parseInt(colorLevels.value, 10) || 0;
        var w = inCanvas.width;
        var h = inCanvas.height;
        var tw = Math.max(4, Math.min(200, Math.round(w / ps)));
        var th = Math.max(4, Math.min(200, Math.round(h / ps)));

        var small = document.createElement('canvas');
        small.width = tw;
        small.height = th;
        var sctx = small.getContext('2d');
        sctx.imageSmoothingEnabled = true;
        sctx.imageSmoothingQuality = 'high';
        sctx.drawImage(inCanvas, 0, 0, tw, th);
        quantize(sctx, tw, th, levels);

        outCanvas.width = w;
        outCanvas.height = h;
        var octx = outCanvas.getContext('2d');
        octx.imageSmoothingEnabled = false;
        octx.drawImage(small, 0, 0, w, h);

        dlBtn.href = outCanvas.toDataURL('image/png');
        results.classList.remove('d-none');
        results.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    });
})();
</script>
@endsection
