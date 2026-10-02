@extends('layouts.app')
@section('title', 'Compare PDF Online Free — Azlaan Tools')
@section('meta_description', 'Compare two PDF files online for free — see added and removed words highlighted with a word-level diff and full stats. No signup, no upload — files stay in your browser.')
@section('content')
<div class="container py-4 pdf-tool">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Compare PDF</h1>
            <p class="lead text-muted">Compare the text of two PDF files — words that were added show in green, deleted words show in red. Best for checking versions of contracts, invoices and documents.</p>
            <div class="alert alert-warning">
                <strong>Honest note:</strong> This tool only compares text — differences in images, layout or formatting will not be caught. Scanned PDFs (with no text layer) will give empty text; for those, first use the Image to Text (OCR) tool.
            </div>

            <div id="alertBox" class="alert alert-danger d-none" role="alert"></div>
            <div id="successBox" class="alert alert-success d-none" role="alert"></div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <div id="dropZoneA" class="drop-zone h-100">
                        <div class="fs-1 mb-2">📄</div>
                        <p class="mb-1 fw-semibold">PDF A — first / old file</p>
                        <p class="text-muted mb-3">Drag &amp; drop, or click to browse</p>
                        <button type="button" class="btn btn-primary">Select PDF A</button>
                        <input type="file" id="fileInputA" accept="application/pdf,.pdf" class="d-none">
                        <p class="small mt-3 mb-0">File: <strong id="fileNameA" class="text-break">No file selected</strong></p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div id="dropZoneB" class="drop-zone h-100">
                        <div class="fs-1 mb-2">📄</div>
                        <p class="mb-1 fw-semibold">PDF B — second / new file</p>
                        <p class="text-muted mb-3">Drag &amp; drop, or click to browse</p>
                        <button type="button" class="btn btn-outline-primary">Select PDF B</button>
                        <input type="file" id="fileInputB" accept="application/pdf,.pdf" class="d-none">
                        <p class="small mt-3 mb-0">File: <strong id="fileNameB" class="text-break">No file selected</strong></p>
                    </div>
                </div>
            </div>

            <div class="d-flex flex-wrap gap-2 mb-3">
                <button type="button" id="compareBtn" class="btn btn-success btn-lg" disabled>Compare PDFs</button>
                <button type="button" id="clearBtn" class="btn btn-outline-secondary">Clear</button>
            </div>
            <div id="progressWrap" class="progress mb-3 d-none" style="height: 24px;">
                <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%;">0%</div>
            </div>

            <div id="resultWrap" class="d-none">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h2 class="h5">Result</h2>
                        <div class="row text-center g-3 mb-3">
                            <div class="col-4">
                                <div class="border rounded p-3" style="background: #e6f4ea;">
                                    <div class="fs-3 fw-bold" id="statAdded">0</div>
                                    <div class="small text-muted">Words added (new in B)</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="border rounded p-3" style="background: #fdecea;">
                                    <div class="fs-3 fw-bold" id="statRemoved">0</div>
                                    <div class="small text-muted">Words removed (gone from A)</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="border rounded p-3" style="background: #f1f3f5;">
                                    <div class="fs-3 fw-bold" id="statUnchanged">0</div>
                                    <div class="small text-muted">Words unchanged</div>
                                </div>
                            </div>
                        </div>
                        <span class="form-label fw-semibold d-block mb-2">Highlighted differences <span class="fw-normal text-muted">(green = added in B, red = removed from A)</span></span>
                        <div id="diffOutput" class="border rounded p-3 mb-4" style="background: #fff; white-space: pre-wrap; word-break: break-word; max-height: 480px; overflow: auto; line-height: 1.9;"></div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="textA" class="form-label fw-semibold">PDF A — full text</label>
                                <textarea id="textA" class="form-control" rows="12" readonly></textarea>
                            </div>
                            <div class="col-md-6">
                                <label for="textB" class="form-label fw-semibold">PDF B — full text</label>
                                <textarea id="textB" class="form-control" rows="12" readonly></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="alert alert-info mt-4">
                <strong>Privacy note:</strong> Your file never leaves your browser — both files stay on your phone or computer, everything happens there, nothing is uploaded.
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select the first (old) PDF in the first box and the second (new) PDF in the other box — drag &amp; drop works too.</li>
                <li>When both files are loaded, press <strong>Compare PDFs</strong>.</li>
                <li>See the stats above: how many words were added, how many were removed, and how many stayed the same.</li>
                <li>Read the differences in the highlighted text below; the full text of both files is also shown side by side.</li>
            </ol>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/pdfjs-dist@3.11.174/build/pdf.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/diff@5.2.0/dist/diff.min.js"></script>
