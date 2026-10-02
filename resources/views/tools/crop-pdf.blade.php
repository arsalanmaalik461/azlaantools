@extends('layouts.app')
@section('title', 'Crop PDF Online Free — Azlaan Tools')
@section('meta_description', 'Crop PDF pages online for free — trim margins from all pages or a page range and download the cropped PDF. No signup, no upload — files stay in your browser.')
@section('content')
<div class="container py-4 pdf-tool">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Crop PDF</h1>
            <p class="lead text-muted">Trim unwanted margins and white borders from your PDF pages. Set the crop margins in points, check the live preview, and download the cropped file.</p>
            <div class="alert alert-warning">
                <strong>Honest note:</strong> Cropping changes the visible page area (CropBox) — the hidden content outside the crop area is not deleted from the file, it is just no longer shown or printed. 72 points = 1 inch, and a normal A4 page is 595 × 842 points.
            </div>

            <div id="alertBox" class="alert alert-danger d-none" role="alert"></div>
            <div id="successBox" class="alert alert-success d-none" role="alert"></div>

            <div id="dropZone" class="drop-zone mb-3">
                <div class="fs-1 mb-2">✂️</div>
                <p class="mb-1 fw-semibold">Drag &amp; drop a PDF here</p>
                <p class="text-muted mb-3">or click to browse from your device</p>
                <button type="button" class="btn btn-primary">Select PDF File</button>
                <input type="file" id="fileInput" accept="application/pdf,.pdf" class="d-none">
            </div>

            <div id="toolWrap" class="d-none">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <p class="mb-1">File: <strong id="fileName" class="text-break"></strong></p>
                        <p class="mb-3">Pages: <span id="pageInfo" class="fw-semibold">—</span> &nbsp; Page 1 size: <span id="pageSizeInfo" class="fw-semibold">—</span></p>

                        <span class="form-label fw-semibold d-block mb-2">Which pages to crop</span>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="cropMode" id="modeAll" value="all" checked>
                            <label class="form-check-label" for="modeAll">Crop ALL pages (same margins)</label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="radio" name="cropMode" id="modeRange" value="range">
                            <label class="form-check-label" for="modeRange">Crop only selected pages</label>
                        </div>
                        <div class="mb-3">
                            <label for="rangeInput" class="form-label fw-semibold">Page range (for selected pages)</label>
                            <input type="text" id="rangeInput" class="form-control" placeholder="e.g. 1-3,5" disabled>
                            <div class="form-text">Examples: 2 &nbsp;|&nbsp; 1-3,5 &nbsp;|&nbsp; 1,3,7-9</div>
                        </div>

                        <span class="form-label fw-semibold d-block mb-2">Crop margins (points — the number of points you enter is trimmed from that side)</span>
                        <div class="row g-3 mb-2">
                            <div class="col-6 col-md-3">
                                <label for="marginTop" class="form-label">Top</label>
                                <input type="number" id="marginTop" class="form-control" value="36" min="0" step="1">
                            </div>
                            <div class="col-6 col-md-3">
                                <label for="marginBottom" class="form-label">Bottom</label>
                                <input type="number" id="marginBottom" class="form-control" value="36" min="0" step="1">
                            </div>
                            <div class="col-6 col-md-3">
                                <label for="marginLeft" class="form-label">Left</label>
                                <input type="number" id="marginLeft" class="form-control" value="36" min="0" step="1">
                            </div>
                            <div class="col-6 col-md-3">
                                <label for="marginRight" class="form-label">Right</label>
                                <input type="number" id="marginRight" class="form-control" value="36" min="0" step="1">
                            </div>
                        </div>
                        <p class="small text-muted mb-3" id="cropResultInfo">New page size will appear here.</p>

                        <span class="form-label fw-semibold d-block mb-2">Live preview — page 1 (green box = the area that will remain)</span>
                        <div id="previewWrap" class="position-relative border rounded mb-3 mx-auto" style="max-width: 640px; background: #fff;">
                            <canvas id="previewCanvas" class="w-100 d-block"></canvas>
                            <div id="cropOverlay" class="position-absolute" style="border: 2px solid #198754; background: rgba(25,135,84,0.12); pointer-events: none; box-sizing: border-box;"></div>
                        </div>

                        <div class="d-flex flex-wrap gap-2">
                            <button type="button" id="processBtn" class="btn btn-success btn-lg">Crop &amp; Download PDF</button>
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
                <li>Click the box above or drag and drop your PDF into it — you will see the page 1 preview right away.</li>
                <li>Choose all pages, or select a page range like 1-3,5.</li>
                <li>Type the margins to trim from the top, bottom, left and right (in points). The green box in the preview updates live.</li>
                <li>Click <strong>Crop &amp; Download PDF</strong> and save your cropped file.</li>
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
    var pageInfo = document.getElementById('pageInfo');
    var pageSizeInfo = document.getElementById('pageSizeInfo');
    var modeAll = document.getElementById('modeAll');
    var modeRange = document.getElementById('modeRange');
    var rangeInput = document.getElementById('rangeInput');
    var marginTop = document.getElementById('marginTop');
    var marginBottom = document.getElementById('marginBottom');
    var marginLeft = document.getElementById('marginLeft');
    var marginRight = document.getElementById('marginRight');
    var cropResultInfo = document.getElementById('cropResultInfo');
    var previewWrap = document.getElementById('previewWrap');
    var previewCanvas = document.getElementById('previewCanvas');
    var cropOverlay = document.getElementById('cropOverlay');
    var processBtn = document.getElementById('processBtn');
    var clearBtn = document.getElementById('clearBtn');
    var progressWrap = document.getElementById('progressWrap');
    var progressBar = document.getElementById('progressBar');
    var storedBuffer = null;
    var storedName = 'document';
    var totalPages = 0;
    var firstPageW = 0;
    var firstPageH = 0;

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
    function getMargin(input) {
        var v = parseFloat(input.value);
        if (isNaN(v) || v < 0) return 0;
        return v;
    }
    function getMargins() {
        return {
            top: getMargin(marginTop),
            bottom: getMargin(marginBottom),
            left: getMargin(marginLeft),
            right: getMargin(marginRight)
        };
    }
    function updateOverlay() {
        if (!firstPageW || !firstPageH) return;
        var m = getMargins();
        var newW = firstPageW - m.left - m.right;
        var newH = firstPageH - m.top - m.bottom;
        var wrapW = previewWrap.clientWidth || previewCanvas.clientWidth || 1;
        var wrapH = previewCanvas.clientHeight || 1;
        cropOverlay.style.left = (m.left / firstPageW * 100) + '%';
        cropOverlay.style.top = (m.top / firstPageH * 100) + '%';
        cropOverlay.style.width = (Math.max(newW, 0) / firstPageW * 100) + '%';
        cropOverlay.style.height = (Math.max(newH, 0) / firstPageH * 100) + '%';
        if (newW <= 0 || newH <= 0) {
            cropResultInfo.textContent = 'Margins are too large — some of the page must remain. Reduce the margins.';
        } else {
            cropResultInfo.textContent = 'New visible size: ' + Math.round(newW) + ' x ' + Math.round(newH) + ' points (was ' + Math.round(firstPageW) + ' x ' + Math.round(firstPageH) + '). Preview frame: ' + wrapW + 'px wide.';
        }
    }
    function syncRangeState() {
        rangeInput.disabled = !modeRange.checked;
        if (modeRange.checked) rangeInput.focus();
    }
    function parseRange(text, total) {
        var result = [];
        var seen = {};
        var parts = text.split(',');
        for (var i = 0; i < parts.length; i++) {
            var part = parts[i].trim();
            if (!part) return null;
            var mm = part.match(/^(\d+)\s*-\s*(\d+)$/);
            if (mm) {
                var from = parseInt(mm[1], 10);
                var to = parseInt(mm[2], 10);
                if (from < 1 || to < from || to > total) return null;
                for (var p = from; p <= to; p++) {
                    if (!seen[p]) { seen[p] = true; result.push(p - 1); }
                }
            } else if (/^\d+$/.test(part)) {
                var single = parseInt(part, 10);
                if (single < 1 || single > total) return null;
                if (!seen[single]) { seen[single] = true; result.push(single - 1); }
            } else {
                return null;
            }
        }
        result.sort(function (a, b) { return a - b; });
        return result.length ? result : null;
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
            storedBuffer = buf;
            storedName = file.name.replace(/\.pdf$/i, '') || 'document';
            var pdfDoc = await pdfjsLib.getDocument({ data: buf.slice(0) }).promise;
            totalPages = pdfDoc.numPages;
            var page = await pdfDoc.getPage(1);
            var baseViewport = page.getViewport({ scale: 1 });
            firstPageW = baseViewport.width;
            firstPageH = baseViewport.height;
            var scale = 640 / baseViewport.width;
            if (scale > 2) scale = 2;
            var viewport = page.getViewport({ scale: scale });
            previewCanvas.width = viewport.width;
            previewCanvas.height = viewport.height;
            var ctx = previewCanvas.getContext('2d');
            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, viewport.width, viewport.height);
            await page.render({ canvasContext: ctx, viewport: viewport }).promise;
            fileNameEl.textContent = file.name + ' (' + formatSize(file.size) + ')';
            pageInfo.textContent = totalPages + (totalPages === 1 ? ' page' : ' pages');
            pageSizeInfo.textContent = Math.round(firstPageW) + ' x ' + Math.round(firstPageH) + ' pt';
            toolWrap.classList.remove('d-none');
            updateOverlay();
            showSuccess('PDF loaded — ' + totalPages + ' page(s). Now set the margins and press Crop.');
        } catch (err) {
            console.error(err);
            storedBuffer = null;
            totalPages = 0;
            toolWrap.classList.add('d-none');
            showError('Could not read this PDF. It may be corrupted or password-protected. Please try a different file.');
        }
    }

    [marginTop, marginBottom, marginLeft, marginRight].forEach(function (input) {
        input.addEventListener('input', updateOverlay);
    });
    modeAll.addEventListener('change', syncRangeState);
    modeRange.addEventListener('change', syncRangeState);
    window.addEventListener('resize', updateOverlay);

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
        storedBuffer = null;
        totalPages = 0;
        firstPageW = 0;
        firstPageH = 0;
        toolWrap.classList.add('d-none');
        hideAlerts();
    });

    processBtn.addEventListener('click', async function () {
        hideAlerts();
        if (!storedBuffer) { showError('Please select a PDF file first.'); return; }
        if (typeof PDFLib === 'undefined') { showError('PDF library failed to load. Please check your internet connection and try again.'); return; }
        var m = getMargins();
        if (m.top + m.bottom <= 0 && m.left + m.right <= 0) {
            showError('Please enter at least one margin greater than 0.');
            return;
        }
        processBtn.disabled = true;
        processBtn.textContent = 'Cropping...';
        progressWrap.classList.remove('d-none');
        progressBar.style.width = '10%';
        progressBar.textContent = '10%';
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
                indices = pages.map(function (pg, idx) { return idx; });
            }
            var croppedCount = 0;
            for (var i = 0; i < indices.length; i++) {
                var page = pages[indices[i]];
                var size = page.getSize();
                var newW = size.width - m.left - m.right;
                var newH = size.height - m.top - m.bottom;
                if (newW <= 10 || newH <= 10) {
                    throw new Error('Margins too large for page ' + (indices[i] + 1));
                }
                var x = m.left;
                var y = m.bottom;
                page.setCropBox(x, y, newW, newH);
                if (typeof page.setMediaBox === 'function') {
                    page.setMediaBox(x, y, newW, newH);
                }
                croppedCount++;
                var pct = Math.round(((i + 1) / indices.length) * 90) + 10;
                progressBar.style.width = pct + '%';
                progressBar.textContent = pct + '%';
            }
            progressBar.style.width = '100%';
            progressBar.textContent = '100%';
            var outBytes = await pdfDoc.save();
            var blob = new Blob([outBytes], { type: 'application/pdf' });
            downloadBlob(blob, storedName + '-cropped.pdf');
            showSuccess('Done! Cropped ' + croppedCount + ' page(s). Your file (' + formatSize(blob.size) + ') has been downloaded.');
        } catch (err) {
            console.error(err);
            if (err && err.message && err.message.indexOf('Margins too large') === 0) {
                showError(err.message + '. Reduce the margins and try again.');
            } else {
                showError('Could not crop this PDF. It may be corrupted or password-protected. Please try a different file.');
            }
        } finally {
            processBtn.disabled = false;
            processBtn.textContent = 'Crop & Download PDF';
            setTimeout(function () { progressWrap.classList.add('d-none'); progressBar.style.width = '0%'; progressBar.textContent = '0%'; }, 800);
        }
    });
})();
</script>
@endsection
