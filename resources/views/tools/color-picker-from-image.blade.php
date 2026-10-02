@extends('layouts.app')
@section('title', 'Color Picker from Image - Pick HEX, RGB, HSL Colors | Azlaan Tools')
@section('meta_description', 'Free color picker from image: upload any photo and click a pixel to get its HEX, RGB and HSL color codes instantly. No signup, no upload — everything runs in your browser.')
@section('content')
<div class="container py-4">
    <h1 class="mb-3">Color Picker from Image</h1>
    <p class="lead">Upload any image, click on any pixel and instantly get its exact color in HEX, RGB and HSL — free and private, right in your browser.</p>

    <div id="alertBox" class="alert alert-danger d-none" role="alert"></div>
    <div id="successBox" class="alert alert-success d-none" role="alert"></div>

    <div id="dropZone" class="border border-2 border-dashed rounded-3 p-4 p-md-5 text-center mb-3" style="border-style: dashed !important; cursor: pointer; background: #f8f9fa;">
        <div class="fs-1 mb-2">🎨</div>
        <p class="mb-1 fw-semibold">Drag &amp; drop an image here</p>
        <p class="text-muted mb-3">or click to browse from your device (PNG, JPG, WebP, GIF)</p>
        <button type="button" class="btn btn-primary">Select Image</button>
        <input type="file" id="fileInput" accept="image/*" class="d-none">
    </div>

    <div id="canvasWrap" class="d-none">
        <div class="card shadow-sm mb-3">
            <div class="card-body text-center">
                <p class="text-muted small mb-2">Click or tap anywhere on the image to pick a color. Image: <span id="imageInfo" class="fw-semibold"></span></p>
                <canvas id="pickerCanvas" style="max-width: 100%; height: auto; cursor: crosshair; border: 1px solid #dee2e6; border-radius: 0.375rem; touch-action: none;"></canvas>
            </div>
        </div>

        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <h2 class="h5 mb-3">Picked Color</h2>
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div id="swatch" style="width: 72px; height: 72px; border-radius: 0.5rem; border: 1px solid #adb5bd; background: #ffffff;"></div>
                    <div>
                        <div class="fw-bold fs-5" id="pickedHex">—</div>
                        <div class="text-muted small" id="pickedPos">Click the image to pick a color</div>
                    </div>
                </div>
                <div class="row g-2">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold" for="hexOut">HEX</label>
                        <div class="input-group">
                            <input type="text" id="hexOut" class="form-control" readonly value="">
                            <button type="button" class="btn btn-outline-primary copy-btn" data-target="hexOut">Copy</button>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold" for="rgbOut">RGB</label>
                        <div class="input-group">
                            <input type="text" id="rgbOut" class="form-control" readonly value="">
                            <button type="button" class="btn btn-outline-primary copy-btn" data-target="rgbOut">Copy</button>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold" for="hslOut">HSL</label>
                        <div class="input-group">
                            <input type="text" id="hslOut" class="form-control" readonly value="">
                            <button type="button" class="btn btn-outline-primary copy-btn" data-target="hslOut">Copy</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="h5 mb-2">Recent Colors (last 8)</h2>
                <p class="text-muted small">Click any swatch below to view that color again.</p>
                <div id="palette" class="d-flex flex-wrap gap-2">
                    <span class="text-muted small" id="paletteEmpty">No colors picked yet.</span>
                </div>
            </div>
        </div>
    </div>

    <div class="alert alert-info mt-4">
        <strong>Privacy note:</strong> Your files never leave your browser — everything happens on your phone or computer, nothing is uploaded.
    </div>

    <h2>How to use</h2>
    <ol>
        <li>Click the box above or drag and drop an image into it.</li>
        <li>Click or tap any point on the displayed image to pick that pixel's color.</li>
        <li>Read the HEX, RGB and HSL values and use the Copy buttons to copy any format.</li>
        <li>Your last 8 picked colors stay in the palette — click one to see its values again.</li>
    </ol>
