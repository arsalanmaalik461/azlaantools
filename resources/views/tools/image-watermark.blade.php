@extends('layouts.app')

@section('title', 'Add Watermark to Image Online Free | Azlaan Tools')
@section('meta_description', 'Free online image watermark tool. Add text or logo watermarks to photos with custom position, opacity, size and colour, with live preview. No signup, files stay in your browser.')

@section('content')
<div class="container py-4">
    <h1 class="mb-3">Image Watermark</h1>
    <p class="lead">Protect and brand your photos with a text or logo watermark. Adjust position, opacity, size and colour with a live preview, then download - free, with no signup.</p>

    <div id="alertBox" class="alert alert-danger d-none" role="alert"></div>
    <div id="successBox" class="alert alert-success d-none" role="alert"></div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-5">
                    <label class="form-label fw-semibold" for="fileInput">Main Image</label>
                    <input type="file" id="fileInput" class="form-control" accept="image/*">

                    <label class="form-label fw-semibold mt-3" for="wmText">Watermark Text</label>
                    <input type="text" id="wmText" class="form-control" value="(c) Azlaan Tools" maxlength="60">

                    <label class="form-label fw-semibold mt-3" for="positionSelect">Position</label>
                    <select id="positionSelect" class="form-select">
                        <option value="bottom-right" selected>Bottom Right</option>
                        <option value="bottom-left">Bottom Left</option>
                        <option value="top-right">Top Right</option>
                        <option value="top-left">Top Left</option>
                        <option value="center">Center</option>
                        <option value="tile">Tile (Repeated)</option>
                    </select>

                    <div class="row g-3 mt-1">
                        <div class="col-6">
                            <label class="form-label fw-semibold" for="opacityRange">Opacity: <span id="opacityLabel">60</span>%</label>
                            <input type="range" id="opacityRange" class="form-range" min="5" max="100" value="60">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold" for="sizeRange">Font Size: <span id="sizeLabel">36</span>px</label>
                            <input type="range" id="sizeRange" class="form-range" min="12" max="120" value="36">
                        </div>
                    </div>

                    <label class="form-label fw-semibold mt-2" for="colorInput">Text Colour</label>
                    <input type="color" id="colorInput" class="form-control form-control-color" value="#ffffff">

                    <label class="form-label fw-semibold mt-3" for="logoInput">Optional Logo Watermark (PNG)</label>
                    <input type="file" id="logoInput" class="form-control" accept="image/*">
                    <div class="form-text">If a logo is added, it is placed in the chosen corner at 18% of image width. Clear it with the button below.</div>
                    <button type="button" id="clearLogoBtn" class="btn btn-outline-secondary btn-sm mt-2">Remove Logo</button>

                    <div class="row g-2 mt-3 align-items-end">
                        <div class="col-5">
                            <label class="form-label fw-semibold" for="formatSelect">Format</label>
                            <select id="formatSelect" class="form-select">
                                <option value="image/png" selected>PNG</option>
                                <option value="image/jpeg">JPG</option>
                            </select>
                        </div>
                        <div class="col-7">
                            <button type="button" id="downloadBtn" class="btn btn-success w-100" disabled>Download Watermarked Image</button>
                        </div>
                    </div>
                </div>
                <div class="col-md-7">
                    <p class="fw-semibold mb-2">Live Preview</p>
                    <div class="text-center bg-light border rounded p-3">
                        <canvas id="previewCanvas" width="640" height="400" style="max-width:100%; height:auto;"></canvas>
                    </div>
                    <p class="text-muted small text-center mt-2 mb-0" id="dimLabel">Upload an image to start.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="alert alert-info">
        <strong>Privacy note:</strong> Your images never leave your browser - the watermark is added entirely on your own device, nothing is uploaded.
    </div>

    <h2>How to use</h2>
    <ol>
        <li>Upload your main image using the <strong>Main Image</strong> picker.</li>
        <li>Type your watermark text and choose its position - a corner, the centre, or a repeated tile pattern.</li>
        <li>Adjust opacity, font size and colour until the preview looks right. Optionally add a logo image as well.</li>
        <li>Choose PNG or JPG and click <strong>Download Watermarked Image</strong>.</li>
    </ol>
