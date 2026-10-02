@extends('layouts.app')
@section('title', 'JPG to PDF Converter Online Free — Azlaan Tools')
@section('meta_description', 'Convert JPG and PNG images into a PDF online for free. No signup, no upload — files are processed in your browser.')
@section('content')
<div class="container py-4 pdf-tool">
    <h1 class="mb-3">JPG to PDF</h1>
    <p class="lead">Turn your JPG and PNG images into a single PDF file. Add photos or scans, arrange them, choose a page size, and download your PDF.</p>

    <div id="alertBox" class="alert alert-danger d-none" role="alert"></div>
    <div id="successBox" class="alert alert-success d-none" role="alert"></div>

    <div id="dropZone" class="border border-2 border-dashed rounded-3 p-4 p-md-5 text-center mb-3" style="border-style: dashed !important; cursor: pointer; background: #f8f9fa;">
        <div class="fs-1 mb-2">📷</div>
        <p class="mb-1 fw-semibold">Drag &amp; drop JPG / PNG images here</p>
        <p class="text-muted mb-3">or click to browse from your device</p>
        <button type="button" class="btn btn-primary">Select Images</button>
        <input type="file" id="fileInput" accept="image/jpeg,image/png,.jpg,.jpeg,.png" multiple class="d-none">
    </div>

    <div id="optionsWrap" class="d-none">
        <h2 class="h5">Selected Images (<span id="fileCount">0</span>)</h2>
        <div id="previewGrid" class="row g-3 mb-3"></div>

        <div class="row g-3 mb-3">
            <div class="col-12 col-md-4">
                <label for="pageSizeSelect" class="form-label">Page size</label>
                <select id="pageSizeSelect" class="form-select">
                    <option value="a4" selected>A4 (210 × 297 mm)</option>
                    <option value="fit">Fit to image (no margins)</option>
                    <option value="letter">Letter (8.5 × 11 in)</option>
                </select>
                <div class="form-text">Orientation is automatic — portrait or landscape is chosen per image.</div>
            </div>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <button type="button" id="convertBtn" class="btn btn-success btn-lg">Create PDF</button>
            <button type="button" id="clearBtn" class="btn btn-outline-secondary">Clear All</button>
        </div>
        <div id="progressWrap" class="progress mt-3 d-none" style="height: 24px;">
            <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%;">0%</div>
        </div>
    </div>

    <div class="alert alert-info mt-4">
        <strong>Privacy note:</strong> Your files never leave your browser — everything happens on your phone or computer, nothing is uploaded.
    </div>

    <h2>How to use</h2>
    <ol>
        <li>Click the box above or drag and drop JPG or PNG images into it.</li>
        <li>Check the previews and use Up / Down or Remove to arrange the images — each image becomes one page, in order.</li>
        <li>Choose a page size: A4, Letter, or Fit to image.</li>
        <li>Click <strong>Create PDF</strong> and your file will download automatically as images.pdf.</li>
    </ol>
