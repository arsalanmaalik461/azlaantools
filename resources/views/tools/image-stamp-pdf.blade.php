@extends('layouts.app')
@section('title', 'Image Stamp on PDF - Add Logo or Signature to PDF | Azlaan Tools')
@section('meta_description', 'Stamp your logo or signature image on every page of a PDF for free. Choose position, size and opacity — files are processed in your browser, no upload.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Image Stamp on PDF</h1>
            <p class="lead text-muted">Stamp your logo or signature image on every page of a PDF. Set the position, size and opacity yourself — the file never leaves your browser.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="pdfInput" class="form-label fw-semibold">1. Select PDF file</label>
                        <input type="file" class="form-control" id="pdfInput" accept="application/pdf,.pdf">
                    </div>
                    <div class="mb-3">
                        <label for="imgInput" class="form-label fw-semibold">2. Logo / signature image (PNG or JPG)</label>
                        <input type="file" class="form-control" id="imgInput" accept="image/png,image/jpeg,.png,.jpg,.jpeg">
                        <div class="form-text">A transparent PNG works best.</div>
                    </div>
                    <div id="imgPreviewWrap" class="mb-3 d-none">
                        <img id="imgPreview" alt="Stamp preview" class="img-thumbnail" style="max-height:120px;">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">3. Position (where the stamp goes)</label>
                        <div class="d-flex flex-wrap gap-2" id="posGrid">
                            <button type="button" class="btn btn-outline-primary pos-btn" data-pos="tl">Top Left</button>
                            <button type="button" class="btn btn-outline-primary pos-btn" data-pos="tc">Top Center</button>
                            <button type="button" class="btn btn-outline-primary pos-btn" data-pos="tr">Top Right</button>
                            <button type="button" class="btn btn-outline-primary pos-btn" data-pos="ml">Middle Left</button>
                            <button type="button" class="btn btn-outline-primary pos-btn" data-pos="mc">Center</button>
                            <button type="button" class="btn btn-outline-primary pos-btn" data-pos="mr">Middle Right</button>
                            <button type="button" class="btn btn-outline-primary pos-btn" data-pos="bl">Bottom Left</button>
                            <button type="button" class="btn btn-outline-primary pos-btn" data-pos="bc">Bottom Center</button>
                            <button type="button" class="btn btn-primary pos-btn active" data-pos="br">Bottom Right</button>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label for="sizeRange" class="form-label fw-semibold">Size: <span id="sizeVal">18</span>% of page width</label>
                            <input type="range" class="form-range" id="sizeRange" min="5" max="60" value="18">
                        </div>
                        <div class="col-md-4">
                            <label for="opacityRange" class="form-label fw-semibold">Opacity: <span id="opacityVal">100</span>%</label>
                            <input type="range" class="form-range" id="opacityRange" min="10" max="100" value="100">
                        </div>
                        <div class="col-md-4">
                            <label for="pagesSel" class="form-label fw-semibold">Stamp on pages</label>
                            <select class="form-select" id="pagesSel">
                                <option value="all" selected>All pages</option>
                                <option value="first">First page only</option>
                                <option value="last">Last page only</option>
                            </select>
                        </div>
                    </div>

                    <button type="button" class="btn btn-primary w-100" id="goBtn">Stamp &amp; Download PDF</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>
                    <div class="alert alert-success mt-3 d-none" id="successBox" role="alert"></div>
                </div>
            </div>

            <div class="alert alert-info"><strong>Privacy note:</strong> Both the PDF and the image are processed in your browser — nothing is uploaded.</div>

            <h2>How to use</h2>
            <ol>
                <li>Select your PDF file.</li>
                <li>Select your logo or signature image (PNG/JPG).</li>
                <li>Choose the position, size, opacity and pages.</li>
                <li>Press <strong>Stamp &amp; Download PDF</strong> — the new PDF will download.</li>
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
    var pdfInput = document.getElementById('pdfInput');
    var imgInput = document.getElementById('imgInput');
    var imgPreviewWrap = document.getElementById('imgPreviewWrap');
    var imgPreview = document.getElementById('imgPreview');
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var successBox = document.getElementById('successBox');
    var sizeRange = document.getElementById('sizeRange');
    var sizeVal = document.getElementById('sizeVal');
    var opacityRange = document.getElementById('opacityRange');
    var opacityVal = document.getElementById('opacityVal');
    var pagesSel = document.getElementById('pagesSel');

    var pdfBytes = null, pdfName = 'document';
    var imgBytes = null, imgType = '', imgDims = { w: 200, h: 100 };
    var position = 'br';

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        successBox.classList.add('d-none');
    }
    function showSuccess(msg) {
        successBox.textContent = msg;
        successBox.classList.remove('d-none');
        errorBox.classList.add('d-none');
    }
    function hideAlerts() {
        errorBox.classList.add('d-none');
        successBox.classList.add('d-none');
    }

    var posBtns = document.querySelectorAll('.pos-btn');
    for (var i = 0; i < posBtns.length; i++) {
        posBtns[i].addEventListener('click', function () {
            for (var j = 0; j < posBtns.length; j++) {
                posBtns[j].classList.remove('btn-primary', 'active');
                posBtns[j].classList.add('btn-outline-primary');
            }
            this.classList.remove('btn-outline-primary');
            this.classList.add('btn-primary', 'active');
            position = this.getAttribute('data-pos');
        });
    }

    sizeRange.addEventListener('input', function () { sizeVal.textContent = sizeRange.value; });
    opacityRange.addEventListener('input', function () { opacityVal.textContent = opacityRange.value; });

    pdfInput.addEventListener('change', function () {
        hideAlerts();
        var f = pdfInput.files[0];
        if (!f) return;
        if (f.type !== 'application/pdf' && !/\.pdf$/i.test(f.name)) {
            showError('Please select a valid PDF file.');
            pdfInput.value = '';
            pdfBytes = null;
            return;
        }
        pdfName = f.name.replace(/\.pdf$/i, '');
        var r = new FileReader();
        r.onload = function () { pdfBytes = r.result; showSuccess('PDF loaded: ' + f.name); };
        r.onerror = function () { showError('Could not read the PDF file.'); };
        r.readAsArrayBuffer(f);
    });

    imgInput.addEventListener('change', function () {
        hideAlerts();
        var f = imgInput.files[0];
        if (!f) return;
        imgType = f.type;
        if (imgType !== 'image/png' && imgType !== 'image/jpeg') {
            showError('Please select a PNG or JPG image.');
            imgInput.value = '';
            imgBytes = null;
            return;
        }
        var r = new FileReader();
        r.onload = function () {
            imgBytes = r.result;
            var probe = new Image();
            probe.onload = function () {
                imgDims = { w: probe.naturalWidth, h: probe.naturalHeight };
                imgPreview.src = probe.src;
                imgPreviewWrap.classList.remove('d-none');
            };
            probe.src = URL.createObjectURL(f);
        };
        r.onerror = function () { showError('Could not read the image file.'); };
        r.readAsArrayBuffer(f);
    });

    function pageIndexes(total, mode) {
        if (mode === 'first') return [0];
        if (mode === 'last') return [total - 1];
        var out = [];
        for (var i = 0; i < total; i++) out.push(i);
        return out;
    }

    function stampCoords(pos, pageW, pageH, imgW, imgH, margin) {
        var x, y;
        var code = pos.charAt(0), code2 = pos.charAt(1);
        if (code === 't') y = pageH - imgH - margin;
        else if (code === 'm') y = (pageH - imgH) / 2;
        else y = margin;
        if (code2 === 'l') x = margin;
        else if (code2 === 'c') x = (pageW - imgW) / 2;
        else x = pageW - imgW - margin;
        return { x: x, y: y };
    }

    goBtn.addEventListener('click', function () {
        hideAlerts();
        if (!pdfBytes) { showError('Please select a PDF file first.'); return; }
        if (!imgBytes) { showError('Please select a logo/signature image first.'); return; }
        if (typeof PDFLib === 'undefined') { showError('PDF library could not load. Please check your internet and retry.'); return; }

        goBtn.disabled = true;
        goBtn.textContent = 'Processing...';

        var sizePct = parseFloat(sizeRange.value) / 100;
        var opacity = parseFloat(opacityRange.value) / 100;
        var mode = pagesSel.value;

        PDFLib.PDFDocument.load(pdfBytes).then(function (pdfDoc) {
            var embedPromise = (imgType === 'image/png')
                ? pdfDoc.embedPng(imgBytes)
                : pdfDoc.embedJpg(imgBytes);
            return embedPromise.then(function (img) {
                var pages = pdfDoc.getPages();
                var targets = pageIndexes(pages.length, mode);
                for (var i = 0; i < targets.length; i++) {
                    var page = pages[targets[i]];
                    var pw = page.getWidth(), ph = page.getHeight();
                    var imgW = pw * sizePct;
                    var imgH = imgW * (img.height / img.width);
                    var margin = Math.min(pw, ph) * 0.04;
                    var c = stampCoords(position, pw, ph, imgW, imgH, margin);
                    page.drawImage(img, { x: c.x, y: c.y, width: imgW, height: imgH, opacity: opacity });
                }
                return pdfDoc.save();
            });
        }).then(function (outBytes) {
            var blob = new Blob([outBytes], { type: 'application/pdf' });
            var a = document.createElement('a');
            a.href = URL.createObjectURL(blob);
            a.download = pdfName + '-stamped.pdf';
            document.body.appendChild(a);
            a.click();
            setTimeout(function () { URL.revokeObjectURL(a.href); a.remove(); }, 2000);
            showSuccess('Done! Stamped PDF downloaded.');
        }).catch(function (err) {
            showError('Failed to process the PDF: ' + (err && err.message ? err.message : err));
        }).then(function () {
            goBtn.disabled = false;
            goBtn.textContent = 'Stamp & Download PDF';
        });
    });
})();
</script>
@endsection
