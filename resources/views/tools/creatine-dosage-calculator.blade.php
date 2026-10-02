@extends('layouts.app')

@section('title', 'Creatine Dosage Calculator — Free Online Tool')
@section('meta_description', 'Work out creatine loading and daily maintenance doses from your body weight')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Creatine Dosage Calculator</h1>
            <p class="lead small text-muted">Creatine dosing is usually set by body weight. Enter yours to see the standard loading and maintenance amounts.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3"><label class="form-label" for="weight">Body weight (kg)</label><input type="number" class="form-control" id="weight" value="70" step="any"></div>
                    <div id="result" class="border rounded p-3 mt-3">Enter your details above and the result will appear here.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Enter your body weight in kilograms.</li><li>Read the loading dose (split into 4 servings) and the daily maintenance dose.</li></ol>
                    <h2 class="h5">Note</h2>
                    <p class="small text-muted mb-0">Loading uses 0.3 g per kg per day for 5 to 7 days; maintenance uses about 0.03 g per kg per day, which lands near the common 3 to 5 g dose. Loading is optional — maintenance alone reaches full saturation in about 3 to 4 weeks. This is a supplement estimate only. Estimate only — not medical advice.</p>
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
        if (isNaN(w) || w <= 0) { out("Please enter a valid body weight."); return; }
        var load = w * 0.3, maint = w * 0.03;
        out("<strong>Loading (5 to 7 days):</strong> about " + fmt(load, 1) + " g per day, split into 4 servings of about " + fmt(load / 4, 1) + " g<br><strong>Maintenance:</strong> about " + fmt(maint, 1) + " g per day (commonly rounded to 3 to 5 g)");
    }
    bind(["weight"], calc); calc();
})();
</script>
@endsection
