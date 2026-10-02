@extends('layouts.app')

@section('title', 'Passport Size Photo Maker - Free CNIC & Passport Photo Online | Azlaan Tools')
@section('meta_description', 'Free passport size photo maker for Pakistan CNIC and passport photos. Upload, zoom and position your photo, choose background and download a single photo or a printable sheet of 8 - no signup needed.')

@section('content')
<div class="container py-4">
    <h1 class="mb-3">Passport Size Photo Maker</h1>
    <p class="lead">Make a perfect passport or CNIC size photo in seconds. Upload your photo, drag and zoom to fit the frame, pick a background and download - free, with no signup.</p>

    <div id="alertBox" class="alert alert-danger d-none" role="alert"></div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label fw-semibold" for="fileInput">1. Upload Photo</label>
                    <input type="file" id="fileInput" class="form-control" accept="image/*">
                    <div class="form-text">JPG, PNG or WebP. A clear front-facing photo works best.</div>

                    <label class="form-label fw-semibold mt-3" for="presetSelect">2. Photo Size Preset</label>
                    <select id="presetSelect" class="form-select">
                        <option value="600x600" selected>2 x 2 inch (600 x 600 px) - CNIC / Passport</option>
                        <option value="413x531">35 x 45 mm (413 x 531 px) - Passport</option>
                        <option value="300x300">1 x 1 inch (300 x 300 px)</option>
                    </select>

                    <label class="form-label fw-semibold mt-3" for="bgSelect">3. Background</label>
                    <select id="bgSelect" class="form-select">
                        <option value="original" selected>Keep original</option>
                        <option value="white">White</option>
                        <option value="blue">Light Blue</option>
                    </select>
                    <div class="form-text">The background colour fills the canvas area behind your photo. Zoom out or move the photo to see it around the edges. For a full background change behind a person, use a photo already taken on a plain background.</div>

                    <label class="form-label fw-semibold mt-3" for="zoomRange">4. Zoom</label>
                    <input type="range" id="zoomRange" class="form-range" min="1" max="3" step="0.01" value="1">
                    <div class="form-text">Drag the photo in the preview to position your face in the centre. Scroll / use the slider to zoom.</div>

                    <div class="d-flex flex-wrap gap-2 mt-3">
                        <button type="button" id="centerBtn" class="btn btn-outline-secondary">Center Photo</button>
                        <button type="button" id="resetBtn" class="btn btn-outline-secondary">Reset Zoom</button>
                    </div>
                </div>
                <div class="col-md-6 text-center">
                    <p class="fw-semibold mb-2">Preview <span class="text-muted small" id="sizeLabel">(600 x 600 px)</span></p>
                    <div class="border rounded bg-light d-inline-block p-2">
                        <canvas id="previewCanvas" width="320" height="320" style="max-width:100%; height:auto; touch-action:none; cursor:grab; background:#fff;"></canvas>
                    </div>
                    <p class="text-muted small mt-2 mb-0" id="emptyMsg">Upload a photo to see the preview here.</p>
                </div>
            </div>
            <hr>
            <div class="d-flex flex-wrap gap-2">
                <button type="button" id="downloadBtn" class="btn btn-success btn-lg" disabled>Download Single Photo (JPG)</button>
                <button type="button" id="sheetBtn" class="btn btn-primary btn-lg" disabled>Print Sheet (8 Photos)</button>
            </div>
            <div class="form-text mt-2">Print Sheet creates an A4-size JPG with 8 copies of your photo, ready to print and cut.</div>
        </div>
            </div>

    <div class="alert alert-info">
        <strong>Privacy note:</strong> Your photo never leaves your browser - all editing happens on your own device, nothing is uploaded to any server.
    </div>

    <h2>How to use</h2>
    <ol>
        <li>Click <strong>Upload Photo</strong> and choose a clear, front-facing photo from your device.</li>
        <li>Select a size preset - 2 x 2 inch for CNIC/passport, or 35 x 45 mm.</li>
        <li>Drag the photo to position your face and use the Zoom slider to fill the frame. Choose White or Light Blue background if needed.</li>
        <li>Click <strong>Download Single Photo</strong> for one JPG, or <strong>Print Sheet (8 Photos)</strong> for a printable A4 sheet with 8 copies.</li>
    </ol>
