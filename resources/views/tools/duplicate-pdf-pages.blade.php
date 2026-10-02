@extends('layouts.app')

@section('title', 'Duplicate PDF Pages - Azlaan Tools')
@section('meta_description', 'Repeat every PDF page N times online for free. Perfect for forms and worksheets.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Duplicate PDF Pages</h1>
            <p class="lead text-muted">Make N copies of every page — for forms, worksheets and print-ready copies. The PDF is processed only in your browser, it is never uploaded.</p></p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="pdfFile" class="form-label fw-semibold">Select a PDF file</label>
                        <input type="file" class="form-control" id="pdfFile" accept=".pdf,application/pdf">
                        <div class="form-text" id="pageInfo">No file chosen yet.</div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="pageRange" class="form-label fw-semibold">Pages</label>
                            <input type="text" class="form-control" id="pageRange" placeholder="all or 1-3,5">
                            <div class="form-text">Type "all", or specific pages — e.g. <code>1-3,5</code></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="repeatCount" class="form-label fw-semibold">How many times per page?</label>
                            <input type="number" class="form-control" id="repeatCount" value="2" min="2" max="20">
                            <div class="form-text">You can repeat from 2 to 20 times</div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Duplicate Pages</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="alert alert-success" id="doneMsg" role="status"></div>
                        <a class="btn btn-success w-100" id="dlBtn" href="#" download>Download PDF</a>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select your PDF file — the total page count will appear below.</li>
                <li>Keep <strong>all</strong>, or type specific pages (e.g. <code>1-3,5</code>).</li>
                <li>Set the repeat count and press <strong>Duplicate Pages</strong>, then download the new PDF.</li>
            </ol>
            <p class="text-muted small">Example: repeating a 2-page form 10 times makes a 20-page PDF — print once, get ten forms.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/pdf-lib@1.17.1/dist/pdf-lib.min.js"></script>
<script>
(function () {
    'use strict';
    var PDFLib = window.PDFLib;
    var pdfFile = document.getElementById('pdfFile');
    var pageRange = document.getElementById('pageRange');
    var repeatCount = document.getElementById('repeatCount');
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var doneMsg = document.getElementById('doneMsg');
    var dlBtn = document.getElementById('dlBtn');
    var pageInfo = document.getElementById('pageInfo');
    var pdfBytes = null;
    var totalPages = 0;

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function resetBtn() {
        goBtn.disabled = false;
        goBtn.textContent = 'Duplicate Pages';
    }
    function readFile(file) {
        return new Promise(function (resolve, reject) {
            var r = new FileReader();
            r.onload = function () { resolve(r.result); };
            r.onerror = function () { reject(new Error('read-failed')); };
            r.readAsArrayBuffer(file);
        });
    }

    pdfFile.addEventListener('change', function () {
        hideError();
        results.classList.add('d-none');
        pdfBytes = null;
        totalPages = 0;
        var f = pdfFile.files[0];
        if (!f) { pageInfo.textContent = 'No file chosen yet.'; return; }
        pageInfo.textContent = 'Reading...';
        readFile(f).then(function (buf) {
            pdfBytes = buf;
            return PDFLib.PDFDocument.load(buf, { ignoreEncryption: true });
        }).then(function (doc) {
            totalPages = doc.getPageCount();
            pageInfo.textContent = 'File: ' + f.name + ' — total ' + totalPages + ' pages.';
        }).catch(function () {
            pdfBytes = null;
            pageInfo.textContent = 'Could not read the file. Please select a PDF file.';
        });
    });

    function parseRange(str, total) {
        var out = [];
        var seen = {};
        if (!str || str.toLowerCase() === 'all') {
            for (var i = 0; i < total; i++) out.push(i);
            return out;
        }
        var parts = str.split(',');
        for (var p = 0; p < parts.length; p++) {
            var token = parts[p].trim();
            if (!token) continue;
            var dash = token.indexOf('-');
            if (dash > -1) {
                var a = parseInt(token.slice(0, dash), 10);
                var b = parseInt(token.slice(dash + 1), 10);
                if (isNaN(a) || isNaN(b) || a < 1 || b < 1 || a > total || b > total || a > b) return null;
                for (var k = a; k <= b; k++) {
                    if (!seen[k]) { seen[k] = 1; out.push(k - 1); }
                }
            } else {
                var n = parseInt(token, 10);
                if (isNaN(n) || n < 1 || n > total) return null;
                if (!seen[n]) { seen[n] = 1; out.push(n - 1); }
            }
        }
        return out.length ? out : null;
    }

    goBtn.addEventListener('click', function () {
        hideError();
        if (!pdfBytes) { showError('Please select a PDF file first.'); return; }
        var n = parseInt(repeatCount.value, 10);
        if (isNaN(n) || n < 2 || n > 20) { showError('Keep the repeat count between 2 and 20.'); return; }
        var idx = parseRange(pageRange.value.trim(), totalPages);
        if (!idx) { showError('Page range is wrong. Type "all" or e.g. "1-3,5".'); return; }
        var finalCount = idx.length * n;
        if (finalCount > 1000) { showError('The final PDF would be more than 1000 pages. Reduce the repeat count or pages.'); return; }
        goBtn.disabled = true;
        goBtn.textContent = 'Banayi ja rahi hai...';
        PDFLib.PDFDocument.load(pdfBytes, { ignoreEncryption: true }).then(function (src) {
            return PDFLib.PDFDocument.create().then(function (out) {
                var chain = Promise.resolve();
                idx.forEach(function (pi) {
                    chain = chain.then(function () {
                        return out.copyPages(src, [pi]).then(function (pg) {
                            for (var r = 0; r < n; r++) out.addPage(pg[0]);
                        });
                    });
                });
                return chain.then(function () { return out.save(); });
            });
        }).then(function (bytes) {
            var blob = new Blob([bytes], { type: 'application/pdf' });
            if (dlBtn.href && dlBtn.href.indexOf('blob:') === 0) URL.revokeObjectURL(dlBtn.href);
            dlBtn.href = URL.createObjectURL(blob);
            var base = pdfFile.files[0].name.replace(/\.pdf$/i, '');
            dlBtn.setAttribute('download', base + '-duplicated.pdf');
            doneMsg.textContent = idx.length + ' pages × ' + n + ' = PDF with total ' + finalCount + ' pages is ready. Press Download.';
            results.classList.remove('d-none');
            resetBtn();
        }).catch(function () {
            showError('Something went wrong — please try again.');
            resetBtn();
        });
    });
})();
</script>
@endsection
