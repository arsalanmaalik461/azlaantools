@extends('layouts.app')
@section('title', 'PDF Metadata Editor — Edit Title, Author & Keywords Online Free — Azlaan Tools')
@section('meta_description', 'View and edit PDF metadata online for free: title, author, subject and keywords. No signup, no upload — files stay in your browser.')
@section('content')
<div class="container py-4 pdf-tool">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">PDF Metadata Editor</h1>
            <p class="lead text-muted">View and edit your PDF's hidden information — title, author, subject and keywords saved in the file's properties.</p>

            <div id="alertBox" class="alert alert-danger d-none" role="alert"></div>
            <div id="successBox" class="alert alert-success d-none" role="alert"></div>

            <div id="dropZone" class="border border-2 border-dashed rounded-3 p-4 p-md-5 text-center mb-3" style="border-style: dashed !important; cursor: pointer; background: #f8f9fa;">
                <div class="fs-1 mb-2">📄</div>
                <p class="mb-1 fw-semibold">Drag &amp; drop a PDF here</p>
                <p class="text-muted mb-3">or click to browse from your device</p>
                <button type="button" class="btn btn-primary">Select PDF File</button>
                <input type="file" id="fileInput" accept="application/pdf,.pdf" class="d-none">
            </div>

            <div id="toolWrap" class="d-none">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <p class="mb-3">File: <strong id="fileName" class="text-break"></strong></p>
                        <h3 class="h6 fw-bold">Current metadata</h3>
                        <ul class="list-unstyled small mb-4">
                            <li>Title: <strong id="curTitle">—</strong></li>
                            <li>Author: <strong id="curAuthor">—</strong></li>
                            <li>Subject: <strong id="curSubject">—</strong></li>
                            <li>Keywords: <strong id="curKeywords">—</strong></li>
                            <li>Creator: <strong id="curCreator">—</strong></li>
                            <li>Producer: <strong id="curProducer">—</strong></li>
                            <li>Created: <strong id="curCreated">—</strong></li>
                            <li>Modified: <strong id="curModified">—</strong></li>
                        </ul>
                        <h3 class="h6 fw-bold">Edit metadata</h3>
                        <div class="mb-3">
                            <label for="editTitle" class="form-label fw-semibold">Title</label>
                            <input type="text" id="editTitle" class="form-control" placeholder="Document title">
                        </div>
                        <div class="mb-3">
                            <label for="editAuthor" class="form-label fw-semibold">Author</label>
                            <input type="text" id="editAuthor" class="form-control" placeholder="Author name">
                        </div>
                        <div class="mb-3">
                            <label for="editSubject" class="form-label fw-semibold">Subject</label>
                            <input type="text" id="editSubject" class="form-control" placeholder="Subject / short description">
                        </div>
                        <div class="mb-3">
                            <label for="editKeywords" class="form-label fw-semibold">Keywords (comma-separated)</label>
                            <input type="text" id="editKeywords" class="form-control" placeholder="invoice, 2026, azlaan">
                            <p class="small text-muted mt-1 mb-0">Separate keywords with commas — each keyword is saved separately.</p>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <button type="button" id="processBtn" class="btn btn-success btn-lg">Save &amp; Download PDF</button>
                            <button type="button" id="clearBtn" class="btn btn-outline-secondary">Clear</button>
                        </div>
                        <div id="progressWrap" class="progress mt-3 d-none" style="height: 24px;">
                            <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%;">0%</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="alert alert-info mt-4">
                <strong>Privacy note:</strong> Your file never leaves your browser — everything runs on your phone or computer, nothing is uploaded.
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Click the box above or drag and drop your PDF into it — current metadata will show right away.</li>
                <li>Type the changes you want in Title, Author, Subject or Keywords (the fields are already filled with current values).</li>
                <li>Click <strong>Save &amp; Download PDF</strong> — the modification date is set to today automatically and the updated PDF will download.</li>
            </ol>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/pdf-lib@1.17.1/dist/pdf-lib.min.js"></script>
