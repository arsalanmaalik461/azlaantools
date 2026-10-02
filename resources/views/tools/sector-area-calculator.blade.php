@extends('layouts.app')

@section('title', 'Sector Area Calculator — Free Online Tool')
@section('meta_description', 'Calculate the area of a circle sector and its share of the full circle.')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Sector Area Calculator</h1>
            <p class="lead small text-muted">Enter the circle radius and the sector angle — you get the sector area, the arc length, and what percent of the full circle it is. Angle can be in degrees or radians.</p>
<div class="row"><div class="col-md-4 mb-3"><label class="form-label" for="scR">Radius (r)</label><input type="number" step="any" class="form-control" id="scR" placeholder="e.g. 7"></div><div class="col-md-4 mb-3"><label class="form-label" for="scAng">Angle</label><input type="number" step="any" class="form-control" id="scAng" placeholder="e.g. 90"></div><div class="col-md-4 mb-3"><label class="form-label" for="scUnit">Angle unit</label><select class="form-select" id="scUnit"><option value="deg">Degrees</option><option value="rad">Radians</option></select></div></div><div class="alert alert-secondary mt-3 mb-0" id="scRes">Enter values — the result will show live here.</div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                <li>Enter the radius and sector angle, choose the unit.</li>
                <li>You get the sector area, arc length and the percent share of the circle.</li>
            </ol>
            <p class="small text-muted mb-0">Note: Formulas: Sector area = (theta/360) x pi r² (degrees) or (1/2) r² theta (radians). Arc length = r x theta (radians). The full circle area is also shown for comparison.</p>
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


function scCalc() {
    var r = num("scR"), ang = num("scAng"), unit = txt("scUnit");
    if (r === null || ang === null || r <= 0) { setT("scRes", "Enter a positive radius and the angle."); return; }
    var rad = unit === "deg" ? ang * Math.PI / 180 : ang;
    var deg = unit === "deg" ? ang : ang * 180 / Math.PI;
    if (deg < 0 || deg > 360) { setT("scRes", "Angle must be between 0 and 360 degrees."); return; }
    var area = 0.5 * r * r * rad; var arc = r * rad; var full = Math.PI * r * r;
    setT("scRes", "Sector area: " + fmt(area, 4) + " | Arc length: " + fmt(arc, 4) + " | Share of full circle: " + fmt(deg / 360 * 100, 2) + "% | Full circle area: " + fmt(full, 4) + " | Angle: " + fmt(deg, 2) + "° = " + fmt(rad, 4) + " rad");
}
bind(["scR","scAng","scUnit"], scCalc); scCalc();

})();
</script>
@endsection
