@extends('layouts.app')
@section('title', 'Organize PDF Pages Online Free — Azlaan Tools')
@section('meta_description', 'Reorder, rotate and delete PDF pages online for free, or extract selected pages into a new PDF. No signup, no upload — files stay in your browser.')
@section('content')
<div class="container py-4 pdf-tool">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Organize PDF</h1>
            <p class="lead text-muted">Rearrange pages in any order, rotate or delete pages, or extract only the pages you need into a fresh PDF.</p>

            <div id="alertBox" class="alert alert-danger d-none" role="alert"></div>
            <div id="successBox" class="alert alert-success d-none" role="alert"></div>

            <div id="dropZone" class="border border-2 border-dashed rounded-3 p-4 p-md-5 text-center mb-3" style="border-style: dashed !important; cursor: pointer; background: #f8f9fa;">
                <div class="fs-1 mb-2">🗂️</div>
                <p class="mb-1 fw-semibold">Drag &amp; drop a PDF here</p>
                <p class="text-muted mb-3">or click to browse from your device</p>
                <button type="button" class="btn btn-primary">Select PDF File</button>
                <input type="file" id="fileInput" accept="application/pdf,.pdf" class="d-none">
            </div>

            <div id="toolWrap" class="d-none">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <p class="mb-3">File: <strong id="fileName" class="text-break"></strong></p>
                        <div class="mb-3">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="modeRadio" id="modeAll" value="all" checked>
                                <label class="form-check-label" for="modeAll">Download all pages in the new order</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="modeRadio" id="modeExtract" value="extract">
                                <label class="form-check-label" for="modeExtract">Extract ticked pages only</label>
                            </div>
                        </div>
                        <div id="pageList" class="row g-3 mb-3"></div>
                        <div class="d-flex flex-wrap gap-2">
                            <button type="button" id="processBtn" class="btn btn-success btn-lg">Create Organized PDF</button>
                            <button type="button" id="clearBtn" class="btn btn-outline-secondary">Clear</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="alert alert-info mt-4">
                <strong>Privacy note:</strong> Your file never leaves your browser — everything happens on your phone/computer, nothing is uploaded.
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Click the box above or drag and drop your PDF into it — every page appears as a card.</li>
                <li>Use the ↑ ↓ buttons to reorder pages, ⟳ to rotate a page, and Delete to remove it.</li>
                <li>To keep only some pages, switch to <strong>Extract ticked pages only</strong> and tick the pages you want.</li>
                <li>Click <strong>Create Organized PDF</strong> and your new file downloads automatically.</li>
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
    var pageList = document.getElementById('pageList');
    var processBtn = document.getElementById('processBtn');
    var clearBtn = document.getElementById('clearBtn');
    var storedBuffer = null;
    var storedName = 'document';
    var items = [];

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

    function renderList() {
        pageList.innerHTML = '';
        items.forEach(function (item, idx) {
            var col = document.createElement('div');
            col.className = 'col-6 col-md-4 col-lg-3';
            var card = document.createElement('div');
            card.className = 'card h-100';
            var img = document.createElement('img');
            img.src = item.thumb;
            img.alt = 'Page ' + item.srcPage;
            img.className = 'card-img-top bg-white';
            img.style.transform = 'rotate(' + item.rotation + 'deg)';
            card.appendChild(img);
            var body = document.createElement('div');
            body.className = 'card-body p-2';
            var title = document.createElement('p');
            title.className = 'small fw-semibold mb-2 text-center';
            title.textContent = 'Position ' + (idx + 1) + ' — original page ' + item.srcPage + (item.rotation ? ' (' + item.rotation + '°)' : '');
            body.appendChild(title);
            var chkWrap = document.createElement('div');
            chkWrap.className = 'form-check mb-2';
            var chk = document.createElement('input');
            chk.type = 'checkbox';
            chk.className = 'form-check-input';
            chk.checked = item.selected;
            chk.addEventListener('change', function () { item.selected = chk.checked; });
            var chkLabel = document.createElement('label');
            chkLabel.className = 'form-check-label small';
            chkLabel.textContent = 'Include in extract';
            chkWrap.appendChild(chk);
            chkWrap.appendChild(chkLabel);
            body.appendChild(chkWrap);
            var row = document.createElement('div');
            row.className = 'd-flex gap-1 flex-wrap justify-content-center';
            var upBtn = document.createElement('button');
            upBtn.type = 'button';
            upBtn.className = 'btn btn-sm btn-outline-primary';
            upBtn.textContent = '↑';
            upBtn.disabled = idx === 0;
            upBtn.addEventListener('click', function () {
                var t = items[idx - 1]; items[idx - 1] = items[idx]; items[idx] = t;
                renderList();
            });
            row.appendChild(upBtn);
            var downBtn = document.createElement('button');
            downBtn.type = 'button';
            downBtn.className = 'btn btn-sm btn-outline-primary';
            downBtn.textContent = '↓';
            downBtn.disabled = idx === items.length - 1;
            downBtn.addEventListener('click', function () {
                var t = items[idx + 1]; items[idx + 1] = items[idx]; items[idx] = t;
                renderList();
            });
            row.appendChild(downBtn);
            var rotBtn = document.createElement('button');
            rotBtn.type = 'button';
            rotBtn.className = 'btn btn-sm btn-outline-secondary';
            rotBtn.textContent = '⟳ Rotate';
            rotBtn.addEventListener('click', function () {
                item.rotation = (item.rotation + 90) % 360;
                renderList();
            });
            row.appendChild(rotBtn);
            var delBtn = document.createElement('button');
            delBtn.type = 'button';
            delBtn.className = 'btn btn-sm btn-outline-danger';
            delBtn.textContent = 'Delete';
            delBtn.addEventListener('click', function () {
                items.splice(idx, 1);
                renderList();
            });
            row.appendChild(delBtn);
            body.appendChild(row);
            card.appendChild(body);
            col.appendChild(card);
            pageList.appendChild(col);
        });
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
            items = [];
            var pdf = await pdfjsLib.getDocument({ data: buf.slice(0) }).promise;
            fileNameEl.textContent = file.name + ' (' + formatSize(file.size) + ') — ' + pdf.numPages + ' page(s)';
            for (var i = 1; i <= pdf.numPages; i++) {
                var page = await pdf.getPage(i);
                var viewport = page.getViewport({ scale: 0.35 });
                var canvas = document.createElement('canvas');
                canvas.width = viewport.width;
                canvas.height = viewport.height;
                await page.render({ canvasContext: canvas.getContext('2d'), viewport: viewport }).promise;
                items.push({ srcPage: i, rotation: 0, selected: true, thumb: canvas.toDataURL('image/png') });
            }
            renderList();
            toolWrap.classList.remove('d-none');
            showSuccess('PDF loaded — ' + pdf.numPages + ' page(s). Reorder, rotate or delete pages below.');
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
        items = [];
        renderList();
        toolWrap.classList.add('d-none');
        hideAlerts();
    });

    processBtn.addEventListener('click', async function () {
        hideAlerts();
        if (!storedBuffer) { showError('Please select a PDF file first.'); return; }
        if (items.length === 0) { showError('No pages left — please reload the PDF or keep at least one page.'); return; }
        var mode = document.getElementById('modeExtract').checked ? 'extract' : 'all';
        var chosen = mode === 'extract' ? items.filter(function (it) { return it.selected; }) : items;
        if (chosen.length === 0) { showError('Please tick at least one page to extract.'); return; }
        processBtn.disabled = true;
        processBtn.textContent = 'Processing...';
        try {
            var src = await PDFLib.PDFDocument.load(storedBuffer.slice(0));
            var out = await PDFLib.PDFDocument.create();
            var indices = chosen.map(function (it) { return it.srcPage - 1; });
            var copied = await out.copyPages(src, indices);
            copied.forEach(function (p, i) {
                if (chosen[i].rotation) {
                    var current = 0;
                    try { current = p.getRotation().angle || 0; } catch (e) { current = 0; }
                    p.setRotation(PDFLib.degrees((current + chosen[i].rotation) % 360));
                }
                out.addPage(p);
            });
            var bytes = await out.save();
            var blob = new Blob([bytes], { type: 'application/pdf' });
            var url = URL.createObjectURL(blob);
            var a = document.createElement('a');
            a.href = url;
            a.download = storedName + (mode === 'extract' ? '-extracted.pdf' : '-organized.pdf');
            document.body.appendChild(a);
            a.click();
            a.remove();
            setTimeout(function () { URL.revokeObjectURL(url); }, 4000);
            showSuccess('Done! Your PDF with ' + chosen.length + ' page(s) (' + formatSize(blob.size) + ') has been downloaded.');
        } catch (err) {
            console.error(err);
            showError('Could not process this PDF. It may be corrupted or password-protected. Please try a different file.');
        } finally {
            processBtn.disabled = false;
            processBtn.textContent = 'Create Organized PDF';
        }
    });
})();
</script>
@endsection
