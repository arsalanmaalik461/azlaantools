@extends('layouts.app')

@section('title', 'Protein Intake Calculator — Free Online Tool')
@section('meta_description', 'Calculate daily protein grams from your body weight, activity level and goal')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Protein Intake Calculator</h1>
            <p class="lead small text-muted">Protein needs scale with body weight and goal. Enter yours to get a daily gram target and a suggested split across meals.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3"><label class="form-label" for="weight">Body weight (kg)</label><input type="number" class="form-control" id="weight" value="70" step="any"></div>                    <div class="mb-3"><label class="form-label" for="goal">Activity level and goal</label><select class="form-select" id="goal"><option value="0.8|0.8">Sedentary adult — 0.8 g per kg (minimum RDA)</option><option value="1.2|1.2">Lightly active — 1.2 g per kg</option><option value="1.4|1.6">Endurance training — 1.4 to 1.6 g per kg</option><option value="1.6|2.2">Muscle gain — 1.6 to 2.2 g per kg</option><option value="1.8|2.2">Fat loss while training — 1.8 to 2.2 g per kg</option></select></div>
                    <div id="result" class="border rounded p-3 mt-3">Enter your details above and the result will appear here.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Enter your body weight in kilograms.</li><li>Choose the activity level and goal closest to yours.</li><li>Your daily protein range and per-meal guide appear instantly.</li></ol>
                    <h2 class="h5">Note</h2>
                    <p class="small text-muted mb-0">Ranges are the widely published grams-per-kilogram figures used in sports nutrition and dietary reference guidance. Higher intakes are commonly used in energy deficits to protect lean mass. Kidney conditions and other medical issues change protein advice — clinical guidance comes first. Estimate only — not medical advice.</p>
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
        var w = num("weight");
        var parts = document.getElementById("goal").value.split("|");
        var lo = parseFloat(parts[0]), hi = parseFloat(parts[1]);
        if (isNaN(w) || w <= 0) { out("Please enter a valid body weight."); return; }
        var glo = w * lo, ghi = w * hi;
        var range = lo === hi ? fmt(glo, 0) + " g per day" : fmt(glo, 0) + " to " + fmt(ghi, 0) + " g per day";
        out("<strong>Daily protein:</strong> about " + range + "<br><strong>Per meal (3 meals):</strong> about " + fmt(glo / 3, 0) + (lo === hi ? "" : " to " + fmt(ghi / 3, 0)) + " g each");
    }
    bind(["weight", "goal"], calc); calc();
})();
</script>
@endsection
