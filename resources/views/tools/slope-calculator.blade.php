@extends('layouts.app')

@section('title', 'Slope Calculator — Free Online Tool')
@section('meta_description', 'Find the slope and gradient of a line from two points, with steps')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Slope Calculator</h1>
            <p class="lead small text-muted">Enter two points (x1, y1) and (x2, y2) — get the slope (rise over run), the line equation, the distance between the points, the midpoint, and the tilt angle, all with steps.</p>
<div class="row"><div class="col-md-4 mb-3"><label class="form-label" for="slX1">x1</label><input type="number" step="any" class="form-control" id="slX1" placeholder="e.g. 1"></div><div class="col-md-4 mb-3"><label class="form-label" for="slY1">y1</label><input type="number" step="any" class="form-control" id="slY1" placeholder="e.g. 2"></div><div class="col-md-4 mb-3"><label class="form-label" for="slX2">x2</label><input type="number" step="any" class="form-control" id="slX2" placeholder="e.g. 4"></div><div class="col-md-4 mb-3"><label class="form-label" for="slY2">y2</label><input type="number" step="any" class="form-control" id="slY2" placeholder="e.g. 8"></div></div><div class="alert alert-secondary mt-3 mb-0" id="slRes">Enter the values — the result will show here live.</div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                <li>Enter x1, y1 of the first point and x2, y2 of the second point.</li>
                <li>The slope, equation (y = mx + b), distance, midpoint, and angle will appear.</li>
            </ol>
            <p class="small text-muted mb-0">Note: Formula: m = (y2 - y1) / (x2 - x1). If x2 = x1, the line is vertical and the slope is undefined. Angle = arctan(m), in degrees.</p>
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


function slCalc() {
    var x1 = num("slX1"), y1 = num("slY1"), x2 = num("slX2"), y2 = num("slY2");
    if (x1 === null || y1 === null || x2 === null || y2 === null) { setT("slRes", "Please enter all four coordinates."); return; }
    var rise = y2 - y1, run = x2 - x1;
    var dist = Math.sqrt(run * run + rise * rise);
    var mid = "(" + fmt((x1 + x2) / 2, 4) + ", " + fmt((y1 + y2) / 2, 4) + ")";
    if (run === 0) { setT("slRes", "Slope: undefined (vertical line, x = " + fmt(x1, 4) + ") | Rise: " + fmt(rise, 4) + " | Run: 0 | Distance: " + fmt(dist, 4) + " | Midpoint: " + mid); return; }
    var m = rise / run; var bint = y1 - m * x1; var ang = Math.atan(m) * 180 / Math.PI;
    setT("slRes", "Slope m = (" + fmt(y2, 2) + " - " + fmt(y1, 2) + ") / (" + fmt(x2, 2) + " - " + fmt(x1, 2) + ") = " + fmt(rise, 4) + " / " + fmt(run, 4) + " = " + fmt(m, 4) + " | Equation: y = " + fmt(m, 4) + "x " + (bint >= 0 ? "+ " : "- ") + fmt(Math.abs(bint), 4) + " | Angle: " + fmt(ang, 2) + "° | Distance: " + fmt(dist, 4) + " | Midpoint: " + mid);
}
bind(["slX1","slY1","slX2","slY2"], slCalc); slCalc();

})();
</script>
@endsection
