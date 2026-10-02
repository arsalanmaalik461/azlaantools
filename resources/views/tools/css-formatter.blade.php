@extends('layouts.app')

@section('title', 'CSS Formatter and Beautifier — Free Online Tool')
@section('meta_description', 'Format messy CSS into clean readable code with proper indentation')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">CSS Formatter and Beautifier</h1>
            <p class="lead small text-muted">Paste messy or minified CSS and get it back cleanly indented, one declaration per line. Comments and quoted strings are preserved.</p>
            <label class="form-label" for="cfIn">Your CSS</label>
            <textarea class="form-control font-monospace" id="cfIn" rows="8" placeholder="Paste CSS here..."></textarea>
            <div class="row g-2 mt-2 align-items-end">
                <div class="col-md-4"><label class="form-label" for="cfIndent">Indent</label><select class="form-select" id="cfIndent"><option value="2">2 spaces</option><option value="4" selected>4 spaces</option></select></div>
                <div class="col-md-4"><button type="button" class="btn btn-primary w-100" id="cfBtn">Format CSS</button></div>
                <div class="col-md-4"><button type="button" class="btn btn-success w-100" id="cfCopy">Copy result</button></div>
            </div>
            <label class="form-label mt-3" for="cfOut">Formatted CSS</label>
            <textarea class="form-control font-monospace" id="cfOut" rows="10" readonly></textarea>
            <p class="small text-muted mt-2 mb-0" id="cfStats"></p>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol><li>Paste your CSS into the first box.</li><li>Choose an indent size and click Format CSS.</li><li>Copy the clean result back into your stylesheet.</li></ol>
            <p class="small text-muted mb-0">Note: The formatter is a structural beautifier for standard CSS. Very unusual syntax inside strings is preserved as-is.</p>
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
    function formatCss(src, indentSize) {
        var pad = "", i; for (i = 0; i < indentSize; i++) pad += " ";
        var out = "", level = 0, buf = "", inStr = null, inComment = false;
        function flushLine() { var t = buf.trim(); if (t) { var pre = ""; for (var k = 0; k < level; k++) pre += pad; out += pre + t + "\n"; } buf = ""; }
        for (i = 0; i < src.length; i++) {
            var c = src.charAt(i), n = src.charAt(i + 1);
            if (inComment) { buf += c; if (c === "*" && n === "/") { buf += n; i++; inComment = false; } continue; }
            if (inStr) { buf += c; if (c === inStr) inStr = null; continue; }
            if (c === "/" && n === "*") { inComment = true; buf += c; continue; }
            if (c === "\"" || c === "'") { inStr = c; buf += c; continue; }
            if (c === "{") { buf += " {"; flushLine(); level++; continue; }
            if (c === "}") { flushLine(); level = Math.max(0, level - 1); buf = "}"; flushLine(); continue; }
            if (c === ";") { buf += ";"; flushLine(); continue; }
            if (c === ":") { buf += ": "; continue; }
            if (c === ",") { buf += ", "; continue; }
            if (/\s/.test(c)) { if (buf && buf.slice(-1) !== " ") buf += " "; continue; }
            buf += c;
        }
        flushLine();
        return out.trim();
    }
    el("cfBtn").addEventListener("click", function () {
        var src = el("cfIn").value;
        if (!src.trim()) { el("cfStats").textContent = "Paste some CSS first."; return; }
        var out = formatCss(src, parseInt(el("cfIndent").value, 10));
        el("cfOut").value = out;
        el("cfStats").textContent = "Formatted: " + src.length + " characters in, " + out.length + " characters out.";
    });
    el("cfCopy").addEventListener("click", function () { copyText("cfOut", el("cfCopy")); });
})();
</script>
@endsection
