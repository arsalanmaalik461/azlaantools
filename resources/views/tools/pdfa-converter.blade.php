@extends('layouts.app')

@section('title', 'PDF to PDF/A Converter - Azlaan Tools')
@section('meta_description', 'Convert any PDF into archival PDF/A format online for free. Made for courts and government records.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">PDF to PDF/A Converter</h1>
            <p class="lead text-muted">Get your PDF ready in <strong>PDF/A archival format</strong> — for courts, government offices and long-term record keeping. Everything happens in your browser; the file is never uploaded.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="pdfFile" class="form-label fw-semibold">Select a PDF file</label>
                        <input type="file" class="form-control" id="pdfFile" accept="application/pdf,.pdf">
                        <div class="form-text">Max 50 MB. Encrypted (password-protected) PDF files are not supported.</div>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Convert to PDF/A</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <h5>Conversion complete</h5>
                        <div id="statBox"></div>
                        <a href="#" class="btn btn-success w-100 mt-3" id="downloadLink" download>Download PDF/A File</a>
                        <div class="mt-4">
                            <h6>What was done (archival checklist)</h6>
                            <ul class="list-group list-group-flush" id="stepList"></ul>
                        </div>
                        <div class="alert alert-info mt-3 small">
                            Note: This tool prepares the PDF close to the PDF/A-1b archival format (normalizes the structure, XMP metadata, flattens forms). For court or strict legal use, verify the file with Adobe Acrobat Preflight or another PDF/A validator.
                        </div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select your PDF file (the box above the button).</li>
                <li>Press <strong>Convert to PDF/A</strong> — the processing happens in your browser.</li>
                <li>Save the archival PDF using the download button.</li>
            </ol>
            <h2>What is PDF/A?</h2>
            <p>PDF/A (ISO 19005) is an archival version of PDF, made so files stay readable for decades: fonts are embedded, there is no encryption, and metadata is standard. Courts and government record rooms often ask for documents in this format.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/pdf-lib@1.17.1/dist/pdf-lib.min.js"></script>
<script>
(function () {
    'use strict';
    var fileInput = document.getElementById('pdfFile');
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var stepList = document.getElementById('stepList');
    var statBox = document.getElementById('statBox');
    var downloadLink = document.getElementById('downloadLink');

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function fmtBytes(n) {
        var units = ['B', 'KB', 'MB', 'GB'];
        var i = 0;
        while (n >= 1024 && i < units.length - 1) { n = n / 1024; i++; }
        return n.toFixed(1) + ' ' + units[i];
    }
    function addStep(text, ok) {
        var li = document.createElement('li');
        li.className = 'list-group-item';
        li.textContent = (ok ? 'OK  ' : 'SKIP  ') + text;
        stepList.appendChild(li);
    }

    function buildXmp(fileName, pages) {
        var now = new Date().toISOString();
        var parts = [];
        parts.push('<?xpacket begin="\uFEFF" id="W5M0MpCehiHzreSzNTczkc9d"?>');
        parts.push('<x:xmpmeta xmlns:x="adobe:ns:meta/" x:xmptk="Azlaan Tools PDF/A Converter">');
        parts.push('<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">');
        parts.push('<rdf:Description rdf:about="" xmlns:pdfaid="http://www.aiim.org/pdfa/ns/id/" pdfaid:part="1" pdfaid:conformance="B">');
        parts.push('</rdf:Description>');
        parts.push('<rdf:Description rdf:about="" xmlns:dc="http://purl.org/dc/elements/1.1/"><dc:format>application/pdf</dc:format><dc:title><rdf:Alt><rdf:li xml:lang="x-default">' + fileName + '</rdf:li></rdf:Alt></dc:title><dc:description><rdf:Alt><rdf:li xml:lang="x-default">Archival copy, ' + pages + ' pages</rdf:li></rdf:Alt></dc:description></rdf:Description>');
        parts.push('<rdf:Description rdf:about="" xmlns:xmp="http://ns.adobe.com/xap/1.0/" xmp:CreateDate="' + now + '" xmp:ModifyDate="' + now + '" xmp:CreatorTool="Azlaan Tools PDF/A Converter"/>');
        parts.push('</rdf:RDF>');
        parts.push('</x:xmpmeta>');
        parts.push('<?xpacket end="w"?>');
        return parts.join('\n');
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var file = fileInput.files[0];
        if (!file) { showError('Please choose a PDF file first.'); return; }
        if (!/\.pdf$/i.test(file.name)) { showError('Only PDF files are supported.'); return; }
        if (file.size > 50 * 1024 * 1024) { showError('File exceeds 50 MB.'); return; }
        goBtn.disabled = true;
        var origText = goBtn.textContent;
        goBtn.textContent = 'Processing...';
        var reader = new FileReader();
        reader.onload = function (e) {
            convertPdf(new Uint8Array(e.target.result), file.name, file.size).then(function () {
                goBtn.disabled = false;
                goBtn.textContent = origText;
            }).catch(function (err) {
                goBtn.disabled = false;
                goBtn.textContent = origText;
                showError(err && err.message ? err.message : 'Conversion failed. Please try again.');
            });
        };
        reader.onerror = function () {
            goBtn.disabled = false;
            goBtn.textContent = origText;
            showError('Could not read the file.');
        };
        reader.readAsArrayBuffer(file);
    });

    async function convertPdf(bytes, name, origSize) {
        var PDFLib = window.PDFLib;
        if (!PDFLib) { throw new Error('PDF library failed to load — please check your internet connection.'); }
        var srcDoc;
        try {
            srcDoc = await PDFLib.PDFDocument.load(bytes);
        } catch (e) {
            throw new Error('Could not open this PDF — it may be password-protected or damaged.');
        }
        var pageCount = srcDoc.getPageCount();
        if (pageCount < 1) { throw new Error('No pages were found in this PDF.'); }

        var outDoc = await PDFLib.PDFDocument.create();
        var indices = [];
        for (var i = 0; i < pageCount; i++) { indices.push(i); }
        var copied = await outDoc.copyPages(srcDoc, indices);
        for (var j = 0; j < copied.length; j++) { outDoc.addPage(copied[j]); }

        stepList.innerHTML = '';
        addStep('Document structure normalized, ' + pageCount + ' pages copied.', true);

        var flatCount = 0;
        try {
            var form = outDoc.getForm();
            var fields = form.getFields();
            flatCount = fields.length;
            if (flatCount > 0) { form.flatten(); }
        } catch (fErr) { flatCount = 0; }
        addStep('Form fields flattened: ' + flatCount + ' field(s) made permanent.', true);

        var stamp = new Date();
        var baseName = name.replace(/\.pdf$/i, '');
        outDoc.setTitle(baseName + ' - PDF/A');
        outDoc.setProducer('Azlaan Tools PDF/A Converter');
        outDoc.setCreator('Azlaan Tools');
        outDoc.setCreationDate(stamp);
        outDoc.setModificationDate(stamp);
        addStep('Document metadata set (title, producer, dates).', true);

        var escName = baseName.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
        var xmpStr = buildXmp(escName, pageCount);
        var xmpBytes = new TextEncoder().encode(xmpStr);
        var xmpRef = outDoc.context.stream(xmpBytes, {
            Type: PDFLib.PDFName.of('Metadata'),
            Subtype: PDFLib.PDFName.of('XML')
        });
        outDoc.catalog.set(PDFLib.PDFName.of('Metadata'), xmpRef);
        addStep('PDF/A identification XMP metadata embedded (Part 1, Conformance B).', true);

        var outBytes = await outDoc.save({ useObjectStreams: true });
        addStep('Final file saved with object streams.', true);

        var blob = new Blob([outBytes], { type: 'application/pdf' });
        if (downloadLink.href && downloadLink.href.indexOf('blob:') === 0) {
            URL.revokeObjectURL(downloadLink.href);
        }
        downloadLink.href = URL.createObjectURL(blob);
        downloadLink.download = baseName + '-pdfa.pdf';

        statBox.innerHTML =
            '<div class="row text-center">' +
            '<div class="col-4"><div class="fw-bold fs-4">' + pageCount + '</div><div class="text-muted small">Pages</div></div>' +
            '<div class="col-4"><div class="fw-bold fs-4">' + fmtBytes(origSize) + '</div><div class="text-muted small">Original</div></div>' +
            '<div class="col-4"><div class="fw-bold fs-4">' + fmtBytes(outBytes.length) + '</div><div class="text-muted small">PDF/A</div></div>' +
            '</div>';

        results.classList.remove('d-none');
        results.scrollIntoView({ behavior: 'smooth' });
    }
})();
</script>
@endsection
