@extends('layouts.app')
@section('title', 'Redact PDF — Cover Words with Black Boxes Online Free — Azlaan Tools')
@section('meta_description', 'Redact a PDF online for free: enter words or phrases and cover every match with a black box. No signup, no upload — files stay in your browser. Visual cover only, text layer is not removed.')
@section('content')
<div class="container py-4 pdf-tool">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Redact PDF</h1>
            <p class="lead text-muted">Enter words or phrases — everywhere that word is found, a black box (black rectangle) is placed over it so it cannot be read.</p>

            <div class="alert alert-warning">
                <strong>Important honest warning:</strong> This tool draws black boxes <em>over</em> the text visually — the original text layer underneath is <strong>NOT removed</strong>. Someone with the right tools could still extract the hidden text. That is why this tool is <strong>not</strong> safe for sharing highly sensitive documents (CNIC, bank details, legal papers). Use it only for quick visual covering, not for true secure redaction.
            </div>

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
                        <label for="redactWords" class="form-label fw-semibold">Words / phrases to cover (one per line)</label>
                        <textarea id="redactWords" class="form-control mb-2" rows="5" placeholder="Example:&#10;0300-1234567&#10;Ahmed Khan&#10;Confidential"></textarea>
                        <p class="small text-muted mb-3">Matching is case-insensitive — “Ahmed”, “ahmed” and “AHMED” will all match. Every text item containing the word will be fully covered.</p>
                        <p class="mb-3">Result: <span id="matchInfo" class="fw-semibold">—</span></p>
                        <div class="d-flex flex-wrap gap-2">
                            <button type="button" id="processBtn" class="btn btn-success btn-lg">Redact &amp; Download PDF</button>
                            <button type="button" id="clearBtn" class="btn btn-outline-secondary">Clear</button>
                        </div>
                        <div id="progressWrap" class="progress mt-3 d-none" style="height: 24px;">
                            <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%;">0%</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="alert alert-info mt-4">
                <strong>Privacy note:</strong> Your file never leaves your browser — everything happens on your phone/computer, no upload takes place.
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Click the box above or drag and drop your PDF into it.</li>
                <li>Write one word or phrase per line that you want to cover.</li>
                <li>Click <strong>Redact &amp; Download PDF</strong> — black boxes are placed on the matches and a new PDF is downloaded.</li>
                <li>Remember: this is only a visual cover, the real text stays underneath — be sure to understand this before sharing a sensitive file.</li>
            </ol>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/pdfjs-dist@3.11.174/build/pdf.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/pdf-lib@1.17.1/dist/pdf-lib.min.js"></script>
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
    var redactWords = document.getElementById('redactWords');
    var matchInfo = document.getElementById('matchInfo');
    var processBtn = document.getElementById('processBtn');
    var clearBtn = document.getElementById('clearBtn');
    var progressWrap = document.getElementById('progressWrap');
    var progressBar = document.getElementById('progressBar');
    var storedBuffer = null;
    var storedName = 'document';
    var storedPageCount = 0;

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

    async function loadFile(file) {
        hideAlerts();
        if (!file) return;
        if (!(file.type === 'application/pdf' || file.name.toLowerCase().endsWith('.pdf'))) {
            showError('Please select a valid PDF file.');
            return;
        }
        if (typeof pdfjsLib === 'undefined' || typeof PDFLib === 'undefined') {
            showError('PDF libraries failed to load. Please check your internet connection and try again.');
            return;
        }
        try {
            var buf = await file.arrayBuffer();
            var pdfDoc = await pdfjsLib.getDocument({ data: buf.slice(0) }).promise;
            storedBuffer = buf;
            storedName = file.name.replace(/\.pdf$/i, '') || 'document';
            storedPageCount = pdfDoc.numPages;
            fileNameEl.textContent = file.name + ' (' + formatSize(file.size) + ') — ' + storedPageCount + ' page(s)';
            matchInfo.textContent = '—';
            toolWrap.classList.remove('d-none');
            showSuccess('PDF loaded. Enter the words to cover, then click Redact.');
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
        storedPageCount = 0;
        redactWords.value = '';
        matchInfo.textContent = '—';
        toolWrap.classList.add('d-none');
        progressWrap.classList.add('d-none');
        setProgress(0);
        hideAlerts();
    });

    processBtn.addEventListener('click', async function () {
        hideAlerts();
        if (!storedBuffer) { showError('Please select a PDF file first.'); return; }
        var targets = redactWords.value.split('\n').map(function (s) { return s.trim().toLowerCase(); }).filter(function (s) { return s.length > 0; });
        if (targets.length === 0) { showError('Please enter at least one word or phrase to cover (one per line).'); return; }
        processBtn.disabled = true;
        processBtn.textContent = 'Redacting...';
        progressWrap.classList.remove('d-none');
        setProgress(0);
        try {
            var jsDoc = await pdfjsLib.getDocument({ data: storedBuffer.slice(0) }).promise;
            var rectsByPage = {};
            var totalMatches = 0;
            var pagesWithMatches = {};
            for (var n = 1; n <= jsDoc.numPages; n++) {
                var page = await jsDoc.getPage(n);
                var tc = await page.getTextContent();
                var pageRects = [];
                tc.items.forEach(function (item) {
                    if (!item.str) return;
                    var hay = item.str.toLowerCase();
                    var hit = false;
                    for (var t = 0; t < targets.length; t++) {
                        if (hay.indexOf(targets[t]) !== -1) { hit = true; break; }
                    }
                    if (!hit) return;
                    var w = item.width || 0;
                    var h = item.height || Math.abs(item.transform[3]) || 12;
                    if (w <= 0) { w = item.str.length * h * 0.55; }
                    pageRects.push({
                        x: item.transform[4],
                        y: item.transform[5] - h * 0.3,
                        width: w,
                        height: h * 1.3
                    });
                    totalMatches++;
                });
                if (pageRects.length > 0) {
                    rectsByPage[n - 1] = pageRects;
                    pagesWithMatches[n] = true;
                }
                setProgress(Math.round((n / jsDoc.numPages) * 70));
            }
            var pageCountWithMatches = Object.keys(pagesWithMatches).length;
            matchInfo.textContent = totalMatches + ' matches covered on ' + pageCountWithMatches + ' page(s)';
            if (totalMatches === 0) {
                showError('No matches found. No word matched — check the spelling, or remember that a scanned PDF has no real text in it (use the OCR PDF tool for that).');
                return;
            }
            var libDoc = await PDFLib.PDFDocument.load(storedBuffer.slice(0), { ignoreEncryption: true });
            var pages = libDoc.getPages();
            var black = PDFLib.rgb(0, 0, 0);
            Object.keys(rectsByPage).forEach(function (idxKey) {
                var idx = parseInt(idxKey, 10);
                var pg = pages[idx];
                if (!pg) return;
                rectsByPage[idxKey].forEach(function (r) {
                    pg.drawRectangle({ x: r.x, y: r.y, width: r.width, height: r.height, color: black });
                });
            });
            setProgress(90);
            var bytes = await libDoc.save();
            setProgress(100);
            var blob = new Blob([bytes], { type: 'application/pdf' });
            downloadBlob(blob, storedName + '-redacted.pdf');
            showSuccess('Done! ' + totalMatches + ' matches covered on ' + pageCountWithMatches + ' page(s). Redacted PDF (' + formatSize(blob.size) + ') downloaded. Reminder: boxes are visual only — the text layer underneath is not removed.');
        } catch (err) {
            console.error(err);
            showError('Could not redact this PDF. It may be corrupted or protected. Please try a different file.');
        } finally {
            processBtn.disabled = false;
            processBtn.textContent = 'Redact & Download PDF';
            setTimeout(function () { progressWrap.classList.add('d-none'); setProgress(0); }, 800);
        }
    });
})();
</script>
@endsection
