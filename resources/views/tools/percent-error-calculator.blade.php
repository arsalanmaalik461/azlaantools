@extends('layouts.app')

@section('title', 'Percent Error Calculator — Free Online Tool')
@section('meta_description', 'Find the percent error between the experimental and actual value.')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Percent Error Calculator</h1>
            <p class="lead small text-muted">Enter your experimental value from the lab and the actual (theoretical) value — get the absolute error and percent error.</p>
<div class="row"><div class="col-md-4 mb-3"><label class="form-label" for="peExp">Experimental value</label><input type="number" step="any" class="form-control" id="peExp" placeholder="e.g. 9.7"></div><div class="col-md-4 mb-3"><label class="form-label" for="peAct">Actual (theoretical) value</label><input type="number" step="any" class="form-control" id="peAct" placeholder="e.g. 9.8"></div></div><div class="alert alert-secondary mt-3 mb-0" id="peRes">Enter the values — the result will show live here.</div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                <li>Write your measured (experimental) value.</li>
                <li>Write the actual value from your book or the standard.</li>
                <li>The result will show both percent error and absolute error.</li>
            </ol>
            <p class="small text-muted mb-0">Note: Formula: percent error = (|experimental - actual| / |actual|) x 100. If the actual value is zero, percent error cannot be found.</p>
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


function peCalc() {
    var e = num("peExp"), a = num("peAct");
    if (e === null || a === null) { setT("peRes", "Please enter both values."); return; }
    if (a === 0) { setT("peRes", "Actual value is zero — percent error cannot be found."); return; }
    var absErr = Math.abs(e - a);
    setT("peRes", "Absolute error: " + fmt(absErr, 6) + " | Percent error: " + fmt(absErr / Math.abs(a) * 100, 4) + "% | Accuracy: " + fmt(100 - absErr / Math.abs(a) * 100, 4) + "%");
}
bind(["peExp","peAct"], peCalc); peCalc();

})();
</script>
@endsection
