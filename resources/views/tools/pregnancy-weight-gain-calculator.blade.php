@extends('layouts.app')

@section('title', 'Pregnancy Weight Gain Calculator — Free Online Tool')
@section('meta_description', 'See the recommended total and weekly weight gain range for your pre pregnancy BMI and week')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Pregnancy Weight Gain Calculator</h1>
            <p class="lead small text-muted">Uses the published IOM 2009 ranges, which depend on pre-pregnancy BMI, to show the total gain range and the expected range at your current week.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3"><label class="form-label" for="weight">Pre-pregnancy weight (kg)</label><input type="number" class="form-control" id="weight" value="62" step="any"></div>                    <div class="mb-3"><label class="form-label" for="height">Height (cm)</label><input type="number" class="form-control" id="height" value="163" step="any"></div>                    <div class="mb-3"><label class="form-label" for="week">Current week of pregnancy (1 to 40)</label><input type="number" class="form-control" id="week" value="20" step="any"></div>
                    <div id="result" class="border rounded p-3 mt-3">Enter your details above and the result will appear here.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Enter your weight before pregnancy and your height.</li><li>Enter your current week.</li><li>Your BMI category, total range and expected range for this week appear instantly.</li></ol>
                    <h2 class="h5">Note</h2>
                    <p class="small text-muted mb-0">IOM 2009 total gain ranges: underweight 12.5 to 18 kg, normal 11.5 to 16 kg, overweight 7 to 11.5 kg, obese 5 to 9 kg, with first-trimester gain of about 0.5 to 2 kg and weekly ranges thereafter. Twins and other situations have different ranges — your care provider guidance comes first. Estimate only — not medical advice.</p>
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
        var w = num("weight"), h = num("height"), week = num("week");
        if ([w, h, week].some(isNaN) || w <= 0 || h <= 0 || week < 1 || week > 42) { out("Please enter valid values, with week between 1 and 42."); return; }
        var hm = h / 100, bmi = w / (hm * hm);
        var cat, totLow, totHigh, wkLow, wkHigh;
        if (bmi < 18.5) { cat = "Underweight (BMI under 18.5)"; totLow = 12.5; totHigh = 18; wkLow = 0.44; wkHigh = 0.58; }
        else if (bmi < 25) { cat = "Normal weight (BMI 18.5 to 24.9)"; totLow = 11.5; totHigh = 16; wkLow = 0.35; wkHigh = 0.50; }
        else if (bmi < 30) { cat = "Overweight (BMI 25 to 29.9)"; totLow = 7; totHigh = 11.5; wkLow = 0.23; wkHigh = 0.33; }
        else { cat = "Obese (BMI 30 or above)"; totLow = 5; totHigh = 9; wkLow = 0.17; wkHigh = 0.27; }
        var expLow, expHigh;
        if (week <= 13) { expLow = 0.5 * week / 13; expHigh = 2 * week / 13; }
        else { expLow = 0.5 + wkLow * (week - 13); expHigh = 2 + wkHigh * (week - 13); }
        if (expHigh > totHigh) { expHigh = totHigh; }
        out("<strong>Pre-pregnancy BMI:</strong> " + fmt(bmi, 1) + " — " + cat + "<br><strong>Recommended total gain:</strong> " + fmt(totLow, 1) + " to " + fmt(totHigh, 1) + " kg<br><strong>Typical weekly gain (2nd and 3rd trimester):</strong> " + fmt(wkLow, 2) + " to " + fmt(wkHigh, 2) + " kg<br><strong>Expected gain by week " + fmt(week, 0) + ":</strong> about " + fmt(expLow, 1) + " to " + fmt(expHigh, 1) + " kg");
    }
    bind(["weight", "height", "week"], calc); calc();
})();
</script>
@endsection
