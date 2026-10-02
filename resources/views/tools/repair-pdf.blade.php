@extends('layouts.app')
@section('title', 'Repair PDF — Fix Damaged PDF Online Free — Azlaan Tools')
@section('meta_description', 'Repair a damaged PDF online for free: re-save a clean copy that fixes many structural errors. No signup, no upload — files stay in your browser.')
@section('content')
<div class="container py-4 pdf-tool">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Repair PDF</h1>
            <p class="lead text-muted">Try to fix a damaged or corrupt PDF — a clean re-save of the file fixes many structural errors.</p>

            <div class="alert alert-warning">
                <strong>Honest note:</strong> This tool can fix many structurally damaged files (broken cross-reference tables, minor corruption, files that refuse to open). But if the file is badly damaged or its content is missing, no tool can bring it back — severely corrupted files cannot be fully recovered.
            </div>

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
                        <p class="mb-2">Diagnosis: <span id="repairInfo" class="fw-semibold">—</span></p>
                        <label for="reportText" class="form-label fw-semibold">Detailed report</label>
                        <textarea id="reportText" class="form-control mb-3" rows="5" readonly></textarea>
                        <div class="d-flex flex-wrap gap-2">
                            <button type="button" id="processBtn" class="btn btn-success btn-lg">Repair &amp; Download PDF</button>
                            <button type="button" id="clearBtn" class="btn btn-outline-secondary">Clear</button>
                        </div>
                        <div id="progressWrap" class="progress mt-3 d-none" style="height: 24px;">
                            <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%;">0%</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="alert alert-info mt-4">
                <strong>Privacy note:</strong> Your file never leaves your browser — everything happens on your phone or computer, nothing is uploaded.
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Click the box above or drag and drop your damaged PDF into it — the diagnosis will show right away what seems to be wrong.</li>
                <li>Click <strong>Repair &amp; Download PDF</strong>.</li>
                <li>The repaired copy will download. Open it in your usual PDF reader and check that all pages look right.</li>
            </ol>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/pdfjs-dist@3.11.174/build/pdf.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/pdf-lib@1.17.1/dist/pdf-lib.min.js"></script>
