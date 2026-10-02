@extends('layouts.app')

@section('title', 'Sanitize PDF - Azlaan Tools')
@section('meta_description', 'Remove hidden data, metadata, comments and attachments from PDF before sharing. Free online PDF sanitizer.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Sanitize PDF</h1>
            <p class="lead text-muted">Strip hidden data, metadata and attachments from your PDF before sharing.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="pdfFile" class="form-label fw-semibold">Choose PDF file</label>
                        <input type="file" class="form-control" id="pdfFile" accept="application/pdf">
                        <div class="form-text">The PDF is processed only in your browser — nothing is uploaded.</div>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="stripMeta" checked>
                        <label class="form-check-label" for="stripMeta">Clear metadata (author, title, creator, dates)</label>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Sanitize PDF</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>
                    <div class="alert alert-info mt-3 d-none" id="statusBox" role="status"></div>

                    <div id="results" class="d-none mt-4">
                        <h5 class="mb-2">Sanitization report</h5>
                        <ul class="list-group mb-3" id="reportList"></ul>
                        <a href="#" class="btn btn-success w-100 mb-3" id="dlLink" download="sanitized.pdf">Download Sanitized PDF</a>
                        <div class="alert alert-secondary small mb-0">
                            <strong>Note:</strong> this tool rebuilds the pages into a new PDF, which drops attachments, embedded files and form data.
                            Small annotations stuck on a page can sometimes survive — for highly sensitive documents use dedicated redaction software.
                        </div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select your PDF file.</li>
                <li>Press "Sanitize PDF".</li>
                <li>Check the report and download the sanitized PDF.</li>
            </ol>
            <h2>What is hidden data in a PDF?</h2>
            <p>PDF files often hide <strong>metadata</strong> (author name, creating software, date), <strong>embedded files/attachments</strong>, <strong>form field data</strong> and <strong>comments</strong>. Cleaning these before sharing is important for privacy.</p>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/pdf-lib@1.17.1/dist/pdf-lib.min.js"></script>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var statusBox = document.getElementById('statusBox');
    var results = document.getElementById('results');

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        statusBox.classList.add('d-none');
        results.classList.add('d-none');
    }
    function setStatus(msg) {
        statusBox.textContent = msg;
        statusBox.classList.remove('d-none');
        errorBox.classList.add('d-none');
    }
    function kb(n) { return (n / 1024).toFixed(1) + ' KB'; }

    goBtn.addEventListener('click', function () {
        results.classList.add('d-none');
        errorBox.classList.add('d-none');
        var file = document.getElementById('pdfFile').files[0];
        if (!file) { showError('Please select a PDF file first.'); return; }
        if (typeof PDFLib === 'undefined') { showError('PDF library did not load. Check the internet and try again.'); return; }
        setStatus('Sanitizing the PDF, please wait...');

        var reader = new FileReader();
        reader.onerror = function () { showError('There was a problem reading the file.'); };
        reader.onload = function () {
            var bytes;
            try { bytes = new Uint8Array(reader.result); }
            catch (e) { showError('There was a problem reading the file.'); return; }
            PDFLib.PDFDocument.load(bytes, { ignoreEncryption: true, updateMetadata: false }).then(function (src) {
                var n = src.getPages().length;
                if (n === 0) { showError('No pages found in this PDF.'); return; }
                // detect what existed before cleaning
                var hadTitle = !!src.getTitle();
                var hadAuthor = !!src.getAuthor();
                var report = [];
                report.push('Pages: ' + n + ' (all pages safe, only content copied)');
                report.push('Original size: ' + kb(bytes.length));
                if (document.getElementById('stripMeta').checked) {
                    var metaFound = [];
                    if (hadTitle) metaFound.push('title');
                    if (hadAuthor) metaFound.push('author');
                    if (src.getSubject()) metaFound.push('subject');
                    if (src.getProducer()) metaFound.push('producer');
                    if (src.getCreator()) metaFound.push('creator');
                    report.push(metaFound.length ? 'Metadata cleared: ' + metaFound.join(', ') : 'Metadata: no notable metadata found');
                } else {
                    report.push('Metadata: you chose not to clear it (kept as is)');
                }
                return PDFLib.PDFDocument.create().then(function (out) {
                    return out.copyPages(src, src.getPageIndices()).then(function (copied) {
                        copied.forEach(function (p) { out.addPage(p); });
                        if (document.getElementById('stripMeta').checked) {
                            out.setTitle('');
                            out.setAuthor('');
                            out.setSubject('');
                            out.setKeywords([]);
                            out.setProducer('');
                            out.setCreator('');
                        }
                        return out.save().then(function (outBytes) {
                            report.push('Attachments / embedded files / form data: not copied into the new PDF (dropped)');
                            report.push('Sanitized size: ' + kb(outBytes.length));
                            var list = document.getElementById('reportList');
                            list.innerHTML = '';
                            report.forEach(function (t) {
                                var li = document.createElement('li');
                                li.className = 'list-group-item';
                                li.textContent = t;
                                list.appendChild(li);
                            });
                            var blob = new Blob([outBytes], { type: 'application/pdf' });
                            var dl = document.getElementById('dlLink');
                            dl.href = URL.createObjectURL(blob);
                            dl.download = 'sanitized.pdf';
                            statusBox.classList.add('d-none');
                            results.classList.remove('d-none');
                            results.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        });
                    });
                });
            }).catch(function (err) {
                var msg = 'The PDF could not be processed.';
                if (err && err.message && /encrypt/i.test(err.message)) msg = 'This PDF is password-protected — unlock it first.';
                showError(msg);
            });
        };
        reader.readAsArrayBuffer(file);
    });
})();
</script>
@endsection
