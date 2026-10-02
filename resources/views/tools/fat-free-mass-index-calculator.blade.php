@extends('layouts.app')

@section('title', 'Fat Free Mass Index Calculator — Free Online Tool')
@section('meta_description', 'Calculate your FFMI and normalized FFMI to gauge muscularity for your height')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Fat Free Mass Index Calculator</h1>
            <p class="lead small text-muted">Fat-Free Mass Index adjusts lean mass for height, so muscularity can be compared between people of different heights.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3"><label class="form-label" for="weight">Weight (kg)</label><input type="number" class="form-control" id="weight" value="80" step="any"></div>                    <div class="mb-3"><label class="form-label" for="height">Height (cm)</label><input type="number" class="form-control" id="height" value="175" step="any"></div>                    <div class="mb-3"><label class="form-label" for="bf">Body fat (%)</label><input type="number" class="form-control" id="bf" value="15" step="any"></div>
                    <div id="result" class="border rounded p-3 mt-3">Enter your details above and the result will appear here.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Enter your weight and height.</li><li>Enter your body fat percentage.</li><li>Your lean mass, FFMI and normalized FFMI appear instantly.</li></ol>
                    <h2 class="h5">Note</h2>
                    <p class="small text-muted mb-0">FFMI = lean mass (kg) divided by height (m) squared. Normalized FFMI adds 6.1 x (1.8 - height in m) to adjust to a 1.8 m reference. General bands: below 18 low, 18 to 20 average, 20 to 22 above average, 22 to 25 very muscular, above 25 is rare without assistance — bands are community conventions, not medical categories. Estimate only — not medical advice.</p>
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
        var w = num("weight"), h = num("height"), bf = num("bf");
        if ([w, h, bf].some(isNaN) || w <= 0 || h <= 0 || bf < 0 || bf >= 100) { out("Please enter valid values — body fat must be between 0 and 100."); return; }
        var hm = h / 100, lean = w * (1 - bf / 100);
        var ffmi = lean / (hm * hm);
        var norm = ffmi + 6.1 * (1.8 - hm);
        out("<strong>Lean (fat-free) mass:</strong> " + fmt(lean, 1) + " kg<br><strong>FFMI:</strong> " + fmt(ffmi, 1) + "<br><strong>Normalized FFMI:</strong> " + fmt(norm, 1));
    }
    bind(["weight", "height", "bf"], calc); calc();
})();
</script>
@endsection
