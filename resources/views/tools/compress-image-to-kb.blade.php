@extends('layouts.app')
@section('title', 'Compress Image to KB — Reduce Photo to 20, 50, 100 KB | Azlaan Tools')
@section('meta_description', 'Compress a photo to an exact target size in KB — 20, 50, 100, 200 or 300 KB for job, passport and online forms. Free, no signup, photo never leaves your browser.')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-2">Compress Image to KB</h1>
            <p class="lead text-muted">Need a photo under a fixed size for a job application, passport or online form? Pick your target KB and this tool shrinks quality — and dimensions only if needed — until the file fits.</p>
            <div class="card shadow-sm">
                <div class="card-body">
                    <div id="dropZone" class="border rounded-3 p-4 text-center bg-light" style="border-style: dashed !important; cursor: pointer;">
                        <p class="fw-semibold mb-1">Drag &amp; drop your photo here, or click to browse</p>
                        <p class="text-muted small mb-0">JPG, PNG or WebP</p>
                        <input type="file" id="fileInput" class="d-none" accept="image/*">
                    </div>
                    <div id="errorBox" class="alert alert-danger d-none mt-3" role="alert"></div>
                    <div id="toolWrap" class="d-none mt-4">
                        <p class="fw-semibold mb-2">Target size</p>
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <button type="button" class="btn btn-outline-primary preset-btn" data-kb="20">20 KB</button>
                            <button type="button" class="btn btn-outline-primary preset-btn" data-kb="50">50 KB</button>
                            <button type="button" class="btn btn-outline-primary preset-btn active" data-kb="100">100 KB</button>
                            <button type="button" class="btn btn-outline-primary preset-btn" data-kb="200">200 KB</button>
                            <button type="button" class="btn btn-outline-primary preset-btn" data-kb="300">300 KB</button>
                        </div>
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label class="form-label fw-semibold" for="customKb">Custom target (KB)</label>
                                <input type="number" id="customKb" class="form-control form-control-lg" min="5" max="5000" placeholder="e.g. 75">
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label fw-semibold" for="formatSelect">Format</label>
                                <select id="formatSelect" class="form-select form-select-lg">
                                    <option value="image/jpeg" selected>JPG (best for forms)</option>
                                    <option value="image/webp">WebP (smallest)</option>
                                </select>
                            </div>
                        </div>
                        <button type="button" id="compressBtn" class="btn btn-primary btn-lg w-100 mt-3">Compress to Target Size</button>
                        <div id="statusText" class="small text-muted mt-2"></div>
                        <div id="resultWrap" class="d-none mt-3">
                            <div class="row g-3 text-center">
                                <div class="col-6"><div class="border rounded p-3"><div class="text-muted small">Before</div><strong id="beforeInfo">-</strong><img id="beforeImg" class="img-fluid rounded mt-2" alt="Before" style="max-height:160px;"></div></div>
                                <div class="col-6"><div class="border rounded p-3"><div class="text-muted small">After</div><strong id="afterInfo">-</strong><img id="afterImg" class="img-fluid rounded mt-2" alt="After" style="max-height:160px;"></div></div>
                            </div>
                            <a id="downloadBtn" href="#" class="btn btn-success btn-lg w-100 mt-3">Download Compressed Photo</a>
                        </div>
                    </div>
                </div>
            </div>
            <h2 class="mt-5">How to use</h2>
            <ol>
                <li>Upload your photo.</li>
                <li>Pick a preset — 20 KB, 50 KB, 100 KB, 200 KB, 300 KB — or type a custom KB. Most job / admission / passport forms ask for 50–200 KB.</li>
                <li>Press <strong>Compress to Target Size</strong>. The tool tries qualities first, then gently reduces dimensions if quality alone is not enough.</li>
                <li>Check before / after sizes and dimensions, then download.</li>
            </ol>
            <div class="alert alert-info mt-4"><strong>Privacy note:</strong> Your photo never leaves your browser. Everything happens on your own device.</div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
