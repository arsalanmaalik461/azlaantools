@extends('layouts.app')

@section('title', 'Parallelogram Area Calculator — Free Online Tool')
@section('meta_description', 'Find the area and perimeter of a parallelogram from base, height and angle')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Parallelogram Area Calculator</h1>
            <p class="lead small text-muted">Find the area of a parallelogram in two ways: base x height, or from two sides and the angle between them. To understand the diagram: base is the bottom side, height is the perpendicular (straight) distance from it, and side is the slant side joined to the base.</p>
<div class="row"><div class="col-md-4 mb-3"><label class="form-label" for="paBase">Base (b)</label><input type="number" step="any" class="form-control" id="paBase" placeholder="e.g. 10"></div><div class="col-md-4 mb-3"><label class="form-label" for="paHeight">Height (h) — perpendicular</label><input type="number" step="any" class="form-control" id="paHeight" placeholder="e.g. 6"></div><div class="col-md-4 mb-3"><label class="form-label" for="paSide">Side (a)</label><input type="number" step="any" class="form-control" id="paSide" placeholder="e.g. 8"></div><div class="col-md-4 mb-3"><label class="form-label" for="paAngle">Angle between base and side (degrees)</label><input type="number" step="any" class="form-control" id="paAngle" placeholder="e.g. 60"></div></div><div class="alert alert-secondary mt-3 mb-0" id="paRes">Enter values — the result will show here live.</div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                <li>Enter base and height to get area = base x height.</li>
                <li>Also enter side and angle to see the second area (base x side x sin angle) and the perimeter.</li>
                <li>Both area values should be equal when the angle and height match each other (height = side x sin angle).</li>
            </ol>
            <p class="small text-muted mb-0">Note: Area formula: A = b x h, and A = b x a x sin(theta). Perimeter: P = 2(a + b). The angle is taken in degrees.</p>
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


function paCalc() {
    var b = num("paBase"), h = num("paHeight"), a = num("paSide"), ang = num("paAngle");
    var lines = [];
    if (b !== null && h !== null) { lines.push("Area (base x height): " + fmt(b * h, 4)); }
    if (b !== null && a !== null && ang !== null) { var rad = ang * Math.PI / 180; lines.push("Area (base x side x sin angle): " + fmt(b * a * Math.sin(rad), 4)); lines.push("Height from side and angle: " + fmt(a * Math.sin(rad), 4)); }
    if (b !== null && a !== null) { lines.push("Perimeter: " + fmt(2 * (b + a), 4)); }
    setT("paRes", lines.length ? lines.join(" | ") : "Enter at least base and height, or base, side and angle.");
}
bind(["paBase","paHeight","paSide","paAngle"], paCalc); paCalc();

})();
</script>
@endsection
