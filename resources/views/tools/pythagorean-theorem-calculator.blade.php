@extends('layouts.app')

@section('title', 'Pythagorean Theorem Calculator — Free Online Tool')
@section('meta_description', 'Find the missing side of a right triangle with the Pythagoras theorem, with diagram logic')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Pythagorean Theorem Calculator</h1>
            <p class="lead small text-muted">Enter two sides of a right triangle, the third will be found automatically. Diagram logic: c is always the longest side (hypotenuse) opposite the right angle (90 degrees); a and b are the two sides that form the right angle.</p>
<div class="row"><div class="col-md-4 mb-3"><label class="form-label" for="ptA">Side a (leg)</label><input type="number" step="any" class="form-control" id="ptA" placeholder="e.g. 3"></div><div class="col-md-4 mb-3"><label class="form-label" for="ptB">Side b (leg)</label><input type="number" step="any" class="form-control" id="ptB" placeholder="e.g. 4"></div><div class="col-md-4 mb-3"><label class="form-label" for="ptC">Side c (hypotenuse)</label><input type="number" step="any" class="form-control" id="ptC" placeholder="e.g. 5"></div></div><div class="alert alert-secondary mt-3 mb-0" id="ptRes">Enter values — the result will show here live.</div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                <li>Enter any two of the three sides, leave the third empty.</li>
                <li>Enter all three and the tool will check whether the triangle satisfies Pythagoras or not.</li>
            </ol>
            <p class="small text-muted mb-0">Note: Formula: a² + b² = c². The hypotenuse is always longer than each leg; if a leg is entered longer than the hypotenuse, the calculation is not possible.</p>
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


function ptCalc() {
    var a = num("ptA"), b = num("ptB"), c = num("ptC");
    var have = [a, b, c].filter(function (v) { return v !== null; }).length;
    if (have < 2) { setT("ptRes", "Please enter at least two sides."); return; }
    if (have === 3) {
        var lhs = a * a + b * b;
        setT("ptRes", Math.abs(lhs - c * c) < 1e-9 ? "Check: a² + b² = " + fmt(lhs, 4) + " = c² — this is a correct right triangle." : "Check: a² + b² = " + fmt(lhs, 4) + " but c² = " + fmt(c * c, 4) + " — this is not a right triangle.");
        return;
    }
    if (c === null) { setT("ptRes", "c = square root of (a² + b²) = " + fmt(Math.sqrt(a * a + b * b), 4)); return; }
    if (a === null) { if (c <= b) { setT("ptRes", "Hypotenuse (c) must be longer than each leg."); return; } setT("ptRes", "a = square root of (c² - b²) = " + fmt(Math.sqrt(c * c - b * b), 4)); return; }
    if (b === null) { if (c <= a) { setT("ptRes", "Hypotenuse (c) must be longer than each leg."); return; } setT("ptRes", "b = square root of (c² - a²) = " + fmt(Math.sqrt(c * c - a * a), 4)); return; }
}
bind(["ptA","ptB","ptC"], ptCalc); ptCalc();

})();
</script>
@endsection
