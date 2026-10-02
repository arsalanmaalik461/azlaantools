@extends('layouts.app')

@section('title', 'PDF Page Labels - Azlaan Tools')
@section('meta_description', 'Set custom PDF page labels like i, ii, iii or A-1 for front matter. Free online tool.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">PDF Page Labels</h1>
            <p class="lead text-muted">Add custom labels to your PDF pages — Roman numbers (i, ii, iii) for front matter and normal numbers for the body. Everything happens in the browser, the file is not uploaded.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="pdfFile" class="form-label fw-semibold">Select PDF file</label>
                        <input type="file" class="form-control" id="pdfFile" accept="application/pdf,.pdf">
                        <div class="form-text">The selected PDF has <span id="pageCount">0</span> pages.</div>
                    </div>

                    <h5 class="mt-4">Label ranges</h5>
                    <div id="rangeList"></div>
                    <button type="button" class="btn btn-outline-secondary btn-sm mt-2" id="addRangeBtn">+ Add range</button>

                    <button type="button" class="btn btn-primary w-100 mt-3" id="goBtn">Add Labels and Download PDF</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="alert alert-success">PDF is ready! Page labels have been added.</div>
                        <a href="#" class="btn btn-success w-100" id="downloadBtn" download="labeled.pdf">Download PDF</a>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select a PDF file — the page count will show automatically.</li>
                <li>Define ranges: from which page to which page, which numbering style (Roman, ABC, or normal), prefix and starting number.</li>
                <li>Press the button — the labeled PDF will download. Labels will show in the PDF viewer's page navigation (thumbnail labels).</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/pdf-lib@1.17.1/dist/pdf-lib.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/pdfjs-dist@3.11.174/build/pdf.min.js"></script>
<script>
(function () {
    'use strict';
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var rangeList = document.getElementById('rangeList');
    var addRangeBtn = document.getElementById('addRangeBtn');
    var pdfFile = document.getElementById('pdfFile');
    var pageCountEl = document.getElementById('pageCount');
    var downloadBtn = document.getElementById('downloadBtn');
    var totalPages = 0;
    var rangeCount = 0;

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }

    function addRangeRow(fromVal, toVal, styleVal, prefixVal, startVal) {
        rangeCount++;
        var id = 'range' + rangeCount;
        var div = document.createElement('div');
        div.className = 'row g-2 align-items-end border rounded p-2 mb-2';
        div.id = id;
        div.innerHTML =
            '<div class="col-6 col-md-2"><label class="form-label small">Page from</label>' +
            '<input type="number" class="form-control form-control-sm rf" min="1" value="' + (fromVal || 1) + '"></div>' +
            '<div class="col-6 col-md-2"><label class="form-label small">Page to</label>' +
            '<input type="number" class="form-control form-control-sm rt" min="1" value="' + (toVal || totalPages || 1) + '"></div>' +
            '<div class="col-6 col-md-3"><label class="form-label small">Style</label>' +
            '<select class="form-control form-control-sm rs">' +
            '<option value="D">Decimal (1, 2, 3)</option>' +
            '<option value="r">Roman lowercase (i, ii, iii)</option>' +
            '<option value="R">Roman UPPERCASE (I, II, III)</option>' +
            '<option value="a">Alpha lowercase (a, b, c)</option>' +
            '<option value="A">Alpha UPPERCASE (A, B, C)</option>' +
            '</select></div>' +
            '<div class="col-6 col-md-2"><label class="form-label small">Prefix</label>' +
            '<input type="text" class="form-control form-control-sm rp" placeholder="e.g. A-" value="' + (prefixVal || '') + '"></div>' +
            '<div class="col-6 col-md-2"><label class="form-label small">Start #</label>' +
            '<input type="number" class="form-control form-control-sm rn" min="1" value="' + (startVal || 1) + '"></div>' +
            '<div class="col-6 col-md-1"><button type="button" class="btn btn-outline-danger btn-sm rdel">&times;</button></div>';
        div.querySelector('.rs').value = styleVal || 'D';
        div.querySelector('.rdel').addEventListener('click', function () { div.remove(); });
        rangeList.appendChild(div);
        return div;
    }

    addRangeBtn.addEventListener('click', function () { addRangeRow(1, totalPages || 1, 'D', '', 1); });

    pdfFile.addEventListener('change', function () {
        hideError();
        var file = pdfFile.files[0];
        if (!file) return;
        var reader = new FileReader();
        reader.onload = function () {
            try {
                var buf = new Uint8Array(reader.result);
                var loadingTask = pdfjsLib.getDocument({ data: buf });
                loadingTask.promise.then(function (pdf) {
                    totalPages = pdf.numPages;
                    pageCountEl.textContent = totalPages;
                    rangeList.innerHTML = '';
                    rangeCount = 0;
                    if (totalPages >= 3) {
                        addRangeRow(1, 2, 'r', '', 1);
                        addRangeRow(3, totalPages, 'D', '', 1);
                    } else {
                        addRangeRow(1, totalPages, 'D', '', 1);
                    }
                }, function () {
                    showError('Please select a valid PDF.');
                });
            } catch (e) { showError('Error reading the file.'); }
        };
        reader.readAsArrayBuffer(file);
    });

    goBtn.addEventListener('click', function () {
        hideError();
        var file = pdfFile.files[0];
        if (!file) { showError('Please select a PDF file first.'); return; }
        if (typeof PDFLib === 'undefined') { showError('The PDF library did not load — check your internet and try again.'); return; }

        var rows = rangeList.querySelectorAll('.row');
        if (!rows.length) { showError('Add at least one label range.'); return; }

        var ranges = [];
        for (var i = 0; i < rows.length; i++) {
            var from = parseInt(rows[i].querySelector('.rf').value, 10);
            var to = parseInt(rows[i].querySelector('.rt').value, 10);
            var style = rows[i].querySelector('.rs').value;
            var prefix = rows[i].querySelector('.rp').value.trim();
            var start = parseInt(rows[i].querySelector('.rn').value, 10) || 1;
            if (isNaN(from) || isNaN(to) || from < 1 || to < from || to > totalPages) {
                showError('Range is wrong: page ' + from + '-' + to + '. (Total pages: ' + totalPages + ')'); return;
            }
            if (!/^[0-9A-Za-z\-_ ]{0,10}$/.test(prefix)) { showError('Prefix can only contain English letters, digits, - or _.'); return; }
            ranges.push({ from: from, to: to, style: style, prefix: prefix, start: start });
        }
        ranges.sort(function (a, b) { return a.from - b.from; });
        for (var j = 1; j < ranges.length; j++) {
            if (ranges[j].from <= ranges[j - 1].to) { showError('Ranges overlap — check the from/to pages.'); return; }
        }

        var reader2 = new FileReader();
        reader2.onload = function () {
            (async function () {
                try {
                    var src = await PDFLib.PDFDocument.load(new Uint8Array(reader2.result).slice(0));
                    var nums = [];
                    ranges.forEach(function (r) {
                        var dict = { S: PDFLib.PDFName.of(r.style) };
                        if (r.prefix) dict.P = r.prefix;
                        if (r.start !== 1) dict.St = r.start;
                        nums.push(r.from - 1);
                        nums.push(src.context.obj(dict));
                    });
                    var labelsDict = src.context.obj({ Nums: nums });
                    src.catalog.set(PDFLib.PDFName.of('PageLabels'), labelsDict);
                    var bytes = await src.save();
                    var blob = new Blob([bytes], { type: 'application/pdf' });
                    var url = URL.createObjectURL(blob);
                    downloadBtn.href = url;
                    downloadBtn.download = file.name.replace(/\.pdf$/i, '') + '-labeled.pdf';
                    results.classList.remove('d-none');
                    results.scrollIntoView({ behavior: 'smooth', block: 'center' });
                } catch (e) {
                    showError('The PDF could not be processed. Is the file corrupt?');
                }
            })();
        };
        reader2.readAsArrayBuffer(file);
    });

    if (typeof pdfjsLib !== 'undefined') {
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdn.jsdelivr.net/npm/pdfjs-dist@3.11.174/build/pdf.worker.min.js';
    }
})();
</script>
@endsection
