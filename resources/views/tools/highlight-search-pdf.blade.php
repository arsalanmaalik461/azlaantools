@extends('layouts.app')

@section('title', 'Bulk Highlight PDF Text - Azlaan Tools')
@section('meta_description', 'Find a word and highlight every match across all PDF pages, free online.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Bulk Highlight PDF Text</h1>
            <p class="lead text-muted">Upload a PDF, type a word, and the tool will highlight every match on every page and give you a new PDF.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="fileInput" class="form-label fw-semibold">PDF file</label>
                        <input type="file" class="form-control" id="fileInput" accept="application/pdf">
                        <div class="form-text">Your file is processed only in your browser. It is never uploaded anywhere.</div>
                    </div>
                    <div class="mb-3">
                        <label for="searchWord" class="form-label fw-semibold">Search word</label>
                        <input type="text" class="form-control" id="searchWord" placeholder="e.g. payment, invoice, note">
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label for="colorSel" class="form-label fw-semibold">Highlight color</label>
                            <select class="form-select" id="colorSel">
                                <option value="#fff176">Yellow</option>
                                <option value="#a5d6a7">Green</option>
                                <option value="#f8bbd0">Pink</option>
                                <option value="#90caf9">Blue</option>
                            </select>
                        </div>
                        <div class="col-6 mb-3 d-flex align-items-end">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="caseChk">
                                <label class="form-check-label" for="caseChk">Case sensitive</label>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Highlight & Download</button>
                    <div class="progress mt-3 d-none" id="progWrap">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" id="progBar" style="width:0%"></div>
                    </div>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="alert alert-success" id="okBox" role="alert"></div>
                        <a href="#" class="btn btn-success w-100" id="dlBtn">Download Highlighted PDF</a>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select your PDF file.</li>
                <li>Type the word you want to highlight (there is also a case sensitive option).</li>
                <li>Press <strong>Highlight &amp; Download</strong> — every page will be scanned and the highlighted PDF will be downloaded.</li>
            </ol>
            <p class="text-muted small">Note: pages are saved as images for highlighting, so large files may take a little time.</p>
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
    var searchWord = document.getElementById('searchWord');
    var colorSel = document.getElementById('colorSel');
    var caseChk = document.getElementById('caseChk');
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var okBox = document.getElementById('okBox');
    var dlBtn = document.getElementById('dlBtn');
    var progWrap = document.getElementById('progWrap');
    var progBar = document.getElementById('progBar');

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
        progWrap.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function setProg(pct, label) {
        progWrap.classList.remove('d-none');
        progBar.style.width = pct + '%';
        progBar.textContent = label || '';
    }

    function findMatches(str, needle, caseSensitive) {
        var idxs = [];
        var hay = caseSensitive ? str : str.toLowerCase();
        var nd = caseSensitive ? needle : needle.toLowerCase();
        var pos = 0;
        while (true) {
            var i = hay.indexOf(nd, pos);
            if (i === -1) break;
            idxs.push(i);
            pos = i + nd.length;
        }
        return idxs;
    }

    goBtn.addEventListener('click', function () {
        hideError();
        results.classList.add('d-none');
        var file = fileInput.files[0];
        var word = searchWord.value.trim();
        if (!file) { showError('Please select a PDF file first.'); return; }
        if (!word) { showError('Please enter a word to highlight.'); return; }
        if (file.type !== 'application/pdf' && !/\.pdf$/i.test(file.name)) {
            showError('Please select a valid PDF file.'); return;
        }

        goBtn.disabled = true;
        setProg(2, 'Loading PDF...');
        var color = colorSel.value;
        var caseSensitive = caseChk.checked;

        var reader = new FileReader();
        reader.onload = function () {
            var data = new Uint8Array(reader.result);
            pdfjsLib.getDocument({ data: data }).promise.then(function (pdf) {
                var totalPages = pdf.numPages;
                var outPdf = PDFLib.PDFDocument.create();
                outPdf.then(function (doc) {
                    var totalMatches = 0;
                    var p = 1;

                    function nextPage() {
                        if (p > totalPages) {
                            setProg(96, 'Saving PDF...');
                            doc.save().then(function (bytes) {
                                var blob = new Blob([bytes], { type: 'application/pdf' });
                                var url = URL.createObjectURL(blob);
                                var base = file.name.replace(/\.pdf$/i, '') || 'document';
                                dlBtn.href = url;
                                dlBtn.download = base + '-highlighted.pdf';
                                okBox.textContent = 'Done! ' + totalMatches + ' matches of "' + word + '" highlighted on ' +
                                    totalPages + ' pages.';
                                results.classList.remove('d-none');
                                progWrap.classList.add('d-none');
                                goBtn.disabled = false;
                            }).catch(function (e) {
                                showError('Could not save the PDF: ' + e.message);
                                goBtn.disabled = false;
                            });
                            return;
                        }
                        setProg(Math.round((p / totalPages) * 90), 'Page ' + p + ' of ' + totalPages);
                        pdf.getPage(p).then(function (page) {
                            var scale = 2;
                            var viewport = page.getViewport({ scale: scale });
                            page.getTextContent().then(function (tc) {
                                // Collect highlight rects in canvas coordinates
                                var rects = [];
                                for (var i = 0; i < tc.items.length; i++) {
                                    var item = tc.items[i];
                                    if (!item.str) continue;
                                    var hits = findMatches(item.str, word, caseSensitive);
                                    if (!hits.length) continue;
                                    var tx = pdfjsLib.Util.transform(viewport.transform, item.transform);
                                    var baseX = tx[4];
                                    var baseY = tx[5];
                                    var runLen = Math.hypot(tx[0], tx[1]);
                                    var charW = item.str.length ? runLen / item.str.length : 0;
                                    var height = Math.hypot(tx[2], tx[3]);
                                    if (!height || height <= 0) height = 12 * scale;
                                    for (var h = 0; h < hits.length; h++) {
                                        totalMatches++;
                                        rects.push({
                                            x: baseX + charW * hits[h],
                                            y: baseY - height,
                                            w: charW * word.length,
                                            h: height
                                        });
                                    }
                                }
                                // Render page, then paint highlights UNDER the text
                                var pageCanvas = document.createElement('canvas');
                                pageCanvas.width = Math.floor(viewport.width);
                                pageCanvas.height = Math.floor(viewport.height);
                                var pctx = pageCanvas.getContext('2d');
                                page.render({ canvasContext: pctx, viewport: viewport }).promise.then(function () {
                                    var hlCanvas = document.createElement('canvas');
                                    hlCanvas.width = pageCanvas.width;
                                    hlCanvas.height = pageCanvas.height;
                                    var hctx = hlCanvas.getContext('2d');
                                    hctx.fillStyle = '#ffffff';
                                    hctx.fillRect(0, 0, hlCanvas.width, hlCanvas.height);
                                    hctx.fillStyle = color;
                                    for (var r = 0; r < rects.length; r++) {
                                        var rc = rects[r];
                                        hctx.fillRect(rc.x, rc.y, rc.w + 2, rc.h);
                                    }
                                    hctx.drawImage(pageCanvas, 0, 0);
                                    var dataUrl = hlCanvas.toDataURL('image/jpeg', 0.9);
                                    var b64 = dataUrl.split(',')[1];
                                    var raw = atob(b64);
                                    var bytes = new Uint8Array(raw.length);
                                    for (var b = 0; b < raw.length; b++) bytes[b] = raw.charCodeAt(b);
                                    doc.embedJpg(bytes).then(function (img) {
                                        var pg = doc.addPage([img.width / scale, img.height / scale]);
                                        pg.drawImage(img, { x: 0, y: 0, width: img.width / scale, height: img.height / scale });
                                        p++;
                                        nextPage();
                                    }).catch(function (e) {
                                        showError('Could not process page ' + p + ': ' + e.message);
                                        goBtn.disabled = false;
                                    });
                                }).catch(function (e) {
                                    showError('Could not render page ' + p + ': ' + e.message);
                                    goBtn.disabled = false;
                                });
                            }).catch(function (e) {
                                showError('Could not read text on page ' + p + ': ' + e.message);
                                goBtn.disabled = false;
                            });
                        }).catch(function (e) {
                            showError('Could not load page ' + p + ': ' + e.message);
                            goBtn.disabled = false;
                        });
                    }
                    nextPage();
                }).catch(function (e) {
                    showError('Could not create the output PDF: ' + e.message);
                    goBtn.disabled = false;
                });
            }).catch(function () {
                showError('Could not open the PDF. The file may be corrupt. Please check it again.');
                goBtn.disabled = false;
            });
        };
        reader.onerror = function () {
            showError('Could not read the file. Please try again.');
            goBtn.disabled = false;
        };
        reader.readAsArrayBuffer(file);
    });
})();
</script>
@endsection
