@extends('layouts.app')

@section('title', 'PDF to Grayscale - Azlaan Tools')
@section('meta_description', 'Convert color PDF pages to black and white online for free. Save ink and make printing cheaper.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">PDF to Grayscale</h1>
            <p class="lead text-muted">Change your color PDF to black &amp; white to save ink while printing. The file is converted in your browser — it is not uploaded anywhere.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="pdfFile" class="form-label fw-semibold">Choose a PDF file</label>
                        <input type="file" class="form-control" id="pdfFile" accept="application/pdf,.pdf">
                        <div class="form-text">Max 60 pages.</div>
                    </div>
                    <div class="mb-3">
                        <label for="qualitySel" class="form-label fw-semibold">Quality</label>
                        <select class="form-select" id="qualitySel">
                            <option value="1.2">Normal (small file)</option>
                            <option value="1.6" selected>Good (recommended)</option>
                            <option value="2.2">Best (large file)</option>
                        </select>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Convert to Grayscale</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div class="progress mt-3 d-none" id="progressWrap" style="height: 22px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" id="progressBar" role="progressbar" style="width: 0%;">0%</div>
                    </div>
                    <p class="text-muted small mt-2 d-none" id="statusText"></p>

                    <div id="results" class="d-none mt-4">
                        <div class="alert alert-success">
                            <strong>Done!</strong> <span id="pageCount"></span> pages converted to grayscale.
                        </div>
                        <a href="#" class="btn btn-success w-100" id="dlLink" download>Download Grayscale PDF</a>
                        <p class="text-muted small mt-2 mb-0">Tip: to convert again, choose a new file and press the button.</p>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Choose your color PDF file.</li>
                <li>Select quality and press <strong>Convert to Grayscale</strong>.</li>
                <li>Every page will change to black &amp; white — then press <strong>Download</strong>.</li>
            </ol>
            <h2>Why grayscale?</h2>
            <p>Color printing uses 4 ink cartridges, grayscale uses only black. Converting notes, forms and documents to grayscale before printing saves both ink and money.</p>
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
    var fileInput = document.getElementById('pdfFile');
    var qualitySel = document.getElementById('qualitySel');
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var progressWrap = document.getElementById('progressWrap');
    var progressBar = document.getElementById('progressBar');
    var statusText = document.getElementById('statusText');
    var dlLink = document.getElementById('dlLink');
    var pageCount = document.getElementById('pageCount');

    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdn.jsdelivr.net/npm/pdfjs-dist@3.11.174/build/pdf.worker.min.js';

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
        progressWrap.classList.add('d-none');
        statusText.classList.add('d-none');
        goBtn.disabled = false;
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function setProgress(done, total) {
        var pct = Math.round((done / total) * 100);
        progressBar.style.width = pct + '%';
        progressBar.textContent = pct + '%';
        statusText.textContent = 'Page ' + done + ' of ' + total + ' converting...';
    }

    goBtn.addEventListener('click', function () {
        hideError();
        results.classList.add('d-none');
        var f = fileInput.files[0];
        if (!f) { showError('Please choose a PDF file first.'); return; }
        if (f.type !== 'application/pdf' && !/\.pdf$/i.test(f.name)) { showError('Only PDF files are supported.'); return; }
        goBtn.disabled = true;
        progressWrap.classList.remove('d-none');
        statusText.classList.remove('d-none');
        setProgress(0, 1);
        statusText.textContent = 'Reading file...';
        var reader = new FileReader();
        reader.onload = function () { convertPdf(new Uint8Array(reader.result), f.name); };
        reader.onerror = function () { showError('Could not read the file.'); };
        reader.readAsArrayBuffer(f);
    });

    function convertPdf(data, name) {
        var scale = parseFloat(qualitySel.value);
        pdfjsLib.getDocument({ data: data }).promise.then(function (pdf) {
            if (pdf.numPages > 60) { showError('The file has more than 60 pages. Choose a smaller file.'); return; }
            PDFLib.PDFDocument.create().then(function (outDoc) {
                var i = 1;
                (function next() {
                    if (i > pdf.numPages) { finish(outDoc, pdf.numPages, name); return; }
                    setProgress(i - 1, pdf.numPages);
                    pdf.getPage(i).then(function (page) {
                        var viewport = page.getViewport({ scale: scale });
                        var canvas = document.createElement('canvas');
                        canvas.width = Math.floor(viewport.width);
                        canvas.height = Math.floor(viewport.height);
                        var ctx = canvas.getContext('2d');
                        ctx.fillStyle = '#ffffff';
                        ctx.fillRect(0, 0, canvas.width, canvas.height);
                        page.render({ canvasContext: ctx, viewport: viewport }).promise.then(function () {
                            var img = ctx.getImageData(0, 0, canvas.width, canvas.height);
                            var d = img.data, p, g;
                            for (p = 0; p < d.length; p += 4) {
                                g = Math.round(0.299 * d[p] + 0.587 * d[p + 1] + 0.114 * d[p + 2]);
                                d[p] = g; d[p + 1] = g; d[p + 2] = g;
                            }
                            ctx.putImageData(img, 0, 0);
                            var jpgUrl = canvas.toDataURL('image/jpeg', 0.92);
                            outDoc.embedJpg(jpgUrl).then(function (jpg) {
                                var wPt = viewport.width / scale;
                                var hPt = viewport.height / scale;
                                var pg = outDoc.addPage([wPt, hPt]);
                                pg.drawImage(jpg, { x: 0, y: 0, width: wPt, height: hPt });
                                i++;
                                setProgress(i - 1, pdf.numPages);
                                next();
                            }).catch(function () { showError('Problem converting the page.'); });
                        }).catch(function () { showError('Problem rendering the page.'); });
                    }).catch(function () { showError('Problem reading the page.'); });
                })();
            }).catch(function () { showError('Problem creating the new PDF.'); });
        }).catch(function () { showError('This does not look like a valid PDF.'); });
    }

    function finish(outDoc, total, name) {
        setProgress(total, total);
        statusText.textContent = 'Preparing download...';
        outDoc.save().then(function (bytes) {
            var blob = new Blob([bytes], { type: 'application/pdf' });
            var url = URL.createObjectURL(blob);
            dlLink.href = url;
            var base = name.replace(/\.pdf$/i, '');
            dlLink.setAttribute('download', 'grayscale-' + base + '.pdf');
            pageCount.textContent = total;
            progressWrap.classList.add('d-none');
            statusText.classList.add('d-none');
            results.classList.remove('d-none');
            goBtn.disabled = false;
        }).catch(function () { showError('Problem creating the download file.'); });
    }
})();
</script>
@endsection