<script>
(function () {
    if (typeof pdfjsLib !== 'undefined') {
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdn.jsdelivr.net/npm/pdfjs-dist@3.11.174/build/pdf.worker.min.js';
    }
    var dropZoneA = document.getElementById('dropZoneA');
    var fileInputA = document.getElementById('fileInputA');
    var dropZoneB = document.getElementById('dropZoneB');
    var fileInputB = document.getElementById('fileInputB');
    var fileNameA = document.getElementById('fileNameA');
    var fileNameB = document.getElementById('fileNameB');
    var alertBox = document.getElementById('alertBox');
    var successBox = document.getElementById('successBox');
    var compareBtn = document.getElementById('compareBtn');
    var clearBtn = document.getElementById('clearBtn');
    var progressWrap = document.getElementById('progressWrap');
    var progressBar = document.getElementById('progressBar');
    var resultWrap = document.getElementById('resultWrap');
    var statAdded = document.getElementById('statAdded');
    var statRemoved = document.getElementById('statRemoved');
    var statUnchanged = document.getElementById('statUnchanged');
    var diffOutput = document.getElementById('diffOutput');
    var textAEl = document.getElementById('textA');
    var textBEl = document.getElementById('textB');
    var docA = null;
    var docB = null;

    function showError(msg) {
        alertBox.textContent = msg;
        alertBox.classList.remove('d-none');
        successBox.classList.add('d-none');
    }
    function showSuccess(msg) {
        successBox.textContent = msg;
        successBox.classList.remove('d-none');
        alertBox.classList.add('d-none');
    }
    function hideAlerts() {
        alertBox.classList.add('d-none');
        successBox.classList.add('d-none');
    }
    function formatSize(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
    }
    function syncCompareBtn() {
        compareBtn.disabled = !(docA && docB);
    }
    function extractPageText(textContent) {
        var linesMap = {};
        textContent.items.forEach(function (item) {
            if (!item.str) return;
            if (!item.transform || item.transform.length < 6) return;
            var y = Math.round(item.transform[5]);
            var key = null;
            Object.keys(linesMap).forEach(function (k) {
                if (Math.abs(parseInt(k, 10) - y) <= 2) key = k;
            });
            if (key === null) {
                key = String(y);
                linesMap[key] = { y: y, parts: [] };
            }
            linesMap[key].parts.push({ x: item.transform[4], str: item.str });
        });
        var lines = Object.values(linesMap).sort(function (a, b) { return b.y - a.y; });
        var out = [];
        lines.forEach(function (line) {
            line.parts.sort(function (a, b) { return a.x - b.x; });
            var t = line.parts.map(function (p) { return p.str; }).join(' ').replace(/\s+/g, ' ').trim();
            if (t) out.push(t);
        });
        return out.join('\n');
    }
    async function extractFullText(pdfDoc) {
        var parts = [];
        for (var n = 1; n <= pdfDoc.numPages; n++) {
            var page = await pdfDoc.getPage(n);
            var tc = await page.getTextContent();
            parts.push(extractPageText(tc));
        }
        return parts.join('\n').replace(/\n{3,}/g, '\n\n').trim();
    }
    function countWords(value) {
        var t = (value || '').trim();
        if (!t) return 0;
        return t.split(/\s+/).length;
    }

    async function loadFile(file, side) {
        hideAlerts();
        if (!file) return;
        if (!(file.type === 'application/pdf' || file.name.toLowerCase().endsWith('.pdf'))) {
            showError('Please select a valid PDF file for PDF ' + side + '.');
            return;
        }
        if (typeof pdfjsLib === 'undefined') {
            showError('PDF library failed to load. Please check your internet connection and try again.');
            return;
        }
        try {
            var buf = await file.arrayBuffer();
            var pdfDoc = await pdfjsLib.getDocument({ data: buf }).promise;
            var label = file.name + ' (' + formatSize(file.size) + ', ' + pdfDoc.numPages + ' page' + (pdfDoc.numPages === 1 ? '' : 's') + ')';
            if (side === 'A') {
                docA = { doc: pdfDoc, name: file.name };
                fileNameA.textContent = label;
            } else {
                docB = { doc: pdfDoc, name: file.name };
                fileNameB.textContent = label;
            }
            resultWrap.classList.add('d-none');
            syncCompareBtn();
            if (docA && docB) {
                showSuccess('Both files are loaded — now press Compare PDFs.');
            } else {
                showSuccess('PDF ' + side + ' loaded. Please select the other file too.');
            }
        } catch (err) {
            console.error(err);
            if (side === 'A') { docA = null; fileNameA.textContent = 'No file selected'; }
            if (side === 'B') { docB = null; fileNameB.textContent = 'No file selected'; }
            syncCompareBtn();
            showError('Could not read PDF ' + side + '. It may be corrupted or password-protected. Please try a different file.');
        }
    }

    function wireDropZone(zone, input, side) {
        zone.addEventListener('click', function () { input.click(); });
        input.addEventListener('change', function () {
            if (input.files && input.files[0]) loadFile(input.files[0], side);
            input.value = '';
        });
        zone.addEventListener('dragover', function (e) { e.preventDefault(); zone.classList.add('dragover'); });
        zone.addEventListener('dragleave', function () { zone.classList.remove('dragover'); });
        zone.addEventListener('drop', function (e) {
            e.preventDefault();
            zone.classList.remove('dragover');
            if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files[0]) loadFile(e.dataTransfer.files[0], side);
        });
    }
    wireDropZone(dropZoneA, fileInputA, 'A');
    wireDropZone(dropZoneB, fileInputB, 'B');

    clearBtn.addEventListener('click', function () {
        docA = null;
        docB = null;
        fileNameA.textContent = 'No file selected';
        fileNameB.textContent = 'No file selected';
        textAEl.value = '';
        textBEl.value = '';
        diffOutput.innerHTML = '';
        statAdded.textContent = '0';
        statRemoved.textContent = '0';
        statUnchanged.textContent = '0';
        resultWrap.classList.add('d-none');
        syncCompareBtn();
        hideAlerts();
    });

    compareBtn.addEventListener('click', async function () {
        hideAlerts();
        if (!docA || !docB) { showError('Please select both PDF files first.'); return; }
        if (typeof Diff === 'undefined') { showError('Diff library failed to load. Please check your internet connection and try again.'); return; }
        compareBtn.disabled = true;
        compareBtn.textContent = 'Comparing...';
        progressWrap.classList.remove('d-none');
        progressBar.style.width = '20%';
        progressBar.textContent = '20%';
        try {
            var fullA = await extractFullText(docA.doc);
            progressBar.style.width = '50%';
            progressBar.textContent = '50%';
            var fullB = await extractFullText(docB.doc);
            progressBar.style.width = '80%';
            progressBar.textContent = '80%';
            textAEl.value = fullA;
            textBEl.value = fullB;
            if (!fullA && !fullB) {
                resultWrap.classList.add('d-none');
                showError('No text layer found in either PDF. These look like scanned PDFs — first use the Image to Text (OCR) tool.');
                return;
            }
            if (!fullA || !fullB) {
                resultWrap.classList.add('d-none');
                showError('No text was found in one PDF, so comparison is not possible. For a scanned PDF, use the Image to Text (OCR) tool.');
                return;
            }
            var parts = Diff.diffWords(fullA, fullB);
            var added = 0;
            var removed = 0;
            var unchanged = 0;
            diffOutput.innerHTML = '';
            parts.forEach(function (part) {
                var span = document.createElement('span');
                span.textContent = part.value;
                var wc = countWords(part.value);
                if (part.added) {
                    span.style.background = '#c8e6c9';
                    span.style.borderRadius = '3px';
                    span.style.padding = '0 2px';
                    added += wc;
                } else if (part.removed) {
                    span.style.background = '#ffcdd2';
                    span.style.borderRadius = '3px';
                    span.style.padding = '0 2px';
                    span.style.textDecoration = 'line-through';
                    removed += wc;
                } else {
                    unchanged += wc;
                }
                diffOutput.appendChild(span);
            });
            statAdded.textContent = added.toLocaleString();
            statRemoved.textContent = removed.toLocaleString();
            statUnchanged.textContent = unchanged.toLocaleString();
            resultWrap.classList.remove('d-none');
            progressBar.style.width = '100%';
            progressBar.textContent = '100%';
            if (added === 0 && removed === 0) {
                showSuccess('Great — the text of both PDFs is exactly the same. No differences found.');
            } else {
                showSuccess('Comparison complete — ' + added.toLocaleString() + ' words added, ' + removed.toLocaleString() + ' words removed.');
            }
        } catch (err) {
            console.error(err);
            showError('Could not compare these PDFs. One of the files may be corrupted. Please try again with different files.');
        } finally {
            compareBtn.textContent = 'Compare PDFs';
            syncCompareBtn();
            setTimeout(function () { progressWrap.classList.add('d-none'); progressBar.style.width = '0%'; progressBar.textContent = '0%'; }, 800);
        }
    });
})();
</script>
@endsection
