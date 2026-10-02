@extends('layouts.app')

@section('title', 'Rowing Pace Calculator — Free Online Tool')
@section('meta_description', 'Convert rowing split per 500 metres into watts, pace and predicted times with the standard power formula')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Rowing Pace Calculator</h1>
            <p class="lead small text-muted">Enter your 500 m split from the erg monitor to see your power in watts and predicted times at the same pace for standard distances.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3"><label class="form-label" for="mins">Split — minutes per 500 m</label><input type="number" class="form-control" id="mins" value="2" step="any"></div>                    <div class="mb-3"><label class="form-label" for="secs">Split — seconds</label><input type="number" class="form-control" id="secs" value="0" step="any"></div>
                    <div id="result" class="border rounded p-3 mt-3">Enter your details above and the result will appear here.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Enter your split as minutes and seconds per 500 metres.</li><li>Your watts and predicted times appear instantly.</li></ol>
                    <h2 class="h5">Note</h2>
                    <p class="small text-muted mb-0">Uses the standard erg relationship: watts = 2.80 x (500 / split seconds) cubed, the formula behind Concept2 pace conversions. On-water times differ with boat, crew and conditions. Estimate only — not medical advice.</p>
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
        var mm = num("mins"), ss = num("secs");
        if (isNaN(mm) || isNaN(ss) || mm < 0 || ss < 0) { out("Please enter a valid split."); return; }
        var split = mm * 60 + ss;
        if (split <= 0) { out("Please enter a split above zero."); return; }
        var watts = 2.80 * Math.pow(500 / split, 3);
        var dists = [1000, 2000, 5000, 10000];
        var rows = "";
        dists.forEach(function (d) { rows += "<tr><td>" + fmt(d, 0) + " m</td><td>" + fmtClock(split * d / 500) + "</td></tr>"; });
        out("<strong>Power:</strong> about " + fmt(watts, 0) + " W<br><strong>Pace per 100 m:</strong> " + fmtClock(split / 5) + "<table class=\"table table-sm mt-2\"><thead><tr><th>Distance at this pace</th><th>Time</th></tr></thead><tbody>" + rows + "</tbody></table>");
    }
    bind(["mins", "secs"], calc); calc();
})();
</script>
@endsection
