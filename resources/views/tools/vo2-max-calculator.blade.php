@extends('layouts.app')

@section('title', 'VO2 Max Calculator — Free Online Tool')
@section('meta_description', 'Estimate VO2 max from a Cooper test, Rockport walk test or resting and max heart rate')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">VO2 Max Calculator</h1>
            <p class="lead small text-muted">Three validated field methods in one tool: the Cooper 12-minute run, the Rockport 1-mile walk, and the heart-rate ratio method.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3"><label class="form-label" for="mode">Test method</label><select class="form-select" id="mode"><option value="cooper">Cooper test — distance run in 12 minutes</option><option value="rockport">Rockport walk test — 1 mile walk</option><option value="hr">Heart rate ratio — max and resting HR</option></select></div>                    <div class="mb-3"><label class="form-label" for="distance">Cooper: distance in 12 minutes (metres)</label><input type="number" class="form-control" id="distance" value="2400" step="any"></div>                    <div class="mb-3"><label class="form-label" for="rw">Rockport: your weight (kg)</label><input type="number" class="form-control" id="rw" value="70" step="any"></div>                    <div class="mb-3"><label class="form-label" for="age">Rockport and general: age (years)</label><input type="number" class="form-control" id="age" value="30" step="any"></div>                    <div class="mb-3"><label class="form-label" for="sex">Rockport: sex</label><select class="form-select" id="sex"><option value="1">Male</option><option value="0">Female</option></select></div>                    <div class="mb-3"><label class="form-label" for="rtime">Rockport: 1 mile walk time (minutes, e.g. 14.5)</label><input type="number" class="form-control" id="rtime" value="14.5" step="any"></div>                    <div class="mb-3"><label class="form-label" for="rhr">Rockport: heart rate at end of walk (bpm)</label><input type="number" class="form-control" id="rhr" value="130" step="any"></div>                    <div class="mb-3"><label class="form-label" for="maxhr">HR method: maximum heart rate (bpm)</label><input type="number" class="form-control" id="maxhr" value="190" step="any"></div>                    <div class="mb-3"><label class="form-label" for="resthr">HR method: resting heart rate (bpm)</label><input type="number" class="form-control" id="resthr" value="60" step="any"></div>
                    <div id="result" class="border rounded p-3 mt-3">Enter your details above and the result will appear here.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Choose your test method.</li><li>Fill in the fields for that method (the others are ignored).</li><li>Your estimated VO2 max and a general fitness guide appear instantly.</li></ol>
                    <h2 class="h5">Note</h2>
                    <p class="small text-muted mb-0">Cooper: VO2 max = (distance in metres - 504.9) / 44.73. Rockport uses the published Kline equation (weight converted to pounds). HR ratio: VO2 max = 15.3 x (max HR / resting HR), from the Uth study. Broad adult bands: below 30 low, 30 to 40 fair, 40 to 50 good, above 50 excellent — exact band tables vary by age and sex. Field tests estimate; laboratory testing measures. Estimate only — not medical advice.</p>
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
        var mode = document.getElementById("mode").value;
        var vo2;
        if (mode === "cooper") { var d = num("distance"); if (isNaN(d) || d <= 504) { out("Please enter a Cooper distance above 505 metres."); return; } vo2 = (d - 504.9) / 44.73; }
        else if (mode === "rockport") {
            var lb = num("rw") * 2.20462, age = num("age"), sex = num("sex"), t = num("rtime"), hr = num("rhr");
            if ([lb, age, t, hr].some(isNaN) || lb <= 0 || t <= 0 || hr <= 0) { out("Please complete the Rockport fields with valid values."); return; }
            vo2 = 132.853 - 0.0769 * lb - 0.3877 * age + 6.315 * sex - 3.2649 * t - 0.1565 * hr;
        } else { var mx = num("maxhr"), rs = num("resthr"); if (isNaN(mx) || isNaN(rs) || mx <= 0 || rs <= 0) { out("Please enter valid maximum and resting heart rates."); return; } vo2 = 15.3 * (mx / rs); }
        if (vo2 <= 0) { out("These inputs produce an invalid result — please check them."); return; }
        var band = vo2 < 30 ? "Low (general adult guide)" : vo2 < 40 ? "Fair (general adult guide)" : vo2 < 50 ? "Good (general adult guide)" : "Excellent (general adult guide)";
        out("<strong>Estimated VO2 max:</strong> " + fmt(vo2, 1) + " ml/kg/min<br><strong>General band:</strong> " + band);
    }
    bind(["mode", "distance", "rw", "age", "sex", "rtime", "rhr", "maxhr", "resthr"], calc); calc();
})();
</script>
@endsection
