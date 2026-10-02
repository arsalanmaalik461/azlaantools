@extends('layouts.app')

@section('title', 'Swimming Pace Calculator — Free Online Tool')
@section('meta_description', 'Convert swim times into pace per 100 metres or yards and predict other distances')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Swimming Pace Calculator</h1>
            <p class="lead small text-muted">Enter a swim distance and your time to get your pace per 100, plus predicted times for other standard distances at the same pace.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3"><label class="form-label" for="dist">Distance swum</label><input type="number" class="form-control" id="dist" value="1000" step="any"></div>                    <div class="mb-3"><label class="form-label" for="unit">Distance unit</label><select class="form-select" id="unit"><option value="m">Metres</option><option value="yd">Yards</option></select></div>                    <div class="mb-3"><label class="form-label" for="mins">Time — minutes</label><input type="number" class="form-control" id="mins" value="20" step="any"></div>                    <div class="mb-3"><label class="form-label" for="secs">Time — seconds</label><input type="number" class="form-control" id="secs" value="0" step="any"></div>
                    <div id="result" class="border rounded p-3 mt-3">Enter your details above and the result will appear here.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Enter the distance you swam and its unit.</li><li>Enter your time.</li><li>Your pace per 100 and predicted times appear instantly.</li></ol>
                    <h2 class="h5">Note</h2>
                    <p class="small text-muted mb-0">Predictions hold your pace constant across distances — in practice pace slows as distance grows, so longer predictions are optimistic. Pool length, turns and stroke all affect pace. Estimate only — not medical advice.</p>
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
        var d = num("dist"), unit = document.getElementById("unit").value, mm = num("mins"), ss = num("secs");
        if ([d, mm, ss].some(isNaN) || d <= 0 || mm < 0 || ss < 0) { out("Please enter a valid distance and time."); return; }
        var total = mm * 60 + ss;
        if (total <= 0) { out("Please enter a time above zero."); return; }
        var pace100 = total / d * 100;
        var targets = unit === "m" ? [200, 400, 800, 1500] : [200, 400, 800, 1650];
        var rows = "";
        targets.forEach(function (t) { rows += "<tr><td>" + fmt(t, 0) + " " + unit + "</td><td>" + fmtClock(pace100 * t / 100) + "</td></tr>"; });
        out("<strong>Pace per 100 " + unit + ":</strong> " + fmtClock(pace100) + "<table class=\"table table-sm mt-2\"><thead><tr><th>Distance at this pace</th><th>Time</th></tr></thead><tbody>" + rows + "</tbody></table>");
    }
    bind(["dist", "unit", "mins", "secs"], calc); calc();
})();
</script>
@endsection
