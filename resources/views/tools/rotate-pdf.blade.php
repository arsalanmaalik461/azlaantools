@extends('layouts.app')
@section('title', 'Rotate PDF Pages - Rotate PDF Online Free | Azlaan Tools')
@section('meta_description', 'Rotate PDF pages online for free by 90, 180 or 270 degrees — rotate all pages or only a chosen page range, then download. No signup, no upload — files stay in your browser.')
@section('content')
<div class="container py-4 pdf-tool">
    <h1 class="mb-3">Rotate PDF Pages</h1>
    <p class="lead">Fix sideways or upside-down PDF pages in seconds — rotate all pages at once or only the pages you choose. Free and private.</p>

    <div id="alertBox" class="alert alert-danger d-none" role="alert"></div>
    <div id="successBox" class="alert alert-success d-none" role="alert"></div>

    <div id="dropZone" class="border border-2 border-dashed rounded-3 p-4 p-md-5 text-center mb-3" style="border-style: dashed !important; cursor: pointer; background: #f8f9fa;">
        <div class="fs-1 mb-2">🔄</div>
        <p class="mb-1 fw-semibold">Drag &amp; drop a PDF here</p>
        <p class="text-muted mb-3">or click to browse from your device</p>
        <button type="button" class="btn btn-primary">Select PDF File</button>
        <input type="file" id="fileInput" accept="application/pdf,.pdf" class="d-none">
    </div>

    <div id="optionsWrap" class="d-none">
        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <p class="mb-3">File: <strong id="fileName" class="text-break"></strong> — <span id="pageCountInfo"></span></p>
                <div class="mb-3">
                    <span class="form-label fw-semibold d-block mb-2">What to rotate</span>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="rotateMode" id="modeAll" value="all" checked>
                        <label class="form-check-label" for="modeAll">Rotate ALL pages</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="rotateMode" id="modeRange" value="range">
                        <label class="form-check-label" for="modeRange">Rotate only selected pages</label>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="rangeInput" class="form-label fw-semibold">Page Range (for selected pages)</label>
                        <input type="text" id="rangeInput" class="form-control" placeholder="e.g. 1-3,5" disabled>
                        <div class="form-text">Examples: 2 &nbsp;|&nbsp; 1-3,5 &nbsp;|&nbsp; 1,3,7-9</div>
                    </div>
                    <div class="col-md-6">
                        <label for="angleSel" class="form-label fw-semibold">Rotation Angle (clockwise)</label>
                        <select id="angleSel" class="form-select">
                            <option value="90" selected>90 degrees</option>
                            <option value="180">180 degrees</option>
                            <option value="270">270 degrees</option>
                        </select>
                    </div>
                </div>
                <div class="d-flex flex-wrap gap-2 mt-4">
                    <button type="button" id="processBtn" class="btn btn-success btn-lg">Rotate &amp; Download</button>
                    <button type="button" id="clearBtn" class="btn btn-outline-secondary">Clear</button>
                </div>
            </div>
        </div>
    </div>

    <div class="alert alert-info mt-4">
        <strong>Privacy note:</strong> Your files never leave your browser — everything happens on your phone/computer, nothing is uploaded.
    </div>

    <h2>How to use</h2>
    <ol>
        <li>Click the box above or drag and drop your PDF into it — the page count will appear.</li>
        <li>Choose to rotate all pages, or select "only selected pages" and type a range like 1-3,5.</li>
        <li>Pick the rotation angle: 90, 180 or 270 degrees clockwise.</li>
        <li>Click <strong>Rotate &amp; Download</strong> — your rotated PDF downloads automatically.</li>
    </ol>
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
    var optionsWrap = document.getElementById('optionsWrap');
    var fileNameEl = document.getElementById('fileName');
    var pageCountInfo = document.getElementById('pageCountInfo');
    var modeAll = document.getElementById('modeAll');
    var modeRange = document.getElementById('modeRange');
    var rangeInput = document.getElementById('rangeInput');
    var angleSel = document.getElementById('angleSel');
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
    function syncRangeState() {
        rangeInput.disabled = !modeRange.checked;
        if (modeRange.checked) {
            rangeInput.focus();
        }
    }
    function parseRange(text, total) {
        var result = [];
        var seen = {};
        var parts = text.split(',');
        for (var i = 0; i < parts.length; i++) {
            var part = parts[i].trim();
            if (!part) {
                return null;
            }
            var m = part.match(/^(\d+)\s*-\s*(\d+)$/);
            if (m) {
                var from = parseInt(m[1], 10);
                var to = parseInt(m[2], 10);
                if (from < 1 || to < from || to > total) {
                    return null;
                }
                for (var p = from; p <= to; p++) {
                    if (!seen[p]) {
                        seen[p] = true;
                        result.push(p - 1);
                    }
                }
            } else if (/^\d+$/.test(part)) {
                var single = parseInt(part, 10);
                if (single < 1 || single > total) {
                    return null;
                }
                if (!seen[single]) {
                    seen[single] = true;
                    result.push(single - 1);
                }
            } else {
                return null;
            }
        }
        result.sort(function (a, b) { return a - b; });
        return result.length ? result : null;
    }
    async function loadFile(file) {
        hideAlerts();
        if (!file) {
            return;
        }
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
            console.error(err);
            storedBuffer = null;
            totalPages = 0;
            optionsWrap.classList.add('d-none');
            showError('Could not read this PDF. It may be corrupted or password-protected. Please try a different file.');
        }
    }

    modeAll.addEventListener('change', syncRangeState);
    modeRange.addEventListener('change', syncRangeState);

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
    clearBtn.addEventListener('click', function () {
        storedBuffer = null;
        totalPages = 0;
        optionsWrap.classList.add('d-none');
        hideAlerts();
    });

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
        var angle = parseInt(angleSel.value, 10);
        processBtn.disabled = true;
        processBtn.textContent = 'Rotating...';
        try {
            var pdfDoc = await PDFLib.PDFDocument.load(storedBuffer.slice(0));
            var pages = pdfDoc.getPages();
            var indices;
            if (modeRange.checked) {
                indices = parseRange(rangeInput.value, pages.length);
                if (!indices) {
                    showError('Invalid page range. Use values inside 1 to ' + pages.length + ', e.g. 1-3,5.');
                    return;
                }
            } else {
                indices = pages.map(function (page, idx) { return idx; });
            }
            indices.forEach(function (idx) {
                var page = pages[idx];
                var current = 0;
                try {
                    current = page.getRotation().angle || 0;
                } catch (e) {
                    current = 0;
                }
                var next = (((current + angle) % 360) + 360) % 360;
                page.setRotation(PDFLib.degrees(next));
            });
            var outBytes = await pdfDoc.save();
            var blob = new Blob([outBytes], { type: 'application/pdf' });
            var url = URL.createObjectURL(blob);
            var a = document.createElement('a');
            a.href = url;
            a.download = storedName + '-rotated.pdf';
            document.body.appendChild(a);
            a.click();
            a.remove();
            setTimeout(function () { URL.revokeObjectURL(url); }, 4000);
            showSuccess('Done! Rotated ' + indices.length + ' page(s) by ' + angle + ' degrees. Your file (' + formatSize(blob.size) + ') has been downloaded.');
        } catch (err) {
            console.error(err);
            showError('Could not process this PDF. It may be corrupted or password-protected. Please try a different file.');
        } finally {
            processBtn.disabled = false;
            processBtn.textContent = 'Rotate & Download';
        }
    });
})();
</script>
@endsection
