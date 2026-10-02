@extends('layouts.app')
@section('title', 'Add Page Borders to PDF - Azlaan Tools')
@section('meta_description', 'Add decorative page borders to any PDF for free. Choose border styles, colors and margins — everything runs in your browser, no upload needed.')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Add Page Borders to PDF</h1>
            <p class="lead text-muted">Add a beautiful decorative border to every page of your PDF. Give your certificate, report or document a stylish frame — the file never leaves your browser.</p>

            <div id="dropZone" class="border border-2 border-dashed rounded p-5 text-center mb-4 bg-light" style="cursor: pointer;">
                <div class="fs-1 mb-2">🖼️</div>
                <p class="mb-1 fw-semibold">Drag &amp; drop a PDF here</p>
                <p class="text-muted mb-3">or click to select a file</p>
                <button type="button" class="btn btn-primary">Select PDF File</button>
                <input type="file" id="fileInput" accept="application/pdf,.pdf" class="d-none">
            </div>

            <div id="toolWrap" class="d-none">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <p class="mb-1">File: <strong id="fileName" class="text-break"></strong></p>
                        <p class="mb-3">Pages: <span id="pageInfo" class="fw-semibold">—</span></p>
                        <div class="alert alert-danger d-none" id="errorBox" role="alert"></div>

                        <div class="mb-3">
                            <label for="styleSel" class="form-label fw-semibold">Border style</label>
                            <select class="form-select" id="styleSel">
                                <option value="single">Single line — simple clean border</option>
                                <option value="double">Double line — two parallel lines</option>
                                <option value="thick">Thick line — bold frame</option>
                                <option value="dashed">Dashed line — broken line</option>
                                <option value="corners">Corner accents — styled corners</option>
                            </select>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label for="colorInput" class="form-label fw-semibold">Border color</label>
                                <input type="color" class="form-control form-control-color w-100" id="colorInput" value="#1a56db" title="Border color">
                            </div>
                            <div class="col-6">
                                <label for="marginInput" class="form-label fw-semibold">Margin (points)</label>
                                <input type="number" class="form-control" id="marginInput" value="36" min="10" max="150" step="1">
                                <div class="form-text">36 points = half an inch. The bigger the margin, the further inside the border.</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <span class="form-label fw-semibold d-block">Which pages</span>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="pgMode" id="modeAll" value="all" checked>
                                <label class="form-check-label" for="modeAll">All pages</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="pgMode" id="modeRange" value="range">
                                <label class="form-check-label" for="modeRange">Selected pages only</label>
                            </div>
                            <input type="text" class="form-control mt-2" id="rangeInput" placeholder="e.g. 1-3,5" disabled>
                        </div>

                        <span class="form-label fw-semibold d-block mb-2">Preview</span>
                        <div class="border rounded bg-white p-3 mb-3 text-center">
                            <svg id="stylePreview" width="220" height="280" viewBox="0 0 220 280" class="border bg-white"></svg>
                        </div>

                        <div class="d-flex flex-wrap gap-2">
                            <button type="button" id="goBtn" class="btn btn-success btn-lg">Add Borders &amp; Download PDF</button>
                            <button type="button" id="clearBtn" class="btn btn-outline-secondary">Clear</button>
                        </div>
                        <div id="progressWrap" class="progress mt-3 d-none" style="height: 24px;">
                            <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%;">0%</div>
                        </div>

                        <div id="results" class="d-none mt-4"></div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Drop your PDF file here or select it.</li>
                <li>Choose the border style, color and margin — decide after looking at the preview.</li>
                <li>Press "Add Borders &amp; Download PDF" — the new bordered PDF will download.</li>
            </ol>
            <p class="text-muted small">Note: the border is drawn at the page edge, not over the original content. If your content touches the page edge, increase the margin.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/pdf-lib@1.17.1/dist/pdf-lib.min.js"></script>
