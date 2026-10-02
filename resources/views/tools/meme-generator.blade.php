@extends('layouts.app')
@section('title', 'Meme Generator - Make Memes Online Free | Azlaan Tools')
@section('meta_description', 'Free online meme generator: upload a photo, add top and bottom text in classic meme style, and download your meme as PNG or JPG. No signup, no watermark, runs in your browser.')
@section('content')
<div class="container py-4">
    <h1 class="mb-3">Meme Generator</h1>
    <p class="lead">Upload a photo, add funny top and bottom text in classic meme style, and download your meme — free, with no watermark and no signup.</p>

    <div id="alertBox" class="alert alert-danger d-none" role="alert"></div>
    <div id="successBox" class="alert alert-success d-none" role="alert"></div>

    <div id="dropZone" class="border border-2 border-dashed rounded-3 p-4 p-md-5 text-center mb-3" style="border-style: dashed !important; cursor: pointer; background: #f8f9fa;">
        <div class="fs-1 mb-2">😂</div>
        <p class="mb-1 fw-semibold">Drag &amp; drop an image here</p>
        <p class="text-muted mb-3">or click to browse from your device (PNG, JPG, WebP, GIF)</p>
        <button type="button" class="btn btn-primary">Select Image</button>
        <input type="file" id="fileInput" accept="image/*" class="d-none">
    </div>

    <div id="editorWrap" class="d-none">
        <div class="row g-3">
            <div class="col-lg-7">
                <div class="card shadow-sm h-100">
                    <div class="card-body text-center">
                        <h2 class="h6 mb-3">Preview</h2>
                        <canvas id="memeCanvas" style="max-width: 100%; height: auto; border: 1px solid #dee2e6; border-radius: 0.375rem; background: #000;"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h2 class="h6 mb-3">Meme Text &amp; Style</h2>
                        <div class="mb-3">
                            <label for="topText" class="form-label fw-semibold">Top Text</label>
                            <input type="text" id="topText" class="form-control" placeholder="WHEN THE BILL ARRIVES" maxlength="120">
                        </div>
                        <div class="mb-3">
                            <label for="bottomText" class="form-label fw-semibold">Bottom Text</label>
                            <input type="text" id="bottomText" class="form-control" placeholder="AND IT IS DOUBLE" maxlength="120">
                        </div>
                        <div class="mb-3">
                            <label for="fontSize" class="form-label fw-semibold">Font Size: <span id="fontSizeVal">48</span> px</label>
                            <input type="range" id="fontSize" class="form-range" min="16" max="120" value="48">
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label for="textColor" class="form-label fw-semibold">Text Color</label>
                                <input type="color" id="textColor" class="form-control form-control-color w-100" value="#ffffff">
                            </div>
                            <div class="col-6">
                                <label for="strokeColor" class="form-label fw-semibold">Outline Color</label>
                                <input type="color" id="strokeColor" class="form-control form-control-color w-100" value="#000000">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="strokeSize" class="form-label fw-semibold">Outline Thickness: <span id="strokeSizeVal">8</span> px</label>
                            <input type="range" id="strokeSize" class="form-range" min="0" max="20" value="8">
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <button type="button" id="downloadPngBtn" class="btn btn-success">Download PNG</button>
                            <button type="button" id="downloadJpgBtn" class="btn btn-outline-success">Download JPG</button>
                        </div>
                    </div>
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
        <li>Type your Top Text and Bottom Text — the preview updates live in classic bold meme style.</li>
        <li>Adjust the font size, text color and outline color / thickness to your liking.</li>
        <li>Click <strong>Download PNG</strong> or <strong>Download JPG</strong> to save your meme.</li>
    </ol>
