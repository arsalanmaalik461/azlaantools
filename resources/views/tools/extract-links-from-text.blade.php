@extends('layouts.app')
@section('title', 'Extract Links from Text — Free Online Tool')
@section('meta_description', 'Extract all URLs and website links from any block of text')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3">Extract Links from Text</h1>
            <p class="lead small text-muted">Paste any block of text and extract every URL and website link into a clean list for audits and cleanup.</p>

                    <label class="form-label fw-semibold" for="elIn">Your text</label>
                    <textarea id="elIn" class="form-control" rows="7" placeholder="Paste or type text here"></textarea>

                    <div class="form-check mt-3"><input type="checkbox" id="elDedupe" class="form-check-input" checked><label class="form-check-label" for="elDedupe">Remove duplicate links</label></div>

                    <button type="button" id="elGo" class="btn btn-primary btn-lg w-100 mt-3">Extract</button>
                    <label class="form-label mt-3" for="elOut">Results (<span id="elCount">0</span>)</label>
                    <textarea id="elOut" class="form-control" rows="8" readonly></textarea>
                    <div class="d-flex gap-2 mt-2"><button type="button" id="elCopy" class="btn btn-outline-secondary">Copy Results</button><button type="button" id="elDl" class="btn btn-outline-secondary">Download .txt</button></div>

            <div id="elMsg" class="alert alert-info d-none mt-3" role="alert"></div>
        </div>
    </div>
    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                    <li>Paste the text containing links.</li>
                    <li>Click Extract to list every http, https and www link.</li>
                    <li>Copy or download the list for your audit or cleanup work.</li>
            </ol>
            <p class="small text-muted mb-0">Trailing punctuation such as a full stop at the end of a sentence is trimmed from each link. Bare domains without www or http are not picked up.</p>
        </div>
    </div>
</div>
@endsection
@section('scripts')

<script>
(function () {
    'use strict';

    var msgBox = document.getElementById("elMsg");
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
        var text = document.getElementById("elIn").value;
        var found = text.match(/(https?:\/\/[^\s<>"')\]]+|www\.[^\s<>"')\]]+)/gi) || [];
        found = found.map(function (u) { return u.replace(/[.,;:]+$/, ""); });
        if (document.getElementById("elDedupe").checked) { var seen = {}; found = found.filter(function (u) { if (seen[u]) return false; seen[u] = 1; return true; }); }
        document.getElementById("elOut").value = found.join("\n");
        document.getElementById("elCount").textContent = found.length;
        showMsg(found.length ? "Found " + found.length + " link(s)." : "No links found in this text.");
    }

    document.getElementById("elGo").addEventListener("click", runExtract);
    document.getElementById("elCopy").addEventListener("click", function () { copyText(document.getElementById("elOut").value, "Results copied."); });
    document.getElementById("elDl").addEventListener("click", function () { var v = document.getElementById("elOut").value; if (!v) { showMsg("Nothing to download yet.", false); return; } dl(new Blob([v], { type: "text/plain" }), "extracted.txt"); });

})();
</script>
@endsection
