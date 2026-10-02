@extends('layouts.app')

@section('title', 'Kaffara Calculator — Free Online Tool')
@section('meta_description', 'Calculate the cost of feeding 10 poor people as kaffara for an oath or a missed fast.')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Kaffara Calculator</h1>
            <p class="lead small text-muted">Choose the kaffara type and enter the cost of one day of food for one poor person — the total cost will be calculated.</p>
            <div class="row g-3">
                <div class="col-md-4"><label class="form-label" for="kfType">Kaffara type</label><select class="form-select kf-in" id="kfType"><option value="10">Oath (qasam) — 10 poor people, 1 day</option><option value="60">Fast (roza) / zihar kaffara (feeding) — 60 poor people</option><option value="custom">Custom — enter meals yourself</option></select></div>
                <div class="col-md-4"><label class="form-label" for="kfMeals">Total meals (number of people fed)</label><input type="number" class="form-control kf-in" id="kfMeals" value="10" step="any"></div>
                <div class="col-md-4"><label class="form-label" for="kfRate">Food cost per meal day (Rs)</label><input type="number" class="form-control kf-in" id="kfRate" value="500" step="any"></div>
            </div>
            <div class="border rounded p-3 mt-3"><div class="row text-center g-2">
                <div class="col-md-6"><div class="text-muted small">Total meals</div><div class="fs-4 fw-bold" id="kfTotalMeals">—</div></div>
                <div class="col-md-6"><div class="text-muted small">Total kaffara cost</div><div class="fs-4 fw-bold" id="kfTotal">—</div></div>
            </div></div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol><li>Choose the kaffara type — the meal count is set automatically (for custom, enter it yourself).</li><li>Enter the cost of one day of food (2 meals) for one poor person.</li><li>See the total cost.</li></ol>
            <p class="small text-muted mb-0">Note: This is only a worksheet calculation for the feeding part of kaffara. The real order of kaffara (freeing, fasting, then feeding) and its conditions differ for each type — be sure to confirm with your mufti before acting. You enter the rate yourself; no official rate is claimed.</p>
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
    function calc() { var m = num("kfMeals"), r = num("kfRate"); el("kfTotalMeals").textContent = fmt(Math.round(m)); el("kfTotal").textContent = rs(m * r); }
    el("kfType").addEventListener("change", function () { var v = el("kfType").value; if (v !== "custom") el("kfMeals").value = v; calc(); });
    document.querySelectorAll(".kf-in").forEach(function (f) { f.addEventListener("input", calc); f.addEventListener("change", calc); });
    calc();
})();
</script>
@endsection
