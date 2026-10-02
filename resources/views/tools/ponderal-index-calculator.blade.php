@extends('layouts.app')

@section('title', 'Ponderal Index Calculator — Free Online Tool')
@section('meta_description', 'Calculate ponderal index, a height cubed alternative to BMI for lean and tall builds')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Ponderal Index Calculator</h1>
            <p class="lead small text-muted">The ponderal index (Rohrer index) cubes height instead of squaring it, which suits very tall or lean builds better than BMI.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3"><label class="form-label" for="weight">Weight (kg)</label><input type="number" class="form-control" id="weight" value="70" step="any"></div>                    <div class="mb-3"><label class="form-label" for="height">Height (cm)</label><input type="number" class="form-control" id="height" value="185" step="any"></div>
                    <div id="result" class="border rounded p-3 mt-3">Enter your details above and the result will appear here.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Enter your weight in kilograms.</li><li>Enter your height in centimetres.</li><li>Your ponderal index and BMI for comparison appear instantly.</li></ol>
                    <h2 class="h5">Note</h2>
                    <p class="small text-muted mb-0">Ponderal index = weight (kg) / height (m) cubed. Values around 11 to 14 roughly parallel the normal BMI band, but the index has no universally agreed categories — it is shown as an alternative metric only. Estimate only — not medical advice.</p>
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
        var w = num("weight"), h = num("height");
        if (isNaN(w) || isNaN(h) || w <= 0 || h <= 0) { out("Please enter valid weight and height values."); return; }
        var hm = h / 100;
        var pi = w / Math.pow(hm, 3);
        var bmi = w / (hm * hm);
        out("<strong>Ponderal index:</strong> " + fmt(pi, 2) + "<br><strong>BMI for comparison:</strong> " + fmt(bmi, 1));
    }
    bind(["weight", "height"], calc); calc();
})();
</script>
@endsection
