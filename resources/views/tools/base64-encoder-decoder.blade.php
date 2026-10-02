@extends('layouts.app')

@section('title', 'Base64 Encode / Decode Online Free | Azlaan Tools')
@section('meta_description', 'Free Base64 encoder and decoder: encode text or files to Base64 and decode Base64 back to text, with full Urdu/Unicode support. No signup, runs in your browser.')

@section('content')
<div class="container py-4">
    <h1 class="mb-3">Base64 Encode / Decode</h1>
    <p class="lead">Encode text or a file to Base64, or decode Base64 back to readable text — free, private and with full Urdu/Unicode support.</p>

    <div id="alertBox" class="alert alert-danger d-none" role="alert"></div>
    <div id="successBox" class="alert alert-success d-none" role="alert"></div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <label for="inputText" class="form-label fw-semibold">Input</label>
            <textarea class="form-control" id="inputText" rows="6" placeholder="Type text to encode, or paste Base64 to decode..."></textarea>

            <div class="d-flex flex-wrap gap-2 mt-3 align-items-center">
                <button type="button" class="btn btn-primary" id="encodeBtn">Encode to Base64</button>
                <button type="button" class="btn btn-outline-primary" id="decodeBtn">Decode from Base64</button>
                <button type="button" class="btn btn-outline-secondary" id="swapBtn" title="Move output back to input">&#8646; Swap</button>
                <button type="button" class="btn btn-outline-danger" id="clearBtn">Clear</button>
            </div>

            <label for="outputText" class="form-label fw-semibold mt-3">Output</label>
            <textarea class="form-control" id="outputText" rows="6" readonly placeholder="Result will appear here..."></textarea>
            <div class="d-flex flex-wrap gap-2 mt-2">
                <button type="button" class="btn btn-success btn-sm" id="copyBtn">Copy Output</button>
                <span class="small text-muted align-self-center">Input: <strong id="inSize">0</strong> chars &nbsp;|&nbsp; Output: <strong id="outSize">0</strong> chars</span>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h2 class="h5">File to Base64</h2>
            <p class="text-muted small">Pick any file (image, PDF, etc.) and get its Base64 / data-URL string. Large files produce very long strings.</p>
            <input type="file" class="form-control" id="fileInput">
            <div id="fileInfo" class="small text-muted mt-2 d-none"></div>
            <label for="fileOutput" class="form-label fw-semibold mt-3">File Base64 (data URL)</label>
            <textarea class="form-control" id="fileOutput" rows="4" readonly placeholder="File Base64 will appear here..."></textarea>
            <div class="d-flex flex-wrap gap-2 mt-2">
                <button type="button" class="btn btn-success btn-sm" id="copyFileBtn">Copy File Base64</button>
                <button type="button" class="btn btn-outline-secondary btn-sm" id="copyRawBtn">Copy Raw Base64 Only</button>
            </div>
        </div>
    </div>

    <div class="alert alert-info">
        <strong>Privacy note:</strong> Everything is encoded/decoded in your browser — your text and files are never uploaded anywhere.
    </div>

    <h2>How to use</h2>
    <ol>
        <li>Type or paste text in the Input box and click <strong>Encode to Base64</strong>.</li>
        <li>To decode, paste a Base64 string and click <strong>Decode from Base64</strong> — invalid input shows an error message.</li>
        <li>Use <strong>Swap</strong> to send the output back to the input for another round.</li>
        <li>For files, choose a file in the File to Base64 section and copy the data-URL or raw Base64 string.</li>
    </ol>
</div>
@endsection

