@extends('layouts.app')

@section('title', 'Square Root Calculator — Free Online Tool')
@section('meta_description', 'Find the square root and get the simplified radical form with steps')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Square Root Calculator</h1>
            <p class="lead small text-muted">Enter a number — get the decimal value of its square root, whether it is a perfect square, and the simplified radical form (like √50 = 5√2) with steps.</p>
<div class="col-md-4 mb-3"><label class="form-label" for="srX">Number</label><input type="number" step="any" class="form-control" id="srX" placeholder="e.g. 50"></div><div class="alert alert-secondary mt-3 mb-0" id="srRes">Enter values — the result will show here live.</div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                <li> Enter the number you need the square root of.</li>
                <li>The result shows the decimal value and the simplified radical form.</li>
                <li>Nearby perfect squares are also shown to help you estimate.</li>
            </ol>
            <p class="small text-muted mb-0">Note: A negative number has no square root in real numbers (it is imaginary). In simplification, the largest perfect-square factor is pulled out.</p>
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


function srCalc() {
    var x = num("srX");
    if (x === null) { setT("srRes", "Enter a number."); return; }
    if (x < 0) { setT("srRes", "A negative number has no real square root."); return; }
    var r = Math.sqrt(x);
    var msg = "Square root: " + fmt(r, 8);
    if (Math.floor(x) === x) {
        var coeff = 1, rest = x;
        for (var p = 2; p * p <= rest; p++) { while (rest % (p * p) === 0) { rest = rest / (p * p); coeff = coeff * p; } }
        if (rest === 1) { msg += " | Perfect square: √" + x + " = " + coeff; }
        else if (coeff > 1) { msg += " | Simplified: √" + x + " = √(" + (coeff * coeff) + " x " + rest + ") = " + coeff + "√" + rest; }
        else { msg += " | √" + x + " is already in simplest form (no perfect-square factor)."; }
        var lo = Math.floor(r);
        msg += " | Nearby perfect squares: " + lo + "² = " + (lo * lo) + " and " + (lo + 1) + "² = " + ((lo + 1) * (lo + 1));
    }
    msg += " | Check: " + fmt(r, 4) + "² ≈ " + fmt(r * r, 4);
    setT("srRes", msg);
}
bind(["srX"], srCalc); srCalc();

})();
</script>
@endsection
