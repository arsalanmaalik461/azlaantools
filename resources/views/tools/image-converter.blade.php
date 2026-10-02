@extends('layouts.app')
@section('title', 'Image Converter - Convert JPG, PNG & WebP Online Free | Azlaan Tools')
@section('meta_description', 'Convert images between JPG, PNG and WebP online for free, one or many at a time. Adjust quality, preview sizes and download instantly. 100% private - files never leave your browser.')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <h1 class="mb-2">Image Converter</h1>
            <p class="lead text-muted">Convert one image or a whole batch between JPG, PNG and WebP. Pick the output format, set the quality, and download each file — or all of them with one click.</p>

            <div class="alert alert-success d-flex align-items-start" role="alert">
                <span class="me-2" aria-hidden="true">&#128274;</span>
                <div><strong>Private by design:</strong> Your files never leave your browser. Conversion happens on your own device using the Canvas API — nothing is uploaded, stored or shared.</div>
            </div>

            <div id="dropZone" class="border border-2 border-primary rounded-3 p-4 p-md-5 text-center bg-light mb-4" style="border-style: dashed !important; cursor: pointer;">
                <div class="fs-1 mb-2" aria-hidden="true">&#128444;</div>
                <p class="mb-1 fw-semibold">Drag &amp; drop one or more images here, or click to browse</p>
                <p class="text-muted small mb-0">Supports JPG, PNG, WebP and other browser-readable images</p>
                <input type="file" id="fileInput" class="d-none" accept="image/*" multiple>
            </div>

            <div id="errorBox" class="alert alert-danger d-none" role="alert"></div>

            <div id="selectedCard" class="card mb-4 d-none">
                <div class="card-header fw-semibold d-flex justify-content-between align-items-center">
                    <span>Selected Images (<span id="selectedCount">0</span>)</span>
                    <button type="button" id="clearBtn" class="btn btn-sm btn-outline-danger">Clear All</button>
                </div>
                <ul id="selectedList" class="list-group list-group-flush"></ul>
            </div>

            <div class="card mb-4">
                <div class="card-header fw-semibold">Conversion Settings</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="formatSelect" class="form-label">Convert to</label>
                            <select class="form-select" id="formatSelect">
                                <option value="image/jpeg" selected>JPG — best for photos, small size</option>
                                <option value="image/png">PNG — lossless, keeps transparency</option>
                                <option value="image/webp">WebP — modern, small size with quality</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="qualityRange" class="form-label d-flex justify-content-between">
                                <span>Quality (JPG / WebP)</span>
                                <span><strong id="qualityValue">90</strong>%</span>
                            </label>
                            <input type="range" class="form-range" id="qualityRange" min="10" max="100" step="1" value="90">
                            <p class="form-text mb-0" id="qualityHint">Quality applies to JPG and WebP output. PNG ignores this setting.</p>
                        </div>
                    </div>
                    <div class="d-grid mt-3">
                        <button type="button" id="convertBtn" class="btn btn-primary btn-lg" disabled>Convert Images</button>
                    </div>
                    <p class="form-text mb-0 mt-2">Note: JPG does not support transparency, so transparent areas become white. PNG and WebP keep transparency.</p>
                </div>
            </div>

            <div id="resultsCard" class="card d-none">
                <div class="card-header fw-semibold d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <span>Converted Images (<span id="resultsCount">0</span>)</span>
                    <button type="button" id="downloadAllBtn" class="btn btn-sm btn-success">Download All</button>
                </div>
                <ul id="resultsList" class="list-group list-group-flush"></ul>
                <div class="card-footer text-muted small">“Download All” saves the files one after another. Your browser may ask permission to download multiple files — please allow it.</div>
            </div>

            <h2 class="mt-5">How to use</h2>
            <ol>
                <li>Drag and drop one or more images into the box above, or click the box to select files from your device.</li>
                <li>Review the selected list — you can clear it and start again at any time.</li>
                <li>Choose the output format: JPG, PNG or WebP, and set the Quality slider if you picked JPG or WebP.</li>
                <li>Click <strong>Convert Images</strong> and wait a moment while each file is converted in your browser.</li>
                <li>Download files one by one from the results list, or click <strong>Download All</strong> to save everything.</li>
            </ol>

            <h2 class="mt-4">Which format should I choose?</h2>
            <ul class="text-muted">
                <li><strong>JPG:</strong> Best for photos and sharing on WhatsApp. Small files, but no transparency and slight quality loss.</li>
                <li><strong>PNG:</strong> Best for logos, screenshots and images with transparency or sharp text. Lossless, but files are larger.</li>
                <li><strong>WebP:</strong> Modern format for websites — similar quality to JPG at a notably smaller size, and it supports transparency.</li>
            </ul>
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
    var selectedCard = document.getElementById('selectedCard');
    var selectedCount = document.getElementById('selectedCount');
    var selectedList = document.getElementById('selectedList');
    var clearBtn = document.getElementById('clearBtn');
    var formatSelect = document.getElementById('formatSelect');
    var qualityRange = document.getElementById('qualityRange');
    var qualityValue = document.getElementById('qualityValue');
    var convertBtn = document.getElementById('convertBtn');
    var resultsCard = document.getElementById('resultsCard');
    var resultsCount = document.getElementById('resultsCount');
    var resultsList = document.getElementById('resultsList');
    var downloadAllBtn = document.getElementById('downloadAllBtn');

    var selectedFiles = [];
    var resultUrls = [];

    function formatBytes(bytes) {
        if (bytes === 0) return '0 B';
        var units = ['B', 'KB', 'MB', 'GB'];
        var i = Math.floor(Math.log(bytes) / Math.log(1024));
        if (i >= units.length) i = units.length - 1;
        var value = bytes / Math.pow(1024, i);
        return value.toFixed(i === 0 ? 0 : 2) + ' ' + units[i];
    }

    function showError(message) {
        errorBox.textContent = message;
        errorBox.classList.remove('d-none');
    }

    function hideError() {
        errorBox.textContent = '';
        errorBox.classList.add('d-none');
    }

    function baseName(name) {
        var dot = name.lastIndexOf('.');
        return dot > 0 ? name.substring(0, dot) : name;
    }

    function extensionForType(type) {
        if (type === 'image/png') return 'png';
        if (type === 'image/webp') return 'webp';
        return 'jpg';
    }

    var thumbUrls = [];
    function renderSelected() {
        // Revoke the previous thumbnails' object URLs before rebuilding the
        // list, otherwise every re-render leaks them for the page's lifetime.
        thumbUrls.forEach(function (u) { URL.revokeObjectURL(u); });
        thumbUrls = [];
        selectedList.innerHTML = '';
        selectedCount.textContent = selectedFiles.length;
        if (selectedFiles.length === 0) {
            selectedCard.classList.add('d-none');
            convertBtn.disabled = true;
            return;
        }
        selectedCard.classList.remove('d-none');
        convertBtn.disabled = false;
        convertBtn.textContent = 'Convert ' + selectedFiles.length + (selectedFiles.length === 1 ? ' Image' : ' Images');
        for (var i = 0; i < selectedFiles.length; i++) {
            (function (file) {
                var li = document.createElement('li');
                li.className = 'list-group-item d-flex align-items-center gap-3';
                var thumb = document.createElement('img');
                thumb.alt = '';
                thumb.style.width = '48px';
                thumb.style.height = '48px';
                thumb.style.objectFit = 'cover';
                thumb.className = 'rounded';
                thumb.src = URL.createObjectURL(file);
                thumbUrls.push(thumb.src);
                var info = document.createElement('div');
                info.className = 'flex-grow-1 text-truncate';
                var nameEl = document.createElement('div');
                nameEl.className = 'fw-semibold text-truncate';
                nameEl.textContent = file.name;
                var sizeEl = document.createElement('div');
                sizeEl.className = 'text-muted small';
                sizeEl.textContent = formatBytes(file.size);
                info.appendChild(nameEl);
                info.appendChild(sizeEl);
                li.appendChild(thumb);
                li.appendChild(info);
                selectedList.appendChild(li);
            })(selectedFiles[i]);
        }
    }

    function addFiles(fileList) {
        hideError();
        var added = 0;
        var skipped = 0;
        for (var i = 0; i < fileList.length; i++) {
            if (fileList[i].type.indexOf('image/') === 0) {
                selectedFiles.push(fileList[i]);
                added++;
            } else {
                skipped++;
            }
        }
        if (skipped > 0) {
            showError(skipped + (skipped === 1 ? ' file was' : ' files were') + ' skipped because they are not images.');
        }
        if (added > 0) renderSelected();
    }

    function convertOne(file, outputType, quality) {
        return new Promise(function (resolve) {
            var objectUrl = URL.createObjectURL(file);
            var img = new Image();
            img.onload = function () {
                var canvas = document.createElement('canvas');
                canvas.width = img.naturalWidth;
                canvas.height = img.naturalHeight;
                var ctx = canvas.getContext('2d');
                if (outputType === 'image/jpeg') {
                    ctx.fillStyle = '#ffffff';
                    ctx.fillRect(0, 0, canvas.width, canvas.height);
                }
                ctx.drawImage(img, 0, 0);
                URL.revokeObjectURL(objectUrl);
                canvas.toBlob(function (blob) {
                    resolve(blob);
                }, outputType, quality);
            };
            img.onerror = function () {
                URL.revokeObjectURL(objectUrl);
                resolve(null);
            };
            img.src = objectUrl;
        });
    }

    function clearResults() {
        for (var i = 0; i < resultUrls.length; i++) URL.revokeObjectURL(resultUrls[i]);
        resultUrls = [];
        resultsList.innerHTML = '';
        resultsCount.textContent = '0';
        resultsCard.classList.add('d-none');
    }

    function convertAll() {
        if (selectedFiles.length === 0) return;
        hideError();
        clearResults();
        var outputType = formatSelect.value;
        var quality = parseInt(qualityRange.value, 10) / 100;
        var ext = extensionForType(outputType);
        convertBtn.disabled = true;
        convertBtn.textContent = 'Converting...';
        var chain = Promise.resolve();
        var done = 0;
        var failed = 0;
        selectedFiles.forEach(function (file) {
            chain = chain.then(function () {
                return convertOne(file, outputType, quality).then(function (blob) {
                    if (!blob) {
                        failed++;
                        return;
                    }
                    var url = URL.createObjectURL(blob);
                    resultUrls.push(url);
                    var outName = baseName(file.name) + '.' + ext;
                    var li = document.createElement('li');
                    li.className = 'list-group-item d-flex flex-wrap align-items-center gap-3';
                    var thumb = document.createElement('img');
                    thumb.alt = '';
                    thumb.style.width = '56px';
                    thumb.style.height = '56px';
                    thumb.style.objectFit = 'cover';
                    thumb.className = 'rounded';
                    thumb.src = url;
                    var info = document.createElement('div');
                    info.className = 'flex-grow-1';
                    info.style.minWidth = '160px';
                    var nameEl = document.createElement('div');
                    nameEl.className = 'fw-semibold text-truncate';
                    nameEl.textContent = outName;
                    var sizeEl = document.createElement('div');
                    sizeEl.className = 'text-muted small';
                    var change = '';
                    if (blob.size < file.size) {
                        change = ' — ' + Math.round((1 - blob.size / file.size) * 100) + '% smaller';
                    } else if (blob.size > file.size) {
                        change = ' — ' + Math.round((blob.size / file.size - 1) * 100) + '% larger';
                    }
                    sizeEl.textContent = formatBytes(file.size) + ' → ' + formatBytes(blob.size) + change;
                    info.appendChild(nameEl);
                    info.appendChild(sizeEl);
                    var link = document.createElement('a');
                    link.href = url;
                    link.setAttribute('download', outName);
                    link.className = 'btn btn-success btn-sm result-download';
                    link.textContent = 'Download';
                    li.appendChild(thumb);
                    li.appendChild(info);
                    li.appendChild(link);
                    resultsList.appendChild(li);
                    done++;
                });
            });
        });
        chain.then(function () {
            convertBtn.disabled = false;
            convertBtn.textContent = 'Convert ' + selectedFiles.length + (selectedFiles.length === 1 ? ' Image' : ' Images');
            if (done > 0) {
                resultsCount.textContent = done;
                resultsCard.classList.remove('d-none');
                resultsCard.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
            if (failed > 0) {
                showError(failed + (failed === 1 ? ' image' : ' images') + ' could not be converted. The rest are ready below.');
            }
        });
    }

    dropZone.addEventListener('click', function () { fileInput.click(); });
    fileInput.addEventListener('change', function () {
        if (fileInput.files && fileInput.files.length > 0) addFiles(fileInput.files);
        fileInput.value = '';
    });
    dropZone.addEventListener('dragover', function (e) {
        e.preventDefault();
        dropZone.classList.add('bg-primary-subtle');
    });
    dropZone.addEventListener('dragleave', function () {
        dropZone.classList.remove('bg-primary-subtle');
    });
    dropZone.addEventListener('drop', function (e) {
        e.preventDefault();
        dropZone.classList.remove('bg-primary-subtle');
        if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length > 0) {
            addFiles(e.dataTransfer.files);
        }
    });

    qualityRange.addEventListener('input', function () {
        qualityValue.textContent = qualityRange.value;
    });
    convertBtn.addEventListener('click', convertAll);
    clearBtn.addEventListener('click', function () {
        selectedFiles = [];
        renderSelected();
        clearResults();
        hideError();
    });
    downloadAllBtn.addEventListener('click', function () {
        var links = resultsList.querySelectorAll('.result-download');
        for (var i = 0; i < links.length; i++) {
            (function (link, delay) {
                setTimeout(function () { link.click(); }, delay);
            })(links[i], i * 700);
        }
    });
})();
</script>
@endsection
