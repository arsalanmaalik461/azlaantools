@extends('layouts.app')
@section('title', 'Photo Editor — Filters, Brightness & Effects Free | Azlaan Tools')
@section('meta_description', 'Edit photos free in your browser: brightness, contrast, saturation, hue, blur, sharpen, preset filters and before/after compare. No signup, photo never leaves your browser.')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-2">Photo Editor</h1>
            <p class="lead text-muted">Fix and style any photo in seconds — sliders, preset filters and a before/after compare. Pure browser editing, no upload.</p>
            <div class="card shadow-sm">
                <div class="card-body">
                    <div id="dropZone" class="border rounded-3 p-4 text-center bg-light" style="border-style: dashed !important; cursor: pointer;">
                        <p class="fw-semibold mb-1">Drag &amp; drop your photo here, or click to browse</p>
                        <p class="text-muted small mb-0">JPG, PNG or WebP</p>
                        <input type="file" id="fileInput" class="d-none" accept="image/*">
                    </div>
                    <div id="errorBox" class="alert alert-danger d-none mt-3" role="alert"></div>
                    <div id="editorWrap" class="d-none mt-4">
                        <p class="fw-semibold mb-2">Preset filters</p>
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <button type="button" class="btn btn-outline-primary preset-btn active" data-preset="normal">Normal</button>
                            <button type="button" class="btn btn-outline-primary preset-btn" data-preset="bw">B&amp;W</button>
                            <button type="button" class="btn btn-outline-primary preset-btn" data-preset="vintage">Vintage</button>
                            <button type="button" class="btn btn-outline-primary preset-btn" data-preset="warm">Warm</button>
                            <button type="button" class="btn btn-outline-primary preset-btn" data-preset="cool">Cool</button>
                            <button type="button" class="btn btn-outline-primary preset-btn" data-preset="vivid">Vivid</button>
                        </div>
                        <div class="row g-4">
                            <div class="col-md-7 text-center">
                                <canvas id="canvas" class="img-fluid rounded border" style="max-height:480px;"></canvas>
                                <div class="form-check form-switch mt-3 d-inline-block">
                                    <input class="form-check-input" type="checkbox" id="compareToggle">
                                    <label class="form-check-label" for="compareToggle">Hold / toggle to see original (before)</label>
                                </div>
                                <img id="origPreview" class="img-fluid rounded border d-none mt-2" alt="Original" style="max-height:480px;">
                            </div>
                            <div class="col-md-5">
                                <div id="sliders"></div>
                                <div class="d-flex flex-wrap gap-3 mt-2">
                                    <div class="form-check"><input class="form-check-input fx-toggle" type="checkbox" id="fxGray"><label class="form-check-label" for="fxGray">Grayscale</label></div>
                                    <div class="form-check"><input class="form-check-input fx-toggle" type="checkbox" id="fxSepia"><label class="form-check-label" for="fxSepia">Sepia</label></div>
                                    <div class="form-check"><input class="form-check-input fx-toggle" type="checkbox" id="fxInvert"><label class="form-check-label" for="fxInvert">Invert</label></div>
                                </div>
                                <div class="d-grid gap-2 mt-4">
                                    <button type="button" id="resetBtn" class="btn btn-outline-secondary">Reset all</button>
                                    <button type="button" id="downloadPngBtn" class="btn btn-success btn-lg">Download PNG</button>
                                    <button type="button" id="downloadJpgBtn" class="btn btn-primary">Download JPG</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <h2 class="mt-5">How to use</h2>
            <ol>
                <li>Upload a photo.</li>
                <li>Tap a preset filter, or move the sliders: brightness, contrast, saturation, hue, exposure, blur and sharpen.</li>
                <li>Toggle Grayscale, Sepia or Invert for extra effects. Use the compare toggle to see the original.</li>
                <li>Download as PNG (best quality) or JPG (smaller file).</li>
            </ol>
            <div class="alert alert-info mt-4"><strong>Privacy note:</strong> Your photo never leaves your browser. Editing uses Canvas on your own device.</div>
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
    var origPreview = document.getElementById('origPreview');
    var compareToggle = document.getElementById('compareToggle');
    var slidersWrap = document.getElementById('sliders');
    var defs = [
        { id: 'brightness', label: 'Brightness', min: 0, max: 200, val: 100 },
        { id: 'contrast', label: 'Contrast', min: 0, max: 200, val: 100 },
        { id: 'saturate', label: 'Saturation', min: 0, max: 300, val: 100 },
        { id: 'hue', label: 'Hue rotate', min: -180, max: 180, val: 0 },
        { id: 'exposure', label: 'Exposure', min: 20, max: 200, val: 100 },
        { id: 'blur', label: 'Blur', min: 0, max: 10, val: 0 },
        { id: 'sharpen', label: 'Sharpen', min: 0, max: 100, val: 0 }
    ];
    var state = {};
    defs.forEach(function (d) {
        state[d.id] = d.val;
        var wrap = document.createElement('div'); wrap.className = 'mb-3';
        wrap.innerHTML = '<label class="form-label d-flex justify-content-between"><span>' + d.label + '</span><strong id="val-' + d.id + '">' + d.val + '</strong></label>';
        var input = document.createElement('input');
        input.type = 'range'; input.className = 'form-range slider'; input.id = 'sl-' + d.id;
        input.min = d.min; input.max = d.max; input.value = d.val;
        input.addEventListener('input', function () { state[d.id] = parseFloat(input.value); document.getElementById('val-' + d.id).textContent = input.value; render(); });
        wrap.appendChild(input); slidersWrap.appendChild(wrap);
    });
    var srcImg = null, srcUrl = null;
    function showError(m) { errorBox.textContent = m; errorBox.classList.remove('d-none'); }
    function handleFile(file) {
        errorBox.classList.add('d-none'); if (!file) return;
        if (!file.type || file.type.indexOf('image/') !== 0) { showError('Please choose an image file.'); return; }
        if (srcUrl) URL.revokeObjectURL(srcUrl);
        srcUrl = URL.createObjectURL(file);
        var img = new Image();
        img.onload = function () { srcImg = img; canvas.width = img.naturalWidth; canvas.height = img.naturalHeight; origPreview.src = srcUrl; editorWrap.classList.remove('d-none'); render(); };
        img.onerror = function () { showError('Could not read that image.'); };
        img.src = srcUrl;
    }
    function applySharpen(amount) {
        if (amount <= 0) return;
        var w = canvas.width, h = canvas.height;
        var src = ctx.getImageData(0, 0, w, h);
        var out = ctx.createImageData(w, h);
        var s = src.data, o = out.data;
        var k = amount / 100 * 2;
        var kernel = [0, -k, 0, -k, 1 + 4 * k, -k, 0, -k, 0];
        for (var y = 0; y < h; y++) {
            for (var x = 0; x < w; x++) {
                var idx = (y * w + x) * 4;
                for (var ch = 0; ch < 3; ch++) {
                    var sum = 0;
                    for (var ky = -1; ky <= 1; ky++) {
                        for (var kx = -1; kx <= 1; kx++) {
                            var px = Math.min(w - 1, Math.max(0, x + kx)), py = Math.min(h - 1, Math.max(0, y + ky));
                            sum += s[(py * w + px) * 4 + ch] * kernel[(ky + 1) * 3 + (kx + 1)];
                        }
                    }
                    o[idx + ch] = Math.max(0, Math.min(255, sum));
                }
                o[idx + 3] = s[idx + 3];
            }
        }
        ctx.putImageData(out, 0, 0);
    }
    function render() {
        if (!srcImg) return;
        var parts = [];
        parts.push('brightness(' + state.brightness + '%)');
        parts.push('contrast(' + state.contrast + '%)');
        parts.push('saturate(' + state.saturate + '%)');
        parts.push('hue-rotate(' + state.hue + 'deg)');
        // Exposure approximated with brightness gamma-like boost combined below via filter brightness already; use extra brightness factor
        parts.push('brightness(' + (state.exposure / 100) + ')');
        if (state.blur > 0) parts.push('blur(' + state.blur + 'px)');
        if (document.getElementById('fxGray').checked) parts.push('grayscale(100%)');
        if (document.getElementById('fxSepia').checked) parts.push('sepia(80%)');
        if (document.getElementById('fxInvert').checked) parts.push('invert(100%)');
        ctx.filter = parts.join(' ');
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.drawImage(srcImg, 0, 0);
        ctx.filter = 'none';
        if (state.sharpen > 0 && canvas.width * canvas.height <= 6000000) applySharpen(state.sharpen);
    }
    var presets = {
        normal: { brightness: 100, contrast: 100, saturate: 100, hue: 0, exposure: 100, blur: 0, sharpen: 0, gray: false, sepia: false, invert: false },
        bw: { brightness: 105, contrast: 115, saturate: 0, hue: 0, exposure: 100, blur: 0, sharpen: 10, gray: true, sepia: false, invert: false },
        vintage: { brightness: 110, contrast: 90, saturate: 70, hue: 0, exposure: 105, blur: 0, sharpen: 0, gray: false, sepia: true, invert: false },
        warm: { brightness: 105, contrast: 105, saturate: 120, hue: -10, exposure: 105, blur: 0, sharpen: 0, gray: false, sepia: false, invert: false },
        cool: { brightness: 100, contrast: 105, saturate: 110, hue: 15, exposure: 100, blur: 0, sharpen: 0, gray: false, sepia: false, invert: false },
        vivid: { brightness: 105, contrast: 120, saturate: 180, hue: 0, exposure: 100, blur: 0, sharpen: 20, gray: false, sepia: false, invert: false }
    };
    document.querySelectorAll('.preset-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.preset-btn').forEach(function (b) { b.classList.remove('active'); });
            btn.classList.add('active');
            var p = presets[btn.getAttribute('data-preset')]; if (!p) return;
            defs.forEach(function (d) { state[d.id] = p[d.id]; var el = document.getElementById('sl-' + d.id); if (el) el.value = p[d.id]; var v = document.getElementById('val-' + d.id); if (v) v.textContent = p[d.id]; });
            document.getElementById('fxGray').checked = p.gray;
            document.getElementById('fxSepia').checked = p.sepia;
            document.getElementById('fxInvert').checked = p.invert;
            render();
        });
    });
    document.querySelectorAll('.fx-toggle').forEach(function (el) { el.addEventListener('change', render); });
    compareToggle.addEventListener('change', function () {
        if (compareToggle.checked) { canvas.classList.add('d-none'); origPreview.classList.remove('d-none'); }
        else { canvas.classList.remove('d-none'); origPreview.classList.add('d-none'); }
    });
    document.getElementById('resetBtn').addEventListener('click', function () {
        document.querySelector('[data-preset="normal"]').click();
    });
    function download(type) {
        if (!srcImg) return;
        var src = canvas;
        if (type === 'image/jpeg') {
            // JPEG has no transparency: composite onto white on an offscreen canvas
            // instead of letting transparent pixels turn black.
            var off = document.createElement('canvas'); off.width = canvas.width; off.height = canvas.height;
            var octx = off.getContext('2d'); octx.fillStyle = '#ffffff'; octx.fillRect(0, 0, off.width, off.height); octx.drawImage(canvas, 0, 0);
            src = off;
        }
        src.toBlob(function (b) {
            if (!b) { showError('Download failed in this browser. Please try PNG instead.'); return; }
            var u = URL.createObjectURL(b); var a = document.createElement('a'); a.href = u; a.download = type === 'image/png' ? 'edited-photo.png' : 'edited-photo.jpg'; document.body.appendChild(a); a.click(); a.remove(); setTimeout(function () { URL.revokeObjectURL(u); }, 3000);
        }, type, 0.92);
    }
    document.getElementById('downloadPngBtn').addEventListener('click', function () { download('image/png'); });
    document.getElementById('downloadJpgBtn').addEventListener('click', function () { download('image/jpeg'); });
    dropZone.addEventListener('click', function () { fileInput.click(); });
    fileInput.addEventListener('change', function () { handleFile(fileInput.files && fileInput.files[0]); });
    dropZone.addEventListener('dragover', function (e) { e.preventDefault(); });
    dropZone.addEventListener('drop', function (e) { e.preventDefault(); if (e.dataTransfer && e.dataTransfer.files.length) handleFile(e.dataTransfer.files[0]); });
})();
</script>
@endsection
