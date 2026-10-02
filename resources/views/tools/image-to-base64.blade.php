@extends('layouts.app')
@section('title', 'Image to Base64 Converter - Free Online | Azlaan Tools')
@section('meta_description', 'Convert any image to Base64 and Data URL online for free. Get the full Data URL, raw Base64 string, file info and preview instantly. No signup, no upload — runs in your browser.')
@section('content')
<div class="container py-4">
    <h1 class="mb-3">Image to Base64</h1>
    <p class="lead">Convert an image into a Base64 string or a ready-to-use Data URL for CSS, HTML and code — free, instant and private.</p>

    <div id="alertBox" class="alert alert-danger d-none" role="alert"></div>
    <div id="successBox" class="alert alert-success d-none" role="alert"></div>

    <div id="dropZone" class="border border-2 border-dashed rounded-3 p-4 p-md-5 text-center mb-3" style="border-style: dashed !important; cursor: pointer; background: #f8f9fa;">
        <div class="fs-1 mb-2">🖼️</div>
        <p class="mb-1 fw-semibold">Drag &amp; drop an image here</p>
        <p class="text-muted mb-3">or click to browse from your device (PNG, JPG, WebP, GIF, SVG)</p>
        <button type="button" class="btn btn-primary">Select Image</button>
        <input type="file" id="fileInput" accept="image/*" class="d-none">
    </div>

    <div id="resultWrap" class="d-none">
        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body text-center">
                        <h2 class="h6">Preview</h2>
                        <img id="previewImg" alt="Uploaded image preview" class="img-fluid rounded border" style="max-height: 220px; object-fit: contain;">
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h2 class="h6 mb-3">File Information</h2>
                        <table class="table table-sm mb-0">
                            <tbody>
                                <tr><th style="width: 40%;">File name</th><td id="infoName" class="text-break">—</td></tr>
                                <tr><th>File size</th><td id="infoSize">—</td></tr>
                                <tr><th>Dimensions</th><td id="infoDims">—</td></tr>
                                <tr><th>Format / MIME type</th><td id="infoFormat">—</td></tr>
                                <tr><th>Base64 output size</th><td id="infoB64Size">—</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <label for="dataUrlOut" class="form-label fw-semibold">Full Data URL (ready for HTML / CSS)</label>
                <textarea id="dataUrlOut" class="form-control font-monospace" rows="4" readonly></textarea>
                <div class="d-flex flex-wrap gap-2 mt-2">
                    <button type="button" id="copyDataUrlBtn" class="btn btn-primary btn-sm">Copy Data URL</button>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <label for="rawB64Out" class="form-label fw-semibold">Raw Base64 String</label>
                <textarea id="rawB64Out" class="form-control font-monospace" rows="4" readonly></textarea>
                <div class="d-flex flex-wrap gap-2 mt-2">
                    <button type="button" id="copyRawBtn" class="btn btn-primary btn-sm">Copy Base64</button>
                    <button type="button" id="downloadBtn" class="btn btn-outline-success btn-sm">Download .txt</button>
                    <button type="button" id="clearBtn" class="btn btn-outline-secondary btn-sm">Clear</button>
                </div>
            </div>
        </div>
    </div>

    <div class="alert alert-info mt-4">
        <strong>Privacy note:</strong> Your files never leave your browser — all work happens on your phone/computer, nothing is uploaded.
    </div>

    <h2>How to use</h2>
    <ol>
        <li>Click the box above or drag and drop an image into it.</li>
        <li>See the preview and file information — name, size, dimensions and format.</li>
        <li>Copy the full Data URL for use in HTML or CSS, or copy just the raw Base64 string.</li>
        <li>Use <strong>Download .txt</strong> to save the Base64 string as a text file.</li>
    </ol>
