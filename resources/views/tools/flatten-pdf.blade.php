@extends('layouts.app')
@section('title', 'Flatten PDF — Flatten Form Fields Online Free — Azlaan Tools')
@section('meta_description', 'Flatten a PDF online for free: turn fillable form fields into permanent page content so they cannot be edited. No signup, no upload — files stay in your browser.')
@section('content')
<div class="container py-4 pdf-tool">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Flatten PDF</h1>
            <p class="lead text-muted">Make fillable form fields permanent — after flattening, the filled-in text becomes part of the page and cannot be edited again.</p>

            <div id="alertBox" class="alert alert-danger d-none" role="alert"></div>
            <div id="successBox" class="alert alert-success d-none" role="alert"></div>

            <div id="dropZone" class="border border-2 border-dashed rounded-3 p-4 p-md-5 text-center mb-3" style="border-style: dashed !important; cursor: pointer; background: #f8f9fa;">
                <div class="fs-1 mb-2">📄</div>
                <p class="mb-1 fw-semibold">Drag &amp; drop a PDF here</p>
                <p class="text-muted mb-3">or click to browse from your device</p>
                <button type="button" class="btn btn-primary">Select PDF File</button>
                <input type="file" id="fileInput" accept="application/pdf,.pdf" class="d-none">
            </div>

            <div id="toolWrap" class="d-none">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <p class="mb-2">File: <strong id="fileName" class="text-break"></strong></p>
                        <p class="mb-3">Form fields: <span id="fieldInfo" class="fw-semibold">—</span></p>
                        <div class="d-flex flex-wrap gap-2">
                            <button type="button" id="processBtn" class="btn btn-success btn-lg">Flatten &amp; Download PDF</button>
                            <button type="button" id="clearBtn" class="btn btn-outline-secondary">Clear</button>
                        </div>
                        <div id="progressWrap" class="progress mt-3 d-none" style="height: 24px;">
                            <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%;">0%</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="alert alert-info mt-4">
                <strong>Privacy note:</strong> Your file never leaves your browser — all work happens on your phone/computer, nothing is uploaded.
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Click the box above or drag and drop your filled PDF form into it — the field count will appear.</li>
                <li>Click <strong>Flatten &amp; Download PDF</strong>.</li>
                <li>In the downloaded copy, the fields will be permanent. If the PDF has no form fields at all, the tool will give you a clean re-saved (normalized) copy and will clearly tell you so.</li>
            </ol>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/pdf-lib@1.17.1/dist/pdf-lib.min.js"></script>
