@extends('layouts.app')

@section('title', 'Extract PDF Form Fields - Azlaan Tools')
@section('meta_description', 'Extract all form field names and values from a fillable PDF into a list or CSV, free online.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Extract PDF Form Fields</h1>
            <p class="lead text-muted">Export every form field from a fillable PDF (AcroForm) as a list or CSV.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="pdfFile" class="form-label fw-semibold">Choose a PDF file</label>
                        <input type="file" class="form-control" id="pdfFile" accept="application/pdf,.pdf">
                        <div class="form-text">Select a PDF that has a fillable form. The file is processed in your browser — nothing is uploaded.</div>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Extract Fields</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="alert alert-success" id="summaryBox"></div>
                        <div class="table-responsive mb-3">
                            <table class="table table-striped table-sm" id="fieldsTable">
                                <thead class="table-light">
                                    <tr><th>#</th><th>Page</th><th>Field Name</th><th>Type</th><th>Value</th></tr>
                                </thead>
                                <tbody id="fieldsBody"></tbody>
                            </table>
                        </div>
                        <button type="button" class="btn btn-outline-primary me-2" id="csvBtn">Download CSV</button>
                        <button type="button" class="btn btn-outline-secondary" id="copyBtn">Copy as Text</button>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select your fillable PDF file.</li>
                <li>Click <strong>Extract Fields</strong> — a list of all form fields (text boxes, checkboxes, radio buttons, dropdowns) will be created.</li>
                <li>View the list in the table, or save it with <strong>Download CSV</strong> to open in Excel.</li>
            </ol>
            <p class="text-muted small">Only fillable (AcroForm) PDFs have extractable fields. Scanned or image PDFs have no fields.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/pdfjs-dist@3.11.174/build/pdf.min.js"></script>
<script>
(function () {
    'use strict';
    var pdfFile = document.getElementById('pdfFile');
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var fieldsBody = document.getElementById('fieldsBody');
    var summaryBox = document.getElementById('summaryBox');
    var csvBtn = document.getElementById('csvBtn');
    var copyBtn = document.getElementById('copyBtn');
    var rows = [];
    var totalPages = 0;

    if (window.pdfjsLib) {
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdn.jsdelivr.net/npm/pdfjs-dist@3.11.174/build/pdf.worker.min.js';
    }

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function csvCell(v) {
        v = String(v == null ? '' : v);
        if (v.indexOf(',') >= 0 || v.indexOf('"') >= 0 || v.indexOf('\n') >= 0) {
            return '"' + v.replace(/"/g, '""') + '"';
        }
        return v;
    }

    goBtn.addEventListener('click', function () {
        hideError();
        results.classList.add('d-none');
        var file = pdfFile.files && pdfFile.files[0];
        if (!file) { showError('Please choose a PDF file first.'); return; }
        if (!window.pdfjsLib) { showError('The PDF library failed to load. Check your internet and try again.'); return; }
        goBtn.disabled = true;
        goBtn.textContent = 'Reading...';

        var reader = new FileReader();
        reader.onload = function () {
            var bytes = new Uint8Array(reader.result);
            pdfjsLib.getDocument({ data: bytes }).promise.then(function (doc) {
                totalPages = doc.numPages;
                var pagePromises = [];
                for (var p = 1; p <= total; p++) {
                    (function (pageNum) {
                        pagePromises.push(
                            doc.getPage(pageNum).then(function (page) {
                                return page.getAnnotations().then(function (annots) {
                                    var out = [];
                                    for (var i = 0; i < annots.length; i++) {
                                        var a = annots[i];
                                        if (a.subtype !== 'Widget' || !a.fieldName) { continue; }
                                        var val = '';
                                        if (a.fieldValue !== undefined && a.fieldValue !== null) {
                                            if (typeof a.fieldValue === 'string') { val = a.fieldValue; }
                                            else if (Array.isArray(a.fieldValue)) { val = a.fieldValue.join(', '); }
                                            else if (typeof a.fieldValue === 'object') { val = JSON.stringify(a.fieldValue); }
                                            else { val = String(a.fieldValue); }
                                        }
                                        if (a.checkBox && a.fieldValue === true) { val = 'Checked'; }
                                        out.push({
                                            page: pageNum,
                                            name: a.fieldName,
                                            type: a.fieldType || (a.checkBox ? 'checkbox' : 'field'),
                                            value: val
                                        });
                                    }
                                    return out;
                                });
                            })
                        );
                    })(p);
                }
                return Promise.all(pagePromises);
            }).then(function (perPage) {
                rows = [];
                perPage.forEach(function (list) { rows = rows.concat(list); });
                goBtn.disabled = false;
                goBtn.textContent = 'Extract Fields';
                if (rows.length === 0) {
                    showError('No fillable form fields found in this PDF. Only fillable (AcroForm) PDFs are supported.');
                    return;
                }
                fieldsBody.innerHTML = '';
                rows.forEach(function (r, idx) {
                    var tr = document.createElement('tr');
                    var tdN = document.createElement('td'); tdN.textContent = idx + 1;
                    var tdP = document.createElement('td'); tdP.textContent = r.page;
                    var tdName = document.createElement('td'); tdName.textContent = r.name;
                    var tdT = document.createElement('td'); tdT.textContent = r.type;
                    var tdV = document.createElement('td'); tdV.textContent = r.value;
                    tr.appendChild(tdN); tr.appendChild(tdP); tr.appendChild(tdName); tr.appendChild(tdT); tr.appendChild(tdV);
                    fieldsBody.appendChild(tr);
                });
                summaryBox.textContent = rows.length + ' form fields found (' + totalPages + ' pages).';
                results.classList.remove('d-none');
            }).catch(function (err) {
                goBtn.disabled = false;
                goBtn.textContent = 'Extract Fields';
                showError('There was a problem reading the PDF: ' + (err && err.message ? err.message : 'unknown error'));
            });
        };
        reader.onerror = function () {
            goBtn.disabled = false;
            goBtn.textContent = 'Extract Fields';
            showError('There was a problem reading the file. Try again.');
        };
        reader.readAsArrayBuffer(file);
    });

    csvBtn.addEventListener('click', function () {
        if (!rows.length) { return; }
        var lines = ['Page,Field Name,Type,Value'];
        rows.forEach(function (r) {
            lines.push([r.page, csvCell(r.name), csvCell(r.type), csvCell(r.value)].join(','));
        });
        var blob = new Blob([lines.join('\r\n')], { type: 'text/csv;charset=utf-8' });
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'pdf-form-fields.csv';
        document.body.appendChild(a);
        a.click();
        setTimeout(function () { URL.revokeObjectURL(a.href); a.remove(); }, 1000);
    });

    copyBtn.addEventListener('click', function () {
        if (!rows.length) { return; }
        var text = rows.map(function (r, i) { return (i + 1) + '. [' + r.type + '] ' + r.name + ' = ' + r.value + ' (page ' + r.page + ')'; }).join('\n');
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(function () { copyBtn.textContent = 'Copied!'; setTimeout(function () { copyBtn.textContent = 'Copy as Text'; }, 1500); });
        }
    });
})();
</script>
@endsection
