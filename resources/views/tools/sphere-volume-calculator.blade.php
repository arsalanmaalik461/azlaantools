@extends('layouts.app')

@section('title', 'Sphere Volume Calculator — Free Online Tool')
@section('meta_description', 'Calculate the volume and surface area of a sphere from its radius')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Sphere Volume Calculator</h1>
            <p class="lead small text-muted">Enter the radius of your sphere — get volume, surface area, diameter and circumference together.</p>
<div class="col-md-4 mb-3"><label class="form-label" for="spR">Radius (r)</label><input type="number" step="any" class="form-control" id="spR" placeholder="e.g. 5"></div><div class="alert alert-secondary mt-3 mb-0" id="spRes">Enter values — the result will show here live.</div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                <li>Enter the radius (if you know the diameter, half of it is the radius).</li>
                <li>Volume and surface area will appear in the result.</li>
            </ol>
            <p class="small text-muted mb-0">Note: Formulas: Volume = (4/3) pi r³, Surface area = 4 pi r². The result will use the same unit as your radius.</p>
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


function spCalc() {
    var r = num("spR");
    if (r === null || r <= 0) { setT("spRes", "Please enter a positive radius."); return; }
    setT("spRes", "Volume: " + fmt(4 / 3 * Math.PI * Math.pow(r, 3), 4) + " cubic units | Surface area: " + fmt(4 * Math.PI * r * r, 4) + " sq units | Diameter: " + fmt(2 * r, 4) + " | Circumference (great circle): " + fmt(2 * Math.PI * r, 4));
}
bind(["spR"], spCalc); spCalc();

})();
</script>
@endsection