<script>
(function () {
    var dropZone = document.getElementById('dropZone');
    var fileInput = document.getElementById('fileInput');
    var alertBox = document.getElementById('alertBox');
    var successBox = document.getElementById('successBox');
    var toolWrap = document.getElementById('toolWrap');
    var fileNameEl = document.getElementById('fileName');
    var fieldInfo = document.getElementById('fieldInfo');
    var processBtn = document.getElementById('processBtn');
    var clearBtn = document.getElementById('clearBtn');
    var progressWrap = document.getElementById('progressWrap');
    var progressBar = document.getElementById('progressBar');
    var storedBuffer = null;
    var storedName = 'document';
    var storedFieldCount = 0;

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
    function downloadBlob(blob, filename) {
        var url = URL.createObjectURL(blob);
        var a = document.createElement('a');
        a.href = url;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        a.remove();
        setTimeout(function () { URL.revokeObjectURL(url); }, 4000);
    }
    function setProgress(pct) {
        progressBar.style.width = pct + '%';
        progressBar.textContent = pct + '%';
    }

    async function loadFile(file) {
        hideAlerts();
        if (!file) return;
        if (!(file.type === 'application/pdf' || file.name.toLowerCase().endsWith('.pdf'))) {
            showError('Please select a valid PDF file.');
            return;
        }
        if (typeof PDFLib === 'undefined') {
            showError('PDF library failed to load. Please check your internet connection and try again.');
            return;
        }
        try {
            var buf = await file.arrayBuffer();
            var doc = await PDFLib.PDFDocument.load(buf.slice(0), { ignoreEncryption: true });
            storedBuffer = buf;
            storedName = file.name.replace(/\.pdf$/i, '') || 'document';
            storedFieldCount = 0;
            try {
                var form = doc.getForm();
                storedFieldCount = form.getFields().length;
            } catch (e) {
                storedFieldCount = 0;
            }
            fileNameEl.textContent = file.name + ' (' + formatSize(file.size) + ') — ' + doc.getPageCount() + ' page(s)';
            fieldInfo.textContent = storedFieldCount > 0 ? storedFieldCount + ' fillable field(s) found — ready to flatten.' : 'No form fields found in this PDF.';
            toolWrap.classList.remove('d-none');
            if (storedFieldCount > 0) {
                showSuccess('PDF loaded with ' + storedFieldCount + ' form field(s). Click Flatten to make them permanent.');
            } else {
                showSuccess('PDF loaded. It has no form fields — pressing Flatten will download a clean normalized copy.');
            }
        } catch (err) {
            console.error(err);
            storedBuffer = null;
            toolWrap.classList.add('d-none');
            showError('Could not read this PDF. It may be corrupted or password-protected. Please try a different file.');
        }
    }

    dropZone.addEventListener('click', function () { fileInput.click(); });
    fileInput.addEventListener('change', function () {
        if (fileInput.files && fileInput.files[0]) loadFile(fileInput.files[0]);
        fileInput.value = '';
    });
    dropZone.addEventListener('dragover', function (e) { e.preventDefault(); dropZone.style.background = '#e9f2ff'; });
    dropZone.addEventListener('dragleave', function () { dropZone.style.background = '#f8f9fa'; });
    dropZone.addEventListener('drop', function (e) {
        e.preventDefault();
        dropZone.style.background = '#f8f9fa';
        if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files[0]) loadFile(e.dataTransfer.files[0]);
    });
    clearBtn.addEventListener('click', function () {
        storedBuffer = null;
        storedFieldCount = 0;
        fieldInfo.textContent = '—';
        toolWrap.classList.add('d-none');
        progressWrap.classList.add('d-none');
        setProgress(0);
        hideAlerts();
    });

    processBtn.addEventListener('click', async function () {
        hideAlerts();
        if (!storedBuffer) { showError('Please select a PDF file first.'); return; }
        processBtn.disabled = true;
        processBtn.textContent = 'Flattening...';
        progressWrap.classList.remove('d-none');
        setProgress(30);
        try {
            var doc = await PDFLib.PDFDocument.load(storedBuffer.slice(0), { ignoreEncryption: true });
            var flattenedCount = 0;
            var form = null;
            try { form = doc.getForm(); } catch (e) { form = null; }
            if (form) {
                var fields = form.getFields();
                flattenedCount = fields.length;
                if (flattenedCount > 0) {
                    try {
                        form.flatten();
                    } catch (fe) {
                        console.error(fe);
                        showError('Form fields could not be flattened for this PDF. Some fields (for example signatures or special types) do not support flattening.');
                        return;
                    }
                }
            }
            setProgress(70);
            var bytes = await doc.save();
            setProgress(100);
            var blob = new Blob([bytes], { type: 'application/pdf' });
            downloadBlob(blob, storedName + '-flattened.pdf');
            if (flattenedCount > 0) {
                showSuccess('Done! ' + flattenedCount + ' form field(s) flattened. Your flattened PDF (' + formatSize(blob.size) + ') has been downloaded — fields are now permanent and cannot be edited.');
            } else {
                showSuccess('Done! This PDF had no form fields, so nothing was flattened — but a clean normalized copy (' + formatSize(blob.size) + ') has been downloaded.');
            }
        } catch (err) {
            console.error(err);
            showError('Could not flatten this PDF. It may be corrupted or protected. Please try a different file.');
        } finally {
            processBtn.disabled = false;
            processBtn.textContent = 'Flatten & Download PDF';
            setTimeout(function () { progressWrap.classList.add('d-none'); setProgress(0); }, 800);
        }
    });
})();
</script>
@endsection