<script>
(function () {
    var dropZone = document.getElementById('dropZone');
    var fileInput = document.getElementById('fileInput');
    var alertBox = document.getElementById('alertBox');
    var successBox = document.getElementById('successBox');
    var toolWrap = document.getElementById('toolWrap');
    var fileNameEl = document.getElementById('fileName');
    var curTitle = document.getElementById('curTitle');
    var curAuthor = document.getElementById('curAuthor');
    var curSubject = document.getElementById('curSubject');
    var curKeywords = document.getElementById('curKeywords');
    var curCreator = document.getElementById('curCreator');
    var curProducer = document.getElementById('curProducer');
    var curCreated = document.getElementById('curCreated');
    var curModified = document.getElementById('curModified');
    var editTitle = document.getElementById('editTitle');
    var editAuthor = document.getElementById('editAuthor');
    var editSubject = document.getElementById('editSubject');
    var editKeywords = document.getElementById('editKeywords');
    var processBtn = document.getElementById('processBtn');
    var clearBtn = document.getElementById('clearBtn');
    var progressWrap = document.getElementById('progressWrap');
    var progressBar = document.getElementById('progressBar');
    var storedBuffer = null;
    var storedName = 'document';

    function showError(msg) {
        alertBox.textContent = msg;
        alertBox.classList.remove('d-none');
        successBox.classList.add('d-none');
    }
    function showSuccess(msg) {
        successBox.textContent = msg;
        successBox.classList.remove('d-none');
        alertBox.classList.add('d-none');
    }
    function hideAlerts() {
        alertBox.classList.add('d-none');
        successBox.classList.add('d-none');
    }
    function formatSize(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
    }
    function downloadBlob(blob, filename) {
        var url = URL.createObjectURL(blob);
        var a = document.createElement('a');
        a.href = url;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        a.remove();
        setTimeout(function () { URL.revokeObjectURL(url); }, 4000);
    }
    function setProgress(pct) {
        progressBar.style.width = pct + '%';
        progressBar.textContent = pct + '%';
    }
    function showVal(v) {
        return (v === undefined || v === null || v === '') ? '—' : String(v);
    }
    function formatDate(d) {
        if (!d) return '—';
        try {
            var dt = (d instanceof Date) ? d : new Date(d);
            if (isNaN(dt.getTime())) return '—';
            return dt.toLocaleString();
        } catch (e) {
            return '—';
        }
    }
    function safeGet(doc, method) {
        try { return doc[method](); } catch (e) { return undefined; }
    }

    async function loadFile(file) {
        hideAlerts();
        if (!file) return;
        if (!(file.type === 'application/pdf' || file.name.toLowerCase().endsWith('.pdf'))) {
            showError('Please select a valid PDF file.');
            return;
        }
        if (typeof PDFLib === 'undefined') {
            showError('PDF library failed to load. Please check your internet connection and try again.');
            return;
        }
        try {
            var buf = await file.arrayBuffer();
            var doc = await PDFLib.PDFDocument.load(buf.slice(0), { ignoreEncryption: true });
            storedBuffer = buf;
            storedName = file.name.replace(/\.pdf$/i, '') || 'document';
            var title = safeGet(doc, 'getTitle');
            var author = safeGet(doc, 'getAuthor');
            var subject = safeGet(doc, 'getSubject');
            var keywords = safeGet(doc, 'getKeywords');
            var creator = safeGet(doc, 'getCreator');
            var producer = safeGet(doc, 'getProducer');
            var created = safeGet(doc, 'getCreationDate');
            var modified = safeGet(doc, 'getModificationDate');
            curTitle.textContent = showVal(title);
            curAuthor.textContent = showVal(author);
            curSubject.textContent = showVal(subject);
            curKeywords.textContent = showVal(keywords);
            curCreator.textContent = showVal(creator);
            curProducer.textContent = showVal(producer);
            curCreated.textContent = formatDate(created);
            curModified.textContent = formatDate(modified);
            editTitle.value = title || '';
            editAuthor.value = author || '';
            editSubject.value = subject || '';
            editKeywords.value = keywords || '';
            fileNameEl.textContent = file.name + ' (' + formatSize(file.size) + ') — ' + doc.getPageCount() + ' page(s)';
            toolWrap.classList.remove('d-none');
            showSuccess('Metadata loaded. Edit the fields below and click Save to download the updated PDF.');
        } catch (err) {
            console.error(err);
            storedBuffer = null;
            toolWrap.classList.add('d-none');
            showError('Could not read this PDF. It may be corrupted or password-protected. Please try a different file.');
        }
    }

    dropZone.addEventListener('click', function () { fileInput.click(); });
    fileInput.addEventListener('change', function () {
        if (fileInput.files && fileInput.files[0]) loadFile(fileInput.files[0]);
        fileInput.value = '';
    });
    dropZone.addEventListener('dragover', function (e) { e.preventDefault(); dropZone.style.background = '#e9f2ff'; });
    dropZone.addEventListener('dragleave', function () { dropZone.style.background = '#f8f9fa'; });
    dropZone.addEventListener('drop', function (e) {
        e.preventDefault();
        dropZone.style.background = '#f8f9fa';
        if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files[0]) loadFile(e.dataTransfer.files[0]);
    });
    clearBtn.addEventListener('click', function () {
        storedBuffer = null;
        editTitle.value = '';
        editAuthor.value = '';
        editSubject.value = '';
        editKeywords.value = '';
        toolWrap.classList.add('d-none');
        progressWrap.classList.add('d-none');
        setProgress(0);
        hideAlerts();
    });

    processBtn.addEventListener('click', async function () {
        hideAlerts();
        if (!storedBuffer) { showError('Please select a PDF file first.'); return; }
        processBtn.disabled = true;
        processBtn.textContent = 'Saving...';
        progressWrap.classList.remove('d-none');
        setProgress(40);
        try {
            var doc = await PDFLib.PDFDocument.load(storedBuffer.slice(0), { ignoreEncryption: true });
            doc.setTitle(editTitle.value.trim());
            doc.setAuthor(editAuthor.value.trim());
            doc.setSubject(editSubject.value.trim());
            var kwArray = editKeywords.value.split(',').map(function (s) { return s.trim(); }).filter(function (s) { return s.length > 0; });
            doc.setKeywords(kwArray);
            doc.setModificationDate(new Date());
            setProgress(80);
            var bytes = await doc.save();
            setProgress(100);
            var blob = new Blob([bytes], { type: 'application/pdf' });
            downloadBlob(blob, storedName + '-metadata.pdf');
            showSuccess('Done! Updated PDF (' + formatSize(blob.size) + ') has been downloaded with the new metadata.');
        } catch (err) {
            console.error(err);
            showError('Could not save the metadata. Please try again with a different PDF.');
        } finally {
            processBtn.disabled = false;
            processBtn.textContent = 'Save & Download PDF';
            setTimeout(function () { progressWrap.classList.add('d-none'); setProgress(0); }, 800);
        }
    });
})();
</script>
@endsection
