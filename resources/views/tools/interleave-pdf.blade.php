@extends('layouts.app')

@section('title', 'Interleave Two PDFs - Azlaan Tools')
@section('meta_description', 'Merge two scanned PDFs page by page - perfect for duplex scans. Free online, files stay in your browser.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Interleave Two PDFs</h1>
            <p class="lead text-muted">Join scans from both sides page by page. PDF A (odd/front pages) and PDF B (even/back pages) are merged in alternate order into one PDF — the most common task after duplex scanning.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="fileInputA" class="form-label fw-semibold">PDF A — Front / odd pages</label>
                        <input type="file" class="form-control" id="fileInputA" accept="application/pdf,.pdf">
                        <div class="form-text">The scan that has pages 1, 3, 5 ...</div>
                    </div>
                    <div class="mb-3">
                        <label for="fileInputB" class="form-label fw-semibold">PDF B — Back / even pages</label>
                        <input type="file" class="form-control" id="fileInputB" accept="application/pdf,.pdf">
                        <div class="form-text">The scan that has pages 2, 4, 6 ...</div>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="reverseB" checked>
                        <label class="form-check-label" for="reverseB">Pages of PDF B are in reverse order (duplex scanners usually scan the back side in reverse)</label>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Interleave &amp; Download</button>
                    <div class="progress mt-3 d-none" id="progressWrap" style="height:22px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" id="progressBar" role="progressbar" style="width:0%">0%</div>
                    </div>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="alert alert-success" id="successBox" role="alert"></div>
                        <a href="#" class="btn btn-success btn-lg w-100" id="downloadLink">Download Interleaved PDF</a>
                    </div>
                </div>
            </div>

            <div class="alert alert-info"><strong>Privacy:</strong> Both PDFs are processed only in your browser — nothing is uploaded, no server sees them.</div>

            <h2>How to use</h2>
            <ol>
                <li>Select the PDF that holds the front / odd pages (1, 3, 5 ...) as <strong>PDF A</strong>.</li>
                <li>Select the PDF that holds the back / even pages (2, 4, 6 ...) as <strong>PDF B</strong>.</li>
                <li>Keep <strong>reverse B</strong> checked if your scanner scanned the back side in reverse order (this is the usual duplex behaviour).</li>
                <li>Click <strong>Interleave &amp; Download</strong>. Pages are merged as A1, B1, A2, B2 ... and the result downloads automatically.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/pdf-lib@1.17.1/dist/pdf-lib.min.js"></script>
<script>
(function () {
    'use strict';
    var fileInputA = document.getElementById('fileInputA');
    var fileInputB = document.getElementById('fileInputB');
    var reverseB = document.getElementById('reverseB');
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var successBox = document.getElementById('successBox');
    var downloadLink = document.getElementById('downloadLink');
    var progressWrap = document.getElementById('progressWrap');
    var progressBar = document.getElementById('progressBar');

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
        progressWrap.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function setProgress(p) {
        progressWrap.classList.remove('d-none');
        progressBar.style.width = p + '%';
        progressBar.textContent = p + '%';
    }

    goBtn.addEventListener('click', function () {
        hideError();
        results.classList.add('d-none');
        var fA = fileInputA.files[0];
        var fB = fileInputB.files[0];
        if (!fA || !fB) { showError('Please select both PDF files.'); return; }
        var rev = reverseB.checked;
        setProgress(5);
        Promise.all([fA.arrayBuffer(), fB.arrayBuffer()]).then(function (bufs) {
            setProgress(20);
            return Promise.all([PDFLib.PDFDocument.load(bufs[0]), PDFLib.PDFDocument.load(bufs[1])]);
        }).then(function (docs) {
            var docA = docs[0];
            var docB = docs[1];
            var nA = docA.getPageCount();
            var nB = docB.getPageCount();
            setProgress(40);
            return PDFLib.PDFDocument.create().then(function (out) {
                var max = Math.max(nA, nB);
                var done = 0;
                var chain = Promise.resolve();
                for (var i = 0; i < max; i++) {
                    (function (i) {
                        chain = chain.then(function () {
                            var jobs = [];
                            if (i < nA) {
                                jobs.push(out.copyPages(docA, [i]).then(function (p) { out.addPage(p[0]); }));
                            }
                            var j = rev ? (nB - 1 - i) : i;
                            if (j >= 0 && j < nB) {
                                jobs.push(out.copyPages(docB, [j]).then(function (p) { out.addPage(p[0]); }));
                            }
                            return Promise.all(jobs).then(function () {
                                done++;
                                setProgress(40 + Math.round(done / max * 50));
                            });
                        });
                    })(i);
                }
                return chain.then(function () {
                    return out.save().then(function (bytes) {
                        return { bytes: bytes, nA: nA, nB: nB, total: out.getPageCount() };
                    });
                });
            });
        }).then(function (res) {
            setProgress(100);
            var blob = new Blob([res.bytes], { type: 'application/pdf' });
            var url = URL.createObjectURL(blob);
            downloadLink.href = url;
            downloadLink.download = 'interleaved.pdf';
            successBox.textContent = 'Done! An interleaved PDF of ' + res.total + ' pages was created from PDF A (' + res.nA + ' pages) and PDF B (' + res.nB + ' pages).';
            results.classList.remove('d-none');
        }).catch(function (err) {
            showError('The PDF could not be processed. Please check that both files are valid PDF files. (' + (err && err.message ? err.message : 'unknown error') + ')');
        });
    });
})();
</script>
@endsection
