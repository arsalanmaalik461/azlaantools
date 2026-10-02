@extends('layouts.app')

@section('title', 'Photo Noise Reducer - Azlaan Tools')
@section('meta_description', 'Reduce digital noise and grain from photos with a free online denoise tool. Cleaner night photos.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Photo Noise Reducer</h1>
            <p class="lead text-muted">Reduce digital noise and grain from your photos, right in the browser. The median filter cleans the grainy texture — best for night photos.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="imgFile" class="form-label fw-semibold">Select a photo</label>
                        <input type="file" class="form-control" id="imgFile" accept="image/*">
                        <div class="form-text">Your photo is only processed in your browser — it is never uploaded anywhere.</div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="strength" class="form-label fw-semibold">Denoise strength</label>
                            <select class="form-select" id="strength">
                                <option value="1">Light — mild noise</option>
                                <option value="2" selected>Medium — medium noise</option>
                                <option value="3">Strong — heavy grain</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="blend" class="form-label fw-semibold">Effect: <span id="blendVal">65</span>%</label>
                            <input type="range" class="form-range" id="blend" min="0" max="100" value="65">
                            <div class="form-text">Low % = more original detail kept; high % = less noise, softer photo.</div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100 mt-3" id="goBtn">Reduce Noise</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>
                    <div class="alert alert-info mt-3 d-none" id="statusBox" role="status"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <h6 class="text-muted">Original</h6>
                                <canvas id="beforeCanvas" class="img-fluid border rounded w-100"></canvas>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted">Denoised</h6>
                                <canvas id="afterCanvas" class="img-fluid border rounded w-100"></canvas>
                            </div>
                        </div>
                        <p class="small text-muted mt-2" id="noteLine"></p>
                        <a href="#" class="btn btn-success w-100" id="dlLink" download="denoised.png">Download Denoised Photo</a>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select your photo.</li>
                <li>Pick the strength for your noise level (Light / Medium / Strong).</li>
                <li>Press "Reduce Noise" — then download the denoised photo.</li>
            </ol>
            <h2>How does it work?</h2>
            <p>This tool finds the <strong>median</strong> of the pixels around every pixel. Random grain (noise) is removed by the median while edges and shapes stay safe. Too much effect can soften the photo, so use the blend slider to pick your own balance.</p>
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
    var statusBox = document.getElementById('statusBox');
    var results = document.getElementById('results');
    var blend = document.getElementById('blend');
    var blendVal = document.getElementById('blendVal');
    blend.addEventListener('input', function () { blendVal.textContent = blend.value; });

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        statusBox.classList.add('d-none');
        results.classList.add('d-none');
    }
    function setStatus(msg) {
        statusBox.textContent = msg;
        statusBox.classList.remove('d-none');
        errorBox.classList.add('d-none');
    }

    function medianOf(arr) {
        var a = arr.slice().sort(function (x, y) { return x - y; });
        var mid = a.length >> 1;
        return a.length % 2 ? a[mid] : Math.round((a[mid - 1] + a[mid]) / 2);
    }

    function denoise(srcData, w, h, radius, blendAmt) {
        var src = srcData.data;
        var out = new Uint8ClampedArray(src.length);
        var b = blendAmt / 100;
        var size = 2 * radius + 1;
        for (var y = 0; y < h; y++) {
            for (var x = 0; x < w; x++) {
                var rs = [], gs = [], bs = [];
                for (var dy = -radius; dy <= radius; dy++) {
                    var yy = y + dy;
                    if (yy < 0) yy = 0; else if (yy >= h) yy = h - 1;
                    for (var dx = -radius; dx <= radius; dx++) {
                        var xx = x + dx;
                        if (xx < 0) xx = 0; else if (xx >= w) xx = w - 1;
                        var k = (yy * w + xx) * 4;
                        rs.push(src[k]); gs.push(src[k + 1]); bs.push(src[k + 2]);
                    }
                }
                var k0 = (y * w + x) * 4;
                out[k0]     = Math.round(src[k0]     * (1 - b) + medianOf(rs) * b);
                out[k0 + 1] = Math.round(src[k0 + 1] * (1 - b) + medianOf(gs) * b);
                out[k0 + 2] = Math.round(src[k0 + 2] * (1 - b) + medianOf(bs) * b);
                out[k0 + 3] = src[k0 + 3];
            }
        }
        return new ImageData(out, w, h);
    }

    goBtn.addEventListener('click', function () {
        errorBox.classList.add('d-none');
        results.classList.add('d-none');
        var file = document.getElementById('imgFile').files[0];
        if (!file) { showError('Please select a photo first.'); return; }
        if (!file.type || file.type.indexOf('image/') !== 0) { showError('Please select an image file (JPG, PNG, etc.).'); return; }
        setStatus('Loading photo...');
        var url = URL.createObjectURL(file);
        var img = new Image();
        img.onload = function () {
            URL.revokeObjectURL(url);
            var maxDim = 1200;
            var scale = Math.min(1, maxDim / Math.max(img.width, img.height));
            var w = Math.max(1, Math.round(img.width * scale));
            var h = Math.max(1, Math.round(img.height * scale));
            var before = document.getElementById('beforeCanvas');
            var after = document.getElementById('afterCanvas');
            before.width = w; before.height = h;
            after.width = w; after.height = h;
            var bctx = before.getContext('2d');
            bctx.drawImage(img, 0, 0, w, h);
            var radius = parseInt(document.getElementById('strength').value, 10);
            var blendAmt = parseInt(blend.value, 10);
            setStatus('Reducing noise... (large photos take a little time)');
            setTimeout(function () {
                try {
                    var imgData = bctx.getImageData(0, 0, w, h);
                    var t0 = performance.now();
                    var clean = denoise(imgData, w, h, radius, blendAmt);
                    var secs = ((performance.now() - t0) / 1000).toFixed(1);
                    after.getContext('2d').putImageData(clean, 0, 0);
                    var note = document.getElementById('noteLine');
                    note.textContent = 'Processed ' + w + 'x' + h + ' px in ' + secs + 's. ' +
                        (scale < 1 ? 'Photo was resized before processing for speed.' : 'Processed at full resolution.');
                    var dl = document.getElementById('dlLink');
                    dl.href = after.toDataURL('image/png');
                    statusBox.classList.add('d-none');
                    results.classList.remove('d-none');
                    results.scrollIntoView({ behavior: 'smooth', block: 'start' });
                } catch (e) {
                    showError('Processing failed: please try a smaller photo.');
                }
            }, 60);
        };
        img.onerror = function () { showError('Could not load the photo. Please try another file.'); };
        img.src = url;
    });
})();
</script>
@endsection
