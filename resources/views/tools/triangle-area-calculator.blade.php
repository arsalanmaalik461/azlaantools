@extends('layouts.app')

@section('title', 'Triangle Area Calculator — Free Online Tool')
@section('meta_description', 'Find the area of a triangle from base and height, three sides, or two sides and an angle')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Triangle Area Calculator</h1>
            <p class="lead small text-muted">Choose the method that matches your data: (1) base and height, (2) all three sides (Heron formula), (3) two sides and the included angle between them. You can give the angle in degrees or radians.</p>
<div class="mb-3"><label class="form-label" for="taMode">Method</label><select class="form-select" id="taMode"><option value="bh">Base and height</option><option value="heron">Three sides (Heron)</option><option value="sas">Two sides and included angle</option></select></div><div class="row"><div class="col-md-4 mb-3"><label class="form-label" for="taA">Value 1 — base / side a / side a</label><input type="number" step="any" class="form-control" id="taA" placeholder="e.g. 10"></div><div class="col-md-4 mb-3"><label class="form-label" for="taB">Value 2 — height / side b / side b</label><input type="number" step="any" class="form-control" id="taB" placeholder="e.g. 6"></div><div class="col-md-4 mb-3"><label class="form-label" for="taC">Value 3 — side c / included angle</label><input type="number" step="any" class="form-control" id="taC" placeholder="e.g. 8"></div><div class="col-md-4 mb-3"><label class="form-label" for="taUnit">Angle unit (for SAS only)</label><select class="form-select" id="taUnit"><option value="deg">Degrees</option><option value="rad">Radians</option></select></div></div><div class="alert alert-secondary mt-3 mb-0" id="taRes">Enter values — the result shows live here.</div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                <li>Choose a method above.</li>
                <li>Base-height: Value 1 = base, Value 2 = height.</li>
                <li>Heron: enter the three sides in Value 1, 2 and 3.</li>
                <li>SAS: Value 1 and 2 = two sides, Value 3 = the angle between them.</li>
            </ol>
            <p class="small text-muted mb-0">Note: Heron formula: s = (a+b+c)/2, Area = square root of (s(s-a)(s-b)(s-c)). The three sides must follow this rule: the sum of any two must be greater than the third (triangle inequality), otherwise no triangle exists.</p>
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


function taCalc() {
    var mode = txt("taMode"); var a = num("taA"), b = num("taB"), c = num("taC");
    if (a === null || b === null || a <= 0 || b <= 0) { setT("taRes", "Enter the first two values (positive)."); return; }
    if (mode === "bh") { setT("taRes", "Area = (base x height) / 2 = " + fmt(a * b / 2, 4)); return; }
    if (mode === "heron") {
        if (c === null || c <= 0) { setT("taRes", "Also enter the third side."); return; }
        if (a + b <= c || a + c <= b || b + c <= a) { setT("taRes", "These sides cannot form a triangle — the sum of any two sides must be greater than the third."); return; }
        var s = (a + b + c) / 2; var area = Math.sqrt(s * (s - a) * (s - b) * (s - c));
        setT("taRes", "Semi-perimeter s = " + fmt(s, 4) + " | Area (Heron) = " + fmt(area, 4) + " | Perimeter = " + fmt(a + b + c, 4));
        return;
    }
    if (c === null) { setT("taRes", "Also enter the included angle."); return; }
    var rad = txt("taUnit") === "deg" ? c * Math.PI / 180 : c;
    var area2 = 0.5 * a * b * Math.sin(rad);
    var third = Math.sqrt(a * a + b * b - 2 * a * b * Math.cos(rad));
    setT("taRes", "Area = (1/2) a b sin(angle) = " + fmt(area2, 4) + " | Third side (law of cosines) = " + fmt(third, 4) + " | Perimeter = " + fmt(a + b + third, 4));
}
bind(["taMode","taA","taB","taC","taUnit"], taCalc); taCalc();

})();
</script>
@endsection
