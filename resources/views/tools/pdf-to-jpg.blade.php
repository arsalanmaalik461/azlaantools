@extends('layouts.app')
@section('title', 'PDF to JPG Converter Online Free — Azlaan Tools')
@section('meta_description', 'Convert PDF pages to JPG images online for free. No signup, no upload — files are processed in your browser.')
@section('content')
<div class="container py-4 pdf-tool">
    <h1 class="mb-3">PDF to JPG</h1>
    <p class="lead">Convert each page of a PDF into a high-quality JPG image. Download pages one by one, or get them all together in a ZIP file.</p>

    <div id="alertBox" class="alert alert-danger d-none" role="alert"></div>
    <div id="successBox" class="alert alert-success d-none" role="alert"></div>

    <div id="dropZone" class="border border-2 border-dashed rounded-3 p-4 p-md-5 text-center mb-3" style="border-style: dashed !important; cursor: pointer; background: #f8f9fa;">
        <div class="fs-1 mb-2">🖼️</div>
        <p class="mb-1 fw-semibold">Drag &amp; drop a PDF file here</p>
        <p class="text-muted mb-3">or click to browse from your device</p>
        <button type="button" class="btn btn-primary">Select PDF File</button>
        <input type="file" id="fileInput" accept="application/pdf,.pdf" class="d-none">
    </div>

    <div id="controlsWrap" class="d-none">
        <div class="card mb-3">
            <div class="card-body">
                <p class="mb-2"><strong>File:</strong> <span id="fileName" class="text-break"></span> &nbsp;|&nbsp; <strong>Pages:</strong> <span id="pageCount">-</span></p>
                <div class="row g-3 align-items-end">
                    <div class="col-12 col-md-4">
                        <label for="qualitySelect" class="form-label">JPG Quality</label>
                        <select id="qualitySelect" class="form-select">
                            <option value="0.7">Standard (0.7) — smaller file</option>
                            <option value="0.85" selected>High (0.85) — recommended</option>
                            <option value="0.95">Maximum (0.95) — best quality</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-8 d-flex flex-wrap gap-2">
                        <button type="button" id="convertBtn" class="btn btn-success">Convert to JPG</button>
                        <button type="button" id="downloadAllBtn" class="btn btn-primary d-none">Download All as ZIP</button>
                        <button type="button" id="clearBtn" class="btn btn-outline-secondary">Choose Another File</button>
                    </div>
                </div>
                <div id="progressWrap" class="progress mt-3 d-none" style="height: 24px;">
                    <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%;">0%</div>
                </div>
            </div>
        </div>
        <div id="previewGrid" class="row g-3"></div>
    </div>

    <div class="alert alert-info mt-4">
        <strong>Privacy note:</strong> Your files never leave your browser — everything runs on your phone or computer, nothing is uploaded.
    </div>

    <h2>How to use</h2>
    <ol>
        <li>Click the box above or drag and drop a PDF file into it.</li>
        <li>Choose the JPG quality — higher quality means a larger image file.</li>
        <li>Click <strong>Convert to JPG</strong> and wait for the page previews to appear.</li>
        <li>Download any single page with its Download button, or click <strong>Download All as ZIP</strong> to get every page at once.</li>
    </ol>
