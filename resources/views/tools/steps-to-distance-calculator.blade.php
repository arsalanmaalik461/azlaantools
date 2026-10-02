@extends('layouts.app')

@section('title', 'Steps to Distance Calculator — Free Online Tool')
@section('meta_description', 'Convert steps into kilometres and miles using your height based stride length')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Steps to Distance Calculator</h1>
            <p class="lead small text-muted">Your stride length comes mostly from your height. Enter both to convert any step count into kilometres and miles.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3"><label class="form-label" for="steps">Steps</label><input type="number" class="form-control" id="steps" value="10000" step="any"></div>                    <div class="mb-3"><label class="form-label" for="height">Your height (cm)</label><input type="number" class="form-control" id="height" value="170" step="any"></div>                    <div class="mb-3"><label class="form-label" for="sex">Sex (stride factor)</label><select class="form-select" id="sex"><option value="0.415">Male — stride about 0.415 x height</option><option value="0.413">Female — stride about 0.413 x height</option></select></div>
                    <div id="result" class="border rounded p-3 mt-3">Enter your details above and the result will appear here.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Enter your step count.</li><li>Enter your height in centimetres.</li><li>Your stride length and the distance in km and miles appear instantly.</li></ol>
                    <h2 class="h5">Note</h2>
                    <p class="small text-muted mb-0">Stride length is estimated as height x 0.415 (men) or x 0.413 (women) — common published approximations. Walking speed, terrain and leg proportions change your real stride; measuring 10 actual steps is more accurate. Estimate only — not medical advice.</p>
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
        var steps = num("steps"), h = num("height"), factor = num("sex");
        if (isNaN(steps) || isNaN(h) || steps <= 0 || h <= 0) { out("Please enter valid steps and height."); return; }
        var strideCm = h * factor;
        var metres = steps * strideCm / 100;
        out("<strong>Estimated stride length:</strong> " + fmt(strideCm, 0) + " cm<br><strong>Distance:</strong> " + fmt(metres / 1000, 2) + " km (" + fmt(metres / 1609.344, 2) + " miles)");
    }
    bind(["steps", "height", "sex"], calc); calc();
})();
</script>
@endsection
