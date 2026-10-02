@extends('layouts.app')
@section('title', 'PNG to PDF Converter Online Free — Azlaan Tools')
@section('meta_description', 'Convert PNG, JPG and WebP images into one PDF online for free. Choose page size and margins. No signup, no upload — files are processed in your browser.')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">PNG to PDF</h1>
            <p class="lead text-muted">Turn your PNG images into a single PDF file. JPG and WebP images are also accepted and converted automatically.</p>

            <div id="alertBox" class="alert alert-danger d-none" role="alert"></div>
            <div id="successBox" class="alert alert-success d-none" role="alert"></div>

            <div id="dropZone" class="border border-2 border-dashed rounded-3 p-4 p-md-5 text-center mb-3" style="border-style: dashed !important; cursor: pointer; background: #f8f9fa;">
                <div class="fs-1 mb-2">🖼️</div>
                <p class="mb-1 fw-semibold">Drag &amp; drop PNG / JPG / WebP images here</p>
                <p class="text-muted mb-3">or click to browse from your device</p>
                <button type="button" class="btn btn-primary">Select Images</button>
                <input type="file" id="fileInput" accept="image/png,image/jpeg,image/webp,.png,.jpg,.jpeg,.webp" multiple class="d-none">
            </div>

            <div id="optionsWrap" class="d-none">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h2 class="h5">Selected Images (<span id="fileCount">0</span>)</h2>
                        <div id="previewGrid" class="row g-3 mb-3"></div>
                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-6">
                                <label for="pageSizeSelect" class="form-label fw-semibold">Page size</label>
                                <select id="pageSizeSelect" class="form-select form-select-lg">
                                    <option value="a4" selected>A4 (210 × 297 mm)</option>
                                    <option value="fit">Fit to image</option>
                                    <option value="letter">Letter (8.5 × 11 in)</option>
                                </select>
                                <div class="form-text">Orientation is automatic — portrait or landscape is chosen per image.</div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="marginSelect" class="form-label fw-semibold">Margin</label>
                                <select id="marginSelect" class="form-select form-select-lg">
                                    <option value="0">None</option>
                                    <option value="18">Small</option>
                                    <option value="36" selected>Medium</option>
                                    <option value="54">Large</option>
                                </select>
                                <div class="form-text">Margins are ignored in Fit to image mode.</div>
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
                </div>
            </div>

            <div class="alert alert-info mt-4">
                <strong>Privacy note:</strong> Your files never leave your browser — everything happens on your phone or computer, nothing is uploaded.
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Click the box above or drag and drop PNG, JPG or WebP images into it.</li>
                <li>Check the previews and use Up / Down or Remove to arrange the images — each image becomes one page, in order.</li>
                <li>Choose a page size (A4, Letter, or Fit to image) and a margin.</li>
                <li>Click <strong>Create PDF</strong> and your file will download automatically as images.pdf.</li>
            </ol>
        </div>
    </div>
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
    var marginSelect = document.getElementById('marginSelect');
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
    function isAccepted(f) {
        var name = f.name.toLowerCase();
        return f.type === 'image/png' || f.type === 'image/jpeg' || f.type === 'image/webp' ||
            name.endsWith('.png') || name.endsWith('.jpg') || name.endsWith('.jpeg') || name.endsWith('.webp');
    }
    function isPng(f) {
        return f.type === 'image/png' || f.name.toLowerCase().endsWith('.png');
    }
    function isJpg(f) {
        return f.type === 'image/jpeg' || f.name.toLowerCase().endsWith('.jpg') || f.name.toLowerCase().endsWith('.jpeg');
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
            if (isAccepted(f)) {
                images.push({ file: f, url: URL.createObjectURL(f) });
                added++;
            }
        }
        if (added === 0) {
            showError('Please select PNG, JPG or WebP images only.');
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
    async function getPngBytes(item) {
        var img = await loadImage(item.url);
        var canvas = document.createElement('canvas');
        canvas.width = img.naturalWidth;
        canvas.height = img.naturalHeight;
        canvas.getContext('2d').drawImage(img, 0, 0);
        var blob = await new Promise(function (resolve) { canvas.toBlob(resolve, 'image/png'); });
        return blob.arrayBuffer();
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
        var margin = parseFloat(marginSelect.value) || 0;
        convertBtn.disabled = true;
        convertBtn.textContent = 'Creating PDF...';
        progressWrap.classList.remove('d-none');
        try {
            var pdfDoc = await PDFLib.PDFDocument.create();
            for (var i = 0; i < images.length; i++) {
                var item = images[i];
                var embedded;
                if (isPng(item.file)) {
                    embedded = await pdfDoc.embedPng(await item.file.arrayBuffer());
                } else if (isJpg(item.file)) {
                    embedded = await pdfDoc.embedJpg(await item.file.arrayBuffer());
                } else {
                    embedded = await pdfDoc.embedPng(await getPngBytes(item));
                }
                var imgObj = await loadImage(item.url);
                var imgW = imgObj.naturalWidth;
                var imgH = imgObj.naturalHeight;
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
                    var availW = Math.max(50, pageW - margin * 2);
                    var availH = Math.max(50, pageH - margin * 2);
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
            showError('Could not create the PDF. One of the images may be corrupted or in an unsupported format. Please try PNG, JPG or WebP images.');
        } finally {
            convertBtn.disabled = false;
            convertBtn.textContent = 'Create PDF';
            setTimeout(function () { progressWrap.classList.add('d-none'); progressBar.style.width = '0%'; progressBar.textContent = '0%'; }, 800);
        }
    });
})();
</script>
@endsection
