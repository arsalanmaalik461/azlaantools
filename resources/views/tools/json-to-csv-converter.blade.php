@extends('layouts.app')

@section('title', 'JSON to CSV Converter — Free Online Tool')
@section('meta_description', 'Convert JSON arrays into downloadable CSV files in one click')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">JSON to CSV Converter</h1>
            <p class="lead small text-muted">Paste a JSON array of objects — nested objects are flattened with dot keys — and download a clean CSV file.</p>
            <label class="form-label" for="jcIn">JSON input</label>
            <textarea class="form-control font-monospace" id="jcIn" rows="8" placeholder="[{&quot;name&quot;:&quot;Ali&quot;,&quot;city&quot;:&quot;Lahore&quot;}]"></textarea>
            <div class="d-flex gap-2 mt-2">
                <button type="button" class="btn btn-primary" id="jcBtn">Convert to CSV</button>
                <button type="button" class="btn btn-success" id="jcDl">Download CSV</button>
            </div>
            <div class="alert alert-danger mt-3 d-none" id="jcErr"></div>
            <label class="form-label mt-3" for="jcOut">CSV output</label>
            <textarea class="form-control font-monospace" id="jcOut" rows="8" readonly></textarea>
            <p class="small text-muted mt-2 mb-0" id="jcStats"></p>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol><li>Paste a JSON array (or a single JSON object).</li><li>Click Convert to CSV to flatten and build the file.</li><li>Download the CSV or copy the text.</li></ol>
            <p class="small text-muted mb-0">Note: Arrays inside objects are joined with a semicolon. Keys that appear in only some records become empty cells in the other rows.</p>
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
    function esc(v) { var s = String(v === null || v === undefined ? "" : v); if (s.indexOf(",") >= 0 || s.indexOf("\"") >= 0 || s.indexOf("\n") >= 0) return "\"" + s.replace(/\"/g, "\"\"") + "\""; return s; }
    function flatten(obj, prefix, out) {
        Object.keys(obj).forEach(function (k) {
            var key = prefix ? prefix + "." + k : k, v = obj[k];
            if (v && typeof v === "object" && !Array.isArray(v)) flatten(v, key, out);
            else if (Array.isArray(v)) out[key] = v.map(function (x) { return (x && typeof x === "object") ? JSON.stringify(x) : x; }).join("; ");
            else out[key] = v;
        });
        return out;
    }
    el("jcBtn").addEventListener("click", function () {
        var err = el("jcErr"); err.classList.add("d-none");
        var parsed;
        try { parsed = JSON.parse(el("jcIn").value); } catch (e) { err.textContent = "Invalid JSON: " + e.message; err.classList.remove("d-none"); return; }
        if (!Array.isArray(parsed)) parsed = [parsed];
        if (!parsed.length || typeof parsed[0] !== "object" || parsed[0] === null) { err.textContent = "The JSON must be an object or an array of objects."; err.classList.remove("d-none"); return; }
        var flat = parsed.map(function (o) { return flatten(o, "", {}); });
        var heads = [];
        flat.forEach(function (o) { Object.keys(o).forEach(function (k) { if (heads.indexOf(k) < 0) heads.push(k); }); });
        var lines = [heads.map(esc).join(",")];
        flat.forEach(function (o) { lines.push(heads.map(function (h) { return esc(o[h]); }).join(",")); });
        el("jcOut").value = lines.join("\n");
        el("jcStats").textContent = "Converted " + flat.length + " record(s) with " + heads.length + " column(s).";
    });
    el("jcDl").addEventListener("click", function () { if (el("jcOut").value) download("data.csv", el("jcOut").value, "text/csv"); });
})();
</script>
@endsection
