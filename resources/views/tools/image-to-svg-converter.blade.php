@extends('layouts.app')

@section('title', 'Image To SVG Converter - Azlaan Tools')
@section('meta_description', 'Convert PNG and JPG images to SVG vector format free online, right in your browser.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Image To SVG Converter</h1>
            <p class="lead text-muted">Turn a PNG or JPG image into an SVG vector file — completely free, no signup. Tracing happens in the browser and the file is never uploaded anywhere. Best for logos, icons and flat graphics.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="fileInput" class="form-label fw-semibold">Select an image (PNG / JPG)</label>
                        <input type="file" class="form-control" id="fileInput" accept="image/png,image/jpeg">
                        <div class="form-text">The image is processed in the browser only — it never goes to a server.</div>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label for="detailSel" class="form-label fw-semibold">Detail level</label>
                            <select class="form-select" id="detailSel">
                                <option value="64">Low (64px) — small file</option>
                                <option value="128" selected>Medium (128px) — balanced</option>
                                <option value="256">High (256px) — large file</option>
                            </select>
                        </div>
                        <div class="col-6 mb-3">
                            <label for="colorSel" class="form-label fw-semibold">Colors</label>
                            <select class="form-select" id="colorSel">
                                <option value="4">4 colors</option>
                                <option value="8" selected>8 colors</option>
                                <option value="16">16 colors</option>
                                <option value="32">32 colors</option>
                            </select>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Convert to SVG</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <h5 class="mb-3">SVG Preview</h5>
                        <div class="text-center border rounded bg-light p-3 mb-3">
                            <img id="svgPreview" alt="SVG preview" style="max-width:100%;image-rendering:pixelated;">
                        </div>
                        <div class="row text-center mb-3">
                            <div class="col-4"><div class="border rounded p-2"><small class="text-muted d-block">Original</small><strong id="origSize">-</strong></div></div>
                            <div class="col-4"><div class="border rounded p-2"><small class="text-muted d-block">SVG size</small><strong id="svgSize">-</strong></div></div>
                            <div class="col-4"><div class="border rounded p-2"><small class="text-muted d-block">Grid</small><strong id="gridSize">-</strong></div></div>
                        </div>
                        <button type="button" class="btn btn-success w-100" id="dlBtn">Download SVG File</button>
                        <p class="form-text mt-2">Tip: this is a pixel-mosaic vectorizer — it works best on flat logos, icons and cartoon images. Photos will get an artistic pixel effect.</p>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select a PNG or JPG image.</li>
                <li>Choose the detail level and colors (more detail = bigger file).</li>
                <li>Press "Convert to SVG", see the preview and download the SVG.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var fileInput = document.getElementById('fileInput');
    var detailSel = document.getElementById('detailSel');
    var colorSel = document.getElementById('colorSel');
    var svgPreview = document.getElementById('svgPreview');
    var dlBtn = document.getElementById('dlBtn');
    var svgString = '';
    var svgBlobUrl = '';

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function fmtKB(bytes) {
        return bytes < 1024 ? bytes + ' B' : (bytes / 1024).toFixed(1) + ' KB';
    }

    dlBtn.addEventListener('click', function () {
        if (!svgString) { return; }
        var blob = new Blob([svgString], { type: 'image/svg+xml' });
        if (svgBlobUrl) { URL.revokeObjectURL(svgBlobUrl); }
        svgBlobUrl = URL.createObjectURL(blob);
        var a = document.createElement('a');
        a.href = svgBlobUrl;
        a.download = 'vectorized.svg';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    });

    goBtn.addEventListener('click', function () {
        hideError();
        var file = fileInput.files[0];
        if (!file) { showError('Please select an image first.'); return; }
        if (!/^image\/(png|jpeg)$/.test(file.type)) { showError('Only a PNG or JPG image is needed.'); return; }

        var img = new Image();
        var objUrl = URL.createObjectURL(file);
        img.onload = function () {
            URL.revokeObjectURL(objUrl);
            try {
                var maxDim = parseInt(detailSel.value, 10);
                var scale = Math.min(1, maxDim / Math.max(img.width, img.height));
                var w = Math.max(1, Math.round(img.width * scale));
                var h = Math.max(1, Math.round(img.height * scale));

                var canvas = document.createElement('canvas');
                canvas.width = w; canvas.height = h;
                var ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0, w, h);
                var data = ctx.getImageData(0, 0, w, h).data;

                var levels = parseInt(colorSel.value, 10);
                function quant(c) { return Math.floor(c / 256 * levels) / levels * 255; }
                function hex(r, g, b) {
                    function h2(x) { var s = Math.round(x).toString(16); return s.length === 1 ? '0' + s : s; }
                    return '#' + h2(r) + h2(g) + h2(b);
                }

                // Build horizontal runs of same color -> one <rect> per run
                var rects = [];
                for (var y = 0; y < h; y++) {
                    var runColor = null, runStart = 0;
                    for (var x = 0; x <= w; x++) {
                        var c = null;
                        if (x < w) {
                            var i = (y * w + x) * 4;
                            c = hex(quant(data[i]), quant(data[i + 1]), quant(data[i + 2]));
                        }
                        if (c !== runColor) {
                            if (runColor !== null) {
                                rects.push({ x: runStart, y: y, w: x - runStart, color: runColor });
                            }
                            runColor = c; runStart = x;
                        }
                    }
                }

                var parts = ['<svg xmlns="http://www.w3.org/2000/svg" width="' + img.width + '" height="' + img.height + '" viewBox="0 0 ' + w + ' ' + h + '" shape-rendering="crispEdges">'];
                for (var k = 0; k < rects.length; k++) {
                    var r = rects[k];
                    parts.push('<rect x="' + r.x + '" y="' + r.y + '" width="' + r.w + '" height="1" fill="' + r.color + '"/>');
                }
                parts.push('</svg>');
                svgString = parts.join('');

                var blob = new Blob([svgString], { type: 'image/svg+xml' });
                if (svgBlobUrl) { URL.revokeObjectURL(svgBlobUrl); }
                svgBlobUrl = URL.createObjectURL(blob);
                svgPreview.src = svgBlobUrl;
                document.getElementById('origSize').textContent = fmtKB(file.size);
                document.getElementById('svgSize').textContent = fmtKB(svgString.length);
                document.getElementById('gridSize').textContent = w + ' x ' + h;
                results.classList.remove('d-none');
            } catch (e) {
                showError('The image could not be processed. Please try another image.');
            }
        };
        img.onerror = function () {
            URL.revokeObjectURL(objUrl);
            showError('The image could not be loaded.');
        };
        img.src = objUrl;
    });
})();
</script>
@endsection
