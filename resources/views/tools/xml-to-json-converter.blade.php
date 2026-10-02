@extends('layouts.app')

@section('title', 'XML to JSON Converter — Free Online Tool')
@section('meta_description', 'Convert XML documents into JSON objects and download the result')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">XML to JSON Converter</h1>
            <p class="lead small text-muted">Paste an XML document and convert it to JSON — attributes are kept with an @ prefix, repeated elements become arrays, and the result can be downloaded.</p>
            <label class="form-label" for="xjIn">Your XML</label>
            <textarea class="form-control font-monospace" id="xjIn" rows="8">&lt;order id="101"&gt;&lt;customer&gt;Ali&lt;/customer&gt;&lt;item&gt;Panel&lt;/item&gt;&lt;item&gt;Inverter&lt;/item&gt;&lt;/order&gt;</textarea>
            <div class="d-flex gap-2 mt-2">
                <button type="button" class="btn btn-primary" id="xjBtn">Convert to JSON</button>
                <button type="button" class="btn btn-success" id="xjDl">Download JSON</button>
            </div>
            <div class="alert alert-danger mt-3 d-none" id="xjErr"></div>
            <label class="form-label mt-3" for="xjOut">JSON output</label>
            <textarea class="form-control font-monospace" id="xjOut" rows="10" readonly></textarea>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol><li>Paste your XML document.</li><li>Click Convert to JSON.</li><li>Copy the result or download it as a .json file.</li></ol>
            <p class="small text-muted mb-0">Note: Conversion convention: element text becomes the value, attributes are stored under @name keys, mixed text uses #text, and repeated child tags become arrays.</p>
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
    function unusedSerialize(node, level, lines) {
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
    function nodeToObj(node) {
        var obj = {}, i, hasKids = false;
        for (i = 0; i < node.attributes.length; i++) { obj["@" + node.attributes[i].name] = node.attributes[i].value; hasKids = true; }
        var text = "";
        node.childNodes.forEach(function (ch) {
            if (ch.nodeType === 3) { text += ch.nodeValue; }
            else if (ch.nodeType === 1) {
                hasKids = true;
                var v = nodeToObj(ch);
                if (obj[ch.tagName] === undefined) obj[ch.tagName] = v;
                else if (Array.isArray(obj[ch.tagName])) obj[ch.tagName].push(v);
                else obj[ch.tagName] = [obj[ch.tagName], v];
            }
        });
        text = text.trim();
        if (!hasKids) return text;
        if (text) obj["#text"] = text;
        return obj;
    }
    el("xjBtn").addEventListener("click", function () {
        var err = el("xjErr"); err.classList.add("d-none");
        try { var doc = parseXml(el("xjIn").value); var root = {}; root[doc.documentElement.tagName] = nodeToObj(doc.documentElement); el("xjOut").value = JSON.stringify(root, null, 2); }
        catch (e) { err.textContent = "Invalid XML: " + e.message; err.classList.remove("d-none"); el("xjOut").value = ""; }
    });
    el("xjDl").addEventListener("click", function () { if (el("xjOut").value) download("converted.json", el("xjOut").value, "application/json"); });
})();
</script>
@endsection
