@extends('layouts.app')
@section('title', 'Image Compressor - Compress JPG, PNG & WebP Online Free | Azlaan Tools')
@section('meta_description', 'Compress JPG, PNG and WebP images online for free. Reduce image file size with a quality slider, live preview and instant download. 100% private - files never leave your browser.')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <h1 class="mb-2">Image Compressor</h1>
            <p class="lead text-muted">Shrink JPG, PNG and WebP images in seconds. Pick the quality you want, see exactly how much space you save, and download the compressed image — no signup, no upload to any server.</p>

            <div class="alert alert-success d-flex align-items-start" role="alert">
                <span class="me-2" aria-hidden="true">&#128274;</span>
                <div><strong>Private by design:</strong> Your files never leave your browser. Compression happens on your own device using the Canvas API — nothing is uploaded, stored or shared.</div>
            </div>

            <div id="dropZone" class="border border-2 border-primary rounded-3 p-4 p-md-5 text-center bg-light mb-4" style="border-style: dashed !important; cursor: pointer;">
                <div class="fs-1 mb-2" aria-hidden="true">&#128444;</div>
                <p class="mb-1 fw-semibold">Drag &amp; drop your image here, or click to browse</p>
                <p class="text-muted small mb-0">Supports JPG, PNG and WebP</p>
                <input type="file" id="fileInput" class="d-none" accept="image/jpeg,image/png,image/webp">
            </div>

            <div id="errorBox" class="alert alert-danger d-none" role="alert"></div>

            <div id="toolArea" class="d-none">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header fw-semibold">Original</div>
                            <div class="card-body text-center">
                                <img id="originalPreview" class="img-fluid rounded mb-3" alt="Original image preview" style="max-height: 300px; object-fit: contain;">
                                <ul class="list-group list-group-flush text-start small">
                                    <li class="list-group-item d-flex justify-content-between"><span>File name</span><strong id="originalName" class="text-truncate ms-2">-</strong></li>
                                    <li class="list-group-item d-flex justify-content-between"><span>Dimensions</span><strong id="originalDims">-</strong></li>
                                    <li class="list-group-item d-flex justify-content-between"><span>Size</span><strong id="originalSize">-</strong></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header fw-semibold">Compressed</div>
                            <div class="card-body text-center">
                                <img id="compressedPreview" class="img-fluid rounded mb-3 d-none" alt="Compressed image preview" style="max-height: 300px; object-fit: contain;">
                                <p id="compressedPlaceholder" class="text-muted py-4 mb-0">Adjust the settings below to see the result.</p>
                                <ul class="list-group list-group-flush text-start small">
                                    <li class="list-group-item d-flex justify-content-between"><span>Dimensions</span><strong id="compressedDims">-</strong></li>
                                    <li class="list-group-item d-flex justify-content-between"><span>New size</span><strong id="compressedSize">-</strong></li>
                                    <li class="list-group-item d-flex justify-content-between"><span>You save</span><strong id="savedPercent" class="text-success">-</strong></li>
                                </ul>
                                <a id="downloadBtn" href="#" download="compressed-image.jpg" class="btn btn-success w-100 mt-3 disabled" aria-disabled="true">Download Compressed Image</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header fw-semibold">Compression Settings</div>
                    <div class="card-body">
                        <label for="qualityRange" class="form-label d-flex justify-content-between">
                            <span>Quality</span>
                            <span><strong id="qualityValue">80</strong>% <span class="text-muted">(lower = smaller file)</span></span>
                        </label>
                        <input type="range" class="form-range" id="qualityRange" min="10" max="100" step="1" value="80">
                        <p class="form-text">Quality applies to JPG and WebP. PNG is lossless, so for PNG files use Max Width below to reduce size.</p>

                        <label for="maxWidthInput" class="form-label mt-3">Max Width (optional)</label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="maxWidthInput" min="1" placeholder="e.g. 1200 — leave empty to keep original width">
                            <span class="input-group-text">px</span>
                        </div>
                        <p class="form-text">If the image is wider than this, it will be scaled down proportionally.</p>

                        <div class="d-grid d-sm-flex gap-2 mt-3">
                            <button type="button" id="compressBtn" class="btn btn-primary flex-sm-grow-1">Compress Image</button>
                            <button type="button" id="resetBtn" class="btn btn-outline-secondary">Choose Another Image</button>
                        </div>
                    </div>
                </div>
            </div>

            <h2 class="mt-5">How to use</h2>
            <ol>
                <li>Drag and drop your JPG, PNG or WebP image into the box above, or click the box to select a file from your device.</li>
                <li>Check the original file size and dimensions shown on the left.</li>
                <li>Move the Quality slider — 70–85% is a good balance for photos. Optionally set a Max Width to also shrink the dimensions.</li>
                <li>Click <strong>Compress Image</strong> (or adjust the slider) and compare the live preview, new size and percentage saved.</li>
                <li>Click <strong>Download Compressed Image</strong> to save the result to your device.</li>
            </ol>

            <h2 class="mt-4">Good to know</h2>
            <p class="text-muted">Smaller images load faster on websites, are easier to share on WhatsApp, and take less storage. For screenshots and graphics with text, keep the quality higher (85–95%) so edges stay sharp. For profile pictures and social posts, 70–80% usually looks identical at a fraction of the size.</p>
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
    var compressedPreview = document.getElementById('compressedPreview');
    var compressedPlaceholder = document.getElementById('compressedPlaceholder');
    var compressedDims = document.getElementById('compressedDims');
    var compressedSize = document.getElementById('compressedSize');
    var savedPercent = document.getElementById('savedPercent');
    var downloadBtn = document.getElementById('downloadBtn');
    var qualityRange = document.getElementById('qualityRange');
    var qualityValue = document.getElementById('qualityValue');
    var maxWidthInput = document.getElementById('maxWidthInput');
    var compressBtn = document.getElementById('compressBtn');
    var resetBtn = document.getElementById('resetBtn');

    var currentFile = null;
    var currentImage = null;
    var originalObjectUrl = null;
    var compressedObjectUrl = null;

    function formatBytes(bytes) {
        if (bytes === 0) return '0 B';
        var units = ['B', 'KB', 'MB', 'GB'];
        var i = Math.floor(Math.log(bytes) / Math.log(1024));
        if (i >= units.length) i = units.length - 1;
        var value = bytes / Math.pow(1024, i);
        return value.toFixed(i === 0 ? 0 : 2) + ' ' + units[i];
    }

    function showError(message) {
        errorBox.textContent = message;
        errorBox.classList.remove('d-none');
    }

    function hideError() {
        errorBox.textContent = '';
        errorBox.classList.add('d-none');
    }

    function extensionForType(type) {
        if (type === 'image/png') return 'png';
        if (type === 'image/webp') return 'webp';
        return 'jpg';
    }

    function baseName(name) {
        var dot = name.lastIndexOf('.');
        return dot > 0 ? name.substring(0, dot) : name;
    }

    function handleFile(file) {
        hideError();
        if (!file) return;
        var allowed = ['image/jpeg', 'image/png', 'image/webp'];
        if (allowed.indexOf(file.type) === -1) {
            showError('Please choose a JPG, PNG or WebP image.');
            return;
        }
        currentFile = file;
        if (originalObjectUrl) URL.revokeObjectURL(originalObjectUrl);
        originalObjectUrl = URL.createObjectURL(file);
        var img = new Image();
        img.onload = function () {
            currentImage = img;
            originalPreview.src = originalObjectUrl;
            originalName.textContent = file.name;
            originalName.title = file.name;
            originalDims.textContent = img.naturalWidth + ' x ' + img.naturalHeight + ' px';
            originalSize.textContent = formatBytes(file.size);
            toolArea.classList.remove('d-none');
            compressImage();
        };
        img.onerror = function () {
            showError('That file could not be read as an image. Please try another file.');
        };
        img.src = originalObjectUrl;
    }

    function compressImage() {
        if (!currentFile || !currentImage) return;
        var quality = parseInt(qualityRange.value, 10) / 100;
        var maxWidth = parseInt(maxWidthInput.value, 10);
        var targetWidth = currentImage.naturalWidth;
        var targetHeight = currentImage.naturalHeight;
        if (!isNaN(maxWidth) && maxWidth > 0 && maxWidth < targetWidth) {
            var ratio = maxWidth / targetWidth;
            targetWidth = Math.round(targetWidth * ratio);
            targetHeight = Math.round(targetHeight * ratio);
        }
        var canvas = document.createElement('canvas');
        canvas.width = targetWidth;
        canvas.height = targetHeight;
        var ctx = canvas.getContext('2d');
        if (currentFile.type === 'image/jpeg') {
            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, targetWidth, targetHeight);
        }
        ctx.drawImage(currentImage, 0, 0, targetWidth, targetHeight);
        var outputType = currentFile.type;
        canvas.toBlob(function (blob) {
            if (!blob) {
                showError('Compression failed in this browser. Please try another image.');
                return;
            }
            if (compressedObjectUrl) URL.revokeObjectURL(compressedObjectUrl);
            compressedObjectUrl = URL.createObjectURL(blob);
            compressedPreview.src = compressedObjectUrl;
            compressedPreview.classList.remove('d-none');
            compressedPlaceholder.classList.add('d-none');
            compressedDims.textContent = targetWidth + ' x ' + targetHeight + ' px';
            compressedSize.textContent = formatBytes(blob.size);
            var diff = currentFile.size - blob.size;
            var pct = (diff / currentFile.size) * 100;
            if (diff >= 0) {
                savedPercent.textContent = pct.toFixed(1) + '% smaller (' + formatBytes(diff) + ' saved)';
                savedPercent.className = 'text-success';
            } else {
                savedPercent.textContent = Math.abs(pct).toFixed(1) + '% larger — try a lower quality';
                savedPercent.className = 'text-danger';
            }
            downloadBtn.href = compressedObjectUrl;
            downloadBtn.setAttribute('download', baseName(currentFile.name) + '-compressed.' + extensionForType(outputType));
            downloadBtn.classList.remove('disabled');
            downloadBtn.removeAttribute('aria-disabled');
        }, outputType, quality);
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

    qualityRange.addEventListener('input', function () {
        qualityValue.textContent = qualityRange.value;
    });
    qualityRange.addEventListener('change', compressImage);
    maxWidthInput.addEventListener('change', compressImage);
    compressBtn.addEventListener('click', compressImage);
    resetBtn.addEventListener('click', function () {
        fileInput.value = '';
        toolArea.classList.add('d-none');
        currentFile = null;
        currentImage = null;
        fileInput.click();
    });
})();
</script>
@endsection
