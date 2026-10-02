@extends('layouts.app')

@section('title', 'Lean Bulk Surplus Calculator — Free Online Tool')
@section('meta_description', 'Find a small calorie surplus and weekly gain target for lean muscle building')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Lean Bulk Surplus Calculator</h1>
            <p class="lead small text-muted">A lean bulk uses a small surplus — enough to build muscle slowly while limiting fat gain. Enter your maintenance calories to plan it.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3"><label class="form-label" for="maint">Maintenance calories per day (kcal)</label><input type="number" class="form-control" id="maint" value="2600" step="any"></div>                    <div class="mb-3"><label class="form-label" for="weight">Body weight (kg)</label><input type="number" class="form-control" id="weight" value="75" step="any"></div>                    <div class="mb-3"><label class="form-label" for="surplus">Surplus size</label><select class="form-select" id="surplus"><option value="5">Conservative — plus 5%</option><option value="10">Standard lean bulk — plus 10%</option><option value="15">Faster bulk — plus 15%</option></select></div>
                    <div id="result" class="border rounded p-3 mt-3">Enter your details above and the result will appear here.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Enter your maintenance calories (for example from a TDEE calculator).</li><li>Enter your body weight and choose a surplus size.</li><li>Read your daily target, expected weekly gain and protein guide.</li></ol>
                    <h2 class="h5">Note</h2>
                    <p class="small text-muted mb-0">Expected gain uses the approximation that 1 kg of body tissue change corresponds to about 7,700 kcal of surplus; real muscle gain is slower than pure energy math, especially for experienced lifters. Protein guide uses 1.6 to 2.2 g per kg, the widely published muscle-gain range. Estimate only — not medical advice.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    function num(id) { var v = parseFloat(document.getElementById(id).value); return isFinite(v) ? v : NaN; }
    function fmt(n, d) { return Number(n).toLocaleString("en-US", { minimumFractionDigits: d, maximumFractionDigits: d }); }
    function bind(ids, fn) { ids.forEach(function (id) { var el = document.getElementById(id); el.addEventListener("input", fn); el.addEventListener("change", fn); }); }
    function out(html) { document.getElementById("result").innerHTML = html; }
    function calc() {
        var m = num("maint"), w = num("weight"), pct = num("surplus");
        if (isNaN(m) || isNaN(w) || m <= 0 || w <= 0) { out("Please enter valid maintenance calories and weight."); return; }
        var extra = m * pct / 100, target = m + extra;
        var weekly = extra * 7 / 7700;
        out("<strong>Daily target:</strong> about " + fmt(target, 0) + " kcal (surplus of " + fmt(extra, 0) + " kcal)<br><strong>Expected gain:</strong> up to about " + fmt(weekly, 2) + " kg per week by energy math — real muscle gain is usually slower<br><strong>Protein guide:</strong> " + fmt(w * 1.6, 0) + " to " + fmt(w * 2.2, 0) + " g per day");
    }
    bind(["maint", "weight", "surplus"], calc); calc();
})();
</script>
@endsection
