@extends('layouts.app')

@section('title', 'Vector Calculator — Free Online Tool')
@section('meta_description', 'Add and subtract vectors, and find dot product, cross product and magnitude')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Vector Calculator</h1>
            <p class="lead small text-muted">Enter the components (x, y, z) of two 3D vectors A and B — you get A+B, A-B, dot product, cross product, both magnitudes and the angle between them, all at once. For 2D, keep z = 0.</p>
<div class="row"><div class="col-md-4 mb-3"><label class="form-label" for="vcAx">A: x</label><input type="number" step="any" class="form-control" id="vcAx" placeholder="e.g. 1"></div><div class="col-md-4 mb-3"><label class="form-label" for="vcAy">A: y</label><input type="number" step="any" class="form-control" id="vcAy" placeholder="e.g. 2"></div><div class="col-md-4 mb-3"><label class="form-label" for="vcAz">A: z</label><input type="number" step="any" class="form-control" id="vcAz" placeholder="e.g. 3"></div><div class="col-md-4 mb-3"><label class="form-label" for="vcBx">B: x</label><input type="number" step="any" class="form-control" id="vcBx" placeholder="e.g. 4"></div><div class="col-md-4 mb-3"><label class="form-label" for="vcBy">B: y</label><input type="number" step="any" class="form-control" id="vcBy" placeholder="e.g. 5"></div><div class="col-md-4 mb-3"><label class="form-label" for="vcBz">B: z</label><input type="number" step="any" class="form-control" id="vcBz" placeholder="e.g. 6"></div></div><div class="alert alert-secondary mt-3 mb-0" id="vcRes">Enter values — the result will show here live.</div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                <li>Enter the x, y, z components of vector A.</li>
                <li>Enter the x, y, z components of vector B (z = 0 for 2D).</li>
                <li>All operation results appear in the result box.</li>
            </ol>
            <p class="small text-muted mb-0">Note: The dot product is a scalar: A.B = ax bx + ay by + az bz. The cross product is a new vector that is perpendicular to both. The angle comes from the dot product: cos theta = (A.B) / (|A| |B|).</p>
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


function vcCalc() {
    var ax = num("vcAx"), ay = num("vcAy"), az = num("vcAz"), bx = num("vcBx"), by = num("vcBy"), bz = num("vcBz");
    if ([ax,ay,az,bx,by,bz].indexOf(null) !== -1) { setT("vcRes", "Enter all components of both vectors (z = 0 for 2D)."); return; }
    function vstr(x, y, z) { return "(" + fmt(x, 4) + ", " + fmt(y, 4) + ", " + fmt(z, 4) + ")"; }
    var dot = ax * bx + ay * by + az * bz;
    var cx = ay * bz - az * by, cy = az * bx - ax * bz, cz = ax * by - ay * bx;
    var ma = Math.sqrt(ax * ax + ay * ay + az * az), mb = Math.sqrt(bx * bx + by * by + bz * bz);
    var ang = (ma === 0 || mb === 0) ? "undefined (one vector is zero)" : fmt(Math.acos(Math.max(-1, Math.min(1, dot / (ma * mb)))) * 180 / Math.PI, 2) + "°";
    setT("vcRes", "A + B = " + vstr(ax + bx, ay + by, az + bz) + " | A - B = " + vstr(ax - bx, ay - by, az - bz) + " | Dot product A.B = " + fmt(dot, 4) + " | Cross product A x B = " + vstr(cx, cy, cz) + " | |A| = " + fmt(ma, 4) + " | |B| = " + fmt(mb, 4) + " | Angle between: " + ang);
}
bind(["vcAx","vcAy","vcAz","vcBx","vcBy","vcBz"], vcCalc); vcCalc();

})();
</script>
@endsection
