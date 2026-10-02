@extends('layouts.app')

@section('title', 'Percentage Points Calculator — Free Online Tool')
@section('meta_description', 'See the difference between two percentages in percentage points and in percent change.')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Percentage Points Calculator</h1>
            <p class="lead small text-muted">Enter two percentages — their direct difference in percentage points, and their relative change in percent change, are explained separately. For example, going from 10% to 12% is 2 percentage points, but a 20% increase.</p>
<div class="row"><div class="col-md-4 mb-3"><label class="form-label" for="ppA">First percentage (%)</label><input type="number" step="any" class="form-control" id="ppA" placeholder="e.g. 10"></div><div class="col-md-4 mb-3"><label class="form-label" for="ppB">Second percentage (%)</label><input type="number" step="any" class="form-control" id="ppB" placeholder="e.g. 12"></div></div><div class="alert alert-secondary mt-3 mb-0" id="ppRes">Enter the values — the result will show live here.</div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                <li>Enter the first percentage and the second percentage.</li>
                <li>The percentage-point difference is a simple subtraction.</li>
                <li>Percent change measures the second compared to the first.</li>
            </ol>
            <p class="small text-muted mb-0">Note: Percentage points = second % minus first %. Percent change = (difference / first %) x 100. If the first percentage is zero, percent change cannot be found.</p>
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


function ppCalc() {
    var a = num("ppA"), b = num("ppB");
    if (a === null || b === null) { setT("ppRes", "Please enter both percentages."); return; }
    var pp = b - a;
    var change = a !== 0 ? fmt(pp / Math.abs(a) * 100, 4) + "%" : "cannot be found (first % is zero)";
    setT("ppRes", "Difference: " + fmt(pp, 4) + " percentage points | Percent change (relative): " + change + " | Direction: " + (pp > 0 ? "increase" : (pp < 0 ? "decrease" : "no change")));
}
bind(["ppA","ppB"], ppCalc); ppCalc();

})();
</script>
@endsection
