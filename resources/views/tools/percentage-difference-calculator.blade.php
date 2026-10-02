@extends('layouts.app')

@section('title', 'Percentage Difference Calculator — Free Online Tool')
@section('meta_description', 'Find the percentage difference between two values using their average as the base.')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Percentage Difference Calculator</h1>
            <p class="lead small text-muted">The percentage difference between two values uses their average as the base — this is different from percent change, because no value here is old or new.</p>
<div class="row"><div class="col-md-4 mb-3"><label class="form-label" for="pdA">Value 1</label><input type="number" step="any" class="form-control" id="pdA" placeholder="e.g. 80"></div><div class="col-md-4 mb-3"><label class="form-label" for="pdB">Value 2</label><input type="number" step="any" class="form-control" id="pdB" placeholder="e.g. 100"></div></div><div class="alert alert-secondary mt-3 mb-0" id="pdRes">Enter the values — the result will show live here.</div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                <li>Enter the first and second value.</li>
                <li>The result shows the absolute difference, the average, and the percentage difference.</li>
            </ol>
            <p class="small text-muted mb-0">Note: Formula: percentage difference = (|V1 - V2| / average of V1 and V2) x 100. If both values are zero, the result is zero.</p>
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


function pdCalc() {
    var a = num("pdA"), b = num("pdB");
    if (a === null || b === null) { setT("pdRes", "Please enter both values."); return; }
    var diff = Math.abs(a - b); var avg = (Math.abs(a) + Math.abs(b)) / 2;
    if (avg === 0) { setT("pdRes", "Both values are zero — percentage difference is 0%."); return; }
    setT("pdRes", "Difference: " + fmt(diff, 4) + " | Average: " + fmt(avg, 4) + " | Percentage difference: " + fmt(diff / avg * 100, 4) + "%");
}
bind(["pdA","pdB"], pdCalc); pdCalc();

})();
</script>
@endsection
