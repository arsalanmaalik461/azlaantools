@extends('layouts.app')
@section('title', 'Image Resizer - Resize Images to Exact Pixels Online Free | Azlaan Tools')
@section('meta_description', 'Resize images to exact pixel dimensions online for free. Presets for WhatsApp DP, Facebook cover, YouTube thumbnail and Instagram post. Download as PNG or JPG - files never leave your browser.')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <h1 class="mb-2">Image Resizer</h1>
            <p class="lead text-muted">Resize any image to the exact width and height you need — for a profile picture, cover photo, thumbnail or post. Use a ready-made preset or type your own dimensions.</p>

            <div class="alert alert-success d-flex align-items-start" role="alert">
                <span class="me-2" aria-hidden="true">&#128274;</span>
                <div><strong>Private by design:</strong> Your files never leave your browser. Resizing happens on your own device using the Canvas API — nothing is uploaded, stored or shared.</div>
            </div>

            <div id="dropZone" class="border border-2 border-primary rounded-3 p-4 p-md-5 text-center bg-light mb-4" style="border-style: dashed !important; cursor: pointer;">
                <div class="fs-1 mb-2" aria-hidden="true">&#128444;</div>
                <p class="mb-1 fw-semibold">Drag &amp; drop your image here, or click to browse</p>
                <p class="text-muted small mb-0">Supports JPG, PNG, WebP and other browser-readable images</p>
                <input type="file" id="fileInput" class="d-none" accept="image/*">
            </div>

            <div id="errorBox" class="alert alert-danger d-none" role="alert"></div>

            <div id="toolArea" class="d-none">
                <div class="card mb-4">
                    <div class="card-header fw-semibold">Original Image</div>
                    <div class="card-body">
                        <div class="row align-items-center g-3">
                            <div class="col-md-5 text-center">
                                <img id="originalPreview" class="img-fluid rounded" alt="Original image preview" style="max-height: 260px; object-fit: contain;">
                            </div>
                            <div class="col-md-7">
                                <ul class="list-group list-group-flush small">
                                    <li class="list-group-item d-flex justify-content-between"><span>File name</span><strong id="originalName" class="text-truncate ms-2">-</strong></li>
                                    <li class="list-group-item d-flex justify-content-between"><span>Original dimensions</span><strong id="originalDims">-</strong></li>
                                    <li class="list-group-item d-flex justify-content-between"><span>Original size</span><strong id="originalSize">-</strong></li>
                                    <li class="list-group-item d-flex justify-content-between"><span>Aspect ratio</span><strong id="originalRatio">-</strong></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header fw-semibold">New Size</div>
                    <div class="card-body">
                        <div class="row g-3 align-items-end">
                            <div class="col-6 col-md-4">
                                <label for="widthInput" class="form-label">Width (px)</label>
                                <input type="number" class="form-control" id="widthInput" min="1" max="20000" placeholder="Width">
                            </div>
                            <div class="col-6 col-md-4">
                                <label for="heightInput" class="form-label">Height (px)</label>
                                <input type="number" class="form-control" id="heightInput" min="1" max="20000" placeholder="Height">
                            </div>
                            <div class="col-md-4">
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="lockRatio" checked>
                                    <label class="form-check-label" for="lockRatio">Lock aspect ratio</label>
                                </div>
                                <p class="form-text mb-0">Keep this on to avoid stretching or squashing the image.</p>
                            </div>
                        </div>

                        <hr>
                        <p class="fw-semibold mb-2">Preset sizes</p>
                        <div class="d-flex flex-wrap gap-2">
                            <button type="button" class="btn btn-outline-primary preset-btn" data-width="640" data-height="640">WhatsApp DP — 640 x 640</button>
                            <button type="button" class="btn btn-outline-primary preset-btn" data-width="820" data-height="312">Facebook Cover — 820 x 312</button>
                            <button type="button" class="btn btn-outline-primary preset-btn" data-width="1280" data-height="720">YouTube Thumbnail — 1280 x 720</button>
                            <button type="button" class="btn btn-outline-primary preset-btn" data-width="1080" data-height="1080">Instagram Post — 1080 x 1080</button>
                        </div>

                        <hr>
                        <div class="row g-3 align-items-end">
                            <div class="col-md-6">
                                <label for="formatSelect" class="form-label">Download format</label>
                                <select class="form-select" id="formatSelect">
                                    <option value="image/png" selected>PNG (best quality, larger file)</option>
                                    <option value="image/jpeg">JPG (smaller file, no transparency)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <div class="d-grid d-sm-flex gap-2">
                                    <button type="button" id="resizeBtn" class="btn btn-primary flex-sm-grow-1">Resize Image</button>
                                    <button type="button" id="resetBtn" class="btn btn-outline-secondary">Another Image</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="resultCard" class="card d-none">
                    <div class="card-header fw-semibold">Resized Result</div>
                    <div class="card-body text-center">
                        <img id="resizedPreview" class="img-fluid rounded mb-3" alt="Resized image preview" style="max-height: 340px; object-fit: contain;">
                        <ul class="list-group list-group-flush text-start small mb-3">
                            <li class="list-group-item d-flex justify-content-between"><span>New dimensions</span><strong id="resizedDims">-</strong></li>
                            <li class="list-group-item d-flex justify-content-between"><span>New size</span><strong id="resizedSize">-</strong></li>
                        </ul>
                        <a id="downloadBtn" href="#" download="resized-image.png" class="btn btn-success w-100">Download Resized Image</a>
                    </div>
                </div>
            </div>

            <h2 class="mt-5">How to use</h2>
            <ol>
                <li>Drag and drop your image into the box above, or click the box to select a file from your device.</li>
                <li>Note the original dimensions, then type the new Width and Height in pixels. With <strong>Lock aspect ratio</strong> on, changing one side updates the other automatically.</li>
                <li>Or tap a preset — WhatsApp DP, Facebook cover, YouTube thumbnail or Instagram post — to fill in the sizes instantly.</li>
                <li>Choose PNG or JPG as the download format.</li>
                <li>Click <strong>Resize Image</strong>, check the preview, then click <strong>Download Resized Image</strong>.</li>
            </ol>

            <h2 class="mt-4">Tips</h2>
            <p class="text-muted">Resizing stretches or fits the whole image into the new dimensions — it does not crop. If the preset ratio is very different from your photo (for example a wide Facebook cover from a square photo), turn the aspect lock off only if a slight stretch is acceptable, or crop the image first. Making an image much larger than the original will look soft or blurry; resizing smaller always stays sharp.</p>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
