@extends('layouts.app')
@section('title', 'PDF to PowerPoint Converter Online Free — Azlaan Tools')
@section('meta_description', 'Convert a PDF to a PowerPoint presentation (.pptx) online for free — one slide per PDF page. No signup, no upload — files stay in your browser.')
@section('content')
<div class="container py-4 pdf-tool">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">PDF to PowerPoint</h1>
            <p class="lead text-muted">Convert a PDF into a PowerPoint file (.pptx) — each PDF page becomes a full-screen slide, exactly as it looks in the PDF.</p>
            <div class="alert alert-warning">
                <strong>Honest note:</strong> Slides are images of the PDF pages — the slide text cannot be edited in PowerPoint, because each slide is a picture. The benefit is that the design, fonts and layout stay 100% the same as in the PDF. If you need editable text, use our PDF to Word tool.
            </div>

            <div id="alertBox" class="alert alert-danger d-none" role="alert"></div>
            <div id="successBox" class="alert alert-success d-none" role="alert"></div>

            <div id="dropZone" class="drop-zone mb-3">
                <div class="fs-1 mb-2">📽️</div>
                <p class="mb-1 fw-semibold">Drag &amp; drop a PDF here</p>
                <p class="text-muted mb-3">or click to browse from your device</p>
                <button type="button" class="btn btn-primary">Select PDF File</button>
                <input type="file" id="fileInput" accept="application/pdf,.pdf" class="d-none">
            </div>

            <div id="toolWrap" class="d-none">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <p class="mb-2">File: <strong id="fileName" class="text-break"></strong></p>
                        <p class="mb-3">Slides to create: <span id="pageInfo" class="fw-semibold">—</span></p>
                        <div class="mb-3">
                            <span class="form-label fw-semibold d-block mb-2">First page preview</span>
                            <canvas id="previewCanvas" class="border rounded w-100 d-block" style="background: #fff;"></canvas>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <button type="button" id="processBtn" class="btn btn-success btn-lg">Convert &amp; Download PowerPoint (.pptx)</button>
                            <button type="button" id="clearBtn" class="btn btn-outline-secondary">Clear</button>
                        </div>
                        <div id="progressWrap" class="progress mt-3 d-none" style="height: 24px;">
                            <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%;">0%</div>
                        </div>
                        <p class="small text-muted mt-2 mb-0" id="statusText"></p>
                    </div>
                </div>
            </div>

            <div class="alert alert-info mt-4">
                <strong>Privacy note:</strong> Your file never leaves your browser — everything runs on your phone or computer, nothing is uploaded.
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Click the box above or drag and drop your PDF into it — a preview of the first page will appear.</li>
                <li>Click <strong>Convert &amp; Download PowerPoint (.pptx)</strong> — each page becomes one slide.</li>
                <li>Open the file in PowerPoint, Google Slides or LibreOffice Impress. Remember: the slides are images — you can add a new text box and write on top, but the old text cannot be edited.</li>
            </ol>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/pdfjs-dist@3.11.174/build/pdf.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/pptxgenjs@3.12.0/dist/pptxgen.bundle.js"></script>
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
    var previewCanvas = document.getElementById('previewCanvas');
    var processBtn = document.getElementById('processBtn');
    var clearBtn = document.getElementById('clearBtn');
    var progressWrap = document.getElementById('progressWrap');
    var progressBar = document.getElementById('progressBar');
    var statusText = document.getElementById('statusText');
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
            var viewport = page.getViewport({ scale: 1.2 });
            previewCanvas.width = viewport.width;
            previewCanvas.height = viewport.height;
            var ctx = previewCanvas.getContext('2d');
            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, viewport.width, viewport.height);
            await page.render({ canvasContext: ctx, viewport: viewport }).promise;
            fileNameEl.textContent = file.name + ' (' + formatSize(file.size) + ')';
            pageInfo.textContent = totalPages + (totalPages === 1 ? ' slide (1 page)' : ' slides (' + totalPages + ' pages)');
            statusText.textContent = '';
            toolWrap.classList.remove('d-none');
            showSuccess('PDF loaded — ' + totalPages + ' page(s). Click Convert to turn each page into a slide.');
        } catch (err) {
            console.error(err);
            storedBuffer = null;
            totalPages = 0;
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
        storedBuffer = null;
        totalPages = 0;
        statusText.textContent = '';
        toolWrap.classList.add('d-none');
        hideAlerts();
    });

    processBtn.addEventListener('click', async function () {
        hideAlerts();
        if (!storedBuffer) { showError('Please select a PDF file first.'); return; }
        if (typeof pdfjsLib === 'undefined') { showError('PDF library failed to load. Please check your internet connection and try again.'); return; }
        if (typeof PptxGenJS === 'undefined') { showError('PowerPoint library failed to load. Please check your internet connection and try again.'); return; }
        processBtn.disabled = true;
        processBtn.textContent = 'Converting...';
        progressWrap.classList.remove('d-none');
        progressBar.style.width = '0%';
        progressBar.textContent = '0%';
        try {
            var pdfDoc = await pdfjsLib.getDocument({ data: storedBuffer.slice(0) }).promise;
            var firstPage = await pdfDoc.getPage(1);
            var baseVp = firstPage.getViewport({ scale: 1 });
            var slideW = baseVp.width / 72;
            var slideH = baseVp.height / 72;
            var pres = new PptxGenJS();
            pres.defineLayout({ name: 'PDF_LAYOUT', width: slideW, height: slideH });
            pres.layout = 'PDF_LAYOUT';
            pres.title = storedName;
            for (var n = 1; n <= pdfDoc.numPages; n++) {
                var page = (n === 1) ? firstPage : await pdfDoc.getPage(n);
                var vp = page.getViewport({ scale: 1.5 });
                var canvas = document.createElement('canvas');
                canvas.width = vp.width;
                canvas.height = vp.height;
                var ctx = canvas.getContext('2d');
                ctx.fillStyle = '#ffffff';
                ctx.fillRect(0, 0, vp.width, vp.height);
                await page.render({ canvasContext: ctx, viewport: vp }).promise;
                var imgData = canvas.toDataURL('image/jpeg', 0.92);
                var pageVp = page.getViewport({ scale: 1 });
                var wIn = pageVp.width / 72;
                var hIn = pageVp.height / 72;
                var slide = pres.addSlide();
                slide.addImage({ data: imgData, x: 0, y: 0, w: wIn, h: hIn });
                var pct = Math.round((n / pdfDoc.numPages) * 100);
                progressBar.style.width = pct + '%';
                progressBar.textContent = pct + '%';
                statusText.textContent = 'Slide ' + n + ' of ' + pdfDoc.numPages + ' ready...';
            }
            statusText.textContent = 'Creating the PowerPoint file...';
            await pres.writeFile({ fileName: storedName + '.pptx' });
            statusText.textContent = '';
            showSuccess('Done! ' + pdfDoc.numPages + ' slide(s) PowerPoint file downloaded. Remember: the slides are images of the pages, so the text cannot be edited.');
        } catch (err) {
            console.error(err);
            statusText.textContent = '';
            showError('Could not create the PowerPoint file. The PDF may be corrupted or too large. Please try again with a different PDF.');
        } finally {
            processBtn.disabled = false;
            processBtn.textContent = 'Convert & Download PowerPoint (.pptx)';
            setTimeout(function () { progressWrap.classList.add('d-none'); progressBar.style.width = '0%'; progressBar.textContent = '0%'; }, 800);
        }
    });
})();
</script>
@endsection
