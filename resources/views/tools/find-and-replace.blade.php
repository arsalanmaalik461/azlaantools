@extends('layouts.app')
@section('title', 'Find and Replace Text — Free Online Tool')
@section('meta_description', 'Find any word in text and replace all matches in one click')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3">Find and Replace Text</h1>
            <p class="lead small text-muted">Find any word or phrase in your text and replace every match in one click, with case, whole word and regex options.</p>

                    <label class="form-label fw-semibold" for="frIn">Your text</label>
                    <textarea id="frIn" class="form-control" rows="7" placeholder="Paste or type text here"></textarea>

                    <div class="row g-3 mt-1">
                        <div class="col-md-5"><label class="form-label" for="frFind">Find</label><input type="text" id="frFind" class="form-control"></div>
                        <div class="col-md-5"><label class="form-label" for="frRep">Replace with</label><input type="text" id="frRep" class="form-control"></div>
                        <div class="col-md-2 d-flex align-items-end"><button type="button" id="frGo" class="btn btn-primary w-100">Replace All</button></div>
                    </div>
                    <div class="d-flex flex-wrap gap-3 mt-3">
                        <div class="form-check"><input type="checkbox" id="frCase" class="form-check-input"><label class="form-check-label" for="frCase">Match case</label></div>
                        <div class="form-check"><input type="checkbox" id="frWord" class="form-check-input"><label class="form-check-label" for="frWord">Whole words only</label></div>
                        <div class="form-check"><input type="checkbox" id="frRegex" class="form-check-input"><label class="form-check-label" for="frRegex">Use regular expression</label></div>
                    </div>
                    <p class="mt-2 mb-0">Matches found: <strong id="frCount">0</strong></p>
                    <label class="form-label mt-2" for="frOut">Result</label>
                    <textarea id="frOut" class="form-control" rows="7" readonly></textarea>
                    <div class="d-flex gap-2 mt-2"><button type="button" id="frCopy" class="btn btn-outline-secondary">Copy Result</button><button type="button" id="frUse" class="btn btn-outline-secondary">Use Result as Input</button></div>

            <div id="frMsg" class="alert alert-info d-none mt-3" role="alert"></div>
        </div>
    </div>
    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                    <li>Paste your text and type what to find and what to replace it with.</li>
                    <li>Tick match case, whole words or regex if you need them.</li>
                    <li>Click Replace All and copy the result, or feed it back as input for another pass.</li>
            </ol>
            <p class="small text-muted mb-0">In regex mode the find box is a JavaScript regular expression and the replacement can use $1 style group references. An invalid pattern shows an error instead of breaking the text.</p>
        </div>
    </div>
</div>
@endsection
@section('scripts')

<script>
(function () {
    'use strict';

    var msgBox = document.getElementById("frMsg");
    function showMsg(t, ok) { msgBox.textContent = t; msgBox.className = "alert mt-3 " + (ok === false ? "alert-danger" : "alert-info"); msgBox.classList.remove("d-none"); }

    function copyText(v, label) {
        if (!v) { showMsg("Nothing to copy yet.", false); return; }
        if (navigator.clipboard) navigator.clipboard.writeText(v).then(function () { showMsg(label || "Copied to clipboard."); }).catch(function () { showMsg("Copy failed. Select the text and copy manually.", false); });
        else showMsg("Clipboard is not available in this browser.", false);
    }

    function buildRegex() {
        var find = document.getElementById("frFind").value;
        if (!find) return null;
        var flags = document.getElementById("frCase").checked ? "g" : "gi";
        if (document.getElementById("frRegex").checked) return new RegExp(find, flags);
        var esc = find.replace(/[.*+?^${}()|[\]\\]/g, "\\$&");
        if (document.getElementById("frWord").checked) esc = "\\b" + esc + "\\b";
        return new RegExp(esc, flags);
    }
    function countMatches() {
        var re; try { re = buildRegex(); } catch (e) { return; }
        if (!re) { document.getElementById("frCount").textContent = 0; return; }
        var m = document.getElementById("frIn").value.match(re);
        document.getElementById("frCount").textContent = m ? m.length : 0;
    }
    document.getElementById("frGo").addEventListener("click", function () {
        var re; try { re = buildRegex(); } catch (e) { showMsg("That regular expression is not valid.", false); return; }
        if (!re) { showMsg("Type something in the Find box first.", false); return; }
        var before = document.getElementById("frIn").value;
        var m = before.match(re), n = m ? m.length : 0;
        document.getElementById("frOut").value = before.replace(re, document.getElementById("frRep").value);
        document.getElementById("frCount").textContent = n;
        showMsg("Replaced " + n + " match(es).");
    });
    ["frIn", "frFind", "frCase", "frWord", "frRegex"].forEach(function (id) { document.getElementById(id).addEventListener("input", countMatches); document.getElementById(id).addEventListener("change", countMatches); });
    document.getElementById("frCopy").addEventListener("click", function () { copyText(document.getElementById("frOut").value, "Result copied."); });
    document.getElementById("frUse").addEventListener("click", function () { document.getElementById("frIn").value = document.getElementById("frOut").value; countMatches(); showMsg("Result moved into the input box."); });

})();
</script>
@endsection
