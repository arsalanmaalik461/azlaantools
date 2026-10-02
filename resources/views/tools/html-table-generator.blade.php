@extends('layouts.app')

@section('title', 'HTML Table Generator — Free Online Tool')
@section('meta_description', 'Create HTML tables visually and copy clean table code instantly')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">HTML Table Generator</h1>
            <p class="lead small text-muted">Choose rows, columns and Bootstrap table styles — the live preview and clean table markup update together.</p>
            <div class="row g-3">
                <div class="col-md-3"><label class="form-label" for="htRows">Rows: <span id="htRowsVal">3</span></label><input type="range" class="form-range ht-in" id="htRows" min="1" max="10" value="3"></div>
                <div class="col-md-3"><label class="form-label" for="htCols">Columns: <span id="htColsVal">3</span></label><input type="range" class="form-range ht-in" id="htCols" min="1" max="8" value="3"></div>
                <div class="col-md-3"><div class="form-check mt-4"><input class="form-check-input ht-in" type="checkbox" id="htHeader" checked><label class="form-check-label" for="htHeader">Header row</label></div></div>
                <div class="col-md-3"><div class="form-check mt-4"><input class="form-check-input ht-in" type="checkbox" id="htStriped" checked><label class="form-check-label" for="htStriped">Striped (Bootstrap)</label></div></div>
            </div>
            <div class="table-responsive border rounded mt-3" id="htPreview"></div>
            <label class="form-label mt-3" for="htOut">HTML code</label>
            <textarea class="form-control font-monospace" id="htOut" rows="9" readonly></textarea>
            <button type="button" class="btn btn-success btn-sm mt-2" id="htCopy">Copy HTML</button>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol><li>Set the number of rows and columns.</li><li>Toggle the header row and striped style.</li><li>Copy the generated table markup into your page.</li></ol>
            <p class="small text-muted mb-0">Note: The striped option adds Bootstrap table classes; without Bootstrap the same markup renders as a plain accessible table with a real header section.</p>
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
    function build() {
        el("htRowsVal").textContent = num("htRows"); el("htColsVal").textContent = num("htCols");
        var rows = num("htRows"), cols = num("htCols"), hasHead = el("htHeader").checked, striped = el("htStriped").checked;
        var cls = striped ? "table table-striped table-bordered" : "table";
        var html = "<table class=\"" + cls + "\">\n", r, c;
        if (hasHead) { html += "  <thead>\n    <tr>"; for (c = 1; c <= cols; c++) html += "<th>Header " + c + "</th>"; html += "</tr>\n  </thead>\n"; }
        html += "  <tbody>\n";
        for (r = 1; r <= rows; r++) { html += "    <tr>"; for (c = 1; c <= cols; c++) html += "<td>Row " + r + " Col " + c + "</td>"; html += "</tr>\n"; }
        html += "  </tbody>\n</table>";
        el("htPreview").innerHTML = html;
        el("htOut").value = html;
    }
    document.querySelectorAll(".ht-in").forEach(function (f) { f.addEventListener("input", build); f.addEventListener("change", build); });
    el("htCopy").addEventListener("click", function () { copyText("htOut", el("htCopy")); });
    build();
})();
</script>
@endsection
