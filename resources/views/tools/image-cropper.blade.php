@extends('layouts.app')

@section('title', 'Image Cropper - Crop Images Online Free | Azlaan Tools')
@section('meta_description', 'Free online image cropper. Crop JPG, PNG and WebP images to 1:1, 4:3, 16:9, CNIC 2:3 and more, rotate and download instantly. No signup, files never leave your browser.')

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.css">
<style>
.crop-wrap { max-height: 520px; }
.crop-wrap img { max-width: 100%; display: block; }
</style>
@endsection

@section('content')
<div class="container py-4">
    <h1 class="mb-3">Image Cropper</h1>
    <p class="lead">Crop any image to the exact size or ratio you need - for CNIC, profile pictures, social media and more. Free and private, with no signup.</p>

    <div id="alertBox" class="alert alert-danger d-none" role="alert"></div>
    <div id="successBox" class="alert alert-success d-none" role="alert"></div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <label class="form-label fw-semibold" for="fileInput">Upload Image</label>
            <input type="file" id="fileInput" class="form-control" accept="image/*">

            <div id="editorWrap" class="d-none mt-3">
                <div class="crop-wrap border rounded bg-dark">
                    <img id="cropImage" alt="Image to crop">
                </div>

                <p class="fw-semibold mt-3 mb-2">Aspect Ratio</p>
                <div class="d-flex flex-wrap gap-2">
                    <button type="button" class="btn btn-outline-primary btn-sm ratio-btn active" data-ratio="free">Free</button>
                    <button type="button" class="btn btn-outline-primary btn-sm ratio-btn" data-ratio="1">1:1</button>
                    <button type="button" class="btn btn-outline-primary btn-sm ratio-btn" data-ratio="1.3333">4:3</button>
                    <button type="button" class="btn btn-outline-primary btn-sm ratio-btn" data-ratio="1.5">3:2</button>
                    <button type="button" class="btn btn-outline-primary btn-sm ratio-btn" data-ratio="1.7777">16:9</button>
                    <button type="button" class="btn btn-outline-primary btn-sm ratio-btn" data-ratio="0.6666">2:3 (CNIC)</button>
                </div>

                <div class="d-flex flex-wrap gap-2 mt-3">
                    <button type="button" id="rotateLeftBtn" class="btn btn-outline-secondary btn-sm">Rotate Left 90&deg;</button>
                    <button type="button" id="rotateRightBtn" class="btn btn-outline-secondary btn-sm">Rotate Right 90&deg;</button>
                    <button type="button" id="zoomInBtn" class="btn btn-outline-secondary btn-sm">Zoom In</button>
                    <button type="button" id="zoomOutBtn" class="btn btn-outline-secondary btn-sm">Zoom Out</button>
                    <button type="button" id="resetBtn" class="btn btn-outline-secondary btn-sm">Reset</button>
                </div>

                <div class="row g-3 mt-2 align-items-end">
                    <div class="col-sm-4">
                        <label class="form-label fw-semibold" for="formatSelect">Output Format</label>
                        <select id="formatSelect" class="form-select">
                            <option value="image/png" selected>PNG</option>
                            <option value="image/jpeg">JPG</option>
                        </select>
                    </div>
                    <div class="col-sm-8">
                        <button type="button" id="cropBtn" class="btn btn-success btn-lg w-100">Crop &amp; Download</button>
                    </div>
                </div>
                <p class="text-muted small mt-2 mb-0">Cropped size: <strong id="cropSizeLabel">-</strong></p>
            </div>
        </div>
    </div>

    <div class="alert alert-info">
        <strong>Privacy note:</strong> Your image never leaves your browser - cropping happens entirely on your own device, nothing is uploaded.
    </div>

    <h2>How to use</h2>
    <ol>
        <li>Click <strong>Upload Image</strong> and choose a JPG, PNG or WebP image.</li>
        <li>Drag the corners of the crop box to select the area you want to keep.</li>
        <li>Use the ratio buttons (1:1, 16:9, 2:3 for CNIC and more) or rotate the image if needed.</li>
        <li>Choose PNG or JPG and click <strong>Crop &amp; Download</strong> to save your cropped image.</li>
    </ol>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.js"></script>