(function () {
    var dropZone = document.getElementById('dropZone');
    var fileInput = document.getElementById('fileInput');
    var errorBox = document.getElementById('errorBox');
    var toolWrap = document.getElementById('toolWrap');
    var customKb = document.getElementById('customKb');
    var formatSelect = document.getElementById('formatSelect');
    var statusText = document.getElementById('statusText');
    var resultWrap = document.getElementById('resultWrap');
    var beforeInfo = document.getElementById('beforeInfo');
    var afterInfo = document.getElementById('afterInfo');
    var beforeImg = document.getElementById('beforeImg');
    var afterImg = document.getElementById('afterImg');
    var downloadBtn = document.getElementById('downloadBtn');
    var targetKb = 100;
    var currentFile = null, currentImg = null, srcUrl = null, outUrl = null;
    function fmt(b) { return b < 1024 ? b + ' B' : (b < 1048576 ? (b / 1024).toFixed(1) + ' KB' : (b / 1048576).toFixed(2) + ' MB'); }
    function showError(m) { errorBox.textContent = m; errorBox.classList.remove('d-none'); }
    function hideError() { errorBox.classList.add('d-none'); }
    document.querySelectorAll('.preset-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.preset-btn').forEach(function (b) { b.classList.remove('active'); });
            btn.classList.add('active'); targetKb = parseInt(btn.getAttribute('data-kb'), 10); customKb.value = '';
        });
    });
    customKb.addEventListener('input', function () {
        var v = parseInt(customKb.value, 10);
        if (v > 0) { targetKb = v; document.querySelectorAll('.preset-btn').forEach(function (b) { b.classList.remove('active'); }); }
    });
    function handleFile(file) {
        hideError(); if (!file) return;
        if (!file.type || file.type.indexOf('image/') !== 0) { showError('Please choose an image file.'); return; }
        currentFile = file;
        if (srcUrl) URL.revokeObjectURL(srcUrl);
        srcUrl = URL.createObjectURL(file);
        var img = new Image();
        img.onload = function () {
            currentImg = img; beforeImg.src = srcUrl;
            beforeInfo.textContent = fmt(file.size) + ' • ' + img.naturalWidth + ' x ' + img.naturalHeight;
            toolWrap.classList.remove('d-none'); resultWrap.classList.add('d-none');
        };
        img.onerror = function () { showError('Could not read that image.'); };
        img.src = srcUrl;
    }
    function encode(w, h, q) {
        return new Promise(function (resolve) {
            var c = document.createElement('canvas'); c.width = w; c.height = h;
            var ctx = c.getContext('2d'); ctx.fillStyle = '#ffffff'; ctx.fillRect(0, 0, w, h); ctx.drawImage(currentImg, 0, 0, w, h);
            c.toBlob(function (b) { resolve(b); }, formatSelect.value, q);
        });
    }
    document.getElementById('compressBtn').addEventListener('click', function () {
        if (!currentImg) { showError('Upload a photo first.'); return; }
        hideError();
        var target = targetKb * 1024;
        var w = currentImg.naturalWidth, h = currentImg.naturalHeight;
        statusText.textContent = 'Working… trying to fit under ' + targetKb + ' KB.';
        var best = null;
        var smallest = null;
        function tryScale(scale) {
            var tw = Math.max(20, Math.round(w * scale)), th = Math.max(20, Math.round(h * scale));
            var lo = 0.05, hi = 0.95, i = 0; best = null;
            function step() {
                if (i >= 9) {
                    if (best && best.size <= target) { finish(best, tw, th); return; }
                    if (scale > 0.15) { tryScale(scale * 0.75); return; }
                    if (smallest) { finish(smallest.blob, smallest.w, smallest.h); statusText.textContent = 'Closest possible result (' + fmt(smallest.blob.size) + ') — could not go below ' + targetKb + ' KB without destroying the photo.'; return; }
                    showError('Compression failed. Try WebP format.'); statusText.textContent = ''; return;
                }
                i++;
                var mid = (lo + hi) / 2;
                encode(tw, th, mid).then(function (blob) {
                    if (!blob) { showError('Encoding failed in this browser.'); return; }
                    if (!smallest || blob.size < smallest.blob.size) smallest = { blob: blob, w: tw, h: th };
                    if (blob.size <= target) { best = blob; lo = mid; } else { hi = mid; }
                    if (blob.size <= target && i >= 5) { finish(best, tw, th); return; }
                    step();
                });
            }
            step();
        }
        function finish(blob, tw, th) {
            if (outUrl) URL.revokeObjectURL(outUrl);
            outUrl = URL.createObjectURL(blob);
            afterImg.src = outUrl;
            afterInfo.textContent = fmt(blob.size) + ' • ' + tw + ' x ' + th;
            downloadBtn.href = outUrl;
            downloadBtn.setAttribute('download', 'compressed-' + targetKb + 'kb.' + (formatSelect.value === 'image/webp' ? 'webp' : 'jpg'));
            resultWrap.classList.remove('d-none');
            statusText.textContent = 'Done: ' + fmt(currentFile.size) + ' → ' + fmt(blob.size) + ' (target ' + targetKb + ' KB).';
        }
        tryScale(1);
    });
    dropZone.addEventListener('click', function () { fileInput.click(); });
    fileInput.addEventListener('change', function () { handleFile(fileInput.files && fileInput.files[0]); });
    dropZone.addEventListener('dragover', function (e) { e.preventDefault(); });
    dropZone.addEventListener('drop', function (e) { e.preventDefault(); if (e.dataTransfer && e.dataTransfer.files.length) handleFile(e.dataTransfer.files[0]); });
})();
</script>
@endsection