<script>
(function () {
    if (typeof pdfjsLib !== 'undefined') {
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdn.jsdelivr.net/npm/pdfjs-dist@3.11.174/build/pdf.worker.min.js';
    }
    var dropZone = document.getElementById('dropZone');
    var fileInput = document.getElementById('fileInput');
    var alertBox = document.getElementById('alertBox');
    var successBox = document.getElementById('successBox');
    var toolWrap = document.getElementById('toolWrap');
    var fileNameEl = document.getElementById('fileName');
    var repairInfo = document.getElementById('repairInfo');
    var reportText = document.getElementById('reportText');
    var processBtn = document.getElementById('processBtn');
    var clearBtn = document.getElementById('clearBtn');
    var progressWrap = document.getElementById('progressWrap');
    var progressBar = document.getElementById('progressBar');
    var storedBuffer = null;
    var storedName = 'document';
    var canRepair = false;

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

    async function diagnose(buf) {
        var lines = [];
        var normalOk = false;
        var ignoreOk = false;
        var pageCount = 0;
        var normalError = '';
        try {
            var d1 = await PDFLib.PDFDocument.load(buf.slice(0));
            normalOk = true;
            pageCount = d1.getPageCount();
            lines.push('pdf-lib (normal load): OK — ' + pageCount + ' page(s) found.');
        } catch (e1) {
            normalError = (e1 && e1.message) ? e1.message : String(e1);
            lines.push('pdf-lib (normal load): FAILED — ' + normalError);
            try {
                var d2 = await PDFLib.PDFDocument.load(buf.slice(0), { ignoreEncryption: true });
                ignoreOk = true;
                pageCount = d2.getPageCount();
                lines.push('pdf-lib (ignoreEncryption retry): OK — ' + pageCount + ' page(s) found. File is encrypted / restricted, or its normal structure is damaged.');
            } catch (e2) {
                lines.push('pdf-lib (ignoreEncryption retry): FAILED — ' + ((e2 && e2.message) ? e2.message : String(e2)));
            }
        }
        var jsOk = false;
        var jsPages = 0;
        try {
            var jsDoc = await pdfjsLib.getDocument({ data: buf.slice(0) }).promise;
            jsOk = true;
            jsPages = jsDoc.numPages;
            lines.push('pdf.js (second opinion): OK — ' + jsPages + ' page(s) found.');
        } catch (e3) {
            lines.push('pdf.js (second opinion): FAILED — ' + ((e3 && e3.message) ? e3.message : String(e3)));
        }
        var summary = '';
        canRepair = normalOk || ignoreOk;
        if (normalOk) {
            summary = pageCount === 0 ? 'File loads but has no pages.' : 'File structure looks readable — a clean re-save may fix minor issues.';
        } else if (ignoreOk) {
            summary = 'Normal parsing failed (possible encryption or structural damage), but a repair re-save looks possible.';
        } else if (jsOk) {
            summary = 'pdf.js can read it (' + jsPages + ' pages) but pdf-lib cannot rebuild it — a full repair here may not be possible.';
            canRepair = false;
        } else {
            summary = 'Could not parse this file at all — it appears severely corrupted or it is not a valid PDF.';
            canRepair = false;
        }
        if (canRepair && pageCount === 0) {
            summary = 'File has no pages — there is no content to repair.';
            canRepair = false;
        }
        return { summary: summary, report: lines.join('\n') };
    }

    async function loadFile(file) {
        hideAlerts();
        if (!file) return;
        if (!(file.type === 'application/pdf' || file.name.toLowerCase().endsWith('.pdf'))) {
            showError('Please select a valid PDF file.');
            return;
        }
        if (typeof PDFLib === 'undefined' || typeof pdfjsLib === 'undefined') {
            showError('PDF libraries failed to load. Please check your internet connection and try again.');
            return;
        }
        try {
            var buf = await file.arrayBuffer();
            storedBuffer = buf;
            storedName = file.name.replace(/\.pdf$/i, '') || 'document';
            var result = await diagnose(buf);
            fileNameEl.textContent = file.name + ' (' + formatSize(file.size) + ')';
            repairInfo.textContent = result.summary;
            reportText.value = result.report;
            processBtn.disabled = !canRepair;
            toolWrap.classList.remove('d-none');
            if (canRepair) {
                showSuccess('Diagnosis complete. Click Repair to download a clean re-saved copy.');
            } else {
                showError('Diagnosis: ' + result.summary);
            }
        } catch (err) {
            console.error(err);
            storedBuffer = null;
            toolWrap.classList.add('d-none');
            showError('Could not read this file at all. Please try a different file.');
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
        canRepair = false;
        reportText.value = '';
        repairInfo.textContent = '—';
        toolWrap.classList.add('d-none');
        progressWrap.classList.add('d-none');
        setProgress(0);
        hideAlerts();
    });

    processBtn.addEventListener('click', async function () {
        hideAlerts();
        if (!storedBuffer) { showError('Please select a PDF file first.'); return; }
        if (!canRepair) { showError('This file could not be parsed, so it cannot be repaired here. Severely corrupted files cannot be recovered.'); return; }
        processBtn.disabled = true;
        processBtn.textContent = 'Repairing...';
        progressWrap.classList.remove('d-none');
        setProgress(30);
        try {
            var doc = null;
            try {
                doc = await PDFLib.PDFDocument.load(storedBuffer.slice(0));
            } catch (e) {
                doc = await PDFLib.PDFDocument.load(storedBuffer.slice(0), { ignoreEncryption: true });
            }
            setProgress(70);
            var bytes = await doc.save();
            setProgress(100);
            var blob = new Blob([bytes], { type: 'application/pdf' });
            downloadBlob(blob, storedName + '-repaired.pdf');
            showSuccess('Done! Repaired PDF (' + formatSize(blob.size) + ', ' + doc.getPageCount() + ' page(s)) has been downloaded. Please open it and check all pages carefully.');
        } catch (err) {
            console.error(err);
            showError('Repair failed. The file appears too severely corrupted to rebuild. No tool can recover content that is no longer in the file.');
        } finally {
            processBtn.disabled = false;
            processBtn.textContent = 'Repair & Download PDF';
            setTimeout(function () { progressWrap.classList.add('d-none'); setProgress(0); }, 800);
        }
    });
})();
</script>
@endsection
