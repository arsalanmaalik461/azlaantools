@extends('layouts.app')

@section('title', 'Unit Circle Calculator — Free Online Tool')
@section('meta_description', 'See exact trig values of the unit circle at standard angles.')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Unit Circle Calculator</h1>
            <p class="lead small text-muted">Enter an angle — get its sin, cos, tan values on the unit circle; exact surd form is also given at standard angles (0, 30, 45, 60...), along with quadrant and reference angle. The radius is always 1, so the point is (cos, sin).</p>
<div class="col-md-4 mb-3"><label class="form-label" for="ucAng">Angle (degrees)</label><input type="number" step="any" class="form-control" id="ucAng" placeholder="e.g. 135"></div><div class="alert alert-secondary mt-3 mb-0" id="ucRes">Enter a value — the result will show here live.</div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                <li>Enter the angle in degrees — negative or larger than 360 is fine, the tool will bring it into 0-360.</li>
                <li>Exact values are only given for standard angles; others will show decimal values.</li>
            </ol>
            <p class="small text-muted mb-0">Note: On the unit circle the point (x, y) = (cos theta, sin theta). The reference angle is the acute angle made with the x-axis. Signs by quadrant: Q1 all +, Q2 sin +, Q3 tan +, Q4 cos + (ASTC rule).</p>
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


var UC = {0:["0","1","0"],30:["1/2","sqrt3/2","1/sqrt3"],45:["sqrt2/2","sqrt2/2","1"],60:["sqrt3/2","1/2","sqrt3"],90:["1","0","undefined"],120:["sqrt3/2","-1/2","-sqrt3"],135:["sqrt2/2","-sqrt2/2","-1"],150:["1/2","-sqrt3/2","-1/sqrt3"],180:["0","-1","0"],210:["-1/2","-sqrt3/2","1/sqrt3"],225:["-sqrt2/2","-sqrt2/2","1"],240:["-sqrt3/2","-1/2","sqrt3"],270:["-1","0","undefined"],300:["-sqrt3/2","1/2","-sqrt3"],315:["-sqrt2/2","sqrt2/2","-1"],330:["-1/2","sqrt3/2","-1/sqrt3"]};
function ucCalc() {
    var ang = num("ucAng");
    if (ang === null) { setT("ucRes", "Enter the angle in degrees."); return; }
    var norm = ((ang % 360) + 360) % 360;
    var rad = norm * Math.PI / 180;
    var s = Math.sin(rad), co = Math.cos(rad);
    var quad = norm === 0 || norm === 90 || norm === 180 || norm === 270 ? "on an axis (no quadrant)" : (norm < 90 ? "Quadrant I" : (norm < 180 ? "Quadrant II" : (norm < 270 ? "Quadrant III" : "Quadrant IV")));
    var ref = norm <= 90 ? norm : (norm <= 180 ? 180 - norm : (norm <= 270 ? norm - 180 : 360 - norm));
    var ta = Math.abs(co) < 1e-12 ? "undefined" : fmt(s / co, 6);
    var ex = UC[Math.round(norm)];
    var exMsg = (ex && Math.abs(norm - Math.round(norm)) < 1e-9) ? " | Exact values: sin = " + ex[0] + ", cos = " + ex[1] + ", tan = " + ex[2] : " | This is not a standard angle — no exact surd form, see the decimal values.";
    setT("ucRes", "Angle (0-360): " + fmt(norm, 2) + "° = " + fmt(rad, 6) + " rad | Point (cos, sin) = (" + fmt(co, 6) + ", " + fmt(s, 6) + ") | sin = " + fmt(s, 6) + " | cos = " + fmt(co, 6) + " | tan = " + ta + " | " + quad + " | Reference angle: " + fmt(ref, 2) + "°" + exMsg);
}
bind(["ucAng"], ucCalc); ucCalc();

})();
</script>
@endsection
