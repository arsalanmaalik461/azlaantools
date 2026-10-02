@extends('layouts.app')
@section('title', 'ROT13 Converter — Free Online Tool')
@section('meta_description', 'Apply ROT13 encoding to text and decode ROT13 in one click')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3">ROT13 Converter</h1>
            <p class="lead small text-muted">Apply ROT13 to any text, or decode ROT13 text back, with one button because ROT13 is its own inverse.</p>

                    <label class="form-label fw-semibold" for="rtIn">Your text</label>
                    <textarea id="rtIn" class="form-control" rows="7" placeholder="Paste or type text here"></textarea>

                    <div class="d-flex flex-wrap gap-2 mt-3">
                        <button type="button" id="rtGo" class="btn btn-primary">Apply ROT13</button>
                        <button type="button" id="rtSwap" class="btn btn-outline-secondary">Swap Input and Output</button>
                        <button type="button" id="rtCopy" class="btn btn-outline-secondary">Copy Result</button>
                    </div>
                    <label class="form-label mt-3" for="rtOut">Result</label>
                    <textarea id="rtOut" class="form-control" rows="7" readonly></textarea>

            <div id="rtMsg" class="alert alert-info d-none mt-3" role="alert"></div>
        </div>
    </div>
    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                    <li>Type or paste your text.</li>
                    <li>Click Apply ROT13 to encode, or use it again on encoded text to decode.</li>
                    <li>Copy the result wherever you need it.</li>
            </ol>
            <p class="small text-muted mb-0">ROT13 shifts each letter by 13 places. Because the alphabet has 26 letters, applying it twice returns the original text exactly. It hides spoilers from a casual glance but is not real encryption.</p>
        </div>
    </div>
</div>
@endsection
@section('scripts')

<script>
(function () {
    'use strict';

    var msgBox = document.getElementById("rtMsg");
    function showMsg(t, ok) { msgBox.textContent = t; msgBox.className = "alert mt-3 " + (ok === false ? "alert-danger" : "alert-info"); msgBox.classList.remove("d-none"); }

    function copyText(v, label) {
        if (!v) { showMsg("Nothing to copy yet.", false); return; }
        if (navigator.clipboard) navigator.clipboard.writeText(v).then(function () { showMsg(label || "Copied to clipboard."); }).catch(function () { showMsg("Copy failed. Select the text and copy manually.", false); });
        else showMsg("Clipboard is not available in this browser.", false);
    }

    function rot13(text) {
        return String(text).replace(/[a-zA-Z]/g, function (ch) {
            var base = ch <= "Z" ? 65 : 97;
            return String.fromCharCode((ch.charCodeAt(0) - base + 13) % 26 + base);
        });
    }
    document.getElementById("rtGo").addEventListener("click", function () {
        document.getElementById("rtOut").value = rot13(document.getElementById("rtIn").value);
        showMsg("ROT13 applied. Apply it again to the result to decode.");
    });
    document.getElementById("rtIn").addEventListener("input", function () {
        document.getElementById("rtOut").value = rot13(this.value);
    });
    document.getElementById("rtSwap").addEventListener("click", function () {
        var v = document.getElementById("rtIn").value;
        document.getElementById("rtIn").value = document.getElementById("rtOut").value;
        document.getElementById("rtOut").value = rot13(document.getElementById("rtIn").value);
        showMsg("Swapped. Output now shows ROT13 of the new input.");
    });
    document.getElementById("rtCopy").addEventListener("click", function () { copyText(document.getElementById("rtOut").value, "Result copied."); });

})();
</script>
@endsection
