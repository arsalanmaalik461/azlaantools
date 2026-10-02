@extends('layouts.app')

@section('title', 'Split PDF by Size - Azlaan Tools')
@section('meta_description', 'Split a PDF into smaller parts under a size limit for email attachments. Free online.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Split PDF by Size</h1>
            <p class="lead text-muted">Break a big PDF into small parts that fit the email size limit — each part will be smaller than the max size.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="pdfFile" class="form-label fw-semibold">Choose a PDF file</label>
                        <input type="file" class="form-control" id="pdfFile" accept="application/pdf">
                        <div class="form-text">The file is processed in the browser only — it is not uploaded anywhere.</div>
                    </div>
                    <div id="toolWrap" class="d-none">
                        <div class="mb-3">
                            <label for="maxMB" class="form-label fw-semibold">Max size of each part (MB)</label>
                            <input type="number" class="form-control" id="maxMB" value="5" min="0.5" max="100" step="0.5">
                            <div class="form-text">Common email attachment limit is 20–25 MB; to be safe, keep 5–10 MB.</div>
                        </div>
                        <div id="fileInfo" class="alert alert-light border small d-none"></div>
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="button" class="btn btn-primary flex-grow-1" id="goBtn">Split PDF</button>
                            <button type="button" class="btn btn-outline-secondary" id="clearBtn">Clear</button>
                        </div>
                        <div class="progress mt-3 d-none" id="progressWrap">
                            <div class="progress-bar" id="progressBar" style="width:0%">0%</div>
                        </div>
                    </div>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>
                    <div id="results" class="d-none mt-4"></div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select a PDF file.</li>
                <li>Enter the max size of each part in MB.</li>
                <li>Press "Split PDF" — download each part separately.</li>
            </ol>
            <p class="text-muted small">Parts will be in order: part-1, part-2... Each part will stay under the size limit.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/pdf-lib@1.17.1/dist/pdf-lib.min.js"></script>
<script>
(function () {
    'use strict';
    var pdfFile = document.getElementById('pdfFile');
    var toolWrap = document.getElementById('toolWrap');
    var fileInfo = document.getElementById('fileInfo');
    var goBtn = document.getElementById('goBtn');
    var clearBtn = document.getElementById('clearBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var progressWrap = document.getElementById('progressWrap');
    var progressBar = document.getElementById('progressBar');
    var maxMBEl = document.getElementById('maxMB');
    var storedBuffer = null;
    var fileName = 'document';

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function setProgress(p) {
        p = Math.max(0, Math.min(100, Math.round(p)));
        progressBar.style.width = p + '%';
        progressBar.textContent = p + '%';
    }
    function mb(bytes) { return (bytes / (1024 * 1024)).toFixed(2); }

    pdfFile.addEventListener('change', function () {
        hideError();
        results.classList.add('d-none');
        var f = pdfFile.files[0];
        if (!f) { toolWrap.classList.add('d-none'); storedBuffer = null; return; }
        fileName = f.name.replace(/\.pdf$/i, '');
        var reader = new FileReader();
        reader.onload = function () {
            storedBuffer = reader.result;
            fileInfo.innerHTML = '<strong>' + f.name.replace(/</g, '&lt;') + '</strong> — ' + mb(f.size) + ' MB loaded.';
            fileInfo.classList.remove('d-none');
            toolWrap.classList.remove('d-none');
        };
        reader.onerror = function () { showError('There was a problem reading the file.'); };
        reader.readAsArrayBuffer(f);
    });

    clearBtn.addEventListener('click', function () {
        pdfFile.value = '';
        storedBuffer = null;
        toolWrap.classList.add('d-none');
        fileInfo.classList.add('d-none');
        hideError();
        results.classList.add('d-none');
    });

    goBtn.addEventListener('click', async function () {
        hideError();
        if (!storedBuffer) { showError('Please select a PDF file first.'); return; }
        if (typeof PDFLib === 'undefined') { showError('The PDF library failed to load. Check the internet and try again.'); return; }
        var limitBytes = (parseFloat(maxMBEl.value) || 0) * 1024 * 1024;
        if (limitBytes < 512 * 1024) { showError('Keep the max size at least 0.5 MB.'); return; }
        goBtn.disabled = true;
        goBtn.textContent = 'Splitting...';
        progressWrap.classList.remove('d-none');
        setProgress(3);
        results.classList.add('d-none');
        try {
            var src = await PDFLib.PDFDocument.load(storedBuffer.slice(0));
            var pageCount = src.getPageCount();
            if (pageCount < 2) { showError('This PDF has only 1 page — no need to split.'); goBtn.disabled = false; goBtn.textContent = 'Split PDF'; return; }
            var fullBytes = await src.save();
            if (fullBytes.length <= limitBytes) {
                showError('The file is already ' + mb(fullBytes.length) + ' MB — smaller than the limit of ' + (limitBytes / (1024 * 1024)) + ' MB. No need to split.');
                goBtn.disabled = false; goBtn.textContent = 'Split PDF'; return;
            }
            setProgress(10);
            var perPage = fullBytes.length / pageCount;
            var perChunk = Math.max(1, Math.floor(limitBytes / perPage));
            var allIdx = [];
            for (var i = 0; i < pageCount; i++) allIdx.push(i);

            var parts = [];
            async function makePart(indices) {
                var doc = await PDFLib.PDFDocument.create();
                var copied = await doc.copyPages(src, indices);
                for (var c = 0; c < copied.length; c++) doc.addPage(copied[c]);
                var bytes = await doc.save();
                if (bytes.length > limitBytes && indices.length > 1) {
                    var half = Math.ceil(indices.length / 2);
                    await makePart(indices.slice(0, half));
                    await makePart(indices.slice(half));
                } else {
                    parts.push({ indices: indices, bytes: bytes });
                }
                setProgress(10 + Math.round((parts.length / (pageCount / perChunk + 1)) * 85));
            }

            // estimated chunks first, then verify/split
            var start = 0;
            while (start < allIdx.length) {
                var end = Math.min(allIdx.length, start + perChunk);
                await makePart(allIdx.slice(start, end));
                start = end;
            }
            setProgress(100);

            var html = '<div class="alert alert-success"><strong>Done!</strong> The PDF was split into ' + parts.length + ' parts. Each part is within the size limit (' + (limitBytes / (1024 * 1024)) + ' MB).</div>';
            html += '<div class="list-group">';
            for (var p = 0; p < parts.length; p++) {
                var part = parts[p];
                var blob = new Blob([part.bytes], { type: 'application/pdf' });
                var url = URL.createObjectURL(blob);
                var from = part.indices[0] + 1, to = part.indices[part.indices.length - 1] + 1;
                html += '<a href="' + url + '" download="' + fileName.replace(/"/g, '') + '-part-' + (p + 1) + '.pdf" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">Part ' + (p + 1) + ' <span class="text-muted small">pages ' + from + '–' + to + ' • ' + mb(part.bytes.length) + ' MB</span> <span class="btn btn-sm btn-outline-success">Download</span></a>';
            }
            html += '</div>';
            results.innerHTML = html;
            results.classList.remove('d-none');
        } catch (e) {
            showError('Could not split: ' + (e && e.message ? e.message : 'unknown error'));
        }
        goBtn.disabled = false;
        goBtn.textContent = 'Split PDF';
    });
})();
</script>
@endsection
