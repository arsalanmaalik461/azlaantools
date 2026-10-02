@extends('layouts.app')
@section('title', 'Bates Numbering PDF - Free Online | Azlaan Tools')
@section('meta_description', 'Add sequential legal bates numbers to every page of a PDF online for free — no signup, files stay in your browser.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Bates Numbering PDF</h1>
            <p class="lead text-muted">Add sequential legal bates numbers (like ACME-000001) to every page of your PDF — ideal for litigation and official documents, free.</p>

            <div class="alert alert-danger d-none" id="alertBox" role="alert"></div>
            <div class="alert alert-success d-none" id="successBox" role="alert"></div>

            <div id="dropZone" class="border border-2 border-dashed rounded-3 p-4 p-md-5 text-center mb-3" style="border-style: dashed !important; cursor: pointer; background: #f8f9fa;">
                <div class="fs-1 mb-2">⚖️</div>
                <p class="mb-1 fw-semibold">Drag &amp; drop a PDF here</p>
                <p class="text-muted mb-3">or click to select from your device</p>
                <button type="button" class="btn btn-primary">Select PDF File</button>
                <input type="file" id="fileInput" accept="application/pdf,.pdf" class="d-none">
            </div>

            <div id="optionsWrap" class="d-none">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <p class="mb-3">File: <strong id="fileName" class="text-break"></strong> — <span id="pageCountInfo"></span></p>
                        <div class="alert alert-info py-2">Sample stamp: <strong id="sampleStamp">ACME-000001</strong></div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="prefixInput" class="form-label fw-semibold">Prefix</label>
                                <input type="text" id="prefixInput" class="form-control" value="ACME" maxlength="20" placeholder="e.g. ACME">
                                <div class="form-text">The first word (English letters).</div>
                            </div>
                            <div class="col-md-4">
                                <label for="sepInput" class="form-label fw-semibold">Separator</label>
                                <input type="text" id="sepInput" class="form-control" value="-" maxlength="3" placeholder="-">
                            </div>
                            <div class="col-md-4">
                                <label for="startInput" class="form-label fw-semibold">Start Number</label>
                                <input type="number" id="startInput" class="form-control" value="1" min="0" max="999999" step="1">
                            </div>
                            <div class="col-md-4">
                                <label for="digitsInput" class="form-label fw-semibold">Number Digits</label>
                                <input type="number" id="digitsInput" class="form-control" value="6" min="1" max="12" step="1">
                                <div class="form-text">e.g. 6 = 000001</div>
                            </div>
                            <div class="col-md-4">
                                <label for="positionSel" class="form-label fw-semibold">Position</label>
                                <select id="positionSel" class="form-select">
                                    <option value="bottom-right" selected>Bottom Right</option>
                                    <option value="bottom-left">Bottom Left</option>
                                    <option value="top-right">Top Right</option>
                                    <option value="top-left">Top Left</option>
                                    <option value="bottom-center">Bottom Center</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="fontSizeInput" class="form-label fw-semibold">Font Size</label>
                                <input type="number" id="fontSizeInput" class="form-control" value="10" min="6" max="48" step="1">
                            </div>
                        </div>
                        <div class="d-flex flex-wrap gap-2 mt-4">
                            <button type="button" id="processBtn" class="btn btn-success btn-lg">Add Bates Numbers &amp; Download</button>
                            <button type="button" id="clearBtn" class="btn btn-outline-secondary">Clear</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="alert alert-info mt-4">
                <strong>Privacy note:</strong> Your file is never uploaded to a server — everything happens in your browser.
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Click the box above or drag and drop your PDF into it.</li>
                <li>Set the prefix (e.g. your case or company code), starting number, digits and stamp position.</li>
                <li>Click <strong>Add Bates Numbers &amp; Download</strong>.</li>
                <li>Your bates-stamped PDF downloads automatically with "-bates" added to its name.</li>
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
    var sampleStamp = document.getElementById('sampleStamp');
    var prefixInput = document.getElementById('prefixInput');
    var sepInput = document.getElementById('sepInput');
    var startInput = document.getElementById('startInput');
    var digitsInput = document.getElementById('digitsInput');
    var positionSel = document.getElementById('positionSel');
    var fontSizeInput = document.getElementById('fontSizeInput');
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
    function padNumber(n, digits) {
        var s = String(Math.max(0, Math.floor(n)));
        while (s.length < digits) { s = '0' + s; }
        return s;
    }
    function buildStamp(num) {
        var prefix = prefixInput.value.trim();
        var sep = sepInput.value;
        var digits = parseInt(digitsInput.value, 10);
        if (isNaN(digits) || digits < 1) digits = 6;
        if (digits > 12) digits = 12;
        var numPart = padNumber(num, digits);
        if (prefix) { return prefix + sep + numPart; }
        return numPart;
    }
    function updateSample() {
        var startNum = parseInt(startInput.value, 10);
        if (isNaN(startNum) || startNum < 0) startNum = 1;
        sampleStamp.textContent = buildStamp(startNum);
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
            optionsWrap.classList.remove('d-none');
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
    [prefixInput, sepInput, startInput, digitsInput].forEach(function (el) {
        el.addEventListener('input', updateSample);
    });
    updateSample();

    processBtn.addEventListener('click', async function () {
        hideAlerts();
        if (!storedBuffer) {
            showError('Please select a PDF file first.');
            return;
        }
        if (typeof PDFLib === 'undefined') {
            showError('PDF library failed to load. Please check your internet connection and try again.');
            return;
        }
        var fontSize = parseFloat(fontSizeInput.value);
        if (isNaN(fontSize) || fontSize < 6 || fontSize > 48) {
            showError('Please enter a font size between 6 and 48.');
            return;
        }
        var startNum = parseInt(startInput.value, 10);
        if (isNaN(startNum) || startNum < 0 || startNum > 999999) {
            showError('Please enter a valid start number (0 to 999999).');
            return;
        }
        processBtn.disabled = true;
        processBtn.textContent = 'Processing...';
        try {
            var pdfDoc = await PDFLib.PDFDocument.load(storedBuffer.slice(0));
            var font = await pdfDoc.embedFont(PDFLib.StandardFonts.Courier);
            var pages = pdfDoc.getPages();
            var total = pages.length;
            var margin = 36;
            pages.forEach(function (page, idx) {
                var size = page.getSize();
                var stamp = buildStamp(startNum + idx);
                var textWidth = font.widthOfTextAtSize(stamp, fontSize);
                var x, y;
                var pos = positionSel.value;
                if (pos === 'bottom-left') { x = margin; y = margin - fontSize; }
                else if (pos === 'bottom-right') { x = size.width - margin - textWidth; y = margin - fontSize; }
                else if (pos === 'top-right') { x = size.width - margin - textWidth; y = size.height - margin; }
                else if (pos === 'top-left') { x = margin; y = size.height - margin; }
                else { x = (size.width - textWidth) / 2; y = margin - fontSize; }
                if (y < 12) { y = 12; }
                if (x < 8) { x = 8; }
                page.drawText(stamp, {
                    x: x,
                    y: y,
                    size: fontSize,
                    font: font,
                    color: PDFLib.rgb(0.1, 0.1, 0.1)
                });
            });
            var outBytes = await pdfDoc.save();
            var blob = new Blob([outBytes], { type: 'application/pdf' });
            var url = URL.createObjectURL(blob);
            var a = document.createElement('a');
            a.href = url;
            a.download = storedName + '-bates.pdf';
            document.body.appendChild(a);
            a.click();
            a.remove();
            setTimeout(function () { URL.revokeObjectURL(url); }, 4000);
            showSuccess('Done! Bates numbers added to ' + total + ' page(s) — from ' + buildStamp(startNum) + ' to ' + buildStamp(startNum + total - 1) + '. Your file (' + formatSize(blob.size) + ') has been downloaded.');
        } catch (err) {
            showError('Could not process this PDF. It may be corrupted or password-protected. Please try a different file.');
        } finally {
            processBtn.disabled = false;
            processBtn.textContent = 'Add Bates Numbers & Download';
        }
    });
})();
</script>
@endsection
