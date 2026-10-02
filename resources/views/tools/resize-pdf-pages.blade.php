@extends('layouts.app')
@section('title', 'Resize PDF Pages — Change PDF Page Size Online Free — Azlaan Tools')
@section('meta_description', 'Resize PDF pages online for free to A4, Letter, Legal or A5. Pages are scaled to fit and centered, aspect ratio preserved. No signup, no upload — files stay in your browser.')
@section('content')
<div class="container py-4 pdf-tool">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Resize PDF Pages</h1>
            <p class="lead text-muted">Change the size of all pages in your PDF — A4, Letter, Legal or A5. Each page is scaled to fit and centered, keeping its shape (aspect ratio).</p>

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
                        <p class="mb-3">File: <strong id="fileName" class="text-break"></strong></p>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="sizeSelect" class="form-label fw-semibold">Target page size</label>
                                <select id="sizeSelect" class="form-select">
                                    <option value="a4" selected>A4 (210 × 297 mm)</option>
                                    <option value="letter">Letter (8.5 × 11 in)</option>
                                    <option value="legal">Legal (8.5 × 14 in)</option>
                                    <option value="a5">A5 (148 × 210 mm)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="orientSelect" class="form-label fw-semibold">Orientation</label>
                                <select id="orientSelect" class="form-select">
                                    <option value="auto" selected>Auto — match each page (landscape pages stay landscape)</option>
                                    <option value="portrait">Portrait — always upright</option>
                                    <option value="landscape">Landscape — always sideways</option>
                                </select>
                            </div>
                        </div>
                        <p class="small text-muted mb-3">In Auto mode, a page that is already landscape keeps a landscape target size — the content does not rotate, only the page size is set to the same direction.</p>
                        <div class="d-flex flex-wrap gap-2">
                            <button type="button" id="processBtn" class="btn btn-success btn-lg">Resize &amp; Download PDF</button>
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
                <li>Click the box above or drag and drop your PDF into it.</li>
                <li>Choose the target size (A4, Letter, Legal or A5) and orientation.</li>
                <li>Click <strong>Resize &amp; Download PDF</strong> — every page will be scaled to fit and centered on the new size, and the new PDF will download.</li>
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
    var sizeSelect = document.getElementById('sizeSelect');
    var orientSelect = document.getElementById('orientSelect');
    var processBtn = document.getElementById('processBtn');
    var clearBtn = document.getElementById('clearBtn');
    var progressWrap = document.getElementById('progressWrap');
    var progressBar = document.getElementById('progressBar');
    var storedBuffer = null;
    var storedName = 'document';
    var storedPageCount = 0;
    var SIZES = {
        a4: { w: 595.28, h: 841.89, label: 'A4' },
        letter: { w: 612, h: 792, label: 'Letter' },
        legal: { w: 612, h: 1008, label: 'Legal' },
        a5: { w: 419.53, h: 595.28, label: 'A5' }
    };

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
            storedPageCount = doc.getPageCount();
            fileNameEl.textContent = file.name + ' (' + formatSize(file.size) + ') — ' + storedPageCount + ' page(s)';
            toolWrap.classList.remove('d-none');
            showSuccess('PDF loaded. Choose a target size, then click Resize.');
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
        storedPageCount = 0;
        toolWrap.classList.add('d-none');
        progressWrap.classList.add('d-none');
        setProgress(0);
        hideAlerts();
    });

    processBtn.addEventListener('click', async function () {
        hideAlerts();
        if (!storedBuffer) { showError('Please select a PDF file first.'); return; }
        var target = SIZES[sizeSelect.value] || SIZES.a4;
        var orient = orientSelect.value;
        processBtn.disabled = true;
        processBtn.textContent = 'Resizing...';
        progressWrap.classList.remove('d-none');
        setProgress(0);
        try {
            var srcDoc = await PDFLib.PDFDocument.load(storedBuffer.slice(0), { ignoreEncryption: true });
            var outDoc = await PDFLib.PDFDocument.create();
            var srcPages = srcDoc.getPages();
            for (var i = 0; i < srcPages.length; i++) {
                var srcPage = srcPages[i];
                var srcSize = srcPage.getSize();
                var embedded = await outDoc.embedPage(srcPage);
                var tw = target.w;
                var th = target.h;
                if (orient === 'landscape') {
                    if (tw < th) { var tmpL = tw; tw = th; th = tmpL; }
                } else if (orient === 'portrait') {
                    if (tw > th) { var tmpP = tw; tw = th; th = tmpP; }
                } else {
                    if (srcSize.width > srcSize.height && tw < th) { var tmpA = tw; tw = th; th = tmpA; }
                    if (srcSize.width <= srcSize.height && tw > th) { var tmpA2 = tw; tw = th; th = tmpA2; }
                }
                var newPage = outDoc.addPage([tw, th]);
                var scale = Math.min(tw / srcSize.width, th / srcSize.height);
                var drawnW = srcSize.width * scale;
                var drawnH = srcSize.height * scale;
                var x = (tw - drawnW) / 2;
                var y = (th - drawnH) / 2;
                newPage.drawPage(embedded, { x: x, y: y, width: drawnW, height: drawnH });
                setProgress(Math.round(((i + 1) / srcPages.length) * 90));
            }
            var bytes = await outDoc.save();
            setProgress(100);
            var blob = new Blob([bytes], { type: 'application/pdf' });
            downloadBlob(blob, storedName + '-' + sizeSelect.value + '.pdf');
            showSuccess('Done! All ' + srcPages.length + ' page(s) resized to ' + target.label + '. Your PDF (' + formatSize(blob.size) + ') has been downloaded.');
        } catch (err) {
            console.error(err);
            showError('Could not resize this PDF. It may be corrupted or protected. Please try a different file.');
        } finally {
            processBtn.disabled = false;
            processBtn.textContent = 'Resize & Download PDF';
            setTimeout(function () { progressWrap.classList.add('d-none'); setProgress(0); }, 800);
        }
    });
})();
</script>
@endsection