</div>
@endsection
@section('scripts')
<script>
(function () {
    var dropZone = document.getElementById('dropZone');
    var fileInput = document.getElementById('fileInput');
    var alertBox = document.getElementById('alertBox');
    var successBox = document.getElementById('successBox');
    var resultWrap = document.getElementById('resultWrap');
    var previewImg = document.getElementById('previewImg');
    var infoName = document.getElementById('infoName');
    var infoSize = document.getElementById('infoSize');
    var infoDims = document.getElementById('infoDims');
    var infoFormat = document.getElementById('infoFormat');
    var infoB64Size = document.getElementById('infoB64Size');
    var dataUrlOut = document.getElementById('dataUrlOut');
    var rawB64Out = document.getElementById('rawB64Out');
    var currentName = 'image';

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
    function formatSize(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
    }
    function formatChars(n) {
        return n.toLocaleString('en-US') + ' characters (' + formatSize(n) + ')';
    }
    async function copyText(text, btn) {
        if (!text) {
            showError('Nothing to copy yet — select an image first.');
            return;
        }
        try {
            await navigator.clipboard.writeText(text);
        } catch (err) {
            var ta = document.createElement('textarea');
            ta.value = text;
            document.body.appendChild(ta);
            ta.select();
            document.execCommand('copy');
            ta.remove();
        }
        if (btn) {
            var old = btn.textContent;
            btn.textContent = 'Copied!';
            setTimeout(function () { btn.textContent = old; }, 1500);
        }
    }
    function loadFile(file) {
        hideAlerts();
        if (!file) {
            return;
        }
        if (file.type && file.type.indexOf('image/') !== 0) {
            showError('Please select a valid image file.');
            return;
        }
        currentName = file.name.replace(/\.[^.]+$/, '') || 'image';
        var reader = new FileReader();
        reader.onload = function () {
            var dataUrl = String(reader.result || '');
            var commaIdx = dataUrl.indexOf(',');
            var raw = commaIdx !== -1 ? dataUrl.substring(commaIdx + 1) : dataUrl;
            dataUrlOut.value = dataUrl;
            rawB64Out.value = raw;
            previewImg.src = dataUrl;
            infoName.textContent = file.name;
            infoSize.textContent = formatSize(file.size);
            infoFormat.textContent = file.type || 'unknown';
            infoB64Size.textContent = formatChars(raw.length) + ' — Data URL: ' + formatChars(dataUrl.length);
            infoDims.textContent = 'Loading...';
            resultWrap.classList.remove('d-none');
            var img = new Image();
            img.onload = function () {
                infoDims.textContent = img.naturalWidth + ' x ' + img.naturalHeight + ' px';
            };
            img.onerror = function () {
                infoDims.textContent = 'Could not read (vector or unsupported image)';
            };
            img.src = dataUrl;
            showSuccess('Image converted to Base64 successfully.');
        };
        reader.onerror = function () {
            showError('Could not read this file. Please try a different image.');
        };
        reader.readAsDataURL(file);
    }

    dropZone.addEventListener('click', function () { fileInput.click(); });
    fileInput.addEventListener('change', function () {
        if (fileInput.files && fileInput.files[0]) {
            loadFile(fileInput.files[0]);
        }
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
            loadFile(e.dataTransfer.files[0]);
        }
    });

    document.getElementById('copyDataUrlBtn').addEventListener('click', function () {
        copyText(dataUrlOut.value, this);
    });
    document.getElementById('copyRawBtn').addEventListener('click', function () {
        copyText(rawB64Out.value, this);
    });
    document.getElementById('downloadBtn').addEventListener('click', function () {
        if (!rawB64Out.value) {
            showError('Nothing to download yet — select an image first.');
            return;
        }
        var blob = new Blob([rawB64Out.value], { type: 'text/plain;charset=utf-8' });
        var url = URL.createObjectURL(blob);
        var a = document.createElement('a');
        a.href = url;
        a.download = currentName + '-base64.txt';
        document.body.appendChild(a);
        a.click();
        a.remove();
        setTimeout(function () { URL.revokeObjectURL(url); }, 2000);
    });
    document.getElementById('clearBtn').addEventListener('click', function () {
        dataUrlOut.value = '';
        rawB64Out.value = '';
        previewImg.removeAttribute('src');
        resultWrap.classList.add('d-none');
        hideAlerts();
    });
})();
</script>
@endsection
