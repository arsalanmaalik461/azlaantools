@extends('layouts.app')
@section('title', 'Photo Censor — Blur, Pixelate & Hide Faces Free | Azlaan Tools')
@section('meta_description', 'Censor photos manually in your browser: drag boxes to pixelate, blur or black-out faces, plates and private details. Free, no signup, photo never leaves your browser.')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-2">Photo Censor <span class="badge bg-secondary fs-6 align-middle">Manual censor</span></h1>
            <p class="lead text-muted">Hide faces, number plates, names or anything private. You draw the boxes yourself — this is a manual censor tool, it does not auto-detect faces.</p>
            <div class="card shadow-sm">
                <div class="card-body">
                    <div id="dropZone" class="border rounded-3 p-4 text-center bg-light" style="border-style: dashed !important; cursor: pointer;">
                        <p class="fw-semibold mb-1">Drag &amp; drop your photo here, or click to browse</p>
                        <p class="text-muted small mb-0">JPG, PNG or WebP</p>
                        <input type="file" id="fileInput" class="d-none" accept="image/*">
                    </div>
                    <div id="errorBox" class="alert alert-danger d-none mt-3" role="alert"></div>
                    <div id="editorWrap" class="d-none mt-4">
                        <p class="fw-semibold mb-2">Censor style</p>
                        <div class="d-flex flex-wrap gap-2 mb-2">
                            <button type="button" class="btn btn-outline-primary style-btn active" data-style="pixelate">Mosaic / Pixelate</button>
                            <button type="button" class="btn btn-outline-primary style-btn" data-style="blur">Blur</button>
                            <button type="button" class="btn btn-outline-primary style-btn" data-style="black">Solid black</button>
                        </div>
                        <label class="form-label fw-semibold" for="strengthRange">Strength (pixel / blur size): <span id="strengthVal">14</span></label>
                        <input type="range" id="strengthRange" class="form-range" min="4" max="40" value="14" style="max-width:320px;">
                        <p class="text-muted small">Now drag on the photo to draw a box over each area to hide. Draw as many boxes as you need — each new box uses the style selected above.</p>
                        <div class="text-center">
                            <canvas id="canvas" class="img-fluid rounded border" style="max-width:100%; touch-action:none; cursor:crosshair;"></canvas>
                        </div>
                        <div class="d-flex flex-wrap gap-2 mt-3">
                            <button type="button" id="undoBtn" class="btn btn-outline-secondary">Undo last box</button>
                            <button type="button" id="clearBtn" class="btn btn-outline-danger">Clear all boxes</button>
                            <button type="button" id="downloadBtn" class="btn btn-success btn-lg ms-auto">Download Censored Photo</button>
                        </div>
                        <p class="small text-muted mt-2 mb-0" id="countText">0 areas censored.</p>
                    </div>
                </div>
            </div>
            <h2 class="mt-5">How to use</h2>
            <ol>
                <li>Upload a photo.</li>
                <li>Choose a style: mosaic pixelate, blur, or solid black (black is the safest — nothing can be recovered from it).</li>
                <li>Drag a rectangle over each face or detail to hide. Repeat for as many areas as you need.</li>
                <li>Use Undo if a box is wrong, then download the censored photo and share that — never the original.</li>
            </ol>
            <div class="alert alert-info mt-4"><strong>Privacy note:</strong> Your photo never leaves your browser. Censoring happens on your own device.</div>
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
    var editorWrap = document.getElementById('editorWrap');
    var canvas = document.getElementById('canvas');
    var ctx = canvas.getContext('2d');
    var strengthRange = document.getElementById('strengthRange');
    var strengthVal = document.getElementById('strengthVal');
    var countText = document.getElementById('countText');
    var srcImg = null, srcUrl = null;
    var boxes = [];
    var mode = 'pixelate';
    var drawing = false, startX = 0, startY = 0, curX = 0, curY = 0;
    function showError(m) { errorBox.textContent = m; errorBox.classList.remove('d-none'); }
    document.querySelectorAll('.style-btn').forEach(function (b) {
        b.addEventListener('click', function () {
            document.querySelectorAll('.style-btn').forEach(function (x) { x.classList.remove('active'); });
            b.classList.add('active'); mode = b.getAttribute('data-style');
        });
    });
    strengthRange.addEventListener('input', function () { strengthVal.textContent = strengthRange.value; });
    function applyBox(box) {
        var x = Math.round(box.x), y = Math.round(box.y), w = Math.round(box.w), h = Math.round(box.h);
        if (w < 4 || h < 4) return;
        if (box.style === 'black') { ctx.fillStyle = '#000000'; ctx.fillRect(x, y, w, h); return; }
        var strength = box.strength;
        if (box.style === 'pixelate') {
            var smallW = Math.max(1, Math.round(w / strength)), smallH = Math.max(1, Math.round(h / strength));
            var tmp = document.createElement('canvas'); tmp.width = smallW; tmp.height = smallH;
            var tctx = tmp.getContext('2d');
            tctx.drawImage(canvas, x, y, w, h, 0, 0, smallW, smallH);
            ctx.imageSmoothingEnabled = false;
            ctx.drawImage(tmp, 0, 0, smallW, smallH, x, y, w, h);
            ctx.imageSmoothingEnabled = true;
            return;
        }
        if (box.style === 'blur') {
            var copy = document.createElement('canvas'); copy.width = canvas.width; copy.height = canvas.height;
            copy.getContext('2d').drawImage(canvas, 0, 0);
            ctx.save();
            ctx.beginPath(); ctx.rect(x, y, w, h); ctx.clip();
            ctx.filter = 'blur(' + strength + 'px)';
            ctx.drawImage(copy, 0, 0);
            ctx.filter = 'none'; ctx.restore();
        }
    }
    function render() {
        if (!srcImg) return;
        ctx.drawImage(srcImg, 0, 0, canvas.width, canvas.height);
        boxes.forEach(applyBox);
        if (drawing) {
            ctx.strokeStyle = '#ff0000'; ctx.lineWidth = 3;
            ctx.strokeRect(startX, startY, curX - startX, curY - startY);
        }
        countText.textContent = boxes.length + ' area(s) censored.';
    }
    function pos(e) {
        var r = canvas.getBoundingClientRect();
        var clientX = e.touches ? e.touches[0].clientX : e.clientX;
        var clientY = e.touches ? e.touches[0].clientY : e.clientY;
        return { x: (clientX - r.left) * canvas.width / r.width, y: (clientY - r.top) * canvas.height / r.height };
    }
    function startDraw(e) { if (!srcImg) return; e.preventDefault(); var p = pos(e); drawing = true; startX = p.x; startY = p.y; curX = p.x; curY = p.y; }
    function moveDraw(e) { if (!drawing) return; e.preventDefault(); var p = pos(e); curX = p.x; curY = p.y; render(); }
    function endDraw(e) {
        if (!drawing) return; drawing = false;
        var x = Math.min(startX, curX), y = Math.min(startY, curY), w = Math.abs(curX - startX), h = Math.abs(curY - startY);
        if (w > 8 && h > 8) boxes.push({ x: x, y: y, w: w, h: h, style: mode, strength: parseInt(strengthRange.value, 10) });
        render();
    }
    canvas.addEventListener('mousedown', startDraw);
    canvas.addEventListener('mousemove', moveDraw);
    canvas.addEventListener('mouseup', endDraw);
    canvas.addEventListener('touchstart', startDraw, { passive: false });
    canvas.addEventListener('touchmove', moveDraw, { passive: false });
    canvas.addEventListener('touchend', endDraw);
    document.getElementById('undoBtn').addEventListener('click', function () { boxes.pop(); render(); });
    document.getElementById('clearBtn').addEventListener('click', function () { boxes = []; render(); });
    document.getElementById('downloadBtn').addEventListener('click', function () {
        drawing = false; render();
        canvas.toBlob(function (b) { var u = URL.createObjectURL(b); var a = document.createElement('a'); a.href = u; a.download = 'censored-photo.png'; document.body.appendChild(a); a.click(); a.remove(); setTimeout(function () { URL.revokeObjectURL(u); }, 3000); }, 'image/png');
    });
    function handleFile(file) {
        errorBox.classList.add('d-none'); if (!file) return;
        if (!file.type || file.type.indexOf('image/') !== 0) { showError('Please choose an image file.'); return; }
        if (srcUrl) URL.revokeObjectURL(srcUrl);
        srcUrl = URL.createObjectURL(file);
        var img = new Image();
        img.onload = function () { srcImg = img; canvas.width = img.naturalWidth; canvas.height = img.naturalHeight; boxes = []; editorWrap.classList.remove('d-none'); render(); };
        img.onerror = function () { showError('Could not read that image.'); };
        img.src = srcUrl;
    }
    dropZone.addEventListener('click', function () { fileInput.click(); });
    fileInput.addEventListener('change', function () { handleFile(fileInput.files && fileInput.files[0]); });
    dropZone.addEventListener('dragover', function (e) { e.preventDefault(); });
    dropZone.addEventListener('drop', function (e) { e.preventDefault(); if (e.dataTransfer && e.dataTransfer.files.length) handleFile(e.dataTransfer.files[0]); });
})();
</script>
@endsection
