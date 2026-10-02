@extends('layouts.app')

@section('title', 'PDF Certificate Maker - Azlaan Tools')
@section('meta_description', 'Generate personalized PDF certificates from a list of names. Free online bulk certificate maker.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">PDF Certificate Maker</h1>
            <p class="lead text-muted">Make personalized certificates from a list of names. Download one PDF with a separate page for each name — completely free.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="certTitle" class="form-label fw-semibold">Certificate Title</label>
                        <input type="text" class="form-control" id="certTitle" placeholder="e.g. Certificate of Appreciation" value="Certificate of Appreciation">
                    </div>
                    <div class="mb-3">
                        <label for="orgName" class="form-label fw-semibold">Presented By (Organization)</label>
                        <input type="text" class="form-control" id="orgName" placeholder="e.g. Azlaan Academy">
                    </div>
                    <div class="mb-3">
                        <label for="certDate" class="form-label fw-semibold">Date</label>
                        <input type="date" class="form-control" id="certDate">
                    </div>
                    <div class="mb-3">
                        <label for="nameList" class="form-label fw-semibold">Names (one per line)</label>
                        <textarea class="form-control" id="nameList" rows="6" placeholder="Ali Khan&#10;Fatima Raza&#10;Muhammad Usman"></textarea>
                        <div class="form-text">Write one name per line — one certificate per name.</div>
                    </div>
                    <div class="mb-3">
                        <label for="borderColor" class="form-label fw-semibold">Border Color</label>
                        <select class="form-select" id="borderColor">
                            <option value="navy">Navy Blue</option>
                            <option value="green">Green</option>
                            <option value="maroon">Maroon</option>
                            <option value="gold">Gold</option>
                        </select>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Generate PDF Certificates</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="alert alert-success" id="okBox" role="alert"></div>
                        <div class="table-responsive">
                            <table class="table table-sm table-striped">
                                <thead><tr><th>#</th><th>Name</th><th>Page</th></tr></thead>
                                <tbody id="nameTable"></tbody>
                            </table>
                        </div>
                        <a href="#" class="btn btn-success w-100" id="downloadLink">Download PDF</a>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Enter the certificate title, organization name and date.</li>
                <li>Write one name per line in the names box.</li>
                <li>Select the border color and press Generate, then download the PDF.</li>
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
    var okBox = document.getElementById('okBox');
    var nameTable = document.getElementById('nameTable');
    var downloadLink = document.getElementById('downloadLink');

    var COLORS = {
        navy: { r: 0.09, g: 0.16, b: 0.36 },
        green: { r: 0.10, g: 0.42, b: 0.22 },
        maroon: { r: 0.45, g: 0.08, b: 0.12 },
        gold: { r: 0.55, g: 0.40, b: 0.05 }
    };

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function fitFont(font, text, maxWidth, startSize) {
        var size = startSize;
        while (size > 12 && font.widthOfTextAtSize(text, size) > maxWidth) {
            size -= 2;
        }
        return size;
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var names = document.getElementById('nameList').value
            .split('\n').map(function (n) { return n.trim(); })
            .filter(function (n) { return n.length > 0; });
        if (!names.length) { showError('Please enter at least one name.'); return; }
        if (names.length > 200) { showError('Maximum 200 names at a time.'); return; }
        var title = document.getElementById('certTitle').value.trim() || 'Certificate of Appreciation';
        var org = document.getElementById('orgName').value.trim() || '';
        var dateVal = document.getElementById('certDate').value;
        var dateStr = dateVal ? new Date(dateVal + 'T00:00:00').toLocaleDateString('en-GB', { day: 'numeric', month: 'long', year: 'numeric' }) : '';
        var col = COLORS[document.getElementById('borderColor').value] || COLORS.navy;

        if (!window.PDFLib) { showError('PDF library failed to load. Check your internet connection.'); return; }
        var PDFDocument = window.PDFLib.PDFDocument;
        var rgb = window.PDFLib.rgb;
        var StandardFonts = window.PDFLib.StandardFonts;

        PDFDocument.create().then(function (pdfDoc) {
            return pdfDoc.embedFont(StandardFonts.TimesRoman).then(function (serif) {
                return pdfDoc.embedFont(StandardFonts.TimesRomanBold).then(function (serifBold) {
                    return pdfDoc.embedFont(StandardFonts.Helvetica).then(function (sans) {
                        return { pdfDoc: pdfDoc, serif: serif, serifBold: serifBold, sans: sans };
                    });
                });
            });
        }).then(function (ctx) {
            var pdfDoc = ctx.pdfDoc;
            names.forEach(function (name) {
                var page = pdfDoc.addPage([841.89, 595.28]); // A4 landscape
                var w = 841.89, h = 595.28;
                // border
                page.drawRectangle({ x: 25, y: 25, width: w - 50, height: h - 50, borderColor: rgb(col.r, col.g, col.b), borderWidth: 5 });
                page.drawRectangle({ x: 40, y: 40, width: w - 80, height: h - 80, borderColor: rgb(col.r, col.g, col.b), borderWidth: 1.5 });
                // title
                var ts = fitFont(ctx.serifBold, title, w - 220, 44);
                page.drawText(title, { x: w / 2 - ctx.serifBold.widthOfTextAtSize(title, ts) / 2, y: h - 150, size: ts, font: ctx.serifBold, color: rgb(col.r, col.g, col.b) });
                page.drawText('This certificate is proudly presented to', { x: w / 2 - ctx.sans.widthOfTextAtSize('This certificate is proudly presented to', 15) / 2, y: h - 210, size: 15, font: ctx.sans, color: rgb(0.3, 0.3, 0.3) });
                // name
                var ns = fitFont(ctx.serifBold, name, w - 260, 52);
                page.drawText(name, { x: w / 2 - ctx.serifBold.widthOfTextAtSize(name, ns) / 2, y: h - 300, size: ns, font: ctx.serifBold, color: rgb(0.1, 0.1, 0.1) });
                // decorative line under name
                page.drawLine({ start: { x: w / 2 - 180, y: h - 315 }, end: { x: w / 2 + 180, y: h - 315 }, thickness: 2, color: rgb(col.r, col.g, col.b) });
                page.drawText('In recognition of outstanding achievement and dedication.', { x: w / 2 - ctx.sans.widthOfTextAtSize('In recognition of outstanding achievement and dedication.', 13) / 2, y: h - 350, size: 13, font: ctx.sans, color: rgb(0.35, 0.35, 0.35) });
                // footer
                if (dateStr) {
                    page.drawText(dateStr, { x: 90, y: 90, size: 13, font: ctx.sans, color: rgb(0.2, 0.2, 0.2) });
                    page.drawLine({ start: { x: 90, y: 108 }, end: { x: 240, y: 108 }, thickness: 1, color: rgb(0.2, 0.2, 0.2) });
                    page.drawText('Date', { x: 150, y: 72, size: 11, font: ctx.sans, color: rgb(0.4, 0.4, 0.4) });
                }
                if (org) {
                    var ow = ctx.sans.widthOfTextAtSize(org, 13);
                    page.drawText(org, { x: w - 90 - ow, y: 90, size: 13, font: ctx.sans, color: rgb(0.2, 0.2, 0.2) });
                    page.drawLine({ start: { x: w - 240, y: 108 }, end: { x: w - 90, y: 108 }, thickness: 1, color: rgb(0.2, 0.2, 0.2) });
                    page.drawText('Organization', { x: w - 190, y: 72, size: 11, font: ctx.sans, color: rgb(0.4, 0.4, 0.4) });
                }
            });
            return pdfDoc.save();
        }).then(function (bytes) {
            var blob = new Blob([bytes], { type: 'application/pdf' });
            var url = URL.createObjectURL(blob);
            downloadLink.href = url;
            downloadLink.download = 'certificates.pdf';
            nameTable.innerHTML = '';
            names.forEach(function (name, i) {
                var tr = document.createElement('tr');
                var c1 = document.createElement('td'); c1.textContent = i + 1;
                var c2 = document.createElement('td'); c2.textContent = name;
                var c3 = document.createElement('td'); c3.textContent = 'Page ' + (i + 1);
                tr.appendChild(c1); tr.appendChild(c2); tr.appendChild(c3);
                nameTable.appendChild(tr);
            });
            okBox.textContent = names.length + ' certificates generated. The PDF was made in your browser — no data was sent to a server.';
            results.classList.remove('d-none');
        }).catch(function (e) {
            showError('The PDF could not be generated. Please try again.');
        });
    });
})();
</script>
@endsection
