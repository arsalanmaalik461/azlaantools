@extends('layouts.app')

@section('title', 'Weight Loss Goal Date Calculator — Free Online Tool')
@section('meta_description', 'See the date you could reach your target weight at a safe weekly loss rate')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Weight Loss Goal Date Calculator</h1>
            <p class="lead small text-muted">Enter your current and target weights and a realistic weekly loss rate to see the date you could get there, and the daily deficit it implies.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3"><label class="form-label" for="current">Current weight (kg)</label><input type="number" class="form-control" id="current" value="85" step="any"></div>                    <div class="mb-3"><label class="form-label" for="target">Target weight (kg)</label><input type="number" class="form-control" id="target" value="75" step="any"></div>                    <div class="mb-3"><label class="form-label" for="rate">Weekly loss rate (kg per week)</label><input type="number" class="form-control" id="rate" value="0.5" step="any"></div>                    <div class="mb-3"><label class="form-label" for="start">Start date</label><input type="date" class="form-control" id="start" value="2026-10-05"></div>
                    <div id="result" class="border rounded p-3 mt-3">Enter your details above and the result will appear here.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Enter your current and target weights.</li><li>Enter a weekly loss rate — 0.25 to 1 kg per week is the commonly cited steady range.</li><li>Choose a start date and read your goal date.</li></ol>
                    <h2 class="h5">Note</h2>
                    <p class="small text-muted mb-0">Uses the approximation that losing 1 kg of body fat corresponds to a cumulative deficit of about 7,700 kcal. Real progress is not linear — early weeks include water changes and loss usually slows over time, so treat the date as a planning guide. Estimate only — not medical advice.</p>
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
        var cur = num("current"), tgt = num("target"), rate = num("rate");
        var v = document.getElementById("start").value;
        if ([cur, tgt, rate].some(isNaN) || cur <= 0 || tgt <= 0 || rate <= 0) { out("Please enter valid weights and a weekly rate above zero."); return; }
        if (tgt >= cur) { out("Target weight must be below current weight for a weight loss plan."); return; }
        if (!v) { out("Please choose a start date."); return; }
        var d = new Date(v + "T00:00:00");
        if (isNaN(d.getTime())) { out("Please choose a valid start date."); return; }
        var weeks = (cur - tgt) / rate;
        d.setDate(d.getDate() + Math.ceil(weeks * 7));
        var months = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];
        out("<strong>Total to lose:</strong> " + fmt(cur - tgt, 1) + " kg over about " + fmt(weeks, 1) + " weeks<br><strong>Daily deficit implied:</strong> about " + fmt(rate * 7700 / 7, 0) + " kcal per day<br><strong>Estimated goal date:</strong> " + d.getDate() + " " + months[d.getMonth()] + " " + d.getFullYear());
    }
    bind(["current", "target", "rate", "start"], calc); calc();
})();
</script>
@endsection
