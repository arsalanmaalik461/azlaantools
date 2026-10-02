@extends('layouts.app')
@section('title', 'File Hash Checksum Calculator - Azlaan Tools')
@section('meta_description', 'Drop any file to compute its MD5, SHA-1 and SHA-256 checksum locally in your browser. Verify download integrity — free, no upload.')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">File Hash Checksum Calculator</h1>
            <p class="lead text-muted">Drop any file — the MD5, SHA-1 and SHA-256 checksums are calculated right in your browser. The file is never uploaded.</p>

            <div id="dropZone" class="border border-2 border-dashed rounded p-5 text-center mb-4 bg-light" style="cursor: pointer;">
                <div class="fs-1 mb-2">🔐</div>
                <p class="mb-1 fw-semibold">Drag &amp; drop any file here</p>
                <p class="text-muted mb-3">or click to select a file (any type/size)</p>
                <button type="button" class="btn btn-primary">Select File</button>
                <input type="file" id="fileInput" class="d-none">
            </div>

            <div class="card shadow-sm mb-4 d-none" id="toolWrap">
                <div class="card-body">
                    <p class="mb-1">File: <strong id="fileName" class="text-break"></strong></p>
                    <p class="mb-3">Size: <span id="fileSize" class="fw-semibold">—</span></p>
                    <div class="alert alert-danger d-none" id="errorBox" role="alert"></div>

                    <div id="progressWrap" class="progress mb-3 d-none" style="height: 24px;">
                        <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%;">0%</div>
                    </div>

                    <div id="results" class="d-none">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">MD5</label>
                            <div class="input-group">
                                <input type="text" class="form-control font-monospace" id="md5Out" readonly>
                                <button class="btn btn-outline-secondary" type="button" data-copy="md5Out">Copy</button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">SHA-1</label>
                            <div class="input-group">
                                <input type="text" class="form-control font-monospace" id="sha1Out" readonly>
                                <button class="btn btn-outline-secondary" type="button" data-copy="sha1Out">Copy</button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">SHA-256</label>
                            <div class="input-group">
                                <input type="text" class="form-control font-monospace" id="sha256Out" readonly>
                                <button class="btn btn-outline-secondary" type="button" data-copy="sha256Out">Copy</button>
                            </div>
                        </div>

                        <hr>
                        <h2 class="h6">Compare with expected hash</h2>
                        <div class="mb-3">
                            <label for="expectInput" class="form-label fw-semibold">Expected hash (copy and paste it from the download page)</label>
                            <input type="text" class="form-control font-monospace" id="expectInput" placeholder="paste MD5, SHA-1 or SHA-256 here">
                        </div>
                        <button type="button" class="btn btn-success w-100" id="compareBtn">Compare</button>
                        <div id="compareOut" class="mt-3"></div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Drop or select a file — the hashes are calculated automatically.</li>
                <li>Copy the hash given on the download page, paste it into "Expected hash" and press Compare.</li>
                <li>If the hash matches, the file downloaded correctly; if it is a mismatch, download the file again.</li>
            </ol>
            <p class="text-muted small">Privacy: everything happens in your browser — the file is never sent to a server, so even large files are safe.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/spark-md5@3.0.2/spark-md5.min.js"></script>