</div>
@endsection

@section('scripts')
<script>
(function () {
    var fileInput = document.getElementById('fileInput');
    var presetSelect = document.getElementById('presetSelect');
    var bgSelect = document.getElementById('bgSelect');
    var zoomRange = document.getElementById('zoomRange');
    var canvas = document.getElementById('previewCanvas');
    var ctx = canvas.getContext('2d');
    var alertBox = document.getElementById('alertBox');
    var downloadBtn = document.getElementById('downloadBtn');
    var sheetBtn = document.getElementById('sheetBtn');
    var sizeLabel = document.getElementById('sizeLabel');
    var emptyMsg = document.getElementById('emptyMsg');

    var img = null;
    var imgUrl = null;
    var targetW = 600;
    var targetH = 600;
    var zoom = 1;
    var offsetX = 0;
    var offsetY = 0;
    var dragging = false;
    var lastX = 0;
    var lastY = 0;

    function showError(msg) {
        alertBox.textContent = msg;
        alertBox.classList.remove('d-none');
    }
    function hideError() {
        alertBox.classList.add('d-none');
    }
    function getBgColor() {
        if (bgSelect.value === 'white') return '#ffffff';
        if (bgSelect.value === 'blue') return '#add8e6';
        return null;
    }
    function updatePreset() {
        var parts = presetSelect.value.split('x');
        targetW = parseInt(parts[0], 10);
        targetH = parseInt(parts[1], 10);
        sizeLabel.textContent = '(' + targetW + ' x ' + targetH + ' px)';
        var previewW = 320;
        var previewH = Math.round(320 * targetH / targetW);
        if (previewH > 420) {
            previewH = 420;
            previewW = Math.round(420 * targetW / targetH);
        }
        canvas.width = previewW;
        canvas.height = previewH;
        draw();
    }
    function drawTo(c, w, h) {
        var cx = c.getContext('2d');
        var bg = getBgColor();
        cx.clearRect(0, 0, w, h);
        if (bg) {
            cx.fillStyle = bg;
            cx.fillRect(0, 0, w, h);
        } else {
            cx.fillStyle = '#ffffff';
            cx.fillRect(0, 0, w, h);
        }
        if (!img) return;
        var baseScale = Math.max(w / img.naturalWidth, h / img.naturalHeight);
        var scale = baseScale * zoom;
        var dw = img.naturalWidth * scale;
        var dh = img.naturalHeight * scale;
        var scaleFactor = w / canvas.width;
        var dx = (w - dw) / 2 + offsetX * scaleFactor;
        var dy = (h - dh) / 2 + offsetY * scaleFactor;
        cx.drawImage(img, dx, dy, dw, dh);
    }
    function draw() {
        drawTo(canvas, canvas.width, canvas.height);
        if (!img) {
            ctx.fillStyle = '#f8f9fa';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            ctx.fillStyle = '#6c757d';
            ctx.font = '16px sans-serif';
            ctx.textAlign = 'center';
            ctx.fillText('No photo yet', canvas.width / 2, canvas.height / 2);
        }
    }
    function renderOutput() {
        var out = document.createElement('canvas');
        out.width = targetW;
        out.height = targetH;
        drawTo(out, targetW, targetH);
        return out;
    }
    function downloadCanvas(c, filename) {
        c.toBlob(function (blob) {
            if (!blob) {
                showError('Could not create the image. Please try again.');
                return;
            }
            var url = URL.createObjectURL(blob);
            var a = document.createElement('a');
            a.href = url;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            a.remove();
            setTimeout(function () {
                URL.revokeObjectURL(url);
            }, 3000);
        }, 'image/jpeg', 0.95);
    }

    fileInput.addEventListener('change', function () {
        hideError();
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
            zoom = 1;
            offsetX = 0;
            offsetY = 0;
            zoomRange.value = '1';
            downloadBtn.disabled = false;
            sheetBtn.disabled = false;
            emptyMsg.textContent = 'Drag the photo to reposition it.';
            draw();
        };
        next.onerror = function () {
            showError('That image could not be loaded. Please try a different file.');
        };
        next.src = imgUrl;
    });

    presetSelect.addEventListener('change', updatePreset);
    bgSelect.addEventListener('change', draw);
    zoomRange.addEventListener('input', function () {
        zoom = parseFloat(zoomRange.value);
        draw();
    });
    document.getElementById('centerBtn').addEventListener('click', function () {
        offsetX = 0;
        offsetY = 0;
        draw();
    });
    document.getElementById('resetBtn').addEventListener('click', function () {
        zoom = 1;
        zoomRange.value = '1';
        offsetX = 0;
        offsetY = 0;
        draw();
    });

    function pointerPos(e) {
        var rect = canvas.getBoundingClientRect();
        var sx = canvas.width / rect.width;
        var sy = canvas.height / rect.height;
        return {
            x: (e.clientX - rect.left) * sx,
            y: (e.clientY - rect.top) * sy
        };
    }
    canvas.addEventListener('pointerdown', function (e) {
        if (!img) return;
        dragging = true;
        var p = pointerPos(e);
        lastX = p.x;
        lastY = p.y;
        canvas.style.cursor = 'grabbing';
        if (canvas.setPointerCapture) canvas.setPointerCapture(e.pointerId);
    });
    canvas.addEventListener('pointermove', function (e) {
        if (!dragging || !img) return;
        var p = pointerPos(e);
        offsetX += p.x - lastX;
        offsetY += p.y - lastY;
        lastX = p.x;
        lastY = p.y;
        draw();
    });
    function endDrag() {
        dragging = false;
        canvas.style.cursor = 'grab';
    }
    canvas.addEventListener('pointerup', endDrag);
    canvas.addEventListener('pointercancel', endDrag);
    canvas.addEventListener('wheel', function (e) {
        if (!img) return;
        e.preventDefault();
        zoom = Math.min(3, Math.max(1, zoom - e.deltaY * 0.001));
        zoomRange.value = String(zoom);
        draw();
    }, { passive: false });

    downloadBtn.addEventListener('click', function () {
        if (!img) {
            showError('Please upload a photo first.');
            return;
        }
        hideError();
        downloadCanvas(renderOutput(), 'passport-photo-' + targetW + 'x' + targetH + '.jpg');
    });

    sheetBtn.addEventListener('click', function () {
        if (!img) {
            showError('Please upload a photo first.');
            return;
        }
        hideError();
        var single = renderOutput();
        var sheetW = 2480;
        var sheetH = 3508;
        var sheet = document.createElement('canvas');
        sheet.width = sheetW;
        sheet.height = sheetH;
        var sctx = sheet.getContext('2d');
        sctx.fillStyle = '#ffffff';
        sctx.fillRect(0, 0, sheetW, sheetH);
        var margin = 120;
        var gap = 60;
        var cols = 4;
        var rows = 2;
        var cellW = (sheetW - margin * 2 - gap * (cols - 1)) / cols;
        var cellH = (sheetH - margin * 2 - gap * (rows - 1)) / rows;
        var drawW = cellW;
        var drawH = cellW * targetH / targetW;
        if (drawH > cellH) {
            drawH = cellH;
            drawW = cellH * targetW / targetH;
        }
        sctx.strokeStyle = '#cccccc';
        sctx.lineWidth = 2;
        for (var r = 0; r < rows; r++) {
            for (var col = 0; col < cols; col++) {
                var cellX = margin + col * (cellW + gap);
                var cellY = margin + r * (cellH + gap);
                var x = cellX + (cellW - drawW) / 2;
                var y = cellY + (cellH - drawH) / 2;
                sctx.drawImage(single, x, y, drawW, drawH);
                sctx.strokeRect(x, y, drawW, drawH);
            }
        }
        downloadCanvas(sheet, 'passport-photo-sheet-8.jpg');
    });

    updatePreset();
    draw();
})();
</script>
@endsection
