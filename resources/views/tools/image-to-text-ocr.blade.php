@extends('layouts.app')

@section('title', 'Image to Text (OCR) Online Free - Extract Text from Image | Azlaan Tools')
@section('meta_description', 'Free Image to Text OCR tool: extract text from photos, screenshots and scanned images in English and Urdu. No signup, no upload - OCR runs in your browser.')

@section('content')
<div class="container py-4">
    <h1 class="mb-3">Image to Text (OCR)</h1>
    <p class="lead">Extract editable text from any image, photo or screenshot - free, fast and private. Supports English and Urdu text recognition right in your browser.</p>

    <div id="alertBox" class="alert alert-danger d-none" role="alert"></div>
    <div id="successBox" class="alert alert-success d-none" role="alert"></div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div id="dropZone" class="border border-2 border-dashed rounded-3 p-4 p-md-5 text-center mb-3" style="border-style: dashed !important; cursor: pointer; background: #f8f9fa;">
                <div class="fs-1 mb-2">&#128247;</div>
                <p class="mb-1 fw-semibold">Drag &amp; drop an image here</p>
                <p class="text-muted mb-3">or click to browse - JPG, PNG, WEBP, BMP supported</p>
                <button type="button" class="btn btn-primary">Select Image</button>
                <input type="file" id="fileInput" accept="image/*" class="d-none">
            </div>

            <div id="previewWrap" class="d-none mb-3 text-center">
                <img id="previewImg" class="img-fluid rounded border" style="max-height: 320px;" alt="Selected image preview">
                <p class="small text-muted mt-2 mb-0" id="fileInfo"></p>
            </div>

            <div class="row g-3 align-items-end mb-3">
                <div class="col-md-6">
                    <label for="langSelect" class="form-label fw-semibold">OCR Language</label>
                    <select id="langSelect" class="form-select">
                        <option value="eng" selected>English</option>
                        <option value="urd">Urdu</option>
                        <option value="eng+urd">English + Urdu</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <button type="button" id="runBtn" class="btn btn-success w-100" disabled>Extract Text</button>
                </div>
            </div>

            <div id="progressWrap" class="d-none mb-3">
                <div class="progress" style="height: 24px;">
                    <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%;">0%</div>
                </div>
                <p class="small text-muted mt-2 mb-0">Status: <span id="statusText">Waiting...</span></p>
            </div>

            <label for="resultText" class="form-label fw-semibold">Extracted Text</label>
            <textarea id="resultText" class="form-control" rows="10" placeholder="Extracted text will appear here..."></textarea>
            <div class="small text-muted mt-2">Characters: <strong id="charCount">0</strong> &nbsp; Words: <strong id="wordCount">0</strong></div>
            <div class="d-flex flex-wrap gap-2 mt-3">
                <button type="button" id="copyBtn" class="btn btn-primary">Copy Text</button>
                <button type="button" id="downloadBtn" class="btn btn-outline-success">Download .txt</button>
                <button type="button" id="clearBtn" class="btn btn-outline-danger">Clear</button>
            </div>
        </div>
    </div>

    <div class="alert alert-info">
        <strong>Privacy note:</strong> Your image never leaves your browser - OCR runs completely on your device, no file is uploaded to any server.
    </div>

    <h2>How to use</h2>
    <ol>
        <li>Click the box above or drag and drop an image (JPG, PNG, screenshot or scan) into it.</li>
        <li>Choose the OCR language: English, Urdu, or English + Urdu.</li>
        <li>Click <strong>Extract Text</strong> and wait while the progress bar completes - first use may take longer as the language data downloads.</li>
        <li>Review the extracted text, then click <strong>Copy Text</strong> or <strong>Download .txt</strong> to save it.</li>
    </ol>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/tesseract.js@5/dist/tesseract.min.js"></script>
