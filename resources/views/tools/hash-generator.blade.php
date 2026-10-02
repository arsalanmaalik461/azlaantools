@extends('layouts.app')

@section('title', 'Hash Generator Online Free - MD5, SHA-1, SHA-256, SHA-512 | Azlaan Tools')
@section('meta_description', 'Free hash generator: calculate MD5, SHA-1, SHA-256, SHA-384 and SHA-512 hashes of text or a file instantly. Runs 100% in your browser, nothing is uploaded.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Hash Generator</h1>
            <p class="lead text-muted">Calculate MD5 and SHA hashes of text or a file. This tool runs 100% in your browser — nothing is uploaded or saved, which is exactly what you want for hashes.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <label class="form-label fw-semibold" for="textInput">Text Input</label>
                    <textarea id="textInput" class="form-control font-monospace" rows="4" placeholder="Type or paste text to hash...">abc</textarea>
                    <div class="border rounded p-3 text-center text-muted mt-3" id="dropZone">Drop a file here to hash the file instead, or <label class="btn btn-outline-primary btn-sm mb-0" for="fileInput">Choose File</label><input type="file" id="fileInput" class="d-none"></div>
                    <div id="fileInfo" class="small mt-2 text-muted"></div>
                    <div id="hashResults" class="mt-3"></div>
                </div>
            </div>
            <h2>How to use</h2>
            <ol>
                <li>Type or paste text — MD5, SHA-1, SHA-256, SHA-384 and SHA-512 hashes update live.</li>
                <li>Or drop a file onto the box (or choose one) to hash the file contents instead.</li>
                <li>Click the <strong>Copy</strong> button next to any hash to copy it.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/spark-md5@3.0.2/spark-md5.min.js"></script>
<script>
(function () {
    var textEl = document.getElementById('textInput');
    var results = document.getElementById('hashResults');
    var algos = ['MD5', 'SHA-1', 'SHA-256', 'SHA-384', 'SHA-512'];
    var currentData = null;
    function render(values) {
        results.innerHTML = '';
        algos.forEach(function (name) {
            var row = document.createElement('div'); row.className = 'mb-2';
            var label = document.createElement('label'); label.className = 'form-label fw-semibold mb-1'; label.textContent = name;
            var grp = document.createElement('div'); grp.className = 'input-group';
            var inp = document.createElement('input'); inp.className = 'form-control font-monospace'; inp.readOnly = true; inp.value = values[name] || '';
            var btn = document.createElement('button'); btn.type = 'button'; btn.className = 'btn btn-outline-success'; btn.textContent = 'Copy';
            btn.addEventListener('click', function () { if (navigator.clipboard) navigator.clipboard.writeText(inp.value); btn.textContent = 'Copied!'; setTimeout(function () { btn.textContent = 'Copy'; }, 1500); });
            grp.appendChild(inp); grp.appendChild(btn); row.appendChild(label); row.appendChild(grp); results.appendChild(row);
        });
    }
    function bufToHex(buf) { return Array.from(new Uint8Array(buf)).map(function (b) { return b.toString(16).padStart(2, '0'); }).join(''); }
    async function hashData(data) {
        var values = {};
        var text = typeof data === 'string' ? data : null;
        var buffer = typeof data === 'string' ? new TextEncoder().encode(data) : data;
        if (window.SparkMD5) { values['MD5'] = text !== null ? window.SparkMD5.hash(text) : window.SparkMD5.ArrayBuffer.hash(buffer); }
        var map = { 'SHA-1': 'SHA-1', 'SHA-256': 'SHA-256', 'SHA-384': 'SHA-384', 'SHA-512': 'SHA-512' };
        for (var key in map) { try { var digest = await crypto.subtle.digest(map[key], buffer); values[key] = bufToHex(digest); } catch (e) { values[key] = 'Not available in this browser'; } }
        render(values);
    }
    textEl.addEventListener('input', function () { currentData = textEl.value; document.getElementById('fileInfo').textContent = ''; hashData(currentData); });
    function handleFile(file) {
        if (!file) return;
        document.getElementById('fileInfo').textContent = 'File: ' + file.name + ' (' + file.size + ' bytes)';
        file.arrayBuffer().then(function (buf) { currentData = buf; hashData(buf); });
    }
    document.getElementById('fileInput').addEventListener('change', function () { handleFile(this.files[0]); });
    var dz = document.getElementById('dropZone');
    dz.addEventListener('dragover', function (e) { e.preventDefault(); dz.classList.add('bg-light'); });
    dz.addEventListener('dragleave', function () { dz.classList.remove('bg-light'); });
    dz.addEventListener('drop', function (e) { e.preventDefault(); dz.classList.remove('bg-light'); if (e.dataTransfer.files.length) handleFile(e.dataTransfer.files[0]); });
    hashData('abc');
})();
</script>
@endsection
