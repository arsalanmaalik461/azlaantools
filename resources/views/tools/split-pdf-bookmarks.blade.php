@extends('layouts.app')

@section('title', 'Split PDF by Bookmarks - Azlaan Tools')
@section('meta_description', 'Split a big PDF into chapters wherever bookmarks divide it. Free online, no upload needed.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Split PDF by Bookmarks</h1>
            <p class="lead text-muted">Break a big PDF into chapters at bookmark points — each chapter a separate PDF, all in one ZIP. Split chapters easily.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="pdfFile" class="form-label fw-semibold">Select a PDF file</label>
                        <input type="file" class="form-control" id="pdfFile" accept=".pdf,application/pdf">
                        <div class="form-text" id="fileInfo">The PDF must have bookmarks (outline) — otherwise it cannot be split.</div>
                    </div>
                    <div class="mb-3">
                        <label for="levelSel" class="form-label fw-semibold">Bookmark level</label>
                        <select class="form-select" id="levelSel">
                            <option value="1">Top-level bookmarks only (main chapters)</option>
                            <option value="2" selected>2 levels (chapters + sub-sections)</option>
                        </select>
                        <div class="form-text">Level 1 = only main chapters; Level 2 = headings inside chapters will also be split</div>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Split by Bookmarks</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="alert alert-success" id="doneMsg" role="status"></div>
                        <div class="table-responsive mb-3">
                            <table class="table table-sm table-striped">
                                <thead><tr><th>#</th><th>Chapter</th><th class="text-end">Pages</th></tr></thead>
                                <tbody id="partRows"></tbody>
                            </table>
                        </div>
                        <a class="btn btn-success w-100" id="dlBtn" href="#" download>Download ZIP (all chapters)</a>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select a PDF with bookmarks.</li>
                <li>Choose a level — only main chapters or sub-sections too.</li>
                <li>Press <strong>Split by Bookmarks</strong>, then download the ZIP.</li>
            </ol>
            <p class="text-muted small">Note: The PDF must contain bookmarks. If the file has no bookmarks, the tool will tell you — in that case use the page-range split tool.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/pdfjs-dist@3.11.174/build/pdf.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/pdf-lib@1.17.1/dist/pdf-lib.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jszip@3.10.1/dist/jszip.min.js"></script>
<script>
(function () {
    'use strict';
    var pdfjsLib = window['pdfjs-dist/build/pdf'];
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdn.jsdelivr.net/npm/pdfjs-dist@3.11.174/build/pdf.worker.min.js';
    var PDFLib = window.PDFLib;

    var pdfFile = document.getElementById('pdfFile');
    var levelSel = document.getElementById('levelSel');
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var doneMsg = document.getElementById('doneMsg');
    var partRows = document.getElementById('partRows');
    var dlBtn = document.getElementById('dlBtn');
    var fileInfo = document.getElementById('fileInfo');
    var pdfBytes = null;

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function sanitize(t) {
        var s = String(t || 'chapter').replace(/[^\w\-\u0600-\u06FF ]+/g, '').trim();
        if (!s) s = 'chapter';
        return s.slice(0, 60);
    }

    pdfFile.addEventListener('change', function () {
        hideError();
        results.classList.add('d-none');
        pdfBytes = null;
        var f = pdfFile.files[0];
        if (!f) { fileInfo.textContent = 'The PDF should have bookmarks (outline).'; return; }
        var r = new FileReader();
        r.onload = function () {
            pdfBytes = r.result;
            fileInfo.textContent = 'File: ' + f.name + ' — ready.';
        };
        r.onerror = function () {
            fileInfo.textContent = 'Could not read the file.';
        };
        r.readAsArrayBuffer(f);
    });

    function flatten(items, depth, maxDepth, out) {
        for (var i = 0; i < items.length; i++) {
            var it = items[i];
            if (depth <= maxDepth) out.push({ title: it.title, dest: it.dest, depth: depth });
            if (it.items && it.items.length) flatten(it.items, depth + 1, maxDepth, out);
        }
    }

    function resolveDest(doc, dest) {
        return new Promise(function (resolve) {
            if (!dest) { resolve(null); return; }
            var p = (typeof dest === 'string') ? doc.getDestination(dest) : Promise.resolve(dest);
            p.then(function (d) {
                if (!d || !d[0]) { resolve(null); return; }
                doc.getPageIndex(d[0]).then(function (idx) {
                    resolve(idx);
                }).catch(function () { resolve(null); });
            }).catch(function () { resolve(null); });
        });
    }

    goBtn.addEventListener('click', function () {
        hideError();
        if (!pdfBytes) { showError('Please select a PDF file first.'); return; }
        var maxDepth = parseInt(levelSel.value, 10) || 2;
        goBtn.disabled = true;
        goBtn.textContent = 'Splitting...';
        var jsBytes = pdfBytes.slice(0);
        var libBytes = pdfBytes.slice(0);

        pdfjsLib.getDocument({ data: jsBytes }).promise.then(function (doc) {
            return doc.getOutline().then(function (outline) {
                return { doc: doc, outline: outline };
            });
        }).then(function (ctx) {
            var doc = ctx.doc;
            var outline = ctx.outline;
            if (!outline || !outline.length) {
                throw new Error('no-bookmarks');
            }
            var flat = [];
            flatten(outline, 1, maxDepth, flat);
            var chain = Promise.resolve();
            var resolved = [];
            flat.forEach(function (f) {
                chain = chain.then(function () {
                    return resolveDest(doc, f.dest).then(function (pg) {
                        if (pg !== null) resolved.push({ title: f.title, page: pg });
                    });
                });
            });
            return chain.then(function () {
                return { doc: doc, resolved: resolved };
            });
        }).then(function (ctx) {
            var doc = ctx.doc;
            var resolved = ctx.resolved;
            if (!resolved.length) throw new Error('no-bookmarks');
            resolved.sort(function (a, b) { return a.page - b.page; });
            var uniq = [];
            var seenPage = {};
            resolved.forEach(function (r) {
                if (!seenPage[r.page]) { seenPage[r.page] = 1; uniq.push(r); }
            });
            if (uniq.length < 1) throw new Error('no-bookmarks');
            var numPages = doc.numPages;
            var parts = [];
            for (var i = 0; i < uniq.length; i++) {
                var start = uniq[i].page;
                var end = (i + 1 < uniq.length) ? uniq[i + 1].page - 1 : numPages - 1;
                if (end >= start) parts.push({ title: uniq[i].title, start: start, end: end });
            }
            if (!parts.length) throw new Error('no-bookmarks');
            return PDFLib.PDFDocument.load(libBytes, { ignoreEncryption: true }).then(function (src) {
                var zip = new JSZip();
                var chain = Promise.resolve();
                parts.forEach(function (part, idx) {
                    chain = chain.then(function () {
                        return PDFLib.PDFDocument.create().then(function (out) {
                            var indices = [];
                            for (var p = part.start; p <= part.end; p++) indices.push(p);
                            return out.copyPages(src, indices).then(function (pgs) {
                                pgs.forEach(function (pg) { out.addPage(pg); });
                                return out.save();
                            }).then(function (bytes) {
                                var fname = sanitize(part.title) + '-part' + (idx + 1) + '.pdf';
                                zip.file(fname, bytes);
                            });
                        });
                    });
                });
                return chain.then(function () { return { zip: zip, parts: parts }; });
            });
        }).then(function (ctx) {
            return ctx.zip.generateAsync({ type: 'blob' }).then(function (blob) {
                return { blob: blob, parts: ctx.parts };
            });
        }).then(function (ctx) {
            if (dlBtn.href && dlBtn.href.indexOf('blob:') === 0) URL.revokeObjectURL(dlBtn.href);
            dlBtn.href = URL.createObjectURL(ctx.blob);
            var base = pdfFile.files[0].name.replace(/\.pdf$/i, '');
            dlBtn.setAttribute('download', base + '-chapters.zip');
            partRows.innerHTML = '';
            ctx.parts.forEach(function (part, i) {
                var tr = document.createElement('tr');
                var tdN = document.createElement('td');
                tdN.textContent = i + 1;
                var tdT = document.createElement('td');
                tdT.textContent = part.title;
                var tdP = document.createElement('td');
                tdP.className = 'text-end';
                tdP.textContent = (part.start + 1) + '–' + (part.end + 1);
                tr.appendChild(tdN); tr.appendChild(tdT); tr.appendChild(tdP);
                partRows.appendChild(tr);
            });
            doneMsg.textContent = ctx.parts.length + ' chapters were split. Download the ZIP.';
            results.classList.remove('d-none');
            goBtn.disabled = false;
            goBtn.textContent = 'Split by Bookmarks';
        }).catch(function (err) {
            goBtn.disabled = false;
            goBtn.textContent = 'Split by Bookmarks';
            if (err && err.message === 'no-bookmarks') {
                showError('No bookmarks found in this PDF. This tool only works on PDFs with bookmarks.');
            } else {
                showError('Something went wrong — please try again.');
            }
        });
    });
})();
</script>
@endsection
