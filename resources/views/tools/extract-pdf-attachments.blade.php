@extends('layouts.app')

@section('title', 'Extract PDF Attachments - Azlaan Tools')
@section('meta_description', 'Extract files embedded inside a PDF online for free. Your file never leaves your browser.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Extract PDF Attachments</h1>
            <p class="lead text-muted">Pull out files embedded inside a PDF. Select your PDF to see a list of hidden files inside it (documents, images, zips), and download each file separately. Everything happens in your browser — the file is never uploaded to a server.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="pdfFile" class="form-label fw-semibold">Select a PDF file</label>
                        <input type="file" class="form-control" id="pdfFile" accept=".pdf,application/pdf">
                        <div class="form-text">Only PDF files. Attachments are taken from embedded/document-level files and page annotations.</div>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Extract Attachments</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <h5 id="resultTitle" class="mb-3"></h5>
                        <div id="fileList" class="list-group"></div>
                        <p class="text-muted small mt-3">Note: some PDFs keep attachments in "portfolio" or encrypted streams that cannot be extracted. For a password-protected PDF, remove the password first.</p>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select a PDF file that has embedded files.</li>
                <li>Press the "Extract Attachments" button.</li>
                <li>Press "Download" next to each attachment in the list to save the file.</li>
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
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var fileList = document.getElementById('fileList');
    var resultTitle = document.getElementById('resultTitle');

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function fmtBytes(n) {
        if (n < 1024) return n + ' B';
        if (n < 1048576) return (n / 1024).toFixed(1) + ' KB';
        return (n / 1048576).toFixed(1) + ' MB';
    }
    function guessMime(name) {
        var ext = (name.split('.').pop() || '').toLowerCase();
        var map = { pdf: 'application/pdf', png: 'image/png', jpg: 'image/jpeg', jpeg: 'image/jpeg', gif: 'image/gif', webp: 'image/webp', txt: 'text/plain', csv: 'text/csv', doc: 'application/msword', docx: 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', xls: 'application/vnd.ms-excel', xlsx: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', pptx: 'application/vnd.openxmlformats-officedocument.presentationml.presentation', zip: 'application/zip', mp3: 'audio/mpeg', mp4: 'video/mp4', xml: 'text/xml', html: 'text/html' };
        return map[ext] || 'application/octet-stream';
    }

    function nameOf(dict, ctx) {
        if (!dict || !dict.lookup) return null;
        var raw = dict.lookup(PDFLib.PDFName.of('UF')) || dict.lookup(PDFLib.PDFName.of('F'));
        var obj = ctx.lookup(raw);
        if (obj && obj.decodeText) return obj.decodeText();
        return null;
    }

    function streamBytes(filespec, ctx) {
        if (!filespec || !filespec.lookup) return null;
        var efRaw = filespec.lookup(PDFLib.PDFName.of('EF'));
        var ef = ctx.lookup(efRaw);
        if (!ef || !ef.lookup) return null;
        var sRaw = ef.lookup(PDFLib.PDFName.of('UF')) || ef.lookup(PDFLib.PDFName.of('F'));
        var stream = ctx.lookup(sRaw);
        if (!stream) return null;
        if (stream.getContents) {
            try { return stream.getContents(); } catch (e) { return null; }
        }
        if (stream.contents) return stream.contents;
        return null;
    }

    function pushFound(found, seen, filespec, ctx, fallbackName) {
        var bytes = streamBytes(filespec, ctx);
        if (!bytes || !bytes.length) return;
        var nm = nameOf(filespec, ctx) || fallbackName;
        var key = nm + '|' + bytes.length;
        if (seen[key]) return;
        seen[key] = true;
        found.push({ name: nm, data: bytes });
    }

    async function doExtract() {
        hideError();
        var file = document.getElementById('pdfFile').files[0];
        if (!file) { showError('Please select a PDF file first.'); return; }
        goBtn.disabled = true;
        goBtn.textContent = 'Scanning...';
        try {
            var buf = await file.arrayBuffer();
            var doc = await PDFLib.PDFDocument.load(buf, { ignoreEncryption: true });
            var ctx = doc.context;
            var found = [];
            var seen = {};

            // 1. Document-level embedded files (Names -> EmbeddedFiles)
            try {
                var namesDict = ctx.lookup(doc.catalog.lookup(PDFLib.PDFName.of('Names')));
                if (namesDict && namesDict.lookup) {
                    var efTree = ctx.lookup(namesDict.lookup(PDFLib.PDFName.of('EmbeddedFiles')));
                    if (efTree && efTree.lookup) {
                        var arr = ctx.lookup(efTree.lookup(PDFLib.PDFName.of('Names')));
                        if (arr && arr.size && arr.get) {
                            for (var i = 0; i + 1 < arr.size(); i += 2) {
                                var fs = ctx.lookup(arr.get(i + 1));
                                pushFound(found, seen, fs, ctx, 'attachment-' + (found.length + 1));
                            }
                        }
                    }
                }
            } catch (e1) { /* continue */ }

            // 2. FileAttachment annotations on pages
            try {
                var pages = doc.getPages();
                for (var p = 0; p < pages.length; p++) {
                    var annotsRaw = pages[p].node.lookup(PDFLib.PDFName.of('Annots'));
                    var annots = ctx.lookup(annotsRaw);
                    if (!annots || !annots.size || !annots.get) continue;
                    for (var a = 0; a < annots.size(); a++) {
                        var annot = ctx.lookup(annots.get(a));
                        if (!annot || !annot.lookup) continue;
                        var sub = ctx.lookup(annot.lookup(PDFLib.PDFName.of('Subtype')));
                        var subName = sub ? sub.toString() : '';
                        if (subName === '/FileAttachment') {
                            var fs2 = ctx.lookup(annot.lookup(PDFLib.PDFName.of('FS')));
                            pushFound(found, seen, fs2, ctx, 'page' + (p + 1) + '-attachment-' + (found.length + 1));
                        }
                    }
                }
            } catch (e2) { /* continue */ }

            fileList.innerHTML = '';
            if (!found.length) {
                resultTitle.textContent = 'No embedded attachments found';
                var empty = document.createElement('div');
                empty.className = 'alert alert-info';
                empty.textContent = 'No extractable embedded file was found in this PDF. Some PDFs only have links, not attachments.';
                fileList.appendChild(empty);
            } else {
                resultTitle.textContent = found.length + ' attachment' + (found.length > 1 ? 's' : '') + ' found';
                found.forEach(function (f, idx) {
                    var item = document.createElement('div');
                    item.className = 'list-group-item d-flex justify-content-between align-items-center flex-wrap gap-2';
                    var left = document.createElement('div');
                    var nm = document.createElement('div');
                    nm.className = 'fw-semibold';
                    nm.textContent = f.name;
                    var meta = document.createElement('div');
                    meta.className = 'text-muted small';
                    meta.textContent = fmtBytes(f.data.length);
                    left.appendChild(nm);
                    left.appendChild(meta);
                    var btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'btn btn-sm btn-success';
                    btn.textContent = 'Download';
                    btn.addEventListener('click', function () {
                        var blob = new Blob([f.data], { type: guessMime(f.name) });
                        var url = URL.createObjectURL(blob);
                        var a = document.createElement('a');
                        a.href = url;
                        a.download = f.name;
                        document.body.appendChild(a);
                        a.click();
                        a.remove();
                        setTimeout(function () { URL.revokeObjectURL(url); }, 4000);
                    });
                    item.appendChild(left);
                    item.appendChild(btn);
                    fileList.appendChild(item);
                    void idx;
                });
            }
            results.classList.remove('d-none');
        } catch (err) {
            showError('Could not read this PDF (the file may be damaged or unsupported): ' + (err && err.message ? err.message : err));
        } finally {
            goBtn.disabled = false;
            goBtn.textContent = 'Extract Attachments';
        }
    }

    goBtn.addEventListener('click', doExtract);
})();
</script>
@endsection
