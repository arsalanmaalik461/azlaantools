@extends('layouts.app')
@section('title', 'N-Up PDF Layout - Azlaan Tools')
@section('meta_description', 'Put 2, 4, 6 or 8 PDF pages on a single sheet to save paper. Free online, runs in your browser.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">N-Up PDF Layout</h1>
            <p class="lead text-muted">Print multiple PDF pages on one sheet and save paper. Select a PDF, choose 2, 4, 6 or 8 pages per sheet, and download a new print-ready PDF.</p>

            <div class="alert alert-info">
                <strong>What will happen:</strong> Each page will shrink into one cell of the A4 sheet. Only the layout will change — it will no longer be a searchable text PDF; pages will become images (best for printing).
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="fileInput" class="form-label fw-semibold">Select a PDF file</label>
                        <input type="file" class="form-control" id="fileInput" accept="application/pdf,.pdf">
                        <div class="form-text">The file is processed only in your browser; it is not uploaded to any server.</div>
                    </div>

                    <div id="toolWrap" class="d-none">
                        <p class="mb-2">File: <strong id="fileName" class="text-break"></strong></p>
                        <p class="mb-3">Total pages: <strong id="pageCount"></strong></p>

                        <span class="form-label fw-semibold d-block mb-2">Pages per sheet</span>
                        <div class="btn-group d-flex mb-3" role="group" aria-label="Pages per sheet">
                            <input type="radio" class="btn-check" name="perPage" id="up2" value="2" checked>
                            <label class="btn btn-outline-primary" for="up2">2-Up</label>
                            <input type="radio" class="btn-check" name="perPage" id="up4" value="4">
                            <label class="btn btn-outline-primary" for="up4">4-Up</label>
                            <input type="radio" class="btn-check" name="perPage" id="up6" value="6">
                            <label class="btn btn-outline-primary" for="up6">6-Up</label>
                            <input type="radio" class="btn-check" name="perPage" id="up8" value="8">
                            <label class="btn btn-outline-primary" for="up8">8-Up</label>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="pageLabels" checked>
                            <label class="form-check-label" for="pageLabels">Write the page number under each page</label>
                        </div>

                        <button type="button" class="btn btn-primary w-100" id="goBtn">Create N-Up PDF</button>
                        <div class="progress mt-3 d-none" id="progWrap">
                            <div class="progress-bar" id="progBar" role="progressbar" style="width: 0%">0%</div>
                        </div>
                        <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>
                        <div class="alert alert-success mt-3 d-none" id="successBox"></div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select your PDF file (with the button or drag-and-drop).</li>
                <li>Choose how many pages you want per sheet: 2, 4, 6 or 8.</li>
                <li>Press "Create N-Up PDF" — the ready PDF will download.</li>
            </ol>
            <h2>Tips</h2>
            <ul>
                <li>2-Up is best for reading; 4-Up and 6-Up are for handouts/printing.</li>
                <li>On a PDF with very small text, the text will be hard to read at 6 or 8 up.</li>
            </ul>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/pdfjs-dist@3.11.174/build/pdf.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/pdf-lib@1.17.1/dist/pdf-lib.min.js"></script>
<script>
(function () {
    'use strict';
    if (typeof pdfjsLib !== 'undefined') {
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdn.jsdelivr.net/npm/pdfjs-dist@3.11.174/build/pdf.worker.min.js';
    }

    var fileInput = document.getElementById('fileInput');
    var toolWrap = document.getElementById('toolWrap');
    var fileName = document.getElementById('fileName');
    var pageCount = document.getElementById('pageCount');
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var successBox = document.getElementById('successBox');
    var progWrap = document.getElementById('progWrap');
    var progBar = document.getElementById('progBar');
    var pageLabels = document.getElementById('pageLabels');

    var storedBytes = null;
    var srcName = '';
    var totalPages = 0;

    var LAYOUTS = { 2: { cols: 1, rows: 2 }, 4: { cols: 2, rows: 2 }, 6: { cols: 2, rows: 3 }, 8: { cols: 2, rows: 4 } };
    var SHEET_W = 595.28, SHEET_H = 841.89; // A4 in points

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        successBox.classList.add('d-none');
    }
    function hideAlerts() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
        successBox.classList.add('d-none');
        successBox.textContent = '';
    }
    function setProgress(done, total) {
        var pct = total ? Math.round(done / total * 100) : 0;
        progWrap.classList.remove('d-none');
        progBar.style.width = pct + '%';
        progBar.textContent = pct + '%';
    }

    function selectedUp() {
        var radios = document.getElementsByName('perPage');
        for (var i = 0; i < radios.length; i++) {
            if (radios[i].checked) return parseInt(radios[i].value, 10);
        }
        return 2;
    }

    fileInput.addEventListener('change', function () {
        hideAlerts();
        var f = fileInput.files[0];
        if (!f) return;
        if (!/\.pdf$/i.test(f.name) && f.type !== 'application/pdf') {
            showError('Please select only a PDF file.');
            return;
        }
        srcName = f.name.replace(/\.pdf$/i, '');
        fileName.textContent = f.name;
        var reader = new FileReader();
        reader.onload = function (e) {
            storedBytes = e.target.result;
            if (typeof pdfjsLib === 'undefined') {
                showError('The PDF reader did not load. Please check the internet and reload the page.');
                return;
            }
            pdfjsLib.getDocument({ data: storedBytes.slice(0) }).promise.then(function (doc) {
                totalPages = doc.numPages;
                pageCount.textContent = totalPages;
                toolWrap.classList.remove('d-none');
                hideAlerts();
            }).catch(function () {
                showError('This PDF could not be read. Please try another file.');
            });
        };
        reader.onerror = function () { showError('There was a problem reading the file.'); };
        reader.readAsArrayBuffer(f);
    });

    function canvasToBytes(canvas) {
        return new Promise(function (resolve, reject) {
            canvas.toBlob(function (blob) {
                if (!blob) { reject(new Error('render failed')); return; }
                var fr = new FileReader();
                fr.onload = function () { resolve(fr.result); };
                fr.onerror = function () { reject(new Error('render failed')); };
                fr.readAsArrayBuffer(blob);
            }, 'image/png');
        });
    }

    goBtn.addEventListener('click', function () {
        hideAlerts();
        if (!storedBytes) { showError('Please select a PDF file first.'); return; }
        if (typeof PDFLib === 'undefined' || typeof pdfjsLib === 'undefined') {
            showError('The PDF libraries did not load. Please check the internet and try again.');
            return;
        }
        goBtn.disabled = true;
        goBtn.textContent = 'Creating...';
        setProgress(0, totalPages);

        var up = selectedUp();
        var layout = LAYOUTS[up];
        var cols = layout.cols, rows = layout.rows;
        var cellW = SHEET_W / cols, cellH = SHEET_H / rows;
        var wantLabels = pageLabels.checked;

        pdfjsLib.getDocument({ data: storedBytes.slice(0) }).promise.then(function (srcDoc) {
            return PDFLib.PDFDocument.create().then(function (outDoc) {
                return outDoc.embedFont(PDFLib.StandardFonts.Helvetica).then(function (font) {
                    var state = { sheet: null, doc: outDoc, font: font, done: 0 };
                    function newSheet() {
                        state.sheet = state.doc.addPage([SHEET_W, SHEET_H]);
                    }
                    function drawOnSheet(pageIndex, pngImage) {
                        var slot = pageIndex % up;
                        var c = slot % cols;
                        var r = Math.floor(slot / cols);
                        var x0 = c * cellW;
                        var y0 = (rows - 1 - r) * cellH;
                        var margin = 16;
                        var labelSpace = wantLabels ? 14 : 0;
                        var availW = cellW - margin * 2;
                        var availH = cellH - margin * 2 - labelSpace;
                        var scale = Math.min(availW / pngImage.width, availH / pngImage.height);
                        var dw = pngImage.width * scale;
                        var dh = pngImage.height * scale;
                        var dx = x0 + (cellW - dw) / 2;
                        var dy = y0 + labelSpace + (cellH - labelSpace - dh) / 2;
                        state.sheet.drawImage(pngImage, { x: dx, y: dy, width: dw, height: dh });
                        if (wantLabels) {
                            state.sheet.drawText('Page ' + (pageIndex + 1), {
                                x: x0 + 8, y: y0 + 5, size: 9, font: state.font,
                                color: PDFLib.rgb(0.4, 0.4, 0.4)
                            });
                        }
                    }
                    var chain = Promise.resolve();
                    for (var i = 0; i < totalPages; i++) {
                        (function (pageIndex) {
                            chain = chain.then(function () {
                                if (pageIndex % up === 0) newSheet();
                                return srcDoc.getPage(pageIndex + 1).then(function (page) {
                                    var vp0 = page.getViewport({ scale: 1 });
                                    var scale = Math.min(2, 2000 / Math.max(vp0.width, vp0.height));
                                    var vp = page.getViewport({ scale: scale });
                                    var canvas = document.createElement('canvas');
                                    canvas.width = Math.ceil(vp.width);
                                    canvas.height = Math.ceil(vp.height);
                                    var ctx = canvas.getContext('2d');
                                    ctx.fillStyle = '#ffffff';
                                    ctx.fillRect(0, 0, canvas.width, canvas.height);
                                    return page.render({ canvasContext: ctx, viewport: vp }).promise
                                        .then(function () { return canvasToBytes(canvas); })
                                        .then(function (bytes) { return state.doc.embedPng(bytes); })
                                        .then(function (png) {
                                            drawOnSheet(pageIndex, png);
                                            state.done++;
                                            setProgress(state.done, totalPages);
                                        });
                                });
                            });
                        })(i);
                    }
                    return chain.then(function () { return state.doc.save(); });
                });
            });
        }).then(function (pdfBytes) {
            var blob = new Blob([pdfBytes], { type: 'application/pdf' });
            var url = URL.createObjectURL(blob);
            var a = document.createElement('a');
            a.href = url;
            a.download = srcName + '-' + up + 'up.pdf';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            setTimeout(function () { URL.revokeObjectURL(url); }, 5000);
            var sheets = Math.ceil(totalPages / up);
            successBox.textContent = 'Done! ' + totalPages + ' pages placed on ' + sheets + ' sheet(s). The download has started.';
            successBox.classList.remove('d-none');
        }).catch(function (err) {
            console.error(err);
            showError('There was a problem creating the PDF. If the file is large, please try again after a while.');
        }).then(function () {
            goBtn.disabled = false;
            goBtn.textContent = 'Create N-Up PDF';
        });
    });
})();
</script>
@endsection
