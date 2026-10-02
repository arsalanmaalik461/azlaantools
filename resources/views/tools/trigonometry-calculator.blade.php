@extends('layouts.app')

@section('title', 'Trigonometry Calculator — Free Online Tool')
@section('meta_description', 'Find sin, cos, tan of an angle and their inverse values, with a table')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Trigonometry Calculator</h1>
            <p class="lead small text-muted">Enter an angle (degrees or radians) — you get sin, cos, tan plus sec, csc and cot. In the inverse section below you can also find the angle from a ratio value. A table of standard angles is also given.</p>
<div class="row"><div class="col-md-4 mb-3"><label class="form-label" for="trAng">Angle</label><input type="number" step="any" class="form-control" id="trAng" placeholder="e.g. 30"></div><div class="col-md-4 mb-3"><label class="form-label" for="trUnit">Unit</label><select class="form-select" id="trUnit"><option value="deg">Degrees</option><option value="rad">Radians</option></select></div></div><div class="alert alert-secondary mt-3 mb-0" id="trRes">Enter values — the result shows live here.</div><h2 class="h6 mt-4">Inverse — angle from value</h2><div class="row"><div class="col-md-4 mb-3"><label class="form-label" for="trVal">Ratio value (such as 0.5)</label><input type="number" step="any" class="form-control" id="trVal" placeholder="e.g. 0.5"></div><div class="col-md-4 mb-3"><label class="form-label" for="trFn">Function</label><select class="form-select" id="trFn"><option value="asin">sin inverse</option><option value="acos">cos inverse</option><option value="atan">tan inverse</option></select></div></div><div class="alert alert-secondary mt-3 mb-0" id="trInv">Enter values — the result shows live here.</div><div class="table-responsive mt-4"><table class="table table-sm table-bordered"><thead><tr><th>Angle</th><th>sin</th><th>cos</th><th>tan</th></tr></thead><tbody><tr><td>0°</td><td>0</td><td>1</td><td>0</td></tr><tr><td>30°</td><td>1/2</td><td>√3/2</td><td>1/√3</td></tr><tr><td>45°</td><td>√2/2</td><td>√2/2</td><td>1</td></tr><tr><td>60°</td><td>√3/2</td><td>1/2</td><td>√3</td></tr><tr><td>90°</td><td>1</td><td>0</td><td>undefined</td></tr></tbody></table></div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                <li>Enter the angle and unit — all 6 ratios appear in the result.</li>
                <li>For the inverse, choose a ratio value (for sin/cos between -1 and 1) and the function.</li>
                <li>The table of standard angles is given below — useful for memorising.</li>
            </ol>
            <p class="small text-muted mb-0">Note: tan is undefined at 90 degrees, so you will see undefined instead of a very large value. The degrees/radians toggle works for both sections.</p>
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


function trSafe(v) { return Math.abs(v) > 1e10 ? "undefined" : fmt(v, 6); }
function trCalc() {
    var ang = num("trAng"); var unit = txt("trUnit");
    if (ang === null) { setT("trRes", "Enter the angle."); }
    else {
        var rad = unit === "deg" ? ang * Math.PI / 180 : ang;
        var deg = unit === "deg" ? ang : ang * 180 / Math.PI;
        var s = Math.sin(rad), co = Math.cos(rad), ta = Math.tan(rad);
        setT("trRes", "Angle: " + fmt(deg, 4) + "° = " + fmt(rad, 6) + " rad | sin = " + fmt(s, 6) + " | cos = " + fmt(co, 6) + " | tan = " + trSafe(ta) + " | csc = " + trSafe(1 / s) + " | sec = " + trSafe(1 / co) + " | cot = " + trSafe(1 / ta));
    }
    var v = num("trVal"); var fn = txt("trFn"); var unit2 = txt("trUnit");
    if (v === null) { setT("trInv", "Enter a ratio value for the inverse."); return; }
    var out;
    if (fn === "asin") { if (v < -1 || v > 1) { setT("trInv", "For sin inverse, the value must be between -1 and 1."); return; } out = Math.asin(v); }
    else if (fn === "acos") { if (v < -1 || v > 1) { setT("trInv", "For cos inverse, the value must be between -1 and 1."); return; } out = Math.acos(v); }
    else { out = Math.atan(v); }
    setT("trInv", "Angle = " + fmt(out * 180 / Math.PI, 4) + "° = " + fmt(out, 6) + " rad");
}
bind(["trAng","trUnit","trVal","trFn"], trCalc); trCalc();

})();
</script>
@endsection
