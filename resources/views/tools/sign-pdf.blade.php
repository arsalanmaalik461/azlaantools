@extends('layouts.app')
@section('title', 'Sign PDF Online Free — Azlaan Tools')
@section('meta_description', 'Sign a PDF online for free. Draw or type your signature, place it on any page and download the signed PDF. No signup, no upload — files stay in your browser.')
@section('content')
<div class="container py-4 pdf-tool">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Sign PDF</h1>
            <p class="lead text-muted">Add your signature to a PDF — draw it or type it, choose the page and position, then download the signed file. Free and private.</p>

            <div id="alertBox" class="alert alert-danger d-none" role="alert"></div>
            <div id="successBox" class="alert alert-success d-none" role="alert"></div>

            <div id="dropZone" class="border border-2 border-dashed rounded-3 p-4 p-md-5 text-center mb-3" style="border-style: dashed !important; cursor: pointer; background: #f8f9fa;">
                <div class="fs-1 mb-2">✍️</div>
                <p class="mb-1 fw-semibold">Drag &amp; drop a PDF here</p>
                <p class="text-muted mb-3">or click to browse from your device</p>
                <button type="button" class="btn btn-primary">Select PDF File</button>
                <input type="file" id="fileInput" accept="application/pdf,.pdf" class="d-none">
            </div>

            <div id="toolWrap" class="d-none">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <p class="mb-3">File: <strong id="fileName" class="text-break"></strong> — <span id="pageCountInfo"></span></p>

                        <h2 class="h5">1. Create your signature</h2>
                        <ul class="nav nav-tabs mb-3" role="tablist">
                            <li class="nav-item"><button type="button" class="nav-link active" id="tabDraw">Draw</button></li>
                            <li class="nav-item"><button type="button" class="nav-link" id="tabType">Type</button></li>
                        </ul>

                        <div id="drawPane">
                            <canvas id="sigPad" width="600" height="200" class="border rounded w-100 bg-white" style="touch-action: none; cursor: crosshair;"></canvas>
                            <div class="d-flex gap-2 mt-2">
                                <button type="button" id="clearSigBtn" class="btn btn-outline-secondary btn-sm">Clear Signature</button>
                            </div>
                        </div>

                        <div id="typePane" class="d-none">
                            <label for="typedName" class="form-label fw-semibold">Type your name</label>
                            <input type="text" id="typedName" class="form-control form-control-lg" placeholder="e.g. Malik Arslan" style="font-family: cursive;">
                            <p class="form-text">It will be placed in a cursive style font.</p>
                        </div>

                        <hr>
                        <h2 class="h5">2. Place the signature</h2>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="pageSel" class="form-label fw-semibold">Page</label>
                                <select id="pageSel" class="form-select form-select-lg"></select>
                            </div>
                            <div class="col-md-4">
                                <label for="posSel" class="form-label fw-semibold">Position</label>
                                <select id="posSel" class="form-select form-select-lg">
                                    <option value="bottom-right" selected>Bottom Right</option>
                                    <option value="bottom-left">Bottom Left</option>
                                    <option value="bottom-center">Bottom Center</option>
                                    <option value="top-right">Top Right</option>
                                    <option value="center">Center</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="sizeRange" class="form-label fw-semibold">Size: <span id="sizeLabel">25%</span> of page width</label>
                                <input type="range" id="sizeRange" class="form-range" min="10" max="50" value="25" step="1">
                            </div>
                        </div>

                        <div class="mt-3">
                            <p class="fw-semibold mb-1">Page preview</p>
                            <canvas id="previewCanvas" class="border rounded w-100 bg-white"></canvas>
                            <p class="form-text">Preview shows the selected page. The signature is stamped at the chosen position when you download.</p>
                        </div>

                        <div class="d-flex flex-wrap gap-2 mt-3">
                            <button type="button" id="signBtn" class="btn btn-success btn-lg">Sign &amp; Download PDF</button>
                            <button type="button" id="clearBtn" class="btn btn-outline-secondary">Clear</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="alert alert-info mt-4">
                <strong>Privacy note:</strong> Your file never leaves your browser — all the work happens on your phone/computer, nothing is uploaded.
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Click the box above or drag and drop your PDF into it.</li>
                <li>Draw your signature with your mouse or finger — or switch to <strong>Type</strong> and type your name instead.</li>
                <li>Choose the page, position and signature size. The preview helps you check the page.</li>
                <li>Click <strong>Sign &amp; Download PDF</strong> and the signed file downloads automatically.</li>
            </ol>
            <p class="text-muted small">Note: this adds a visible signature image to your PDF. It is not a cryptographic / digital-certificate signature.</p>
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
    var pageCountInfo = document.getElementById('pageCountInfo');
    var sigPad = document.getElementById('sigPad');
    var clearSigBtn = document.getElementById('clearSigBtn');
    var tabDraw = document.getElementById('tabDraw');
    var tabType = document.getElementById('tabType');
    var drawPane = document.getElementById('drawPane');
    var typePane = document.getElementById('typePane');
    var typedName = document.getElementById('typedName');
    var pageSel = document.getElementById('pageSel');
    var posSel = document.getElementById('posSel');
    var sizeRange = document.getElementById('sizeRange');
    var sizeLabel = document.getElementById('sizeLabel');
    var previewCanvas = document.getElementById('previewCanvas');
    var signBtn = document.getElementById('signBtn');
    var clearBtn = document.getElementById('clearBtn');

    var storedBuffer = null;
    var storedName = 'document';
    var pdfJsDoc = null;
    var totalPages = 0;
    var sigMode = 'draw';
    var drawing = false;
    var hasDrawn = false;
    var sigCtx = sigPad.getContext('2d');
    sigCtx.lineWidth = 2.5;
    sigCtx.lineCap = 'round';
    sigCtx.strokeStyle = '#1a1a8c';

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

    function padPos(e) {
        var rect = sigPad.getBoundingClientRect();
        var x = (e.touches ? e.touches[0].clientX : e.clientX) - rect.left;
        var y = (e.touches ? e.touches[0].clientY : e.clientY) - rect.top;
        return { x: x * (sigPad.width / rect.width), y: y * (sigPad.height / rect.height) };
    }
    function startDraw(e) {
        drawing = true;
        hasDrawn = true;
        var p = padPos(e);
        sigCtx.beginPath();
        sigCtx.moveTo(p.x, p.y);
        e.preventDefault();
    }
    function moveDraw(e) {
        if (!drawing) return;
        var p = padPos(e);
        sigCtx.lineTo(p.x, p.y);
        sigCtx.stroke();
        e.preventDefault();
    }
    function endDraw() { drawing = false; }
    sigPad.addEventListener('mousedown', startDraw);
    sigPad.addEventListener('mousemove', moveDraw);
    window.addEventListener('mouseup', endDraw);
    sigPad.addEventListener('touchstart', startDraw, { passive: false });
    sigPad.addEventListener('touchmove', moveDraw, { passive: false });
    sigPad.addEventListener('touchend', endDraw);
    clearSigBtn.addEventListener('click', function () {
        sigCtx.clearRect(0, 0, sigPad.width, sigPad.height);
        hasDrawn = false;
    });

    tabDraw.addEventListener('click', function () {
        sigMode = 'draw';
        tabDraw.classList.add('active');
        tabType.classList.remove('active');
        drawPane.classList.remove('d-none');
        typePane.classList.add('d-none');
    });
    tabType.addEventListener('click', function () {
        sigMode = 'type';
        tabType.classList.add('active');
        tabDraw.classList.remove('active');
        typePane.classList.remove('d-none');
        drawPane.classList.add('d-none');
    });
    sizeRange.addEventListener('input', function () { sizeLabel.textContent = sizeRange.value + '%'; });

    function trimSignatureCanvas(src) {
        // The pad is mostly transparent — embedding it whole makes the actual
        // signature appear tiny inside a big invisible box. Crop to the ink.
        var w = src.width;
        var h = src.height;
        var data;
        try {
            data = src.getContext('2d').getImageData(0, 0, w, h).data;
        } catch (e) {
            return src;
        }
        var minX = w, minY = h, maxX = -1, maxY = -1;
        for (var y = 0; y < h; y++) {
            for (var x = 0; x < w; x++) {
                if (data[(y * w + x) * 4 + 3] > 0) {
                    if (x < minX) minX = x;
                    if (x > maxX) maxX = x;
                    if (y < minY) minY = y;
                    if (y > maxY) maxY = y;
                }
            }
        }
        if (maxX < 0) return null;
        var pad = 6;
        minX = Math.max(0, minX - pad);
        minY = Math.max(0, minY - pad);
        maxX = Math.min(w - 1, maxX + pad);
        maxY = Math.min(h - 1, maxY + pad);
        var out = document.createElement('canvas');
        out.width = maxX - minX + 1;
        out.height = maxY - minY + 1;
        out.getContext('2d').drawImage(src, minX, minY, out.width, out.height, 0, 0, out.width, out.height);
        return out;
    }
    function getSignatureCanvas() {
        if (sigMode === 'type') {
            var name = typedName.value.trim();
            if (!name) return null;
            var c = document.createElement('canvas');
            var ctx = c.getContext('2d');
            var font = '64px "Segoe Script", "Brush Script MT", cursive';
            ctx.font = font;
            var w = Math.ceil(ctx.measureText(name).width) + 24;
            c.width = w;
            c.height = 100;
            ctx = c.getContext('2d');
            ctx.font = font;
            ctx.fillStyle = '#1a1a8c';
            ctx.textBaseline = 'middle';
            ctx.fillText(name, 12, 54);
            return c;
        }
        if (!hasDrawn) return null;
        return trimSignatureCanvas(sigPad);
    }

    async function renderPreview() {
        if (!pdfJsDoc) return;
        try {
            var pageNum = parseInt(pageSel.value, 10) || 1;
            var page = await pdfJsDoc.getPage(pageNum);
            var viewport = page.getViewport({ scale: 1.2 });
            previewCanvas.width = viewport.width;
            previewCanvas.height = viewport.height;
            await page.render({ canvasContext: previewCanvas.getContext('2d'), viewport: viewport }).promise;
        } catch (err) {
            console.error(err);
        }
    }
    pageSel.addEventListener('change', renderPreview);

    async function loadFile(file) {
        hideAlerts();
        if (!file) return;
        if (!(file.type === 'application/pdf' || file.name.toLowerCase().endsWith('.pdf'))) {
            showError('Please select a valid PDF file.');
            return;
        }
        try {
            var buf = await file.arrayBuffer();
            storedBuffer = buf;
            storedName = file.name.replace(/\.pdf$/i, '') || 'document';
            if (typeof pdfjsLib !== 'undefined') {
                pdfJsDoc = await pdfjsLib.getDocument({ data: buf.slice(0) }).promise;
                totalPages = pdfJsDoc.numPages;
            } else {
                var doc = await PDFLib.PDFDocument.load(buf.slice(0));
                totalPages = doc.getPageCount();
                pdfJsDoc = null;
            }
            pageSel.innerHTML = '';
            for (var i = 1; i <= totalPages; i++) {
                var opt = document.createElement('option');
                opt.value = i;
                opt.textContent = 'Page ' + i + ' of ' + totalPages;
                if (i === totalPages) opt.selected = true;
                pageSel.appendChild(opt);
            }
            fileNameEl.textContent = file.name + ' (' + formatSize(file.size) + ')';
            pageCountInfo.textContent = totalPages + (totalPages === 1 ? ' page' : ' pages');
            toolWrap.classList.remove('d-none');
            showSuccess('PDF loaded — ' + totalPages + ' page(s) found. Now create your signature.');
            renderPreview();
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
        pdfJsDoc = null;
        toolWrap.classList.add('d-none');
        hideAlerts();
    });

    signBtn.addEventListener('click', async function () {
        hideAlerts();
        if (!storedBuffer) { showError('Please select a PDF file first.'); return; }
        if (typeof PDFLib === 'undefined') { showError('PDF library failed to load. Please check your internet connection and try again.'); return; }
        var sigCanvas = getSignatureCanvas();
        if (!sigCanvas) {
            showError(sigMode === 'type' ? 'Please type your name first.' : 'Please draw your signature first.');
            return;
        }
        signBtn.disabled = true;
        signBtn.textContent = 'Signing...';
        try {
            var pngBytes = await new Promise(function (resolve, reject) {
                sigCanvas.toBlob(function (b) {
                    if (!b) { reject(new Error('Could not read the signature image.')); return; }
                    b.arrayBuffer().then(resolve, reject);
                }, 'image/png');
            });
            var pdfDoc = await PDFLib.PDFDocument.load(storedBuffer.slice(0));
            var sigImg = await pdfDoc.embedPng(pngBytes);
            var pageIdx = (parseInt(pageSel.value, 10) || 1) - 1;
            var page = pdfDoc.getPages()[pageIdx];
            var size = page.getSize();
            var sigW = size.width * (parseInt(sizeRange.value, 10) / 100);
            var sigH = sigW * (sigCanvas.height / sigCanvas.width);
            var margin = 36;
            var x, y;
            var pos = posSel.value;
            if (pos === 'bottom-left') { x = margin; y = margin; }
            else if (pos === 'bottom-center') { x = (size.width - sigW) / 2; y = margin; }
            else if (pos === 'top-right') { x = size.width - margin - sigW; y = size.height - margin - sigH; }
            else if (pos === 'center') { x = (size.width - sigW) / 2; y = (size.height - sigH) / 2; }
            else { x = size.width - margin - sigW; y = margin; }
            page.drawImage(sigImg, { x: x, y: y, width: sigW, height: sigH });
            var out = await pdfDoc.save();
            var blob = new Blob([out], { type: 'application/pdf' });
            downloadBlob(blob, storedName + '-signed.pdf');
            showSuccess('Done! Your signed PDF (' + formatSize(blob.size) + ') has been downloaded.');
        } catch (err) {
            console.error(err);
            showError('Could not sign this PDF. It may be corrupted or password-protected. Please try a different file.');
        } finally {
            signBtn.disabled = false;
            signBtn.textContent = 'Sign & Download PDF';
        }
    });
})();
</script>
@endsection
