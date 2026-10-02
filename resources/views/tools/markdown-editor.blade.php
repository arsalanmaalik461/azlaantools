@extends('layouts.app')

@section('title', 'Markdown Editor and Previewer — Free Online Tool')
@section('meta_description', 'Write markdown with live preview and export HTML or a file download')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Markdown Editor and Previewer</h1>
            <p class="lead small text-muted">Write Markdown on the left and see the rendered HTML live on the right — then download the Markdown file or the generated HTML. Your text stays in your browser.</p>
            <div class="row g-3">
                <div class="col-md-6"><label class="form-label" for="mdIn">Markdown</label><textarea class="form-control font-monospace" id="mdIn" rows="14"># Hello

Write **bold**, *italic* and lists:

- First item
- Second item

Add [a link](https://example.com) or code with backticks.</textarea><p class="small text-muted mt-1" id="mdStats"></p></div>
                <div class="col-md-6"><label class="form-label">Live preview</label><div class="border rounded p-3" id="mdPreview" style="min-height:300px"></div></div>
            </div>
            <div class="d-flex gap-2 mt-3">
                <button type="button" class="btn btn-success" id="mdDlMd">Download .md</button>
                <button type="button" class="btn btn-primary" id="mdDlHtml">Download HTML</button>
            </div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol><li>Type or paste Markdown in the left box.</li><li>Watch the preview update as you type.</li><li>Download the .md file or a standalone HTML file.</li></ol>
            <p class="small text-muted mb-0">Note: Preview rendering uses the marked library loaded from a CDN. If it cannot load, the preview shows your text safely escaped instead.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/marked@12.0.2/marked.min.js"></script>
<script>
(function () {
    "use strict";
    function el(id) { return document.getElementById(id); }
    function num(id) { var v = parseFloat(el(id).value); return isNaN(v) ? 0 : v; }
    function fmt(n) { return Number(n).toLocaleString("en-US", { maximumFractionDigits: 2 }); }
    function rs(n) { return "Rs " + Math.round(n).toLocaleString("en-US"); }
    function copyText(id, btn) { var t = el(id); if (!t) return; var v = t.value !== undefined && t.tagName !== "DIV" ? t.value : t.textContent; if (navigator.clipboard) { navigator.clipboard.writeText(v); } if (btn) { var o = btn.textContent; btn.textContent = "Copied"; setTimeout(function () { btn.textContent = o; }, 1200); } }
    function download(name, text, type) { var b = new Blob([text], { type: type || "text/plain" }); var a = document.createElement("a"); a.href = URL.createObjectURL(b); a.download = name; a.click(); setTimeout(function () { URL.revokeObjectURL(a.href); }, 500); }
    function render() {
        var src = el("mdIn").value;
        el("mdStats").textContent = src.length + " characters, " + (src.trim() ? src.trim().split(/\s+/).length : 0) + " words.";
        if (window.marked && marked.parse) { el("mdPreview").innerHTML = marked.parse(src); }
        else { el("mdPreview").textContent = src; }
    }
    el("mdIn").addEventListener("input", render);
    el("mdDlMd").addEventListener("click", function () { download("document.md", el("mdIn").value, "text/markdown"); });
    el("mdDlHtml").addEventListener("click", function () { download("document.html", "<!DOCTYPE html>\n<html><head><meta charset=\"utf-8\"><title>Document</title></head><body>\n" + el("mdPreview").innerHTML + "\n</body></html>", "text/html"); });
    render();
})();
</script>
@endsection
