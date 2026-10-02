@extends('layouts.app')

@section('title', 'Fitrana Calculator — Free Online Tool')
@section('meta_description', 'Calculate total Fitrana from the number of family members and the per-person rate.')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Fitrana Calculator</h1>
            <p class="lead small text-muted">Calculate Sadqa-e-Fitr: one Fitrana for every person in the household. Enter the per-person rate announced for this year in your area.</p>
            <div class="row g-3">
                <div class="col-md-4"><label class="form-label" for="ftPersons">Family members</label><input type="number" class="form-control ft-in" id="ftPersons" value="5" step="any"></div>
                <div class="col-md-4"><label class="form-label" for="ftRate">Fitrana per person (Rs)</label><input type="number" class="form-control ft-in" id="ftRate" value="500" step="any"></div>
                <div class="col-md-4"><label class="form-label" for="ftExtra">Extra sadqa (optional, Rs)</label><input type="number" class="form-control ft-in" id="ftExtra" value="0" step="any"></div>
            </div>
            <div class="border rounded p-3 mt-3"><div class="row text-center g-2">
                <div class="col-md-6"><div class="text-muted small">Total fitrana</div><div class="fs-4 fw-bold" id="ftTotal">—</div></div>
                <div class="col-md-6"><div class="text-muted small">Per person</div><div class="fs-4 fw-bold" id="ftPer">—</div></div>
            </div></div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol><li>Enter the number of all family members you pay Fitrana for (including children).</li><li>Enter the announced per-person rate for this year — the rate varies by item (wheat, barley, dates, raisins).</li><li>See the total Fitrana and pay it before the Eid prayer.</li></ol>
            <p class="small text-muted mb-0">Note: The per-person rate is not fixed in this tool — the announced rate differs every year and area, so enter your area's rate. Rates change — verify with the official source before relying on this.</p>
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
    function calc() { var p = num("ftPersons"), r = num("ftRate"), e = num("ftExtra"); el("ftTotal").textContent = rs(p * r + e); el("ftPer").textContent = rs(r); }
    document.querySelectorAll(".ft-in").forEach(function (f) { f.addEventListener("input", calc); }); calc();
})();
</script>
@endsection
