@extends('layouts.app')

@section('title', 'Calorie Zigzag Calculator — Free Online Tool')
@section('meta_description', 'Plan a weekly zigzag calorie schedule with higher and lower days that average to your target')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Calorie Zigzag Calculator</h1>
            <p class="lead small text-muted">Calorie cycling alternates higher and lower days while keeping the same weekly average. Enter your daily target and get a 7-day plan.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3"><label class="form-label" for="target">Average daily calorie target (kcal)</label><input type="number" class="form-control" id="target" value="2000" step="any"></div>
                    <div id="result" class="border rounded p-3 mt-3">Enter your details above and the result will appear here.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Enter the daily average you want to hit across the week.</li><li>The plan sets 2 higher days and 5 lower days that average exactly to your target.</li><li>Follow the day-by-day table.</li></ol>
                    <h2 class="h5">Note</h2>
                    <p class="small text-muted mb-0">High days are set 30 percent above the low days, and the low-day figure is solved so the weekly average matches your target exactly. Total weekly intake is what drives weight change; the zigzag is a scheduling preference. Estimate only — not medical advice.</p>
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
        var t = num("target");
        if (isNaN(t) || t <= 0) { out("Please enter a valid daily calorie target."); return; }
        var low = t * 7 / 7.6, high = low * 1.3;
        var days = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];
        var rows = "";
        for (var i = 0; i < 7; i++) { var isHigh = (i === 2 || i === 5); rows += "<tr><td>" + days[i] + (isHigh ? " (high day)" : "") + "</td><td>" + fmt(isHigh ? high : low, 0) + " kcal</td></tr>"; }
        out("<strong>Weekly total:</strong> " + fmt(t * 7, 0) + " kcal — average " + fmt(t, 0) + " kcal per day" + "<table class=\"table table-sm mt-2\"><thead><tr><th>Day</th><th>Calories</th></tr></thead><tbody>" + rows + "</tbody></table>");
    }
    bind(["target"], calc); calc();
})();
</script>
@endsection
