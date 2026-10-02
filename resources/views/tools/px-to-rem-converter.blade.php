@extends('layouts.app')

@section('title', 'PX to REM Converter — Free Online Tool')
@section('meta_description', 'Convert pixels to REM EM and percent with a live size table')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">PX to REM Converter</h1>
            <p class="lead small text-muted">Convert pixels to rem, em and percent against any root font size, with a live reference table of common sizes.</p>
            <div class="row g-3">
                <div class="col-md-4"><label class="form-label" for="prBase">Root font size (px)</label><input type="number" class="form-control" id="prBase" value="16" step="any"></div>
                <div class="col-md-4"><label class="form-label" for="prPx">Pixels (px)</label><input type="number" class="form-control" id="prPx" value="24" step="any"></div>
                <div class="col-md-4"><label class="form-label" for="prRem">REM</label><input type="number" class="form-control" id="prRem" step="any"></div>
            </div>
            <div class="border rounded p-3 mt-3">
                <div class="row text-center g-2">
                    <div class="col-3"><div class="text-muted small">REM</div><div class="fs-5 fw-bold" id="prOutRem">—</div></div>
                    <div class="col-3"><div class="text-muted small">EM</div><div class="fs-5 fw-bold" id="prOutEm">—</div></div>
                    <div class="col-3"><div class="text-muted small">Percent</div><div class="fs-5 fw-bold" id="prOutPct">—</div></div>
                    <div class="col-3"><div class="text-muted small">Points</div><div class="fs-5 fw-bold" id="prOutPt">—</div></div>
                </div>
            </div>
            <div class="table-responsive mt-3"><table class="table table-sm table-striped"><thead><tr><th>px</th><th>rem / em</th><th>percent</th></tr></thead><tbody id="prTable"></tbody></table></div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol><li>Set the root font size — 16px is the browser default.</li><li>Type pixels to see rem, em, percent and points, or type rem to get pixels back.</li><li>Use the reference table for common design sizes.</li></ol>
            <p class="small text-muted mb-0">Note: EM in the results assumes the parent font size equals the root size — in real layouts em compounds with the parent, while rem always uses the root.</p>
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
    var last = "px";
    function trim(n) { return String(Math.round(n * 10000) / 10000); }
    function calc() {
        var base = num("prBase"); if (base <= 0) base = 16;
        var px;
        if (last === "rem") { px = num("prRem") * base; el("prPx").value = trim(px); }
        else { px = num("prPx"); el("prRem").value = trim(px / base); }
        el("prOutRem").textContent = trim(px / base) + "rem";
        el("prOutEm").textContent = trim(px / base) + "em";
        el("prOutPct").textContent = trim(px / base * 100) + "%";
        el("prOutPt").textContent = trim(px * 0.75) + "pt";
        var sizes = [8, 10, 12, 14, 16, 18, 20, 24, 28, 32, 36, 48, 60, 72], html = "";
        sizes.forEach(function (s) { html += "<tr><td>" + s + "px</td><td>" + trim(s / base) + "</td><td>" + trim(s / base * 100) + "%</td></tr>"; });
        el("prTable").innerHTML = html;
    }
    el("prPx").addEventListener("input", function () { last = "px"; calc(); });
    el("prRem").addEventListener("input", function () { last = "rem"; calc(); });
    el("prBase").addEventListener("input", function () { last = "px"; calc(); });
    calc();
})();
</script>
@endsection
