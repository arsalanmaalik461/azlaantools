@extends('layouts.app')
@section('title', 'Unlock PDF — Remove Restrictions Online Free — Azlaan Tools')
@section('meta_description', 'Remove print, copy and edit restrictions from your own PDF online for free. No signup, no upload — files stay in your browser. No password cracking.')
@section('content')
<div class="container py-4 pdf-tool">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Unlock PDF</h1>
            <p class="lead text-muted">Remove owner restrictions (printing, copying and editing locks) from a PDF you own, so the file opens and works without limits.</p>

            <div id="alertBox" class="alert alert-danger d-none" role="alert"></div>
            <div id="successBox" class="alert alert-success d-none" role="alert"></div>

            <div id="dropZone" class="border border-2 border-dashed rounded-3 p-4 p-md-5 text-center mb-3" style="border-style: dashed !important; cursor: pointer; background: #f8f9fa;">
                <div class="fs-1 mb-2">🔓</div>
                <p class="mb-1 fw-semibold">Drag &amp; drop a PDF here</p>
                <p class="text-muted mb-3">or click to browse from your device</p>
                <button type="button" class="btn btn-primary">Select PDF File</button>
                <input type="file" id="fileInput" accept="application/pdf,.pdf" class="d-none">
            </div>

            <div id="toolWrap" class="d-none">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <p class="mb-2">File: <strong id="fileName" class="text-break"></strong></p>
                        <p class="mb-3">Status: <span id="lockInfo" class="fw-semibold"></span></p>
                        <div id="passwordWrap" class="d-none mb-3">
                            <label for="openPassword" class="form-label fw-semibold">Open password for this PDF</label>
                            <input type="password" id="openPassword" class="form-control form-control-lg" autocomplete="off" placeholder="Type the password you open this PDF with">
                            <div class="form-text">Only needed when the PDF asks for a password to open. Your password never leaves your browser, and nothing is guessed or cracked — this only works with a password you already know.</div>
                        </div>
                        <div id="rebuildNote" class="alert alert-warning d-none">
                            This PDF is encrypted, so its pages will be rebuilt as high-quality images to remove the restrictions (the same method as our Compress PDF tool). The unlocked file will open, print and edit freely, but text in it will no longer be selectable/copyable.
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <button type="button" id="processBtn" class="btn btn-success btn-lg">Unlock &amp; Download PDF</button>
                            <button type="button" id="clearBtn" class="btn btn-outline-secondary">Clear</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="alert alert-warning mt-4">
                <strong>Please note honestly:</strong> this tool removes <em>owner restrictions</em> (print / copy / edit locks) from PDFs you own. If a PDF needs an <strong>open password</strong> to view it, you can enter that password above — one you already know — and get an unlocked copy. This tool does <strong>not</strong> crack, guess or recover unknown passwords. Only unlock files you own or have permission to modify.
            </div>

            <div class="alert alert-info mt-4">
                <strong>Privacy note:</strong> Your file never leaves your browser — everything is done on your phone or computer, nothing is uploaded.
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Click the box above or drag and drop your PDF into it — its lock status will be shown.</li>
                <li>If the PDF asks for an open password, type the password you normally open it with.</li>
                <li>Click <strong>Unlock &amp; Download PDF</strong>.</li>
                <li>The downloaded copy has no owner restrictions, so printing, copying and editing work normally.</li>
            </ol>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/pdf-lib@1.17.1/dist/pdf-lib.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/pdfjs-dist@3.11.174/build/pdf.min.js"></script>
