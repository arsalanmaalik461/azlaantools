@extends('layouts.app')

@section('title', 'Polynomial Factoring Calculator — Free Online Tool')
@section('meta_description', 'Break polynomial and trinomial expressions into factors.')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Polynomial Factoring Calculator</h1>
            <p class="lead small text-muted">Enter the coefficients of the quadratic trinomial ax² + bx + c — you will get the roots and factored form with steps. (This tool is for quadratic, i.e. degree-2, polynomials.)</p>
<div class="row"><div class="col-md-4 mb-3"><label class="form-label" for="pfA">a (x² ka coefficient)</label><input type="number" step="any" class="form-control" id="pfA" placeholder="e.g. 1"></div><div class="col-md-4 mb-3"><label class="form-label" for="pfB">b (x ka coefficient)</label><input type="number" step="any" class="form-control" id="pfB" placeholder="e.g. 5"></div><div class="col-md-4 mb-3"><label class="form-label" for="pfC">c (constant)</label><input type="number" step="any" class="form-control" id="pfC" placeholder="e.g. 6"></div></div><div class="alert alert-secondary mt-3 mb-0" id="pfRes">Enter values — the result will show live here.</div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                <li>Write the expression in the form ax² + bx + c and enter a, b, c. For example, for x² + 5x + 6: a=1, b=5, c=6.</li>
                <li>The factored form and both roots will appear in the result; the middle step (two numbers whose sum is b and product is a x c) will also be shown.</li>
            </ol>
            <p class="small text-muted mb-0">Note: Discriminant D = b² - 4ac. If D &lt; 0, real factors are not formed (roots are complex). If a = 0, the expression is no longer quadratic.</p>
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


function pfCalc() {
    var a = num("pfA"), b = num("pfB"), c = num("pfC");
    if (a === null || b === null || c === null) { setT("pfRes", "Please enter all three: a, b and c."); return; }
    if (a === 0) { setT("pfRes", "a is zero — this is not quadratic, it is a linear expression (bx + c)."); return; }
    var D = b * b - 4 * a * c;
    if (D < 0) { setT("pfRes", "Discriminant D = " + fmt(D, 4) + " (negative) — no factors in real numbers; the roots are complex."); return; }
    var sq = Math.sqrt(D); var r1 = (-b + sq) / (2 * a); var r2 = (-b - sq) / (2 * a);
    var mid = ""; var found = null;
    for (var p = -1000; p <= 1000 && !found; p++) { if (p === 0) { continue; } if ((a * c) % p === 0) { var q = (a * c) / p; if (p + q === b) { found = [p, q]; } } }
    if (found) { mid = " | Middle term split: " + found[0] + " and " + found[1] + " (sum = b, product = a x c = " + fmt(a * c, 0) + ")"; }
    function term(r) { return r >= 0 ? "- " + fmt(r, 4) : "+ " + fmt(-r, 4); }
    var factForm;
    if (Math.abs(r1 - r2) < 1e-12) { factForm = "(x " + term(r1) + ")²"; }
    else { factForm = "(x " + term(r1) + ")(x " + term(r2) + ")"; }
    if (a !== 1) { factForm = a + factForm; }
    setT("pfRes", "Discriminant D = " + fmt(D, 4) + " | Roots: x = " + fmt(r1, 4) + ", x = " + fmt(r2, 4) + " | Factored form: " + factForm + mid);
}
bind(["pfA","pfB","pfC"], pfCalc); pfCalc();

})();
</script>
@endsection