(function () {
    var dropZone = document.getElementById('dropZone');
    var fileInput = document.getElementById('fileInput');
    var errorBox = document.getElementById('errorBox');
    var toolArea = document.getElementById('toolArea');
    var originalPreview = document.getElementById('originalPreview');
    var originalName = document.getElementById('originalName');
    var originalDims = document.getElementById('originalDims');
    var originalSize = document.getElementById('originalSize');
    var originalRatio = document.getElementById('originalRatio');
    var widthInput = document.getElementById('widthInput');
    var heightInput = document.getElementById('heightInput');
    var lockRatio = document.getElementById('lockRatio');
    var formatSelect = document.getElementById('formatSelect');
    var resizeBtn = document.getElementById('resizeBtn');
    var resetBtn = document.getElementById('resetBtn');
    var resultCard = document.getElementById('resultCard');
    var resizedPreview = document.getElementById('resizedPreview');
    var resizedDims = document.getElementById('resizedDims');
    var resizedSize = document.getElementById('resizedSize');
    var downloadBtn = document.getElementById('downloadBtn');
    var presetBtns = document.querySelectorAll('.preset-btn');

    var currentFile = null;
    var currentImage = null;
    var originalObjectUrl = null;
    var resizedObjectUrl = null;
    var aspect = 1;

    function formatBytes(bytes) {
        if (bytes === 0) return '0 B';
        var units = ['B', 'KB', 'MB', 'GB'];
        var i = Math.floor(Math.log(bytes) / Math.log(1024));
        if (i >= units.length) i = units.length - 1;
        var value = bytes / Math.pow(1024, i);
        return value.toFixed(i === 0 ? 0 : 2) + ' ' + units[i];
    }

    function gcd(a, b) {
        return b === 0 ? a : gcd(b, a % b);
    }

    function showError(message) {
        errorBox.textContent = message;
        errorBox.classList.remove('d-none');
    }

    function hideError() {
        errorBox.textContent = '';
        errorBox.classList.add('d-none');
    }

    function baseName(name) {
        var dot = name.lastIndexOf('.');
        return dot > 0 ? name.substring(0, dot) : name;
    }

    function handleFile(file) {
        hideError();
        if (!file) return;
        if (file.type.indexOf('image/') !== 0) {
            showError('Please choose an image file.');
            return;
        }
        currentFile = file;
        if (originalObjectUrl) URL.revokeObjectURL(originalObjectUrl);
        originalObjectUrl = URL.createObjectURL(file);
        var img = new Image();
        img.onload = function () {
            currentImage = img;
            aspect = img.naturalWidth / img.naturalHeight;
            originalPreview.src = originalObjectUrl;
            originalName.textContent = file.name;
            originalName.title = file.name;
            originalDims.textContent = img.naturalWidth + ' x ' + img.naturalHeight + ' px';
            originalSize.textContent = formatBytes(file.size);
            var divisor = gcd(img.naturalWidth, img.naturalHeight);
            originalRatio.textContent = (img.naturalWidth / divisor) + ':' + (img.naturalHeight / divisor);
            widthInput.value = img.naturalWidth;
            heightInput.value = img.naturalHeight;
            resultCard.classList.add('d-none');
            toolArea.classList.remove('d-none');
        };
        img.onerror = function () {
            showError('That file could not be read as an image. Please try another file.');
        };
        img.src = originalObjectUrl;
    }

    function resizeImage() {
        hideError();
        if (!currentFile || !currentImage) return;
        var targetWidth = parseInt(widthInput.value, 10);
        var targetHeight = parseInt(heightInput.value, 10);
        if (isNaN(targetWidth) || targetWidth < 1 || isNaN(targetHeight) || targetHeight < 1) {
            showError('Please enter a valid width and height (at least 1 px).');
            return;
        }
        var canvas = document.createElement('canvas');
        canvas.width = targetWidth;
        canvas.height = targetHeight;
        var ctx = canvas.getContext('2d');
        ctx.imageSmoothingEnabled = true;
        ctx.imageSmoothingQuality = 'high';
        var outputType = formatSelect.value;
        if (outputType === 'image/jpeg') {
            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, targetWidth, targetHeight);
        }
        ctx.drawImage(currentImage, 0, 0, targetWidth, targetHeight);
        canvas.toBlob(function (blob) {
            if (!blob) {
                showError('Resizing failed in this browser. Please try another image.');
                return;
            }
            if (resizedObjectUrl) URL.revokeObjectURL(resizedObjectUrl);
            resizedObjectUrl = URL.createObjectURL(blob);
            resizedPreview.src = resizedObjectUrl;
            resizedDims.textContent = targetWidth + ' x ' + targetHeight + ' px';
            resizedSize.textContent = formatBytes(blob.size);
            var ext = outputType === 'image/jpeg' ? 'jpg' : 'png';
            downloadBtn.href = resizedObjectUrl;
            downloadBtn.setAttribute('download', baseName(currentFile.name) + '-' + targetWidth + 'x' + targetHeight + '.' + ext);
            resultCard.classList.remove('d-none');
            resultCard.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }, outputType, 0.92);
    }

    widthInput.addEventListener('input', function () {
        if (lockRatio.checked && currentImage) {
            var w = parseInt(widthInput.value, 10);
            if (!isNaN(w) && w > 0) heightInput.value = Math.max(1, Math.round(w / aspect));
        }
    });
    heightInput.addEventListener('input', function () {
        if (lockRatio.checked && currentImage) {
            var h = parseInt(heightInput.value, 10);
            if (!isNaN(h) && h > 0) widthInput.value = Math.max(1, Math.round(h * aspect));
        }
    });

    for (var i = 0; i < presetBtns.length; i++) {
        presetBtns[i].addEventListener('click', function () {
            widthInput.value = this.getAttribute('data-width');
            heightInput.value = this.getAttribute('data-height');
            if (currentImage) resizeImage();
        });
    }

    dropZone.addEventListener('click', function () { fileInput.click(); });
    fileInput.addEventListener('change', function () {
        if (fileInput.files && fileInput.files.length > 0) handleFile(fileInput.files[0]);
    });
    dropZone.addEventListener('dragover', function (e) {
        e.preventDefault();
        dropZone.classList.add('bg-primary-subtle');
    });
    dropZone.addEventListener('dragleave', function () {
        dropZone.classList.remove('bg-primary-subtle');
    });
    dropZone.addEventListener('drop', function (e) {
        e.preventDefault();
        dropZone.classList.remove('bg-primary-subtle');
        if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length > 0) {
            handleFile(e.dataTransfer.files[0]);
        }
    });

    resizeBtn.addEventListener('click', resizeImage);
    resetBtn.addEventListener('click', function () {
        fileInput.value = '';
        toolArea.classList.add('d-none');
        resultCard.classList.add('d-none');
        currentFile = null;
        currentImage = null;
        fileInput.click();
    });
})();
</script>
@endsection
