@extends('layouts.app')

@section('title', 'Border Radius Generator — Free Online Tool')
@section('meta_description', 'Design fancy border radius shapes and copy the CSS code')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Border Radius Generator</h1>
            <p class="lead small text-muted">Move the eight sliders to shape the horizontal and vertical radii separately — the classic way to make blob shapes and fancy cards — and copy the CSS.</p>
            <div class="border rounded p-3 mb-3 d-flex align-items-center justify-content-center" style="min-height:230px">
                <div id="brPreview" style="width:220px;height:170px;border:3px solid #0d6efd"></div>
            </div>
            <div class="row g-3">
                <div class="col-md-3"><label class="form-label" for="brH1">Top side — left: <span id="brH1Val">30</span>%</label><input type="range" class="form-range br-in" id="brH1" min="0" max="100" value="30"></div>
                <div class="col-md-3"><label class="form-label" for="brH2">Top side — right: <span id="brH2Val">30</span>%</label><input type="range" class="form-range br-in" id="brH2" min="0" max="100" value="30"></div>
                <div class="col-md-3"><label class="form-label" for="brH3">Bottom side — right: <span id="brH3Val">30</span>%</label><input type="range" class="form-range br-in" id="brH3" min="0" max="100" value="30"></div>
                <div class="col-md-3"><label class="form-label" for="brH4">Bottom side — left: <span id="brH4Val">30</span>%</label><input type="range" class="form-range br-in" id="brH4" min="0" max="100" value="30"></div>
                <div class="col-md-3"><label class="form-label" for="brV1">Left side — top: <span id="brV1Val">30</span>%</label><input type="range" class="form-range br-in" id="brV1" min="0" max="100" value="30"></div>
                <div class="col-md-3"><label class="form-label" for="brV2">Right side — top: <span id="brV2Val">30</span>%</label><input type="range" class="form-range br-in" id="brV2" min="0" max="100" value="30"></div>
                <div class="col-md-3"><label class="form-label" for="brV3">Right side — bottom: <span id="brV3Val">30</span>%</label><input type="range" class="form-range br-in" id="brV3" min="0" max="100" value="30"></div>
                <div class="col-md-3"><label class="form-label" for="brV4">Left side — bottom: <span id="brV4Val">30</span>%</label><input type="range" class="form-range br-in" id="brV4" min="0" max="100" value="30"></div>
            </div>
            <label class="form-label mt-3" for="brOut">CSS code</label>
            <textarea class="form-control font-monospace" id="brOut" rows="2" readonly></textarea>
            <button type="button" class="btn btn-success btn-sm mt-2" id="brCopy">Copy CSS</button>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol><li>Adjust the four horizontal and four vertical radius sliders.</li><li>Watch the preview box change shape live.</li><li>Copy the CSS and paste it into your stylesheet.</li></ol>
            <p class="small text-muted mb-0">Note: The output uses the full eight-value border-radius syntax (horizontal values, a slash, then vertical values), which is how fancy blob shapes are built in pure CSS.</p>
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
    var ids = ["brH1","brH2","brH3","brH4","brV1","brV2","brV3","brV4"];
    function calc() {
        var v = ids.map(function (id) { var n = num(id); el(id + "Val").textContent = n; return n + "%"; });
        var css = v.slice(0, 4).join(" ") + " / " + v.slice(4).join(" ");
        el("brPreview").style.borderRadius = css;
        el("brOut").value = "border-radius: " + css + ";";
    }
    ids.forEach(function (id) { el(id).addEventListener("input", calc); });
    el("brCopy").addEventListener("click", function () { copyText("brOut", el("brCopy")); });
    calc();
})();
</script>
@endsection
