@extends('layouts.app')
@section('title', 'Add Crop Marks to PDF Online Free — Azlaan Tools')
@section('meta_description', 'Add printer crop marks, bleed and registration marks to any PDF online for free. Professional prepress marks added in your browser — no upload, no signup.')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Add Crop Marks to PDF</h1>
            <p class="lead text-muted">Add printer crop marks, bleed and registration marks to your PDF. Download a file ready for professional printing and prepress — everything happens in your browser, the file is not uploaded.</p>
            <div class="alert alert-info">
                <strong>What it does:</strong> Bleed (extra margin) is added around every page, and crop marks are drawn outside it so the print shop can cut each page to the correct size. 3 mm bleed is the standard for professional printing.
            </div>

            <div class="mb-3">
                <label for="fileInput" class="form-label fw-semibold">Select PDF file</label>
                <input type="file" class="form-control" id="fileInput" accept="application/pdf,.pdf">
                <div class="form-text" id="fileInfo">No file selected yet.</div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6 col-md-3">
                            <label for="bleedMm" class="form-label fw-semibold">Bleed (mm)</label>
                            <select class="form-select" id="bleedMm">
                                <option value="0">0 (marks only)</option>
                                <option value="3" selected>3 (standard)</option>
                                <option value="5">5</option>
                                <option value="10">10</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-3">
                            <label for="markLen" class="form-label fw-semibold">Mark length (mm)</label>
                            <select class="form-select" id="markLen">
                                <option value="5">5</option>
                                <option value="7" selected>7</option>
                                <option value="10">10</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-3">
                            <label for="markOffset" class="form-label fw-semibold">Mark offset (mm)</label>
                            <select class="form-select" id="markOffset">
                                <option value="2">2</option>
                                <option value="3" selected>3</option>
                                <option value="5">5</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-3">
                            <label for="markColor" class="form-label fw-semibold">Mark color</label>
                            <select class="form-select" id="markColor">
                                <option value="black" selected>Black</option>
                                <option value="red">Red</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" id="centerMarks" checked>
                        <label class="form-check-label" for="centerMarks">Also add center registration marks (cross marks at the edge centers)</label>
                    </div>
                    <div class="form-check mt-1">
                        <input class="form-check-input" type="checkbox" id="pageInfoMarks">
                        <label class="form-check-label" for="pageInfoMarks">Write page number and size on every page</label>
                    </div>
                    <div class="mb-3 mt-3">
                        <label for="rangeInput" class="form-label fw-semibold">Pages (leave empty = all pages)</label>
                        <input type="text" class="form-control" id="rangeInput" placeholder="e.g. 1-3,5">
                        <div class="form-text">Example: 2 &nbsp;|&nbsp; 1-3,5 &nbsp;|&nbsp; 1,3,7-9</div>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Add Crop Marks</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>
                    <div id="results" class="d-none mt-4">
                        <div class="alert alert-success" id="doneMsg"></div>
                        <a href="#" class="btn btn-success w-100" id="dlLink" download="with-crop-marks.pdf">Download PDF</a>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select your PDF file.</li>
                <li>Choose the bleed size (3 mm is standard) and change mark length, offset and color if needed.</li>
                <li>Press <strong>Add Crop Marks</strong> — the new PDF will have crop marks outside the trim box on every page.</li>
                <li>Download it and send it to the print shop.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/pdf-lib@1.17.1/dist/pdf-lib.min.js"></script>
