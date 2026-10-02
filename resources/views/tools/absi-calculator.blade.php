@extends('layouts.app')

@section('title', 'ABSI Calculator — Free Online Tool')
@section('meta_description', 'Calculate A Body Shape Index from waist size, BMI and height')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">ABSI Calculator</h1>
            <p class="lead small text-muted">A Body Shape Index (ABSI) combines waist circumference, BMI and height into a single body shape metric.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3"><label class="form-label" for="waist">Waist circumference (cm)</label><input type="number" class="form-control" id="waist" value="85" step="any"></div>                    <div class="mb-3"><label class="form-label" for="weight">Weight (kg)</label><input type="number" class="form-control" id="weight" value="70" step="any"></div>                    <div class="mb-3"><label class="form-label" for="height">Height (cm)</label><input type="number" class="form-control" id="height" value="170" step="any"></div>
                    <div id="result" class="border rounded p-3 mt-3">Enter your details above and the result will appear here.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Measure your waist at the navel and enter it in centimetres.</li><li>Enter your weight and height.</li><li>Your BMI and ABSI appear instantly.</li></ol>
                    <h2 class="h5">Note</h2>
                    <p class="small text-muted mb-0">ABSI = waist (m) divided by (BMI to the power 2/3 times height (m) to the power 1/2). It is presented as a shape metric only, not a risk score. Estimate only — not medical advice.</p>
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
        var waist = num("waist"), w = num("weight"), h = num("height");
        if ([waist, w, h].some(isNaN) || waist <= 0 || w <= 0 || h <= 0) { out("Please enter valid positive values for waist, weight and height."); return; }
        var hm = h / 100, bmi = w / (hm * hm);
        var absi = (waist / 100) / (Math.pow(bmi, 2 / 3) * Math.pow(hm, 0.5));
        out("<strong>BMI:</strong> " + fmt(bmi, 1) + "<br><strong>ABSI:</strong> " + fmt(absi, 4) + "<br>Typical adult ABSI values fall roughly between 0.07 and 0.09.");
    }
    bind(["waist", "weight", "height"], calc); calc();
})();
</script>
@endsection
