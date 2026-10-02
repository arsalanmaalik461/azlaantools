@extends('layouts.app')

@section('title', 'Reverse Percentage Calculator — Free Online Tool')
@section('meta_description', 'Find the original price from a price after a percent increase or decrease')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Reverse Percentage Calculator</h1>
            <p class="lead small text-muted">Enter the final price (after the percent change) and the percent — the original price will be found. Great for finding the original of discounted or taxed prices.</p>
<div class="row"><div class="col-md-4 mb-3"><label class="form-label" for="rvMode">Type of change</label><select class="form-select" id="rvMode"><option value="increase">Increase</option><option value="decrease">Decrease</option></select></div><div class="col-md-4 mb-3"><label class="form-label" for="rvFinal">Final value (after change)</label><input type="number" step="any" class="form-control" id="rvFinal" placeholder="e.g. 1200"></div><div class="col-md-4 mb-3"><label class="form-label" for="rvPct">Percent (%)</label><input type="number" step="any" class="form-control" id="rvPct" placeholder="e.g. 20"></div></div><div class="alert alert-secondary mt-3 mb-0" id="rvRes">Enter values — result will show here live.</div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                <li>First choose whether the value increased or decreased.</li>
                <li>Enter the final value and percent.</li>
                <li>You will get both the original value and the change amount.</li>
            </ol>
            <p class="small text-muted mb-0">Note: For increase: original = final / (1 + p/100). For decrease: original = final / (1 - p/100). If decrease is 100% or more, the original cannot be found.</p>
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


function rvCalc() {
    var mode = txt("rvMode"); var f = num("rvFinal"); var p = num("rvPct");
    if (f === null || p === null) { setT("rvRes", "Enter the final value and percent."); return; }
    var orig;
    if (mode === "increase") { orig = f / (1 + p / 100); }
    else { if (p >= 100) { setT("rvRes", "If decrease is 100% or more, the original value cannot be found."); return; } orig = f / (1 - p / 100); }
    setT("rvRes", "Original value: " + fmt(orig, 4) + " | Change amount: " + fmt(Math.abs(f - orig), 4) + " | Check: " + fmt(orig, 2) + (mode === "increase" ? " + " : " - ") + fmt(p, 2) + "% = " + fmt(f, 2));
}
bind(["rvMode","rvFinal","rvPct"], rvCalc); rvCalc();

})();
</script>
@endsection