</div>
@endsection
@section('scripts')
<script>
(function () {
    var dropZone = document.getElementById('dropZone');
    var fileInput = document.getElementById('fileInput');
    var canvasWrap = document.getElementById('canvasWrap');
    var canvas = document.getElementById('pickerCanvas');
    var ctx = canvas.getContext('2d', { willReadFrequently: true });
    var alertBox = document.getElementById('alertBox');
    var successBox = document.getElementById('successBox');
    var swatch = document.getElementById('swatch');
    var pickedHex = document.getElementById('pickedHex');
    var pickedPos = document.getElementById('pickedPos');
    var hexOut = document.getElementById('hexOut');
    var rgbOut = document.getElementById('rgbOut');
    var hslOut = document.getElementById('hslOut');
    var paletteEl = document.getElementById('palette');
    var imageInfo = document.getElementById('imageInfo');
    var imageLoaded = false;
    var palette = [];

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
    function toHex2(n) {
        var s = n.toString(16);
        return s.length === 1 ? '0' + s : s;
    }
    function rgbToHex(r, g, b) {
        return ('#' + toHex2(r) + toHex2(g) + toHex2(b)).toUpperCase();
    }
    function rgbToHsl(r, g, b) {
        var rn = r / 255, gn = g / 255, bn = b / 255;
        var max = Math.max(rn, gn, bn), min = Math.min(rn, gn, bn);
        var h = 0, s = 0, l = (max + min) / 2;
        if (max !== min) {
            var d = max - min;
            s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
            if (max === rn) {
                h = (gn - bn) / d + (gn < bn ? 6 : 0);
            } else if (max === gn) {
                h = (bn - rn) / d + 2;
            } else {
                h = (rn - gn) / d + 4;
            }
            h = h * 60;
        }
        return { h: Math.round(h), s: Math.round(s * 100), l: Math.round(l * 100) };
    }
    function showColor(r, g, b) {
        var hex = rgbToHex(r, g, b);
        var hsl = rgbToHsl(r, g, b);
        var rgbStr = 'rgb(' + r + ', ' + g + ', ' + b + ')';
        var hslStr = 'hsl(' + hsl.h + ', ' + hsl.s + '%, ' + hsl.l + '%)';
        swatch.style.background = hex;
        pickedHex.textContent = hex;
        hexOut.value = hex;
        rgbOut.value = rgbStr;
        hslOut.value = hslStr;
        return hex;
    }
    function addToPalette(hex) {
        var idx = palette.indexOf(hex);
        if (idx !== -1) {
            palette.splice(idx, 1);
        }
        palette.unshift(hex);
        if (palette.length > 8) {
            palette.length = 8;
        }
        renderPalette();
    }
    function renderPalette() {
        paletteEl.innerHTML = '';
        if (palette.length === 0) {
            var empty = document.createElement('span');
            empty.className = 'text-muted small';
            empty.textContent = 'No colors picked yet.';
            paletteEl.appendChild(empty);
            return;
        }
        palette.forEach(function (hex) {
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'border rounded';
            btn.title = hex;
            btn.style.width = '52px';
            btn.style.height = '52px';
            btn.style.background = hex;
            btn.style.cursor = 'pointer';
            btn.setAttribute('aria-label', 'Color ' + hex);
            btn.addEventListener('click', function () {
                var r = parseInt(hex.substring(1, 3), 16);
                var g = parseInt(hex.substring(3, 5), 16);
                var b = parseInt(hex.substring(5, 7), 16);
                showColor(r, g, b);
                pickedPos.textContent = 'From palette';
            });
            var wrap = document.createElement('div');
            wrap.className = 'text-center';
            wrap.appendChild(btn);
            var label = document.createElement('div');
            label.className = 'small text-muted';
            label.style.fontSize = '0.7rem';
            label.textContent = hex;
            wrap.appendChild(label);
            paletteEl.appendChild(wrap);
        });
    }
    function pickAt(clientX, clientY) {
        if (!imageLoaded) {
            return;
        }
        var rect = canvas.getBoundingClientRect();
        if (rect.width === 0 || rect.height === 0) {
            return;
        }
        var x = Math.floor((clientX - rect.left) * (canvas.width / rect.width));
        var y = Math.floor((clientY - rect.top) * (canvas.height / rect.height));
        if (x < 0) { x = 0; }
        if (y < 0) { y = 0; }
        if (x >= canvas.width) { x = canvas.width - 1; }
        if (y >= canvas.height) { y = canvas.height - 1; }
        try {
            var data = ctx.getImageData(x, y, 1, 1).data;
            var hex = showColor(data[0], data[1], data[2]);
            pickedPos.textContent = 'Pixel x: ' + x + ', y: ' + y;
            addToPalette(hex);
        } catch (err) {
            console.error(err);
            showError('Could not read the color from this image. Please try a different image.');
        }
    }
    function loadFile(file) {
        hideAlerts();
        if (!file) {
            return;
        }
        if (file.type && file.type.indexOf('image/') !== 0) {
            showError('Please select a valid image file (PNG, JPG, WebP or GIF).');
            return;
        }
        var url = URL.createObjectURL(file);
        var img = new Image();
        img.onload = function () {
            canvas.width = img.naturalWidth;
            canvas.height = img.naturalHeight;
            ctx.drawImage(img, 0, 0);
            imageLoaded = true;
            imageInfo.textContent = file.name + ' — ' + img.naturalWidth + ' x ' + img.naturalHeight + ' px';
            canvasWrap.classList.remove('d-none');
            URL.revokeObjectURL(url);
            showSuccess('Image loaded. Click anywhere on it to pick a color.');
        };
        img.onerror = function () {
            URL.revokeObjectURL(url);
            showError('Could not load this image. The file may be corrupted — please try another image.');
        };
        img.src = url;
    }

    dropZone.addEventListener('click', function () { fileInput.click(); });
    fileInput.addEventListener('change', function () {
        if (fileInput.files && fileInput.files[0]) {
            loadFile(fileInput.files[0]);
        }
        fileInput.value = '';
    });
    dropZone.addEventListener('dragover', function (e) {
        e.preventDefault();
        dropZone.style.background = '#e9f2ff';
    });
    dropZone.addEventListener('dragleave', function () {
        dropZone.style.background = '#f8f9fa';
    });
    dropZone.addEventListener('drop', function (e) {
        e.preventDefault();
        dropZone.style.background = '#f8f9fa';
        if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files[0]) {
            loadFile(e.dataTransfer.files[0]);
        }
    });
    canvas.addEventListener('click', function (e) {
        pickAt(e.clientX, e.clientY);
    });
    canvas.addEventListener('touchstart', function (e) {
        if (e.touches && e.touches[0]) {
            e.preventDefault();
            pickAt(e.touches[0].clientX, e.touches[0].clientY);
        }
    }, { passive: false });

    document.querySelectorAll('.copy-btn').forEach(function (btn) {
        btn.addEventListener('click', async function () {
            var target = document.getElementById(btn.getAttribute('data-target'));
            if (!target || !target.value) {
                showError('Pick a color from the image first, then copy it.');
                return;
            }
            try {
                await navigator.clipboard.writeText(target.value);
            } catch (err) {
                target.select();
                document.execCommand('copy');
            }
            var old = btn.textContent;
            btn.textContent = 'Copied!';
            setTimeout(function () { btn.textContent = old; }, 1500);
        });
    });
})();
</script>
@endsection
