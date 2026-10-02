@extends('layouts.app')
@section('title', 'Split PDF Online Free — Azlaan Tools')
@section('meta_description', 'Split a PDF into a page range or separate pages online for free. No signup, no upload — files are processed in your browser.')
@section('content')
<div class="container py-4 pdf-tool">
    <h1 class="mb-3">Split PDF</h1>
    <p class="lead">Extract a page range from a PDF, or split every page into its own PDF file. Free and private — everything happens in your browser.</p>

    <div id="alertBox" class="alert alert-danger d-none" role="alert"></div>
    <div id="successBox" class="alert alert-success d-none" role="alert"></div>

    <div id="dropZone" class="border border-2 border-dashed rounded-3 p-4 p-md-5 text-center mb-3" style="border-style: dashed !important; cursor: pointer; background: #f8f9fa;">
        <div class="fs-1 mb-2">✂️</div>
        <p class="mb-1 fw-semibold">Drag &amp; drop a PDF file here</p>
        <p class="text-muted mb-3">or click to browse from your device</p>
        <button type="button" class="btn btn-primary">Select PDF File</button>
        <input type="file" id="fileInput" accept="application/pdf,.pdf" class="d-none">
    </div>

    <div id="optionsWrap" class="d-none">
        <div class="card mb-3">
            <div class="card-body">
                <p class="mb-1"><strong>File:</strong> <span id="fileName" class="text-break"></span></p>
                <p class="mb-0"><strong>Total pages:</strong> <span id="pageCount">-</span></p>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Split mode</label>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="splitMode" id="modeRange" value="range" checked>
                <label class="form-check-label" for="modeRange">Extract page range into one PDF</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="splitMode" id="modeAll" value="all">
                <label class="form-check-label" for="modeAll">Split every page into separate PDFs (downloaded as ZIP)</label>
            </div>
        </div>

        <div id="rangeWrap" class="row g-3 mb-3">
            <div class="col-6 col-md-3">
                <label for="fromPage" class="form-label">From page</label>
                <input type="number" id="fromPage" class="form-control" min="1" value="1">
            </div>
            <div class="col-6 col-md-3">
                <label for="toPage" class="form-label">To page</label>
                <input type="number" id="toPage" class="form-control" min="1" value="1">
            </div>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <button type="button" id="splitBtn" class="btn btn-success btn-lg">Split PDF</button>
            <button type="button" id="clearBtn" class="btn btn-outline-secondary">Choose Another File</button>
        </div>
        <div id="progressWrap" class="progress mt-3 d-none" style="height: 24px;">
            <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%;">0%</div>
        </div>
    </div>

    <div class="alert alert-info mt-4">
        <strong>Privacy note:</strong> Your files never leave your browser — all the work happens on your phone/computer, nothing is uploaded.
    </div>

    <h2>How to use</h2>
    <ol>
        <li>Click the box above or drag and drop a PDF file into it.</li>
        <li>Check the total page count shown for your file.</li>
        <li>Choose <strong>Extract page range</strong> and enter From and To page numbers, or choose <strong>Split every page</strong> to get one PDF per page in a ZIP file.</li>
        <li>Click <strong>Split PDF</strong> and your file will download automatically.</li>
    </ol>
