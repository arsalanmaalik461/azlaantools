@extends('layouts.app')
@section('title', 'Remove Duplicate Words — Free Online Tool')
@section('meta_description', 'Remove repeated words from text and keep only unique words')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3">Remove Duplicate Words</h1>
            <p class="lead small text-muted">Paste text and remove every repeated word, keeping only the first use of each word in its original order.</p>

                    <label class="form-label fw-semibold" for="rdIn">Your text</label>
                    <textarea id="rdIn" class="form-control" rows="7" placeholder="Paste or type text here"></textarea>

                    <div class="d-flex flex-wrap gap-3 mt-3">
                        <div class="form-check"><input type="checkbox" id="rdCase" class="form-check-input"><label class="form-check-label" for="rdCase">Case sensitive (Apple and apple count as different)</label></div>
                        <div class="form-check"><input type="checkbox" id="rdSort" class="form-check-input"><label class="form-check-label" for="rdSort">Sort unique words alphabetically</label></div>
                    </div>
                    <button type="button" id="rdGo" class="btn btn-primary btn-lg w-100 mt-3">Remove Duplicate Words</button>
                    <p class="mt-3 mb-0">Words before: <strong id="rdBefore">0</strong> &nbsp;·&nbsp; Unique after: <strong id="rdAfter">0</strong> &nbsp;·&nbsp; Removed: <strong id="rdRemoved">0</strong></p>
                    <label class="form-label mt-2" for="rdOut">Result</label>
                    <textarea id="rdOut" class="form-control" rows="7" readonly></textarea>
                    <button type="button" id="rdCopy" class="btn btn-outline-secondary mt-2">Copy Result</button>

            <div id="rdMsg" class="alert alert-info d-none mt-3" role="alert"></div>
        </div>
    </div>
    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                    <li>Paste your text.</li>
                    <li>Choose case sensitivity and sorting options.</li>
                    <li>Click the button and copy the deduplicated text.</li>
            </ol>
            <p class="small text-muted mb-0">Words are split on whitespace and punctuation stays attached to its word, so hello, and hello count as different tokens. Use the extra spaces cleaner first for very messy text.</p>
        </div>
    </div>
</div>
@endsection
@section('scripts')

<script>
(function () {
    'use strict';

    var msgBox = document.getElementById("rdMsg");
    function showMsg(t, ok) { msgBox.textContent = t; msgBox.className = "alert mt-3 " + (ok === false ? "alert-danger" : "alert-info"); msgBox.classList.remove("d-none"); }

    function copyText(v, label) {
        if (!v) { showMsg("Nothing to copy yet.", false); return; }
        if (navigator.clipboard) navigator.clipboard.writeText(v).then(function () { showMsg(label || "Copied to clipboard."); }).catch(function () { showMsg("Copy failed. Select the text and copy manually.", false); });
        else showMsg("Clipboard is not available in this browser.", false);
    }

    document.getElementById("rdGo").addEventListener("click", function () {
        var tokens = document.getElementById("rdIn").value.split(/\s+/).filter(Boolean);
        var seen = {}, out = [];
        var cs = document.getElementById("rdCase").checked;
        tokens.forEach(function (w) { var k = cs ? w : w.toLowerCase(); if (!seen[k]) { seen[k] = 1; out.push(w); } });
        if (document.getElementById("rdSort").checked) out.sort(function (a, b) { return a.toLowerCase() < b.toLowerCase() ? -1 : 1; });
        document.getElementById("rdBefore").textContent = tokens.length;
        document.getElementById("rdAfter").textContent = out.length;
        document.getElementById("rdRemoved").textContent = tokens.length - out.length;
        document.getElementById("rdOut").value = out.join(" ");
        showMsg("Kept " + out.length + " unique words, removed " + (tokens.length - out.length) + " repeats.");
    });
    document.getElementById("rdCopy").addEventListener("click", function () { copyText(document.getElementById("rdOut").value, "Result copied."); });

})();
</script>
@endsection
