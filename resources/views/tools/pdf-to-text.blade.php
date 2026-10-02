@extends('layouts.app')

@section('title', 'PDF to Text Online Free - Extract Text from PDF | Azlaan Tools')
@section('meta_description', 'Free PDF to Text tool: extract text from any PDF page by page in your browser. No signup, no upload - copy or download the text instantly.')

@section('content')
<div class="container py-4 pdf-tool">
    <h1 class="mb-3">PDF to Text</h1>
    <p class="lead">Extract all readable text from a PDF file, page by page - free and private. Perfect for copying content out of documents, forms and e-books.</p>

    <div id="alertBox" class="alert alert-danger d-none" role="alert"></div>
    <div id="successBox" class="alert alert-success d-none" role="alert"></div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div id="dropZone" class="border border-2 border-dashed rounded-3 p-4 p-md-5 text-center mb-3" style="border-style: dashed !important; cursor: pointer; background: #f8f9fa;">
                <div class="fs-1 mb-2">&#128196;</div>
                <p class="mb-1 fw-semibold">Drag &amp; drop a PDF here</p>
                <p class="text-muted mb-3">or click to browse from your device</p>
                <button type="button" class="btn btn-primary">Select PDF File</button>
                <input type="file" id="fileInput" accept="application/pdf,.pdf" class="d-none">
            </div>

            <p class="small text-muted mb-3" id="fileInfo">No file selected yet.</p>

            <div id="progressWrap" class="d-none mb-3">
                <div class="progress" style="height: 24px;">
                    <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%;">0%</div>
                </div>
                <p class="small text-muted mt-2 mb-0" id="statusText">Waiting...</p>
            </div>

            <label for="resultText" class="form-label fw-semibold">Extracted Text</label>
            <textarea id="resultText" class="form-control" rows="12" placeholder="Extracted PDF text will appear here, separated by page..."></textarea>
            <div class="small text-muted mt-2">
                Pages: <strong id="pageCount">0</strong> &nbsp; Characters: <strong id="charCount">0</strong> &nbsp; Words: <strong id="wordCount">0</strong>
            </div>
            <div class="d-flex flex-wrap gap-2 mt-3">
                <button type="button" id="extractBtn" class="btn btn-success" disabled>Extract Text</button>
                <button type="button" id="copyBtn" class="btn btn-primary">Copy Text</button>
                <button type="button" id="downloadBtn" class="btn btn-outline-success">Download .txt</button>
                <button type="button" id="clearBtn" class="btn btn-outline-danger">Clear</button>
            </div>
        </div>
    </div>

    <div class="alert alert-info">
        <strong>Privacy note:</strong> Your PDF never leaves your browser - extraction happens completely on your device, no file is uploaded to any server. Note: scanned-image PDFs with no text layer will return empty text; use the Image to Text (OCR) tool for those.
    </div>

    <h2>How to use</h2>
    <ol>
        <li>Click the box above or drag and drop a PDF file into it.</li>
        <li>Click <strong>Extract Text</strong> and wait while each page is processed.</li>
        <li>Read the result - every page is separated with a --- Page N --- marker and the total page count is shown.</li>
        <li>Click <strong>Copy Text</strong> or <strong>Download .txt</strong> to save the extracted text.</li>
    </ol>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/pdfjs-dist@3.11.174/build/pdf.min.js"></script>
<script>
(function () {
    var dropZone = document.getElementById('dropZone');
    var fileInput = document.getElementById('fileInput');
    var fileInfo = document.getElementById('fileInfo');
    var extractBtn = document.getElementById('extractBtn');
    var progressWrap = document.getElementById('progressWrap');
    var progressBar = document.getElementById('progressBar');
    var statusText = document.getElementById('statusText');
    var resultText = document.getElementById('resultText');
    var alertBox = document.getElementById('alertBox');
    var successBox = document.getElementById('successBox');
    var selectedFile = null;

    if (typeof pdfjsLib !== 'undefined') {
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdn.jsdelivr.net/npm/pdfjs-dist@3.11.174/build/pdf.worker.min.js';
    }

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
    function updateCounts(pages) {
        var t = resultText.value;
        document.getElementById('charCount').textContent = t.length;
        document.getElementById('wordCount').textContent = t.trim() ? t.trim().split(/\s+/).length : 0;
        if (typeof pages === 'number') {
            document.getElementById('pageCount').textContent = pages;
        }
    }
    function setFile(file) {
        hideAlerts();
        if (!file) return;
        if (!(file.type === 'application/pdf' || file.name.toLowerCase().endsWith('.pdf'))) {
            showError('Please select a valid PDF file.');
            return;
        }
        selectedFile = file;
        fileInfo.textContent = 'Selected: ' + file.name + ' (' + formatSize(file.size) + ')';
        extractBtn.disabled = false;
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

    resultText.addEventListener('input', function () {
        updateCounts();
    } );

    extractBtn.addEventListener('click', async function () {
        hideAlerts();
        if (!selectedFile) {
            showError('Please select a PDF file first.');
            return;
        }
        if (typeof pdfjsLib === 'undefined') {
            showError('PDF library failed to load. Please check your internet connection and refresh the page.');
            return;
        }
        extractBtn.disabled = true;
        extractBtn.textContent = 'Extracting...';
        progressWrap.classList.remove('d-none');
        progressBar.style.width = '0%';
        progressBar.textContent = '0%';
        statusText.textContent = 'Reading PDF...';
        resultText.value = '';
        updateCounts(0);
        try {
            var buffer = await selectedFile.arrayBuffer();
            var loadingTask = pdfjsLib.getDocument({
                data: buffer
            } );
            var pdf = await loadingTask.promise;
            var total = pdf.numPages;
            updateCounts(total);
            var parts = [];
            for (var p = 1; p <= total; p++) {
                var page = await pdf.getPage(p);
                var content = await page.getTextContent();
                var lines = [];
                var lastY = null;
                content.items.forEach(function (item) {
                    var y = null;
                    if (item.transform && item.transform.length >= 6) {
                        y = item.transform[5];
                    }
                    if (lastY !== null && y !== null && Math.abs(y - lastY) > 2) {
                        lines.push('\n');
                    }
                    lines.push(item.str);
                    if (item.hasEOL) {
                        lines.push('\n');
                    } else {
                        lines.push(' ');
                    }
                    lastY = y;
                } );
                var pageText = lines.join('').replace(/[ \t]+\n/g, '\n').replace(/\n{3,}/g, '\n\n').trim();
                parts.push('--- Page ' + p + ' ---\n' + pageText);
                var pct = Math.round((p / total) * 100);
                progressBar.style.width = pct + '%';
                progressBar.textContent = pct + '%';
                statusText.textContent = 'Processed page ' + p + ' of ' + total;
            }
            resultText.value = parts.join('\n\n');
            updateCounts(total);
            if (resultText.value.replace(/--- Page \d+ ---/g, '').trim()) {
                showSuccess('Done! Extracted text from ' + total + ' page(s).');
            } else {
                showError('No text layer was found in this PDF. It may be a scanned image PDF - try the Image to Text (OCR) tool instead.');
            }
        } catch (err) {
            console.error(err);
            showError('Could not read this PDF. The file may be corrupted or password-protected. Please try another file.');
        } finally {
            extractBtn.disabled = false;
            extractBtn.textContent = 'Extract Text';
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
        a.download = 'pdf-text.txt';
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
        fileInfo.textContent = 'No file selected yet.';
        progressWrap.classList.add('d-none');
        extractBtn.disabled = true;
        hideAlerts();
        updateCounts(0);
    } );
} )();
</script>
@endsection