@section('scripts')
<script>
(function () {
    var input = document.getElementById('inputText');
    var output = document.getElementById('outputText');
    var alertBox = document.getElementById('alertBox');
    var successBox = document.getElementById('successBox');
    var fileInput = document.getElementById('fileInput');
    var fileOutput = document.getElementById('fileOutput');
    var fileInfo = document.getElementById('fileInfo');
    var rawBase64 = '';

    function showError(msg) {
        alertBox.textContent = msg;
        alertBox.classList.remove('d-none');
        successBox.classList.add('d-none');
    }
    function showSuccess(msg) {
        successBox.textContent = msg;
        successBox.classList.remove('d-none');
        alertBox.classList.add('d-none');
        setTimeout(function () { successBox.classList.add('d-none'); }, 2500);
    }
    function hideAlerts() { alertBox.classList.add('d-none'); successBox.classList.add('d-none'); }
    function formatSize(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
    }
    function updateSizes() {
        document.getElementById('inSize').textContent = input.value.length;
        document.getElementById('outSize').textContent = output.value.length;
    }
    async function copyText(text, msg) {
        if (!text) { showError('Nothing to copy yet.'); return; }
        try { await navigator.clipboard.writeText(text); }
        catch (e) {
            var ta = document.createElement('textarea');
            ta.value = text; document.body.appendChild(ta); ta.select();
            document.execCommand('copy'); ta.remove();
        }
        showSuccess(msg || 'Copied to clipboard!');
    }

    // Unicode-safe Base64 encode using TextEncoder + chunked btoa
    function encodeBase64(str) {
        var bytes = new TextEncoder().encode(str);
        var bin = '';
        var chunk = 0x8000;
        for (var i = 0; i < bytes.length; i += chunk) {
            bin += String.fromCharCode.apply(null, bytes.subarray(i, i + chunk));
        }
        return btoa(bin);
    }
    // Unicode-safe Base64 decode using atob + TextDecoder
    function decodeBase64(b64) {
        var clean = b64.replace(/\s+/g, '');
        if (!clean) return '';
        if (!/^[A-Za-z0-9+/=_-]+$/.test(clean)) throw new Error('Contains characters that are not valid Base64.');
        // Support URL-safe variant
        clean = clean.replace(/-/g, '+').replace(/_/g, '/');
        while (clean.length % 4) clean += '=';
        var bin = atob(clean);
        var bytes = new Uint8Array(bin.length);
        for (var i = 0; i < bin.length; i++) bytes[i] = bin.charCodeAt(i);
        return new TextDecoder('utf-8', { fatal: true }).decode(bytes);
    }

    document.getElementById('encodeBtn').addEventListener('click', function () {
        hideAlerts();
        try {
            output.value = encodeBase64(input.value);
            showSuccess('Text encoded to Base64 successfully.');
        } catch (e) {
            showError('Encoding failed: ' + e.message);
        }
        updateSizes();
    });

    document.getElementById('decodeBtn').addEventListener('click', function () {
        hideAlerts();
        if (!input.value.trim()) { showError('Please paste a Base64 string first.'); return; }
        try {
            output.value = decodeBase64(input.value);
            showSuccess('Base64 decoded successfully.');
        } catch (e) {
            output.value = '';
            showError('Invalid Base64 input — could not decode it. ' + e.message);
        }
        updateSizes();
    });

    document.getElementById('swapBtn').addEventListener('click', function () {
        if (!output.value) { showError('Nothing in the output to swap.'); return; }
        input.value = output.value;
        output.value = '';
        hideAlerts();
        updateSizes();
        input.focus();
    });

    document.getElementById('clearBtn').addEventListener('click', function () {
        input.value = ''; output.value = ''; hideAlerts(); updateSizes(); input.focus();
    });

    document.getElementById('copyBtn').addEventListener('click', function () { copyText(output.value); });
    input.addEventListener('input', updateSizes);

    fileInput.addEventListener('change', function () {
        hideAlerts();
        var file = fileInput.files && fileInput.files[0];
        if (!file) return;
        var reader = new FileReader();
        reader.onload = function () {
            var dataUrl = String(reader.result || '');
            fileOutput.value = dataUrl;
            var comma = dataUrl.indexOf(',');
            rawBase64 = comma >= 0 ? dataUrl.slice(comma + 1) : dataUrl;
            fileInfo.textContent = 'File: ' + file.name + ' | Type: ' + (file.type || 'unknown') + ' | Size: ' + formatSize(file.size) + ' | Base64 length: ' + rawBase64.length + ' chars (data URL: ' + dataUrl.length + ' chars)';
            fileInfo.classList.remove('d-none');
            showSuccess('File converted to Base64.');
        };
        reader.onerror = function () { showError('Could not read that file. Please try another one.'); };
        reader.readAsDataURL(file);
    });

    document.getElementById('copyFileBtn').addEventListener('click', function () { copyText(fileOutput.value, 'Data URL copied to clipboard!'); });
    document.getElementById('copyRawBtn').addEventListener('click', function () { copyText(rawBase64, 'Raw Base64 copied to clipboard!'); });

    updateSizes();
})();
</script>
@endsection
