@extends('layouts.app')

@section('title', 'Markdown Table Generator — Free Online Tool')
@section('meta_description', 'Build markdown tables visually and copy clean table code')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Markdown Table Generator</h1>
            <p class="lead small text-muted">Fill the grid like a spreadsheet, choose the column alignment, and copy clean GitHub-style Markdown table code.</p>
            <div class="row g-3 align-items-end">
                <div class="col-md-3"><label class="form-label" for="mtRows">Body rows: <span id="mtRowsVal">2</span></label><input type="range" class="form-range" id="mtRows" min="1" max="8" value="2"></div>
                <div class="col-md-3"><label class="form-label" for="mtCols">Columns: <span id="mtColsVal">3</span></label><input type="range" class="form-range" id="mtCols" min="1" max="6" value="3"></div>
                <div class="col-md-3"><label class="form-label" for="mtAlign">Alignment</label><select class="form-select" id="mtAlign"><option value="left">Left</option><option value="center">Center</option><option value="right">Right</option></select></div>
            </div>
            <div id="mtGrid" class="mt-3"></div>
            <label class="form-label mt-3" for="mtOut">Markdown code</label>
            <textarea class="form-control font-monospace" id="mtOut" rows="7" readonly></textarea>
            <button type="button" class="btn btn-success btn-sm mt-2" id="mtCopy">Copy markdown</button>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol><li>Set the number of rows and columns.</li><li>Type your headings and cell values into the grid.</li><li>Copy the Markdown and paste it into GitHub, a README or any Markdown editor.</li></ol>
            <p class="small text-muted mb-0">Note: Pipe characters inside cell text are escaped automatically so they do not break the table.</p>
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
    var headerVals = ["Name", "City", "Phone", "", "", ""], bodyVals = {};
    function buildGrid() {
        el("mtRowsVal").textContent = num("mtRows"); el("mtColsVal").textContent = num("mtCols");
        var rows = num("mtRows"), cols = num("mtCols"), grid = el("mtGrid");
        grid.innerHTML = "";
        var table = document.createElement("table"); table.className = "table table-sm";
        var thead = document.createElement("thead"), htr = document.createElement("tr"), c, r;
        for (c = 0; c < cols; c++) { (function (ci) { var th = document.createElement("th"); var inp = document.createElement("input"); inp.type = "text"; inp.className = "form-control form-control-sm mt-head"; inp.value = headerVals[ci] || ("Header " + (ci + 1)); inp.addEventListener("input", function () { headerVals[ci] = inp.value; gen(); }); th.appendChild(inp); htr.appendChild(th); })(c); }
        thead.appendChild(htr); table.appendChild(thead);
        var tbody = document.createElement("tbody");
        for (r = 0; r < rows; r++) { (function (ri) { var tr = document.createElement("tr"); for (var c2 = 0; c2 < cols; c2++) { (function (ci) { var td = document.createElement("td"); var inp = document.createElement("input"); inp.type = "text"; inp.className = "form-control form-control-sm"; inp.value = bodyVals[ri + ":" + ci] || ""; inp.placeholder = "Cell"; inp.addEventListener("input", function () { bodyVals[ri + ":" + ci] = inp.value; gen(); }); td.appendChild(inp); tr.appendChild(td); })(c2); } tbody.appendChild(tr); })(r); }
        table.appendChild(tbody); grid.appendChild(table);
        gen();
    }
    function gen() {
        var rows = num("mtRows"), cols = num("mtCols"), align = el("mtAlign").value, r, c, cells;
        function cell(s) { return String(s || "").replace(/\|/g, "\\|"); }
        var head = []; for (c = 0; c < cols; c++) head.push(cell(headerVals[c] || ("Header " + (c + 1))));
        var sep = head.map(function () { return align === "center" ? ":---:" : (align === "right" ? "---:" : "---"); });
        var lines = ["| " + head.join(" | ") + " |", "| " + sep.join(" | ") + " |"];
        for (r = 0; r < rows; r++) { cells = []; for (c = 0; c < cols; c++) cells.push(cell(bodyVals[r + ":" + c])); lines.push("| " + cells.join(" | ") + " |"); }
        el("mtOut").value = lines.join("\n");
    }
    ["mtRows","mtCols"].forEach(function (id) { el(id).addEventListener("input", buildGrid); });
    el("mtAlign").addEventListener("change", gen);
    el("mtCopy").addEventListener("click", function () { copyText("mtOut", el("mtCopy")); });
    buildGrid();
})();
</script>
@endsection
