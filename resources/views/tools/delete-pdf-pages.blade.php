@extends('layouts.app')
@section('title', 'Delete Pages from PDF Online Free — Azlaan Tools')
@section('meta_description', 'Delete unwanted pages from a PDF online for free. Tick the pages to remove and download the cleaned PDF. No signup, no upload — files stay in your browser.')
@section('content')
<div class="container py-4 pdf-tool">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Delete PDF Pages</h1>
            <p class="lead text-muted">Remove the pages you do not need from any PDF. Tick pages in the preview grid, then download the cleaned file.</p>

            <div id="alertBox" class="alert alert-danger d-none" role="alert"></div>
            <div id="successBox" class="alert alert-success d-none" role="alert"></div>

            <div id="dropZone" class="border border-2 border-dashed rounded-3 p-4 p-md-5 text-center mb-3" style="border-style: dashed !important; cursor: pointer; background: #f8f9fa;">
                <div class="fs-1 mb-2">🗑️</div>
                <p class="mb-1 fw-semibold">Drag &amp; drop a PDF here</p>
                <p class="text-muted mb-3">or click to browse from your device</p>
                <button type="button" class="btn btn-primary">Select PDF File</button>
                <input type="file" id="fileInput" accept="application/pdf,.pdf" class="d-none">
            </div>

            <div id="toolWrap" class="d-none">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <p class="mb-2">File: <strong id="fileName" class="text-break"></strong></p>
                        <p class="mb-3"><span id="selInfo" class="fw-semibold">0 pages marked for deletion</span> — <span id="remainInfo"></span></p>
                        <div id="thumbGrid" class="row g-3 mb-3"></div>
                        <div class="d-flex flex-wrap gap-2">
                            <button type="button" id="processBtn" class="btn btn-success btn-lg">Delete Pages &amp; Download</button>
                            <button type="button" id="clearBtn" class="btn btn-outline-secondary">Clear</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="alert alert-info mt-4">
                <strong>Privacy note:</strong> Your file never leaves your browser — everything happens on your phone or computer, nothing is uploaded.
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Click the box above or drag and drop your PDF into it — page previews will appear.</li>
                <li>Tick the checkbox on every page you want to remove.</li>
                <li>Click <strong>Delete Pages &amp; Download</strong>. At least one page must remain in the PDF.</li>
                <li>Your new PDF downloads automatically.</li>
            </ol>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/pdf-lib@1.17.1/dist/pdf-lib.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/pdfjs-dist@3.11.174/build/pdf.min.js"></script>
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
    var selInfo = document.getElementById('selInfo');
    var remainInfo = document.getElementById('remainInfo');
    var thumbGrid = document.getElementById('thumbGrid');
    var processBtn = document.getElementById('processBtn');
    var clearBtn = document.getElementById('clearBtn');
    var storedBuffer = null;
    var storedName = 'document';
    var totalPages = 0;
    var marked = {};

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
    function updateInfo() {
        var count = Object.keys(marked).length;
        selInfo.textContent = count + (count === 1 ? ' page' : ' pages') + ' marked for deletion';
        remainInfo.textContent = (totalPages - count) + ' page(s) will remain';
    }

    async function loadFile(file) {
        hideAlerts();
        if (!file) return;
        if (!(file.type === 'application/pdf' || file.name.toLowerCase().endsWith('.pdf'))) {
            showError('Please select a valid PDF file.');
            return;
        }
        if (typeof pdfjsLib === 'undefined' || typeof PDFLib === 'undefined') {
            showError('PDF libraries failed to load. Please check your internet connection and try again.');
            return;
        }
        try {
            var buf = await file.arrayBuffer();
            storedBuffer = buf;
            storedName = file.name.replace(/\.pdf$/i, '') || 'document';
            marked = {};
            var pdf = await pdfjsLib.getDocument({ data: buf.slice(0) }).promise;
            totalPages = pdf.numPages;
            fileNameEl.textContent = file.name + ' (' + formatSize(file.size) + ') — ' + totalPages + ' page(s)';
            thumbGrid.innerHTML = '';
            for (var i = 1; i <= totalPages; i++) {
                var page = await pdf.getPage(i);
                var viewport = page.getViewport({ scale: 0.35 });
                var col = document.createElement('div');
                col.className = 'col-6 col-md-4 col-lg-3';
                var card = document.createElement('div');
                card.className = 'card h-100';
                var canvas = document.createElement('canvas');
                canvas.width = viewport.width;
                canvas.height = viewport.height;
                canvas.className = 'card-img-top bg-white';
                card.appendChild(canvas);
                var body = document.createElement('div');
                body.className = 'card-body p-2 text-center';
                var label = document.createElement('label');
                label.className = 'form-check-label fw-semibold d-block mb-0';
                var cb = document.createElement('input');
                cb.type = 'checkbox';
                cb.className = 'form-check-input me-1';
                cb.setAttribute('data-page', i);
                label.appendChild(cb);
                label.appendChild(document.createTextNode('Delete page ' + i));
                body.appendChild(label);
                card.appendChild(body);
                col.appendChild(card);
                thumbGrid.appendChild(col);
                await page.render({ canvasContext: canvas.getContext('2d'), viewport: viewport }).promise;
                (function (checkbox, pageNum, cardEl) {
                    checkbox.addEventListener('change', function () {
                        if (checkbox.checked) { marked[pageNum] = true; cardEl.classList.add('border-danger'); }
                        else { delete marked[pageNum]; cardEl.classList.remove('border-danger'); }
                        updateInfo();
                    });
                })(cb, i, card);
            }
            updateInfo();
            toolWrap.classList.remove('d-none');
            showSuccess('PDF loaded — ' + totalPages + ' page(s). Tick the pages you want to delete.');
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
        marked = {};
        thumbGrid.innerHTML = '';
        toolWrap.classList.add('d-none');
        hideAlerts();
    });

    processBtn.addEventListener('click', async function () {
        hideAlerts();
        if (!storedBuffer) { showError('Please select a PDF file first.'); return; }
        var deleteCount = Object.keys(marked).length;
        if (deleteCount === 0) { showError('Please tick at least one page to delete.'); return; }
        if (deleteCount >= totalPages) { showError('You cannot delete every page — at least one page must remain.'); return; }
        processBtn.disabled = true;
        processBtn.textContent = 'Processing...';
        try {
            var src = await PDFLib.PDFDocument.load(storedBuffer.slice(0));
            var out = await PDFLib.PDFDocument.create();
            var keep = [];
            for (var i = 0; i < totalPages; i++) {
                if (!marked[i + 1]) keep.push(i);
            }
            var copied = await out.copyPages(src, keep);
            copied.forEach(function (p) { out.addPage(p); });
            var bytes = await out.save();
            var blob = new Blob([bytes], { type: 'application/pdf' });
            var url = URL.createObjectURL(blob);
            var a = document.createElement('a');
            a.href = url;
            a.download = storedName + '-pages-deleted.pdf';
            document.body.appendChild(a);
            a.click();
            a.remove();
            setTimeout(function () { URL.revokeObjectURL(url); }, 4000);
            showSuccess('Done! ' + deleteCount + ' page(s) deleted. Your PDF (' + formatSize(blob.size) + ') has been downloaded.');
        } catch (err) {
            console.error(err);
            showError('Could not process this PDF. It may be corrupted or password-protected. Please try a different file.');
        } finally {
            processBtn.disabled = false;
            processBtn.textContent = 'Delete Pages & Download';
        }
    });
})();
</script>
@endsection
