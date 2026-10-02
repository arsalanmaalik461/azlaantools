@extends('layouts.app')
@section('title', 'Extract Emails from Text — Free Online Tool')
@section('meta_description', 'Pull all email addresses out of pasted text into a clean list')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3">Extract Emails from Text</h1>
            <p class="lead small text-muted">Paste any messy text and pull out every email address into a clean, deduplicated list you can copy or download.</p>

                    <label class="form-label fw-semibold" for="emIn">Your text</label>
                    <textarea id="emIn" class="form-control" rows="7" placeholder="Paste or type text here"></textarea>

                    <div class="d-flex flex-wrap gap-3 mt-3">
                        <div class="form-check"><input type="checkbox" id="emDedupe" class="form-check-input" checked><label class="form-check-label" for="emDedupe">Remove duplicates</label></div>
                        <div><label class="form-label me-2" for="emSep">Separator</label><select id="emSep" class="form-select d-inline-block w-auto"><option value="line">One per line</option><option value="comma">Comma separated</option></select></div>
                    </div>

                    <button type="button" id="emGo" class="btn btn-primary btn-lg w-100 mt-3">Extract</button>
                    <label class="form-label mt-3" for="emOut">Results (<span id="emCount">0</span>)</label>
                    <textarea id="emOut" class="form-control" rows="8" readonly></textarea>
                    <div class="d-flex gap-2 mt-2"><button type="button" id="emCopy" class="btn btn-outline-secondary">Copy Results</button><button type="button" id="emDl" class="btn btn-outline-secondary">Download .txt</button></div>

            <div id="emMsg" class="alert alert-info d-none mt-3" role="alert"></div>
        </div>
    </div>
    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                    <li>Paste the text containing email addresses.</li>
                    <li>Choose deduplication and separator options.</li>
                    <li>Click Extract, then copy or download the clean list.</li>
            </ol>
            <p class="small text-muted mb-0">Only addresses that match the standard email pattern are picked up. Obfuscated addresses written as name at domain dot com are not detected.</p>
        </div>
    </div>
</div>
@endsection
@section('scripts')

<script>
(function () {
    'use strict';

    var msgBox = document.getElementById("emMsg");
    function showMsg(t, ok) { msgBox.textContent = t; msgBox.className = "alert mt-3 " + (ok === false ? "alert-danger" : "alert-info"); msgBox.classList.remove("d-none"); }

    function copyText(v, label) {
        if (!v) { showMsg("Nothing to copy yet.", false); return; }
        if (navigator.clipboard) navigator.clipboard.writeText(v).then(function () { showMsg(label || "Copied to clipboard."); }).catch(function () { showMsg("Copy failed. Select the text and copy manually.", false); });
        else showMsg("Clipboard is not available in this browser.", false);
    }

    function dl(blob, name) {
        var a = document.createElement("a");
        a.href = URL.createObjectURL(blob);
        a.download = name;
        document.body.appendChild(a);
        a.click();
        a.remove();
        setTimeout(function () { URL.revokeObjectURL(a.href); }, 3000);
    }
    function fmtBytes(b) {
        if (!b && b !== 0) return "-";
        if (b < 1024) return b + " B";
        if (b < 1048576) return (b / 1024).toFixed(1) + " KB";
        return (b / 1048576).toFixed(2) + " MB";
    }

    function runExtract() {
        var text = document.getElementById("emIn").value;
        var found = text.match(/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/g) || [];
        if (document.getElementById("emDedupe").checked) { var seen = {}; found = found.filter(function (e) { var k = e.toLowerCase(); if (seen[k]) return false; seen[k] = 1; return true; }); }
        var sep = document.getElementById("emSep").value;
        document.getElementById("emOut").value = found.join(sep === "comma" ? ", " : "\n");
        document.getElementById("emCount").textContent = found.length;
        showMsg(found.length ? "Found " + found.length + " email address(es)." : "No email addresses found in this text.");
    }

    document.getElementById("emGo").addEventListener("click", runExtract);
    document.getElementById("emCopy").addEventListener("click", function () { copyText(document.getElementById("emOut").value, "Results copied."); });
    document.getElementById("emDl").addEventListener("click", function () { var v = document.getElementById("emOut").value; if (!v) { showMsg("Nothing to download yet.", false); return; } dl(new Blob([v], { type: "text/plain" }), "extracted.txt"); });

})();
</script>
@endsection
