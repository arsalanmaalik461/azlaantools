@extends('layouts.app')
@section('title', 'PDF to PNG Converter Online Free — Azlaan Tools')
@section('meta_description', 'Convert PDF pages to PNG images online for free. Choose image quality, download pages one by one or all together as a ZIP. No signup, no upload.')
@section('content')
<div class="container py-4 pdf-tool">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">PDF to PNG</h1>
            <p class="lead text-muted">Turn every page of your PDF into a crisp PNG image. Pick the quality, then download pages individually or all at once as a ZIP file.</p>

            <div id="alertBox" class="alert alert-danger d-none" role="alert"></div>
            <div id="successBox" class="alert alert-success d-none" role="alert"></div>

            <div id="dropZone" class="border border-2 border-dashed rounded-3 p-4 p-md-5 text-center mb-3" style="border-style: dashed !important; cursor: pointer; background: #f8f9fa;">
                <div class="fs-1 mb-2">🖼️</div>
                <p class="mb-1 fw-semibold">Drag &amp; drop a PDF here</p>
                <p class="text-muted mb-3">or click to browse from your device</p>
                <button type="button" class="btn btn-primary">Select PDF File</button>
                <input type="file" id="fileInput" accept="application/pdf,.pdf" class="d-none">
            </div>

            <div id="toolWrap" class="d-none">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <p class="mb-3">File: <strong id="fileName" class="text-break"></strong> — <span id="pageCountInfo"></span></p>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="scaleSel" class="form-label fw-semibold">Image quality</label>
                                <select id="scaleSel" class="form-select form-select-lg">
                                    <option value="1">Standard — 72 DPI</option>
                                    <option value="2" selected>High — 144 DPI</option>
                                    <option value="3">Very High — 216 DPI</option>
                                    <option value="4">Print — 288 DPI</option>
                                </select>
                            </div>
                        </div>
                        <div id="thumbGrid" class="row g-3 mb-3"></div>
                        <div class="d-flex flex-wrap gap-2">
                            <button type="button" id="zipBtn" class="btn btn-success btn-lg">Download All as ZIP</button>
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
                <li>Click the box above or drag and drop your PDF into it — page previews will appear.</li>
                <li>Choose the image quality (higher DPI means bigger, sharper images).</li>
                <li>Download a single page with its <strong>PNG</strong> button, or click <strong>Download All as ZIP</strong> for every page in one file.</li>
            </ol>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/pdfjs-dist@3.11.174/build/pdf.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
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
    var pageCountInfo = document.getElementById('pageCountInfo');
    var scaleSel = document.getElementById('scaleSel');
    var thumbGrid = document.getElementById('thumbGrid');
    var zipBtn = document.getElementById('zipBtn');
    var clearBtn = document.getElementById('clearBtn');
    var progressWrap = document.getElementById('progressWrap');
    var progressBar = document.getElementById('progressBar');
    var pdfDoc = null;
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
    function downloadBlob(blob, name) {
        var url = URL.createObjectURL(blob);
        var a = document.createElement('a');
        a.href = url;
        a.download = name;
        document.body.appendChild(a);
        a.click();
        a.remove();
        setTimeout(function () { URL.revokeObjectURL(url); }, 4000);
    }
    async function renderPageBlob(pageNum, scale) {
        var page = await pdfDoc.getPage(pageNum);
        var viewport = page.getViewport({ scale: scale });
        var canvas = document.createElement('canvas');
        canvas.width = viewport.width;
        canvas.height = viewport.height;
        var ctx = canvas.getContext('2d');
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        await page.render({ canvasContext: ctx, viewport: viewport }).promise;
        return new Promise(function (resolve) { canvas.toBlob(resolve, 'image/png'); });
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
            pdfDoc = await pdfjsLib.getDocument({ data: buf }).promise;
            totalPages = pdfDoc.numPages;
            storedName = file.name.replace(/\.pdf$/i, '') || 'document';
            fileNameEl.textContent = file.name + ' (' + formatSize(file.size) + ')';
            pageCountInfo.textContent = totalPages + (totalPages === 1 ? ' page' : ' pages');
            thumbGrid.innerHTML = '';
            for (var i = 1; i <= totalPages; i++) {
                var page = await pdfDoc.getPage(i);
                var viewport = page.getViewport({ scale: 0.35 });
                var canvas = document.createElement('canvas');
                canvas.width = viewport.width;
                canvas.height = viewport.height;
                var col = document.createElement('div');
                col.className = 'col-6 col-md-4 col-lg-3';
                var card = document.createElement('div');
                card.className = 'card h-100';
                canvas.className = 'card-img-top bg-white';
                card.appendChild(canvas);
                var body = document.createElement('div');
                body.className = 'card-body p-2 text-center';
                var label = document.createElement('p');
                label.className = 'small fw-semibold mb-2';
                label.textContent = 'Page ' + i;
                body.appendChild(label);
                var btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'btn btn-sm btn-primary';
                btn.textContent = 'Download PNG';
                (function (pageNum, button) {
                    button.addEventListener('click', async function () {
                        button.disabled = true;
                        button.textContent = 'Rendering...';
                        try {
                            var blob = await renderPageBlob(pageNum, parseFloat(scaleSel.value));
                            downloadBlob(blob, storedName + '-page-' + pageNum + '.png');
                            showSuccess('Page ' + pageNum + ' downloaded as PNG (' + formatSize(blob.size) + ').');
                        } catch (err) {
                            console.error(err);
                            showError('Could not render page ' + pageNum + '. Please try a lower quality setting.');
                        } finally {
                            button.disabled = false;
                            button.textContent = 'Download PNG';
                        }
                    });
                })(i, btn);
                body.appendChild(btn);
                card.appendChild(body);
                col.appendChild(card);
                thumbGrid.appendChild(col);
                await page.render({ canvasContext: canvas.getContext('2d'), viewport: viewport }).promise;
            }
            toolWrap.classList.remove('d-none');
            showSuccess('PDF loaded — ' + totalPages + ' page(s). Choose a quality and download.');
        } catch (err) {
            console.error(err);
            pdfDoc = null;
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
        pdfDoc = null;
        thumbGrid.innerHTML = '';
        toolWrap.classList.add('d-none');
        hideAlerts();
    });

    zipBtn.addEventListener('click', async function () {
        hideAlerts();
        if (!pdfDoc) { showError('Please select a PDF file first.'); return; }
        if (typeof JSZip === 'undefined') { showError('ZIP library failed to load. Please check your internet connection and try again.'); return; }
        zipBtn.disabled = true;
        zipBtn.textContent = 'Creating ZIP...';
        progressWrap.classList.remove('d-none');
        try {
            var zip = new JSZip();
            var scale = parseFloat(scaleSel.value);
            for (var i = 1; i <= totalPages; i++) {
                var blob = await renderPageBlob(i, scale);
                zip.file(storedName + '-page-' + i + '.png', blob);
                var pct = Math.round((i / totalPages) * 100);
                progressBar.style.width = pct + '%';
                progressBar.textContent = pct + '%';
            }
            var zipBlob = await zip.generateAsync({ type: 'blob' });
            downloadBlob(zipBlob, storedName + '-pngs.zip');
            showSuccess('Done! ZIP with ' + totalPages + ' PNG image(s) (' + formatSize(zipBlob.size) + ') has been downloaded.');
        } catch (err) {
            console.error(err);
            showError('Could not create the ZIP. The PDF may be too large at this quality — please try a lower DPI setting.');
        } finally {
            zipBtn.disabled = false;
            zipBtn.textContent = 'Download All as ZIP';
            setTimeout(function () { progressWrap.classList.add('d-none'); progressBar.style.width = '0%'; progressBar.textContent = '0%'; }, 800);
        }
    });
})();
</script>
@endsection
