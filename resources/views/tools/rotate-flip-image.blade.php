@extends('layouts.app')

@section('title', 'Rotate & Flip Image Online Free | Azlaan Tools')
@section('meta_description', 'Free online tool to rotate and flip images. Rotate left or right 90 degrees, flip horizontal or vertical, and download as JPG or PNG. No signup, works in your browser.')

@section('content')
<div class="container py-4">
    <h1 class="mb-3">Rotate &amp; Flip Image</h1>
    <p class="lead">Fix sideways or mirrored photos in one click. Rotate 90 degrees left or right, flip horizontally or vertically, and download the corrected image - free, with no signup.</p>

    <div id="alertBox" class="alert alert-danger d-none" role="alert"></div>
    <div id="successBox" class="alert alert-success d-none" role="alert"></div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <label class="form-label fw-semibold" for="fileInput">Upload Image</label>
            <input type="file" id="fileInput" class="form-control" accept="image/*">

            <div id="editorWrap" class="d-none mt-3">
                <div class="text-center bg-light border rounded p-3">
                    <canvas id="previewCanvas" style="max-width:100%; height:auto;"></canvas>
                </div>
                <p class="text-muted small text-center mt-2 mb-0">Current size: <strong id="sizeLabel">-</strong> &nbsp;|&nbsp; Rotation: <strong id="rotLabel">0&deg;</strong> &nbsp;|&nbsp; Flip: <strong id="flipLabel">None</strong></p>

                <div class="d-flex flex-wrap gap-2 justify-content-center mt-3">
                    <button type="button" id="rotLeftBtn" class="btn btn-primary">Rotate Left 90&deg;</button>
                    <button type="button" id="rotRightBtn" class="btn btn-primary">Rotate Right 90&deg;</button>
                    <button type="button" id="flipHBtn" class="btn btn-outline-primary">Flip Horizontal</button>
                    <button type="button" id="flipVBtn" class="btn btn-outline-primary">Flip Vertical</button>
                    <button type="button" id="resetBtn" class="btn btn-outline-secondary">Reset</button>
                </div>

                <div class="row g-3 mt-2 align-items-end">
                    <div class="col-sm-4">
                        <label class="form-label fw-semibold" for="formatSelect">Format</label>
                        <select id="formatSelect" class="form-select">
                            <option value="image/jpeg" selected>JPG</option>
                            <option value="image/png">PNG</option>
                        </select>
                    </div>
                    <div class="col-sm-4">
                        <label class="form-label fw-semibold" for="qualityRange">JPG Quality: <span id="qualityLabel">92</span>%</label>
                        <input type="range" id="qualityRange" class="form-range" min="10" max="100" value="92">
                    </div>
                    <div class="col-sm-4">
                        <button type="button" id="downloadBtn" class="btn btn-success btn-lg w-100">Download Image</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="alert alert-info">
        <strong>Privacy note:</strong> Your image never leaves your browser - rotating and flipping happens entirely on your own device.
    </div>

    <h2>How to use</h2>
    <ol>
        <li>Click <strong>Upload Image</strong> and choose the photo you want to fix.</li>
        <li>Click <strong>Rotate Left</strong> or <strong>Rotate Right</strong> to turn it 90 degrees at a time.</li>
        <li>Use <strong>Flip Horizontal</strong> or <strong>Flip Vertical</strong> to mirror the image.</li>
        <li>Choose JPG or PNG, set quality if needed, and click <strong>Download Image</strong>.</li>
    </ol>
</div>
@endsection

