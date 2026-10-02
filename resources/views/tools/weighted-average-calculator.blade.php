@extends('layouts.app')

@section('title', 'Weighted Average Calculator — Free Online Tool')
@section('meta_description', 'Find the average with weights, like marks with credit hours')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Weighted Average Calculator</h1>
            <p class="lead small text-muted">Write the value and its weight on each line, separated by a comma — see the weighted average, sum of weights, sum of values and the simple average for comparison, with intermediate values.</p>
<div class="mb-3"><label class="form-label" for="waData">Value, Weight — one per line</label><textarea class="form-control" id="waData" rows="5" placeholder="e.g.
80, 3
90, 2
70, 1"></textarea></div><div class="alert alert-secondary mt-3 mb-0" id="waRes">Enter values — the result will show live here.</div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                <li>Write the value first, then its weight, on each line. Example: Maths 80 marks with weight (credit hours) 3 means the line: 80, 3.</li>
                <li>The result shows the sum of weights, sum of (value x weight) and the weighted average.</li>
            </ol>
            <p class="small text-muted mb-0">Note: Formula: Weighted average = sum(value x weight) / sum(weights). If a weight is zero, that value is not included in the calculation. If all weights are equal, the weighted average is the same as the simple average.</p>
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


function waCalc() {
    var lines = txt("waData").split(/\n/); var pairs = [];
    lines.forEach(function (ln) { var p = parseList(ln); if (p.length >= 2) { pairs.push([p[0], p[1]]); } });
    if (!pairs.length) { setT("waRes", "Write the value and weight on each line, like: 80, 3"); return; }
    var sw = 0, swx = 0, sx = 0;
    pairs.forEach(function (pr) { sw += pr[1]; swx += pr[0] * pr[1]; sx += pr[0]; });
    if (sw === 0) { setT("waRes", "The sum of weights is zero — cannot find the weighted average."); return; }
    setT("waRes", "Entries: " + pairs.length + " | Sum of weights: " + fmt(sw, 4) + " | Sum of (value x weight): " + fmt(swx, 4) + " | Weighted average: " + fmt(swx / sw, 4) + " | Simple average (for comparison): " + fmt(sx / pairs.length, 4));
}
bind(["waData"], waCalc); waCalc();

})();
</script>
@endsection
