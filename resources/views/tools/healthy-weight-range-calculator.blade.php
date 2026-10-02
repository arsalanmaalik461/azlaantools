@extends('layouts.app')

@section('title', 'Healthy Weight Range Calculator — Free Online Tool')
@section('meta_description', 'See the healthy weight range for your height based on the standard BMI range')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Healthy Weight Range Calculator</h1>
            <p class="lead small text-muted">Enter your height to see the weight range that corresponds to the standard healthy BMI band of 18.5 to 24.9.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3"><label class="form-label" for="height">Height (cm)</label><input type="number" class="form-control" id="height" value="170" step="any"></div>
                    <div id="result" class="border rounded p-3 mt-3">Enter your details above and the result will appear here.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Enter your height in centimetres.</li><li>The healthy weight range in kilograms and pounds appears instantly.</li></ol>
                    <h2 class="h5">Note</h2>
                    <p class="small text-muted mb-0">The range inverts the standard BMI band: weight = BMI x height (m) squared, at BMI 18.5 and 24.9. BMI bands do not account for muscle mass, frame or fat distribution. Estimate only — not medical advice.</p>
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
        var h = num("height");
        if (isNaN(h) || h <= 0) { out("Please enter a valid height."); return; }
        var hm = h / 100, low = 18.5 * hm * hm, high = 24.9 * hm * hm;
        out("<strong>Healthy weight range:</strong> " + fmt(low, 1) + " kg to " + fmt(high, 1) + " kg<br>That is " + fmt(low * 2.20462, 0) + " lb to " + fmt(high * 2.20462, 0) + " lb");
    }
    bind(["height"], calc); calc();
})();
</script>
@endsection
