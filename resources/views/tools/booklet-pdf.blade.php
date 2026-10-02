@extends('layouts.app')

@section('title', 'PDF Booklet Maker - Azlaan Tools')
@section('meta_description', 'Rearrange PDF pages into booklet order for double sided printing. Free online imposition tool.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">PDF Booklet Maker</h1>
            <p class="lead text-muted">Put your PDF into booklet order. Two pages side by side on each sheet — print double-sided, then fold and staple. Rearrange pages into booklet imposition for saddle-stitch printing.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="pdfFile" class="form-label fw-semibold">Choose a PDF file</label>
                        <input type="file" class="form-control" id="pdfFile" accept="application/pdf">
                        <div class="form-text">Your PDF is processed right in your browser — it is never uploaded.</div>
                    </div>
                    <div class="mb-3">
                        <label for="bindingEdge" class="form-label fw-semibold">Binding</label>
                        <select class="form-select" id="bindingEdge">
                            <option value="left">Left edge (Urdu/English, left-to-right)</option>
                            <option value="right">Right edge (right-to-left booklet)</option>
                        </select>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Make Booklet PDF</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>
                    <div class="alert alert-info mt-3 d-none" id="statusBox" role="status"></div>

                    <div id="results" class="d-none mt-4">
                        <h5 class="mb-2">Your booklet is ready</h5>
                        <ul class="list-group mb-3" id="bookletInfo"></ul>
                        <a href="#" class="btn btn-success w-100 mb-3" id="dlLink" download="booklet.pdf">Download Booklet PDF</a>
                        <div class="alert alert-secondary">
                            <strong>How to print:</strong>
                            <ol class="mb-0 mt-2">
                                <li>Choose double-sided (duplex) printing, flip on <strong>short edge</strong>.</li>
                                <li>Fold all sheets in half and staple on the spine.</li>
                                <li>The pages will fall into the right order by themselves.</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select your PDF file.</li>
                <li>Choose the binding side (left or right edge).</li>
                <li>Press "Make Booklet PDF" — the new PDF will download in booklet order.</li>
                <li>Print double-sided, then fold and staple.</li>
            </ol>
            <h2>What is booklet order?</h2>
            <p>In an 8-page booklet, sheet 1 has front (pages 8,1) and back (2,7), sheet 2 has front (6,3) and back (4,5). When you print and fold, pages 1,2,3... end up in the right order. If the page count is not a multiple of 4, blank pages are added at the end.</p>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/pdf-lib@1.17.1/dist/pdf-lib.min.js"></script>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var statusBox = document.getElementById('statusBox');
    var results = document.getElementById('results');

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        statusBox.classList.add('d-none');
        results.classList.add('d-none');
    }
    function setStatus(msg) {
        statusBox.textContent = msg;
        statusBox.classList.remove('d-none');
        errorBox.classList.add('d-none');
    }
    function clearStatus() {
        statusBox.classList.add('d-none');
        statusBox.textContent = '';
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }

    function drawFitted(page, embedded, xOff, halfW, pageH) {
        var ew = embedded.width, eh = embedded.height;
        var scale = Math.min(halfW / ew, pageH / eh);
        var dw = ew * scale, dh = eh * scale;
        page.drawPage(embedded, {
            x: xOff + (halfW - dw) / 2,
            y: (pageH - dh) / 2,
            width: dw,
            height: dh
        });
    }

    goBtn.addEventListener('click', function () {
        clearStatus();
        results.classList.add('d-none');
        var fileInput = document.getElementById('pdfFile');
        var file = fileInput.files && fileInput.files[0];
        if (!file) { showError('Please select a PDF file first.'); return; }
        if (typeof PDFLib === 'undefined') { showError('The PDF library did not load. Check your internet and try again.'); return; }
        var binding = document.getElementById('bindingEdge').value;
        setStatus('Reading the PDF, please wait...');

        var reader = new FileReader();
        reader.onerror = function () { showError('There was a problem reading the file.'); };
        reader.onload = function () {
            var bytes;
            try { bytes = new Uint8Array(reader.result); }
            catch (e) { showError('There was a problem reading the file.'); return; }
            PDFLib.PDFDocument.load(bytes, { ignoreEncryption: true }).then(function (src) {
                var srcPages = src.getPages();
                var n0 = srcPages.length;
                if (n0 === 0) { showError('No pages found in this PDF.'); return; }
                var N = Math.ceil(n0 / 4) * 4;
                var blanks = N - n0;
                var w = srcPages[0].getWidth();
                var h = srcPages[0].getHeight();
                setStatus('Arranging booklet order (' + n0 + ' pages -> ' + N + ' pages)...');
                return PDFLib.PDFDocument.create().then(function (out) {
                    var sequence = [];
                    for (var i = 0; i < N / 4; i++) {
                        // standard booklet imposition, 0-indexed
                        sequence.push([N - 1 - 2 * i, 2 * i]);       // sheet front: left, right
                        sequence.push([2 * i + 1, N - 2 - 2 * i]);   // sheet back:  left, right
                    }
                    var chain = Promise.resolve();
                    sequence.forEach(function (pair) {
                        chain = chain.then(function () {
                            var sheet = out.addPage([w * 2, h]);
                            var jobs = [];
                            pair.forEach(function (idx, slot) {
                                if (idx < n0) {
                                    jobs.push(out.embedPage(srcPages[idx]).then(function (emb) {
                                        drawFitted(sheet, emb, slot === 0 ? 0 : w, w, h);
                                    }));
                                }
                            });
                            return Promise.all(jobs);
                        });
                    });
                    return chain.then(function () {
                        if (binding === 'right') {
                            // mirror horizontally: re-embed swapped is complex, instead flip via scale
                            var sheets = out.getPages();
                            sheets.forEach(function (sp) {
                                sp.scale(-1, 1);
                                sp.translate(w * 2, 0);
                            });
                        }
                        out.setTitle('Booklet');
                        out.setAuthor('');
                        out.setSubject('');
                        out.setKeywords([]);
                        out.setProducer('');
                        out.setCreator('');
                        return out.save().then(function (outBytes) {
                            var blob = new Blob([outBytes], { type: 'application/pdf' });
                            var url = URL.createObjectURL(blob);
                            var info = document.getElementById('bookletInfo');
                            info.innerHTML = '';
                            var rows = [
                                'Original pages: ' + n0,
                                'Booklet pages (up to a multiple of 4): ' + N,
                                blanks > 0 ? 'Blank pages added: ' + blanks : 'Blank pages: none',
                                'Sheets (for double-sided printing): ' + (N / 4),
                                'Binding: ' + (binding === 'right' ? 'right edge' : 'left edge')
                            ];
                            rows.forEach(function (t) {
                                var li = document.createElement('li');
                                li.className = 'list-group-item';
                                li.textContent = t;
                                info.appendChild(li);
                            });
                            var dl = document.getElementById('dlLink');
                            dl.href = url;
                            dl.download = 'booklet.pdf';
                            clearStatus();
                            results.classList.remove('d-none');
                            results.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        });
                    });
                });
            }).catch(function (err) {
                var msg = 'The PDF could not be processed.';
                if (err && err.message && /encrypt/i.test(err.message)) msg = 'This PDF is password-protected — unlock it first.';
                showError(msg);
            });
        };
        reader.readAsArrayBuffer(file);
    });
})();
</script>
@endsection
