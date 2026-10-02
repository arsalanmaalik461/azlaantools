@extends('layouts.app')

@section('title', 'Percent Change Calculator — Free Online Tool')
@section('meta_description', 'Find the percent increase or decrease between the old and new value.')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Percent Change Calculator</h1>
            <p class="lead small text-muted">Enter the old and new value — get the percent increase or decrease, with the difference, live.</p>
<div class="row"><div class="col-md-4 mb-3"><label class="form-label" for="pcOld">Old value</label><input type="number" step="any" class="form-control" id="pcOld" placeholder="e.g. 1000"></div><div class="col-md-4 mb-3"><label class="form-label" for="pcNew">New value</label><input type="number" step="any" class="form-control" id="pcNew" placeholder="e.g. 1200"></div></div><div class="alert alert-secondary mt-3 mb-0" id="pcRes">Enter the values — the result will show live here.</div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                <li>Enter the old value and the new value.</li>
                <li>The result shows both the percent change and the actual difference (new minus old).</li>
                <li>It shows increase if the value went up, and decrease if it went down.</li>
            </ol>
            <p class="small text-muted mb-0">Note: Formula: percent change = ((new - old) / old) x 100. If the old value is zero, percent change cannot be found.</p>
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


function pcCalc() {
    var o = num("pcOld"), n = num("pcNew");
    if (o === null || n === null) { setT("pcRes", "Please enter both values."); return; }
    if (o === 0) { setT("pcRes", "Old value is zero — percent change cannot be found (cannot divide by zero)."); return; }
    var diff = n - o; var pct = diff / o * 100;
    var label = pct > 0 ? "increase" : (pct < 0 ? "decrease" : "no change");
    setT("pcRes", "Percent change: " + fmt(Math.abs(pct), 4) + "% " + label + " | Difference: " + fmt(diff, 4));
}
bind(["pcOld","pcNew"], pcCalc); pcCalc();

})();
</script>
@endsection
