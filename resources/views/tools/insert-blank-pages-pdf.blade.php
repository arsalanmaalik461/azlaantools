@extends('layouts.app')

@section('title', 'Insert Blank Pages in PDF - Azlaan Tools')
@section('meta_description', 'Add blank pages anywhere in a PDF for notes or duplex printing, free online, all in your browser.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Insert Blank Pages in PDF</h1>
            <p class="lead text-muted">Add blank pages to a PDF — for notes or duplex printing. Your file is processed only in your browser, never uploaded.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="pdfFile" class="form-label fw-semibold">PDF file</label>
                        <input type="file" class="form-control" id="pdfFile" accept=".pdf,application/pdf">
                    </div>
                    <div class="mb-3">
                        <label for="positions" class="form-label fw-semibold">Insert blank page AFTER page numbers</label>
                        <input type="text" class="form-control" id="positions" placeholder="e.g. 2, 5, 8  (0 = at the very start)">
                        <div class="form-text">Write page numbers separated by commas. Writing 0 puts a blank page at the very start of the file.</div>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label for="blankCount" class="form-label fw-semibold">Pages per position</label>
                            <input type="number" class="form-control" id="blankCount" min="1" max="10" value="1">
                        </div>
                        <div class="col-6 mb-3">
                            <label for="addEnd" class="form-label fw-semibold">Also add at end</label>
                            <select class="form-select" id="addEnd">
                                <option value="0">No</option>
                                <option value="1">1 blank page at end</option>
                                <option value="2">2 blank pages at end</option>
                            </select>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Insert Blank Pages</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="alert alert-success" id="successMsg"></div>
                        <a class="btn btn-success w-100" id="downloadBtn" href="#" download="with-blank-pages.pdf">Download PDF</a>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select your PDF file.</li>
                <li>Write the page numbers AFTER which you want a blank page, separated by commas (0 = at the very start).</li>
                <li>Press "Insert Blank Pages" and download the new PDF.</li>
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
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var downloadBtn = document.getElementById('downloadBtn');
    var successMsg = document.getElementById('successMsg');
    var lastUrl = null;

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }

    goBtn.addEventListener('click', function () {
        hideError();
        results.classList.add('d-none');
        var fileInput = document.getElementById('pdfFile');
        var positionsRaw = document.getElementById('positions').value.trim();
        var blankCount = parseInt(document.getElementById('blankCount').value, 10);
        var addEnd = parseInt(document.getElementById('addEnd').value, 10);

        if (!fileInput.files || !fileInput.files[0]) {
            showError('Please select a PDF file.');
            return;
        }
        if (isNaN(blankCount) || blankCount < 1 || blankCount > 10) {
            showError('Pages per position must be between 1 and 10.');
            return;
        }

        var reader = new FileReader();
        reader.onload = function () {
            var bytes;
            try {
                bytes = new Uint8Array(reader.result);
            } catch (e) {
                showError('Could not read the file. Please try again.');
                return;
            }
            processPdf(bytes, positionsRaw, blankCount, addEnd).catch(function (err) {
                showError('Error: ' + (err && err.message ? err.message : 'could not process PDF'));
            });
        };
        reader.onerror = function () {
            showError('Could not read the file. Please try again.');
        };
        reader.readAsArrayBuffer(fileInput.files[0]);
    });

    function processPdf(bytes, positionsRaw, blankCount, addEnd) {
        return PDFLib.PDFDocument.load(bytes).then(function (pdfDoc) {
            var pageCount = pdfDoc.getPageCount();
            var positions = [];

            if (positionsRaw) {
                var parts = positionsRaw.split(',');
                for (var i = 0; i < parts.length; i++) {
                    var n = parseInt(parts[i].trim(), 10);
                    if (isNaN(n) || n < 0 || n > pageCount) {
                        throw new Error('Invalid page number "' + parts[i].trim() + '". File has ' + pageCount + ' pages (use 0-' + pageCount + ').');
                    }
                    if (positions.indexOf(n) === -1) positions.push(n);
                }
                if (positions.length === 0 && addEnd === 0) {
                    throw new Error('No valid page number found. Write page numbers separated by commas, e.g. 2, 5, 8.');
                }
            }

            // Insert from the end backwards so indexes stay valid.
            positions.sort(function (a, b) { return b - a; });
            var inserted = 0;

            function insertBlankAt(afterPage) {
                // afterPage: insert blank AFTER this page number (0 = before first page)
                var refIndex = Math.min(afterPage, pageCount - 1);
                var refPage = pdfDoc.getPage(refIndex);
                var size = refPage.getSize();
                for (var k = 0; k < blankCount; k++) {
                    pdfDoc.insertPage(afterPage + k, [size.width, size.height]);
                    inserted++;
                }
            }

            for (var j = 0; j < positions.length; j++) {
                insertBlankAt(positions[j]);
            }
            for (var e = 0; e < addEnd; e++) {
                insertBlankAt(pdfDoc.getPageCount() - 1);
            }

            if (inserted === 0) {
                throw new Error('No blank page was inserted. Please check the page numbers.');
            }

            return pdfDoc.save();
        }).then(function (outBytes) {
            if (lastUrl) { URL.revokeObjectURL(lastUrl); }
            var blob = new Blob([outBytes], { type: 'application/pdf' });
            lastUrl = URL.createObjectURL(blob);
            downloadBtn.href = lastUrl;
            successMsg.textContent = inserted.toString() + ' blank page(s) added.';
            results.classList.remove('d-none');
        });
    }
})();
</script>
@endsection