<script>
(function () {
    'use strict';
    var dropZone = document.getElementById('dropZone');
    var fileInput = document.getElementById('fileInput');
    var toolWrap = document.getElementById('toolWrap');
    var fileName = document.getElementById('fileName');
    var fileSize = document.getElementById('fileSize');
    var errorBox = document.getElementById('errorBox');
    var progressWrap = document.getElementById('progressWrap');
    var progressBar = document.getElementById('progressBar');
    var results = document.getElementById('results');
    var md5Out = document.getElementById('md5Out');
    var sha1Out = document.getElementById('sha1Out');
    var sha256Out = document.getElementById('sha256Out');
    var expectInput = document.getElementById('expectInput');
    var compareBtn = document.getElementById('compareBtn');
    var compareOut = document.getElementById('compareOut');
    var CHUNK = 2 * 1024 * 1024;

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function setProgress(pct) {
        progressWrap.classList.remove('d-none');
        progressBar.style.width = pct + '%';
        progressBar.textContent = pct + '%';
    }
    function fmtSize(b) {
        if (b < 1024) return b + ' bytes';
        if (b < 1048576) return (b / 1024).toFixed(1) + ' KB';
        if (b < 1073741824) return (b / 1048576).toFixed(1) + ' MB';
        return (b / 1073741824).toFixed(2) + ' GB';
    }
    function bufToHex(buf) {
        var arr = new Uint8Array(buf);
        var s = '';
        for (var i = 0; i < arr.length; i++) {
            s += ('0' + arr[i].toString(16)).slice(-2);
        }
        return s;
    }

    dropZone.addEventListener('click', function () { fileInput.click(); });
    dropZone.addEventListener('dragover', function (e) { e.preventDefault(); });
    dropZone.addEventListener('drop', function (e) {
        e.preventDefault();
        if (e.dataTransfer.files.length) hashFile(e.dataTransfer.files[0]);
    });
    fileInput.addEventListener('change', function () {
        if (fileInput.files.length) hashFile(fileInput.files[0]);
    });

    function hashFile(file) {
        hideError();
        compareOut.innerHTML = '';
        expectInput.value = '';
        fileName.textContent = file.name;
        fileSize.textContent = fmtSize(file.size);
        toolWrap.classList.remove('d-none');
        results.classList.add('d-none');
        md5Out.value = sha1Out.value = sha256Out.value = '';
        setProgress(0);

        var spark = new SparkMD5.ArrayBuffer();
        var sha1Parts = [];
        var sha256Parts = [];
        var offset = 0;

        function readChunk() {
            var slice = file.slice(offset, offset + CHUNK);
            var reader = new FileReader();
            reader.onload = function (e) {
                var buf = e.target.result;
                spark.append(buf);
                sha1Parts.push(buf);
                sha256Parts.push(buf);
                offset += CHUNK;
                setProgress(Math.min(99, Math.round(offset / file.size * 100)));
                if (offset < file.size) { readChunk(); }
                else { finish(); }
            };
            reader.onerror = function () { showError('Error while reading the file.'); };
            reader.readAsArrayBuffer(slice);
        }

        function finish() {
            var md5 = spark.end();
            var total = new Blob(sha256Parts);
            var done = 0;
            function subtle(algo, out, next) {
                total.arrayBuffer().then(function (buf) {
                    return crypto.subtle.digest(algo, buf);
                }).then(function (digest) {
                    out.value = bufToHex(digest);
                    done++;
                    if (done === 2) { setProgress(100); results.classList.remove('d-none'); }
                    if (next) next();
                }).catch(function () { showError('Your browser does not support hashing — please use a modern browser.'); });
            }
            md5Out.value = md5;
            subtle('SHA-1', sha1Out);
            subtle('SHA-256', sha256Out);
        }

        readChunk();
    }

    compareBtn.addEventListener('click', function () {
        compareOut.innerHTML = '';
        var exp = expectInput.value.replace(/[\s-]/g, '').toLowerCase();
        if (!exp) { showError('Please paste the expected hash first.'); return; }
        hideError();
        var got = { md5: md5Out.value, 'sha-1': sha1Out.value, 'sha-256': sha256Out.value };
        var matched = null;
        Object.keys(got).forEach(function (k) { if (got[k] && got[k].toLowerCase() === exp) matched = k; });
        if (matched) {
            compareOut.innerHTML = '<div class="alert alert-success mb-0"><strong>✔ Match!</strong> Expected hash matched with ' + matched.toUpperCase() + ' — the file is fully correct.</div>';
        } else {
            var len = exp.length;
            var hint = (len === 32) ? 'MD5' : (len === 40) ? 'SHA-1' : (len === 64) ? 'SHA-256' : 'unknown format';
            compareOut.innerHTML = '<div class="alert alert-danger mb-0"><strong>✘ Mismatch.</strong> Expected hash (' + hint + ') did not match any calculated hash. Please download the file again or check the hash again.</div>';
        }
    });

    document.querySelectorAll('[data-copy]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var el = document.getElementById(btn.getAttribute('data-copy'));
            if (!el.value) return;
            navigator.clipboard.writeText(el.value).then(function () {
                var old = btn.textContent;
                btn.textContent = 'Copied';
                setTimeout(function () { btn.textContent = old; }, 1200);
            });
        });
    });
})();
</script>
@endsection