</div>
@endsection
@section('scripts')
<script>
(function () {
    var dropZone = document.getElementById('dropZone');
    var fileInput = document.getElementById('fileInput');
    var alertBox = document.getElementById('alertBox');
    var successBox = document.getElementById('successBox');
    var editorWrap = document.getElementById('editorWrap');
    var canvas = document.getElementById('memeCanvas');
    var ctx = canvas.getContext('2d');
    var topText = document.getElementById('topText');
    var bottomText = document.getElementById('bottomText');
    var fontSize = document.getElementById('fontSize');
    var fontSizeVal = document.getElementById('fontSizeVal');
    var strokeSize = document.getElementById('strokeSize');
    var strokeSizeVal = document.getElementById('strokeSizeVal');
    var textColor = document.getElementById('textColor');
    var strokeColor = document.getElementById('strokeColor');
    var baseImage = null;
    var baseName = 'meme';

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
    function wrapText(text, maxWidth) {
        var words = text.split(/\s+/).filter(function (w) { return w.length > 0; });
        var lines = [];
        var line = '';
        words.forEach(function (word) {
            var test = line ? line + ' ' + word : word;
            if (ctx.measureText(test).width > maxWidth && line) {
                lines.push(line);
                line = word;
            } else {
                line = test;
            }
        });
        if (line) {
            lines.push(line);
        }
        return lines;
    }
    function drawBlock(text, isTop) {
        if (!text || !text.trim()) {
            return;
        }
        var size = parseInt(fontSize.value, 10);
        var stroke = parseInt(strokeSize.value, 10);
        ctx.font = 'bold ' + size + 'px Impact, "Arial Black", Arial, sans-serif';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'top';
        var maxWidth = canvas.width - 24;
        var lines = wrapText(text.toUpperCase(), maxWidth);
        var lineHeight = size * 1.15;
        var startY;
        if (isTop) {
            startY = 12;
        } else {
            startY = canvas.height - 12 - (lines.length * lineHeight);
        }
        lines.forEach(function (line, i) {
            var y = startY + (i * lineHeight);
            var x = canvas.width / 2;
            if (stroke > 0) {
                ctx.lineWidth = stroke;
                ctx.strokeStyle = strokeColor.value;
                ctx.lineJoin = 'round';
                ctx.miterLimit = 2;
                ctx.strokeText(line, x, y);
            }
            ctx.fillStyle = textColor.value;
            ctx.fillText(line, x, y);
        });
    }
    function redraw() {
        if (!baseImage) {
            return;
        }
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.drawImage(baseImage, 0, 0, canvas.width, canvas.height);
        drawBlock(topText.value, true);
        drawBlock(bottomText.value, false);
        fontSizeVal.textContent = fontSize.value;
        strokeSizeVal.textContent = strokeSize.value;
    }
    function loadFile(file) {
        hideAlerts();
        if (!file) {
            return;
        }
        if (file.type && file.type.indexOf('image/') !== 0) {
            showError('Please select a valid image file.');
            return;
        }
        baseName = (file.name.replace(/\.[^.]+$/, '') || 'meme');
        var url = URL.createObjectURL(file);
        var img = new Image();
        img.onload = function () {
            var maxDim = 1200;
            var scale = Math.min(1, maxDim / Math.max(img.naturalWidth, img.naturalHeight));
            canvas.width = Math.round(img.naturalWidth * scale);
            canvas.height = Math.round(img.naturalHeight * scale);
            baseImage = img;
            var suggested = Math.max(20, Math.round(canvas.width / 12));
            fontSize.value = suggested;
            editorWrap.classList.remove('d-none');
            URL.revokeObjectURL(url);
            redraw();
            showSuccess('Image loaded. Add your text and download your meme.');
        };
        img.onerror = function () {
            URL.revokeObjectURL(url);
            showError('Could not load this image. The file may be corrupted — please try another image.');
        };
        img.src = url;
    }
    function download(format) {
        if (!baseImage) {
            showError('Upload an image first, then download your meme.');
            return;
        }
        redraw();
        var mime = format === 'jpg' ? 'image/jpeg' : 'image/png';
        var dataUrl = canvas.toDataURL(mime, 0.92);
        var a = document.createElement('a');
        a.href = dataUrl;
        a.download = baseName + '-meme.' + format;
        document.body.appendChild(a);
        a.click();
        a.remove();
        showSuccess('Your meme has been downloaded as ' + format.toUpperCase() + '.');
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

    [topText, bottomText].forEach(function (el) {
        el.addEventListener('input', redraw);
    });
    [fontSize, strokeSize].forEach(function (el) {
        el.addEventListener('input', redraw);
    });
    [textColor, strokeColor].forEach(function (el) {
        el.addEventListener('input', redraw);
    });
    document.getElementById('downloadPngBtn').addEventListener('click', function () { download('png'); });
    document.getElementById('downloadJpgBtn').addEventListener('click', function () { download('jpg'); });
})();
</script>
@endsection
