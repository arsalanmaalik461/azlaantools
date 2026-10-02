@extends('layouts.app')
@section('title', 'Remove Extra Spaces — Free Online Tool')
@section('meta_description', 'Remove extra spaces double gaps and blank lines from any text')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3">Remove Extra Spaces</h1>
            <p class="lead small text-muted">Clean pasted text by removing double spaces, trailing spaces, tabs and extra blank lines in one click.</p>

                    <label class="form-label fw-semibold" for="rxIn">Your text</label>
                    <textarea id="rxIn" class="form-control" rows="7" placeholder="Paste or type text here"></textarea>

                    <div class="d-flex flex-wrap gap-3 mt-3">
                        <div class="form-check"><input type="checkbox" id="rxSpaces" class="form-check-input" checked><label class="form-check-label" for="rxSpaces">Collapse multiple spaces into one</label></div>
                        <div class="form-check"><input type="checkbox" id="rxTabs" class="form-check-input" checked><label class="form-check-label" for="rxTabs">Replace tabs with a space</label></div>
                        <div class="form-check"><input type="checkbox" id="rxTrim" class="form-check-input" checked><label class="form-check-label" for="rxTrim">Trim spaces at line ends</label></div>
                        <div class="form-check"><input type="checkbox" id="rxBlank" class="form-check-input" checked><label class="form-check-label" for="rxBlank">Remove blank lines</label></div>
                    </div>
                    <button type="button" id="rxGo" class="btn btn-primary btn-lg w-100 mt-3">Clean Text</button>
                    <p class="mt-3 mb-0">Characters before: <strong id="rxBefore">0</strong> &nbsp;·&nbsp; After: <strong id="rxAfter">0</strong> &nbsp;·&nbsp; Saved: <strong id="rxSaved">0</strong></p>
                    <label class="form-label mt-2" for="rxOut">Cleaned text</label>
                    <textarea id="rxOut" class="form-control" rows="7" readonly></textarea>
                    <div class="d-flex gap-2 mt-2"><button type="button" id="rxCopy" class="btn btn-outline-secondary">Copy Result</button><button type="button" id="rxUse" class="btn btn-outline-secondary">Use Result as Input</button></div>

            <div id="rxMsg" class="alert alert-info d-none mt-3" role="alert"></div>
        </div>
    </div>
    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                    <li>Paste the messy text.</li>
                    <li>Tick the cleanups you want.</li>
                    <li>Click Clean Text and copy the tidy result.</li>
            </ol>
            <p class="small text-muted mb-0">Line breaks between paragraphs are kept as single line breaks when blank line removal is on. Turn it off if your text relies on paragraph spacing.</p>
        </div>
    </div>
</div>
@endsection
@section('scripts')

<script>
(function () {
    'use strict';

    var msgBox = document.getElementById("rxMsg");
    function showMsg(t, ok) { msgBox.textContent = t; msgBox.className = "alert mt-3 " + (ok === false ? "alert-danger" : "alert-info"); msgBox.classList.remove("d-none"); }

    function copyText(v, label) {
        if (!v) { showMsg("Nothing to copy yet.", false); return; }
        if (navigator.clipboard) navigator.clipboard.writeText(v).then(function () { showMsg(label || "Copied to clipboard."); }).catch(function () { showMsg("Copy failed. Select the text and copy manually.", false); });
        else showMsg("Clipboard is not available in this browser.", false);
    }

    document.getElementById("rxGo").addEventListener("click", function () {
        var t = document.getElementById("rxIn").value, before = t.length;
        if (document.getElementById("rxTabs").checked) t = t.replace(/\t/g, " ");
        var lines = t.split("\n");
        if (document.getElementById("rxTrim").checked) lines = lines.map(function (l) { return l.replace(/^\s+|\s+$/g, ""); });
        if (document.getElementById("rxSpaces").checked) lines = lines.map(function (l) { return l.replace(/ {2,}/g, " "); });
        if (document.getElementById("rxBlank").checked) lines = lines.filter(function (l) { return l.trim() !== ""; });
        t = lines.join("\n").trim();
        document.getElementById("rxBefore").textContent = before;
        document.getElementById("rxAfter").textContent = t.length;
        document.getElementById("rxSaved").textContent = Math.max(0, before - t.length);
        document.getElementById("rxOut").value = t;
        showMsg("Cleaned. Removed " + Math.max(0, before - t.length) + " characters of clutter.");
    });
    document.getElementById("rxCopy").addEventListener("click", function () { copyText(document.getElementById("rxOut").value, "Cleaned text copied."); });
    document.getElementById("rxUse").addEventListener("click", function () { document.getElementById("rxIn").value = document.getElementById("rxOut").value; showMsg("Result moved into the input box."); });

})();
</script>
@endsection
