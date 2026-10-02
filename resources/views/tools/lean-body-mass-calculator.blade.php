@extends('layouts.app')

@section('title', 'Lean Body Mass Calculator — Free Online Tool')
@section('meta_description', 'Calculate lean body mass from your weight and body fat percentage, or estimate it with the Boer and James formulas')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Lean Body Mass Calculator</h1>
            <p class="lead small text-muted">See your lean body mass three ways: from your body fat percentage, and from the Boer, James and Hume estimation formulas.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3"><label class="form-label" for="weight">Weight (kg)</label><input type="number" class="form-control" id="weight" value="75" step="any"></div>                    <div class="mb-3"><label class="form-label" for="height">Height (cm)</label><input type="number" class="form-control" id="height" value="175" step="any"></div>                    <div class="mb-3"><label class="form-label" for="sex">Sex</label><select class="form-select" id="sex"><option value="male">Male</option><option value="female">Female</option></select></div>                    <div class="mb-3"><label class="form-label" for="bf">Body fat (%) — optional, leave 0 to skip</label><input type="number" class="form-control" id="bf" value="15" step="any"></div>
                    <div id="result" class="border rounded p-3 mt-3">Enter your details above and the result will appear here.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Enter your weight, height and sex.</li><li>Optionally enter a body fat percentage for the direct figure.</li><li>All lean mass estimates appear instantly.</li></ol>
                    <h2 class="h5">Note</h2>
                    <p class="small text-muted mb-0">Boer, James and Hume are published regression formulas using weight, height and sex. The body-fat method (weight x (1 - body fat)) is only as good as the body fat measurement behind it. Estimate only — not medical advice.</p>
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
        var w = num("weight"), h = num("height"), bf = num("bf"), sex = document.getElementById("sex").value;
        if (isNaN(w) || isNaN(h) || w <= 0 || h <= 0) { out("Please enter valid weight and height values."); return; }
        var boer, james, hume;
        if (sex === "male") { boer = 0.407 * w + 0.267 * h - 19.2; james = 1.1 * w - 128 * Math.pow(w / h, 2); hume = 0.328 * w + 0.339 * h - 29.53; }
        else { boer = 0.252 * w + 0.473 * h - 48.3; james = 1.07 * w - 148 * Math.pow(w / h, 2); hume = 0.296 * w + 0.418 * h - 43.29; }
        var direct = (!isNaN(bf) && bf > 0 && bf < 100) ? "<br><strong>From your body fat:</strong> " + fmt(w * (1 - bf / 100), 1) + " kg" : "";
        out("<strong>Boer formula:</strong> " + fmt(boer, 1) + " kg<br><strong>James formula:</strong> " + fmt(james, 1) + " kg<br><strong>Hume formula:</strong> " + fmt(hume, 1) + " kg" + direct);
    }
    bind(["weight", "height", "sex", "bf"], calc); calc();
})();
</script>
@endsection
