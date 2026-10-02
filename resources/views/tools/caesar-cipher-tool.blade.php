@extends('layouts.app')
@section('title', 'Caesar Cipher Encoder Decoder — Free Online Tool')
@section('meta_description', 'Encode and decode text by shifting letters with a custom key')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3">Caesar Cipher Encoder Decoder</h1>
            <p class="lead small text-muted">Shift every letter in your text by a custom number of places to encode a Caesar cipher, or shift back to decode it.</p>

                    <label class="form-label fw-semibold" for="csIn">Your text</label>
                    <textarea id="csIn" class="form-control" rows="7" placeholder="Paste or type text here"></textarea>

                    <label class="form-label mt-3" for="csShift">Shift key: <span id="csShiftVal">3</span></label>
                    <input type="range" id="csShift" class="form-range" min="1" max="25" value="3">
                    <div class="d-flex flex-wrap gap-2 mt-2"><button type="button" id="csEnc" class="btn btn-primary">Encode</button><button type="button" id="csDec" class="btn btn-outline-primary">Decode</button><button type="button" id="csCopy" class="btn btn-outline-secondary">Copy Result</button></div>
                    <label class="form-label mt-3" for="csOut">Result</label>
                    <textarea id="csOut" class="form-control" rows="7" readonly></textarea>

            <div id="csMsg" class="alert alert-info d-none mt-3" role="alert"></div>
        </div>
    </div>
    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                    <li>Type or paste your text.</li>
                    <li>Choose the shift key with the slider.</li>
                    <li>Click Encode to shift forward or Decode to shift back, then copy the result.</li>
            </ol>
            <p class="small text-muted mb-0">Only A to Z letters are shifted, with wrap-around. Numbers, spaces and punctuation pass through unchanged, and letter case is preserved.</p>
        </div>
    </div>
</div>
@endsection
@section('scripts')

<script>
(function () {
    'use strict';

    var msgBox = document.getElementById("csMsg");
    function showMsg(t, ok) { msgBox.textContent = t; msgBox.className = "alert mt-3 " + (ok === false ? "alert-danger" : "alert-info"); msgBox.classList.remove("d-none"); }

    function copyText(v, label) {
        if (!v) { showMsg("Nothing to copy yet.", false); return; }
        if (navigator.clipboard) navigator.clipboard.writeText(v).then(function () { showMsg(label || "Copied to clipboard."); }).catch(function () { showMsg("Copy failed. Select the text and copy manually.", false); });
        else showMsg("Clipboard is not available in this browser.", false);
    }

    function shiftText(text, shift) {
        return String(text).replace(/[a-zA-Z]/g, function (ch) {
            var base = ch <= "Z" ? 65 : 97;
            return String.fromCharCode((ch.charCodeAt(0) - base + shift + 26) % 26 + base);
        });
    }
    function run(dir) {
        var shift = Number(document.getElementById("csShift").value) * dir;
        document.getElementById("csShiftVal").textContent = document.getElementById("csShift").value;
        document.getElementById("csOut").value = shiftText(document.getElementById("csIn").value, shift);
        showMsg(dir > 0 ? "Encoded with shift " + document.getElementById("csShift").value + "." : "Decoded with shift " + document.getElementById("csShift").value + ".");
    }
    document.getElementById("csShift").addEventListener("input", function () { document.getElementById("csShiftVal").textContent = this.value; });
    document.getElementById("csEnc").addEventListener("click", function () { run(1); });
    document.getElementById("csDec").addEventListener("click", function () { run(-1); });
    document.getElementById("csCopy").addEventListener("click", function () { copyText(document.getElementById("csOut").value, "Cipher text copied."); });

})();
</script>
@endsection
