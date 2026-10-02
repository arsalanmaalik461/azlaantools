@extends('layouts.app')

@section('title', 'Rounding Calculator — Free Online Tool')
@section('meta_description', 'Round any number to decimal places, tens or hundreds')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Rounding Calculator</h1>
            <p class="lead small text-muted">Enter a number and choose the rounding level — from nearest thousand down to 4 decimal places. The rounded result and the difference from the original will appear.</p>
<div class="row"><div class="col-md-4 mb-3"><label class="form-label" for="rdX">Number</label><input type="number" step="any" class="form-control" id="rdX" placeholder="e.g. 1234.5678"></div><div class="col-md-4 mb-3"><label class="form-label" for="rdTo">Round to</label><select class="form-select" id="rdTo"><option value="1000">Nearest thousand (1000)</option><option value="100">Nearest hundred (100)</option><option value="10">Nearest ten (10)</option><option value="1" selected>Nearest whole number (1)</option><option value="0.1">1 decimal place (0.1)</option><option value="0.01">2 decimal places (0.01)</option><option value="0.001">3 decimal places (0.001)</option><option value="0.0001">4 decimal places (0.0001)</option></select></div></div><div class="alert alert-secondary mt-3 mb-0" id="rdRes">Enter values — the result appears here live.</div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                <li>Enter a number.</li>
                <li>Choose the rounding level from the Round to list.</li>
                <li>The rounded result and the difference from the original appear.</li>
            </ol>
            <p class="small text-muted mb-0">Note: The standard half-up rule is used: 5 or above rounds up, below that rounds down. The same rule applies to negative numbers (the Math.round method).</p>
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


function rdCalc() {
    var x = num("rdX"); var unit = parseFloat(txt("rdTo"));
    if (x === null) { setT("rdRes", "Please enter a number."); return; }
    var r = Math.round(x / unit) * unit;
    // avoid floating dust
    r = parseFloat(r.toFixed(10));
    setT("rdRes", "Rounded: " + fmt(r, unit < 1 ? Math.round(-Math.log10(unit)) : 0) + " | Original: " + fmt(x, 6) + " | Difference: " + fmt(r - x, 6));
}
bind(["rdX","rdTo"], rdCalc); rdCalc();

})();
</script>
@endsection
