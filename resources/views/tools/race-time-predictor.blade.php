@extends('layouts.app')

@section('title', 'Race Time Predictor — Free Online Tool')
@section('meta_description', 'Predict your 5K, 10K, half marathon and marathon times from a recent race result')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Race Time Predictor</h1>
            <p class="lead small text-muted">The Riegel formula predicts finish times at other distances from one recent race, assuming similar training and conditions.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3"><label class="form-label" for="from">Recent race distance</label><select class="form-select" id="from"><option value="5">5 km</option><option value="10">10 km</option><option value="21.0975">Half marathon</option><option value="42.195">Marathon</option></select></div>                    <div class="mb-3"><label class="form-label" for="hours">Your time — hours</label><input type="number" class="form-control" id="hours" value="0" step="any"></div>                    <div class="mb-3"><label class="form-label" for="mins">Your time — minutes</label><input type="number" class="form-control" id="mins" value="25" step="any"></div>                    <div class="mb-3"><label class="form-label" for="secs">Your time — seconds</label><input type="number" class="form-control" id="secs" value="0" step="any"></div>
                    <div id="result" class="border rounded p-3 mt-3">Enter your details above and the result will appear here.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Choose the distance of your recent race.</li><li>Enter your finish time.</li><li>Predicted times for the other standard distances appear instantly.</li></ol>
                    <h2 class="h5">Note</h2>
                    <p class="small text-muted mb-0">Uses the Riegel formula: T2 = T1 x (D2 / D1)^1.06. Predictions assume endurance training matched to the target distance — a 5K time alone will over-predict marathon performance without long-run training. Estimate only — not medical advice.</p>
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
    function fmtClock(sec) { sec = Math.round(sec); var h = Math.floor(sec / 3600), m = Math.floor((sec % 3600) / 60), s = sec % 60; function p(x) { return (x < 10 ? "0" : "") + x; } return (h > 0 ? h + ":" + p(m) : m) + ":" + p(s); }
    function calc() {
        var d1 = num("from"), hh = num("hours"), mm = num("mins"), ss = num("secs");
        if ([d1, hh, mm, ss].some(isNaN) || d1 <= 0) { out("Please enter a valid race and time."); return; }
        var t1 = hh * 3600 + mm * 60 + ss;
        if (t1 <= 0) { out("Please enter a finish time above zero."); return; }
        var targets = [[5, "5 km"], [10, "10 km"], [21.0975, "Half marathon"], [42.195, "Marathon"]];
        var rows = "";
        targets.forEach(function (t) { var pred = t1 * Math.pow(t[0] / d1, 1.06); rows += "<tr><td>" + t[1] + "</td><td>" + fmtClock(pred) + "</td><td>" + fmtClock(pred / t[0]) + " per km</td></tr>"; });
        out("<table class=\"table mt-2\"><thead><tr><th>Distance</th><th>Predicted time</th><th>Pace</th></tr></thead><tbody>" + rows + "</tbody></table>");
    }
    bind(["from", "hours", "mins", "secs"], calc); calc();
})();
</script>
@endsection
