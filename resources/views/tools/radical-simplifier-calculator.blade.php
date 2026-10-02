@extends('layouts.app')

@section('title', 'Radical Simplifier — Free Online Tool')
@section('meta_description', 'Simplify surd and radical expressions to their simplest form. Free online radical simplifier.')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Radical Simplifier</h1>
            <p class="lead small text-muted">Enter the radicand (the number inside the root) and the index (2 = square root, 3 = cube root) — you will get the simplified form, like root(72) = 6 root(2), with steps.</p>
<div class="row"><div class="col-md-4 mb-3"><label class="form-label" for="rsN">Radicand (number inside)</label><input type="number" step="1" class="form-control" id="rsN" placeholder="e.g. 72"></div><div class="col-md-4 mb-3"><label class="form-label" for="rsK">Index (2 square, 3 cube)</label><input type="number" step="1" class="form-control" id="rsK" placeholder="e.g. 2"></div></div><div class="alert alert-secondary mt-3 mb-0" id="rsRes">Enter values — the result will show live here.</div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                <li>Enter the radicand and index (if the index is empty, 2 is assumed).</li>
                <li>The result shows the part taken outside (coefficient), the part left inside, and the decimal value.</li>
            </ol>
            <p class="small text-muted mb-0">Note: method — the radicand's perfect power factors are taken outside. A negative radicand with an even index has no real result.</p>
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


function rsCalc() {
    var n = num("rsN"); var k = num("rsK");
    if (n === null) { setT("rsRes", "Enter the radicand."); return; }
    if (k === null) { k = 2; }
    if (Math.floor(k) !== k || k < 2) { setT("rsRes", "Index must be a whole number of 2 or more."); return; }
    if (Math.floor(n) !== n) { setT("rsRes", "Enter a whole number (integer) as the radicand here."); return; }
    var neg = n < 0;
    if (neg && k % 2 === 0) { setT("rsRes", "A negative number with an even index (like square root) has no real result."); return; }
    var m = Math.abs(n); var coeff = 1; var rest = m;
    for (var p = 2; Math.pow(p, k) <= rest; p++) { var pw = Math.pow(p, k); while (rest % pw === 0) { rest = rest / pw; coeff = coeff * p; } }
    var dec = neg ? -Math.pow(m, 1 / k) : Math.pow(m, 1 / k);
    var sign = neg ? "-" : "";
    var rootSym = k === 2 ? "sqrt" : "root[" + k + "]";
    var simple;
    if (rest === 1) { simple = sign + coeff; }
    else if (coeff === 1) { simple = sign + rootSym + "(" + rest + ") — already simple"; }
    else { simple = sign + coeff + " x " + rootSym + "(" + rest + ")"; }
    setT("rsRes", "Simplified form: " + simple + " | Decimal value: " + fmt(dec, 6) + " | Outside part (coefficient): " + (neg ? "-" : "") + coeff + " | Left inside: " + rest);
}
bind(["rsN","rsK"], rsCalc); rsCalc();

})();
</script>
@endsection
