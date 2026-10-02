@extends('layouts.app')

@section('title', 'Pyramid Volume Calculator — Free Online Tool')
@section('meta_description', 'Find the volume and surface area of a square or rectangular pyramid')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Pyramid Volume Calculator</h1>
            <p class="lead small text-muted">Enter the pyramid base and height. Diagram help: the base is the square/rectangle at the bottom, height is the straight distance from the apex (top point) to the center of the base.</p>
<div class="row"><div class="col-md-4 mb-3"><label class="form-label" for="pyType">Base type</label><select class="form-select" id="pyType"><option value="square">Square base</option><option value="rect">Rectangular base</option></select></div><div class="col-md-4 mb-3"><label class="form-label" for="pyL">Base length</label><input type="number" step="any" class="form-control" id="pyL" placeholder="e.g. 6"></div><div class="col-md-4 mb-3"><label class="form-label" for="pyW">Base width (for rectangular)</label><input type="number" step="any" class="form-control" id="pyW" placeholder="e.g. 4"></div><div class="col-md-4 mb-3"><label class="form-label" for="pyH">Height</label><input type="number" step="any" class="form-control" id="pyH" placeholder="e.g. 5"></div></div><div class="alert alert-secondary mt-3 mb-0" id="pyRes">Enter values — the result will show here live.</div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                <li>Choose the base type: square or rectangular.</li>
                <li>For square, enter length and height; for rectangle, enter length, width and height.</li>
                <li>You will get the volume, slant height and total surface area.</li>
            </ol>
            <p class="small text-muted mb-0">Note: Volume: V = (1/3) x base area x height. Surface area includes the base and all triangular faces; slant height is found with Pythagoras.</p>
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


function pyCalc() {
    var type = txt("pyType"); var L = num("pyL"), W = num("pyW"), H = num("pyH");
    if (L === null || H === null || L <= 0 || H <= 0) { setT("pyRes", "Please enter base length and height (positive numbers)."); return; }
    if (type === "rect" && (W === null || W <= 0)) { setT("pyRes", "For a rectangular base, please also enter the width."); return; }
    var w = type === "square" ? L : W;
    var baseArea = L * w; var vol = baseArea * H / 3;
    var slantW = Math.sqrt(Math.pow(w / 2, 2) + H * H);
    var slantL = Math.sqrt(Math.pow(L / 2, 2) + H * H);
    var sa = baseArea + L * slantW + w * slantL;
    setT("pyRes", "Base area: " + fmt(baseArea, 4) + " | Volume: " + fmt(vol, 4) + " | Slant height (width side): " + fmt(slantW, 4) + " | Slant height (length side): " + fmt(slantL, 4) + " | Total surface area: " + fmt(sa, 4));
}
bind(["pyType","pyL","pyW","pyH"], pyCalc); pyCalc();

})();
</script>
@endsection