<script>
(function () {
    var dropZone = document.getElementById('dropZone');
    var fileInput = document.getElementById('fileInput');
    var previewWrap = document.getElementById('previewWrap');
    var previewImg = document.getElementById('previewImg');
    var fileInfo = document.getElementById('fileInfo');
    var langSelect = document.getElementById('langSelect');
    var runBtn = document.getElementById('runBtn');
    var progressWrap = document.getElementById('progressWrap');
    var progressBar = document.getElementById('progressBar');
    var statusText = document.getElementById('statusText');
    var resultText = document.getElementById('resultText');
    var alertBox = document.getElementById('alertBox');
    var successBox = document.getElementById('successBox');
    var selectedFile = null;
    var objectUrl = null;

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
    function updateCounts() {
        var t = resultText.value;
        document.getElementById('charCount').textContent = t.length;
        document.getElementById('wordCount').textContent = t.trim() ? t.trim().split(/\s+/).length : 0;
    }
    function setFile(file) {
        hideAlerts();
        if (!file) return;
        if (file.type.indexOf('image/') !== 0) {
            showError('Please select a valid image file (JPG, PNG, WEBP or BMP).');
            return;
        }
        selectedFile = file;
        if (objectUrl) URL.revokeObjectURL(objectUrl);
        objectUrl = URL.createObjectURL(file);
        previewImg.src = objectUrl;
        fileInfo.textContent = file.name + ' (' + formatSize(file.size) + ')';
        previewWrap.classList.remove('d-none');
        runBtn.disabled = false;
    }

    dropZone.addEventListener('click', function () {
        fileInput.click();
    } );
    fileInput.addEventListener('change', function () {
        if (fileInput.files.length) setFile(fileInput.files[0]);
        fileInput.value = '';
    } );
    dropZone.addEventListener('dragover', function (e) {
        e.preventDefault();
        dropZone.style.background = '#e9f2ff';
    } );
    dropZone.addEventListener('dragleave', function () {
        dropZone.style.background = '#f8f9fa';
    } );
    dropZone.addEventListener('drop', function (e) {
        e.preventDefault();
        dropZone.style.background = '#f8f9fa';
        if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length) setFile(e.dataTransfer.files[0]);
    } );

    resultText.addEventListener('input', updateCounts);

    runBtn.addEventListener('click', async function () {
        hideAlerts();
        if (!selectedFile) {
            showError('Please select an image first.');
            return;
        }
        if (typeof Tesseract === 'undefined') {
            showError('OCR library failed to load. Please check your internet connection and refresh the page.');
            return;
        }
        runBtn.disabled = true;
        runBtn.textContent = 'Recognizing...';
        progressWrap.classList.remove('d-none');
        progressBar.style.width = '0%';
        progressBar.textContent = '0%';
        statusText.textContent = 'Starting OCR engine...';
        try {
            var lang = langSelect.value;
            var worker = await Tesseract.createWorker(lang, 1, {
                logger: function (m) {
                    if (m && m.status) {
                        statusText.textContent = m.status;
                    }
                    if (m && typeof m.progress === 'number') {
                        var pct = Math.round(m.progress * 100);
                        progressBar.style.width = pct + '%';
                        progressBar.textContent = pct + '%';
                    }
                }
            } );
            var result = await worker.recognize(selectedFile);
            await worker.terminate();
            var text = '';
            if (result && result.data && typeof result.data.text === 'string') {
                text = result.data.text;
            }
            resultText.value = text.trim();
            updateCounts();
            progressBar.style.width = '100%';
            progressBar.textContent = '100%';
            statusText.textContent = 'Done';
            if (text.trim()) {
                showSuccess('Text extracted successfully. You can copy it or download it as a .txt file.');
            } else {
                showError('No readable text was found in this image. Try a clearer, higher-resolution image.');
            }
        } catch (err) {
            console.error(err);
            showError('OCR failed for this image. Please try a clearer image or check your internet connection (language data downloads on first use).');
        } finally {
            runBtn.disabled = false;
            runBtn.textContent = 'Extract Text';
        }
    } );

    document.getElementById('copyBtn').addEventListener('click', async function () {
        if (!resultText.value) {
            showError('There is no text to copy yet.');
            return;
        }
        try {
            await navigator.clipboard.writeText(resultText.value);
            showSuccess('Copied to clipboard!');
        } catch (e) {
            resultText.select();
            document.execCommand('copy');
            showSuccess('Copied to clipboard!');
        }
    } );

    document.getElementById('downloadBtn').addEventListener('click', function () {
        if (!resultText.value) {
            showError('There is no text to download yet.');
            return;
        }
        var blob = new Blob([resultText.value], {
            type: 'text/plain;charset=utf-8'
        } );
        var url = URL.createObjectURL(blob);
        var a = document.createElement('a');
        a.href = url;
        a.download = 'extracted-text.txt';
        document.body.appendChild(a);
        a.click();
        a.remove();
        setTimeout(function () {
            URL.revokeObjectURL(url);
        }, 2000);
    } );

    document.getElementById('clearBtn').addEventListener('click', function () {
        selectedFile = null;
        resultText.value = '';
        previewWrap.classList.add('d-none');
        progressWrap.classList.add('d-none');
        runBtn.disabled = true;
        hideAlerts();
        updateCounts();
    } );

    updateCounts();
} )();
</script>
@endsection