<script>
(function () {
    'use strict';
    var MM = 72 / 25.4; // points per mm
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var fileInput = document.getElementById('fileInput');
    var fileInfo = document.getElementById('fileInfo');

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }

    fileInput.addEventListener('change', function () {
        hideError();
        if (fileInput.files.length) {
            fileInfo.textContent = 'Selected: ' + fileInput.files[0].name + ' (' + Math.round(fileInput.files[0].size / 1024) + ' KB)';
        } else {
            fileInfo.textContent = 'No file selected yet.';
        }
    });

    function parseRange(str, total) {
        str = (str || '').trim();
        if (!str) {
            var all = [];
            for (var i = 0; i < total; i++) { all.push(i); }
            return all;
        }
        var set = {};
        var parts = str.split(',');
        for (var p = 0; p < parts.length; p++) {
            var t = parts[p].trim();
            if (!t) { continue; }
            var dash = t.indexOf('-');
            if (dash >= 0) {
                var a = parseInt(t.slice(0, dash), 10), b = parseInt(t.slice(dash + 1), 10);
                if (isNaN(a) || isNaN(b) || a < 1 || b < 1) { return null; }
                var lo = Math.min(a, b), hi = Math.max(a, b);
                for (var k = lo; k <= hi; k++) { if (k <= total) { set[k - 1] = 1; } }
            } else {
                var n = parseInt(t, 10);
                if (isNaN(n) || n < 1 || n > total) { return null; }
                set[n - 1] = 1;
            }
        }
        var out = [];
        for (var key in set) { if (set.hasOwnProperty(key)) { out.push(parseInt(key, 10)); } }
        out.sort(function (x, y) { return x - y; });
        return out.length ? out : null;
    }

    goBtn.addEventListener('click', function () {
        hideError();
        if (typeof PDFLib === 'undefined') { showError('The PDF library did not load. Check your internet and try again.'); return; }
        if (!fileInput.files.length) { showError('Please select a PDF file first.'); return; }
        var bleed = parseFloat(document.getElementById('bleedMm').value) * MM;
        var mLen = parseFloat(document.getElementById('markLen').value) * MM;
        var mOff = parseFloat(document.getElementById('markOffset').value) * MM;
        var colorName = document.getElementById('markColor').value;
        var withCenters = document.getElementById('centerMarks').checked;
        var withInfo = document.getElementById('pageInfoMarks').checked;
        var rangeStr = document.getElementById('rangeInput').value;

        goBtn.disabled = true;
        goBtn.textContent = 'Working...';

        var reader = new FileReader();
        reader.onload = function () {
            PDFLib.PDFDocument.load(reader.result, { ignoreEncryption: true }).then(function (pdf) {
                var total = pdf.getPageCount();
                var pages = parseRange(rangeStr, total);
                if (!pages) { throw new Error('Page range is wrong. Example: 1-3,5'); }
                var ink = colorName === 'red' ? PDFLib.rgb(0.8, 0, 0) : PDFLib.rgb(0, 0, 0);

                function hline(page, x1, x2, y) {
                    page.drawLine({ start: { x: x1, y: y }, end: { x: x2, y: y }, thickness: 0.5, color: ink });
                }
                function vline(page, x, y1, y2) {
                    page.drawLine({ start: { x: x, y: y1 }, end: { x: x, y: y2 }, thickness: 0.5, color: ink });
                }

                for (var i = 0; i < pages.length; i++) {
                    var page = pdf.getPage(pages[i]);
                    var mb = page.getMediaBox();
                    var ox = mb.x, oy = mb.y, w = mb.width, h = mb.height;
                    // Expand page box by bleed on all sides (trim box stays the original area)
                    page.setMediaBox(ox - bleed, oy - bleed, ox + w + bleed, oy + h + bleed);
                    var L = ox + bleed, R = ox + w - bleed, B = oy + bleed, T = oy + h - bleed;
                    var o = mOff, m = mLen;
                    // Top edge marks
                    hline(page, L - o - m, L - o, T + o);
                    hline(page, R + o, R + o + m, T + o);
                    // Bottom edge marks
                    hline(page, L - o - m, L - o, B - o);
                    hline(page, R + o, R + o + m, B - o);
                    // Left edge marks
                    vline(page, L - o, B - o - m, B - o);
                    vline(page, L - o, T + o, T + o + m);
                    // Right edge marks
                    vline(page, R + o, B - o - m, B - o);
                    vline(page, R + o, T + o, T + o + m);
                    // Center registration marks (small crosses at edge midpoints)
                    if (withCenters) {
                        var cx = (L + R) / 2, cy = (B + T) / 2, c = m * 0.6;
                        hline(page, cx - c, cx + c, T + o); vline(page, cx, T + o - c, T + o + c);
                        hline(page, cx - c, cx + c, B - o); vline(page, cx, B - o - c, B - o + c);
                        vline(page, L - o, cy - c, cy + c); hline(page, L - o - c, L - o + c, cy);
                        vline(page, R + o, cy - c, cy + c); hline(page, R + o - c, R + o + c, cy);
                    }
                    if (withInfo) {
                        var label = 'Page ' + (pages[i] + 1) + ' — ' + Math.round(w) + ' x ' + Math.round(h) + ' pt';
                        page.drawText(label, { x: L + 6, y: B - o - 14, size: 8, color: ink });
                    }
                }
                return pdf.save();
            }).then(function (bytes) {
                var blob = new Blob([bytes], { type: 'application/pdf' });
                var url = URL.createObjectURL(blob);
                var dl = document.getElementById('dlLink');
                var base = fileInput.files[0].name.replace(/\.pdf$/i, '');
                dl.href = url;
                dl.setAttribute('download', base + '-crop-marks.pdf');
                document.getElementById('doneMsg').textContent = 'Done! Crop marks added — download the file.';
                results.classList.remove('d-none');
            }).catch(function (err) {
                showError('Error: ' + (err && err.message ? err.message : 'the file could not be processed.'));
            }).finally(function () {
                goBtn.disabled = false;
                goBtn.textContent = 'Add Crop Marks';
            });
        };
        reader.onerror = function () {
            showError('There was a problem reading the file.');
            goBtn.disabled = false;
            goBtn.textContent = 'Add Crop Marks';
        };
        reader.readAsArrayBuffer(fileInput.files[0]);
    });
})();
</script>
@endsection
