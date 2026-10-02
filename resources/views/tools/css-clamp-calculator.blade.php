@extends('layouts.app')

@section('title', 'CSS Clamp Calculator — Free Online Tool')
@section('meta_description', 'Calculate fluid font sizes with CSS clamp for responsive type')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">CSS Clamp Calculator</h1>
            <p class="lead small text-muted">Give a minimum and maximum font size and the viewport range between them — the tool builds the CSS clamp() expression with the correct slope and intercept, and previews it live.</p>
            <div class="row g-3">
                <div class="col-md-3"><label class="form-label" for="clMinFs">Min font size (px)</label><input type="number" class="form-control" id="clMinFs" value="16" step="any"></div>
                <div class="col-md-3"><label class="form-label" for="clMaxFs">Max font size (px)</label><input type="number" class="form-control" id="clMaxFs" value="32" step="any"></div>
                <div class="col-md-3"><label class="form-label" for="clMinVp">Min viewport (px)</label><input type="number" class="form-control" id="clMinVp" value="360" step="any"></div>
                <div class="col-md-3"><label class="form-label" for="clMaxVp">Max viewport (px)</label><input type="number" class="form-control" id="clMaxVp" value="1200" step="any"></div>
            </div>
            <div class="alert alert-warning mt-3 d-none" id="clMsg"></div>
            <div class="border rounded p-3 mt-3 text-center"><div class="text-muted small">Live preview — resize the window to see it scale</div><div id="clPreview" class="fw-bold">Fluid typography preview</div></div>
            <label class="form-label mt-3" for="clOut">CSS code</label>
            <textarea class="form-control font-monospace" id="clOut" rows="2" readonly></textarea>
            <button type="button" class="btn btn-success btn-sm mt-2" id="clCopy">Copy CSS</button>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol><li>Enter the smallest and largest font sizes you want.</li><li>Enter the viewport widths where scaling should start and stop.</li><li>Copy the clamp() expression into your CSS.</li></ol>
            <p class="small text-muted mb-0">Note: The preferred value is a straight line between the two points: slope in vw plus an intercept in rem, assuming a 16px root font size.</p>
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
        var minFs = num("clMinFs"), maxFs = num("clMaxFs"), minVp = num("clMinVp"), maxVp = num("clMaxVp"), msg = el("clMsg");
        if (maxFs <= minFs || maxVp <= minVp || minFs <= 0 || minVp <= 0) { msg.textContent = "Max values must be larger than min values, and all values must be positive."; msg.classList.remove("d-none"); el("clOut").value = ""; return; }
        msg.classList.add("d-none");
        var slope = (maxFs - minFs) / (maxVp - minVp);
        var interceptPx = minFs - slope * minVp;
        var css = "clamp(" + (minFs / 16) + "rem, " + (interceptPx / 16).toFixed(4) + "rem + " + (slope * 100).toFixed(4) + "vw, " + (maxFs / 16) + "rem)";
        el("clOut").value = "font-size: " + css + ";";
        el("clPreview").style.fontSize = css;
    }
    ["clMinFs","clMaxFs","clMinVp","clMaxVp"].forEach(function (id) { el(id).addEventListener("input", calc); });
    el("clCopy").addEventListener("click", function () { copyText("clOut", el("clCopy")); });
    calc();
})();
</script>
@endsection
