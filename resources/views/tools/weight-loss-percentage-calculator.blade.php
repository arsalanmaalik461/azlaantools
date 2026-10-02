@extends('layouts.app')

@section('title', 'Weight Loss Percentage Calculator — Free Online Tool')
@section('meta_description', 'Calculate the percentage of body weight you have lost between two weigh ins')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Weight Loss Percentage Calculator</h1>
            <p class="lead small text-muted">Total kilograms lost only tell half the story — percentage of starting weight is how challenges and clinics usually track progress.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3"><label class="form-label" for="start">Starting weight (kg)</label><input type="number" class="form-control" id="start" value="90" step="any"></div>                    <div class="mb-3"><label class="form-label" for="now">Current weight (kg)</label><input type="number" class="form-control" id="now" value="82" step="any"></div>
                    <div id="result" class="border rounded p-3 mt-3">Enter your details above and the result will appear here.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Enter your starting weight.</li><li>Enter your current weight.</li><li>Your percentage lost and kilograms lost appear instantly.</li></ol>
                    <h2 class="h5">Note</h2>
                    <p class="small text-muted mb-0">Percentage lost = (start - current) / start x 100. A loss of 5 to 10 percent of starting weight is the range commonly associated with meaningful health improvements in published guidance. Estimate only — not medical advice.</p>
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
        var s = num("start"), n = num("now");
        if (isNaN(s) || isNaN(n) || s <= 0 || n <= 0) { out("Please enter valid starting and current weights."); return; }
        var diff = s - n, pct = diff / s * 100;
        var label = diff > 0 ? "lost" : diff < 0 ? "gained" : "no change";
        out("<strong>Change:</strong> " + fmt(Math.abs(diff), 1) + " kg " + label + "<br><strong>Percentage of starting weight:</strong> " + fmt(Math.abs(pct), 1) + "% " + label + "<br><strong>Current weight as share of start:</strong> " + fmt(n / s * 100, 1) + "%");
    }
    bind(["start", "now"], calc); calc();
})();
</script>
@endsection
