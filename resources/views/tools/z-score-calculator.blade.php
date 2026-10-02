@extends('layouts.app')

@section('title', 'Z Score Calculator — Free Online Tool')
@section('meta_description', 'Find the z score and its table value from value, mean and standard deviation')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Z Score Calculator</h1>
            <p class="lead small text-muted">Enter the value (x), the data mean and the standard deviation — you will get the z-score plus its percentile (normal table value), meaning how much percent of data is below this value.</p>
<div class="row"><div class="col-md-4 mb-3"><label class="form-label" for="zsX">Value (x)</label><input type="number" step="any" class="form-control" id="zsX" placeholder="e.g. 85"></div><div class="col-md-4 mb-3"><label class="form-label" for="zsMean">Mean</label><input type="number" step="any" class="form-control" id="zsMean" placeholder="e.g. 70"></div><div class="col-md-4 mb-3"><label class="form-label" for="zsSd">Standard deviation</label><input type="number" step="any" class="form-control" id="zsSd" placeholder="e.g. 10"></div></div><div class="alert alert-secondary mt-3 mb-0" id="zsRes">Enter values — the result will show here live.</div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                <li>Enter your value x, the group mean and the standard deviation.</li>
                <li>The result shows the z-score, the percentile, and the percent area above this value.</li>
            </ol>
            <p class="small text-muted mb-0">Note: Formula: z = (x - mean) / SD. The percentile is taken from the standard normal table (cumulative) — if z is positive the value is above the mean, if negative it is below. This calculation assumes a normal distribution.</p>
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


function normCdf(z) { var t = 1 / (1 + 0.2316419 * Math.abs(z)); var d = 0.3989423 * Math.exp(-z * z / 2); var p = d * t * (0.3193815 + t * (-0.3565638 + t * (1.781478 + t * (-1.821256 + t * 1.330274)))); return z >= 0 ? 1 - p : p; }
function zsCalc() {
    var x = num("zsX"), m = num("zsMean"), sd = num("zsSd");
    if (x === null || m === null || sd === null) { setT("zsRes", "Enter the value, mean and standard deviation, all three."); return; }
    if (sd <= 0) { setT("zsRes", "Standard deviation must be positive."); return; }
    var z = (x - m) / sd; var pct = normCdf(z) * 100;
    setT("zsRes", "Z-score: " + fmt(z, 4) + " | Meaning: the value is " + fmt(Math.abs(z), 2) + " standard deviations " + (z >= 0 ? "above" : "below") + " the mean | Percentile (data below this): " + fmt(pct, 2) + "% | Data above this: " + fmt(100 - pct, 2) + "%");
}
bind(["zsX","zsMean","zsSd"], zsCalc); zsCalc();

})();
</script>
@endsection
