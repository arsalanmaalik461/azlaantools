@extends('layouts.app')

@section('title', 'XML Formatter and Validator — Free Online Tool')
@section('meta_description', 'Format validate and beautify XML with clear error locations')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">XML Formatter and Validator</h1>
            <p class="lead small text-muted">Paste XML to validate it and pretty-print it with clean indentation — invalid XML reports the parser error instead of silent garbage.</p>
            <label class="form-label" for="xfIn">Your XML</label>
            <textarea class="form-control font-monospace" id="xfIn" rows="8" placeholder="<root><item id=&quot;1&quot;>Value</item></root>">&lt;root&gt;&lt;item id="1"&gt;Value&lt;/item&gt;&lt;item id="2"&gt;Second&lt;/item&gt;&lt;/root&gt;</textarea>
            <div class="d-flex gap-2 mt-2">
                <button type="button" class="btn btn-primary" id="xfBtn">Format and validate</button>
                <button type="button" class="btn btn-secondary" id="xfMin">Minify</button>
                <button type="button" class="btn btn-success" id="xfCopy">Copy result</button>
            </div>
            <div class="alert alert-danger mt-3 d-none" id="xfErr"></div>
            <div class="alert alert-success mt-3 d-none" id="xfOk">XML is valid.</div>
            <label class="form-label mt-3" for="xfOut">Result</label>
            <textarea class="form-control font-monospace" id="xfOut" rows="10" readonly></textarea>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol><li>Paste your XML.</li><li>Click Format and validate — valid XML is beautified, invalid XML shows the parser error.</li><li>Or click Minify to remove the indentation again.</li></ol>
            <p class="small text-muted mb-0">Note: Validation uses the browser DOMParser, which checks well-formedness (matching tags, quoting and nesting). It does not validate against a DTD or schema.</p>
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
    function serialize(node, level, lines) {
        var pad = "", i; for (i = 0; i < level; i++) pad += "  ";
        if (node.nodeType === 3) { var t = node.nodeValue.trim(); if (t) lines.push(pad + t); return; }
        if (node.nodeType === 8) { lines.push(pad + "<!--" + node.nodeValue + "-->"); return; }
        if (node.nodeType !== 1) return;
        var attrs = "";
        for (i = 0; i < node.attributes.length; i++) { attrs += " " + node.attributes[i].name + "=\"" + node.attributes[i].value + "\""; }
        var kids = [];
        node.childNodes.forEach(function (ch) { if (ch.nodeType === 1 || ch.nodeType === 8 || (ch.nodeType === 3 && ch.nodeValue.trim())) kids.push(ch); });
        if (!kids.length) { lines.push(pad + "<" + node.tagName + attrs + " />"); return; }
        if (kids.length === 1 && kids[0].nodeType === 3) { lines.push(pad + "<" + node.tagName + attrs + ">" + kids[0].nodeValue.trim() + "</" + node.tagName + ">"); return; }
        lines.push(pad + "<" + node.tagName + attrs + ">");
        kids.forEach(function (ch) { serialize(ch, level + 1, lines); });
        lines.push(pad + "</" + node.tagName + ">");
    }
    function parseXml(text) {
        var doc = new DOMParser().parseFromString(text, "text/xml");
        var errNode = doc.querySelector("parsererror");
        if (errNode) throw new Error(errNode.textContent.split("\n")[0] || "XML parse error");
        return doc;
    }
    function showErr(e) { var err = el("xfErr"); err.textContent = "Invalid XML: " + e.message; err.classList.remove("d-none"); el("xfOk").classList.add("d-none"); }
    el("xfBtn").addEventListener("click", function () {
        el("xfErr").classList.add("d-none");
        try { var doc = parseXml(el("xfIn").value); var lines = []; serialize(doc.documentElement, 0, lines); el("xfOut").value = lines.join("\n"); el("xfOk").classList.remove("d-none"); }
        catch (e) { showErr(e); el("xfOut").value = ""; }
    });
    el("xfMin").addEventListener("click", function () {
        el("xfErr").classList.add("d-none");
        try { var doc = parseXml(el("xfIn").value); el("xfOut").value = new XMLSerializer().serializeToString(doc).replace(/>\s+</g, "><").trim(); el("xfOk").classList.remove("d-none"); }
        catch (e) { showErr(e); }
    });
    el("xfCopy").addEventListener("click", function () { copyText("xfOut", el("xfCopy")); });
})();
</script>
@endsection
