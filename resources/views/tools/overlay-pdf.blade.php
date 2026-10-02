@extends('layouts.app')

@section('title', 'PDF Overlay Tool - Azlaan Tools')
@section('meta_description', 'Overlay one PDF on top of another online free. Put your letterhead or stamp on your pages — everything happens in your browser.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">PDF Overlay Tool</h1>
            <p class="lead text-muted">Overlay one PDF (like a letterhead, stamp or watermark) on the pages of another PDF. Everything happens in your browser — your files are never uploaded.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="baseFile" class="form-label fw-semibold">1. Base PDF (the one to overlay onto)</label>
                        <input type="file" class="form-control" id="baseFile" accept="application/pdf,.pdf">
                        <div class="form-text" id="baseInfo">No file selected.</div>
                    </div>
                    <div class="mb-3">
                        <label for="overlayFile" class="form-label fw-semibold">2. Overlay PDF (letterhead / stamp / watermark)</label>
                        <input type="file" class="form-control" id="overlayFile" accept="application/pdf,.pdf">
                        <div class="form-text" id="overlayInfo">No file selected.</div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="pageMode" class="form-label fw-semibold">Which overlay page to use</label>
                            <select class="form-select" id="pageMode">
                                <option value="first">First page of overlay on every page</option>
                                <option value="match">Matching page number on every page</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="fitMode" class="form-label fw-semibold">Overlay size</label>
                            <select class="form-select" id="fitMode">
                                <option value="stretch">Fill whole page (stretch)</option>
                                <option value="fit">Fit in center with aspect ratio</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="opacityRange" class="form-label fw-semibold">Opacity: <span id="opacityVal">100</span>%</label>
                        <input type="range" class="form-range" id="opacityRange" min="10" max="100" value="100">
                        <div class="form-text">Keep opacity low for a watermark (for example 20%). Use 100% for a letterhead.</div>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Apply Overlay</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>
                    <div class="alert alert-success mt-3 d-none" id="successBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div><strong id="resultText"></strong></div>
                            <a href="#" class="btn btn-success" id="downloadLink">Download PDF</a>
                        </div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Choose the base PDF — this is your main document that gets the overlay.</li>
                <li>Choose the overlay PDF — the one with your letterhead, stamp or watermark.</li>
                <li>Set the opacity and size, then press the button.</li>
                <li>Your new PDF will be ready with the overlay on every page — download it.</li>
            </ol>
            <p class="text-muted small">Tip: If your overlay PDF has a white background, it will cover the page below. For better results, use a PDF with a transparent background.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/pdf-lib@1.17.1/dist/pdf-lib.min.js"></script>
<script>
(function () {
    'use strict';
    var baseFile = document.getElementById('baseFile');
    var overlayFile = document.getElementById('overlayFile');
    var baseInfo = document.getElementById('baseInfo');
    var overlayInfo = document.getElementById('overlayInfo');
    var pageMode = document.getElementById('pageMode');
    var fitMode = document.getElementById('fitMode');
    var opacityRange = document.getElementById('opacityRange');
    var opacityVal = document.getElementById('opacityVal');
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var successBox = document.getElementById('successBox');
    var results = document.getElementById('results');
    var resultText = document.getElementById('resultText');
    var downloadLink = document.getElementById('downloadLink');

    var baseBuf = null;
    var overlayBuf = null;
    var baseName = '';

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        successBox.classList.add('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function fileLabel(f) {
        return f.name + ' (' + Math.max(1, Math.round(f.size / 1024)) + ' KB)';
    }
    function readFile(file, done) {
        var reader = new FileReader();
        reader.onload = function () { done(reader.result); };
        reader.onerror = function () { showError('There was a problem reading the file. Please try again.'); };
        reader.readAsArrayBuffer(file);
    }

    baseFile.addEventListener('change', function () {
        var f = baseFile.files[0];
        if (!f) { baseBuf = null; baseInfo.textContent = 'No file selected.'; return; }
        if (!/\.pdf$/i.test(f.name) && f.type !== 'application/pdf') {
            showError('The base file must be a PDF.'); baseFile.value = ''; return;
        }
        hideError();
        baseName = f.name;
        readFile(f, function (buf) { baseBuf = buf; baseInfo.textContent = 'Base: ' + fileLabel(f); });
    });

    overlayFile.addEventListener('change', function () {
        var f = overlayFile.files[0];
        if (!f) { overlayBuf = null; overlayInfo.textContent = 'No file selected.'; return; }
        if (!/\.pdf$/i.test(f.name) && f.type !== 'application/pdf') {
            showError('The overlay file must be a PDF.'); overlayFile.value = ''; return;
        }
        hideError();
        readFile(f, function (buf) { overlayBuf = buf; overlayInfo.textContent = 'Overlay: ' + fileLabel(f); });
    });

    opacityRange.addEventListener('input', function () {
        opacityVal.textContent = opacityRange.value;
    });

    goBtn.addEventListener('click', function () {
        hideError();
        successBox.classList.add('d-none');
        results.classList.add('d-none');
        if (!baseBuf) { showError('Please choose the base PDF first.'); return; }
        if (!overlayBuf) { showError('Please choose the overlay PDF.'); return; }
        goBtn.disabled = true;
        goBtn.textContent = 'Working...';
        processOverlay().then(function () {
            goBtn.disabled = false;
            goBtn.textContent = 'Apply Overlay';
        }).catch(function (err) {
            goBtn.disabled = false;
            goBtn.textContent = 'Apply Overlay';
            showError('Could not process the PDF. Is the file corrupt? (' + (err && err.message ? err.message : 'unknown error') + ')');
        });
    });

    async function processOverlay() {
        var baseDoc = await PDFLib.PDFDocument.load(baseBuf);
        var overDoc = await PDFLib.PDFDocument.load(overlayBuf);
        var outDoc = await PDFLib.PDFDocument.create();
        var copied = await outDoc.copyPages(baseDoc, baseDoc.getPageIndices());
        for (var k = 0; k < copied.length; k++) { outDoc.addPage(copied[k]); }
        var overPages = overDoc.getPages();
        var overCount = overPages.length;
        var pages = outDoc.getPages();
        var opacity = parseInt(opacityRange.value, 10) / 100;
        var mode = pageMode.value;
        var fit = fitMode.value;
        for (var i = 0; i < pages.length; i++) {
            var ovPage = (mode === 'match') ? overPages[i % overCount] : overPages[0];
            var size = ovPage.getSize();
            var ow = size.width, oh = size.height;
            var emb = await outDoc.embedPage(ovPage);
            var pg = pages[i];
            var pw = pg.getWidth(), ph = pg.getHeight();
            var dx = 0, dy = 0, dw = pw, dh = ph;
            if (fit === 'fit') {
                var scale = Math.min(pw / ow, ph / oh);
                dw = ow * scale; dh = oh * scale;
                dx = (pw - dw) / 2; dy = (ph - dh) / 2;
            }
            pg.drawPage(emb, { x: dx, y: dy, width: dw, height: dh, opacity: opacity });
        }
        var bytes = await outDoc.save();
        var blob = new Blob([bytes], { type: 'application/pdf' });
        var url = URL.createObjectURL(blob);
        downloadLink.href = url;
        downloadLink.download = baseName.replace(/\.pdf$/i, '') + '-overlay.pdf';
        resultText.textContent = pages.length + ' pages done — overlay applied on every page. File is ready.';
        successBox.textContent = 'Done! Overlay applied successfully.';
        successBox.classList.remove('d-none');
        results.classList.remove('d-none');
    }
})();
</script>
@endsection
