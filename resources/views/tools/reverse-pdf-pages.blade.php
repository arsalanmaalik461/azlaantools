@extends('layouts.app')
@section('title', 'Reverse PDF Pages - Free Online | Azlaan Tools')
@section('meta_description', 'Flip your PDF page order online for free - last page comes first. Fix reversed page order, no signup, files stay in your browser.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Reverse PDF Pages</h1>
            <p class="lead text-muted">Flip the page order of your PDF so the last page becomes first — for scanned documents saved in the wrong order. Fix reversed order, free.</p>

            <div class="alert alert-danger d-none" id="alertBox" role="alert"></div>
            <div class="alert alert-success d-none" id="successBox" role="alert"></div>

            <div id="dropZone" class="border border-2 border-dashed rounded-3 p-4 p-md-5 text-center mb-3" style="border-style: dashed !important; cursor: pointer; background: #f8f9fa;">
                <div class="fs-1 mb-2">🔄</div>
                <p class="mb-1 fw-semibold">Drag &amp; drop a PDF here</p>
                <p class="text-muted mb-3">or click to select from your device</p>
                <button type="button" class="btn btn-primary">Select PDF File</button>
                <input type="file" id="fileInput" accept="application/pdf,.pdf" class="d-none">
            </div>

            <div id="optionsWrap" class="d-none">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <p class="mb-3">File: <strong id="fileName" class="text-break"></strong> — <span id="pageCountInfo"></span></p>
                        <p class="text-muted small mb-3">Page order now: <span id="orderPreview" class="fw-semibold text-dark"></span><br>
                        Page order after reversing: <span id="orderReversed" class="fw-semibold text-dark"></span></p>
                        <div class="d-flex flex-wrap gap-2">
                            <button type="button" id="processBtn" class="btn btn-success btn-lg">Reverse Pages &amp; Download</button>
                            <button type="button" id="clearBtn" class="btn btn-outline-secondary">Clear</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="alert alert-info mt-4">
                <strong>Privacy note:</strong> Your file is never uploaded to any server — everything happens in your browser.
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Click the box above or drag and drop your PDF into it.</li>
                <li>Check the page order preview — it shows how the order will change.</li>
                <li>Click <strong>Reverse Pages &amp; Download</strong>.</li>
                <li>Your reversed PDF downloads automatically with "-reversed" added to its name.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/pdf-lib@1.17.1/dist/pdf-lib.min.js"></script>
<script>
(function () {
    'use strict';
    var dropZone = document.getElementById('dropZone');
    var fileInput = document.getElementById('fileInput');
    var alertBox = document.getElementById('alertBox');
    var successBox = document.getElementById('successBox');
    var optionsWrap = document.getElementById('optionsWrap');
    var fileNameEl = document.getElementById('fileName');
    var pageCountInfo = document.getElementById('pageCountInfo');
    var orderPreview = document.getElementById('orderPreview');
    var orderReversed = document.getElementById('orderReversed');
    var processBtn = document.getElementById('processBtn');
    var clearBtn = document.getElementById('clearBtn');
    var storedBuffer = null;
    var storedName = 'document';
    var totalPages = 0;

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
    function orderString(n, reversed) {
        var nums = [];
        var i;
        if (n <= 8) {
            for (i = 1; i <= n; i++) { nums.push(i); }
            if (reversed) { nums.reverse(); }
            return nums.join(' → ');
        }
        var first = [], last = [];
        for (i = 1; i <= 4; i++) { first.push(i); }
        for (i = n - 3; i <= n; i++) { last.push(i); }
        if (reversed) {
            var f2 = first.slice().reverse(), l2 = last.slice().reverse();
            return l2.join(' → ') + ' → … → ' + f2.join(' → ');
        }
        return first.join(' → ') + ' → … → ' + last.join(' → ');
    }
    async function loadFile(file) {
        hideAlerts();
        if (!file) { return; }
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
            var doc = await PDFLib.PDFDocument.load(buf.slice(0), { ignoreEncryption: false });
            storedBuffer = buf;
            storedName = file.name.replace(/\.pdf$/i, '') || 'document';
            totalPages = doc.getPageCount();
            fileNameEl.textContent = file.name + ' (' + formatSize(file.size) + ')';
            pageCountInfo.textContent = totalPages + (totalPages === 1 ? ' page' : ' pages');
            orderPreview.textContent = orderString(totalPages, false);
            orderReversed.textContent = orderString(totalPages, true);
            optionsWrap.classList.remove('d-none');
            if (totalPages < 2) {
                showError('This PDF has only 1 page — there is nothing to reverse.');
                optionsWrap.classList.add('d-none');
                storedBuffer = null;
                return;
            }
            showSuccess('PDF loaded successfully — ' + totalPages + ' page(s) found.');
        } catch (err) {
            storedBuffer = null;
            totalPages = 0;
            optionsWrap.classList.add('d-none');
            showError('Could not read this PDF. It may be corrupted or password-protected. Please try a different file.');
        }
    }

    dropZone.addEventListener('click', function () { fileInput.click(); });
    fileInput.addEventListener('change', function () {
        if (fileInput.files && fileInput.files[0]) { loadFile(fileInput.files[0]); }
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
    clearBtn.addEventListener('click', function () {
        storedBuffer = null;
        totalPages = 0;
        optionsWrap.classList.add('d-none');
        hideAlerts();
    });

    processBtn.addEventListener('click', async function () {
        hideAlerts();
        if (!storedBuffer || totalPages < 2) {
            showError('Please select a PDF file with at least 2 pages first.');
            return;
        }
        if (typeof PDFLib === 'undefined') {
            showError('PDF library failed to load. Please check your internet connection and try again.');
            return;
        }
        processBtn.disabled = true;
        processBtn.textContent = 'Processing...';
        try {
            var srcDoc = await PDFLib.PDFDocument.load(storedBuffer.slice(0));
            var newDoc = await PDFLib.PDFDocument.create();
            var indices = [];
            for (var i = totalPages - 1; i >= 0; i--) { indices.push(i); }
            var copied = await newDoc.copyPages(srcDoc, indices);
            copied.forEach(function (p) { newDoc.addPage(p); });
            var outBytes = await newDoc.save();
            var blob = new Blob([outBytes], { type: 'application/pdf' });
            var url = URL.createObjectURL(blob);
            var a = document.createElement('a');
            a.href = url;
            a.download = storedName + '-reversed.pdf';
            document.body.appendChild(a);
            a.click();
            a.remove();
            setTimeout(function () { URL.revokeObjectURL(url); }, 4000);
            showSuccess('Done! Page order reversed — old page ' + totalPages + ' is now page 1. Your file (' + formatSize(blob.size) + ') has been downloaded.');
        } catch (err) {
            showError('Could not process this PDF. It may be corrupted or password-protected. Please try a different file.');
        } finally {
            processBtn.disabled = false;
            processBtn.textContent = 'Reverse Pages & Download';
        }
    });
})();
</script>
@endsection
