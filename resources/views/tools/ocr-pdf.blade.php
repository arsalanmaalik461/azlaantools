@extends('layouts.app')
@section('title', 'OCR PDF — Extract Text from Scanned PDF Online Free — Azlaan Tools')
@section('meta_description', 'OCR a scanned PDF online for free: extract text from scanned pages in English and Urdu. No signup, no upload — OCR runs in your browser.')
@section('content')
<div class="container py-4 pdf-tool">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">OCR PDF</h1>
            <p class="lead text-muted">Extract text from a scanned PDF — each page is turned into an image and read with OCR, then all the text is shown in one place where you can copy or download it.</p>

            <div class="alert alert-warning">
                <strong>Honest note:</strong> The accuracy of scanned or handwritten text depends on the page quality — a clean, straight, high-resolution scan gives a better result; blurry scans or handwriting may have mistakes. Languages: English + Urdu attempted (if Urdu is not available, it falls back to English).
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
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <button type="button" id="processBtn" class="btn btn-success btn-lg">Run OCR &amp; Extract Text</button>
                            <button type="button" id="clearBtn" class="btn btn-outline-secondary">Clear</button>
                        </div>
                        <div id="progressWrap" class="d-none mb-3">
                            <div class="progress" style="height: 24px;">
                                <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%;">0%</div>
                            </div>
                            <p class="small text-muted mt-2 mb-0">Status: <span id="statusText">Waiting...</span></p>
                        </div>
                        <label for="resultText" class="form-label fw-semibold">Extracted Text</label>
                        <textarea id="resultText" class="form-control" rows="12" placeholder="OCR text will appear here, page by page..."></textarea>
                        <div class="small text-muted mt-2">Characters: <strong id="charCount">0</strong> &nbsp; Words: <strong id="wordCount">0</strong></div>
                        <div class="d-flex flex-wrap gap-2 mt-3">
                            <button type="button" id="copyBtn" class="btn btn-primary">Copy Text</button>
                            <button type="button" id="downloadBtn" class="btn btn-outline-success">Download .txt</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="alert alert-info mt-4">
                <strong>Privacy note:</strong> Your file never leaves your browser — all work happens on your phone/computer, nothing is uploaded. On first use the OCR language data is downloaded from the internet, so the first run may take some time.
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Click the box above or drag and drop your scanned PDF into it.</li>
                <li>Click <strong>Run OCR &amp; Extract Text</strong> — each page will be rendered and read, and the progress bar will show the page-by-page status.</li>
                <li>When the text is complete, check it, then save it with <strong>Copy Text</strong> or <strong>Download .txt</strong>.</li>
                <li>A PDF with more pages will take more time — each page is read separately.</li>
            </ol>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/pdfjs-dist@3.11.174/build/pdf.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/tesseract.js@5/dist/tesseract.min.js"></script>
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
    var processBtn = document.getElementById('processBtn');
    var clearBtn = document.getElementById('clearBtn');
    var progressWrap = document.getElementById('progressWrap');
    var progressBar = document.getElementById('progressBar');
    var statusText = document.getElementById('statusText');
    var resultText = document.getElementById('resultText');
    var charCount = document.getElementById('charCount');
    var wordCount = document.getElementById('wordCount');
    var copyBtn = document.getElementById('copyBtn');
    var downloadBtn = document.getElementById('downloadBtn');
    var storedBuffer = null;
    var storedName = 'document';
    var storedPdfDoc = null;

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
    function updateCounts() {
        var t = resultText.value;
        charCount.textContent = t.length;
        wordCount.textContent = t.trim() ? t.trim().split(/\s+/).length : 0;
    }

    async function loadFile(file) {
        hideAlerts();
        if (!file) return;
        if (!(file.type === 'application/pdf' || file.name.toLowerCase().endsWith('.pdf'))) {
            showError('Please select a valid PDF file.');
            return;
        }
        if (typeof pdfjsLib === 'undefined') {
            showError('PDF library failed to load. Please check your internet connection and try again.');
            return;
        }
        try {
            var buf = await file.arrayBuffer();
            var doc = await pdfjsLib.getDocument({ data: buf.slice(0) }).promise;
            storedBuffer = buf;
            storedPdfDoc = doc;
            storedName = file.name.replace(/\.pdf$/i, '') || 'document';
            fileNameEl.textContent = file.name + ' (' + formatSize(file.size) + ') — ' + doc.numPages + ' page(s)';
            resultText.value = '';
            updateCounts();
            toolWrap.classList.remove('d-none');
            showSuccess('PDF loaded with ' + doc.numPages + ' page(s). Click Run OCR to extract text from its pages.');
        } catch (err) {
            console.error(err);
            storedBuffer = null;
            storedPdfDoc = null;
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
    resultText.addEventListener('input', updateCounts);
    clearBtn.addEventListener('click', function () {
        storedBuffer = null;
        storedPdfDoc = null;
        resultText.value = '';
        updateCounts();
        toolWrap.classList.add('d-none');
        progressWrap.classList.add('d-none');
        setProgress(0);
        statusText.textContent = 'Waiting...';
        hideAlerts();
    });
    copyBtn.addEventListener('click', async function () {
        if (!resultText.value) { showError('There is no text to copy yet.'); return; }
        try {
            await navigator.clipboard.writeText(resultText.value);
            showSuccess('Copied to clipboard!');
        } catch (e) {
            resultText.select();
            document.execCommand('copy');
            showSuccess('Copied to clipboard!');
        }
    });
    downloadBtn.addEventListener('click', function () {
        if (!resultText.value) { showError('There is no text to download yet.'); return; }
        var blob = new Blob([resultText.value], { type: 'text/plain;charset=utf-8' });
        downloadBlob(blob, storedName + '-ocr.txt');
        showSuccess('Text file downloaded.');
    });

    processBtn.addEventListener('click', async function () {
        hideAlerts();
        if (!storedPdfDoc) { showError('Please select a PDF file first.'); return; }
        if (typeof Tesseract === 'undefined') { showError('OCR library failed to load. Please check your internet connection and refresh the page.'); return; }
        processBtn.disabled = true;
        processBtn.textContent = 'Running OCR...';
        progressWrap.classList.remove('d-none');
        setProgress(0);
        statusText.textContent = 'Starting OCR...';
        resultText.value = '';
        updateCounts();
        var allText = [];
        var usedLang = 'eng+urd';
        try {
            var totalPages = storedPdfDoc.numPages;
            for (var n = 1; n <= totalPages; n++) {
                statusText.textContent = 'Rendering page ' + n + ' of ' + totalPages + '...';
                var page = await storedPdfDoc.getPage(n);
                var viewport = page.getViewport({ scale: 2 });
                var canvas = document.createElement('canvas');
                canvas.width = viewport.width;
                canvas.height = viewport.height;
                var ctx = canvas.getContext('2d');
                await page.render({ canvasContext: ctx, viewport: viewport }).promise;
                statusText.textContent = 'OCR on page ' + n + ' of ' + totalPages + ' (languages: English + Urdu attempted)...';
                var pageText = '';
                try {
                    var res1 = await Tesseract.recognize(canvas, usedLang, {
                        logger: function (m) {
                            if (m && typeof m.progress === 'number' && m.status === 'recognizing text') {
                                var overall = Math.round((((n - 1) + m.progress) / totalPages) * 100);
                                setProgress(overall);
                                statusText.textContent = 'OCR page ' + n + ' of ' + totalPages + ': ' + Math.round(m.progress * 100) + '%';
                            }
                        }
                    });
                    pageText = (res1 && res1.data && res1.data.text) ? res1.data.text : '';
                } catch (ocrErr) {
                    console.error(ocrErr);
                    if (usedLang !== 'eng') {
                        usedLang = 'eng';
                        statusText.textContent = 'Urdu+English OCR failed on page ' + n + ' — retrying with English only...';
                        var res2 = await Tesseract.recognize(canvas, 'eng', {
                            logger: function (m) {
                                if (m && typeof m.progress === 'number' && m.status === 'recognizing text') {
                                    var overall2 = Math.round((((n - 1) + m.progress) / totalPages) * 100);
                                    setProgress(overall2);
                                }
                            }
                        });
                        pageText = (res2 && res2.data && res2.data.text) ? res2.data.text : '';
                    } else {
                        throw ocrErr;
                    }
                }
                allText.push('--- Page ' + n + ' ---\n' + pageText.trim());
                resultText.value = allText.join('\n\n');
                updateCounts();
                setProgress(Math.round((n / totalPages) * 100));
            }
            statusText.textContent = 'Done — languages used: ' + (usedLang === 'eng' ? 'English only (Urdu fallback was needed)' : 'English + Urdu');
            var joined = resultText.value.replace(/--- Page \d+ ---/g, '').trim();
            if (joined.length > 0) {
                showSuccess('OCR complete! Text extracted from ' + totalPages + ' page(s). Check it below, then copy or download the .txt file. Accuracy depends on scan quality.');
            } else {
                showError('OCR finished but no readable text was found. Try a clearer, higher-resolution scan.');
            }
        } catch (err) {
            console.error(err);
            statusText.textContent = 'OCR failed';
            if (allText.length > 0) {
                resultText.value = allText.join('\n\n');
                updateCounts();
                showError('OCR stopped partway — text from ' + allText.length + ' page(s) is shown below. Error: the remaining pages could not be processed. Please try again or use a smaller PDF.');
            } else {
                showError('OCR failed for this PDF. Please try a clearer scan or check your internet connection (language data downloads on first use).');
            }
        } finally {
            processBtn.disabled = false;
            processBtn.textContent = 'Run OCR & Extract Text';
        }
    });
})();
</script>
@endsection
