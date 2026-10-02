@extends('layouts.app')

@section('title', 'Online CSV Viewer and Editor — Free Online Tool')
@section('meta_description', 'Open and edit CSV files in a clean table with search and export')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Online CSV Viewer and Editor</h1>
            <p class="lead small text-muted">Open a CSV file, search the rows, edit cells directly in the table, and export the edited result as a new CSV file.</p>
            <label class="form-label" for="cvFile">Open a CSV file</label>
            <input type="file" class="form-control" id="cvFile" accept=".csv,text/csv,text/plain">
            <label class="form-label mt-3" for="cvIn">Or paste CSV, then click Load</label>
            <textarea class="form-control font-monospace" id="cvIn" rows="4" placeholder="name,city&#10;Ali,Lahore"></textarea>
            <div class="row g-2 mt-2">
                <div class="col-md-4"><button type="button" class="btn btn-primary w-100" id="cvLoad">Load into table</button></div>
                <div class="col-md-4"><input type="text" class="form-control" id="cvSearch" placeholder="Search rows..."></div>
                <div class="col-md-4"><button type="button" class="btn btn-success w-100" id="cvExport">Export CSV</button></div>
            </div>
            <p class="small text-muted mt-2" id="cvStats">No data loaded yet.</p>
            <div class="table-responsive border rounded"><table class="table table-sm table-striped mb-0"><thead id="cvHead"></thead><tbody id="cvBody"></tbody></table></div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol><li>Upload a CSV file or paste CSV text and click Load.</li><li>Use the search box to filter rows.</li><li>Click any cell to edit it, then Export CSV to download the edited file.</li></ol>
            <p class="small text-muted mb-0">Note: Editing happens in your browser only — your original file is never changed or uploaded. Export always writes the full dataset, not just the filtered rows.</p>
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
    var data = [], header = [];
    function render() {
        var q = el("cvSearch").value.toLowerCase();
        var thead = el("cvHead"), tbody = el("cvBody");
        thead.innerHTML = ""; tbody.innerHTML = "";
        if (!header.length) { el("cvStats").textContent = "No data loaded yet."; return; }
        var tr = document.createElement("tr");
        header.forEach(function (h) { var th = document.createElement("th"); th.textContent = h; tr.appendChild(th); });
        thead.appendChild(tr);
        var shown = 0;
        data.forEach(function (row, ri) {
            var joined = row.join(" ").toLowerCase();
            if (q && joined.indexOf(q) < 0) return;
            shown++;
            var tr2 = document.createElement("tr");
            row.forEach(function (cell, ci) { var td = document.createElement("td"); td.textContent = cell; td.contentEditable = "true"; td.setAttribute("data-r", ri); td.setAttribute("data-c", ci); tr2.appendChild(td); });
            tbody.appendChild(tr2);
        });
        el("cvStats").textContent = "Showing " + shown + " of " + data.length + " row(s), " + header.length + " column(s). Cells are editable.";
    }
    function load(text) {
        var rows = parseCsv(text, ",");
        if (!rows.length) { el("cvStats").textContent = "No rows could be parsed."; return; }
        header = rows[0]; data = rows.slice(1);
        render();
    }
    el("cvFile").addEventListener("change", function () { var f = el("cvFile").files[0]; if (!f) return; var r = new FileReader(); r.onload = function () { el("cvIn").value = String(r.result || ""); load(el("cvIn").value); }; r.readAsText(f); });
    el("cvLoad").addEventListener("click", function () { load(el("cvIn").value); });
    el("cvSearch").addEventListener("input", render);
    el("cvBody").addEventListener("focusout", function (e) { var td = e.target; if (td && td.getAttribute("data-r") !== null) { data[parseInt(td.getAttribute("data-r"), 10)][parseInt(td.getAttribute("data-c"), 10)] = td.textContent; } });
    el("cvExport").addEventListener("click", function () { if (!header.length) return; var lines = [header.map(csvEscape).join(",")]; data.forEach(function (r) { lines.push(r.map(csvEscape).join(",")); }); download("edited.csv", lines.join("\n"), "text/csv"); });
})();
</script>
@endsection
