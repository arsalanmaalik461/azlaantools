@extends('layouts.app')

@section('title', 'PDF Background Color - Azlaan Tools')
@section('meta_description', 'Add a soft solid color background to every PDF page for comfortable reading. Free online tool.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">PDF Background Color</h1>
            <p class="lead text-muted">Add a soft color background to every page of your PDF — easy on the eyes for long reading. Upload the PDF, pick a color, and download it.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="pdfFile" class="form-label fw-semibold">Choose PDF file</label>
                        <input type="file" class="form-control" id="pdfFile" accept="application/pdf">
                        <div class="form-text">The file is processed in your browser only — it is not uploaded to a server.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold d-block">Background color</label>
                        <div id="swatches" class="d-flex flex-wrap gap-2 mb-2"></div>
                        <div class="d-flex align-items-center gap-2">
                            <input type="color" class="form-control form-control-color" id="bgColor" value="#fff7e6" title="Custom color">
                            <label for="bgColor" class="form-label mb-0 small text-muted">Or choose your own color</label>
                        </div>
                    </div>

                    <button type="button" class="btn btn-primary w-100" id="goBtn">Apply &amp; Download PDF</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="alert alert-success" id="doneBox" role="alert"></div>
                        <a class="btn btn-success w-100" id="downloadLink" href="#" download="background.pdf">Download PDF</a>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select your PDF file.</li>
                <li>Pick one of the ready colors below, or use the custom color picker to make your own.</li>
                <li>Press <strong>Apply &amp; Download PDF</strong> — the new PDF will download with color applied on every page.</li>
            </ol>
            <p class="text-muted small">Note: this color goes behind the pages; on pages that are already fully colored, the change will be less visible.</p>
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
    var swatches = document.getElementById('swatches');
    var bgColor = document.getElementById('bgColor');
    var selected = '#fff7e6';

    var presets = [
        ['Cream', '#fff7e6'],
        ['Peach', '#ffeedd'],
        ['Light blue', '#e8f4ff'],
        ['Light green', '#eafbea'],
        ['Warm gray', '#f5f0e8'],
        ['Lavender', '#f1ecfd'],
        ['Rose', '#fdeef2'],
        ['White (default)', '#ffffff']
    ];

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }

    function hexToRgb(hex) {
        hex = hex.replace('#', '');
        return {
            r: parseInt(hex.substr(0, 2), 16) / 255,
            g: parseInt(hex.substr(2, 2), 16) / 255,
            b: parseInt(hex.substr(4, 2), 16) / 255
        };
    }

    presets.forEach(function (p, i) {
        var btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'btn btn-sm' + (i === 0 ? ' btn-primary' : ' btn-outline-secondary');
        btn.style.width = '34px';
        btn.style.height = '34px';
        btn.style.padding = '0';
        btn.style.backgroundColor = p[1];
        btn.style.borderColor = '#bbb';
        btn.title = p[0];
        btn.setAttribute('aria-label', p[0]);
        btn.addEventListener('click', function () {
            selected = p[1];
            bgColor.value = p[1];
            var all = swatches.querySelectorAll('button');
            for (var k = 0; k < all.length; k++) {
                all[k].classList.remove('btn-primary');
                all[k].classList.add('btn-outline-secondary');
            }
            btn.classList.remove('btn-outline-secondary');
            btn.classList.add('btn-primary');
        });
        swatches.appendChild(btn);
    });

    bgColor.addEventListener('input', function () {
        selected = bgColor.value;
    });

    goBtn.addEventListener('click', async function () {
        hideError();
        results.classList.add('d-none');

        var fileInput = document.getElementById('pdfFile');
        if (!fileInput.files || !fileInput.files[0]) {
            showError('Please choose a PDF file first.');
            return;
        }
        if (typeof PDFLib === 'undefined') {
            showError('The PDF library did not load. Check your internet and try again.');
            return;
        }

        goBtn.disabled = true;
        goBtn.textContent = 'Processing...';

        try {
            var buf = await fileInput.files[0].arrayBuffer();
            var src = await PDFLib.PDFDocument.load(buf);
            var out = await PDFLib.PDFDocument.create();
            var c = hexToRgb(selected);
            var n = src.getPageCount();

            for (var i = 0; i < n; i++) {
                var page = src.getPage(i);
                var w = page.getWidth();
                var h = page.getHeight();
                var embedded = await out.embedPage(page);
                var newPage = out.addPage([w, h]);
                newPage.drawRectangle({ x: 0, y: 0, width: w, height: h, color: PDFLib.rgb(c.r, c.g, c.b) });
                newPage.drawPage(embedded, { x: 0, y: 0, width: w, height: h });
            }

            var bytes = await out.save();
            var blob = new Blob([bytes], { type: 'application/pdf' });
            var url = URL.createObjectURL(blob);
            var link = document.getElementById('downloadLink');
            var base = fileInput.files[0].name.replace(/\.pdf$/i, '');
            link.href = url;
            link.download = base + '-background.pdf';

            var doneBox = document.getElementById('doneBox');
            doneBox.textContent = 'Done! Background added to ' + n + ' pages.';
            results.classList.remove('d-none');
        } catch (e) {
            showError('The PDF could not be processed. The file may be damaged or password-protected.');
        }

        goBtn.disabled = false;
        goBtn.textContent = 'Apply & Download PDF';
    });
})();
</script>
@endsection
