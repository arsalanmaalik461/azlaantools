@extends('layouts.app')

@section('title', 'PNG to JPG Converter - Azlaan Tools')
@section('meta_description', 'Convert PNG images to JPG format online for free, with quality control and background color option.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">PNG to JPG Converter</h1>
            <p class="lead text-muted">Convert PNG images to JPG — smaller file size, easier to share on websites and WhatsApp. Set the quality yourself. Everything happens in your browser, the file is never uploaded.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="fileInput" class="form-label fw-semibold">Select PNG images</label>
                        <input type="file" class="form-control" id="fileInput" accept="image/png" multiple>
                        <p class="form-text">You can select many files at once.</p>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="qualityRange" class="form-label fw-semibold d-flex justify-content-between">
                                <span>JPG Quality</span>
                                <span><strong id="qualityValue">90</strong>%</span>
                            </label>
                            <input type="range" class="form-range" id="qualityRange" min="10" max="100" step="1" value="90">
                        </div>
                        <div class="col-md-6">
                            <label for="bgColor" class="form-label fw-semibold">Background color</label>
                            <input type="color" class="form-control form-control-color w-100" id="bgColor" value="#ffffff">
                            <p class="form-text">JPG does not support PNG transparency — empty areas will be filled with this color.</p>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100 mt-3" id="goBtn">Convert to JPG</button>
                    <button type="button" class="btn btn-outline-secondary w-100 mt-2 d-none" id="downloadAllBtn">Download All</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <h5>Converted files</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm align-middle">
                                <thead><tr><th>Preview</th><th>File</th><th>Original</th><th>JPG size</th><th></th></tr></thead>
                                <tbody id="resultRows"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li><strong>Select PNG images</strong> — one or many, all will be converted.</li>
                <li><strong>Set the quality</strong> — 85–95 is best for photos, lower quality = smaller file.</li>
                <li><strong>Choose the background color</strong> — the transparent area of the PNG will be filled with this color (usually white).</li>
                <li>Press <strong>Convert to JPG</strong>, then download each file separately or take them all at once with <strong>Download All</strong>.</li>
            </ol>

            <h2 class="mt-4">PNG vs JPG</h2>
            <p class="text-muted">PNG is lossless (full quality) but has a big file size; JPG is compressed and can be 5–10 times smaller for photos. PNG is better for graphics or logos with text, JPG is better for photos.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var fileInput = document.getElementById('fileInput');
    var qualityRange = document.getElementById('qualityRange');
    var qualityValue = document.getElementById('qualityValue');
    var bgColor = document.getElementById('bgColor');
    var goBtn = document.getElementById('goBtn');
    var downloadAllBtn = document.getElementById('downloadAllBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var resultRows = document.getElementById('resultRows');

    var converted = [];

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function formatBytes(bytes) {
        if (bytes === 0) return '0 B';
        var units = ['B', 'KB', 'MB', 'GB'];
        var i = Math.floor(Math.log(bytes) / Math.log(1024));
        if (i >= units.length) i = units.length - 1;
        return (bytes / Math.pow(1024, i)).toFixed(i === 0 ? 0 : 1) + ' ' + units[i];
    }
    function loadImage(file) {
        return new Promise(function (resolve, reject) {
            var reader = new FileReader();
            reader.onload = function (e) {
                var img = new Image();
                img.onload = function () { resolve(img); };
                img.onerror = function () { reject(new Error('load failed')); };
                img.src = e.target.result;
            };
            reader.onerror = function () { reject(new Error('read failed')); };
            reader.readAsDataURL(file);
        });
    }
    function triggerDownload(url, name) {
        var a = document.createElement('a');
        a.href = url;
        a.download = name;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    }

    qualityRange.addEventListener('input', function () {
        qualityValue.textContent = qualityRange.value;
    });

    goBtn.addEventListener('click', function () {
        hideError();
        var files = fileInput.files;
        if (!files || files.length === 0) { showError('Please select a PNG file first.'); return; }
        var quality = parseInt(qualityRange.value, 10) / 100;
        var bg = bgColor.value;
        converted = [];
        resultRows.innerHTML = '';
        goBtn.disabled = true;
        goBtn.textContent = 'Converting...';

        var queue = [];
        for (var f = 0; f < files.length; f++) {
            if (files[f].type === 'image/png') queue.push(files[f]);
        }
        if (queue.length === 0) {
            goBtn.disabled = false; goBtn.textContent = 'Convert to JPG';
            showError('No PNG file found. Only .png files are converted.');
            return;
        }

        var chain = Promise.resolve();
        queue.forEach(function (file) {
            chain = chain.then(function () {
                return loadImage(file).then(function (img) {
                    var canvas = document.createElement('canvas');
                    canvas.width = img.width;
                    canvas.height = img.height;
                    var ctx = canvas.getContext('2d');
                    ctx.fillStyle = bg;
                    ctx.fillRect(0, 0, canvas.width, canvas.height);
                    ctx.drawImage(img, 0, 0);
                    var url = canvas.toDataURL('image/jpeg', quality);
                    var jpgSize = Math.round(url.length * 0.75);
                    var outName = file.name.replace(/\.png$/i, '') + '.jpg';
                    converted.push({ url: url, name: outName });

                    var tr = document.createElement('tr');
                    var tdPrev = document.createElement('td');
                    var thumb = document.createElement('img');
                    thumb.src = url;
                    thumb.style.maxWidth = '80px';
                    thumb.style.maxHeight = '50px';
                    thumb.className = 'img-thumbnail';
                    tdPrev.appendChild(thumb);
                    var tdName = document.createElement('td');
                    tdName.textContent = outName;
                    var tdOrig = document.createElement('td');
                    tdOrig.textContent = formatBytes(file.size);
                    var tdNew = document.createElement('td');
                    tdNew.textContent = formatBytes(jpgSize);
                    var tdBtn = document.createElement('td');
                    var btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'btn btn-sm btn-success';
                    btn.textContent = 'Download';
                    (function (u, n) {
                        btn.addEventListener('click', function () { triggerDownload(u, n); });
                    })(url, outName);
                    tdBtn.appendChild(btn);
                    tr.appendChild(tdPrev); tr.appendChild(tdName);
                    tr.appendChild(tdOrig); tr.appendChild(tdNew); tr.appendChild(tdBtn);
                    resultRows.appendChild(tr);
                }).catch(function () { /* skip unreadable file */ });
            });
        });
        chain.then(function () {
            goBtn.disabled = false;
            goBtn.textContent = 'Convert to JPG';
            if (converted.length === 0) {
                showError('No file could be converted. Please try another PNG file.');
                return;
            }
            results.classList.remove('d-none');
            downloadAllBtn.classList.toggle('d-none', converted.length < 2);
        });
    });

    downloadAllBtn.addEventListener('click', function () {
        converted.forEach(function (item, idx) {
            setTimeout(function () { triggerDownload(item.url, item.name); }, idx * 600);
        });
    });
})();
</script>
@endsection
