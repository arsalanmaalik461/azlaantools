@extends('layouts.app')
@section('title', 'NATO Phonetic Alphabet Converter — Free Online Tool')
@section('meta_description', 'Convert text into NATO phonetic alphabet spelling for clear calls')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3">NATO Phonetic Alphabet Converter</h1>
            <p class="lead small text-muted">Spell any word or code in the NATO phonetic alphabet, Alfa Bravo Charlie style, so it is understood clearly on any call.</p>

                    <label class="form-label fw-semibold" for="naIn">Your text</label>
                    <textarea id="naIn" class="form-control" rows="7" placeholder="Paste or type text here"></textarea>

                    <button type="button" id="naGo" class="btn btn-primary btn-lg w-100 mt-3">Convert to NATO Spelling</button>
                    <label class="form-label mt-3" for="naOut">Phonetic spelling</label>
                    <textarea id="naOut" class="form-control" rows="5" readonly></textarea>
                    <div class="d-flex gap-2 mt-2"><button type="button" id="naCopy" class="btn btn-outline-secondary">Copy Result</button></div>
                    <div class="table-responsive mt-3"><table class="table table-sm"><tbody id="naTable"></tbody></table></div>

            <div id="naMsg" class="alert alert-info d-none mt-3" role="alert"></div>
        </div>
    </div>
    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                    <li>Type the word, name or code you need to spell out.</li>
                    <li>Click convert to get the NATO spelling word by word.</li>
                    <li>Read it out on the call, or copy it for your notes.</li>
            </ol>
            <p class="small text-muted mb-0">This is the standard ICAO and NATO spelling alphabet used by aviation, shipping and emergency services worldwide. Digits are spoken as single words, for example 3 is Tree in formal radio use; this tool uses the common Three form for clarity.</p>
        </div>
    </div>
</div>
@endsection
@section('scripts')

<script>
(function () {
    'use strict';

    var msgBox = document.getElementById("naMsg");
    function showMsg(t, ok) { msgBox.textContent = t; msgBox.className = "alert mt-3 " + (ok === false ? "alert-danger" : "alert-info"); msgBox.classList.remove("d-none"); }

    function copyText(v, label) {
        if (!v) { showMsg("Nothing to copy yet.", false); return; }
        if (navigator.clipboard) navigator.clipboard.writeText(v).then(function () { showMsg(label || "Copied to clipboard."); }).catch(function () { showMsg("Copy failed. Select the text and copy manually.", false); });
        else showMsg("Clipboard is not available in this browser.", false);
    }

    var MAP = { A: "Alfa", B: "Bravo", C: "Charlie", D: "Delta", E: "Echo", F: "Foxtrot", G: "Golf", H: "Hotel", I: "India", J: "Juliett", K: "Kilo", L: "Lima", M: "Mike", N: "November", O: "Oscar", P: "Papa", Q: "Quebec", R: "Romeo", S: "Sierra", T: "Tango", U: "Uniform", V: "Victor", W: "Whiskey", X: "X-ray", Y: "Yankee", Z: "Zulu", "0": "Zero", "1": "One", "2": "Two", "3": "Three", "4": "Four", "5": "Five", "6": "Six", "7": "Seven", "8": "Eight", "9": "Nine", "-": "Dash", ".": "Decimal", "@": "At sign" };
    var table = document.getElementById("naTable");
    var keys = Object.keys(MAP).filter(function (k) { return /[A-Z]/.test(k); });
    for (var r = 0; r < keys.length; r += 4) {
        var tr = document.createElement("tr");
        keys.slice(r, r + 4).forEach(function (k) { var td = document.createElement("td"); td.innerHTML = ""; td.textContent = k + " - " + MAP[k]; tr.appendChild(td); });
        table.appendChild(tr);
    }
    function convert() {
        var parts = document.getElementById("naIn").value.toUpperCase().split("").map(function (ch) { return MAP[ch] || ch; });
        document.getElementById("naOut").value = parts.join("  ");
    }
    document.getElementById("naGo").addEventListener("click", function () { convert(); showMsg("Converted. Words are separated by double spaces."); });
    document.getElementById("naIn").addEventListener("input", convert);
    document.getElementById("naCopy").addEventListener("click", function () { copyText(document.getElementById("naOut").value, "Phonetic spelling copied."); });

})();
</script>
@endsection
