@extends('layouts.app')

@section('title', 'Rectangle Area and Perimeter Calculator — Free Online Tool')
@section('meta_description', 'Find the area, perimeter and diagonal of a rectangle at the same time')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Rectangle Area and Perimeter Calculator</h1>
            <p class="lead small text-muted">Enter the length and width — get the area, perimeter and diagonal (by Pythagoras) all at once. Perfect for land, room or plot calculations.</p>
<div class="row"><div class="col-md-4 mb-3"><label class="form-label" for="raL">Length</label><input type="number" step="any" class="form-control" id="raL" placeholder="e.g. 12"></div><div class="col-md-4 mb-3"><label class="form-label" for="raW">Width</label><input type="number" step="any" class="form-control" id="raW" placeholder="e.g. 8"></div></div><div class="alert alert-secondary mt-3 mb-0" id="raRes">Enter values — the result will show live here.</div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                <li>Enter the length and width in the same unit (feet, meter etc).</li>
                <li>The area, perimeter and diagonal will all show in the result.</li>
            </ol>
            <p class="small text-muted mb-0">Note: Formulas: Area = length x width, Perimeter = 2(length + width), Diagonal = √(length² + width²). The result will be in the same unit you entered.</p>
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


function raCalc() {
    var L = num("raL"), W = num("raW");
    if (L === null || W === null || L <= 0 || W <= 0) { setT("raRes", "Enter length and width (positive values)."); return; }
    setT("raRes", "Area: " + fmt(L * W, 4) + " sq units | Perimeter: " + fmt(2 * (L + W), 4) + " | Diagonal: " + fmt(Math.sqrt(L * L + W * W), 4));
}
bind(["raL","raW"], raCalc); raCalc();

})();
</script>
@endsection
