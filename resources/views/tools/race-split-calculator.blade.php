@extends('layouts.app')

@section('title', 'Race Split Calculator — Free Online Tool')
@section('meta_description', 'Build even, negative or positive split tables for your marathon or half marathon goal time')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Race Split Calculator</h1>
            <p class="lead small text-muted">Enter your race distance and goal time, then choose a pacing strategy to get a kilometre-by-kilometre split table you can use on race day.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3"><label class="form-label" for="dist">Race distance</label><select class="form-select" id="dist"><option value="42.195">Marathon — 42.195 km</option><option value="21.0975">Half marathon — 21.098 km</option><option value="10">10 km</option><option value="5">5 km</option></select></div>                    <div class="mb-3"><label class="form-label" for="hours">Goal time — hours</label><input type="number" class="form-control" id="hours" value="3" step="any"></div>                    <div class="mb-3"><label class="form-label" for="mins">Goal time — minutes</label><input type="number" class="form-control" id="mins" value="30" step="any"></div>                    <div class="mb-3"><label class="form-label" for="secs">Goal time — seconds</label><input type="number" class="form-control" id="secs" value="0" step="any"></div>                    <div class="mb-3"><label class="form-label" for="strat">Pacing strategy</label><select class="form-select" id="strat"><option value="even">Even splits</option><option value="neg">Negative split — second half 2% faster</option><option value="pos">Positive split — second half 2% slower</option></select></div>
                    <div id="result" class="border rounded p-3 mt-3">Enter your details above and the result will appear here.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Choose your race distance.</li><li>Enter your goal time.</li><li>Choose even, negative or positive splits and read the table.</li></ol>
                    <h2 class="h5">Note</h2>
                    <p class="small text-muted mb-0">Negative and positive strategies are modelled with the second half run 2 percent faster or slower than the first half, solved so the splits still add up exactly to your goal time. Real courses, weather and crowds change pacing. Estimate only — not medical advice.</p>
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
        var d = num("dist"), hh = num("hours"), mm = num("mins"), ss = num("secs"), strat = document.getElementById("strat").value;
        if ([d, hh, mm, ss].some(isNaN) || d <= 0) { out("Please enter a valid distance and goal time."); return; }
        var total = hh * 3600 + mm * 60 + ss;
        if (total <= 0) { out("Please enter a goal time above zero."); return; }
        var half = d / 2, factor = strat === "neg" ? 0.98 : strat === "pos" ? 1.02 : 1.0;
        var p1 = total / (half + half * factor);
        var rows = "", cum = 0, full = Math.floor(d);
        for (var k = 1; k <= full; k++) { var segStart = k - 1; var pace = (segStart < half && k <= half) || k <= half ? p1 : (segStart >= half ? p1 * factor : p1); if (segStart < half && k > half) { pace = (p1 * (half - segStart) + p1 * factor * (k - half)) / 1; } cum += pace; rows += "<tr><td>" + k + " km</td><td>" + fmtClock(pace) + "</td><td>" + fmtClock(cum) + "</td></tr>"; }
        var rem = d - full;
        if (rem > 0.001) { var lastPace = p1 * factor * rem; cum += lastPace; rows += "<tr><td>" + fmt(d, 3) + " km (finish)</td><td>" + fmtClock(lastPace) + "</td><td>" + fmtClock(cum) + "</td></tr>"; }
        out("<strong>Average pace:</strong> " + fmtClock(total / d) + " per km" + "<table class=\"table table-sm mt-2\"><thead><tr><th>Point</th><th>Split</th><th>Cumulative</th></tr></thead><tbody>" + rows + "</tbody></table>");
    }
    bind(["dist", "hours", "mins", "secs", "strat"], calc); calc();
})();
</script>
@endsection
