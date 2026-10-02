@extends('layouts.app')

@section('title', 'Expand PDF Margins - Azlaan Tools')
@section('meta_description', 'Add white margin space around PDF pages for binding and notes. Free online.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Expand PDF Margins</h1>
            <p class="lead text-muted">Add white margin space around every page — make room for binding, notes or annotations. The PDF stays the same, only the page grows bigger.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="pdfFile" class="form-label fw-semibold">Choose a PDF file</label>
                        <input type="file" class="form-control" id="pdfFile" accept="application/pdf">
                        <div class="form-text">Your file stays in your browser — it is never uploaded.</div>
                    </div>
                    <div id="toolWrap" class="d-none">
                        <div class="row g-2 mb-3">
                            <div class="col-6 col-md-3">
                                <label for="mTop" class="form-label">Top (pt)</label>
                                <input type="number" class="form-control" id="mTop" value="36" min="0" max="500">
                            </div>
                            <div class="col-6 col-md-3">
                                <label for="mBottom" class="form-label">Bottom (pt)</label>
                                <input type="number" class="form-control" id="mBottom" value="36" min="0" max="500">
                            </div>
                            <div class="col-6 col-md-3">
                                <label for="mLeft" class="form-label">Left (pt)</label>
                                <input type="number" class="form-control" id="mLeft" value="36" min="0" max="500">
                            </div>
                            <div class="col-6 col-md-3">
                                <label for="mRight" class="form-label">Right (pt)</label>
                                <input type="number" class="form-control" id="mRight" value="36" min="0" max="500">
                            </div>
                        </div>
                        <div class="form-text mb-2">72 pt = 1 inch. For example, use 72 pt on the left side for binding.</div>
                        <div id="pageInfo" class="alert alert-light border small mb-3 d-none"></div>
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="button" class="btn btn-primary flex-grow-1" id="goBtn">Expand Margins</button>
                            <button type="button" class="btn btn-outline-secondary" id="clearBtn">Clear</button>
                        </div>
                        <div class="progress mt-3 d-none" id="progressWrap">
                            <div class="progress-bar" id="progressBar" style="width:0%">0%</div>
                        </div>
                    </div>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>
                    <div id="results" class="d-none mt-4"></div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select your PDF file.</li>
                <li>Type how much margin you want on each side, in points (72 pt = 1 inch).</li>
                <li>Press "Expand Margins" and download the new PDF.</li>
            </ol>
            <p class="text-muted small">In the new PDF, every page will have extra white space around it; the existing content stays in its place.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/pdf-lib@1.17.1/dist/pdf-lib.min.js"></script>
<script>
(function () {
    'use strict';
    var pdfFile = document.getElementById('pdfFile');
    var toolWrap = document.getElementById('toolWrap');
    var pageInfo = document.getElementById('pageInfo');
    var goBtn = document.getElementById('goBtn');
    var clearBtn = document.getElementById('clearBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var progressWrap = document.getElementById('progressWrap');
    var progressBar = document.getElementById('progressBar');
    var storedBuffer = null;
    var totalPages = 0;

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function setProgress(p) {
        p = Math.max(0, Math.min(100, Math.round(p)));
        progressBar.style.width = p + '%';
        progressBar.textContent = p + '%';
    }

    pdfFile.addEventListener('change', function () {
        hideError();
        results.classList.add('d-none');
        var f = pdfFile.files[0];
        if (!f) { toolWrap.classList.add('d-none'); storedBuffer = null; return; }
        var reader = new FileReader();
        reader.onload = function () {
            storedBuffer = reader.result;
            totalPages = 0;
            pageInfo.classList.add('d-none');
            toolWrap.classList.remove('d-none');
            pageInfo.innerHTML = '<strong>' + f.name.replace(/</g, '&lt;') + '</strong> loaded. Enter the margins, then press Expand.';
            pageInfo.classList.remove('d-none');
        };
        reader.onerror = function () { showError('There was a problem reading the file. Please try again.'); };
        reader.readAsArrayBuffer(f);
    });

    clearBtn.addEventListener('click', function () {
        pdfFile.value = '';
        storedBuffer = null;
        totalPages = 0;
        toolWrap.classList.add('d-none');
        pageInfo.classList.add('d-none');
        hideError();
        results.classList.add('d-none');
    });

    function getMargins() {
        return {
            top: Math.max(0, parseFloat(document.getElementById('mTop').value) || 0),
            bottom: Math.max(0, parseFloat(document.getElementById('mBottom').value) || 0),
            left: Math.max(0, parseFloat(document.getElementById('mLeft').value) || 0),
            right: Math.max(0, parseFloat(document.getElementById('mRight').value) || 0)
        };
    }

    goBtn.addEventListener('click', async function () {
        hideError();
        if (!storedBuffer) { showError('Please select a PDF file first.'); return; }
        if (typeof PDFLib === 'undefined') { showError('The PDF library failed to load. Check your internet connection and try again.'); return; }
        var m = getMargins();
        if (m.top + m.bottom + m.left + m.right <= 0) {
            showError('Please enter at least one margin greater than 0.');
            return;
        }
        goBtn.disabled = true;
        goBtn.textContent = 'Expanding...';
        progressWrap.classList.remove('d-none');
        setProgress(5);
        results.classList.add('d-none');
        try {
            var pdfDoc = await PDFLib.PDFDocument.load(storedBuffer.slice(0));
            var pages = pdfDoc.getPages();
            for (var i = 0; i < pages.length; i++) {
                var page = pages[i];
                var size = page.getSize();
                var newW = size.width + m.left + m.right;
                var newH = size.height + m.top + m.bottom;
                // Push the MediaBox outward; the content stays at its own coordinates.
                if (typeof page.setMediaBox === 'function') {
                    page.setMediaBox(-m.left, -m.bottom, newW, newH);
                } else {
                    page.setCropBox(-m.left, -m.bottom, newW, newH);
                }
                setProgress(5 + Math.round(((i + 1) / pages.length) * 90));
            }
            var outBytes = await pdfDoc.save();
            setProgress(100);
            var blob = new Blob([outBytes], { type: 'application/pdf' });
            var url = URL.createObjectURL(blob);
            var kb = (blob.size / 1024).toFixed(1);
            results.innerHTML =
                '<div class="alert alert-success"><strong>Done!</strong> ' + pages.length +
                ' pages had their margins expanded (Top ' + m.top + ', Bottom ' + m.bottom +
                ', Left ' + m.left + ', Right ' + m.right + ' pt). File size: ' + kb + ' KB.</div>' +
                '<a class="btn btn-success w-100" href="' + url + '" download="expanded-margins.pdf">Download PDF</a>';
            results.classList.remove('d-none');
        } catch (e) {
            showError('Could not process the PDF: ' + (e && e.message ? e.message : 'unknown error'));
        }
        goBtn.disabled = false;
        goBtn.textContent = 'Expand Margins';
    });
})();
</script>
@endsection
