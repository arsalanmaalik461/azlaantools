@extends('layouts.app')

@section('title', 'Breastfeeding Calorie Calculator — Free Online Tool')
@section('meta_description', 'Estimate extra daily calories needed while breastfeeding for the first and second six months')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Breastfeeding Calorie Calculator</h1>
            <p class="lead small text-muted">Producing milk burns extra energy. Enter your maintenance calories and feeding stage to estimate your daily target while nursing.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3"><label class="form-label" for="maint">Your maintenance calories per day (kcal)</label><input type="number" class="form-control" id="maint" value="2000" step="any"></div>                    <div class="mb-3"><label class="form-label" for="stage">Feeding stage</label><select class="form-select" id="stage"><option value="330">First 6 months, mostly or fully breastfeeding — plus 330 kcal</option><option value="400">Second 6 months, still breastfeeding with solids started — plus 400 kcal</option><option value="170">Partial breastfeeding — plus 170 kcal</option></select></div>
                    <div id="result" class="border rounded p-3 mt-3">Enter your details above and the result will appear here.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Enter your normal maintenance calories (for example from a TDEE calculator).</li><li>Choose your feeding stage.</li><li>Your estimated daily target appears instantly.</li></ol>
                    <h2 class="h5">Note</h2>
                    <p class="small text-muted mb-0">Increments follow published dietary reference values for lactation energy needs. Appetite, milk volume and weight change are better day-to-day guides than any single number. Estimate only — not medical advice.</p>
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
        var m = num("maint"), add = num("stage");
        if (isNaN(m) || m <= 0) { out("Please enter valid maintenance calories."); return; }
        out("<strong>Estimated daily target:</strong> about " + fmt(m + add, 0) + " kcal per day<br>That is your maintenance of " + fmt(m, 0) + " kcal plus " + fmt(add, 0) + " kcal for milk production.");
    }
    bind(["maint", "stage"], calc); calc();
})();
</script>
@endsection
