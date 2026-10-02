@extends('layouts.app')

@section('title', 'Root Calculator — Free Online Tool')
@section('meta_description', 'Find any nth root — cube root, fourth root and more — in one place')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Root Calculator</h1>
            <p class="lead small text-muted">Enter the number and the root degree — square root (2), cube root (3), fourth root (4) or any nth root, with a decimal result and a check.</p>
<div class="row"><div class="col-md-4 mb-3"><label class="form-label" for="rcX">Number (x)</label><input type="number" step="any" class="form-control" id="rcX" placeholder="e.g. 81"></div><div class="col-md-4 mb-3"><label class="form-label" for="rcN">Root degree (n)</label><input type="number" step="1" class="form-control" id="rcN" placeholder="e.g. 4"></div></div><div class="alert alert-secondary mt-3 mb-0" id="rcRes">Enter values — the result updates live here.</div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                <li>Enter the number whose root you need.</li>
                <li>Enter the degree: 2 = square root, 3 = cube root, 4 = fourth root, and so on.</li>
                <li>The result also includes a check (the result raised to the power n).</li>
            </ol>
            <p class="small text-muted mb-0">Note: the root of a negative number is only real for an odd degree (3, 5, 7...); for an even degree there is no real result.</p>
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


function rcCalc() {
    var x = num("rcX"), n = num("rcN");
    if (x === null || n === null) { setT("rcRes", "Enter both the number and the degree."); return; }
    if (Math.floor(n) !== n || n < 1) { setT("rcRes", "The degree must be a whole number of 1 or more."); return; }
    if (x < 0 && n % 2 === 0) { setT("rcRes", "A negative number has no real even root."); return; }
    var r = x < 0 ? -Math.pow(-x, 1 / n) : Math.pow(x, 1 / n);
    setT("rcRes", n + "-th root of " + fmt(x, 4) + " = " + fmt(r, 8) + " | Check: " + fmt(r, 4) + "^" + n + " ≈ " + fmt(Math.pow(r, n), 4));
}
bind(["rcX","rcN"], rcCalc); rcCalc();

})();
</script>
@endsection
