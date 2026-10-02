@extends('layouts.app')
@section('title', 'Extract PDF Pages - Azlaan Tools')
@section('meta_description', 'Pull selected pages out of a PDF online for free. Tick pages or type a range, then download the extracted PDF. No upload — files stay in your browser.')

@section('content')
<div class="container py-4 pdf-tool">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Extract PDF Pages</h1>
            <p class="lead text-muted">Pull out the pages you want from a PDF and make a new PDF. Tick the pages or type a range — the file is processed in your browser only.</p>

            <div id="alertBox" class="alert alert-danger d-none" role="alert"></div>
            <div id="successBox" class="alert alert-success d-none" role="alert"></div>

            <div id="dropZone" class="border border-2 rounded-3 p-4 p-md-5 text-center mb-3" style="border-style: dashed !important; cursor: pointer; background: #f8f9fa;">
                <div class="fs-1 mb-2">📄</div>
                <p class="mb-1 fw-semibold">Drag &amp; drop a PDF here</p>
                <p class="text-muted mb-3">or click to browse from your device</p>
                <button type="button" class="btn btn-primary">Select PDF File</button>
                <input type="file" id="fileInput" accept="application/pdf,.pdf" class="d-none">
            </div>

            <div id="toolWrap" class="d-none">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <p class="mb-2">File: <strong id="fileName" class="text-break"></strong> (<span id="pageCount"></span> pages)</p>
                        <p class="mb-2"><span id="selInfo" class="fw-semibold">0 pages selected</span></p>

                        <div class="mb-3">
                            <label for="rangeInput" class="form-label fw-semibold">Or type a page range</label>
                            <input type="text" class="form-control" id="rangeInput" placeholder="e.g. 1,3,5-8">
                            <div class="form-text">Separate with commas, use a dash for a range: <code>1,3,5-8</code></div>
                        </div>

                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <button type="button" id="applyRangeBtn" class="btn btn-outline-primary btn-sm">Apply Range</button>
                            <button type="button" id="selectAllBtn" class="btn btn-outline-secondary btn-sm">Select All</button>
                            <button type="button" id="clearSelBtn" class="btn btn-outline-secondary btn-sm">Clear Selection</button>
                        </div>

                        <div id="thumbGrid" class="row g-2 mb-3" style="max-height: 420px; overflow-y: auto;"></div>

                        <div class="d-flex flex-wrap gap-2">
                            <button type="button" id="extractBtn" class="btn btn-success btn-lg">Extract &amp; Download</button>
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
                <li>Tick the pages you want in the new PDF, or type a range and press <strong>Apply Range</strong>.</li>
                <li>Press <strong>Extract &amp; Download</strong> — only a new PDF with the selected pages will be downloaded.</li>
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
    'use strict';
    if (typeof pdfjsLib !== 'undefined') {
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdn.jsdelivr.net/npm/pdfjs-dist@3.11.174/build/pdf.worker.min.js';
    }
    var dropZone = document.getElementById('dropZone');
    var fileInput = document.getElementById('fileInput');
    var alertBox = document.getElementById('alertBox');
    var successBox = document.getElementById('successBox');
    var toolWrap = document.getElementById('toolWrap');
    var fileNameEl = document.getElementById('fileName');
    var pageCountEl = document.getElementById('pageCount');
    var selInfo = document.getElementById('selInfo');
    var thumbGrid = document.getElementById('thumbGrid');
    var rangeInput = document.getElementById('rangeInput');
    var applyRangeBtn = document.getElementById('applyRangeBtn');
    var selectAllBtn = document.getElementById('selectAllBtn');
    var clearSelBtn = document.getElementById('clearSelBtn');
    var extractBtn = document.getElementById('extractBtn');
    var clearBtn = document.getElementById('clearBtn');

    var storedBuffer = null, storedName = 'document', totalPages = 0;
    var MAX_THUMBS = 60;

    function showError(msg) {
        alertBox.textContent = msg;
        alertBox.classList.remove('d-none');
        successBox.classList.add('d-none');
    }
    function hideMsg() {
        alertBox.classList.add('d-none');
        alertBox.textContent = '';
        successBox.classList.add('d-none');
    }
    function showSuccess(msg) {
        successBox.textContent = msg;
        successBox.classList.remove('d-none');
        alertBox.classList.add('d-none');
    }

    function selectedPages() {
        var boxes = thumbGrid.querySelectorAll('.page-chk'), out = [], i;
        for (i = 0; i < boxes.length; i++) {
            if (boxes[i].checked) out.push(parseInt(boxes[i].value, 10));
        }
        return out.sort(function (a, b) { return a - b; });
    }
    function updateSelInfo() {
        var n = selectedPages().length;
        selInfo.textContent = n + ' page' + (n === 1 ? '' : 's') + ' selected';
    }
    function setChecked(pages, on) {
        var boxes = thumbGrid.querySelectorAll('.page-chk'), i;
        for (i = 0; i < boxes.length; i++) {
            if (pages.indexOf(parseInt(boxes[i].value, 10)) !== -1) boxes[i].checked = on;
        }
        updateSelInfo();
    }

    function parseRange(text) {
        var out = [], parts = text.split(','), i, p, dash, a, b, j;
        for (i = 0; i < parts.length; i++) {
            p = parts[i].trim();
            if (!p) continue;
            dash = p.indexOf('-');
            if (dash === -1) {
                a = parseInt(p, 10);
                if (a >= 1 && a <= totalPages) out.push(a);
            } else {
                a = parseInt(p.slice(0, dash).trim(), 10);
                b = parseInt(p.slice(dash + 1).trim(), 10);
                if (!(a >= 1) || !(b >= 1)) continue;
                if (a > b) { j = a; a = b; b = j; }
                for (j = a; j <= b && j <= totalPages; j++) out.push(j);
            }
        }
        out = out.filter(function (v, idx) { return out.indexOf(v) === idx; });
        return out.sort(function (x, y) { return x - y; });
    }

    function renderThumbs(pdf) {
        thumbGrid.innerHTML = '';
        var n = Math.min(totalPages, MAX_THUMBS), i;
        for (i = 1; i <= n; i++) {
            (function (pageNum) {
                var col = document.createElement('div');
                col.className = 'col-4 col-md-3';
                col.innerHTML =
                    '<label class="d-block border rounded p-1 text-center" style="cursor:pointer;">' +
                    '<canvas class="img-fluid w-100" style="background:#fff;"></canvas>' +
                    '<div class="form-check d-flex justify-content-center gap-1 mt-1">' +
                    '<input type="checkbox" class="form-check-input page-chk" value="' + pageNum + '">' +
                    '<span class="small">Page ' + pageNum + '</span></div></label>';
                var canvas = col.querySelector('canvas');
                thumbGrid.appendChild(col);
                pdf.getPage(pageNum).then(function (page) {
                    var vp = page.getViewport({ scale: 0.35 });
                    canvas.width = vp.width;
                    canvas.height = vp.height;
                    page.render({ canvasContext: canvas.getContext('2d'), viewport: vp });
                });
                col.querySelector('.page-chk').addEventListener('change', updateSelInfo);
            })(i);
        }
        if (totalPages > MAX_THUMBS) {
            var note = document.createElement('div');
            note.className = 'col-12';
            note.innerHTML = '<div class="alert alert-warning small mb-0">Only the first ' + MAX_THUMBS + ' pages are shown as previews — use the range box for the remaining pages (it works on all pages).</div>';
            thumbGrid.appendChild(note);
        }
        updateSelInfo();
    }

    function loadFile(file) {
        hideMsg();
        if (!/\.pdf$/i.test(file.name) && file.type !== 'application/pdf') {
            showError('Please select only a PDF file.');
            return;
        }
        var reader = new FileReader();
        reader.onload = function () {
            storedBuffer = reader.result;
            storedName = file.name.replace(/\.pdf$/i, '');
            if (typeof pdfjsLib === 'undefined') { showError('The PDF library did not load. Check your internet and try again.'); return; }
            var task = pdfjsLib.getDocument({ data: storedBuffer.slice(0) });
            task.promise.then(function (pdf) {
                totalPages = pdf.numPages;
                fileNameEl.textContent = file.name;
                pageCountEl.textContent = totalPages;
                toolWrap.classList.remove('d-none');
                renderThumbs(pdf);
                showSuccess('PDF loaded — select the pages you want.');
            }, function () {
                showError('The PDF could not be read. The file may be corrupt.');
            });
        };
        reader.readAsArrayBuffer(file);
    }

    dropZone.addEventListener('click', function () { fileInput.click(); });
    fileInput.addEventListener('change', function () { if (fileInput.files[0]) loadFile(fileInput.files[0]); });
    dropZone.addEventListener('dragover', function (e) { e.preventDefault(); });
    dropZone.addEventListener('drop', function (e) {
        e.preventDefault();
        if (e.dataTransfer.files[0]) loadFile(e.dataTransfer.files[0]);
    });

    applyRangeBtn.addEventListener('click', function () {
        hideMsg();
        var pages = parseRange(rangeInput.value);
        if (!pages.length) { showError('No valid page number found. Example: 1,3,5-8'); return; }
        setChecked(pages, true);
        showSuccess(pages.length + ' pages selected: ' + pages.join(', '));
    });
    selectAllBtn.addEventListener('click', function () {
        hideMsg();
        var boxes = thumbGrid.querySelectorAll('.page-chk'), i;
        for (i = 0; i < boxes.length; i++) boxes[i].checked = true;
        updateSelInfo();
    });
    clearSelBtn.addEventListener('click', function () {
        hideMsg();
        var boxes = thumbGrid.querySelectorAll('.page-chk'), i;
        for (i = 0; i < boxes.length; i++) boxes[i].checked = false;
        rangeInput.value = '';
        updateSelInfo();
    });

    extractBtn.addEventListener('click', function () {
        hideMsg();
        var pages = selectedPages();
        var ranged = parseRange(rangeInput.value);
        var j;
        for (j = 0; j < ranged.length; j++) {
            if (pages.indexOf(ranged[j]) === -1) pages.push(ranged[j]);
        }
        pages.sort(function (a, b) { return a - b; });
        if (!pages.length) { showError('Please select at least one page.'); return; }
        if (typeof PDFLib === 'undefined') { showError('The PDF library did not load. Check your internet and try again.'); return; }
        extractBtn.disabled = true;
        extractBtn.textContent = 'Extracting...';
        PDFLib.PDFDocument.load(storedBuffer.slice(0)).then(function (src) {
            return PDFLib.PDFDocument.create().then(function (out) {
                var idx = pages.map(function (p) { return p - 1; });
                return out.copyPages(src, idx).then(function (copied) {
                    var k;
                    for (k = 0; k < copied.length; k++) out.addPage(copied[k]);
                    return out.save();
                });
            });
        }).then(function (bytes) {
            var blob = new Blob([bytes], { type: 'application/pdf' });
            var url = URL.createObjectURL(blob);
            var a = document.createElement('a');
            a.href = url;
            a.download = storedName + '-extracted.pdf';
            document.body.appendChild(a);
            a.click();
            setTimeout(function () { document.body.removeChild(a); URL.revokeObjectURL(url); }, 800);
            showSuccess(pages.length + ' pages extracted and downloaded.');
        }).catch(function (e) {
            showError('Could not extract: ' + (e && e.message ? e.message : e));
        }).finally(function () {
            if (typeof extractBtn !== 'undefined') { /* noop guard */ }
            extractBtn.disabled = false;
            extractBtn.innerHTML = 'Extract &amp; Download';
        });
    });

    clearBtn.addEventListener('click', function () {
        toolWrap.classList.add('d-none');
        thumbGrid.innerHTML = '';
        fileInput.value = '';
        rangeInput.value = '';
        storedBuffer = null;
        totalPages = 0;
        hideMsg();
    });
})();
</script>
@endsection
