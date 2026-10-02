@extends('layouts.app')

@section('title', 'BAC Calculator — Free Online Tool')
@section('meta_description', 'Estimate blood alcohol content from drinks, body weight and time using the Widmark formula')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">BAC Calculator</h1>
            <p class="lead small text-muted">Roughly estimate blood alcohol content with the Widmark formula. This estimate must never be used to decide whether it is safe or legal to drive.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3"><label class="form-label" for="volume">Total drink volume (ml)</label><input type="number" class="form-control" id="volume" value="710" step="any"></div>                    <div class="mb-3"><label class="form-label" for="abv">Average strength ABV (%)</label><input type="number" class="form-control" id="abv" value="5" step="any"></div>                    <div class="mb-3"><label class="form-label" for="weight">Body weight (kg)</label><input type="number" class="form-control" id="weight" value="70" step="any"></div>                    <div class="mb-3"><label class="form-label" for="sex">Sex</label><select class="form-select" id="sex"><option value="0.68">Male (Widmark r = 0.68)</option><option value="0.55">Female (Widmark r = 0.55)</option></select></div>                    <div class="mb-3"><label class="form-label" for="hours">Hours since drinking started</label><input type="number" class="form-control" id="hours" value="1" step="any"></div>
                    <div id="result" class="border rounded p-3 mt-3">Enter your details above and the result will appear here.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Enter the total volume of drinks consumed and their average ABV.</li><li>Enter your body weight and sex.</li><li>Enter the hours since you started drinking.</li></ol>
                    <h2 class="h5">Note</h2>
                    <p class="small text-muted mb-0">Uses the Widmark formula with an average elimination rate of 0.015 percent per hour. Food, medicines, fatigue and individual differences change real BAC a lot. Never drive after drinking — this number cannot tell you whether you are fit or legal to drive. Estimate only — not medical advice.</p>
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
        var vol = num("volume"), abv = num("abv"), w = num("weight"), r = num("sex"), hrs = num("hours");
        if ([vol, abv, w, hrs].some(isNaN) || vol <= 0 || abv <= 0 || w <= 0 || hrs < 0) { out("Please enter valid values."); return; }
        var grams = vol * abv / 100 * 0.789;
        var bac = (grams / (w * 1000 * r)) * 100 - 0.015 * hrs;
        if (bac < 0) { bac = 0; }
        out("<strong>Pure alcohol consumed:</strong> " + fmt(grams, 1) + " g<br><strong>Estimated BAC:</strong> about " + fmt(bac, 3) + "%<br><strong>Warning:</strong> this is a rough estimate only. Never use it to judge fitness to drive — do not drive after drinking.");
    }
    bind(["volume", "abv", "weight", "sex", "hours"], calc); calc();
})();
</script>
@endsection