@section('scripts')
<script>
(function () {
    var fileInput = document.getElementById('fileInput');
    var editorWrap = document.getElementById('editorWrap');
    var canvas = document.getElementById('previewCanvas');
    var ctx = canvas.getContext('2d');
    var alertBox = document.getElementById('alertBox');
    var successBox = document.getElementById('successBox');
    var sizeLabel = document.getElementById('sizeLabel');
    var rotLabel = document.getElementById('rotLabel');
    var flipLabel = document.getElementById('flipLabel');
    var qualityRange = document.getElementById('qualityRange');
    var qualityLabel = document.getElementById('qualityLabel');

    var img = null;
    var imgUrl = null;
    var rotation = 0;
    var flipH = false;
    var flipV = false;

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
    function isSideways() {
        var r = ((rotation % 360) + 360) % 360;
        return r === 90 || r === 270;
    }
    function renderTo(targetCanvas) {
        if (!img) return;
        var sideways = isSideways();
        var w = sideways ? img.naturalHeight : img.naturalWidth;
        var h = sideways ? img.naturalWidth : img.naturalHeight;
        targetCanvas.width = w;
        targetCanvas.height = h;
        var c = targetCanvas.getContext('2d');
        c.save();
        c.translate(w / 2, h / 2);
        c.rotate(rotation * Math.PI / 180);
        c.scale(flipH ? -1 : 1, flipV ? -1 : 1);
        c.drawImage(img, -img.naturalWidth / 2, -img.naturalHeight / 2);
        c.restore();
        return { w: w, h: h };
    }
    function draw() {
        if (!img) return;
        var res = renderTo(canvas);
        sizeLabel.textContent = res.w + ' x ' + res.h + ' px';
        var r = ((rotation % 360) + 360) % 360;
        rotLabel.textContent = r + '\u00B0';
        var flips = [];
        if (flipH) flips.push('Horizontal');
        if (flipV) flips.push('Vertical');
        flipLabel.textContent = flips.length ? flips.join(' + ') : 'None';
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
        if (imgUrl) URL.revokeObjectURL(imgUrl);
        imgUrl = URL.createObjectURL(file);
        var next = new Image();
        next.onload = function () {
            img = next;
            rotation = 0;
            flipH = false;
            flipV = false;
            editorWrap.classList.remove('d-none');
            draw();
        };
        next.onerror = function () {
            showError('That image could not be loaded. Please try a different file.');
        };
        next.src = imgUrl;
    });

    document.getElementById('rotLeftBtn').addEventListener('click', function () {
        rotation -= 90;
        draw();
    });
    document.getElementById('rotRightBtn').addEventListener('click', function () {
        rotation += 90;
        draw();
    });
    document.getElementById('flipHBtn').addEventListener('click', function () {
        flipH = !flipH;
        draw();
    });
    document.getElementById('flipVBtn').addEventListener('click', function () {
        flipV = !flipV;
        draw();
    });
    document.getElementById('resetBtn').addEventListener('click', function () {
        rotation = 0;
        flipH = false;
        flipV = false;
        draw();
    });
    qualityRange.addEventListener('input', function () {
        qualityLabel.textContent = qualityRange.value;
    });

    document.getElementById('downloadBtn').addEventListener('click', function () {
        hideAlerts();
        if (!img) {
            showError('Please upload an image first.');
            return;
        }
        var format = document.getElementById('formatSelect').value;
        var ext = format === 'image/jpeg' ? 'jpg' : 'png';
        var out = document.createElement('canvas');
        if (format === 'image/jpeg') {
            var tmp = document.createElement('canvas');
            renderTo(tmp);
            out.width = tmp.width;
            out.height = tmp.height;
            var oc = out.getContext('2d');
            oc.fillStyle = '#ffffff';
            oc.fillRect(0, 0, out.width, out.height);
            oc.drawImage(tmp, 0, 0);
        } else {
            out = document.createElement('canvas');
            var r2 = renderTo(out);
            void r2;
        }
        var quality = parseInt(qualityRange.value, 10) / 100;
        out.toBlob(function (blob) {
            if (!blob) {
                showError('Could not create the image. Please try again.');
                return;
            }
            var url = URL.createObjectURL(blob);
            var a = document.createElement('a');
            a.href = url;
            a.download = 'rotated-image.' + ext;
            document.body.appendChild(a);
            a.click();
            a.remove();
            setTimeout(function () {
                URL.revokeObjectURL(url);
            }, 3000);
            showSuccess('Done! Your image has been downloaded as ' + ext.toUpperCase() + '.');
        }, format, quality);
    });
})();
</script>
@endsection
