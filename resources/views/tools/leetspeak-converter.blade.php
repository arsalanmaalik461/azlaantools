@extends('layouts.app')
@section('title', 'Leetspeak Converter — Free Online Tool')
@section('meta_description', 'Convert normal text into leetspeak and decode leetspeak back')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3">Leetspeak Converter</h1>
            <p class="lead small text-muted">Convert normal text into leetspeak (1337) for gaming names and fun, or decode leetspeak back into plain text.</p>

                    <label class="form-label fw-semibold" for="ltIn">Your text</label>
                    <textarea id="ltIn" class="form-control" rows="7" placeholder="Paste or type text here"></textarea>

                    <div class="d-flex flex-wrap gap-2 mt-3">
                        <button type="button" id="ltEnc" class="btn btn-primary">Encode to Leetspeak</button>
                        <button type="button" id="ltDec" class="btn btn-outline-primary">Decode to Plain Text</button>
                        <button type="button" id="ltCopy" class="btn btn-outline-secondary">Copy Result</button>
                    </div>
                    <label class="form-label mt-3" for="ltOut">Result</label>
                    <textarea id="ltOut" class="form-control" rows="7" readonly></textarea>

            <div id="ltMsg" class="alert alert-info d-none mt-3" role="alert"></div>
        </div>
    </div>
    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                    <li>Type or paste your text.</li>
                    <li>Click Encode to turn it into leetspeak, or Decode to turn leetspeak back into plain text.</li>
                    <li>Copy the result for your username or post.</li>
            </ol>
            <p class="small text-muted mb-0">Encoding uses one fixed substitution per letter, so decoding is a best effort: some leet characters stand for more than one letter in the wild, for example 1 can be I or L.</p>
        </div>
    </div>
</div>
@endsection
@section('scripts')

<script>
(function () {
    'use strict';

    var msgBox = document.getElementById("ltMsg");
    function showMsg(t, ok) { msgBox.textContent = t; msgBox.className = "alert mt-3 " + (ok === false ? "alert-danger" : "alert-info"); msgBox.classList.remove("d-none"); }

    function copyText(v, label) {
        if (!v) { showMsg("Nothing to copy yet.", false); return; }
        if (navigator.clipboard) navigator.clipboard.writeText(v).then(function () { showMsg(label || "Copied to clipboard."); }).catch(function () { showMsg("Copy failed. Select the text and copy manually.", false); });
        else showMsg("Clipboard is not available in this browser.", false);
    }

    var ENC = { a: "4", b: "8", c: "(", e: "3", g: "9", h: "#", i: "1", l: "1", o: "0", s: "5", t: "7", z: "2" };
    var DEC = { "4": "a", "8": "b", "(": "c", "3": "e", "9": "g", "#": "h", "1": "i", "0": "o", "5": "s", "7": "t", "2": "z", "@": "a", "$": "s" };
    document.getElementById("ltEnc").addEventListener("click", function () {
        var out = document.getElementById("ltIn").value.split("").map(function (ch) { var low = ch.toLowerCase(); return ENC[low] !== undefined ? ENC[low] : ch; }).join("");
        document.getElementById("ltOut").value = out; showMsg("Encoded to leetspeak.");
    });
    document.getElementById("ltDec").addEventListener("click", function () {
        var out = document.getElementById("ltIn").value.split("").map(function (ch) { return DEC[ch] !== undefined ? DEC[ch] : ch; }).join("");
        document.getElementById("ltOut").value = out; showMsg("Decoded on a best effort basis. The digit 1 is read as i here.");
    });
    document.getElementById("ltCopy").addEventListener("click", function () { copyText(document.getElementById("ltOut").value, "Result copied."); });

})();
</script>
@endsection
