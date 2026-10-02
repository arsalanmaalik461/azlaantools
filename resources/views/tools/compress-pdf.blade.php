@extends('layouts.app')
@section('title', 'Compress PDF Online Free — Azlaan Tools')
@section('meta_description', 'Compress PDF files online for free and reduce PDF size. No signup, no upload — files are processed in your browser.')
@section('content')
<div class="container py-4 pdf-tool">
    <h1 class="mb-3">Compress PDF</h1>
    <p class="lead">Reduce the size of your PDF by re-rendering each page as a compressed image and rebuilding the PDF. Best for scanned documents, bills and image-heavy PDFs.</p>

    <div class="alert alert-warning">
        <strong>Please note:</strong> Works best on scanned/image PDFs; text-based PDFs may not shrink much. In this method pages become images, so text in the compressed PDF will not be selectable.
    </div>

    <div id="alertBox" class="alert alert-danger d-none" role="alert"></div>
    <div id="successBox" class="alert alert-success d-none" role="alert"></div>

    <div id="dropZone" class="border border-2 border-dashed rounded-3 p-4 p-md-5 text-center mb-3" style="border-style: dashed !important; cursor: pointer; background: #f8f9fa;">
        <div class="fs-1 mb-2">🗜️</div>
        <p class="mb-1 fw-semibold">Drag &amp; drop a PDF file here</p>
        <p class="text-muted mb-3">or click to browse from your device</p>
        <button type="button" class="btn btn-primary">Select PDF File</button>
        <input type="file" id="fileInput" accept="application/pdf,.pdf" class="d-none">
    </div>

    <div id="controlsWrap" class="d-none">
        <div class="card mb-3">
            <div class="card-body">
                <p class="mb-2"><strong>File:</strong> <span id="fileName" class="text-break"></span></p>
                <p class="mb-3"><strong>Original size:</strong> <span id="originalSize">-</span> &nbsp;|&nbsp; <strong>Pages:</strong> <span id="pageCount">-</span></p>

                <label class="form-label fw-semibold">Compression level</label>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="preset" id="presetLow" value="low">
                    <label class="form-check-label" for="presetLow">Low quality — smallest file (scale 1, JPG quality 0.5)</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="preset" id="presetMedium" value="medium" checked>
                    <label class="form-check-label" for="presetMedium">Medium — balanced (scale 1.5, JPG quality 0.7)</label>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="radio" name="preset" id="presetHigh" value="high">
                    <label class="form-check-label" for="presetHigh">High quality — clearer pages (scale 2, JPG quality 0.85)</label>
                </div>

                <div class="d-flex flex-wrap gap-2">
                    <button type="button" id="compressBtn" class="btn btn-success btn-lg">Compress PDF</button>
                    <button type="button" id="clearBtn" class="btn btn-outline-secondary">Choose Another File</button>
                </div>

                <div id="progressWrap" class="progress mt-3 d-none" style="height: 24px;">
                    <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%;">0%</div>
                </div>

                <div id="resultWrap" class="mt-3 d-none">
                    <div class="row g-3 text-center">
                        <div class="col-4">
                            <div class="border rounded p-2 h-100">
                                <div class="text-muted small">Original size</div>
                                <div class="fw-bold" id="resultOriginal">-</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border rounded p-2 h-100">
                                <div class="text-muted small">New size</div>
                                <div class="fw-bold" id="resultNew">-</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border rounded p-2 h-100">
                                <div class="text-muted small">Saved</div>
                                <div class="fw-bold text-success" id="resultSaved">-</div>
                            </div>
                        </div>
                    </div>
                    <button type="button" id="downloadBtn" class="btn btn-primary btn-lg w-100 mt-3">Download Compressed PDF</button>
                </div>
            </div>
        </div>
    </div>

    <div class="alert alert-info mt-4">
        <strong>Privacy note:</strong> Your files never leave your browser — all the work happens on your phone or computer, nothing is uploaded.
    </div>

    <h2>How to use</h2>
    <ol>
        <li>Click the box above or drag and drop a PDF file into it — the original size and page count will be shown.</li>
        <li>Choose a compression level: Low for the smallest file, Medium for a balance, or High for clearer pages.</li>
        <li>Click <strong>Compress PDF</strong> and wait while each page is processed.</li>
        <li>Compare the original size, new size and percentage saved, then click <strong>Download Compressed PDF</strong>. If the new file is larger, try the Low preset.</li>
    </ol>
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

    var PRESETS = {
        low: { scale: 1, quality: 0.5 },
        medium: { scale: 1.5, quality: 0.7 },
        high: { scale: 2, quality: 0.85 }
    };

    var selectedFile = null;
    var pdfDoc = null;
    var resultBlob = null;

    var dropZone = document.getElementById('dropZone');
    var fileInput = document.getElementById('fileInput');
    var controlsWrap = document.getElementById('controlsWrap');
    var fileNameEl = document.getElementById('fileName');
    var originalSizeEl = document.getElementById('originalSize');
    var pageCountEl = document.getElementById('pageCount');
    var compressBtn = document.getElementById('compressBtn');
    var clearBtn = document.getElementById('clearBtn');
    var downloadBtn = document.getElementById('downloadBtn');
    var resultWrap = document.getElementById('resultWrap');
    var resultOriginal = document.getElementById('resultOriginal');
    var resultNew = document.getElementById('resultNew');
    var resultSaved = document.getElementById('resultSaved');
    var alertBox = document.getElementById('alertBox');
    var successBox = document.getElementById('successBox');
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
    function formatSize(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
    }
    function baseName(name) {
        return name.replace(/\.pdf$/i, '');
    }
    function getPreset() {
        var checked = document.querySelector('input[name="preset"]:checked');
        var key = checked ? checked.value : 'medium';
        return PRESETS[key] || PRESETS.medium;
    }
    function canvasToBlob(canvas, quality) {
        return new Promise(function (resolve) {
            canvas.toBlob(function (blob) { resolve(blob); }, 'image/jpeg', quality);
        });
    }
    function blobToArrayBuffer(blob) {
        return new Promise(function (resolve, reject) {
            var reader = new FileReader();
            reader.onload = function () { resolve(reader.result); };
            reader.onerror = reject;
            reader.readAsArrayBuffer(blob);
        });
    }

    async function handleFile(file) {
        hideAlerts();
        if (!file || !(file.type === 'application/pdf' || file.name.toLowerCase().endsWith('.pdf'))) {
            showError('Please select a valid PDF file.');
            return;
        }
        if (typeof pdfjsLib === 'undefined') {
            showError('PDF library failed to load. Please check your internet connection and try again.');
            return;
        }
        try {
            var buffer = await file.arrayBuffer();
            pdfDoc = await pdfjsLib.getDocument({ data: buffer }).promise;
            selectedFile = file;
            resultBlob = null;
            resultWrap.classList.add('d-none');
            fileNameEl.textContent = file.name;
            originalSizeEl.textContent = formatSize(file.size);
            pageCountEl.textContent = pdfDoc.numPages;
            controlsWrap.classList.remove('d-none');
        } catch (err) {
            console.error(err);
            selectedFile = null;
            pdfDoc = null;
            controlsWrap.classList.add('d-none');
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
        pdfDoc = null;
        resultBlob = null;
        resultWrap.classList.add('d-none');
        controlsWrap.classList.add('d-none');
        hideAlerts();
    });

    compressBtn.addEventListener('click', async function () {
        hideAlerts();
        if (!pdfDoc || !selectedFile) {
            showError('Please select a PDF file first.');
            return;
        }
        if (typeof PDFLib === 'undefined') {
            showError('PDF library failed to load. Please check your internet connection and try again.');
            return;
        }
        var preset = getPreset();
        compressBtn.disabled = true;
        compressBtn.textContent = 'Compressing...';
        resultWrap.classList.add('d-none');
        resultBlob = null;
        progressWrap.classList.remove('d-none');
        try {
            var newPdf = await PDFLib.PDFDocument.create();
            for (var i = 1; i <= pdfDoc.numPages; i++) {
                var page = await pdfDoc.getPage(i);
                var baseViewport = page.getViewport({ scale: 1 });
                var viewport = page.getViewport({ scale: preset.scale });
                var canvas = document.createElement('canvas');
                canvas.width = viewport.width;
                canvas.height = viewport.height;
                var ctx = canvas.getContext('2d');
                ctx.fillStyle = '#ffffff';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
                await page.render({ canvasContext: ctx, viewport: viewport }).promise;

                var jpgBlob = await canvasToBlob(canvas, preset.quality);
                if (!jpgBlob) throw new Error('Image conversion failed on page ' + i);
                var jpgBytes = await blobToArrayBuffer(jpgBlob);
                var embedded = await newPdf.embedJpg(jpgBytes);
                var newPage = newPdf.addPage([baseViewport.width, baseViewport.height]);
                newPage.drawImage(embedded, { x: 0, y: 0, width: baseViewport.width, height: baseViewport.height });

                var pct = Math.round((i / pdfDoc.numPages) * 100);
                progressBar.style.width = pct + '%';
                progressBar.textContent = pct + '%';
            }
            var newBytes = await newPdf.save();
            resultBlob = new Blob([newBytes], { type: 'application/pdf' });

            var savedPct = ((selectedFile.size - resultBlob.size) / selectedFile.size) * 100;
            resultOriginal.textContent = formatSize(selectedFile.size);
            resultNew.textContent = formatSize(resultBlob.size);
            if (savedPct >= 0) {
                resultSaved.textContent = savedPct.toFixed(1) + '% smaller';
                resultSaved.className = 'fw-bold text-success';
                showSuccess('Done! Your PDF was compressed from ' + formatSize(selectedFile.size) + ' to ' + formatSize(resultBlob.size) + ' (' + savedPct.toFixed(1) + '% saved).');
            } else {
                resultSaved.textContent = Math.abs(savedPct).toFixed(1) + '% larger';
                resultSaved.className = 'fw-bold text-warning';
                showSuccess('Done, but the new file is slightly larger than the original. This can happen with text-based PDFs — try the Low preset for a smaller result.');
            }
            resultWrap.classList.remove('d-none');
        } catch (err) {
            console.error(err);
            showError('Something went wrong while compressing this PDF. Please try again, or try a lower quality preset.');
        } finally {
            compressBtn.disabled = false;
            compressBtn.textContent = 'Compress PDF';
            setTimeout(function () { progressWrap.classList.add('d-none'); progressBar.style.width = '0%'; progressBar.textContent = '0%'; }, 800);
        }
    });

    downloadBtn.addEventListener('click', function () {
        if (!resultBlob || !selectedFile) {
            showError('Please compress the PDF first.');
            return;
        }
        var url = URL.createObjectURL(resultBlob);
        var a = document.createElement('a');
        a.href = url;
        a.download = baseName(selectedFile.name) + '-compressed.pdf';
        document.body.appendChild(a);
        a.click();
        a.remove();
        setTimeout(function () { URL.revokeObjectURL(url); }, 4000);
    });
})();
</script>
@endsection
