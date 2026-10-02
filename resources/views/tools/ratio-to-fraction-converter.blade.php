@extends('layouts.app')

@section('title', 'Ratio to Fraction Converter — Free Online Tool')
@section('meta_description', 'Convert a ratio into fraction and percent form for each part.')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Ratio to Fraction Converter</h1>
            <p class="lead small text-muted">Enter both parts of a ratio, like 3 : 5 — you will get the fraction (what share of the total) and percent for each part, and if you enter a total, the actual value of each part too.</p>
<div class="row"><div class="col-md-4 mb-3"><label class="form-label" for="rfA">First part (a)</label><input type="number" step="any" class="form-control" id="rfA" placeholder="e.g. 3"></div><div class="col-md-4 mb-3"><label class="form-label" for="rfB">Second part (b)</label><input type="number" step="any" class="form-control" id="rfB" placeholder="e.g. 5"></div><div class="col-md-4 mb-3"><label class="form-label" for="rfT">Total value (optional)</label><input type="number" step="any" class="form-control" id="rfT" placeholder="e.g. 800"></div></div><div class="alert alert-secondary mt-3 mb-0" id="rfRes">Enter values — the result will show here live.</div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                <li>Enter both parts of the ratio.</li>
                <li>The fraction and percent of each part will appear in the result.</li>
                <li>Enter an optional total to also get the actual value of each part.</li>
            </ol>
            <p class="small text-muted mb-0">Note: fractions are shown in the smallest form (divided by GCD). Both parts cannot be zero.</p>
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


function rfCalc() {
    var a = num("rfA"), b = num("rfB"), t = num("rfT");
    if (a === null || b === null) { setT("rfRes", "Please enter both parts of the ratio."); return; }
    var sum = a + b;
    if (sum === 0) { setT("rfRes", "The sum of both parts is zero — a ratio cannot be made."); return; }
    function gcd(x, y) { x = Math.abs(x); y = Math.abs(y); while (y) { var m = x % y; x = y; y = m; } return x || 1; }
    var whole = (Math.floor(a) === a && Math.floor(b) === b && Math.floor(sum) === sum);
    var simp = whole ? (a / gcd(a, b)) + " : " + (b / gcd(a, b)) : fmt(a, 2) + " : " + fmt(b, 2);
    var fa = whole ? (a / gcd(a, sum)) + "/" + (sum / gcd(a, sum)) : fmt(a / sum, 4);
    var fb = whole ? (b / gcd(b, sum)) + "/" + (sum / gcd(b, sum)) : fmt(b / sum, 4);
    var single = whole ? (a / gcd(a, b)) + "/" + (b / gcd(a, b)) : fmt(a / b, 4);
    var msg = "Simplified ratio: " + simp + " | First part: " + fa + " of total = " + fmt(a / sum * 100, 2) + "% | Second part: " + fb + " of total = " + fmt(b / sum * 100, 2) + "% | Ratio as single fraction a/b: " + single;
    if (t !== null) { msg += " | Of total " + fmt(t, 2) + ", first part = " + fmt(t * a / sum, 2) + ", second part = " + fmt(t * b / sum, 2); }
    setT("rfRes", msg);
}
bind(["rfA","rfB","rfT"], rfCalc); rfCalc();

})();
</script>
@endsection
