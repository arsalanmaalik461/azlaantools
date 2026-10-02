@extends('layouts.app')

@section('title', 'HTML Minifier — Free Online Tool')
@section('meta_description', 'Minify HTML code by removing comments and extra whitespace')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">HTML Minifier</h1>
            <p class="lead small text-muted">Paste HTML to remove comments and collapse unnecessary whitespace between tags, with a live size-saving report.</p>
            <label class="form-label" for="hmIn">Your HTML</label>
            <textarea class="form-control font-monospace" id="hmIn" rows="8" placeholder="Paste HTML here..."></textarea>
            <div class="form-check mt-2"><input class="form-check-input" type="checkbox" id="hmComments" checked><label class="form-check-label" for="hmComments">Remove HTML comments</label></div>
            <button type="button" class="btn btn-primary mt-2" id="hmBtn">Minify HTML</button>
            <button type="button" class="btn btn-success mt-2" id="hmCopy">Copy result</button>
            <label class="form-label mt-3" for="hmOut">Minified HTML</label>
            <textarea class="form-control font-monospace" id="hmOut" rows="6" readonly></textarea>
            <p class="small text-muted mt-2 mb-0" id="hmStats"></p>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol><li>Paste your HTML into the first box.</li><li>Choose whether to remove comments, then click Minify HTML.</li><li>Copy the minified result and check the saving.</li></ol>
            <p class="small text-muted mb-0">Note: Content inside pre, textarea, script and style blocks can be whitespace-sensitive — review minified output before using it on a live page.</p>
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
    el("hmBtn").addEventListener("click", function () {
        var src = el("hmIn").value;
        if (!src.trim()) { el("hmStats").textContent = "Paste some HTML first."; return; }
        var out = src;
        if (el("hmComments").checked) out = out.replace(/<!--[\s\S]*?-->/g, "");
        out = out.replace(/>\s+</g, "><").replace(/\n\s*/g, " ").replace(/ {2,}/g, " ").trim();
        el("hmOut").value = out;
        var saved = src.length - out.length, pct = src.length ? (saved / src.length * 100).toFixed(1) : 0;
        el("hmStats").textContent = "Original " + src.length + " characters → minified " + out.length + " characters (saved " + saved + ", " + pct + "%).";
    });
    el("hmCopy").addEventListener("click", function () { copyText("hmOut", el("hmCopy")); });
})();
</script>
@endsection
