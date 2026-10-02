@extends('layouts.app')
@section('title', 'Collage Maker — Photo Grid Collage Free | Azlaan Tools')
@section('meta_description', 'Make a photo collage free: 2 to 9 photos in grid layouts, with gap, rounded corners and background colour controls. High-res PNG download, no signup.')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-2">Collage Maker</h1>
            <p class="lead text-muted">Turn 2–9 photos into a beautiful grid collage. Choose a layout, adjust spacing and colours, then download a high-resolution PNG.</p>
            <div class="card shadow-sm">
                <div class="card-body">
                    <div id="dropZone" class="border rounded-3 p-4 text-center bg-light" style="border-style: dashed !important; cursor: pointer;">
                        <p class="fw-semibold mb-1">Drag &amp; drop 2–9 photos here, or click to browse</p>
                        <p class="text-muted small mb-0">JPG, PNG or WebP — select multiple files</p>
                        <input type="file" id="fileInput" class="d-none" accept="image/*" multiple>
                    </div>
                    <div id="errorBox" class="alert alert-danger d-none mt-3" role="alert"></div>
                    <div id="editorWrap" class="d-none mt-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold" for="layoutSelect">Layout</label>
                                <select id="layoutSelect" class="form-select">
                                    <option value="auto" selected>Auto grid (best fit)</option>
                                    <option value="2x2">2 x 2 grid</option>
                                    <option value="3x3">3 x 3 grid</option>
                                    <option value="1plus2">1 big + 2 small</option>
                                    <option value="hstrip">Horizontal strip</option>
                                    <option value="vstrip">Vertical strip</option>
                                </select>
                                <label class="form-label fw-semibold mt-3" for="gapRange">Gap: <span id="gapVal">12</span> px</label>
                                <input type="range" id="gapRange" class="form-range" min="0" max="60" value="12">
                                <label class="form-label fw-semibold mt-2" for="radiusRange">Corner radius: <span id="radiusVal">12</span> px</label>
                                <input type="range" id="radiusRange" class="form-range" min="0" max="80" value="12">
                                <label class="form-label fw-semibold mt-2" for="bgColor">Background colour</label>
                                <input type="color" id="bgColor" class="form-control form-control-color w-100" value="#ffffff">
                                <label class="form-label fw-semibold mt-3" for="sizeSelect">Export size</label>
                                <select id="sizeSelect" class="form-select">
                                    <option value="1200">1200 px wide</option>
                                    <option value="2000" selected>2000 px wide (high-res)</option>
                                    <option value="3000">3000 px wide (print)</option>
                                </select>
                                <button type="button" id="downloadBtn" class="btn btn-success btn-lg w-100 mt-4">Download Collage PNG</button>
                                <button type="button" id="clearBtn" class="btn btn-outline-secondary w-100 mt-2">Add / change photos</button>
                                <p class="small text-muted mt-2 mb-0" id="countText"></p>
                            </div>
                            <div class="col-md-8 text-center">
                                <canvas id="canvas" class="img-fluid rounded border" style="max-height:560px;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <h2 class="mt-5">How to use</h2>
            <ol>
                <li>Upload 2–9 photos at once.</li>
                <li>Pick a layout: auto grid, 2x2, 3x3, 1 big + 2 small, or strips.</li>
                <li>Adjust gap, corner radius and background colour — the preview updates instantly.</li>
                <li>Download your high-res collage PNG, ready for WhatsApp, Instagram or printing.</li>
            </ol>
            <div class="alert alert-info mt-4"><strong>Privacy note:</strong> Your photos never leave your browser. The collage is built on your own device.</div>
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
    var layoutSelect = document.getElementById('layoutSelect');
    var gapRange = document.getElementById('gapRange');
    var radiusRange = document.getElementById('radiusRange');
    var bgColor = document.getElementById('bgColor');
    var sizeSelect = document.getElementById('sizeSelect');
    var countText = document.getElementById('countText');
    var images = [];
    function showError(m) { errorBox.textContent = m; errorBox.classList.remove('d-none'); }
    function roundRectPath(x, y, w, h, r) {
        r = Math.min(r, w / 2, h / 2);
        ctx.beginPath();
        ctx.moveTo(x + r, y);
        ctx.arcTo(x + w, y, x + w, y + h, r);
        ctx.arcTo(x + w, y + h, x, y + h, r);
        ctx.arcTo(x, y + h, x, y, r);
        ctx.arcTo(x, y, x + w, y, r);
        ctx.closePath();
    }
    function drawCover(img, x, y, w, h, r) {
        ctx.save();
        roundRectPath(x, y, w, h, r); ctx.clip();
        var scale = Math.max(w / img.naturalWidth, h / img.naturalHeight);
        var dw = img.naturalWidth * scale, dh = img.naturalHeight * scale;
        ctx.drawImage(img, x + (w - dw) / 2, y + (h - dh) / 2, dw, dh);
        ctx.restore();
    }
    function gridDims(n, layout) {
        if (layout === '2x2') return { cols: 2, rows: 2 };
        if (layout === '3x3') return { cols: 3, rows: 3 };
        if (layout === 'hstrip') return { cols: n, rows: 1 };
        if (layout === 'vstrip') return { cols: 1, rows: n };
        var cols = Math.ceil(Math.sqrt(n));
        return { cols: cols, rows: Math.ceil(n / cols) };
    }
    function render() {
        if (images.length < 1) return;
        var W = parseInt(sizeSelect.value, 10);
        var gap = parseInt(gapRange.value, 10);
        var radius = parseInt(radiusRange.value, 10);
        var layout = layoutSelect.value;
        var n = images.length;
        var H, cells = [];
        if (layout === '1plus2' && n >= 3) {
            H = Math.round(W * 0.75);
            canvas.width = W; canvas.height = H;
            cells.push({ i: 0, x: gap, y: gap, w: W * 0.62 - gap * 1.5, h: H - gap * 2 });
            var sx = W * 0.62 + gap * 0.5, sw = W - sx - gap, sh = (H - gap * 3) / 2;
            cells.push({ i: 1, x: sx, y: gap, w: sw, h: sh });
            cells.push({ i: 2, x: sx, y: gap * 2 + sh, w: sw, h: sh });
            for (var e = 3; e < n; e++) { /* extra photos ignored in this layout */ }
        } else {
            var d = gridDims(n, layout);
            var cellW = (W - gap * (d.cols + 1)) / d.cols;
            var cellH = layout === 'hstrip' || layout === 'vstrip' ? cellW : cellW * 0.85;
            if (layout === 'vstrip') { cellH = cellW * 0.85; }
            H = Math.round(cellH * d.rows + gap * (d.rows + 1));
            canvas.width = W; canvas.height = H;
            for (var idx = 0; idx < n; idx++) {
                var col = idx % d.cols, row = Math.floor(idx / d.cols);
                cells.push({ i: idx, x: gap + col * (cellW + gap), y: gap + row * (cellH + gap), w: cellW, h: cellH });
            }
        }
        ctx.fillStyle = bgColor.value; ctx.fillRect(0, 0, canvas.width, canvas.height);
        cells.forEach(function (c) { if (images[c.i]) drawCover(images[c.i], c.x, c.y, c.w, c.h, radius); });
        countText.textContent = cells.length + ' of ' + images.length + ' photo(s) shown in this layout • Output ' + canvas.width + ' x ' + canvas.height + ' px';
    }
    function handleFiles(files) {
        errorBox.classList.add('d-none');
        var list = Array.prototype.slice.call(files || []).filter(function (f) { return f.type && f.type.indexOf('image/') === 0; }).slice(0, 9);
        if (!list.length) { showError('Please choose image files for the collage.'); return; }
        // Load into indexed slots so selection order is kept, and only count images
        // that actually decode — a broken image left in the array makes drawCover
        // divide by naturalWidth 0 and breaks the whole render.
        var slots = new Array(list.length);
        var settled = 0, failed = 0;
        function done() {
            settled++;
            if (settled !== list.length) return;
            images = slots.filter(function (im) { return !!im; });
            if (images.length < 2) {
                showError(failed > 0 ? 'Only ' + images.length + ' photo(s) could be read — please choose at least 2 readable photos.' : 'Please choose at least 2 photos for a collage.');
                return;
            }
            if (failed > 0) showError(failed + ' photo(s) could not be read and were skipped.');
            editorWrap.classList.remove('d-none');
            render();
        }
        list.forEach(function (file, idx) {
            var url = URL.createObjectURL(file);
            var img = new Image();
            img.onload = function () { slots[idx] = img; done(); };
            img.onerror = function () { failed++; URL.revokeObjectURL(url); done(); };
            img.src = url;
        });
    }
    [layoutSelect, sizeSelect].forEach(function (el) { el.addEventListener('change', render); });
    [gapRange, radiusRange].forEach(function (el) { el.addEventListener('input', function () { document.getElementById('gapVal').textContent = gapRange.value; document.getElementById('radiusVal').textContent = radiusRange.value; render(); }); });
    bgColor.addEventListener('input', render);
    document.getElementById('clearBtn').addEventListener('click', function () { fileInput.click(); });
    document.getElementById('downloadBtn').addEventListener('click', function () {
        canvas.toBlob(function (b) { var u = URL.createObjectURL(b); var a = document.createElement('a'); a.href = u; a.download = 'collage.png'; document.body.appendChild(a); a.click(); a.remove(); setTimeout(function () { URL.revokeObjectURL(u); }, 3000); }, 'image/png');
    });
    dropZone.addEventListener('click', function () { fileInput.click(); });
    fileInput.addEventListener('change', function () { handleFiles(fileInput.files); });
    dropZone.addEventListener('dragover', function (e) { e.preventDefault(); });
    dropZone.addEventListener('drop', function (e) { e.preventDefault(); if (e.dataTransfer) handleFiles(e.dataTransfer.files); });
})();
</script>
@endsection
