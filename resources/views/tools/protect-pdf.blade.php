@extends('layouts.app')
@section('title', 'Protect PDF with Password Online Free — Azlaan Tools')
@section('meta_description', 'Add a password to a PDF online for free. Protect your PDF with strong encryption so only people with the password can open it. No signup, no upload.')
@section('content')
<div class="container py-4 pdf-tool">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Protect PDF with Password</h1>
            <p class="lead text-muted">Lock your PDF with a password so only people who know it can open the file. You can also control printing and copying.</p>

            <div id="alertBox" class="alert alert-danger d-none" role="alert"></div>
            <div id="successBox" class="alert alert-success d-none" role="alert"></div>

            <div id="dropZone" class="border border-2 border-dashed rounded-3 p-4 p-md-5 text-center mb-3" style="border-style: dashed !important; cursor: pointer; background: #f8f9fa;">
                <div class="fs-1 mb-2">🔒</div>
                <p class="mb-1 fw-semibold">Drag &amp; drop a PDF here</p>
                <p class="text-muted mb-3">or click to browse from your device</p>
                <button type="button" class="btn btn-primary">Select PDF File</button>
                <input type="file" id="fileInput" accept="application/pdf,.pdf" class="d-none">
            </div>

            <div id="toolWrap" class="d-none">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <p class="mb-3">File: <strong id="fileName" class="text-break"></strong></p>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="userPass" class="form-label fw-semibold">Open password (required)</label>
                                <input type="password" id="userPass" class="form-control form-control-lg" autocomplete="new-password" placeholder="Password to open the PDF">
                            </div>
                            <div class="col-md-6">
                                <label for="confirmPass" class="form-label fw-semibold">Confirm password</label>
                                <input type="password" id="confirmPass" class="form-control form-control-lg" autocomplete="new-password" placeholder="Type it again">
                            </div>
                        </div>
                        <div class="form-check mt-3">
                            <input class="form-check-input" type="checkbox" id="showPass">
                            <label class="form-check-label" for="showPass">Show passwords</label>
                        </div>
                        <hr>
                        <p class="fw-semibold mb-2">Permissions for people who open it</p>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="permPrint" checked>
                            <label class="form-check-label" for="permPrint">Allow printing</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="permCopy">
                            <label class="form-check-label" for="permCopy">Allow copying text</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="permModify">
                            <label class="form-check-label" for="permModify">Allow editing / modifying</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="permAnnotate" checked>
                            <label class="form-check-label" for="permAnnotate">Allow comments / annotations</label>
                        </div>
                        <div class="alert alert-warning mt-3 mb-0">
                            <strong>Important:</strong> Remember your password and keep it somewhere safe. If you forget it, the file cannot be opened again — we cannot recover it for you.
                        </div>
                        <div class="d-flex flex-wrap gap-2 mt-3">
                            <button type="button" id="processBtn" class="btn btn-success btn-lg">Protect &amp; Download PDF</button>
                            <button type="button" id="clearBtn" class="btn btn-outline-secondary">Clear</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="alert alert-info mt-4">
                <strong>Privacy note:</strong> Your file and your password never leave your browser — everything runs on your phone/computer, nothing is uploaded.
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Click the box above or drag and drop your PDF into it.</li>
                <li>Type a strong open password and confirm it.</li>
                <li>Choose which permissions (printing, copying, editing) people with the password will have.</li>
                <li>Click <strong>Protect &amp; Download PDF</strong>. Test the downloaded file once to make sure the password works.</li>
            </ol>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/pdf-lib-plus-encrypt@1.1.0/dist/pdf-lib-plus-encrypt.min.js"></script>
<script>
(function () {
    var dropZone = document.getElementById('dropZone');
    var fileInput = document.getElementById('fileInput');
    var alertBox = document.getElementById('alertBox');
    var successBox = document.getElementById('successBox');
    var toolWrap = document.getElementById('toolWrap');
    var fileNameEl = document.getElementById('fileName');
    var userPass = document.getElementById('userPass');
    var confirmPass = document.getElementById('confirmPass');
    var showPass = document.getElementById('showPass');
    var permPrint = document.getElementById('permPrint');
    var permCopy = document.getElementById('permCopy');
    var permModify = document.getElementById('permModify');
    var permAnnotate = document.getElementById('permAnnotate');
    var processBtn = document.getElementById('processBtn');
    var clearBtn = document.getElementById('clearBtn');
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

    showPass.addEventListener('change', function () {
        var t = showPass.checked ? 'text' : 'password';
        userPass.type = t;
        confirmPass.type = t;
    });

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
            var doc = await PDFLib.PDFDocument.load(buf.slice(0));
            storedBuffer = buf;
            storedName = file.name.replace(/\.pdf$/i, '') || 'document';
            fileNameEl.textContent = file.name + ' (' + formatSize(file.size) + ') — ' + doc.getPageCount() + ' page(s)';
            toolWrap.classList.remove('d-none');
            showSuccess('PDF loaded. Now set a password below.');
        } catch (err) {
            console.error(err);
            storedBuffer = null;
            toolWrap.classList.add('d-none');
            showError('Could not read this PDF. It may be corrupted or already password-protected. Please try a different file.');
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
        userPass.value = '';
        confirmPass.value = '';
        toolWrap.classList.add('d-none');
        hideAlerts();
    });

    processBtn.addEventListener('click', async function () {
        hideAlerts();
        if (!storedBuffer) { showError('Please select a PDF file first.'); return; }
        var pw = userPass.value;
        if (!pw || pw.length < 4) { showError('Please enter a password of at least 4 characters.'); return; }
        if (pw !== confirmPass.value) { showError('The two passwords do not match. Please type them again.'); return; }
        processBtn.disabled = true;
        processBtn.textContent = 'Protecting...';
        try {
            var pdfDoc = await PDFLib.PDFDocument.load(storedBuffer.slice(0));
            if (typeof pdfDoc.encrypt !== 'function') {
                showError('Encryption is not available in the loaded library. Please reload the page and try again.');
                return;
            }
            await pdfDoc.encrypt({
                userPassword: pw,
                ownerPassword: pw,
                permissions: {
                    printing: permPrint.checked ? 'highResolution' : false,
                    modifying: permModify.checked,
                    copying: permCopy.checked,
                    annotating: permAnnotate.checked
                }
            });
            var bytes = await pdfDoc.save();
            var blob = new Blob([bytes], { type: 'application/pdf' });
            var url = URL.createObjectURL(blob);
            var a = document.createElement('a');
            a.href = url;
            a.download = storedName + '-protected.pdf';
            document.body.appendChild(a);
            a.click();
            a.remove();
            setTimeout(function () { URL.revokeObjectURL(url); }, 4000);
            showSuccess('Done! Your password-protected PDF (' + formatSize(blob.size) + ') has been downloaded. Open it once to test the password.');
        } catch (err) {
            console.error(err);
            showError('Could not protect this PDF. It may be corrupted or already password-protected. Please try a different file.');
        } finally {
            processBtn.disabled = false;
            processBtn.textContent = 'Protect & Download PDF';
        }
    });
})();
</script>
@endsection