</div>
@endsection
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/pdfjs-dist@3.11.174/build/pdf.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script>
(function () {
    if (typeof pdfjsLib !== 'undefined') {
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdn.jsdelivr.net/npm/pdfjs-dist@3.11.174/build/pdf.worker.min.js';
    }

    var selectedFile = null;
    var pdfDoc = null;
    var converted = [];

    var dropZone = document.getElementById('dropZone');
    var fileInput = document.getElementById('fileInput');
    var controlsWrap = document.getElementById('controlsWrap');
    var fileNameEl = document.getElementById('fileName');
    var pageCountEl = document.getElementById('pageCount');
    var qualitySelect = document.getElementById('qualitySelect');
    var convertBtn = document.getElementById('convertBtn');
    var downloadAllBtn = document.getElementById('downloadAllBtn');
    var clearBtn = document.getElementById('clearBtn');
    var previewGrid = document.getElementById('previewGrid');
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
    function baseName(name) {
        return name.replace(/\.pdf$/i, '');
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
    function canvasToBlob(canvas, quality) {
        return new Promise(function (resolve) {
            canvas.toBlob(function (blob) { resolve(blob); }, 'image/jpeg', quality);
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
            converted = [];
            previewGrid.innerHTML = '';
            downloadAllBtn.classList.add('d-none');
            fileNameEl.textContent = file.name;
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
        converted = [];
        previewGrid.innerHTML = '';
        controlsWrap.classList.add('d-none');
        hideAlerts();
    });

    convertBtn.addEventListener('click', async function () {
        hideAlerts();
        if (!pdfDoc || !selectedFile) {
            showError('Please select a PDF file first.');
            return;
        }
        var quality = parseFloat(qualitySelect.value);
        convertBtn.disabled = true;
        convertBtn.textContent = 'Converting...';
        downloadAllBtn.classList.add('d-none');
        previewGrid.innerHTML = '';
        converted = [];
        progressWrap.classList.remove('d-none');
        try {
            for (var i = 1; i <= pdfDoc.numPages; i++) {
                var page = await pdfDoc.getPage(i);
                var viewport = page.getViewport({ scale: 2 });
                var canvas = document.createElement('canvas');
                canvas.width = viewport.width;
                canvas.height = viewport.height;
                var ctx = canvas.getContext('2d');
                ctx.fillStyle = '#ffffff';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
                await page.render({ canvasContext: ctx, viewport: viewport }).promise;

                var blob = await canvasToBlob(canvas, quality);
                if (!blob) throw new Error('Image conversion failed on page ' + i);
                var filename = baseName(selectedFile.name) + '-page-' + i + '.jpg';
                converted.push({ blob: blob, filename: filename });

                var thumbUrl = URL.createObjectURL(blob);
                var col = document.createElement('div');
                col.className = 'col-6 col-md-4 col-lg-3';
                var card = document.createElement('div');
                card.className = 'card h-100';
                var img = document.createElement('img');
                img.src = thumbUrl;
                img.alt = 'Page ' + i;
                img.className = 'card-img-top';
                img.style.maxHeight = '260px';
                img.style.objectFit = 'contain';
                img.style.background = '#f8f9fa';
                card.appendChild(img);
                var body = document.createElement('div');
                body.className = 'card-body text-center p-2';
                var label = document.createElement('p');
                label.className = 'mb-2 small fw-semibold';
                label.textContent = 'Page ' + i;
                body.appendChild(label);
                var dlBtn = document.createElement('button');
                dlBtn.type = 'button';
                dlBtn.className = 'btn btn-sm btn-primary w-100';
                dlBtn.textContent = 'Download JPG';
                (function (b, fn) {
                    dlBtn.addEventListener('click', function () { downloadBlob(b, fn); });
                })(blob, filename);
                body.appendChild(dlBtn);
                card.appendChild(body);
                col.appendChild(card);
                previewGrid.appendChild(col);

                var pct = Math.round((i / pdfDoc.numPages) * 100);
                progressBar.style.width = pct + '%';
                progressBar.textContent = pct + '%';
            }
            if (converted.length > 1) downloadAllBtn.classList.remove('d-none');
            showSuccess('Done! Converted ' + converted.length + ' page(s) to JPG.');
        } catch (err) {
            console.error(err);
            showError('Something went wrong while converting this PDF. Please try again.');
        } finally {
            convertBtn.disabled = false;
            convertBtn.textContent = 'Convert to JPG';
            setTimeout(function () { progressWrap.classList.add('d-none'); progressBar.style.width = '0%'; progressBar.textContent = '0%'; }, 800);
        }
    });

    downloadAllBtn.addEventListener('click', async function () {
        hideAlerts();
        if (converted.length === 0) {
            showError('Please convert the PDF first.');
            return;
        }
        if (typeof JSZip === 'undefined') {
            showError('ZIP library failed to load. Please check your internet connection and try again.');
            return;
        }
        downloadAllBtn.disabled = true;
        downloadAllBtn.textContent = 'Preparing ZIP...';
        try {
            var zip = new JSZip();
            converted.forEach(function (item) { zip.file(item.filename, item.blob); });
            var zipBlob = await zip.generateAsync({ type: 'blob' });
            downloadBlob(zipBlob, baseName(selectedFile.name) + '-images.zip');
            showSuccess('Done! All JPG images have been downloaded in a ZIP file.');
        } catch (err) {
            console.error(err);
            showError('Could not create the ZIP file. Please download the images one by one.');
        } finally {
            downloadAllBtn.disabled = false;
            downloadAllBtn.textContent = 'Download All as ZIP';
        }
    });
})();
</script>
@endsection
