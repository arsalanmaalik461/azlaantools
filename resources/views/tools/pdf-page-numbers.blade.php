@extends('layouts.app')
@section('title', 'Add Page Numbers to PDF - Free Online | Azlaan Tools')
@section('meta_description', 'Add page numbers to any PDF online for free. Choose position, format, font size and starting number, then download the numbered PDF. No signup, no upload — files stay in your browser.')
@section('content')
<div class="container py-4 pdf-tool">
    <h1 class="mb-3">Add Page Numbers to PDF</h1>
    <p class="lead">Stamp clean page numbers on every page of your PDF — pick the position, format and starting number, then download. Free and private.</p>

    <div id="alertBox" class="alert alert-danger d-none" role="alert"></div>
    <div id="successBox" class="alert alert-success d-none" role="alert"></div>

    <div id="dropZone" class="border border-2 border-dashed rounded-3 p-4 p-md-5 text-center mb-3" style="border-style: dashed !important; cursor: pointer; background: #f8f9fa;">
        <div class="fs-1 mb-2">🔢</div>
        <p class="mb-1 fw-semibold">Drag &amp; drop a PDF here</p>
        <p class="text-muted mb-3">or click to browse from your device</p>
        <button type="button" class="btn btn-primary">Select PDF File</button>
        <input type="file" id="fileInput" accept="application/pdf,.pdf" class="d-none">
    </div>

    <div id="optionsWrap" class="d-none">
        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <p class="mb-3">File: <strong id="fileName" class="text-break"></strong> — <span id="pageCountInfo"></span></p>
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="positionSel" class="form-label fw-semibold">Position</label>
                        <select id="positionSel" class="form-select">
                            <option value="bottom-center" selected>Bottom Center</option>
                            <option value="bottom-right">Bottom Right</option>
                            <option value="bottom-left">Bottom Left</option>
                            <option value="top-right">Top Right</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="formatSel" class="form-label fw-semibold">Format</label>
                        <select id="formatSel" class="form-select">
                            <option value="plain" selected>1</option>
                            <option value="page">Page 1</option>
                            <option value="of">1 / N</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="fontSizeInput" class="form-label fw-semibold">Font Size</label>
                        <input type="number" id="fontSizeInput" class="form-control" value="12" min="8" max="36" step="1">
                    </div>
                    <div class="col-md-3">
                        <label for="startInput" class="form-label fw-semibold">Start Number</label>
                        <input type="number" id="startInput" class="form-control" value="1" min="0" max="9999" step="1">
                    </div>
                </div>
                <div class="d-flex flex-wrap gap-2 mt-4">
                    <button type="button" id="processBtn" class="btn btn-success btn-lg">Add Page Numbers &amp; Download</button>
                    <button type="button" id="clearBtn" class="btn btn-outline-secondary">Clear</button>
                </div>
            </div>
        </div>
    </div>

    <div class="alert alert-info mt-4">
        <strong>Privacy note:</strong> Your files never leave your browser — everything runs on your phone or computer, nothing is uploaded.
    </div>

    <h2>How to use</h2>
    <ol>
        <li>Click the box above or drag and drop your PDF into it — the page count will appear.</li>
        <li>Choose the position, number format, font size and the starting number.</li>
        <li>Click <strong>Add Page Numbers &amp; Download</strong>.</li>
        <li>Your numbered PDF downloads automatically with "-numbered" added to its name.</li>
    </ol>
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
    var optionsWrap = document.getElementById('optionsWrap');
    var fileNameEl = document.getElementById('fileName');
    var pageCountInfo = document.getElementById('pageCountInfo');
    var positionSel = document.getElementById('positionSel');
    var formatSel = document.getElementById('formatSel');
    var fontSizeInput = document.getElementById('fontSizeInput');
    var startInput = document.getElementById('startInput');
    var processBtn = document.getElementById('processBtn');
    var clearBtn = document.getElementById('clearBtn');
    var storedBuffer = null;
    var storedName = 'document';
    var totalPages = 0;

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
    async function loadFile(file) {
        hideAlerts();
        if (!file) {
            return;
        }
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
            var doc = await PDFLib.PDFDocument.load(buf.slice(0), { ignoreEncryption: false });
            storedBuffer = buf;
            storedName = file.name.replace(/\.pdf$/i, '') || 'document';
            totalPages = doc.getPageCount();
            fileNameEl.textContent = file.name + ' (' + formatSize(file.size) + ')';
            pageCountInfo.textContent = totalPages + (totalPages === 1 ? ' page' : ' pages');
            optionsWrap.classList.remove('d-none');
            showSuccess('PDF loaded successfully — ' + totalPages + ' page(s) found.');
        } catch (err) {
            console.error(err);
            storedBuffer = null;
            totalPages = 0;
            optionsWrap.classList.add('d-none');
            showError('Could not read this PDF. It may be corrupted or password-protected. Please try a different file.');
        }
    }
    function buildLabel(pageNum, total) {
        var fmt = formatSel.value;
        if (fmt === 'page') {
            return 'Page ' + pageNum;
        }
        if (fmt === 'of') {
            var lastNum = (parseInt(startInput.value, 10) || 1) + total - 1;
            return pageNum + ' / ' + lastNum;
        }
        return String(pageNum);
    }

    dropZone.addEventListener('click', function () { fileInput.click(); });
    fileInput.addEventListener('change', function () {
        if (fileInput.files && fileInput.files[0]) {
            loadFile(fileInput.files[0]);
        }
        fileInput.value = '';
    });
    dropZone.addEventListener('dragover', function (e) {
        e.preventDefault();
        dropZone.style.background = '#e9f2ff';
    });
    dropZone.addEventListener('dragleave', function () {
        dropZone.style.background = '#f8f9fa';
    });
    dropZone.addEventListener('drop', function (e) {
        e.preventDefault();
        dropZone.style.background = '#f8f9fa';
        if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files[0]) {
            loadFile(e.dataTransfer.files[0]);
        }
    });
    clearBtn.addEventListener('click', function () {
        storedBuffer = null;
        totalPages = 0;
        optionsWrap.classList.add('d-none');
        hideAlerts();
    });

    processBtn.addEventListener('click', async function () {
        hideAlerts();
        if (!storedBuffer) {
            showError('Please select a PDF file first.');
            return;
        }
        if (typeof PDFLib === 'undefined') {
            showError('PDF library failed to load. Please check your internet connection and try again.');
            return;
        }
        var fontSize = parseFloat(fontSizeInput.value);
        if (isNaN(fontSize) || fontSize < 6 || fontSize > 72) {
            showError('Please enter a font size between 6 and 72.');
            return;
        }
        var startNum = parseInt(startInput.value, 10);
        if (isNaN(startNum) || startNum < 0) {
            showError('Please enter a valid start number (0 or higher).');
            return;
        }
        processBtn.disabled = true;
        processBtn.textContent = 'Processing...';
        try {
            var pdfDoc = await PDFLib.PDFDocument.load(storedBuffer.slice(0));
            var font = await pdfDoc.embedFont(PDFLib.StandardFonts.Helvetica);
            var pages = pdfDoc.getPages();
            var total = pages.length;
            var margin = 36;
            pages.forEach(function (page, idx) {
                var size = page.getSize();
                var label = buildLabel(startNum + idx, total);
                var textWidth = font.widthOfTextAtSize(label, fontSize);
                var x;
                var y;
                var pos = positionSel.value;
                if (pos === 'bottom-left') {
                    x = margin;
                    y = margin - fontSize;
                } else if (pos === 'bottom-right') {
                    x = size.width - margin - textWidth;
                    y = margin - fontSize;
                } else if (pos === 'top-right') {
                    x = size.width - margin - textWidth;
                    y = size.height - margin;
                } else {
                    x = (size.width - textWidth) / 2;
                    y = margin - fontSize;
                }
                if (y < 12) { y = 12; }
                var drawOpts = {
                    x: x,
                    y: y,
                    size: fontSize,
                    font: font,
                    color: PDFLib.rgb(0.25, 0.25, 0.25)
                };
                page.drawText(label, drawOpts);
            });
            var outBytes = await pdfDoc.save();
            var blob = new Blob([outBytes], { type: 'application/pdf' });
            var url = URL.createObjectURL(blob);
            var a = document.createElement('a');
            a.href = url;
            a.download = storedName + '-numbered.pdf';
            document.body.appendChild(a);
            a.click();
            a.remove();
            setTimeout(function () { URL.revokeObjectURL(url); }, 4000);
            showSuccess('Done! Page numbers added to ' + total + ' page(s). Your file (' + formatSize(blob.size) + ') has been downloaded.');
        } catch (err) {
            console.error(err);
            showError('Could not process this PDF. It may be corrupted or password-protected. Please try a different file.');
        } finally {
            processBtn.disabled = false;
            processBtn.textContent = 'Add Page Numbers & Download';
        }
    });
})();
</script>
@endsection
