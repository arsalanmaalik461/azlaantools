@extends('layouts.app')

@section('title', 'Binary / Text Converter - Text to Binary Free | Azlaan Tools')
@section('meta_description', 'Free binary to text and text to binary converter with full Urdu and Unicode support using UTF-8. Copy results with one click. No signup needed.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="mb-3">Binary / Text Converter</h1>
            <p class="lead text-muted">Convert text to binary code and binary back to text. Urdu and emoji work too, because conversion uses UTF-8 bytes — free, no signup.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <label for="textInput" class="form-label fw-semibold">Text</label>
                    <textarea class="form-control" id="textInput" rows="3" placeholder="Type text here, e.g. Hello or Urdu text..."></textarea>
                    <div class="d-flex flex-wrap gap-2 mt-2">
                        <button type="button" class="btn btn-primary btn-sm" id="toBinaryBtn">Text to Binary</button>
                        <button type="button" class="btn btn-outline-primary btn-sm" id="copyTextBtn">Copy Text</button>
                        <button type="button" class="btn btn-outline-danger btn-sm" id="clearTextBtn">Clear</button>
                    </div>
                    <hr>
                    <label for="binaryInput" class="form-label fw-semibold">Binary (8-bit groups, space separated)</label>
                    <textarea class="form-control" id="binaryInput" rows="4" placeholder="e.g. 01001000 01101001" style="font-family: monospace;"></textarea>
                    <div class="d-flex flex-wrap gap-2 mt-2">
                        <button type="button" class="btn btn-success btn-sm" id="toTextBtn">Binary to Text</button>
                        <button type="button" class="btn btn-outline-success btn-sm" id="copyBinaryBtn">Copy Binary</button>
                        <button type="button" class="btn btn-outline-danger btn-sm" id="clearBinaryBtn">Clear</button>
                    </div>
                    <div class="alert alert-danger mt-3 mb-0 d-none" id="errorBox"></div>
                    <div class="alert alert-success mt-3 mb-0 d-none" id="copyMsg">Copied to clipboard!</div>
                    <p class="small text-muted mt-3 mb-0">How it works: each character is stored as UTF-8 bytes, and each byte is shown as an 8-bit binary group. English letters use 1 byte; Urdu letters usually use 2 bytes.</p>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Type any text (English, Urdu or emoji) and click <strong>Text to Binary</strong>.</li>
                        <li>Copy the binary code with the copy button and share it anywhere.</li>
                        <li>Paste binary code (8-bit groups) in the lower box and click <strong>Binary to Text</strong> to decode it.</li>
                        <li>Invalid binary shows a clear error message so you can fix the groups.</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    var textInput = document.getElementById('textInput');
    var binaryInput = document.getElementById('binaryInput');
    var errorBox = document.getElementById('errorBox');
    var copyMsg = document.getElementById('copyMsg');
    function showError(msg) { errorBox.textContent = msg; errorBox.classList.remove('d-none'); }
    function hideError() { errorBox.classList.add('d-none'); }
    function showCopied() {
        copyMsg.classList.remove('d-none');
        setTimeout(function () { copyMsg.classList.add('d-none'); }, 1800);
    }
    function textToBinary() {
        hideError();
        var enc = new TextEncoder();
        var bytes = enc.encode(textInput.value);
        var parts = [];
        bytes.forEach(function (b) {
            var s = b.toString(2);
            while (s.length < 8) { s = '0' + s; }
            parts.push(s);
        });
        binaryInput.value = parts.join(' ');
    }
    function binaryToText() {
        hideError();
        var raw = binaryInput.value.trim();
        if (!raw) { showError('Please paste binary code first.'); return; }
        var groups = raw.split(/\s+/);
        var bytes = [];
        for (var i = 0; i < groups.length; i++) {
            var g = groups[i];
            if (!/^[01]{8}$/.test(g)) {
                showError('Invalid binary group number ' + (i + 1) + ': "' + g + '". Each group must be exactly 8 bits of 0 and 1.');
                return;
            }
            bytes.push(parseInt(g, 2));
        }
        try {
            var dec = new TextDecoder('utf-8', { fatal: true });
            textInput.value = dec.decode(new Uint8Array(bytes));
        } catch (e) {
            showError('These bytes are not valid UTF-8 text. Check the binary code and try again.');
        }
    }
    document.getElementById('toBinaryBtn').addEventListener('click', textToBinary);
    document.getElementById('toTextBtn').addEventListener('click', binaryToText);
    document.getElementById('copyTextBtn').addEventListener('click', async function () {
        if (!textInput.value) { return; }
        try { await navigator.clipboard.writeText(textInput.value); }
        catch (e) { textInput.select(); document.execCommand('copy'); }
        showCopied();
    });
    document.getElementById('copyBinaryBtn').addEventListener('click', async function () {
        if (!binaryInput.value) { return; }
        try { await navigator.clipboard.writeText(binaryInput.value); }
        catch (e) { binaryInput.select(); document.execCommand('copy'); }
        showCopied();
    });
    document.getElementById('clearTextBtn').addEventListener('click', function () { textInput.value = ''; hideError(); });
    document.getElementById('clearBinaryBtn').addEventListener('click', function () { binaryInput.value = ''; hideError(); });
})();
</script>
@endsection
