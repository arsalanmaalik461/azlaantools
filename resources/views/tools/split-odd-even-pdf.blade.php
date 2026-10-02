@extends('layouts.app')
@section('title', 'Split PDF Odd Even Pages Online Free — Azlaan Tools')
@section('meta_description', 'Split a PDF into odd pages and even pages online for free. Download two separate PDF files — one with odd pages, one with even. No signup, no upload.')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Split Odd Even Pages</h1>
            <p class="lead text-muted">Split a PDF into two files — odd pages and even pages. Great for duplex printing — free and fast.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="fileInput" class="form-label fw-semibold">PDF file</label>
                        <input type="file" class="form-control" id="fileInput" accept="application/pdf,.pdf">
                        <div class="form-text">Your PDF stays in your browser — nothing is uploaded.</div>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Split into Odd &amp; Even</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="alert alert-success" id="splitInfo"></div>
                        <div class="d-flex flex-wrap gap-2">
                            <button type="button" class="btn btn-success" id="oddBtn">Download Odd Pages PDF</button>
                            <button type="button" class="btn btn-success" id="evenBtn">Download Even Pages PDF</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="alert alert-info">
                <strong>Privacy note:</strong> Your file never leaves your browser — all the work happens on your phone/computer, nothing is uploaded.
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select your PDF file.</li>
                <li>Press <strong>Split into Odd &amp; Even</strong> — the tool will separate pages 1, 3, 5... and 2, 4, 6...</li>
                <li>Download the odd-pages and even-pages PDF files with the two buttons.</li>
            </ol>
            <h2>Tip</h2>
            <p>Useful for duplex (two-sided) printing: print the odd pages first, then flip the paper and print the even pages.</p>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/pdf-lib@1.17.1/dist/pdf-lib.min.js"></script>
<script>
(function () {
    'use strict';
    var fileInput = document.getElementById('fileInput');
    var goBtn = document.getElementById('goBtn');
    var oddBtn = document.getElementById('oddBtn');
    var evenBtn = document.getElementById('evenBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var splitInfo = document.getElementById('splitInfo');

    var pdfBytes = null;
    var fileBase = 'document';
    var oddBytes = null;
    var evenBytes = null;

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }

    function download(bytes, name) {
        var blob = new Blob([bytes], { type: 'application/pdf' });
        var url = URL.createObjectURL(blob);
        var a = document.createElement('a');
        a.href = url;
        a.download = name;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        setTimeout(function () { URL.revokeObjectURL(url); }, 5000);
    }

    fileInput.addEventListener('change', function () {
        hideError();
        results.classList.add('d-none');
        var file = fileInput.files[0];
        if (!file) { return; }
        if (file.type !== 'application/pdf' && file.name.slice(-4).toLowerCase() !== '.pdf') {
            showError('Please select a PDF file.');
            return;
        }
        fileBase = file.name.replace(/\.pdf$/i, '') || 'document';
        var reader = new FileReader();
        reader.onload = function () {
            pdfBytes = reader.result;
        };
        reader.readAsArrayBuffer(file);
    });

    goBtn.addEventListener('click', async function () {
        hideError();
        results.classList.add('d-none');
        if (!pdfBytes) { showError('Please select a PDF file first.'); return; }
        try {
            var src = await PDFLib.PDFDocument.load(pdfBytes);
            var n = src.getPageCount();
            if (n < 2) {
                showError('The PDF must have at least 2 pages.');
                return;
            }
            var oddDoc = await PDFLib.PDFDocument.create();
            var evenDoc = await PDFLib.PDFDocument.create();
            for (var i = 0; i < n; i++) {
                var pages = await (i % 2 === 0 ? oddDoc : evenDoc).copyPages(src, [i]);
                (i % 2 === 0 ? oddDoc : evenDoc).addPage(pages[0]);
            }
            oddBytes = await oddDoc.save();
            evenBytes = await evenDoc.save();
            var oddN = Math.ceil(n / 2);
            var evenN = Math.floor(n / 2);
            splitInfo.textContent = 'Split complete: from ' + n + ' pages, an odd-pages file (' + oddN + ' pages) and an even-pages file (' + evenN + ' pages) were made.';
            results.classList.remove('d-none');
        } catch (e) {
            showError('Could not split this PDF. It may be corrupted or password-protected.');
        }
    });

    oddBtn.addEventListener('click', function () {
        if (oddBytes) { download(oddBytes, fileBase + '-odd-pages.pdf'); }
    });
    evenBtn.addEventListener('click', function () {
        if (evenBytes) { download(evenBytes, fileBase + '-even-pages.pdf'); }
    });
})();
</script>
@endsection
