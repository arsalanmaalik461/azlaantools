@extends('layouts.app')

@section('title', 'Waist to Height Ratio Calculator — Free Online Tool')
@section('meta_description', 'Check your waist to height ratio against the simple keep waist under half height guide')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Waist to Height Ratio Calculator</h1>
            <p class="lead small text-muted">One simple rule: keep your waist below half your height. Enter both measurements to see your ratio and band.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3"><label class="form-label" for="waist">Waist circumference (cm)</label><input type="number" class="form-control" id="waist" value="85" step="any"></div>                    <div class="mb-3"><label class="form-label" for="height">Height (cm)</label><input type="number" class="form-control" id="height" value="170" step="any"></div>
                    <div id="result" class="border rounded p-3 mt-3">Enter your details above and the result will appear here.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Measure your waist at the midpoint between your lowest rib and hip bone, or at the navel.</li><li>Enter waist and height in the same unit.</li><li>Your ratio and band appear instantly.</li></ol>
                    <h2 class="h5">Note</h2>
                    <p class="small text-muted mb-0">Published boundary values: below 0.40 may indicate underweight, 0.40 to 0.49 healthy, 0.50 to 0.59 increased risk, 0.60 and above high risk. The ratio applies broadly across ages, sexes and ethnic groups, which is its main advantage over BMI. Estimate only — not medical advice.</p>
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
        var wa = num("waist"), h = num("height");
        if (isNaN(wa) || isNaN(h) || wa <= 0 || h <= 0) { out("Please enter valid waist and height values."); return; }
        var r = wa / h;
        var band = r < 0.40 ? "Below 0.40 — possibly underweight range" : r < 0.50 ? "Healthy range — waist under half your height" : r < 0.60 ? "Increased risk range — waist over half your height" : "High risk range";
        out("<strong>Waist to height ratio:</strong> " + fmt(r, 3) + "<br><strong>Band:</strong> " + band + "<br><strong>Half your height is:</strong> " + fmt(h / 2, 1) + " cm — your waist is " + fmt(wa, 1) + " cm");
    }
    bind(["waist", "height"], calc); calc();
})();
</script>
@endsection
