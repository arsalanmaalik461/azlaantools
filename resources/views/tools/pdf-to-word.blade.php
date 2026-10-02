@extends('layouts.app')
@section('title', 'PDF to Word Converter Online Free — Azlaan Tools')
@section('meta_description', 'Convert a PDF to an editable Word (.docx) document online for free. Extracts the text into paragraphs you can edit. No signup, no upload — files stay in your browser.')
@section('content')
<div class="container py-4 pdf-tool">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">PDF to Word</h1>
            <p class="lead text-muted">Convert a text-based PDF into an editable Word document (.docx) that you can open in Microsoft Word, Google Docs or LibreOffice.</p>
            <div class="alert alert-warning">
                <strong>Honest note:</strong> Best for text-based PDFs — tables, columns and exact layout may not be preserved. Scanned PDFs: use our <strong>Image to Text (OCR)</strong> tool first, because scanned pages contain pictures of text, not real text.
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
                        <p class="mb-2">File: <strong id="fileName" class="text-break"></strong></p>
                        <p class="mb-3">Extracted text: <span id="extractInfo" class="fw-semibold">—</span></p>
                        <label for="previewText" class="form-label fw-semibold">Text preview (first pages)</label>
                        <textarea id="previewText" class="form-control mb-3" rows="8" readonly></textarea>
                        <div class="d-flex flex-wrap gap-2">
                            <button type="button" id="processBtn" class="btn btn-success btn-lg">Convert &amp; Download Word (.docx)</button>
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
                <li>Click the box above or drag and drop your PDF into it — the text is extracted automatically.</li>
                <li>Check the preview. If it is empty, your PDF is probably a scan — use the Image to Text (OCR) tool instead.</li>
                <li>Click <strong>Convert &amp; Download Word (.docx)</strong> and open the file in Word or Google Docs to edit it.</li>
            </ol>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/pdfjs-dist@3.11.174/build/pdf.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/docx@8.5.0/build/index.umd.js"></script>
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
    var extractInfo = document.getElementById('extractInfo');
    var previewText = document.getElementById('previewText');
    var processBtn = document.getElementById('processBtn');
    var clearBtn = document.getElementById('clearBtn');
    var progressWrap = document.getElementById('progressWrap');
    var progressBar = document.getElementById('progressBar');
    var pdfDoc = null;
    var storedName = 'document';
    var pageParagraphs = [];

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

    function extractParagraphs(textContent) {
        var linesMap = {};
        textContent.items.forEach(function (item) {
            if (!item.str || !item.str.trim()) return;
            var y = Math.round(item.transform[5]);
            var key = null;
            Object.keys(linesMap).forEach(function (k) {
                if (Math.abs(parseInt(k, 10) - y) <= 2) key = k;
            });
            if (key === null) {
                key = String(y);
                linesMap[key] = { y: y, parts: [] };
            }
            linesMap[key].parts.push({ x: item.transform[4], str: item.str, h: Math.abs(item.transform[3]) || 12 });
        });
        var lines = Object.values(linesMap).sort(function (a, b) { return b.y - a.y; });
        lines.forEach(function (line) {
            line.parts.sort(function (a, b) { return a.x - b.x; });
            line.text = line.parts.map(function (p) { return p.str; }).join(' ').replace(/\s+/g, ' ').trim();
            line.height = line.parts.length ? line.parts[0].h : 12;
        });
        lines = lines.filter(function (l) { return l.text.length > 0; });
        var paragraphs = [];
        var current = '';
        for (var i = 0; i < lines.length; i++) {
            if (!current) {
                current = lines[i].text;
            } else {
                var gap = lines[i - 1].y - lines[i].y;
                if (gap > lines[i].height * 1.7) {
                    paragraphs.push(current);
                    current = lines[i].text;
                } else {
                    current += ' ' + lines[i].text;
                }
            }
        }
        if (current) paragraphs.push(current);
        return paragraphs;
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
            pdfDoc = await pdfjsLib.getDocument({ data: buf }).promise;
            storedName = file.name.replace(/\.pdf$/i, '') || 'document';
            pageParagraphs = [];
            var totalChars = 0;
            for (var n = 1; n <= pdfDoc.numPages; n++) {
                var page = await pdfDoc.getPage(n);
                var tc = await page.getTextContent();
                var paras = extractParagraphs(tc);
                pageParagraphs.push(paras);
                paras.forEach(function (p) { totalChars += p.length; });
            }
            fileNameEl.textContent = file.name + ' (' + formatSize(file.size) + ') — ' + pdfDoc.numPages + ' page(s)';
            extractInfo.textContent = totalChars.toLocaleString() + ' characters of text found';
            var previewParas = [];
            pageParagraphs.forEach(function (paras) { paras.forEach(function (p) { previewParas.push(p); }); });
            previewText.value = previewParas.slice(0, 40).join('\n\n');
            processBtn.disabled = totalChars === 0;
            toolWrap.classList.remove('d-none');
            if (totalChars === 0) {
                showError('No selectable text was found in this PDF. It is probably a scanned document — please use our Image to Text (OCR) tool first.');
            } else {
                showSuccess('Text extracted — ' + totalChars.toLocaleString() + ' characters. Click convert to download the Word file.');
            }
        } catch (err) {
            console.error(err);
            pdfDoc = null;
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
        pdfDoc = null;
        pageParagraphs = [];
        previewText.value = '';
        toolWrap.classList.add('d-none');
        hideAlerts();
    });

    processBtn.addEventListener('click', async function () {
        hideAlerts();
        if (!pdfDoc) { showError('Please select a PDF file first.'); return; }
        if (typeof docx === 'undefined') { showError('Word library failed to load. Please check your internet connection and try again.'); return; }
        processBtn.disabled = true;
        processBtn.textContent = 'Converting...';
        progressWrap.classList.remove('d-none');
        try {
            var children = [];
            pageParagraphs.forEach(function (paras, pageIdx) {
                if (pageIdx > 0) {
                    children.push(new docx.Paragraph({ children: [new docx.TextRun('')] }));
                }
                paras.forEach(function (p) {
                    children.push(new docx.Paragraph({
                        children: [new docx.TextRun(p)],
                        spacing: { after: 160 }
                    }));
                });
                var pct = Math.round(((pageIdx + 1) / pageParagraphs.length) * 100);
                progressBar.style.width = pct + '%';
                progressBar.textContent = pct + '%';
            });
            if (children.length === 0) {
                children.push(new docx.Paragraph({ children: [new docx.TextRun('')] }));
            }
            var doc = new docx.Document({ sections: [{ children: children }] });
            var blob = await docx.Packer.toBlob(doc);
            var url = URL.createObjectURL(blob);
            var a = document.createElement('a');
            a.href = url;
            a.download = storedName + '.docx';
            document.body.appendChild(a);
            a.click();
            a.remove();
            setTimeout(function () { URL.revokeObjectURL(url); }, 4000);
            showSuccess('Done! Your Word document (' + formatSize(blob.size) + ') has been downloaded.');
        } catch (err) {
            console.error(err);
            showError('Could not create the Word document. Please try again with a different PDF.');
        } finally {
            processBtn.disabled = false;
            processBtn.textContent = 'Convert & Download Word (.docx)';
            setTimeout(function () { progressWrap.classList.add('d-none'); progressBar.style.width = '0%'; progressBar.textContent = '0%'; }, 800);
        }
    });
})();
</script>
@endsection
