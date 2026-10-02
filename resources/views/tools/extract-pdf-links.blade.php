@extends('layouts.app')

@section('title', 'Extract PDF Links - Azlaan Tools')
@section('meta_description', 'Extract every hyperlink and URL hidden in a PDF file, free online. See all the links in one list.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Extract PDF Links</h1>
            <p class="lead text-muted">Upload a PDF file and see all the hidden hyperlinks and URLs in one list. View all the links in one place, then copy or download them.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="pdfFile" class="form-label fw-semibold">Select a PDF file</label>
                        <input type="file" class="form-control" id="pdfFile" accept="application/pdf,.pdf">
                        <div class="form-text">The file is only read in your browser; it is never uploaded to a server.</div>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Extract Links</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <h5 id="linkCount" class="mb-3"></h5>
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <button type="button" class="btn btn-outline-primary btn-sm" id="copyBtn">Copy All Links</button>
                            <button type="button" class="btn btn-outline-success btn-sm" id="dlTxtBtn">Download .txt</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="dlCsvBtn">Download .csv</button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle">
                                <thead>
                                    <tr><th style="width:70px">Page</th><th>Link</th><th style="width:110px">Source</th></tr>
                                </thead>
                                <tbody id="linkRows"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select your PDF file and press <strong>Extract Links</strong>.</li>
                <li>Clickable links from every page and URLs written in the text will be listed.</li>
                <li>Copy the links or download them in a .txt / .csv file.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/pdfjs-dist@3.11.174/build/pdf.min.js"></script>
<script>
(function () {
    'use strict';
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdn.jsdelivr.net/npm/pdfjs-dist@3.11.174/build/pdf.worker.min.js';

    var pdfFile = document.getElementById('pdfFile');
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var linkCount = document.getElementById('linkCount');
    var linkRows = document.getElementById('linkRows');
    var copyBtn = document.getElementById('copyBtn');
    var dlTxtBtn = document.getElementById('dlTxtBtn');
    var dlCsvBtn = document.getElementById('dlCsvBtn');
    var found = [];

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function cleanUrl(u) {
        u = u.replace(/[.,;:'"\]]+$/, '');
        while (u.charAt(u.length - 1) === String.fromCharCode(41)) { u = u.slice(0, -1); }
        return u;
    }
    function esc(s) {
        return s.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }
    function addLink(page, url, source, seen) {
        var key = page + '|' + url;
        if (seen[key]) { return; }
        seen[key] = true;
        found.push({ page: page, url: url, source: source });
    }

    goBtn.addEventListener('click', function () {
        hideError();
        if (!pdfFile.files || !pdfFile.files[0]) {
            showError('Please select a PDF file first.');
            return;
        }
        var file = pdfFile.files[0];
        if (!/\.pdf$/i.test(file.name) && file.type !== 'application/pdf') {
            showError('Please select only a PDF file.');
            return;
        }
        goBtn.disabled = true;
        goBtn.textContent = 'Reading PDF...';
        found = [];
        var reader = new FileReader();
        reader.onload = function () {
            var data = new Uint8Array(reader.result);
            pdfjsLib.getDocument({ data: data }).promise.then(function (pdf) {
                var seen = {};
                var jobs = [];
                for (var p = 1; p <= pdf.numPages; p++) {
                    (function (pageNum) {
                        jobs.push(pdf.getPage(pageNum).then(function (page) {
                            return Promise.all([page.getAnnotations(), page.getTextContent()]).then(function (res) {
                                var ann = res[0], txt = res[1];
                                ann.forEach(function (a) {
                                    if (a.url && /^https?:\/\//i.test(a.url)) {
                                        addLink(pageNum, cleanUrl(a.url), 'Clickable link', seen);
                                    }
                                });
                                var text = txt.items.map(function (it) { return it.str; }).join(' ');
                                var re = /https?:\/\/[^\s<>"'\]]+/gi, m;
                                while ((m = re.exec(text)) !== null) {
                                    addLink(pageNum, cleanUrl(m[0]), 'Found in text', seen);
                                }
                            });
                        }));
                    })(p);
                }
                return Promise.all(jobs).then(function () { return pdf.numPages; });
            }).then(function (numPages) {
                goBtn.disabled = false;
                goBtn.textContent = 'Extract Links';
                linkRows.innerHTML = '';
                if (!found.length) {
                    linkCount.textContent = 'No links found in this PDF (0 links, ' + numPages + ' pages checked).';
                } else {
                    linkCount.textContent = found.length + ' link' + (found.length > 1 ? 's' : '') + ' found (' + numPages + ' pages checked).';
                    found.forEach(function (l) {
                        var tr = document.createElement('tr');
                        var tdP = document.createElement('td');
                        tdP.textContent = l.page;
                        var tdL = document.createElement('td');
                        var a = document.createElement('a');
                        a.href = l.url;
                        a.target = '_blank';
                        a.rel = 'noopener';
                        a.textContent = l.url.length > 70 ? l.url.slice(0, 70) + '...' : l.url;
                        tdL.appendChild(a);
                        var tdS = document.createElement('td');
                        tdS.innerHTML = '<span class="badge ' + (l.source === 'Clickable link' ? 'bg-primary' : 'bg-secondary') + '">' + l.source + '</span>';
                        tr.appendChild(tdP); tr.appendChild(tdL); tr.appendChild(tdS);
                        linkRows.appendChild(tr);
                    });
                }
                results.classList.remove('d-none');
            }).catch(function (err) {
                goBtn.disabled = false;
                goBtn.textContent = 'Extract Links';
                showError('There was a problem reading the PDF: ' + (err && err.message ? err.message : err));
            });
        };
        reader.onerror = function () {
            goBtn.disabled = false;
            goBtn.textContent = 'Extract Links';
            showError('There was a problem reading the file. Please try again.');
        };
        reader.readAsArrayBuffer(file);
    });

    function download(name, text, type) {
        var blob = new Blob([text], { type: type });
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = name;
        document.body.appendChild(a);
        a.click();
        setTimeout(function () { URL.revokeObjectURL(a.href); a.remove(); }, 500);
    }

    copyBtn.addEventListener('click', function () {
        var txt = found.map(function (l) { return 'Page ' + l.page + ': ' + l.url; }).join('\n');
        navigator.clipboard.writeText(txt).then(function () {
            copyBtn.textContent = 'Copied!';
            setTimeout(function () { copyBtn.textContent = 'Copy All Links'; }, 1500);
        });
    });
    dlTxtBtn.addEventListener('click', function () {
        download('pdf-links.txt', found.map(function (l) { return 'Page ' + l.page + ': ' + l.url; }).join('\n'), 'text/plain');
    });
    dlCsvBtn.addEventListener('click', function () {
        var csv = 'Page,URL,Source\n' + found.map(function (l) {
            return l.page + ',"' + l.url.replace(/"/g, '""') + '","' + l.source + '"';
        }).join('\n');
        download('pdf-links.csv', csv, 'text/csv');
    });
})();
</script>
@endsection
