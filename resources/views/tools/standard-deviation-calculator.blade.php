@extends('layouts.app')

@section('title', 'Standard Deviation Calculator — Free Online Tool')
@section('meta_description', 'Find the standard deviation and variance of a data set using both sample and population methods')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Standard Deviation Calculator</h1>
            <p class="lead small text-muted">Enter a list of numbers separated by commas, spaces or new lines — get count, sum, mean, variance and standard deviation (both sample and population) with intermediate values.</p>
<div class="mb-3"><label class="form-label" for="sdData">List of numbers</label><textarea class="form-control" id="sdData" rows="4" placeholder="e.g. 10, 12, 23, 23, 16, 23, 21, 16"></textarea></div><div class="alert alert-secondary mt-3 mb-0" id="sdRes">Enter values — the result will show here live.</div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                <li>Enter data values — you can separate them with commas, spaces or Enter.</li>
                <li>The result shows count, sum and mean first, then the sum of squared deviations, then variance and SD.</li>
                <li>Use population SD for a full group, and sample SD when your data is only a sample of a larger group.</li>
            </ol>
            <p class="small text-muted mb-0">Note: Population variance = sum of squared deviations / N. Sample variance = sum / (n - 1). SD is the square root of variance. You need at least 2 values for the sample.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    "use strict";

function fmt(n, d) { if (n === null || n === undefined || !isFinite(n)) { return "\u2014"; } var dec = (d === undefined ? 4 : d); return Number(n.toFixed(dec)).toLocaleString("en-US", { maximumFractionDigits: dec }); }
function num(id) { var e = document.getElementById(id); if (!e) { return null; } var v = parseFloat(e.value); return isNaN(v) ? null : v; }
function txt(id) { var e = document.getElementById(id); return e ? e.value : ""; }
function setT(id, t) { var e = document.getElementById(id); if (e) { e.textContent = t; } }
function setH(id, t) { var e = document.getElementById(id); if (e) { e.innerHTML = t; } }
function bind(ids, fn) { ids.forEach(function (id) { var e = document.getElementById(id); if (e) { e.addEventListener("input", fn); e.addEventListener("change", fn); } }); }
function parseList(s) { if (!s) { return []; } var parts = s.split(/[\s,;]+/); var out = []; for (var i = 0; i < parts.length; i++) { if (parts[i] === "") { continue; } var v = parseFloat(parts[i]); if (!isNaN(v) && isFinite(v)) { out.push(v); } } return out; }
function esc(s) { return String(s).replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;"); }


function sdCalc() {
    var vals = parseList(txt("sdData"));
    if (vals.length === 0) { setT("sdRes", "Enter at least one number, for example: 10, 12, 23, 16"); return; }
    var n = vals.length; var sum = vals.reduce(function (s, v) { return s + v; }, 0); var mean = sum / n;
    var ss = vals.reduce(function (s, v) { return s + (v - mean) * (v - mean); }, 0);
    var sorted = vals.slice().sort(function (a, b) { return a - b; });
    var popVar = ss / n; var msg = "Count (n): " + n + " | Sum: " + fmt(sum, 4) + " | Mean: " + fmt(mean, 4) + " | Sum of squared deviations: " + fmt(ss, 4) + " | Min: " + fmt(sorted[0], 4) + " | Max: " + fmt(sorted[n - 1], 4) + " | Range: " + fmt(sorted[n - 1] - sorted[0], 4) + " | Population variance: " + fmt(popVar, 4) + " | Population SD: " + fmt(Math.sqrt(popVar), 4);
    if (n >= 2) { var sVar = ss / (n - 1); msg += " | Sample variance: " + fmt(sVar, 4) + " | Sample SD: " + fmt(Math.sqrt(sVar), 4); }
    else { msg += " | You need at least 2 values for the sample SD."; }
    setT("sdRes", msg);
}
bind(["sdData"], sdCalc); sdCalc();

})();
</script>
@endsection
