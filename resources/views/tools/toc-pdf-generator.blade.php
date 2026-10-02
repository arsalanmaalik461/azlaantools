@extends('layouts.app')

@section('title', 'PDF Table of Contents - Azlaan Tools')
@section('meta_description', 'Auto-create a clickable table of contents for any PDF from its headings - free online.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">PDF Table of Contents</h1>
            <p class="lead text-muted">Make a clickable list (TOC) automatically from the headings of any PDF. Download the new PDF — the list is on the first page, and clicking it takes you straight to that page.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="pdfFile" class="form-label fw-semibold">Choose a PDF file</label>
                        <input type="file" class="form-control" id="pdfFile" accept="application/pdf,.pdf">
                        <div class="form-text">The file is processed only in your browser, it is not uploaded.</div>
                    </div>
                    <div class="mb-3">
                        <label for="senseSelect" class="form-label fw-semibold">Heading detection</label>
                        <select class="form-select" id="senseSelect">
                            <option value="1.4">Strict (only big headings)</option>
                            <option value="1.25" selected>Normal</option>
                            <option value="1.12">Loose (small headings too)</option>
                        </select>
                        <div class="form-text">Headings are found by font size — big text = heading.</div>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Find Headings</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="headsWrap" class="d-none mt-4">
                        <h5>Headings found <span class="text-muted fw-normal small">(uncheck the ones you do not want)</span></h5>
                        <div id="headsList" class="border rounded p-2 mb-3" style="max-height: 260px; overflow-y: auto;"></div>
                        <button type="button" class="btn btn-success w-100" id="genBtn">Make PDF with Clickable TOC</button>
                    </div>

                    <div id="results" class="d-none mt-4">
                        <div class="alert alert-success" id="summaryBox"></div>
                        <button type="button" class="btn btn-success w-100" id="dlBtn">Download PDF with TOC</button>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select a PDF and press <strong>Find Headings</strong>.</li>
                <li>Uncheck the unwanted headings from the list.</li>
                <li><strong>Make PDF with Clickable TOC</strong> — the list will be on the first page, every entry a clickable link.</li>
            </ol>
            <p class="text-muted small">In scanned (image) PDFs, headings cannot be found — use a text PDF.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/pdfjs-dist@3.11.174/build/pdf.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/pdf-lib@1.17.1/dist/pdf-lib.min.js"></script>
<script>
(function () {
    'use strict';
    var pdfFile = document.getElementById('pdfFile');
    var senseSelect = document.getElementById('senseSelect');
    var goBtn = document.getElementById('goBtn');
    var genBtn = document.getElementById('genBtn');
    var dlBtn = document.getElementById('dlBtn');
    var errorBox = document.getElementById('errorBox');
    var headsWrap = document.getElementById('headsWrap');
    var headsList = document.getElementById('headsList');
    var results = document.getElementById('results');
    var summaryBox = document.getElementById('summaryBox');

    if (window.pdfjsLib) {
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdn.jsdelivr.net/npm/pdfjs-dist@3.11.174/build/pdf.worker.min.js';
    }

    var fileBytes = null;
    var headings = [];
    var outUrl = null;

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        headsWrap.classList.add('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }

    goBtn.addEventListener('click', function () {
        hideError();
        headsWrap.classList.add('d-none');
        results.classList.add('d-none');
        var file = pdfFile.files && pdfFile.files[0];
        if (!file) { showError('Please choose a PDF file first.'); return; }
        if (!window.pdfjsLib) { showError('The PDF library did not load. Please check your internet and try again.'); return; }
        goBtn.disabled = true;
        goBtn.textContent = 'Scanning...';

        var reader = new FileReader();
        reader.onload = function () {
            fileBytes = new Uint8Array(reader.result);
            pdfjsLib.getDocument({ data: fileBytes.slice() }).promise.then(function (doc) {
                var jobs = [];
                for (var p = 1; p <= doc.numPages; p++) {
                    (function (pageNum) {
                        jobs.push(doc.getPage(pageNum).then(function (page) {
                            return page.getTextContent().then(function (tc) {
                                return { page: pageNum, items: tc.items };
                            });
                        }));
                    })(p);
                }
                return Promise.all(jobs);
            }).then(function (pages) {
                goBtn.disabled = false;
                goBtn.textContent = 'Find Headings';
                var mult = parseFloat(senseSelect.value);
                var all = [];
                pages.forEach(function (pg) {
                    pg.items.forEach(function (it) {
                        var str = (it.str || '').trim();
                        if (str.length < 3 || str.length > 140) { return; }
                        var size = Math.abs(it.transform[0]) || 0;
                        if (size <= 0) { return; }
                        all.push({ size: size, page: pg.page, text: str, y: it.transform[5] });
                    });
                });
                if (!all.length) {
                    showError('No selectable text found in this PDF (it may be scanned).');
                    return;
                }
                var sizes = all.map(function (a) { return a.size; }).sort(function (a, b) { return a - b; });
                var body = sizes[Math.floor(sizes.length / 2)];
                var threshold = Math.max(body * mult, body + 1.5);
                var cands = all.filter(function (a) { return a.size >= threshold; });
                cands.sort(function (a, b) { return a.page - b.page || b.y - a.y; });
                // level by distinct size rank (top 3)
                var distinct = [];
                cands.forEach(function (c) {
                    var r = Math.round(c.size);
                    if (distinct.indexOf(r) < 0) { distinct.push(r); }
                });
                distinct.sort(function (a, b) { return b - a; });
                headings = cands.slice(0, 80).map(function (c) {
                    var lvl = distinct.indexOf(Math.round(c.size));
                    return { text: c.text, page: c.page, level: lvl < 0 ? 2 : Math.min(lvl, 2) };
                });
                if (!headings.length) {
                    showError('No headings found. Try "Loose" detection or use a text PDF.');
                    return;
                }
                headsList.innerHTML = '';
                headings.forEach(function (h, idx) {
                    var div = document.createElement('div');
                    div.className = 'form-check';
                    var cb = document.createElement('input');
                    cb.type = 'checkbox'; cb.className = 'form-check-input';
                    cb.id = 'hd' + idx; cb.checked = true;
                    cb.setAttribute('data-idx', idx);
                    var lb = document.createElement('label');
                    lb.className = 'form-check-label'; lb.htmlFor = 'hd' + idx;
                    lb.textContent = h.text + '  (page ' + h.page + ', level ' + (h.level + 1) + ')';
                    div.appendChild(cb); div.appendChild(lb);
                    headsList.appendChild(div);
                });
                headsWrap.classList.remove('d-none');
            }).catch(function (err) {
                goBtn.disabled = false;
                goBtn.textContent = 'Find Headings';
                showError('Problem scanning the PDF: ' + (err && err.message ? err.message : 'unknown error'));
            });
        };
        reader.readAsArrayBuffer(file);
    });

    genBtn.addEventListener('click', function () {
        hideError();
        results.classList.add('d-none');
        var boxes = headsList.querySelectorAll('input[type="checkbox"]');
        var chosen = [];
        boxes.forEach(function (cb) {
            if (cb.checked) { chosen.push(headings[parseInt(cb.getAttribute('data-idx'), 10)]); }
        });
        if (!chosen.length) { showError('Please select at least one heading.'); return; }
        if (!window.PDFLib) { showError('The PDF library did not load. Please try again.'); return; }
        genBtn.disabled = true;
        genBtn.textContent = 'Making...';

        PDFLib.PDFDocument.load(fileBytes.slice()).then(function (pdfDoc) {
            return Promise.all([
                pdfDoc.embedFont(PDFLib.StandardFonts.Helvetica),
                pdfDoc.embedFont(PDFLib.StandardFonts.HelveticaBold)
            ]).then(function (fonts) {
                var font = fonts[0], bold = fonts[1];
                var PAGE_W = 612, PAGE_H = 792, MARGIN = 60, LINE_H = 26, PER_PAGE = 24;
                var tocCount = Math.max(1, Math.ceil(chosen.length / PER_PAGE));
                for (var t = 0; t < tocCount; t++) { pdfDoc.insertPage(t, [PAGE_W, PAGE_H]); }

                var ctx = pdfDoc.context;
                chosen.forEach(function (h, i) {
                    var tocIdx = Math.floor(i / PER_PAGE);
                    var lineNo = i % PER_PAGE;
                    var page = pdfDoc.getPage(tocIdx);
                    var y = PAGE_H - 90 - lineNo * LINE_H;
                    if (lineNo === 0) {
                        page.drawText(tocCount > 1 ? 'Table of Contents (' + (tocIdx + 1) + '/' + tocCount + ')' : 'Table of Contents',
                            { x: MARGIN, y: PAGE_H - 60, size: 20, font: bold, color: PDFLib.rgb(0.1, 0.1, 0.1) });
                    }
                    var indent = h.level * 22;
                    var f = h.level === 0 ? bold : font;
                    var size = h.level === 0 ? 13 : 11.5;
                    var label = h.text.length > 62 ? h.text.slice(0, 60) + '..' : h.text;
                    var pageLabel = String(h.page + tocCount);
                    var x0 = MARGIN + indent;
                    page.drawText(label, { x: x0, y: y, size: size, font: f, color: PDFLib.rgb(0.15, 0.15, 0.15) });
                    var labelW = f.widthOfTextAtSize(label, size);
                    var numW = font.widthOfTextAtSize(pageLabel, size);
                    var dotsX = x0 + labelW + 8;
                    var dotsEnd = PAGE_W - MARGIN - numW - 8;
                    if (dotsEnd > dotsX) {
                        var dots = '';
                        var dotW = font.widthOfTextAtSize('.', size);
                        var n = Math.floor((dotsEnd - dotsX) / (dotW + 2));
                        for (var d = 0; d < n; d++) { dots += '. '; }
                        page.drawText(dots, { x: dotsX, y: y, size: size, font: font, color: PDFLib.rgb(0.6, 0.6, 0.6) });
                    }
                    page.drawText(pageLabel, { x: PAGE_W - MARGIN - numW, y: y, size: size, font: font, color: PDFLib.rgb(0.15, 0.15, 0.15) });

                    // clickable link annotation -> target page (original page h.page, now shifted by tocCount)
                    var targetPage = pdfDoc.getPage(h.page - 1 + tocCount);
                    var dest = ctx.obj([targetPage.ref, PDFLib.PDFName.of('Fit')]);
                    var annot = ctx.obj({
                        Type: PDFLib.PDFName.of('Annot'),
                        Subtype: PDFLib.PDFName.of('Link'),
                        Rect: ctx.obj([x0, y - 5, PAGE_W - MARGIN, y + 15]),
                        Border: ctx.obj([0, 0, 0]),
                        Dest: dest
                    });
                    var ref = ctx.register(annot);
                    var existing = page.node.Annots();
                    if (existing) { existing.push(ref); }
                    else { page.node.set(PDFLib.PDFName.of('Annots'), ctx.obj([ref])); }
                });
                return pdfDoc.save();
            });
        }).then(function (bytes) {
            genBtn.disabled = false;
            genBtn.textContent = 'Make PDF with Clickable TOC';
            var blob = new Blob([bytes], { type: 'application/pdf' });
            if (outUrl) { URL.revokeObjectURL(outUrl); }
            outUrl = URL.createObjectURL(blob);
            summaryBox.textContent = 'Done! The TOC is added on the first page(s) — every entry is a clickable link.';
            results.classList.remove('d-none');
        }).catch(function (err) {
            genBtn.disabled = false;
            genBtn.textContent = 'Make PDF with Clickable TOC';
            showError('Problem making the PDF: ' + (err && err.message ? err.message : 'unknown error'));
        });
    });

    dlBtn.addEventListener('click', function () {
        if (!outUrl) { return; }
        var a = document.createElement('a');
        a.href = outUrl;
        a.download = 'pdf-with-toc.pdf';
        document.body.appendChild(a);
        a.click();
        a.remove();
    });
})();
</script>
@endsection
