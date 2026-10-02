@extends('layouts.app')

@section('title', 'Fast Web View PDF - Azlaan Tools')
@section('meta_description', 'Linearize and optimize your PDF for fast web viewing so the first page loads instantly. Free online tool.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Fast Web View PDF</h1>
            <p class="lead text-muted">Optimize your PDF for the web so the first page opens fast in the browser. The file is processed only in your browser.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="pdfFile" class="form-label fw-semibold">Select a PDF file</label>
                        <input type="file" class="form-control" id="pdfFile" accept="application/pdf,.pdf">
                        <div class="form-text">The file is not uploaded to any server — everything happens on your device.</div>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="optStreams" checked>
                        <label class="form-check-label" for="optStreams">Compress object streams (fast web view)</label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="optMeta">
                        <label class="form-check-label" for="optMeta">Remove document metadata</label>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Optimize for Web</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <h5>Result</h5>
                        <table class="table table-bordered">
                            <tbody>
                                <tr><th style="width:50%">Original size</th><td id="origSize"></td></tr>
                                <tr><th>Optimized size</th><td id="newSize"></td></tr>
                                <tr><th>Total pages</th><td id="pageCount"></td></tr>
                                <tr><th>Size change</th><td id="sizeDiff"></td></tr>
                            </tbody>
                        </table>
                        <a href="#" class="btn btn-success w-100" id="downloadBtn">Download Optimized PDF</a>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select your PDF file.</li>
                <li>Click "Optimize for Web" — the file will rebuild in your browser.</li>
                <li>Download the optimized PDF and upload it to your website.</li>
            </ol>
            <p class="text-muted small">This tool rebuilds the PDF with compressed object streams, so browsers load the file fast and the first page shows quickly.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/pdf-lib@1.17.1/dist/pdf-lib.min.js"></script>
<script>
(function () {
    'use strict';
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var fileInput = document.getElementById('pdfFile');
    var downloadBtn = document.getElementById('downloadBtn');
    var blobUrl = null;

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function fmtSize(bytes) {
        if (bytes < 1024) { return bytes + ' B'; }
        var kb = bytes / 1024;
        if (kb < 1024) { return kb.toFixed(1) + ' KB'; }
        return (kb / 1024).toFixed(2) + ' MB';
    }
    function resetBtn() {
        goBtn.disabled = false;
        goBtn.textContent = 'Optimize for Web';
    }

    goBtn.addEventListener('click', function () {
        hideError();
        results.classList.add('d-none');
        var file = (fileInput.files && fileInput.files[0]) || null;
        if (!file) { showError('Please select a PDF file.'); return; }
        if (!/\.pdf$/i.test(file.name)) { showError('This does not look like a PDF file. Please select a .pdf file.'); return; }
        if (typeof PDFLib === 'undefined') { showError('PDF library could not load. Check your internet and try again.'); return; }
        goBtn.disabled = true;
        goBtn.textContent = 'Processing...';

        file.arrayBuffer().then(function (buf) {
            return PDFLib.PDFDocument.load(buf, { ignoreEncryption: true });
        }).then(function (pdf) {
            if (document.getElementById('optMeta').checked) {
                pdf.setTitle('');
                pdf.setAuthor('');
                pdf.setSubject('');
                pdf.setKeywords([]);
                pdf.setProducer('');
                pdf.setCreator('');
            }
            var useStreams = document.getElementById('optStreams').checked;
            return pdf.save({ useObjectStreams: useStreams }).then(function (bytes) {
                return { pages: pdf.getPageCount(), bytes: bytes };
            });
        }).then(function (out) {
            if (blobUrl) { URL.revokeObjectURL(blobUrl); }
            blobUrl = URL.createObjectURL(new Blob([out.bytes], { type: 'application/pdf' }));
            downloadBtn.href = blobUrl;
            var base = file.name.replace(/\.pdf$/i, '');
            downloadBtn.setAttribute('download', base + '-web.pdf');
            document.getElementById('origSize').textContent = fmtSize(file.size);
            document.getElementById('newSize').textContent = fmtSize(out.bytes.length);
            document.getElementById('pageCount').textContent = String(out.pages);
            var diff = file.size - out.bytes.length;
            var pct = file.size > 0 ? (Math.abs(diff) / file.size * 100).toFixed(1) : '0.0';
            document.getElementById('sizeDiff').textContent = (diff >= 0 ? 'Reduced: ' : 'Increased: ') + fmtSize(Math.abs(diff)) + ' (' + pct + '%)';
            results.classList.remove('d-none');
            resetBtn();
        }).catch(function () {
            showError('The PDF could not be processed. The file may be broken or password-protected.');
            resetBtn();
        });
    });
})();
</script>
@endsection
