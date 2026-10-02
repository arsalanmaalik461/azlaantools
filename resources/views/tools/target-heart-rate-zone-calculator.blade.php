@extends('layouts.app')

@section('title', 'Target Heart Rate Zone Calculator — Free Online Tool')
@section('meta_description', 'Get your five training heart rate zones using the Karvonen heart rate reserve method')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Target Heart Rate Zone Calculator</h1>
            <p class="lead small text-muted">The Karvonen method personalises training zones using your resting heart rate, not just your age. Enter both to get your five zones.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3"><label class="form-label" for="age">Age (years)</label><input type="number" class="form-control" id="age" value="30" step="any"></div>                    <div class="mb-3"><label class="form-label" for="rest">Resting heart rate (bpm)</label><input type="number" class="form-control" id="rest" value="60" step="any"></div>
                    <div id="result" class="border rounded p-3 mt-3">Enter your details above and the result will appear here.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Enter your age.</li><li>Measure your resting heart rate first thing in the morning and enter it.</li><li>Your five Karvonen zones appear instantly.</li></ol>
                    <h2 class="h5">Note</h2>
                    <p class="small text-muted mb-0">Max heart rate uses 220 - age (the Tanaka estimate, 208 - 0.7 x age, is also shown for comparison). Karvonen target = resting HR + intensity x (max HR - resting HR). Beta blockers and some other medicines lower heart rate response — perceived effort matters more in that case. Estimate only — not medical advice.</p>
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
        var age = num("age"), rest = num("rest");
        if (isNaN(age) || isNaN(rest) || age <= 0 || rest <= 0) { out("Please enter valid age and resting heart rate values."); return; }
        var max = 220 - age, tanaka = 208 - 0.7 * age, hrr = max - rest;
        if (hrr <= 0) { out("Resting heart rate must be below the estimated maximum — please check the values."); return; }
        var zones = [[50, 60, "Zone 1 — recovery / very light"], [60, 70, "Zone 2 — fat burning / endurance base"], [70, 80, "Zone 3 — aerobic / tempo"], [80, 90, "Zone 4 — threshold / hard"], [90, 100, "Zone 5 — maximum / VO2 max"]];
        var rows = "";
        zones.forEach(function (z) { var lo = rest + hrr * z[0] / 100, hi = rest + hrr * z[1] / 100; rows += "<tr><td>" + z[2] + "</td><td>" + fmt(lo, 0) + " to " + fmt(hi, 0) + " bpm</td></tr>"; });
        out("<strong>Estimated max heart rate:</strong> " + fmt(max, 0) + " bpm (220 - age); Tanaka estimate " + fmt(tanaka, 0) + " bpm<br><strong>Heart rate reserve:</strong> " + fmt(hrr, 0) + " bpm" + "<table class=\"table table-sm mt-2\"><thead><tr><th>Zone</th><th>Heart rate</th></tr></thead><tbody>" + rows + "</tbody></table>");
    }
    bind(["age", "rest"], calc); calc();
})();
</script>
@endsection
