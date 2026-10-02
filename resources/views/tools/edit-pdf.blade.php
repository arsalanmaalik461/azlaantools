@extends('layouts.app')
@section('title', 'Edit PDF Online Free — Add Text & Images — Azlaan Tools')
@section('meta_description', 'Edit a PDF online for free: add text, images, whiteout blocks and freehand drawing on any page, then download. No signup, no upload — files stay in your browser.')
@section('content')
<div class="container py-4 pdf-tool">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Edit PDF</h1>
            <p class="lead text-muted">Add text, images, whiteout blocks and drawings on top of your PDF pages, then download the edited file.</p>
            <div class="alert alert-warning">
                <strong>Honest note:</strong> Add text &amp; images — existing text inside the PDF cannot be changed. To hide old text, cover it with a whiteout block and type new text on top. Pages are rebuilt as high-quality images, so text in the downloaded file is no longer selectable.
            </div>

            <div id="alertBox" class="alert alert-danger d-none" role="alert"></div>
            <div id="successBox" class="alert alert-success d-none" role="alert"></div>

            <div id="dropZone" class="border border-2 border-dashed rounded-3 p-4 p-md-5 text-center mb-3" style="border-style: dashed !important; cursor: pointer; background: #f8f9fa;">
                <div class="fs-1 mb-2">🖊️</div>
                <p class="mb-1 fw-semibold">Drag &amp; drop a PDF here</p>
                <p class="text-muted mb-3">or click to browse from your device</p>
                <button type="button" class="btn btn-primary">Select PDF File</button>
                <input type="file" id="fileInput" accept="application/pdf,.pdf" class="d-none">
            </div>

            <div id="toolWrap" class="d-none">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <p class="mb-3">File: <strong id="fileName" class="text-break"></strong></p>

                        <div class="btn-group flex-wrap mb-3" role="group" aria-label="Tools">
                            <button type="button" class="btn btn-outline-primary tool-btn active" data-tool="text">T — Add Text</button>
                            <button type="button" class="btn btn-outline-primary tool-btn" data-tool="image">🖼️ Add Image</button>
                            <button type="button" class="btn btn-outline-primary tool-btn" data-tool="whiteout">⬜ Whiteout</button>
                            <button type="button" class="btn btn-outline-primary tool-btn" data-tool="draw">✏️ Draw</button>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-5">
                                <label for="textInput" class="form-label fw-semibold">Text to add</label>
                                <input type="text" id="textInput" class="form-control form-control-lg" placeholder="Type text, then click on the page">
                            </div>
                            <div class="col-md-2">
                                <label for="fontSizeInput" class="form-label fw-semibold">Text size</label>
                                <input type="number" id="fontSizeInput" class="form-control form-control-lg" value="20" min="8" max="80">
                            </div>
                            <div class="col-md-2">
                                <label for="colorInput" class="form-label fw-semibold">Colour</label>
                                <input type="color" id="colorInput" class="form-control form-control-color w-100" value="#000000">
                            </div>
                            <div class="col-md-3">
                                <label for="imgInput" class="form-label fw-semibold">Image to add</label>
                                <input type="file" id="imgInput" accept="image/*" class="form-control">
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-2 mb-2">
                            <button type="button" id="prevPageBtn" class="btn btn-outline-secondary">← Prev</button>
                            <span id="pageInfo" class="fw-semibold">Page 1 of 1</span>
                            <button type="button" id="nextPageBtn" class="btn btn-outline-secondary">Next →</button>
                            <button type="button" id="undoBtn" class="btn btn-outline-danger ms-auto">Undo Last</button>
                        </div>

                        <canvas id="editCanvas" class="border rounded w-100 bg-white" style="touch-action: none; cursor: crosshair;"></canvas>
                        <p class="form-text" id="toolHint">Text tool: type your text above, then click on the page where it should appear.</p>

                        <div class="d-flex flex-wrap gap-2 mt-3">
                            <button type="button" id="downloadBtn" class="btn btn-success btn-lg">Download Edited PDF</button>
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
                <li>Click the box above or drag and drop your PDF into it.</li>
                <li>Pick a tool: <strong>Add Text</strong> (type, then click the page), <strong>Add Image</strong> (choose an image, then click the page), <strong>Whiteout</strong> (drag a box to cover something), or <strong>Draw</strong> (drag to draw freehand).</li>
                <li>Move between pages with Prev / Next and edit each page. Use <strong>Undo Last</strong> to remove the last change on the current page.</li>
                <li>Click <strong>Download Edited PDF</strong> when you are finished.</li>
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
    var SCALE = 1.5;
    var dropZone = document.getElementById('dropZone');
    var fileInput = document.getElementById('fileInput');
    var alertBox = document.getElementById('alertBox');
    var successBox = document.getElementById('successBox');
    var toolWrap = document.getElementById('toolWrap');
    var fileNameEl = document.getElementById('fileName');
    var textInput = document.getElementById('textInput');
    var fontSizeInput = document.getElementById('fontSizeInput');
    var colorInput = document.getElementById('colorInput');
    var imgInput = document.getElementById('imgInput');
    var editCanvas = document.getElementById('editCanvas');
    var pageInfo = document.getElementById('pageInfo');
    var prevPageBtn = document.getElementById('prevPageBtn');
    var nextPageBtn = document.getElementById('nextPageBtn');
    var undoBtn = document.getElementById('undoBtn');
    var downloadBtn = document.getElementById('downloadBtn');
    var clearBtn = document.getElementById('clearBtn');
    var toolHint = document.getElementById('toolHint');
    var toolBtns = document.querySelectorAll('.tool-btn');

    var pdfDoc = null;
    var storedName = 'document';
    var totalPages = 0;
    var currentPage = 1;
    var baseCanvas = null;
    var pageSizes = {};
    var annotations = {};
    var activeTool = 'text';
    var pendingImage = null;
    var dragStart = null;
    var dragCurrent = null;
    var drawingPath = null;

    var hints = {
        text: 'Text tool: type your text above, then click on the page where it should appear.',
        image: 'Image tool: choose an image above, then click on the page to place it.',
        whiteout: 'Whiteout tool: drag a rectangle over the area you want to cover with white.',
        draw: 'Draw tool: drag on the page to draw freehand.'
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
    function pageAnnotations() {
        if (!annotations[currentPage]) annotations[currentPage] = [];
        return annotations[currentPage];
    }

    toolBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            toolBtns.forEach(function (b) { b.classList.remove('active'); });
            btn.classList.add('active');
            activeTool = btn.getAttribute('data-tool');
            toolHint.textContent = hints[activeTool];
        });
    });
    imgInput.addEventListener('change', function () {
        if (imgInput.files && imgInput.files[0]) {
            var url = URL.createObjectURL(imgInput.files[0]);
            var img = new Image();
            img.onload = function () { pendingImage = img; };
            img.src = url;
        }
    });

    function drawOp(ctx, op) {
        if (op.type === 'text') {
            ctx.font = 'bold ' + op.size + 'px Arial, sans-serif';
            ctx.fillStyle = op.color;
            ctx.textBaseline = 'top';
            ctx.fillText(op.text, op.x, op.y);
        } else if (op.type === 'image') {
            ctx.drawImage(op.img, op.x, op.y, op.w, op.h);
        } else if (op.type === 'rect') {
            ctx.fillStyle = '#ffffff';
            ctx.fillRect(op.x, op.y, op.w, op.h);
        } else if (op.type === 'path') {
            ctx.strokeStyle = op.color;
            ctx.lineWidth = op.width;
            ctx.lineCap = 'round';
            ctx.lineJoin = 'round';
            ctx.beginPath();
            op.points.forEach(function (pt, i) {
                if (i === 0) ctx.moveTo(pt.x, pt.y); else ctx.lineTo(pt.x, pt.y);
            });
            ctx.stroke();
        }
    }
    function redraw() {
        if (!baseCanvas) return;
        var ctx = editCanvas.getContext('2d');
        ctx.clearRect(0, 0, editCanvas.width, editCanvas.height);
        ctx.drawImage(baseCanvas, 0, 0);
        pageAnnotations().forEach(function (op) { drawOp(ctx, op); });
        if (dragStart && dragCurrent && activeTool === 'whiteout') {
            ctx.fillStyle = 'rgba(255,255,255,0.85)';
            ctx.strokeStyle = '#999';
            var x = Math.min(dragStart.x, dragCurrent.x);
            var y = Math.min(dragStart.y, dragCurrent.y);
            var w = Math.abs(dragCurrent.x - dragStart.x);
            var h = Math.abs(dragCurrent.y - dragStart.y);
            ctx.fillRect(x, y, w, h);
            ctx.strokeRect(x, y, w, h);
        }
        if (drawingPath) drawOp(ctx, drawingPath);
    }

    async function showPage(num) {
        if (!pdfDoc) return;
        currentPage = num;
        var page = await pdfDoc.getPage(num);
        var viewport = page.getViewport({ scale: SCALE });
        var off = document.createElement('canvas');
        off.width = viewport.width;
        off.height = viewport.height;
        await page.render({ canvasContext: off.getContext('2d'), viewport: viewport }).promise;
        baseCanvas = off;
        var p1 = page.getViewport({ scale: 1 });
        pageSizes[num] = { width: p1.width, height: p1.height };
        editCanvas.width = off.width;
        editCanvas.height = off.height;
        pageInfo.textContent = 'Page ' + num + ' of ' + totalPages;
        prevPageBtn.disabled = num <= 1;
        nextPageBtn.disabled = num >= totalPages;
        redraw();
    }
    prevPageBtn.addEventListener('click', function () { if (currentPage > 1) showPage(currentPage - 1); });
    nextPageBtn.addEventListener('click', function () { if (currentPage < totalPages) showPage(currentPage + 1); });
    undoBtn.addEventListener('click', function () {
        pageAnnotations().pop();
        redraw();
    });

    function canvasPos(e) {
        var rect = editCanvas.getBoundingClientRect();
        var cx = (e.touches ? e.touches[0].clientX : e.clientX) - rect.left;
        var cy = (e.touches ? e.touches[0].clientY : e.clientY) - rect.top;
        return { x: cx * (editCanvas.width / rect.width), y: cy * (editCanvas.height / rect.height) };
    }
    function onDown(e) {
        if (!baseCanvas) return;
        var p = canvasPos(e);
        if (activeTool === 'text') {
            var txt = textInput.value.trim();
            if (!txt) { showError('Type the text in the box above first, then click on the page.'); return; }
            hideAlerts();
            pageAnnotations().push({ type: 'text', x: p.x, y: p.y, text: txt, size: parseInt(fontSizeInput.value, 10) || 20, color: colorInput.value });
            redraw();
        } else if (activeTool === 'image') {
            if (!pendingImage) { showError('Choose an image above first, then click on the page to place it.'); return; }
            hideAlerts();
            var w = 220;
            var h = w * (pendingImage.naturalHeight / pendingImage.naturalWidth);
            pageAnnotations().push({ type: 'image', x: p.x, y: p.y, w: w, h: h, img: pendingImage });
            redraw();
        } else if (activeTool === 'whiteout') {
            dragStart = p;
            dragCurrent = p;
        } else if (activeTool === 'draw') {
            drawingPath = { type: 'path', points: [p], color: colorInput.value, width: 3 };
        }
        e.preventDefault();
    }
    function onMove(e) {
        var p = canvasPos(e);
        if (dragStart) { dragCurrent = p; redraw(); }
        else if (drawingPath) { drawingPath.points.push(p); redraw(); }
    }
    function onUp(e) {
        if (dragStart && dragCurrent) {
            var x = Math.min(dragStart.x, dragCurrent.x);
            var y = Math.min(dragStart.y, dragCurrent.y);
            var w = Math.abs(dragCurrent.x - dragStart.x);
            var h = Math.abs(dragCurrent.y - dragStart.y);
            if (w > 4 && h > 4) pageAnnotations().push({ type: 'rect', x: x, y: y, w: w, h: h });
            dragStart = null;
            dragCurrent = null;
            redraw();
        }
        if (drawingPath) {
            if (drawingPath.points.length > 1) pageAnnotations().push(drawingPath);
            drawingPath = null;
            redraw();
        }
    }
    editCanvas.addEventListener('mousedown', onDown);
    editCanvas.addEventListener('mousemove', onMove);
    window.addEventListener('mouseup', onUp);
    editCanvas.addEventListener('touchstart', onDown, { passive: false });
    editCanvas.addEventListener('touchmove', onMove, { passive: false });
    editCanvas.addEventListener('touchend', onUp);

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
            pdfDoc = await pdfjsLib.getDocument({ data: buf }).promise;
            totalPages = pdfDoc.numPages;
            storedName = file.name.replace(/\.pdf$/i, '') || 'document';
            annotations = {};
            pageSizes = {};
            fileNameEl.textContent = file.name + ' (' + formatSize(file.size) + ') — ' + totalPages + ' page(s)';
            toolWrap.classList.remove('d-none');
            await showPage(1);
            showSuccess('PDF loaded. Choose a tool and click on the page to start editing.');
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
        annotations = {};
        toolWrap.classList.add('d-none');
        hideAlerts();
    });

    downloadBtn.addEventListener('click', async function () {
        hideAlerts();
        if (!pdfDoc) { showError('Please select a PDF file first.'); return; }
        downloadBtn.disabled = true;
        downloadBtn.textContent = 'Building PDF...';
        try {
            var out = await PDFLib.PDFDocument.create();
            for (var n = 1; n <= totalPages; n++) {
                var page = await pdfDoc.getPage(n);
                var viewport = page.getViewport({ scale: SCALE });
                var off = document.createElement('canvas');
                off.width = viewport.width;
                off.height = viewport.height;
                await page.render({ canvasContext: off.getContext('2d'), viewport: viewport }).promise;
                var ctx = off.getContext('2d');
                (annotations[n] || []).forEach(function (op) { drawOp(ctx, op); });
                var blob = await new Promise(function (resolve) { off.toBlob(resolve, 'image/png'); });
                var imgBytes = await blob.arrayBuffer();
                var embedded = await out.embedPng(imgBytes);
                var p1 = page.getViewport({ scale: 1 });
                var newPage = out.addPage([p1.width, p1.height]);
                newPage.drawImage(embedded, { x: 0, y: 0, width: p1.width, height: p1.height });
            }
            var bytes = await out.save();
            var outBlob = new Blob([bytes], { type: 'application/pdf' });
            var url = URL.createObjectURL(outBlob);
            var a = document.createElement('a');
            a.href = url;
            a.download = storedName + '-edited.pdf';
            document.body.appendChild(a);
            a.click();
            a.remove();
            setTimeout(function () { URL.revokeObjectURL(url); }, 4000);
            showSuccess('Done! Your edited PDF (' + formatSize(outBlob.size) + ') has been downloaded.');
        } catch (err) {
            console.error(err);
            showError('Could not build the edited PDF. Please try again with a smaller file.');
        } finally {
            downloadBtn.disabled = false;
            downloadBtn.textContent = 'Download Edited PDF';
        }
    });
})();
</script>
@endsection