</div>
@endsection
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/pdf-lib@1.17.1/dist/pdf-lib.min.js"></script>
<script>
(function () {
    var images = [];

    var dropZone = document.getElementById('dropZone');
    var fileInput = document.getElementById('fileInput');
    var optionsWrap = document.getElementById('optionsWrap');
    var previewGrid = document.getElementById('previewGrid');
    var fileCount = document.getElementById('fileCount');
    var pageSizeSelect = document.getElementById('pageSizeSelect');
    var convertBtn = document.getElementById('convertBtn');
    var clearBtn = document.getElementById('clearBtn');
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

    function renderPreviews() {
        previewGrid.innerHTML = '';
        fileCount.textContent = images.length;
        if (images.length === 0) {
            optionsWrap.classList.add('d-none');
            return;
        }
        optionsWrap.classList.remove('d-none');
        images.forEach(function (item, idx) {
            var col = document.createElement('div');
            col.className = 'col-6 col-md-4 col-lg-3';
            var card = document.createElement('div');
            card.className = 'card h-100';

            var img = document.createElement('img');
            img.src = item.url;
            img.alt = item.file.name;
            img.className = 'card-img-top';
            img.style.height = '160px';
            img.style.objectFit = 'cover';
            card.appendChild(img);

            var body = document.createElement('div');
            body.className = 'card-body p-2';
            var label = document.createElement('p');
            label.className = 'small text-break mb-2';
            label.textContent = (idx + 1) + '. ' + item.file.name;
            body.appendChild(label);

            var btnRow = document.createElement('div');
            btnRow.className = 'd-flex gap-1 flex-wrap';

            var upBtn = document.createElement('button');
            upBtn.type = 'button';
            upBtn.className = 'btn btn-sm btn-outline-primary';
            upBtn.textContent = '↑';
            upBtn.disabled = idx === 0;
            upBtn.addEventListener('click', function () {
                var tmp = images[idx - 1]; images[idx - 1] = images[idx]; images[idx] = tmp;
                renderPreviews();
            });
            btnRow.appendChild(upBtn);

            var downBtn = document.createElement('button');
            downBtn.type = 'button';
            downBtn.className = 'btn btn-sm btn-outline-primary';
            downBtn.textContent = '↓';
            downBtn.disabled = idx === images.length - 1;
            downBtn.addEventListener('click', function () {
                var tmp = images[idx + 1]; images[idx + 1] = images[idx]; images[idx] = tmp;
                renderPreviews();
            });
            btnRow.appendChild(downBtn);

            var removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'btn btn-sm btn-outline-danger';
            removeBtn.textContent = 'Remove';
            removeBtn.addEventListener('click', function () {
                URL.revokeObjectURL(images[idx].url);
                images.splice(idx, 1);
                renderPreviews();
            });
            btnRow.appendChild(removeBtn);

            body.appendChild(btnRow);
            card.appendChild(body);
            col.appendChild(card);
            previewGrid.appendChild(col);
        });
    }

    function addFiles(fileListObj) {
        hideAlerts();
        var added = 0;
        for (var i = 0; i < fileListObj.length; i++) {
            var f = fileListObj[i];
            var name = f.name.toLowerCase();
            if (f.type === 'image/jpeg' || f.type === 'image/png' || name.endsWith('.jpg') || name.endsWith('.jpeg') || name.endsWith('.png')) {
                images.push({ file: f, url: URL.createObjectURL(f) });
                added++;
            }
        }
        if (added === 0) {
            showError('Please select JPG or PNG images only.');
        }
        renderPreviews();
    }

    dropZone.addEventListener('click', function () { fileInput.click(); });
    fileInput.addEventListener('change', function () {
        addFiles(fileInput.files);
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
        if (e.dataTransfer && e.dataTransfer.files) addFiles(e.dataTransfer.files);
    });

    clearBtn.addEventListener('click', function () {
        images.forEach(function (item) { URL.revokeObjectURL(item.url); });
        images = [];
        hideAlerts();
        renderPreviews();
    });

    function loadImage(url) {
        return new Promise(function (resolve, reject) {
            var img = new Image();
            img.onload = function () { resolve(img); };
            img.onerror = reject;
            img.src = url;
        });
    }
    function canvasToBlob(canvas, type, quality) {
        return new Promise(function (resolve, reject) {
            canvas.toBlob(function (blob) {
                if (blob) resolve(blob);
                else reject(new Error('Image conversion failed.'));
            }, type, quality);
        });
    }

    convertBtn.addEventListener('click', async function () {
        hideAlerts();
        if (images.length === 0) {
            showError('Please add at least one image.');
            return;
        }
        if (typeof PDFLib === 'undefined') {
            showError('PDF library failed to load. Please check your internet connection and try again.');
            return;
        }
        var sizeMode = pageSizeSelect.value;
        convertBtn.disabled = true;
        convertBtn.textContent = 'Creating PDF...';
        progressWrap.classList.remove('d-none');
        try {
            var pdfDoc = await PDFLib.PDFDocument.create();
            for (var i = 0; i < images.length; i++) {
                var item = images[i];
                // Re-encode every image through a canvas instead of embedding the
                // raw file bytes: the browser applies EXIF orientation when it
                // decodes the image (phone photos no longer come out sideways or
                // stretched), and the encoded format always matches the embed
                // call — pdf-lib throws on mislabelled files (e.g. a PNG named
                // .jpg) and on CMYK JPEGs when raw bytes are embedded directly.
                var imgEl = await loadImage(item.url);
                var imgW = imgEl.naturalWidth;
                var imgH = imgEl.naturalHeight;
                if (!imgW || !imgH) throw new Error('Could not read image ' + item.file.name);
                var isPng = item.file.type === 'image/png' || item.file.name.toLowerCase().endsWith('.png');
                var canvas = document.createElement('canvas');
                canvas.width = imgW;
                canvas.height = imgH;
                var cctx = canvas.getContext('2d');
                if (!isPng) {
                    cctx.fillStyle = '#ffffff';
                    cctx.fillRect(0, 0, imgW, imgH);
                }
                cctx.drawImage(imgEl, 0, 0, imgW, imgH);
                var imgBlob = await canvasToBlob(canvas, isPng ? 'image/png' : 'image/jpeg', 0.92);
                var buffer = await imgBlob.arrayBuffer();
                var embedded = isPng ? await pdfDoc.embedPng(buffer) : await pdfDoc.embedJpg(buffer);
                var isLandscape = imgW > imgH;

                var pageW, pageH;
                if (sizeMode === 'fit') {
                    var maxDim = 14400;
                    var scaleFit = Math.min(1, maxDim / Math.max(imgW, imgH));
                    pageW = imgW * scaleFit;
                    pageH = imgH * scaleFit;
                    var fitPage = pdfDoc.addPage([pageW, pageH]);
                    fitPage.drawImage(embedded, { x: 0, y: 0, width: pageW, height: pageH });
                } else {
                    if (sizeMode === 'letter') {
                        pageW = isLandscape ? 792 : 612;
                        pageH = isLandscape ? 612 : 792;
                    } else {
                        pageW = isLandscape ? 841.89 : 595.28;
                        pageH = isLandscape ? 595.28 : 841.89;
                    }
                    var margin = 36;
                    var availW = pageW - margin * 2;
                    var availH = pageH - margin * 2;
                    var scale = Math.min(availW / imgW, availH / imgH);
                    var drawW = imgW * scale;
                    var drawH = imgH * scale;
                    var page = pdfDoc.addPage([pageW, pageH]);
                    page.drawImage(embedded, {
                        x: (pageW - drawW) / 2,
                        y: (pageH - drawH) / 2,
                        width: drawW,
                        height: drawH
                    });
                }
                var pct = Math.round(((i + 1) / images.length) * 100);
                progressBar.style.width = pct + '%';
                progressBar.textContent = pct + '%';
            }
            var pdfBytes = await pdfDoc.save();
            var blob = new Blob([pdfBytes], { type: 'application/pdf' });
            var url = URL.createObjectURL(blob);
            var a = document.createElement('a');
            a.href = url;
            a.download = 'images.pdf';
            document.body.appendChild(a);
            a.click();
            a.remove();
            setTimeout(function () { URL.revokeObjectURL(url); }, 4000);
            showSuccess('Done! Your PDF with ' + images.length + ' page(s) has been downloaded.');
        } catch (err) {
            console.error(err);
            showError('Could not create the PDF. One of the images may be corrupted or in an unsupported format. Please try JPG or PNG images.');
        } finally {
            convertBtn.disabled = false;
            convertBtn.textContent = 'Create PDF';
            setTimeout(function () { progressWrap.classList.add('d-none'); progressBar.style.width = '0%'; progressBar.textContent = '0%'; }, 800);
        }
    });
})();
</script>
@endsection