</div>
@endsection

@section('scripts')
<script>
(function () {
    var fileInput = document.getElementById('fileInput');
    var logoInput = document.getElementById('logoInput');
    var wmText = document.getElementById('wmText');
    var positionSelect = document.getElementById('positionSelect');
    var opacityRange = document.getElementById('opacityRange');
    var opacityLabel = document.getElementById('opacityLabel');
    var sizeRange = document.getElementById('sizeRange');
    var sizeLabel = document.getElementById('sizeLabel');
    var colorInput = document.getElementById('colorInput');
    var canvas = document.getElementById('previewCanvas');
    var ctx = canvas.getContext('2d');
    var alertBox = document.getElementById('alertBox');
    var successBox = document.getElementById('successBox');
    var dimLabel = document.getElementById('dimLabel');
    var downloadBtn = document.getElementById('downloadBtn');

    var img = null;
    var imgUrl = null;
    var logo = null;
    var logoUrl = null;

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

    function drawLogoAt(c, x, y, w, h) {
        c.drawImage(logo, x, y, w, h);
    }

    function renderTo(target) {
        if (!img) return;
        target.width = img.naturalWidth;
        target.height = img.naturalHeight;
        var c = target.getContext('2d');
        c.drawImage(img, 0, 0);
        var w = target.width;
        var h = target.height;
        var opacity = parseInt(opacityRange.value, 10) / 100;
        var text = wmText.value || '';
        var baseSize = parseInt(sizeRange.value, 10);
        var scale = Math.max(0.5, w / 1000);
        var fontSize = Math.round(baseSize * scale * 2);
        var pad = Math.round(fontSize * 0.6);

        if (logo) {
            var lw = Math.round(w * 0.18);
            var lh = Math.round(lw * logo.naturalHeight / logo.naturalWidth);
            c.save();
            c.globalAlpha = opacity;
            var pos = positionSelect.value;
            var lx = w - lw - pad;
            var ly = h - lh - pad;
            if (pos === 'top-left' || pos === 'bottom-left') lx = pad;
            if (pos === 'top-left' || pos === 'top-right') ly = pad;
            if (pos === 'center') {
                lx = (w - lw) / 2;
                ly = (h - lh) / 2;
            }
            if (pos === 'tile') {
                ly = h - lh - pad;
                lx = w - lw - pad;
            }
            drawLogoAt(c, lx, ly, lw, lh);
            c.restore();
        }

        if (text) {
            c.save();
            c.globalAlpha = opacity;
            c.font = 'bold ' + fontSize + 'px Arial, sans-serif';
            c.fillStyle = colorInput.value;
            c.shadowColor = 'rgba(0,0,0,0.45)';
            c.shadowBlur = Math.max(2, Math.round(fontSize / 12));
            c.shadowOffsetX = 1;
            c.shadowOffsetY = 1;
            var pos2 = positionSelect.value;
            if (pos2 === 'tile') {
                c.textAlign = 'center';
                c.textBaseline = 'middle';
                var tw = c.measureText(text).width;
                var stepX = tw + fontSize * 2;
                var stepY = fontSize * 4;
                c.save();
                c.translate(w / 2, h / 2);
                c.rotate(-0.35);
                var diag = Math.sqrt(w * w + h * h);
                for (var yy = -diag / 2; yy < diag / 2; yy += stepY) {
                    for (var xx = -diag / 2; xx < diag / 2; xx += stepX) {
                        c.fillText(text, xx, yy);
                    }
                }
                c.restore();
            } else {
                var tx = pad;
                var ty = pad;
                c.textBaseline = 'top';
                c.textAlign = 'left';
                if (pos2 === 'bottom-right') {
                    c.textAlign = 'right';
                    c.textBaseline = 'bottom';
                    tx = w - pad;
                    ty = h - pad;
                } else if (pos2 === 'bottom-left') {
                    c.textBaseline = 'bottom';
                    tx = pad;
                    ty = h - pad;
                } else if (pos2 === 'top-right') {
                    c.textAlign = 'right';
                    tx = w - pad;
                    ty = pad;
                } else if (pos2 === 'center') {
                    c.textAlign = 'center';
                    c.textBaseline = 'middle';
                    tx = w / 2;
                    ty = h / 2;
                } else {
                    tx = pad;
                    ty = pad;
                }
                if (logo && pos2 !== 'center') {
                    ty = pos2.indexOf('top') === 0 ? ty + fontSize + pad : ty;
                    if (pos2.indexOf('bottom') === 0) ty = ty - Math.round(w * 0.18 * logo.naturalHeight / logo.naturalWidth) - 6;
                }
                c.fillText(text, tx, ty);
            }
            c.restore();
        }
    }

    function drawPlaceholder() {
        canvas.width = 640;
        canvas.height = 400;
        ctx.fillStyle = '#f8f9fa';
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        ctx.fillStyle = '#6c757d';
        ctx.font = '18px sans-serif';
        ctx.textAlign = 'center';
        ctx.fillText('Upload an image to preview the watermark', canvas.width / 2, canvas.height / 2);
    }
    function draw() {
        if (!img) {
            drawPlaceholder();
            return;
        }
        renderTo(canvas);
        dimLabel.textContent = 'Output size: ' + img.naturalWidth + ' x ' + img.naturalHeight + ' px (preview shown at full resolution, scaled to fit)';
    }

    function loadImageInput(input, cb) {
        var file = input.files && input.files[0];
        if (!file) return null;
        if (!file.type || file.type.indexOf('image/') !== 0) {
            showError('Please choose an image file (JPG, PNG or WebP).');
            input.value = '';
            return null;
        }
        var url = URL.createObjectURL(file);
        var im = new Image();
        im.onload = function () {
            cb(im, url);
        };
        im.onerror = function () {
            URL.revokeObjectURL(url);
            showError('That image could not be loaded. Please try a different file.');
        };
        im.src = url;
        return url;
    }

    fileInput.addEventListener('change', function () {
        hideAlerts();
        loadImageInput(fileInput, function (im, url) {
            if (imgUrl) URL.revokeObjectURL(imgUrl);
            img = im;
            imgUrl = url;
            downloadBtn.disabled = false;
            draw();
        });
    });
    logoInput.addEventListener('change', function () {
        hideAlerts();
        loadImageInput(logoInput, function (im, url) {
            if (logoUrl) URL.revokeObjectURL(logoUrl);
            logo = im;
            logoUrl = url;
            draw();
        });
    });
    document.getElementById('clearLogoBtn').addEventListener('click', function () {
        logo = null;
        logoInput.value = '';
        if (logoUrl) URL.revokeObjectURL(logoUrl);
        logoUrl = null;
        draw();
    });

    var controls = [wmText, positionSelect, colorInput];
    controls.forEach(function (el) {
        el.addEventListener('input', draw);
        el.addEventListener('change', draw);
    });
    opacityRange.addEventListener('input', function () {
        opacityLabel.textContent = opacityRange.value;
        draw();
    });
    sizeRange.addEventListener('input', function () {
        sizeLabel.textContent = sizeRange.value;
        draw();
    });

    downloadBtn.addEventListener('click', function () {
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
            renderTo(out);
        }
        out.toBlob(function (blob) {
            if (!blob) {
                showError('Could not create the image. Please try again.');
                return;
            }
            var url = URL.createObjectURL(blob);
            var a = document.createElement('a');
            a.href = url;
            a.download = 'watermarked-image.' + ext;
            document.body.appendChild(a);
            a.click();
            a.remove();
            setTimeout(function () {
                URL.revokeObjectURL(url);
            }, 3000);
            showSuccess('Done! Your watermarked image has been downloaded.');
        }, format, 0.95);
    });

    drawPlaceholder();
})();
</script>
@endsection
