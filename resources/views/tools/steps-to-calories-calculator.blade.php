@extends('layouts.app')

@section('title', 'Steps to Calories Calculator — Free Online Tool')
@section('meta_description', 'Convert your daily step count into calories burned using your weight and walking pace')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Steps to Calories Calculator</h1>
            <p class="lead small text-muted">Step counters count steps, not energy. Add your weight and pace to turn todays steps into an estimated calorie burn.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3"><label class="form-label" for="steps">Steps</label><input type="number" class="form-control" id="steps" value="10000" step="any"></div>                    <div class="mb-3"><label class="form-label" for="weight">Body weight (kg)</label><input type="number" class="form-control" id="weight" value="70" step="any"></div>                    <div class="mb-3"><label class="form-label" for="pace">Walking pace</label><select class="form-select" id="pace"><option value="0.55">Slow stroll — about 0.55 kcal per kg per km</option><option value="0.70">Average walking — about 0.70 kcal per kg per km</option><option value="0.85">Brisk walking — about 0.85 kcal per kg per km</option></select></div>
                    <div id="result" class="border rounded p-3 mt-3">Enter your details above and the result will appear here.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Enter your step count.</li><li>Enter your body weight.</li><li>Choose your usual walking pace and read the estimate.</li></ol>
                    <h2 class="h5">Note</h2>
                    <p class="small text-muted mb-0">Method: distance uses an average adult stride of about 0.762 m per step, then energy uses the per-kilogram-per-kilometre factor for the pace you chose. Wearables use heart rate and motion data and will differ. Estimate only — not medical advice.</p>
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
        var steps = num("steps"), w = num("weight"), f = num("pace");
        if ([steps, w].some(isNaN) || steps <= 0 || w <= 0) { out("Please enter valid steps and weight."); return; }
        var km = steps * 0.762 / 1000;
        var kcal = km * w * f;
        out("<strong>Distance covered:</strong> about " + fmt(km, 2) + " km<br><strong>Estimated calories burned:</strong> about " + fmt(kcal, 0) + " kcal<br>That is roughly " + fmt(kcal / steps * 1000, 1) + " kcal per 1,000 steps at your weight and pace.");
    }
    bind(["steps", "weight", "pace"], calc); calc();
})();
</script>
@endsection
