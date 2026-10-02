@extends('layouts.app')

@section('title', 'Macro Calculator — Free Online Tool')
@section('meta_description', 'Get daily protein, carb and fat grams matched to your calories and your goal')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Macro Calculator</h1>
            <p class="lead small text-muted">Enter your daily calories and pick a split to get protein, carbohydrate and fat in grams.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3"><label class="form-label" for="kcal">Daily calories (kcal)</label><input type="number" class="form-control" id="kcal" value="2200" step="any"></div>                    <div class="mb-3"><label class="form-label" for="split">Goal / split</label><select class="form-select" id="split"><option value="30|40|30">Balanced — 30% protein, 40% carbs, 30% fat</option><option value="40|30|30">High protein — 40% protein, 30% carbs, 30% fat</option><option value="40|20|40">Low carb — 40% protein, 20% carbs, 40% fat</option><option value="20|55|25">Endurance — 20% protein, 55% carbs, 25% fat</option><option value="25|45|30">Moderate — 25% protein, 45% carbs, 30% fat</option></select></div>
                    <div id="result" class="border rounded p-3 mt-3">Enter your details above and the result will appear here.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Enter your daily calorie target.</li><li>Choose the split closest to your goal.</li><li>Your daily grams for each macro appear instantly.</li></ol>
                    <h2 class="h5">Note</h2>
                    <p class="small text-muted mb-0">Grams use 4 kcal per gram for protein and carbohydrate and 9 kcal per gram for fat. Percentage splits are starting points — total calories and protein adequacy matter more than hitting an exact split. Estimate only — not medical advice.</p>
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
        var kcal = num("kcal");
        var parts = document.getElementById("split").value.split("|");
        var p = parseFloat(parts[0]), c = parseFloat(parts[1]), f = parseFloat(parts[2]);
        if (isNaN(kcal) || kcal <= 0) { out("Please enter valid daily calories."); return; }
        out("<strong>Protein:</strong> " + fmt(kcal * p / 100 / 4, 0) + " g<br><strong>Carbohydrates:</strong> " + fmt(kcal * c / 100 / 4, 0) + " g<br><strong>Fat:</strong> " + fmt(kcal * f / 100 / 9, 0) + " g");
    }
    bind(["kcal", "split"], calc); calc();
})();
</script>
@endsection