<script>
(function () {
    if (typeof pdfjsLib !== 'undefined') {
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdn.jsdelivr.net/npm/pdfjs-dist@3.11.174/build/pdf.worker.min.js';
    }
    var dropZone = document.getElementById('dropZone');
    var fileInput = document.getElementById('fileInput');
    var alertBox = document.getElementById('alertBox');
    var successBox = document.getElementById('successBox');
    var toolWrap = document.getElementById('toolWrap');
    var fileNameEl = document.getElementById('fileName');
    var lockInfo = document.getElementById('lockInfo');
    var passwordWrap = document.getElementById('passwordWrap');
    var openPassword = document.getElementById('openPassword');
    var rebuildNote = document.getElementById('rebuildNote');
    var processBtn = document.getElementById('processBtn');
    var clearBtn = document.getElementById('clearBtn');
    var storedBytes = null;
    var storedName = 'document';
    var pageTotal = 0;
    var isEncrypted = false;
    var needsPassword = false;
    var pdfJsDoc = null;

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

    function isPasswordError(err) {
        return err && (err.name === 'PasswordException' || err.code === 1 || err.code === 2);
    }
    // pdf-lib cannot decrypt an encrypted PDF at all: loading one with
    // { ignoreEncryption: true } parses the shell but leaves every content
    // stream encrypted, so save()/copyPages() then crash deep inside pdf-lib
    // ("Cannot read properties of undefined (reading 'Pages')"). That was the
    // old Unlock flow, which failed for every restricted file. pdf.js does
    // implement the PDF security handler, so encrypted files are opened with
    // pdf.js instead (owner-restricted files decrypt with the standard empty
    // user password; files with an open password need the password typed in).
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
        storedBytes = null;
        pdfJsDoc = null;
        isEncrypted = false;
        needsPassword = false;
        pageTotal = 0;
        passwordWrap.classList.add('d-none');
        rebuildNote.classList.add('d-none');
        openPassword.value = '';
        try {
            storedBytes = new Uint8Array(await file.arrayBuffer());
            storedName = file.name.replace(/\.pdf$/i, '') || 'document';
            try {
                var plainDoc = await PDFLib.PDFDocument.load(storedBytes.slice(0));
                pageTotal = plainDoc.getPageCount();
                fileNameEl.textContent = file.name + ' (' + formatSize(file.size) + ') — ' + pageTotal + ' page(s)';
                lockInfo.textContent = 'No encryption detected. The file will simply be re-saved cleanly.';
                toolWrap.classList.remove('d-none');
                showSuccess('PDF loaded. Click Unlock to download a clean copy.');
                return;
            } catch (loadErr) {
                isEncrypted = true;
            }
            if (typeof pdfjsLib === 'undefined') {
                storedBytes = null;
                toolWrap.classList.add('d-none');
                showError('This PDF is encrypted, and the PDF reader library failed to load. Please check your internet connection and try again.');
                return;
            }
            try {
                pdfJsDoc = await pdfjsLib.getDocument({ data: storedBytes.slice(0) }).promise;
                pageTotal = pdfJsDoc.numPages;
                fileNameEl.textContent = file.name + ' (' + formatSize(file.size) + ') — ' + pageTotal + ' page(s)';
                lockInfo.textContent = 'This PDF is encrypted / restricted (print / copy / edit locks). We will rebuild it without them.';
                rebuildNote.classList.remove('d-none');
                toolWrap.classList.remove('d-none');
                showSuccess('Restricted PDF loaded. Click Unlock to download an unrestricted copy.');
            } catch (pdfErr) {
                if (isPasswordError(pdfErr)) {
                    needsPassword = true;
                    fileNameEl.textContent = file.name + ' (' + formatSize(file.size) + ')';
                    lockInfo.textContent = 'This PDF needs its open password before it can be unlocked.';
                    passwordWrap.classList.remove('d-none');
                    rebuildNote.classList.remove('d-none');
                    toolWrap.classList.remove('d-none');
                    showSuccess('This PDF is password-protected. Type the password you open it with, then click Unlock.');
                } else {
                    throw pdfErr;
                }
            }
        } catch (err) {
            console.error(err);
            storedBytes = null;
            pdfJsDoc = null;
            toolWrap.classList.add('d-none');
            showError('Could not read this PDF. The file may be corrupted. Please try a different file.');
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
        storedBytes = null;
        pdfJsDoc = null;
        isEncrypted = false;
        needsPassword = false;
        openPassword.value = '';
        passwordWrap.classList.add('d-none');
        rebuildNote.classList.add('d-none');
        toolWrap.classList.add('d-none');
        hideAlerts();
    });

    function downloadPdfBytes(bytes, suffix) {
        var blob = new Blob([bytes], { type: 'application/pdf' });
        var url = URL.createObjectURL(blob);
        var a = document.createElement('a');
        a.href = url;
        a.download = storedName + suffix;
        document.body.appendChild(a);
        a.click();
        a.remove();
        setTimeout(function () { URL.revokeObjectURL(url); }, 4000);
        return blob;
    }
    function canvasToBlob(canvas, quality) {
        return new Promise(function (resolve) {
            canvas.toBlob(function (blob) { resolve(blob); }, 'image/jpeg', quality);
        });
    }
    async function rebuildUnlockedPdf() {
        // Rebuild each (decrypted) page as a high-quality image in a fresh,
        // unencrypted PDF — same approach as the Compress PDF tool.
        var out = await PDFLib.PDFDocument.create();
        for (var n = 1; n <= pdfJsDoc.numPages; n++) {
            processBtn.textContent = 'Unlocking... page ' + n + ' of ' + pdfJsDoc.numPages;
            var page = await pdfJsDoc.getPage(n);
            var baseViewport = page.getViewport({ scale: 1 });
            var scale = baseViewport.width > 900 || baseViewport.height > 900 ? 1.5 : 2;
            var viewport = page.getViewport({ scale: scale });
            var canvas = document.createElement('canvas');
            canvas.width = viewport.width;
            canvas.height = viewport.height;
            var ctx = canvas.getContext('2d');
            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            await page.render({ canvasContext: ctx, viewport: viewport }).promise;
            var jpgBlob = await canvasToBlob(canvas, 0.92);
            if (!jpgBlob) throw new Error('Image conversion failed on page ' + n);
            var jpgBytes = await jpgBlob.arrayBuffer();
            var embedded = await out.embedJpg(jpgBytes);
            var newPage = out.addPage([baseViewport.width, baseViewport.height]);
            newPage.drawImage(embedded, { x: 0, y: 0, width: baseViewport.width, height: baseViewport.height });
        }
        return out.save();
    }

    processBtn.addEventListener('click', async function () {
        hideAlerts();
        if (!storedBytes) { showError('Please select a PDF file first.'); return; }
        processBtn.disabled = true;
        processBtn.textContent = 'Unlocking...';
        try {
            if (!isEncrypted) {
                var pdfDoc = await PDFLib.PDFDocument.load(storedBytes.slice(0));
                var cleanBytes = await pdfDoc.save();
                var cleanBlob = downloadPdfBytes(cleanBytes, '-unlocked.pdf');
                showSuccess('Done! Your PDF (' + formatSize(cleanBlob.size) + ') has been re-saved and downloaded.');
                return;
            }
            if (needsPassword) {
                var pw = openPassword.value;
                if (!pw) {
                    showError('Please type the open password for this PDF first.');
                    return;
                }
                try {
                    pdfJsDoc = await pdfjsLib.getDocument({ data: storedBytes.slice(0), password: pw }).promise;
                    needsPassword = false;
                    pageTotal = pdfJsDoc.numPages;
                    passwordWrap.classList.add('d-none');
                } catch (pwErr) {
                    if (isPasswordError(pwErr)) {
                        showError('That password did not open this PDF. Please check it and try again.');
                    } else {
                        showError('Could not open this PDF with that password. Please try again.');
                    }
                    return;
                }
            }
            if (!pdfJsDoc) {
                showError('Please reload the PDF and try again.');
                return;
            }
            var rebuiltBytes = await rebuildUnlockedPdf();
            var rebuiltBlob = downloadPdfBytes(rebuiltBytes, '-unlocked.pdf');
            showSuccess('Done! Your unlocked PDF (' + formatSize(rebuiltBlob.size) + ') has been downloaded — it opens with no password and no print / copy / edit restrictions.');
        } catch (err) {
            console.error(err);
            showError('Could not unlock this PDF. Please try again, or try a different file.');
        } finally {
            processBtn.disabled = false;
            processBtn.textContent = 'Unlock & Download PDF';
        }
    });
})();
</script>
@endsection
