@extends('layouts.app')

@section('title', 'Square Area and Perimeter Calculator — Free Online Tool')
@section('meta_description', 'Calculate the area, perimeter and diagonal of a square from its side')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Square Area and Perimeter Calculator</h1>
            <p class="lead small text-muted">Enter one side of your square — get area, perimeter and diagonal. All sides are equal, so one side is enough.</p>
<div class="col-md-4 mb-3"><label class="form-label" for="sqS">Side</label><input type="number" step="any" class="form-control" id="sqS" placeholder="e.g. 5"></div><div class="alert alert-secondary mt-3 mb-0" id="sqRes">Enter values — the result will show here live.</div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                <li>Enter the length of the square's side.</li>
                <li>Area, perimeter and diagonal will appear. For marla/land math: if the side is in feet, the area will be in square feet.</li>
            </ol>
            <p class="small text-muted mb-0">Note: Formulas: Area = side², Perimeter = 4 x side, Diagonal = side x √2.</p>
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


function sqCalc() {
    var s = num("sqS");
    if (s === null || s <= 0) { setT("sqRes", "Please enter a positive side."); return; }
    setT("sqRes", "Area: " + fmt(s * s, 4) + " sq units | Perimeter: " + fmt(4 * s, 4) + " | Diagonal: " + fmt(s * Math.SQRT2, 4));
}
bind(["sqS"], sqCalc); sqCalc();

})();
</script>
@endsection
