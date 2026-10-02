@extends('layouts.app')

@section('title', 'Right Triangle Solver — Free Online Tool')
@section('meta_description', 'Enter a side or angle and get all the sides and angles of a right triangle')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Right Triangle Solver</h1>
            <p class="lead small text-muted">Enter any two values — two sides, or one side and one angle — and the rest of the sides and angles are found. Diagram: c is the hypotenuse, angle A is opposite side a, angle B is opposite side b, and the third angle is always 90 degrees.</p>
<div class="row"><div class="col-md-4 mb-3"><label class="form-label" for="rtA">Side a (leg)</label><input type="number" step="any" class="form-control" id="rtA" placeholder="e.g. 3"></div><div class="col-md-4 mb-3"><label class="form-label" for="rtB">Side b (leg)</label><input type="number" step="any" class="form-control" id="rtB" placeholder="e.g. 4"></div><div class="col-md-4 mb-3"><label class="form-label" for="rtC">Side c (hypotenuse)</label><input type="number" step="" class="form-control" id="rtC" placeholder=""></div><div class="col-md-4 mb-3"><label class="form-label" for="rtAngA">Angle A (degrees, opposite side a)</label><input type="number" step="any" class="form-control" id="rtAngA" placeholder="e.g. 36.87"></div><div class="col-md-4 mb-3"><label class="form-label" for="rtAngB">Angle B (degrees, opposite side b)</label><input type="number" step="any" class="form-control" id="rtAngB" placeholder=""></div></div><div class="alert alert-secondary mt-3 mb-0" id="rtRes">Enter values — the result updates live here.</div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                <li>Enter two sides (leave the third empty), or one side together with one angle.</li>
                <li>The result shows all sides, both acute angles, the area and the perimeter.</li>
                <li>Angles are in degrees; A + B is always 90 degrees.</li>
            </ol>
            <p class="small text-muted mb-0">Note: Pythagoras (a² + b² = c²) and the basic trig ratios (sin, cos, tan) are used. Angles must be between 0 and 90.</p>
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


function rtCalc() {
    var a = num("rtA"), b = num("rtB"), c = num("rtC"), A = num("rtAngA"), B = num("rtAngB");
    var sides = [a, b, c].filter(function (v) { return v !== null && v > 0; }).length;
    var angs = [A, B].filter(function (v) { return v !== null; }).length;
    if (sides + angs < 2 || sides === 0) { setT("rtRes", "Enter at least one side plus one more value (a side or an angle)."); return; }
    if (A !== null && (A <= 0 || A >= 90)) { setT("rtRes", "Angle A must be between 0 and 90 degrees."); return; }
    if (B !== null && (B <= 0 || B >= 90)) { setT("rtRes", "Angle B must be between 0 and 90 degrees."); return; }
    if (A !== null && B !== null && Math.abs(A + B - 90) > 0.01) { setT("rtRes", "Angles A and B must add up to 90 degrees."); return; }
    if (A === null && B !== null) { A = 90 - B; }
    if (B === null && A !== null) { B = 90 - A; }
    if (sides >= 2) {
        if (c === null) { c = Math.sqrt(a * a + b * b); }
        else if (a === null) { if (c <= b) { setT("rtRes", "The hypotenuse must be the longest side."); return; } a = Math.sqrt(c * c - b * b); }
        else if (b === null) { if (c <= a) { setT("rtRes", "The hypotenuse must be the longest side."); return; } b = Math.sqrt(c * c - a * a); }
        A = Math.asin(a / c) * 180 / Math.PI; B = 90 - A;
    } else {
        // one side + angle
        if (A === null) { setT("rtRes", "Enter one angle together with the side."); return; }
        var Ar = A * Math.PI / 180;
        if (a !== null) { c = a / Math.sin(Ar); b = a / Math.tan(Ar); }
        else if (b !== null) { c = b / Math.cos(Ar); a = b * Math.tan(Ar); }
        else if (c !== null) { a = c * Math.sin(Ar); b = c * Math.cos(Ar); }
        B = 90 - A;
    }
    var area = a * b / 2; var per = a + b + c;
    setT("rtRes", "a = " + fmt(a, 4) + " | b = " + fmt(b, 4) + " | c (hypotenuse) = " + fmt(c, 4) + " | Angle A = " + fmt(A, 2) + "° | Angle B = " + fmt(B, 2) + "° | Angle C = 90° | Area = " + fmt(area, 4) + " | Perimeter = " + fmt(per, 4));
}
bind(["rtA","rtB","rtC","rtAngA","rtAngB"], rtCalc); rtCalc();

})();
</script>
@endsection
