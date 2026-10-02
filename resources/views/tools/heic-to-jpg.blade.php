@extends('layouts.app')
@section('title', 'HEIC to JPG — Convert iPhone Photos Free | Azlaan Tools')
@section('meta_description', 'Convert iPhone HEIC and HEIF photos to JPG or PNG free, in batch, with ZIP download. No signup, photos never leave your browser.')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-2">HEIC to JPG Converter</h1>
            <p class="lead text-muted">iPhone photos in HEIC format do not open everywhere. Convert one or many HEIC / HEIF photos to JPG or PNG here — in your browser, for free.</p>
            <div class="card shadow-sm">
                <div class="card-body">
                    <div id="dropZone" class="border rounded-3 p-4 text-center bg-light" style="border-style: dashed !important; cursor: pointer;">
                        <p class="fw-semibold mb-1">Drag &amp; drop HEIC photos here, or click to browse</p>
                        <p class="text-muted small mb-0">You can select many files at once</p>
                        <input type="file" id="fileInput" class="d-none" accept=".heic,.heif,image/heic,image/heif" multiple>
                    </div>
                    <div id="errorBox" class="alert alert-danger d-none mt-3" role="alert"></div>
                    <div class="row g-3 mt-2">
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold" for="formatSelect">Output format</label>
                            <select id="formatSelect" class="form-select">
                                <option value="image/jpeg" selected>JPG</option>
                                <option value="image/png">PNG</option>
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold" for="qualityRange">JPG quality: <span id="qualityVal">92</span>%</label>
                            <input type="range" id="qualityRange" class="form-range" min="50" max="100" value="92">
                        </div>
                    </div>
                    <div id="statusText" class="small text-muted mt-2"></div>
                    <ul id="resultList" class="list-group mt-3"></ul>
                    <button type="button" id="zipBtn" class="btn btn-success btn-lg w-100 mt-3 d-none">Download All as ZIP</button>
                </div>
            </div>
            <h2 class="mt-5">How to use</h2>
            <ol>
                <li>Upload one or more .heic / .heif photos from your iPhone.</li>
                <li>Choose JPG or PNG and set quality.</li>
                <li>Each converted photo appears with its own download link — or press <strong>Download All as ZIP</strong> for batches.</li>
            </ol>
            <div class="alert alert-info mt-4"><strong>Privacy note:</strong> Your photos never leave your browser. Conversion happens on your own device.</div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/heic2any@0.0.4/dist/heic2any.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script>
(function () {
    var dropZone = document.getElementById('dropZone');
    var fileInput = document.getElementById('fileInput');
    var errorBox = document.getElementById('errorBox');
    var formatSelect = document.getElementById('formatSelect');
    var qualityRange = document.getElementById('qualityRange');
    var qualityVal = document.getElementById('qualityVal');
    var statusText = document.getElementById('statusText');
    var resultList = document.getElementById('resultList');
    var zipBtn = document.getElementById('zipBtn');
    var converted = [];
    qualityRange.addEventListener('input', function () { qualityVal.textContent = qualityRange.value; });
    function showError(m) { errorBox.textContent = m; errorBox.classList.remove('d-none'); }
    function fmt(b) { return b < 1024 ? b + ' B' : (b < 1048576 ? (b / 1024).toFixed(1) + ' KB' : (b / 1048576).toFixed(2) + ' MB'); }
    function base(name) { var d = name.lastIndexOf('.'); return d > 0 ? name.substring(0, d) : name; }
    function handleFiles(files) {
        errorBox.classList.add('d-none');
        if (typeof heic2any === 'undefined') { showError('Converter library failed to load. Check your internet and refresh.'); return; }
        var list = Array.prototype.slice.call(files || []);
        if (!list.length) return;
        var done = 0;
        var failedNames = [];
        statusText.textContent = 'Converting 0 / ' + list.length + '… (one at a time — HEIC decoding is heavy, please wait)';
        // Convert strictly one file at a time: heic2any runs a large WASM decoder,
        // and firing a whole batch in parallel can freeze or crash the tab.
        var chain = Promise.resolve();
        list.forEach(function (file) {
            chain = chain.then(function () {
                var type = formatSelect.value;
                var ext = type === 'image/png' ? 'png' : 'jpg';
                var q = parseInt(qualityRange.value, 10) / 100;
                return heic2any({ blob: file, toType: type, quality: q }).then(function (result) {
                    var blob = Array.isArray(result) ? result[0] : result;
                    if (!blob) throw new Error('empty result');
                    var name = base(file.name) + '.' + ext;
                    // Avoid duplicate names (they collide in the ZIP and on download).
                    var unique = name, n = 2;
                    while (converted.some(function (c) { return c.name === unique; })) { unique = base(file.name) + ' (' + n + ').' + ext; n++; }
                    name = unique;
                    var url = URL.createObjectURL(blob);
                    converted.push({ name: name, blob: blob });
                    var li = document.createElement('li');
                    li.className = 'list-group-item d-flex justify-content-between align-items-center';
                    var span = document.createElement('span'); span.textContent = name + ' — ' + fmt(blob.size);
                    var a = document.createElement('a'); a.href = url; a.download = name; a.className = 'btn btn-sm btn-success'; a.textContent = 'Download';
                    li.appendChild(span); li.appendChild(a); resultList.appendChild(li);
                    if (converted.length > 1) zipBtn.classList.remove('d-none');
                }).catch(function (err) {
                    failedNames.push(file.name);
                }).then(function () {
                    done++; statusText.textContent = 'Converted ' + done + ' / ' + list.length + '.';
                });
            });
        });
        chain.then(function () {
            if (failedNames.length) {
                showError(failedNames.length + ' file(s) could not be converted: ' + failedNames.join(', ') + '. They may not be real HEIC photos, or the photo may be too large for this device — try one at a time on a computer.');
            }
        });
    }
    zipBtn.addEventListener('click', function () {
        if (typeof JSZip === 'undefined') { showError('ZIP library failed to load.'); return; }
        var zip = new JSZip();
        converted.forEach(function (item) { zip.file(item.name, item.blob); });
        zipBtn.disabled = true; zipBtn.textContent = 'Making ZIP…';
        zip.generateAsync({ type: 'blob' }).then(function (blob) {
            var u = URL.createObjectURL(blob); var a = document.createElement('a'); a.href = u; a.download = 'heic-converted.zip'; document.body.appendChild(a); a.click(); a.remove();
            zipBtn.disabled = false; zipBtn.textContent = 'Download All as ZIP';
            setTimeout(function () { URL.revokeObjectURL(u); }, 3000);
        });
    });
    dropZone.addEventListener('click', function () { fileInput.click(); });
    fileInput.addEventListener('change', function () { handleFiles(fileInput.files); fileInput.value = ''; });
    dropZone.addEventListener('dragover', function (e) { e.preventDefault(); });
    dropZone.addEventListener('drop', function (e) { e.preventDefault(); if (e.dataTransfer) handleFiles(e.dataTransfer.files); });
})();
</script>
@endsection
