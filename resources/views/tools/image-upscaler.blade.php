@extends('layouts.app')
@section('title', 'Image Upscaler - Azlaan Tools')
@section('meta_description', 'Enlarge your photo up to 2x or 4x without blur. Free online image upscaler, no signup.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Image Upscaler</h1>
            <p class="lead text-muted">Make your photo up to 2x or 4x bigger — with smart smoothing inside the browser, without losing quality. Everything happens on your device; your photo is never uploaded to a server.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="upFile" class="form-label fw-semibold">Choose a photo</label>
                        <input type="file" class="form-control" id="upFile" accept="image/*">
                        <div class="form-text">JPG, PNG, WebP and more. Your photo is processed only in your browser.</div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="upScale" class="form-label fw-semibold">Upscale factor</label>
                            <select class="form-select" id="upScale">
                                <option value="2">2x (double size)</option>
                                <option value="3">3x</option>
                                <option value="4">4x (quadruple)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="upFormat" class="form-label fw-semibold">Output format</label>
                            <select class="form-select" id="upFormat">
                                <option value="image/png">PNG (best quality)</option>
                                <option value="image/jpeg">JPG (smaller size)</option>
                                <option value="image/webp">WebP</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" id="upSharpen" checked>
                        <label class="form-check-label" for="upSharpen">Apply mild sharpening (keeps edges crisp)</label>
                    </div>
                    <button type="button" class="btn btn-primary w-100 mt-3" id="goBtn">Upscale &amp; Download</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="alert alert-success" id="upInfo" role="alert"></div>
                        <div class="row g-3">
                            <div class="col-6">
                                <p class="fw-semibold mb-1">Original</p>
                                <img id="upOrig" class="img-fluid border rounded" alt="Original photo">
                                <p class="small text-muted" id="upOrigSize"></p>
                            </div>
                            <div class="col-6">
                                <p class="fw-semibold mb-1">Upscaled</p>
                                <img id="upBig" class="img-fluid border rounded" alt="Upscaled photo">
                                <p class="small text-muted" id="upBigSize"></p>
                            </div>
                        </div>
                        <a class="btn btn-success w-100 mt-3 disabled" id="upDownload" href="#" download="upscaled.png">Download Full Size</a>
                        <p class="small text-muted mt-2 mb-0">Preview is shown smaller — the download will have full resolution.</p>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Choose your photo.</li>
                <li>Select a 2x, 3x or 4x factor and an output format.</li>
                <li>Press <strong>Upscale &amp; Download</strong> to get your full-size HD photo.</li>
            </ol>
            <p class="small text-muted">This tool uses high-quality canvas scaling in the browser — it does not invent detail like an AI upscaler, but it keeps edges sharp and blur to a minimum.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function fmtBytes(b) {
        if (!b && b !== 0) return '-';
        if (b < 1024) return b + ' B';
        if (b < 1048576) return (b / 1024).toFixed(1) + ' KB';
        return (b / 1048576).toFixed(2) + ' MB';
    }
    function downloadBlob(blob, name) {
        var a = document.getElementById('upDownload');
        a.href = URL.createObjectURL(blob);
        a.download = name;
        a.classList.remove('disabled');
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var f = document.getElementById('upFile').files[0];
        if (!f) { showError('Please choose a photo first.'); return; }
        var factor = parseInt(document.getElementById('upScale').value, 10) || 2;
        var format = document.getElementById('upFormat').value;
        var sharpen = document.getElementById('upSharpen').checked;

        var img = new Image();
        img.onload = function () {
            try {
                var ow = img.naturalWidth, oh = img.naturalHeight;
                var nw = ow * factor, nh = oh * factor;
                var maxSide = 8192;
                if (Math.max(nw, nh) > maxSide) {
                    var rs = maxSide / Math.max(nw, nh);
                    nw = Math.round(nw * rs); nh = Math.round(nh * rs);
                }
                var cv = document.createElement('canvas');
                cv.width = nw; cv.height = nh;
                var ctx = cv.getContext('2d');
                ctx.imageSmoothingEnabled = true;
                ctx.imageSmoothingQuality = 'high';
                ctx.drawImage(img, 0, 0, nw, nh);
                if (sharpen) {
                    // simple unsharp mask
                    var id = ctx.getImageData(0, 0, nw, nh);
                    var d = id.data, w = nw, h = nh;
                    var orig = new Uint8ClampedArray(d);
                    for (var y = 1; y < h - 1; y++) {
                        for (var x = 1; x < w - 1; x++) {
                            for (var c = 0; c < 3; c++) {
                                var i = (y * w + x) * 4 + c;
                                var avg = (orig[i - w * 4] + orig[i + w * 4] + orig[i - 4] + orig[i + 4]) / 4;
                                var v = orig[i] + (orig[i] - avg) * 0.45;
                                d[i] = v < 0 ? 0 : (v > 255 ? 255 : v);
                            }
                        }
                    }
                    ctx.putImageData(id, 0, 0);
                }
                cv.toBlob(function (blob) {
                    if (!blob) { showError('Upscale failed. Please try a different photo.'); return; }
                    document.getElementById('upOrig').src = img.src;
                    document.getElementById('upBig').src = URL.createObjectURL(blob);
                    document.getElementById('upOrigSize').textContent = ow + ' x ' + oh + ' px';
                    document.getElementById('upBigSize').textContent = nw + ' x ' + nh + ' px - ' + fmtBytes(blob.size);
                    document.getElementById('upInfo').textContent = 'Done! ' + ow + 'x' + oh + ' to ' + nw + 'x' + nh + ' px (' + factor + 'x).';
                    var ext = format === 'image/png' ? 'png' : (format === 'image/jpeg' ? 'jpg' : 'webp');
                    downloadBlob(blob, 'upscaled-' + factor + 'x.' + ext);
                    results.classList.remove('d-none');
                }, format, 0.92);
            } catch (e) {
                showError('Upscale failed: ' + e.message);
            }
        };
        img.onerror = function () { showError('This file could not be opened as an image.'); };
        img.src = URL.createObjectURL(f);
    });
})();
</script>
@endsection
