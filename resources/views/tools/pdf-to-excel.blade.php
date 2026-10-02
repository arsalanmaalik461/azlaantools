@extends('layouts.app')
@section('title', 'PDF to Excel Converter Online Free — Azlaan Tools')
@section('meta_description', 'Convert a PDF to an Excel spreadsheet (.xlsx) online for free — one worksheet per PDF page. No signup, no upload — files stay in your browser.')
@section('content')
<div class="container py-4 pdf-tool">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">PDF to Excel</h1>
            <p class="lead text-muted">Turn a text-based PDF into an Excel file (.xlsx) — each PDF page gets its own worksheet, so you can edit the data in Excel or Google Sheets.</p>
            <div class="alert alert-warning">
                <strong>Honest note:</strong> Works best on simple, table-like PDFs where text is in clean rows. Rows from multi-column layouts, scanned PDFs and complex tables may merge — you may need to check and fix the result in Excel. For scanned PDFs, use the Image to Text (OCR) tool first.
            </div>

            <div id="alertBox" class="alert alert-danger d-none" role="alert"></div>
            <div id="successBox" class="alert alert-success d-none" role="alert"></div>

            <div id="dropZone" class="drop-zone mb-3">
                <div class="fs-1 mb-2">📊</div>
                <p class="mb-1 fw-semibold">Drag &amp; drop a PDF here</p>
                <p class="text-muted mb-3">or click to browse from your device</p>
                <button type="button" class="btn btn-primary">Select PDF File</button>
                <input type="file" id="fileInput" accept="application/pdf,.pdf" class="d-none">
            </div>

            <div id="toolWrap" class="d-none">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <p class="mb-2">File: <strong id="fileName" class="text-break"></strong></p>
                        <p class="mb-3">Extracted: <span id="extractInfo" class="fw-semibold">—</span></p>
                        <label for="previewText" class="form-label fw-semibold">Preview (first rows from each page)</label>
                        <textarea id="previewText" class="form-control mb-3" rows="8" readonly></textarea>
                        <div class="d-flex flex-wrap gap-2">
                            <button type="button" id="processBtn" class="btn btn-success btn-lg">Convert &amp; Download Excel (.xlsx)</button>
                            <button type="button" id="clearBtn" class="btn btn-outline-secondary">Clear</button>
                        </div>
                        <div id="progressWrap" class="progress mt-3 d-none" style="height: 24px;">
                            <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%;">0%</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="alert alert-info mt-4">
                <strong>Privacy note:</strong> Your file never leaves your browser — everything runs on your phone or computer, nothing is uploaded.
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Click the box above or drag and drop your PDF into it — text rows will extract automatically.</li>
                <li>Check the preview. If the preview is empty, the PDF is probably scanned — use the OCR tool first.</li>
                <li>Click <strong>Convert &amp; Download Excel (.xlsx)</strong> — a file with a separate worksheet for each page will download.</li>
                <li>Open the file in Excel, Google Sheets or LibreOffice Calc and clean it as needed.</li>
            </ol>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/pdfjs-dist@3.11.174/build/pdf.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
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
    var extractInfo = document.getElementById('extractInfo');
    var previewText = document.getElementById('previewText');
    var processBtn = document.getElementById('processBtn');
    var clearBtn = document.getElementById('clearBtn');
    var progressWrap = document.getElementById('progressWrap');
    var progressBar = document.getElementById('progressBar');
    var storedName = 'document';
    var pageRows = [];

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
    function extractRows(textContent) {
        var linesMap = {};
        textContent.items.forEach(function (item) {
            if (!item.str || !item.str.trim()) return;
            if (!item.transform || item.transform.length < 6) return;
            var y = Math.round(item.transform[5]);
            var key = null;
            Object.keys(linesMap).forEach(function (k) {
                if (Math.abs(parseInt(k, 10) - y) <= 3) key = k;
            });
            if (key === null) {
                key = String(y);
                linesMap[key] = { y: y, parts: [] };
            }
            linesMap[key].parts.push({ x: item.transform[4], str: item.str });
        });
        var lines = Object.values(linesMap).sort(function (a, b) { return b.y - a.y; });
        var rows = [];
        lines.forEach(function (line) {
            line.parts.sort(function (a, b) { return a.x - b.x; });
            var text = line.parts.map(function (p) { return p.str; }).join(' ').replace(/\s+/g, ' ').trim();
            if (text) rows.push(text);
        });
        return rows;
    }

    async function loadFile(file) {
        hideAlerts();
        if (!file) return;
        if (!(file.type === 'application/pdf' || file.name.toLowerCase().endsWith('.pdf'))) {
            showError('Please select a valid PDF file.');
            return;
        }
        if (typeof pdfjsLib === 'undefined') {
            showError('PDF library failed to load. Please check your internet connection and try again.');
            return;
        }
        try {
            var buf = await file.arrayBuffer();
            var pdfDoc = await pdfjsLib.getDocument({ data: buf }).promise;
            storedName = file.name.replace(/\.pdf$/i, '') || 'document';
            pageRows = [];
            var totalRows = 0;
            var totalChars = 0;
            for (var n = 1; n <= pdfDoc.numPages; n++) {
                var page = await pdfDoc.getPage(n);
                var tc = await page.getTextContent();
                var rows = extractRows(tc);
                pageRows.push(rows);
                totalRows += rows.length;
                rows.forEach(function (r) { totalChars += r.length; });
            }
            fileNameEl.textContent = file.name + ' (' + formatSize(file.size) + ') — ' + pdfDoc.numPages + ' page(s)';
            extractInfo.textContent = totalRows.toLocaleString() + ' rows, ' + totalChars.toLocaleString() + ' characters';
            var previewLines = [];
            pageRows.forEach(function (rows, idx) {
                previewLines.push('--- Page ' + (idx + 1) + ' ---');
                rows.slice(0, 12).forEach(function (r) { previewLines.push(r); });
                if (rows.length > 12) previewLines.push('... (' + (rows.length - 12) + ' more rows)');
            });
            previewText.value = previewLines.join('\n');
            processBtn.disabled = totalRows === 0;
            toolWrap.classList.remove('d-none');
            if (totalRows === 0) {
                showError('No selectable text was found in this PDF. It is probably a scanned document — please use our Image to Text (OCR) tool first.');
            } else {
                showSuccess('Text extracted — ' + totalRows.toLocaleString() + ' rows found. Click convert to download the Excel file.');
            }
        } catch (err) {
            console.error(err);
            pageRows = [];
            toolWrap.classList.add('d-none');
            showError('Could not read this PDF. It may be corrupted or password-protected. Please try a different file.');
        }
    }

    dropZone.addEventListener('click', function () { fileInput.click(); });
    fileInput.addEventListener('change', function () {
        if (fileInput.files && fileInput.files[0]) loadFile(fileInput.files[0]);
        fileInput.value = '';
    });
    dropZone.addEventListener('dragover', function (e) { e.preventDefault(); dropZone.classList.add('dragover'); });
    dropZone.addEventListener('dragleave', function () { dropZone.classList.remove('dragover'); });
    dropZone.addEventListener('drop', function (e) {
        e.preventDefault();
        dropZone.classList.remove('dragover');
        if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files[0]) loadFile(e.dataTransfer.files[0]);
    });
    clearBtn.addEventListener('click', function () {
        pageRows = [];
        previewText.value = '';
        toolWrap.classList.add('d-none');
        hideAlerts();
    });

    processBtn.addEventListener('click', function () {
        hideAlerts();
        if (!pageRows.length) { showError('Please select a PDF file first.'); return; }
        if (typeof XLSX === 'undefined') { showError('Excel library failed to load. Please check your internet connection and try again.'); return; }
        processBtn.disabled = true;
        processBtn.textContent = 'Converting...';
        progressWrap.classList.remove('d-none');
        progressBar.style.width = '30%';
        progressBar.textContent = '30%';
        try {
            var wb = XLSX.utils.book_new();
            pageRows.forEach(function (rows, idx) {
                var aoa = rows.map(function (r) { return [r]; });
                if (aoa.length === 0) aoa = [['']];
                var ws = XLSX.utils.aoa_to_sheet(aoa);
                ws['!cols'] = [{ wch: 100 }];
                var sheetName = 'Page ' + (idx + 1);
                XLSX.utils.book_append_sheet(wb, ws, sheetName);
                var pct = 30 + Math.round(((idx + 1) / pageRows.length) * 60);
                progressBar.style.width = pct + '%';
                progressBar.textContent = pct + '%';
            });
            progressBar.style.width = '100%';
            progressBar.textContent = '100%';
            XLSX.writeFile(wb, storedName + '.xlsx');
            showSuccess('Done! Your Excel file with ' + pageRows.length + ' worksheet(s) has been downloaded. Check the rows of each page — some rows in complex tables may be merged.');
        } catch (err) {
            console.error(err);
            showError('Could not create the Excel file. Please try again with a different PDF.');
        } finally {
            processBtn.disabled = false;
            processBtn.textContent = 'Convert & Download Excel (.xlsx)';
            setTimeout(function () { progressWrap.classList.add('d-none'); progressBar.style.width = '0%'; progressBar.textContent = '0%'; }, 800);
        }
    });
})();
</script>
@endsection
