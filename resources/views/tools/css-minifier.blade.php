@extends('layouts.app')

@section('title', 'CSS Minifier — Free Online Tool')
@section('meta_description', 'Minify CSS code to reduce file size and speed up websites')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">CSS Minifier</h1>
            <p class="lead small text-muted">Paste CSS to strip comments and unnecessary whitespace. The result, size saving and percentage are shown instantly.</p>
            <label class="form-label" for="cmIn">Your CSS</label>
            <textarea class="form-control font-monospace" id="cmIn" rows="8" placeholder="Paste CSS here..."></textarea>
            <button type="button" class="btn btn-primary mt-2" id="cmBtn">Minify CSS</button>
            <button type="button" class="btn btn-success mt-2" id="cmCopy">Copy result</button>
            <label class="form-label mt-3" for="cmOut">Minified CSS</label>
            <textarea class="form-control font-monospace" id="cmOut" rows="6" readonly></textarea>
            <p class="small text-muted mt-2 mb-0" id="cmStats"></p>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol><li>Paste your CSS into the first box.</li><li>Click Minify CSS.</li><li>Copy the minified result and check the size saving.</li></ol>
            <p class="small text-muted mb-0">Note: Minification here removes comments and safe whitespace only — it does not rename classes or change values, so the output behaves exactly like the input.</p>
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
    el("cmBtn").addEventListener("click", function () {
        var src = el("cmIn").value;
        if (!src.trim()) { el("cmStats").textContent = "Paste some CSS first."; return; }
        var out = src.replace(/\/\*[\s\S]*?\*\//g, "").replace(/\s+/g, " ").replace(/\s*([{}:;,>~+])\s*/g, "$1").replace(/;}/g, "}").trim();
        el("cmOut").value = out;
        var saved = src.length - out.length, pct = src.length ? (saved / src.length * 100).toFixed(1) : 0;
        el("cmStats").textContent = "Original " + src.length + " characters → minified " + out.length + " characters (saved " + saved + ", " + pct + "%).";
    });
    el("cmCopy").addEventListener("click", function () { copyText("cmOut", el("cmCopy")); });
})();
</script>
@endsection
