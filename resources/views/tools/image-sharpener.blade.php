@extends('layouts.app')

@section('title', 'Image Sharpener - Azlaan Tools')
@section('meta_description', 'Sharpen any photo in your browser with an adjustable unsharp mask. Free, private, no signup.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Image Sharpener</h1>
            <p class="lead text-muted">Increase the sharpness and detail of your photo with an unsharp mask. The image is processed in your browser only and is never uploaded anywhere.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="imgInput" class="form-label fw-semibold">Choose a photo (JPG/PNG/WebP)</label>
                        <input type="file" class="form-control" id="imgInput" accept="image/*">
                        <div class="form-text">The photo stays on your device only - it is never uploaded to a server.</div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="amountRange" class="form-label fw-semibold">Sharpness amount: <span id="amountVal" class="badge bg-primary">80%</span></label>
                            <input type="range" class="form-range" id="amountRange" min="0" max="200" value="80">
                            <div class="form-text">A higher amount means more detail, but too much can cause a "halo" effect.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="radiusRange" class="form-label fw-semibold">Radius: <span id="radiusVal" class="badge bg-primary">1.5 px</span></label>
                            <input type="range" class="form-range" id="radiusRange" min="0" max="5" step="0.5" value="1.5">
                            <div class="form-text">A smaller radius is better for fine detail.</div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 flex-wrap mb-3">
                        <button type="button" class="btn btn-primary flex-grow-1" id="goBtn">Sharpen Photo</button>
                        <button type="button" class="btn btn-outline-secondary" id="toggleBtn" disabled>View Before / After</button>
                        <button type="button" class="btn btn-outline-success" id="dlBtn" disabled>Download PNG</button>
                    </div>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="row">
                            <div class="col-12">
                                <div class="text-center mb-2"><span class="badge bg-info" id="stageBadge">Sharpened preview</span></div>
                                <img id="previewImg" class="img-fluid border rounded w-100" alt="Sharpened preview">
                            </div>
                        </div>
                        <div class="mt-2 small text-muted" id="dimInfo"></div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Choose your photo file.</li>
                <li>Adjust the sharpness amount and radius sliders.</li>
                <li>Press <strong>Sharpen Photo</strong> - the result will appear below right away.</li>
                <li>Use <strong>View Before / After</strong> to compare the original and sharpened photo.</li>
                <li>Use <strong>Download PNG</strong> to save the final photo.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var imgInput = document.getElementById('imgInput');
    var amountRange = document.getElementById('amountRange');
    var radiusRange = document.getElementById('radiusRange');
    var amountVal = document.getElementById('amountVal');
    var radiusVal = document.getElementById('radiusVal');
    var goBtn = document.getElementById('goBtn');
    var toggleBtn = document.getElementById('toggleBtn');
    var dlBtn = document.getElementById('dlBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var previewImg = document.getElementById('previewImg');
    var stageBadge = document.getElementById('stageBadge');
    var dimInfo = document.getElementById('dimInfo');

    var origImg = null, origName = 'sharpened', resultUrl = null, showingResult = true;
    var MAX_SIDE = 2000;

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function revokeUrl() {
        if (resultUrl) { URL.revokeObjectURL(resultUrl); resultUrl = null; }
    }

    amountRange.addEventListener('input', function () { amountVal.textContent = amountRange.value + '%'; });
    radiusRange.addEventListener('input', function () { radiusVal.textContent = Number(radiusRange.value).toFixed(1) + ' px'; });

    imgInput.addEventListener('change', function () {
        var f = imgInput.files[0];
        if (!f) return;
        if (!f.type.match(/^image\//)) { showError('Please select an image file.'); return; }
        hideError();
        var reader = new FileReader();
        reader.onload = function (e) {
            var img = new Image();
            img.onload = function () {
                origImg = img;
                origName = f.name.replace(/\.[^.]+$/, '') || 'sharpened';
                toggleBtn.disabled = false;
                dimInfo.textContent = 'Original size: ' + img.naturalWidth + ' x ' + img.naturalHeight + ' px';
                showError(''); // clear
                hideError();
            };
            img.onerror = function () { showError('The image could not be loaded. The file may be corrupt.'); };
            img.src = e.target.result;
        };
        reader.readAsDataURL(f);
    });

    function unsharpMask(img, amountPct, radius) {
        var scale = Math.min(1, MAX_SIDE / Math.max(img.naturalWidth, img.naturalHeight));
        var w = Math.max(1, Math.round(img.naturalWidth * scale));
        var h = Math.max(1, Math.round(img.naturalHeight * scale));
        var base = document.createElement('canvas');
        base.width = w; base.height = h;
        base.getContext('2d').drawImage(img, 0, 0, w, h);
        if (radius <= 0 || amountPct <= 0) return base;
        var blur = document.createElement('canvas');
        blur.width = w; blur.height = h;
        var bctx = blur.getContext('2d');
        bctx.filter = 'blur(' + radius + 'px)';
        bctx.drawImage(base, 0, 0);
        var bdata = bctx.getImageData(0, 0, w, h);
        var bctx2 = base.getContext('2d');
        var odata = bctx2.getImageData(0, 0, w, h);
        var od = odata.data, bd = bdata.data;
        var amt = amountPct / 100;
        for (var i = 0; i < od.length; i += 4) {
            for (var c = 0; c < 3; c++) {
                var v = od[i + c] + (od[i + c] - bd[i + c]) * amt;
                od[i + c] = v < 0 ? 0 : (v > 255 ? 255 : Math.round(v));
            }
        }
        bctx2.putImageData(odata, 0, 0);
        return base;
    }

    goBtn.addEventListener('click', function () {
        hideError();
        if (!origImg) { showError('Please select a photo first.'); return; }
        var amount = parseInt(amountRange.value, 10);
        var radius = parseFloat(radiusRange.value);
        try {
            var canvas = unsharpMask(origImg, amount, radius);
            revokeUrl();
            resultUrl = canvas.toDataURL('image/png');
            previewImg.src = resultUrl;
            showingResult = true;
            stageBadge.textContent = 'Sharpened preview';
            toggleBtn.disabled = false;
            dlBtn.disabled = false;
            results.classList.remove('d-none');
            dimInfo.textContent = 'Processed size: ' + canvas.width + ' x ' + canvas.height + ' px (amount ' + amount + '%, radius ' + radius + ' px)';
        } catch (err) {
            showError('There was a problem with processing. Please try a smaller image.');
        }
    });

    toggleBtn.addEventListener('click', function () {
        if (!origImg || !resultUrl) return;
        showingResult = !showingResult;
        if (showingResult) {
            previewImg.src = resultUrl;
            stageBadge.textContent = 'Sharpened preview';
        } else {
            previewImg.src = origImg.src;
            stageBadge.textContent = 'Original (before)';
        }
        results.classList.remove('d-none');
    });

    dlBtn.addEventListener('click', function () {
        if (!resultUrl) return;
        var a = document.createElement('a');
        a.href = resultUrl;
        a.download = origName + '-sharpened.png';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    });
})();
</script>
@endsection
