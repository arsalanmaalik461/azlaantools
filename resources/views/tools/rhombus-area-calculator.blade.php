@extends('layouts.app')

@section('title', 'Rhombus Area Calculator — Free Online Tool')
@section('meta_description', 'Calculate rhombus area from diagonals or base and height')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Rhombus Area Calculator</h1>
            <p class="lead small text-muted">Rhombus area in two common ways: from diagonals (d1 x d2 / 2) or from base x height. About the shape: the diagonals cut each other in half at a right angle, and all sides are equal. Fill in the method you have data for.</p>
<div class="row"><div class="col-md-4 mb-3"><label class="form-label" for="rhD1">Diagonal 1 (d1)</label><input type="number" step="any" class="form-control" id="rhD1" placeholder="e.g. 10"></div><div class="col-md-4 mb-3"><label class="form-label" for="rhD2">Diagonal 2 (d2)</label><input type="number" step="any" class="form-control" id="rhD2" placeholder="e.g. 8"></div><div class="col-md-4 mb-3"><label class="form-label" for="rhBase">Base / side</label><input type="number" step="any" class="form-control" id="rhBase" placeholder="e.g. 6.4"></div><div class="col-md-4 mb-3"><label class="form-label" for="rhH">Height</label><input type="number" step="any" class="form-control" id="rhH" placeholder="e.g. 6"></div></div><div class="alert alert-secondary mt-3 mb-0" id="rhRes">Enter values — result will show here live.</div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                <li>Diagonals method: enter d1 and d2 — you will also get area, side and perimeter.</li>
                <li>Base-height method: enter base and height — you will get the area.</li>
                <li>You can also fill both methods together.</li>
            </ol>
            <p class="small text-muted mb-0">Note: Formulas: Area = (d1 x d2) / 2, Side = √((d1/2)² + (d2/2)²), Perimeter = 4 x side, and Area = base x height.</p>
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


function rhCalc() {
    var d1 = num("rhD1"), d2 = num("rhD2"), b = num("rhBase"), h = num("rhH");
    var lines = [];
    if (d1 !== null && d2 !== null && d1 > 0 && d2 > 0) { var side = Math.sqrt(Math.pow(d1 / 2, 2) + Math.pow(d2 / 2, 2)); lines.push("Area from diagonals: " + fmt(d1 * d2 / 2, 4) + ", side: " + fmt(side, 4) + ", perimeter: " + fmt(4 * side, 4)); }
    if (b !== null && h !== null && b > 0 && h > 0) { lines.push("Area from base x height: " + fmt(b * h, 4)); }
    setT("rhRes", lines.length ? lines.join(" | ") : "Enter diagonals (d1, d2) or base and height.");
}
bind(["rhD1","rhD2","rhBase","rhH"], rhCalc); rhCalc();

})();
</script>
@endsection
