@extends('layouts.app')

@section('title', 'CSS Grid Generator — Free Online Tool')
@section('meta_description', 'Create CSS grid layouts with rows columns and gaps visually')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">CSS Grid Generator</h1>
            <p class="lead small text-muted">Set columns, rows and gaps — the preview grid and the grid-template CSS update live.</p>
            <div class="row g-3">
                <div class="col-md-3"><label class="form-label" for="grCols">Columns: <span id="grColsVal">3</span></label><input type="range" class="form-range" id="grCols" min="1" max="8" value="3"></div>
                <div class="col-md-3"><label class="form-label" for="grRows">Rows: <span id="grRowsVal">2</span></label><input type="range" class="form-range" id="grRows" min="1" max="6" value="2"></div>
                <div class="col-md-3"><label class="form-label" for="grGap">Gap: <span id="grGapVal">10</span>px</label><input type="range" class="form-range" id="grGap" min="0" max="40" value="10"></div>
                <div class="col-md-3"><label class="form-label" for="grRowH">Row height: <span id="grRowHVal">70</span>px</label><input type="range" class="form-range" id="grRowH" min="30" max="150" value="70"></div>
            </div>
            <div id="grPreview" class="mt-3"></div>
            <label class="form-label mt-3" for="grOut">CSS code</label>
            <textarea class="form-control font-monospace" id="grOut" rows="6" readonly></textarea>
            <button type="button" class="btn btn-success btn-sm mt-2" id="grCopy">Copy CSS</button>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol><li>Set the number of columns and rows.</li><li>Adjust the gap and row height while watching the preview.</li><li>Copy the CSS for the container and items.</li></ol>
            <p class="small text-muted mb-0">Note: The columns use repeat(n, 1fr) so every column gets an equal share of the available width.</p>
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
    function calc() {
        ["grCols","grRows","grGap","grRowH"].forEach(function (id) { el(id + "Val").textContent = num(id); });
        var cols = num("grCols"), rows = num("grRows"), gap = num("grGap"), rh = num("grRowH");
        var pv = el("grPreview");
        pv.style.display = "grid"; pv.style.gridTemplateColumns = "repeat(" + cols + ", 1fr)"; pv.style.gap = gap + "px";
        pv.innerHTML = "";
        for (var i = 1; i <= cols * rows; i++) { var d = document.createElement("div"); d.className = "border rounded text-center fw-semibold"; d.style.padding = "10px"; d.style.minHeight = rh + "px"; d.textContent = i; pv.appendChild(d); }
        el("grOut").value = ".grid-container {\n  display: grid;\n  grid-template-columns: repeat(" + cols + ", 1fr);\n  grid-template-rows: repeat(" + rows + ", " + rh + "px);\n  gap: " + gap + "px;\n}";
    }
    ["grCols","grRows","grGap","grRowH"].forEach(function (id) { el(id).addEventListener("input", calc); });
    el("grCopy").addEventListener("click", function () { copyText("grOut", el("grCopy")); });
    calc();
})();
</script>
@endsection
