@extends('layouts.app')

@section('title', 'Body Surface Area Calculator — Free Online Tool')
@section('meta_description', 'Calculate body surface area in square metres with Mosteller and Du Bois formulas')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Body Surface Area Calculator</h1>
            <p class="lead small text-muted">Body surface area (BSA) is used in nutrition and clinical reference work. This tool shows the Mosteller and Du Bois results side by side.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3"><label class="form-label" for="weight">Weight (kg)</label><input type="number" class="form-control" id="weight" value="70" step="any"></div>                    <div class="mb-3"><label class="form-label" for="height">Height (cm)</label><input type="number" class="form-control" id="height" value="170" step="any"></div>
                    <div id="result" class="border rounded p-3 mt-3">Enter your details above and the result will appear here.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Enter your weight in kilograms.</li><li>Enter your height in centimetres.</li><li>Both BSA formulas appear instantly.</li></ol>
                    <h2 class="h5">Note</h2>
                    <p class="small text-muted mb-0">Mosteller: square root of (height x weight / 3600). Du Bois: 0.007184 x height^0.725 x weight^0.425, with height in cm and weight in kg. Estimate only — not medical advice.</p>
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
        var most = Math.sqrt(h * w / 3600);
        var dub = 0.007184 * Math.pow(h, 0.725) * Math.pow(w, 0.425);
        out("<strong>Mosteller:</strong> " + fmt(most, 2) + " m²<br><strong>Du Bois:</strong> " + fmt(dub, 2) + " m²");
    }
    bind(["weight", "height"], calc); calc();
})();
</script>
@endsection