<script>
(function () {
    'use strict';
    var dropZone = document.getElementById('dropZone');
    var fileInput = document.getElementById('fileInput');
    var toolWrap = document.getElementById('toolWrap');
    var fileName = document.getElementById('fileName');
    var pageInfo = document.getElementById('pageInfo');
    var errorBox = document.getElementById('errorBox');
    var styleSel = document.getElementById('styleSel');
    var colorInput = document.getElementById('colorInput');
    var marginInput = document.getElementById('marginInput');
    var modeAll = document.getElementById('modeAll');
    var modeRange = document.getElementById('modeRange');
    var rangeInput = document.getElementById('rangeInput');
    var stylePreview = document.getElementById('stylePreview');
    var goBtn = document.getElementById('goBtn');
    var clearBtn = document.getElementById('clearBtn');
    var progressWrap = document.getElementById('progressWrap');
    var progressBar = document.getElementById('progressBar');
    var results = document.getElementById('results');
    var pdfBytes = null;
    var totalPages = 0;

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function setProgress(pct) {
        progressWrap.classList.remove('d-none');
        progressBar.style.width = pct + '%';
        progressBar.textContent = pct + '%';
    }

    dropZone.addEventListener('click', function () { fileInput.click(); });
    dropZone.addEventListener('dragover', function (e) { e.preventDefault(); dropZone.classList.add('bg-white'); });
    dropZone.addEventListener('dragleave', function () { dropZone.classList.remove('bg-white'); });
    dropZone.addEventListener('drop', function (e) {
        e.preventDefault();
        dropZone.classList.remove('bg-white');
        if (e.dataTransfer.files.length) handleFile(e.dataTransfer.files[0]);
    });
    fileInput.addEventListener('change', function () {
        if (fileInput.files.length) handleFile(fileInput.files[0]);
    });

    function handleFile(f) {
        hideError();
        if (!/pdf$/i.test(f.name) && f.type !== 'application/pdf') {
            showError('Please select a PDF file.');
            return;
        }
        var reader = new FileReader();
        reader.onload = function () {
            pdfBytes = reader.result;
            try {
                PDFLib.PDFDocument.load(pdfBytes).then(function (doc) {
                    totalPages = doc.getPageCount();
                    fileName.textContent = f.name;
                    pageInfo.textContent = totalPages;
                    toolWrap.classList.remove('d-none');
                    dropZone.classList.add('d-none');
                    updatePreview();
                }).catch(function () { showError('Could not read this PDF.'); });
            } catch (err) { showError('Could not read this PDF.'); }
        };
        reader.readAsArrayBuffer(f);
    }

    modeAll.addEventListener('change', function () { rangeInput.disabled = true; });
    modeRange.addEventListener('change', function () { rangeInput.disabled = false; });

    styleSel.addEventListener('change', updatePreview);
    colorInput.addEventListener('input', updatePreview);
    marginInput.addEventListener('input', updatePreview);

    function svgRect(x, y, w, h, style, color) {
        var attrs = 'x="' + x + '" y="' + y + '" width="' + w + '" height="' + h + '" fill="none" stroke="' + color + '"';
        if (style === 'double') attrs += ' stroke-width="2"';
        else if (style === 'thick') attrs += ' stroke-width="8"';
        else if (style === 'dashed') attrs += ' stroke-width="2" stroke-dasharray="8,6"';
        else attrs += ' stroke-width="3"';
        return '<rect ' + attrs + '/>';
    }

    function updatePreview() {
        var style = styleSel.value;
        var color = colorInput.value;
        var m = Math.min(60, Math.max(14, parseInt(marginInput.value, 10) || 36)) / 2.2;
        var w = 220, h = 280, html = '';
        html += '<rect x="2" y="2" width="216" height="276" fill="#f8f9fa" stroke="#dee2e6"/>';
        html += '<rect x="70" y="30" width="80" height="10" fill="#ced4da"/>';
        html += '<rect x="50" y="50" width="120" height="7" fill="#ced4da"/>';
        html += '<rect x="60" y="65" width="100" height="7" fill="#ced4da"/>';
        html += svgRect(m, m, w - 2 * m, h - 2 * m, style, color);
        if (style === 'double') html += svgRect(m + 10, m + 10, w - 2 * (m + 10), h - 2 * (m + 10), 'single', color);
        if (style === 'corners') {
            var r = 7;
            [[m, m], [w - m, m], [m, h - m], [w - m, h - m]].forEach(function (pt) {
                html += '<circle cx="' + pt[0] + '" cy="' + pt[1] + '" r="' + r + '" fill="' + color + '"/>';
            });
        }
        stylePreview.innerHTML = html;
    }

    function parseRange(str, max) {
        var out = [], seen = {};
        var parts = str.split(',');
        for (var i = 0; i < parts.length; i++) {
            var p = parts[i].trim();
            if (!p) continue;
            var m = p.match(/^(\d+)(?:\s*-\s*(\d+))?$/);
            if (!m) return null;
            var a = parseInt(m[1], 10), b = m[2] ? parseInt(m[2], 10) : a;
            if (a < 1 || b < 1 || a > max || b > max) return null;
            var lo = Math.min(a, b), hi = Math.max(a, b);
            for (var n = lo; n <= hi; n++) {
                if (!seen[n]) { seen[n] = true; out.push(n); }
            }
        }
        return out.length ? out : null;
    }

    function hexToRgb(hex) {
        var v = parseInt(hex.slice(1), 16);
        return PDFLib.rgb(((v >> 16) & 255) / 255, ((v >> 8) & 255) / 255, (v & 255) / 255);
    }

    goBtn.addEventListener('click', function () {
        hideError();
        results.classList.add('d-none');
        if (!pdfBytes) { showError('Please select a PDF file first.'); return; }
        var style = styleSel.value;
        var color = hexToRgb(colorInput.value);
        var margin = Math.min(150, Math.max(10, parseInt(marginInput.value, 10) || 36));
        var targetPages = [];
        for (var i = 0; i < totalPages; i++) targetPages.push(i);
        if (modeRange.checked) {
            var parsed = parseRange(rangeInput.value, totalPages);
            if (!parsed) { showError('Page range is wrong. Example: 1-3,5'); return; }
            targetPages = parsed.map(function (n) { return n - 1; });
        }
        setProgress(5);
        var busy = true;
        PDFLib.PDFDocument.load(pdfBytes).then(function (doc) {
            var pages = doc.getPages();
            targetPages.forEach(function (idx, k) {
                var page = pages[idx];
                var pw = page.getWidth(), ph = page.getHeight();
                var x = margin, y = margin, w = pw - 2 * margin, h = ph - 2 * margin;
                if (w <= 40 || h <= 40) { showError('The margin is too big for this page. Use a smaller margin.'); busy = false; return; }
                var opt = { x: x, y: y, width: w, height: h, borderColor: color, borderWidth: 2 };
                if (style === 'thick') opt.borderWidth = 7;
                else if (style === 'double') opt.borderWidth = 2;
                else if (style === 'dashed') { opt.borderWidth = 2; opt.borderDashArray = [9, 7]; }
                page.drawRectangle(opt);
                if (style === 'double') {
                    page.drawRectangle({ x: x + 10, y: y + 10, width: w - 20, height: h - 20, borderColor: color, borderWidth: 1.5 });
                }
                if (style === 'corners') {
                    var r = 7;
                    [[x, y], [x + w, y], [x, y + h], [x + w, y + h]].forEach(function (pt) {
                        page.drawCircle({ x: pt[0], y: pt[1], size: r, color: color });
                    });
                }
                setProgress(10 + Math.round(85 * (k + 1) / targetPages.length));
            });
            if (!busy) { progressWrap.classList.add('d-none'); return null; }
            return doc.save();
        }).then(function (bytes) {
            if (!bytes) return;
            var blob = new Blob([bytes], { type: 'application/pdf' });
            var url = URL.createObjectURL(blob);
            setProgress(100);
            results.classList.remove('d-none');
            results.innerHTML = '<div class="alert alert-success">Done! Border added to ' + targetPages.length + ' page(s). <a href="' + url + '" download="bordered.pdf" class="alert-link">Download Bordered PDF</a></div>';
        }).catch(function () {
            progressWrap.classList.add('d-none');
            showError('Something went wrong — please try again.');
        });
    });

    clearBtn.addEventListener('click', function () {
        pdfBytes = null; totalPages = 0; fileInput.value = '';
        toolWrap.classList.add('d-none');
        dropZone.classList.remove('d-none');
        results.classList.add('d-none');
        progressWrap.classList.add('d-none');
        hideError();
    });
})();
</script>
@endsection
