@extends('layouts.app')

@section('title', 'Attach Files to PDF Online Free - Azlaan Tools')
@section('meta_description', 'Attach files to PDF online free. Embed documents, images or any file inside your PDF as an attachment, all processed in your browser.')

@section('content')
<div class="container py-4 pdf-tool">
    <h1 class="mb-3">Attach Files to PDF</h1>
    <p class="lead text-muted">Embed any file (document, image, zip) inside your PDF as an attachment. Attached files appear in the attachments panel when you open the PDF. Everything happens in your browser — no file is uploaded anywhere.</p>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="mb-3">
                <label for="pdfInput" class="form-label fw-semibold">1. Select a PDF file</label>
                <input type="file" class="form-control" id="pdfInput" accept="application/pdf,.pdf">
                <div class="form-text" id="pdfInfo">No PDF selected yet.</div>
            </div>

            <div class="mb-3">
                <label for="attachInput" class="form-label fw-semibold">2. Select files to attach</label>
                <input type="file" class="form-control" id="attachInput" multiple>
                <div class="form-text">You can select more than one file. Each file max 50 MB.</div>
            </div>

            <div class="mb-3 d-none" id="attachListWrap">
                <label class="form-label fw-semibold">List of attachments</label>
                <ul class="list-group" id="attachList"></ul>
            </div>

            <button type="button" class="btn btn-primary w-100" id="goBtn">Attach and Download PDF</button>
            <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

            <div id="results" class="d-none mt-4">
                <div class="alert alert-success" id="successBox" role="alert"></div>
                <a class="btn btn-success w-100" id="downloadBtn" href="#" download="with-attachments.pdf">Download PDF with Attachments</a>
            </div>
        </div>
    </div>

    <h2>How to use</h2>
    <ol>
        <li>Select your PDF file.</li>
        <li>Select the files you want to attach inside the PDF (documents, images, zip).</li>
        <li>Click "Attach and Download PDF" and download the new PDF — the attachments appear in the attachments panel of Adobe Reader and similar tools.</li>
    </ol>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/pdf-lib@1.17.1/dist/pdf-lib.min.js"></script>
<script>
(function () {
    'use strict';
    var pdfInput = document.getElementById('pdfInput');
    var attachInput = document.getElementById('attachInput');
    var attachListWrap = document.getElementById('attachListWrap');
    var attachList = document.getElementById('attachList');
    var pdfInfo = document.getElementById('pdfInfo');
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var successBox = document.getElementById('successBox');
    var downloadBtn = document.getElementById('downloadBtn');
    var attachments = [];

    function fmtSize(b) {
        if (b < 1024) return b + ' B';
        if (b < 1048576) return (b / 1024).toFixed(1) + ' KB';
        return (b / 1048576).toFixed(1) + ' MB';
    }

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }

    pdfInput.addEventListener('change', function () {
        hideError();
        var f = pdfInput.files[0];
        if (!f) { pdfInfo.textContent = 'No PDF selected yet.'; return; }
        if (!/\.pdf$/i.test(f.name) && f.type !== 'application/pdf') {
            showError('Please select a valid PDF file.');
            pdfInput.value = '';
            return;
        }
        pdfInfo.textContent = f.name + ' (' + fmtSize(f.size) + ')';
    });

    attachInput.addEventListener('change', function () {
        hideError();
        var files = attachInput.files;
        for (var i = 0; i < files.length; i++) {
            (function (file) {
                if (file.size > 50 * 1048576) {
                    showError('File "' + file.name + '" is larger than 50 MB and was skipped.');
                    return;
                }
                var reader = new FileReader();
                reader.onload = function (e) {
                    attachments.push({ name: file.name, bytes: new Uint8Array(e.target.result), size: file.size });
                    renderList();
                };
                reader.readAsArrayBuffer(file);
            })(files[i]);
        }
        attachInput.value = '';
    });

    function renderList() {
        attachList.innerHTML = '';
        attachments.forEach(function (a, idx) {
            var li = document.createElement('li');
            li.className = 'list-group-item d-flex justify-content-between align-items-center';
            var span = document.createElement('span');
            span.className = 'text-break me-2';
            span.textContent = a.name + ' (' + fmtSize(a.size) + ')';
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'btn btn-sm btn-outline-danger flex-shrink-0';
            btn.textContent = 'Remove';
            btn.addEventListener('click', function () {
                attachments.splice(idx, 1);
                renderList();
            });
            li.appendChild(span);
            li.appendChild(btn);
            attachList.appendChild(li);
        });
        attachListWrap.classList.toggle('d-none', attachments.length === 0);
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var f = pdfInput.files[0];
        if (!f) { showError('Please select a PDF file first.'); return; }
        if (attachments.length === 0) { showError('Please add at least one attachment.'); return; }
        if (typeof PDFLib === 'undefined') { showError('The PDF library did not load. Check your internet and try again.'); return; }

        goBtn.disabled = true;
        goBtn.textContent = 'Processing...';
        var reader = new FileReader();
        reader.onload = function (e) {
            try {
                PDFLib.PDFDocument.load(new Uint8Array(e.target.result)).then(function (pdfDoc) {
                    attachments.forEach(function (a) {
                        pdfDoc.attach(a.bytes, a.name);
                    });
                    pdfDoc.save().then(function (bytes) {
                        var blob = new Blob([bytes], { type: 'application/pdf' });
                        var url = URL.createObjectURL(blob);
                        downloadBtn.href = url;
                        var base = f.name.replace(/\.pdf$/i, '');
                        downloadBtn.download = base + '-with-attachments.pdf';
                        successBox.textContent = attachments.length + ' file(s) attached. Press download now.';
                        results.classList.remove('d-none');
                        goBtn.disabled = false;
                        goBtn.textContent = 'Attach and Download PDF';
                    }).catch(function () {
                        goBtn.disabled = false;
                        goBtn.textContent = 'Attach and Download PDF';
                        showError('Could not save the PDF.');
                    });
                }).catch(function () {
                    goBtn.disabled = false;
                    goBtn.textContent = 'Attach and Download PDF';
                    showError('Could not read the PDF file.');
                });
            } catch (err) {
                goBtn.disabled = false;
                goBtn.textContent = 'Attach and Download PDF';
                showError('Something went wrong.');
            }
        };
        reader.readAsArrayBuffer(f);
    });
})();
</script>
@endsection