</div>
@endsection
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/pdf-lib@1.17.1/dist/pdf-lib.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script>
(function () {
    var selectedFile = null;
    var totalPages = 0;
    var sourceBytes = null;

    var dropZone = document.getElementById('dropZone');
    var fileInput = document.getElementById('fileInput');
    var optionsWrap = document.getElementById('optionsWrap');
    var fileNameEl = document.getElementById('fileName');
    var pageCountEl = document.getElementById('pageCount');
    var alertBox = document.getElementById('alertBox');
    var successBox = document.getElementById('successBox');
    var splitBtn = document.getElementById('splitBtn');
    var clearBtn = document.getElementById('clearBtn');
    var rangeWrap = document.getElementById('rangeWrap');
    var fromPage = document.getElementById('fromPage');
    var toPage = document.getElementById('toPage');
    var progressWrap = document.getElementById('progressWrap');
    var progressBar = document.getElementById('progressBar');

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
    function getMode() {
        var checked = document.querySelector('input[name="splitMode"]:checked');
        return checked ? checked.value : 'range';
    }
    function updateRangeVisibility() {
        if (getMode() === 'range') {
            rangeWrap.classList.remove('d-none');
        } else {
            rangeWrap.classList.add('d-none');
        }
    }
    document.querySelectorAll('input[name="splitMode"]').forEach(function (radio) {
        radio.addEventListener('change', updateRangeVisibility);
    });

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
    function baseName(name) {
        return name.replace(/\.pdf$/i, '');
    }

    async function handleFile(file) {
        hideAlerts();
        if (!file || !(file.type === 'application/pdf' || file.name.toLowerCase().endsWith('.pdf'))) {
            showError('Please select a valid PDF file.');
            return;
        }
        if (typeof PDFLib === 'undefined') {
            showError('PDF library failed to load. Please check your internet connection and try again.');
            return;
        }
        try {
            sourceBytes = await file.arrayBuffer();
            var pdf = await PDFLib.PDFDocument.load(sourceBytes);
            totalPages = pdf.getPageCount();
            selectedFile = file;
            fileNameEl.textContent = file.name;
            pageCountEl.textContent = totalPages;
            fromPage.value = 1;
            fromPage.max = totalPages;
            toPage.value = totalPages;
            toPage.max = totalPages;
            optionsWrap.classList.remove('d-none');
            updateRangeVisibility();
        } catch (err) {
            console.error(err);
            selectedFile = null;
            sourceBytes = null;
            optionsWrap.classList.add('d-none');
            showError('Could not read this PDF. The file may be corrupted or password-protected.');
        }
    }

    dropZone.addEventListener('click', function () { fileInput.click(); });
    fileInput.addEventListener('change', function () {
        if (fileInput.files.length > 0) handleFile(fileInput.files[0]);
        fileInput.value = '';
    });
    dropZone.addEventListener('dragover', function (e) {
        e.preventDefault();
        dropZone.style.background = '#e9f2ff';
    });
    dropZone.addEventListener('dragleave', function () { dropZone.style.background = '#f8f9fa'; });
    dropZone.addEventListener('drop', function (e) {
        e.preventDefault();
        dropZone.style.background = '#f8f9fa';
        if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length > 0) handleFile(e.dataTransfer.files[0]);
    });

    clearBtn.addEventListener('click', function () {
        selectedFile = null;
        sourceBytes = null;
        totalPages = 0;
        optionsWrap.classList.add('d-none');
        hideAlerts();
    });

    splitBtn.addEventListener('click', async function () {
        hideAlerts();
        if (!selectedFile || !sourceBytes) {
            showError('Please select a PDF file first.');
            return;
        }
        var mode = getMode();
        splitBtn.disabled = true;
        splitBtn.textContent = 'Working...';
        progressWrap.classList.remove('d-none');
        try {
            var srcPdf = await PDFLib.PDFDocument.load(sourceBytes);
            if (mode === 'range') {
                var from = parseInt(fromPage.value, 10);
                var to = parseInt(toPage.value, 10);
                if (isNaN(from) || isNaN(to) || from < 1 || to < from || to > totalPages) {
                    showError('Please enter a valid page range between 1 and ' + totalPages + '.');
                    return;
                }
                var indices = [];
                for (var p = from; p <= to; p++) indices.push(p - 1);
                var newPdf = await PDFLib.PDFDocument.create();
                var copied = await newPdf.copyPages(srcPdf, indices);
                copied.forEach(function (page) { newPdf.addPage(page); });
                progressBar.style.width = '100%';
                progressBar.textContent = '100%';
                var bytes = await newPdf.save();
                downloadBlob(new Blob([bytes], { type: 'application/pdf' }), baseName(selectedFile.name) + '-pages-' + from + '-' + to + '.pdf');
                showSuccess('Done! Pages ' + from + ' to ' + to + ' have been downloaded as one PDF.');
            } else {
                if (typeof JSZip === 'undefined') {
                    showError('ZIP library failed to load. Please check your internet connection and try again.');
                    return;
                }
                var zip = new JSZip();
                for (var i = 0; i < totalPages; i++) {
                    var pct = Math.round(((i + 1) / totalPages) * 100);
                    progressBar.style.width = pct + '%';
                    progressBar.textContent = pct + '%';
                    var singlePdf = await PDFLib.PDFDocument.create();
                    var pages = await singlePdf.copyPages(srcPdf, [i]);
                    singlePdf.addPage(pages[0]);
                    var singleBytes = await singlePdf.save();
                    zip.file(baseName(selectedFile.name) + '-page-' + (i + 1) + '.pdf', singleBytes);
                }
                var zipBlob = await zip.generateAsync({ type: 'blob' });
                downloadBlob(zipBlob, baseName(selectedFile.name) + '-split.zip');
                showSuccess('Done! All ' + totalPages + ' pages have been downloaded in a ZIP file.');
            }
        } catch (err) {
            console.error(err);
            showError('Something went wrong while splitting this PDF. Please try again.');
        } finally {
            splitBtn.disabled = false;
            splitBtn.textContent = 'Split PDF';
            setTimeout(function () { progressWrap.classList.add('d-none'); progressBar.style.width = '0%'; progressBar.textContent = '0%'; }, 800);
        }
    });
})();
</script>
@endsection
