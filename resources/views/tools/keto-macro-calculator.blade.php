@extends('layouts.app')

@section('title', 'Keto Macro Calculator — Free Online Tool')
@section('meta_description', 'Set keto macros with low carb, moderate protein and high fat grams for your calories')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Keto Macro Calculator</h1>
            <p class="lead small text-muted">The classic ketogenic split is about 70 percent fat, 25 percent protein and 5 percent carbohydrate. Enter your calories to get the grams.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3"><label class="form-label" for="kcal">Daily calories (kcal)</label><input type="number" class="form-control" id="kcal" value="1800" step="any"></div>                    <div class="mb-3"><label class="form-label" for="fiber">Fiber within those carbs (g) — for net carbs</label><input type="number" class="form-control" id="fiber" value="8" step="any"></div>
                    <div id="result" class="border rounded p-3 mt-3">Enter your details above and the result will appear here.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Enter your daily calorie target.</li><li>Optionally enter your fiber grams to see net carbs.</li><li>Your fat, protein and carb grams appear instantly.</li></ol>
                    <h2 class="h5">Note</h2>
                    <p class="small text-muted mb-0">Grams use 9 kcal per gram of fat and 4 kcal per gram of protein and carbohydrate. Net carbs = total carbs minus fiber. Very low carbohydrate diets are not suitable for everyone and can interact with medicines — medical guidance matters. Estimate only — not medical advice.</p>
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
        var kcal = num("kcal"), fiber = num("fiber");
        if (isNaN(kcal) || kcal <= 0) { out("Please enter valid daily calories."); return; }
        if (isNaN(fiber) || fiber < 0) { fiber = 0; }
        var fat = kcal * 0.70 / 9, pro = kcal * 0.25 / 4, carb = kcal * 0.05 / 4;
        var net = Math.max(carb - fiber, 0);
        out("<strong>Fat:</strong> " + fmt(fat, 0) + " g (70%)<br><strong>Protein:</strong> " + fmt(pro, 0) + " g (25%)<br><strong>Total carbs:</strong> " + fmt(carb, 0) + " g (5%)<br><strong>Net carbs:</strong> about " + fmt(net, 0) + " g after " + fmt(fiber, 0) + " g of fiber");
    }
    bind(["kcal", "fiber"], calc); calc();
})();
</script>
@endsection