<script>
(function () {
    var fileInput = document.getElementById('fileInput');
    var cropImage = document.getElementById('cropImage');
    var editorWrap = document.getElementById('editorWrap');
    var alertBox = document.getElementById('alertBox');
    var successBox = document.getElementById('successBox');
    var cropSizeLabel = document.getElementById('cropSizeLabel');
    var cropper = null;
    var currentUrl = null;

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

    fileInput.addEventListener('change', function () {
        hideAlerts();
        var file = fileInput.files && fileInput.files[0];
        if (!file) return;
        if (!file.type || file.type.indexOf('image/') !== 0) {
            showError('Please choose an image file (JPG, PNG or WebP).');
            fileInput.value = '';
            return;
        }
        if (typeof Cropper === 'undefined') {
            showError('Cropper library failed to load. Please check your internet connection and refresh the page.');
            return;
        }
        if (currentUrl) URL.revokeObjectURL(currentUrl);
        currentUrl = URL.createObjectURL(file);
        // Reset the input so picking the SAME file again later still fires
        // 'change' (browsers suppress it when the value is unchanged).
        fileInput.value = '';
        if (cropper) {
            cropper.destroy();
            cropper = null;
        }
        cropImage.src = currentUrl;
        editorWrap.classList.remove('d-none');
        cropImage.onload = function () {
            cropper = new Cropper(cropImage, {
                viewMode: 1,
                autoCropArea: 0.9,
                responsive: true,
                background: false,
                crop: function (event) {
                    var d = event.detail;
                    cropSizeLabel.textContent = Math.round(d.width) + ' x ' + Math.round(d.height) + ' px';
                }
            });
        };
    });

    document.querySelectorAll('.ratio-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            if (!cropper) return;
            document.querySelectorAll('.ratio-btn').forEach(function (b) {
                b.classList.remove('active');
            });
            btn.classList.add('active');
            var val = btn.getAttribute('data-ratio');
            if (val === 'free') {
                cropper.setAspectRatio(NaN);
            } else {
                cropper.setAspectRatio(parseFloat(val));
            }
        });
    });

    document.getElementById('rotateLeftBtn').addEventListener('click', function () {
        if (cropper) cropper.rotate(-90);
    });
    document.getElementById('rotateRightBtn').addEventListener('click', function () {
        if (cropper) cropper.rotate(90);
    });
    document.getElementById('zoomInBtn').addEventListener('click', function () {
        if (cropper) cropper.zoom(0.1);
    });
    document.getElementById('zoomOutBtn').addEventListener('click', function () {
        if (cropper) cropper.zoom(-0.1);
    });
    document.getElementById('resetBtn').addEventListener('click', function () {
        if (cropper) cropper.reset();
    });

    document.getElementById('cropBtn').addEventListener('click', function () {
        hideAlerts();
        if (!cropper) {
            showError('Please upload an image first.');
            return;
        }
        var format = document.getElementById('formatSelect').value;
        var ext = format === 'image/jpeg' ? 'jpg' : 'png';
        var outCanvas = cropper.getCroppedCanvas({
            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high',
            fillColor: format === 'image/jpeg' ? '#ffffff' : undefined
        });
        if (!outCanvas) {
            showError('Could not crop this image. Please try again.');
            return;
        }
        outCanvas.toBlob(function (blob) {
            if (!blob) {
                showError('Could not create the cropped image. Please try again.');
                return;
            }
            var url = URL.createObjectURL(blob);
            var a = document.createElement('a');
            a.href = url;
            a.download = 'cropped-image.' + ext;
            document.body.appendChild(a);
            a.click();
            a.remove();
            setTimeout(function () {
                URL.revokeObjectURL(url);
            }, 3000);
            showSuccess('Done! Your cropped image (' + outCanvas.width + ' x ' + outCanvas.height + ' px) has been downloaded.');
        }, format, 0.95);
    });
})();
</script>
@endsection
