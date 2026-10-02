@extends('layouts.app')
@section('title', 'PDF Watermark - Add Watermark to PDF Free | Azlaan Tools')
@section('meta_description', 'Add a text watermark to every page of a PDF online for free. Control text, opacity, size, color and diagonal rotation, then download instantly. No signup, no upload — files stay in your browser.')
@section('content')
<div class="container py-4 pdf-tool">
    <h1 class="mb-3">PDF Watermark</h1>
    <p class="lead">Stamp your own watermark text — like CONFIDENTIAL or your brand name — across every page of a PDF. Free, fast and fully private.</p>

    <div id="alertBox" class="alert alert-danger d-none" role="alert"></div>
    <div id="successBox" class="alert alert-success d-none" role="alert"></div>

    <div id="dropZone" class="border border-2 border-dashed rounded-3 p-4 p-md-5 text-center mb-3" style="border-style: dashed !important; cursor: pointer; background: #f8f9fa;">
        <div class="fs-1 mb-2">💧</div>
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
                    <div class="col-md-6">
                        <label for="wmText" class="form-label fw-semibold">Watermark Text</label>
                        <input type="text" id="wmText" class="form-control" value="CONFIDENTIAL" maxlength="60" placeholder="e.g. CONFIDENTIAL">
                    </div>
                    <div class="col-md-3">
                        <label for="fontSizeInput" class="form-label fw-semibold">Font Size</label>
                        <input type="number" id="fontSizeInput" class="form-control" value="48" min="12" max="120" step="1">
                    </div>
                    <div class="col-md-3">
                        <label for="colorSel" class="form-label fw-semibold">Color</label>
                        <select id="colorSel" class="form-select">
                            <option value="gray" selected>Gray</option>
                            <option value="lightred">Light Red</option>
                            <option value="lightblue">Light Blue</option>
                            <option value="black">Black</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="opacityRange" class="form-label fw-semibold">Opacity: <span id="opacityVal">25</span>%</label>
                        <input type="range" id="opacityRange" class="form-range" min="5" max="90" value="25">
                    </div>
                    <div class="col-md-6 d-flex align-items-end">
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" id="diagonalCheck" checked>
                            <label class="form-check-label fw-semibold" for="diagonalCheck">Diagonal watermark (-45 degrees)</label>
                        </div>
                    </div>
                </div>
                <div class="d-flex flex-wrap gap-2 mt-3">
                    <button type="button" id="processBtn" class="btn btn-success btn-lg">Add Watermark &amp; Download</button>
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
        <li>Type your watermark text and adjust opacity, font size, color and the diagonal option.</li>
        <li>Click <strong>Add Watermark &amp; Download</strong>.</li>
        <li>Your watermarked PDF downloads automatically with "-watermarked" added to its name.</li>
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
    var wmText = document.getElementById('wmText');
    var fontSizeInput = document.getElementById('fontSizeInput');
    var colorSel = document.getElementById('colorSel');
    var opacityRange = document.getElementById('opacityRange');
    var opacityVal = document.getElementById('opacityVal');
    var diagonalCheck = document.getElementById('diagonalCheck');
    var processBtn = document.getElementById('processBtn');
    var clearBtn = document.getElementById('clearBtn');
    var storedBuffer = null;
    var storedName = 'document';

    var colorPresets = {
        gray: [0.55, 0.55, 0.55],
        lightred: [0.92, 0.45, 0.45],
        lightblue: [0.45, 0.62, 0.92],
        black: [0.1, 0.1, 0.1]
    };

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
            var count = doc.getPageCount();
            fileNameEl.textContent = file.name + ' (' + formatSize(file.size) + ')';
            pageCountInfo.textContent = count + (count === 1 ? ' page' : ' pages');
            optionsWrap.classList.remove('d-none');
            showSuccess('PDF loaded successfully — ' + count + ' page(s) found.');
        } catch (err) {
            console.error(err);
            storedBuffer = null;
            optionsWrap.classList.add('d-none');
            showError('Could not read this PDF. It may be corrupted or password-protected. Please try a different file.');
        }
    }

    opacityRange.addEventListener('input', function () {
        opacityVal.textContent = opacityRange.value;
    });

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
        optionsWrap.classList.add('d-none');
        hideAlerts();
    });

    processBtn.addEventListener('click', async function () {
        hideAlerts();
        if (!storedBuffer) {
            showError('Please select a PDF file first.');
            return;
        }
        var text = wmText.value.trim();
        if (!text) {
            showError('Please enter watermark text.');
            return;
        }
        var fontSize = parseFloat(fontSizeInput.value);
        if (isNaN(fontSize) || fontSize < 8 || fontSize > 200) {
            showError('Please enter a font size between 8 and 200.');
            return;
        }
        if (typeof PDFLib === 'undefined') {
            showError('PDF library failed to load. Please check your internet connection and try again.');
            return;
        }
        var opacity = parseInt(opacityRange.value, 10) / 100;
        var preset = colorPresets[colorSel.value] || colorPresets.gray;
        var diagonal = diagonalCheck.checked;
        processBtn.disabled = true;
        processBtn.textContent = 'Processing...';
        try {
            var pdfDoc = await PDFLib.PDFDocument.load(storedBuffer.slice(0));
            var font = await pdfDoc.embedFont(PDFLib.StandardFonts.HelveticaBold);
            var pages = pdfDoc.getPages();
            var textWidth = font.widthOfTextAtSize(text, fontSize);
            var angle = diagonal ? -45 : 0;
            pages.forEach(function (page) {
                var size = page.getSize();
                var x;
                var y;
                if (diagonal) {
                    // pdf-lib rotates the text around the (x, y) baseline point.
                    // The centre of the rotated text is at
                    // (x + (textWidth/2)*cos, y + (textWidth/2)*sin) plus a small
                    // perpendicular offset for the glyph height — solve for x/y so
                    // the centre lands on the page centre. (The old code used the
                    // same sign for x and y, which pushed diagonal watermarks far
                    // below the centre of the page.)
                    var rad = angle * Math.PI / 180;
                    var cosA = Math.cos(rad);
                    var sinA = Math.sin(rad);
                    x = (size.width / 2) - (textWidth / 2) * cosA + (fontSize * 0.35) * sinA;
                    y = (size.height / 2) - (textWidth / 2) * sinA - (fontSize * 0.35) * cosA;
                } else {
                    x = (size.width - textWidth) / 2;
                    y = (size.height - fontSize) / 2;
                }
                if (x < 0) { x = 10; }
                if (y < 0) { y = 10; }
                var drawOpts = {
                    x: x,
                    y: y,
                    size: fontSize,
                    font: font,
                    color: PDFLib.rgb(preset[0], preset[1], preset[2]),
                    opacity: opacity,
                    rotate: PDFLib.degrees(angle)
                };
                page.drawText(text, drawOpts);
            });
            var outBytes = await pdfDoc.save();
            var blob = new Blob([outBytes], { type: 'application/pdf' });
            var url = URL.createObjectURL(blob);
            var a = document.createElement('a');
            a.href = url;
            a.download = storedName + '-watermarked.pdf';
            document.body.appendChild(a);
            a.click();
            a.remove();
            setTimeout(function () { URL.revokeObjectURL(url); }, 4000);
            showSuccess('Done! Watermark added to ' + pages.length + ' page(s). Your file (' + formatSize(blob.size) + ') has been downloaded.');
        } catch (err) {
            console.error(err);
            showError('Could not process this PDF. It may be corrupted or password-protected, or the watermark text has unsupported characters. Please try again.');
        } finally {
            processBtn.disabled = false;
            processBtn.textContent = 'Add Watermark & Download';
        }
    });
})();
</script>
@endsection
