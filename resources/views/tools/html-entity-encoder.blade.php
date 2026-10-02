@extends('layouts.app')

@section('title', 'HTML Entity Encoder Decoder — Free Online Tool')
@section('meta_description', 'Encode special characters to HTML entities and decode them back')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">HTML Entity Encoder Decoder</h1>
            <p class="lead small text-muted">Convert special characters like angle brackets, quotes and ampersands into HTML entities so code displays safely in a page — and decode entities back to plain text.</p>
            <label class="form-label" for="heIn">Input text</label>
            <textarea class="form-control font-monospace" id="heIn" rows="6" placeholder="Type or paste text here..."></textarea>
            <div class="form-check mt-2"><input class="form-check-input" type="checkbox" id="heNumeric"><label class="form-check-label" for="heNumeric">Also encode non-ASCII characters as numeric entities</label></div>
            <div class="d-flex gap-2 mt-2">
                <button type="button" class="btn btn-primary" id="heEnc">Encode</button>
                <button type="button" class="btn btn-secondary" id="heDec">Decode</button>
                <button type="button" class="btn btn-success" id="heCopy">Copy output</button>
            </div>
            <label class="form-label mt-3" for="heOut">Output</label>
            <textarea class="form-control font-monospace" id="heOut" rows="6" readonly></textarea>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol><li>Paste the text or code snippet.</li><li>Click Encode to make it safe to display in HTML, or Decode to turn entities back into characters.</li><li>Copy the output.</li></ol>
            <p class="small text-muted mb-0">Note: Encoding the five core characters (ampersand, angle brackets and both quote types) prevents broken markup and most accidental HTML injection in snippets.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    "use strict";
    function el(id) { return document.getElementById(id); }
    function num(id) { var v = parseFloat(el(id).value); return isNaN(v) ? 0 : v; }
    function fmt(n) { return Number(n).toLocaleString("en-US", { maximumFractionDigits: 2 }); }
    function rs(n) { return "Rs " + Math.round(n).toLocaleString("en-US"); }
    function copyText(id, btn) { var t = el(id); if (!t) return; var v = t.value !== undefined && t.tagName !== "DIV" ? t.value : t.textContent; if (navigator.clipboard) { navigator.clipboard.writeText(v); } if (btn) { var o = btn.textContent; btn.textContent = "Copied"; setTimeout(function () { btn.textContent = o; }, 1200); } }
    function download(name, text, type) { var b = new Blob([text], { type: type || "text/plain" }); var a = document.createElement("a"); a.href = URL.createObjectURL(b); a.download = name; a.click(); setTimeout(function () { URL.revokeObjectURL(a.href); }, 500); }
    var named = { "&": "&amp;", "<": "&lt;", ">": "&gt;", "\"": "&quot;", "'": "&#39;" };
    el("heEnc").addEventListener("click", function () {
        var src = el("heIn").value, useNum = el("heNumeric").checked, out = "", i, ch;
        for (i = 0; i < src.length; i++) { ch = src.charAt(i); if (named[ch]) out += named[ch]; else if (useNum && src.charCodeAt(i) > 127) out += "&#" + src.charCodeAt(i) + ";"; else out += ch; }
        el("heOut").value = out;
    });
    el("heDec").addEventListener("click", function () {
        var t = document.createElement("textarea"); t.innerHTML = el("heIn").value; el("heOut").value = t.value;
    });
    el("heCopy").addEventListener("click", function () { copyText("heOut", el("heCopy")); });
})();
</script>
@endsection
