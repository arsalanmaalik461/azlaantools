@extends('layouts.app')

@section('title', 'Fidya Calculator — Free Online Tool')
@section('meta_description', 'Calculate total fidya from the number of missed fasts and the daily food rate.')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Fidya Calculator</h1>
            <p class="lead small text-muted">Fidya for fasts that cannot be kept due to health or chronic illness: food for one poor person for each fast. You enter the daily food rate yourself.</p>
            <div class="row g-3">
                <div class="col-md-4"><label class="form-label" for="fdDays">Missed fasts (days)</label><input type="number" class="form-control fd-in" id="fdDays" value="30" step="any"></div>
                <div class="col-md-4"><label class="form-label" for="fdRate">Food rate per day (Rs) — price of 2 meals for one poor person</label><input type="number" class="form-control fd-in" id="fdRate" value="500" step="any"></div>
                <div class="col-md-4"><label class="form-label" for="fdPersons">Fidya for how many persons?</label><input type="number" class="form-control fd-in" id="fdPersons" value="1" step="any"></div>
            </div>
            <div class="border rounded p-3 mt-3"><div class="row text-center g-2">
                <div class="col-md-4"><div class="text-muted small">Fidya per person</div><div class="fs-4 fw-bold" id="fdPer">—</div></div>
                <div class="col-md-4"><div class="text-muted small">Total fidya</div><div class="fs-4 fw-bold" id="fdTotal">—</div></div>
                <div class="col-md-4"><div class="text-muted small">Total meals (food for the poor)</div><div class="fs-4 fw-bold" id="fdMeals">—</div></div>
            </div></div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol><li>Enter the number of missed fasts.</li><li>Enter the daily food price for one poor person that is fair in your area.</li><li>Enter the number of persons and see the total fidya.</li></ol>
            <p class="small text-muted mb-0">Note: This tool does not claim any fixed official rate — you enter the rate yourself. Some scholars calculate fidya from the amount of wheat (about half a sa); confirm your situation with your mufti. Fasts that can be made up later should first be made up (qaza).</p>
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
    function calc() { var d = num("fdDays"), r = num("fdRate"), p = Math.max(1, num("fdPersons")); el("fdPer").textContent = rs(d * r); el("fdTotal").textContent = rs(d * r * p); el("fdMeals").textContent = fmt(Math.round(d * p)) + " days of food"; }
    document.querySelectorAll(".fd-in").forEach(function (f) { f.addEventListener("input", calc); }); calc();
})();
</script>
@endsection
