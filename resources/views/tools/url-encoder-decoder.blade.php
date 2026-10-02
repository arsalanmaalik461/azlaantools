@extends('layouts.app')

@section('title', 'URL Encode / Decode Online Free | Azlaan Tools')
@section('meta_description', 'Free URL encoder and decoder: encode special characters for safe URLs or decode percent-encoded URLs back to readable text. No signup, runs in your browser.')

@section('content')
<div class="container py-4">
    <h1 class="mb-3">URL Encode / Decode</h1>
    <p class="lead">Encode text for safe use in a URL, or decode a percent-encoded URL back to readable text — free and instant.</p>

    <div id="alertBox" class="alert alert-danger d-none" role="alert"></div>
    <div id="successBox" class="alert alert-success d-none" role="alert"></div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <label for="inputText" class="form-label fw-semibold">Input</label>
            <textarea class="form-control" id="inputText" rows="5" placeholder="Type text or a URL here, e.g. https://example.com/search?q=electricity bill"></textarea>

            <div class="form-check form-switch mt-3">
                <input class="form-check-input" type="checkbox" id="fullUrlMode">
                <label class="form-check-label" for="fullUrlMode">Full-URL mode (encodeURI — keeps : / ? &amp; = intact, best for whole URLs)</label>
            </div>

            <div class="d-flex flex-wrap gap-2 mt-3">
                <button type="button" class="btn btn-primary" id="encodeBtn">URL Encode</button>
                <button type="button" class="btn btn-outline-primary" id="decodeBtn">URL Decode</button>
                <button type="button" class="btn btn-outline-secondary" id="swapBtn">&#8646; Swap</button>
                <button type="button" class="btn btn-outline-danger" id="clearBtn">Clear</button>
            </div>

            <label for="outputText" class="form-label fw-semibold mt-3">Output</label>
            <textarea class="form-control" id="outputText" rows="5" readonly placeholder="Result will appear here..."></textarea>
            <div class="d-flex flex-wrap gap-2 mt-2">
                <button type="button" class="btn btn-success btn-sm" id="copyBtn">Copy Output</button>
            </div>
        </div>
    </div>

    <div class="alert alert-info">
        <strong>Privacy note:</strong> Encoding and decoding happen entirely in your browser — nothing is sent to any server.
    </div>

    <h2>How to use</h2>
    <ol>
        <li>Type or paste your text or URL in the Input box.</li>
        <li>Turn on <strong>Full-URL mode</strong> if you are encoding a complete URL, so its : / ? &amp; characters stay intact.</li>
        <li>Click <strong>URL Encode</strong> to encode, or <strong>URL Decode</strong> to convert a percent-encoded string back to readable text.</li>
        <li>Click <strong>Copy Output</strong> to copy the result. A malformed % sequence in decode mode will show a clear error.</li>
    </ol>
</div>
@endsection

@section('scripts')
<script>
(function () {
    var input = document.getElementById('inputText');
    var output = document.getElementById('outputText');
    var fullUrlMode = document.getElementById('fullUrlMode');
    var alertBox = document.getElementById('alertBox');
    var successBox = document.getElementById('successBox');

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

    document.getElementById('encodeBtn').addEventListener('click', function () {
        hideAlerts();
        try {
            output.value = fullUrlMode.checked ? encodeURI(input.value) : encodeURIComponent(input.value);
            showSuccess('Encoded successfully.');
        } catch (e) {
            output.value = '';
            showError('Encoding failed: ' + e.message);
        }
    });

    document.getElementById('decodeBtn').addEventListener('click', function () {
        hideAlerts();
        if (!input.value) { showError('Please enter something to decode first.'); return; }
        try {
            output.value = fullUrlMode.checked ? decodeURI(input.value) : decodeURIComponent(input.value);
            showSuccess('Decoded successfully.');
        } catch (e) {
            output.value = '';
            showError('Could not decode: the input contains a malformed % escape sequence (for example "%zz" or a lone "%"). Please check the input and try again.');
        }
    });

    document.getElementById('swapBtn').addEventListener('click', function () {
        if (!output.value) { showError('Nothing in the output to swap.'); return; }
        input.value = output.value;
        output.value = '';
        hideAlerts();
        input.focus();
    });

    document.getElementById('clearBtn').addEventListener('click', function () {
        input.value = ''; output.value = ''; hideAlerts(); input.focus();
    });

    document.getElementById('copyBtn').addEventListener('click', async function () {
        if (!output.value) { showError('Nothing to copy yet.'); return; }
        try { await navigator.clipboard.writeText(output.value); }
        catch (e) { output.select(); document.execCommand('copy'); }
        showSuccess('Output copied to clipboard!');
    });
})();
</script>
@endsection
