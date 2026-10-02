@extends('layouts.app')

@section('title', 'Trapezoid Area Calculator — Free Online Tool')
@section('meta_description', 'Calculate the area, perimeter and height of a trapezoid with different formulas')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Trapezoid Area Calculator</h1>
            <p class="lead small text-muted">Enter the two parallel sides (a and b) and the height — you get the area and the median (mid-segment). Also enter the legs (non-parallel sides) to get the perimeter. Diagram help: a is the top parallel side, b is the bottom one, and height is the straight distance between them.</p>
<div class="row"><div class="col-md-4 mb-3"><label class="form-label" for="tzA">Parallel side a (top)</label><input type="number" step="any" class="form-control" id="tzA" placeholder="e.g. 6"></div><div class="col-md-4 mb-3"><label class="form-label" for="tzB">Parallel side b (bottom)</label><input type="number" step="any" class="form-control" id="tzB" placeholder="e.g. 10"></div><div class="col-md-4 mb-3"><label class="form-label" for="tzH">Height</label><input type="number" step="any" class="form-control" id="tzH" placeholder="e.g. 4"></div><div class="col-md-4 mb-3"><label class="form-label" for="tzC">Leg c (optional)</label><input type="number" step="any" class="form-control" id="tzC" placeholder="e.g. 5"></div><div class="col-md-4 mb-3"><label class="form-label" for="tzD">Leg d (optional)</label><input type="number" step="any" class="form-control" id="tzD" placeholder="e.g. 5"></div></div><div class="alert alert-secondary mt-3 mb-0" id="tzRes">Enter values — the result shows live here.</div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                <li>Enter the parallel sides a and b and the height.</li>
                <li>Also enter legs c and d to get the perimeter.</li>
                <li>The median (average of the two parallel sides) also shows in the result.</li>
            </ol>
            <p class="small text-muted mb-0">Note: Formula: Area = ((a + b) / 2) x height. Median = (a + b) / 2, and Area can also be written as median x height. Perimeter is the sum of all four sides.</p>
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


function tzCalc() {
    var a = num("tzA"), b = num("tzB"), h = num("tzH"), c = num("tzC"), d = num("tzD");
    if (a === null || b === null || h === null || a <= 0 || b <= 0 || h <= 0) { setT("tzRes", "Enter parallel sides a and b and height (positive)."); return; }
    var med = (a + b) / 2; var msg = "Area: " + fmt(med * h, 4) + " | Median (mid-segment): " + fmt(med, 4);
    if (c !== null && d !== null) { msg += " | Perimeter: " + fmt(a + b + c + d, 4); }
    else { msg += " | Also enter both legs (c, d) for the perimeter."; }
    setT("tzRes", msg);
}
bind(["tzA","tzB","tzH","tzC","tzD"], tzCalc); tzCalc();

})();
</script>
@endsection
