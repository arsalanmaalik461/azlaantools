@extends('layouts.app')

@section('title', 'CSV to JSON Converter — Free Online Tool')
@section('meta_description', 'Convert CSV data and files into clean JSON arrays instantly')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">CSV to JSON Converter</h1>
            <p class="lead small text-muted">Paste CSV or upload a file — the first row becomes the keys and every other row becomes a JSON object. Quoted fields, commas inside quotes and escaped quotes are handled properly.</p>
            <label class="form-label" for="cjFile">Upload a CSV file</label>
            <input type="file" class="form-control" id="cjFile" accept=".csv,text/csv,text/plain">
            <label class="form-label mt-3" for="cjIn">Or paste CSV</label>
            <textarea class="form-control font-monospace" id="cjIn" rows="7" placeholder="name,city,phone&#10;Ali,Lahore,0300-0000000"></textarea>
            <div class="row g-2 mt-2 align-items-end">
                <div class="col-md-3"><label class="form-label" for="cjDelim">Delimiter</label><select class="form-select" id="cjDelim"><option value=",">Comma</option><option value=";">Semicolon</option><option value="tab">Tab</option></select></div>
                <div class="col-md-3"><div class="form-check"><input class="form-check-input" type="checkbox" id="cjHeader" checked><label class="form-check-label" for="cjHeader">First row is header</label></div></div>
                <div class="col-md-3"><button type="button" class="btn btn-primary w-100" id="cjBtn">Convert to JSON</button></div>
                <div class="col-md-3"><button type="button" class="btn btn-success w-100" id="cjDl">Download JSON</button></div>
            </div>
            <div class="alert alert-danger mt-3 d-none" id="cjErr"></div>
            <label class="form-label mt-3" for="cjOut">JSON output</label>
            <textarea class="form-control font-monospace" id="cjOut" rows="9" readonly></textarea>
            <p class="small text-muted mt-2 mb-0" id="cjStats"></p>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol><li>Upload a CSV file or paste CSV text.</li><li>Pick the delimiter and whether the first row is a header.</li><li>Convert, then copy or download the JSON.</li></ol>
            <p class="small text-muted mb-0">Note: All values stay as strings, exactly as they appear in the CSV — convert numbers in your own code if you need typed values. Files are read locally and never uploaded.</p>
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
    function parseCsv(text, delim) {
        var rows = [], row = [], field = "", inQ = false, i, c;
        for (i = 0; i < text.length; i++) {
            c = text.charAt(i);
            if (inQ) {
                if (c === "\"") { if (text.charAt(i + 1) === "\"") { field += "\""; i++; } else { inQ = false; } }
                else { field += c; }
            } else if (c === "\"") { inQ = true; }
            else if (c === delim) { row.push(field); field = ""; }
            else if (c === "\n") { row.push(field); field = ""; rows.push(row); row = []; }
            else if (c === "\r") { }
            else { field += c; }
        }
        if (field !== "" || row.length) { row.push(field); rows.push(row); }
        return rows.filter(function (r) { return r.length > 1 || (r.length === 1 && r[0] !== ""); });
    }
    function csvEscape(v) { var s = String(v === null || v === undefined ? "" : v); if (s.indexOf(",") >= 0 || s.indexOf("\"") >= 0 || s.indexOf("\n") >= 0) { return "\"" + s.replace(/\"/g, "\"\"") + "\""; } return s; }
    el("cjFile").addEventListener("change", function () { var f = el("cjFile").files[0]; if (!f) return; var r = new FileReader(); r.onload = function () { el("cjIn").value = String(r.result || ""); convert(); }; r.readAsText(f); });
    function convert() {
        var err = el("cjErr"); err.classList.add("d-none");
        var text = el("cjIn").value;
        if (!text.trim()) { err.textContent = "Paste CSV or upload a file first."; err.classList.remove("d-none"); return; }
        var delim = el("cjDelim").value === "tab" ? "\t" : el("cjDelim").value;
        var rows = parseCsv(text, delim);
        if (!rows.length) { err.textContent = "No rows could be parsed."; err.classList.remove("d-none"); return; }
        var out;
        if (el("cjHeader").checked) {
            var head = rows[0]; out = [];
            for (var i = 1; i < rows.length; i++) { var o = {}; head.forEach(function (h, j) { o[h] = rows[i][j] === undefined ? "" : rows[i][j]; }); out.push(o); }
        } else { out = rows; }
        el("cjOut").value = JSON.stringify(out, null, 2);
        el("cjStats").textContent = "Converted " + out.length + " record(s) from " + rows.length + " row(s).";
    }
    el("cjBtn").addEventListener("click", convert);
    el("cjDl").addEventListener("click", function () { if (el("cjOut").value) download("data.json", el("cjOut").value, "application/json"); });
})();
</script>
@endsection
