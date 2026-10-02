@extends('layouts.app')

@section('title', 'Synthetic Division Calculator — Free Online Tool')
@section('meta_description', 'Divide a polynomial with synthetic division, with a step table')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Synthetic Division Calculator</h1>
            <p class="lead small text-muted">Enter the root of the divisor (the number you are dividing by) and the coefficients of the polynomial, from the highest power down to the constant — you will get the step-by-step synthetic division table, the quotient and the remainder. Example: to divide x³ - 6x² + 11x - 6 by (x - 1), use root = 1 and coefficients = 1, -6, 11, -6.</p>
<div class="row"><div class="col-md-4 mb-3"><label class="form-label" for="syRoot">Divisor root (c) — divide by (x - c)</label><input type="number" step="any" class="form-control" id="syRoot" placeholder="e.g. 1"></div></div><div class="mb-3"><label class="form-label" for="syCoeff">Coefficients (separate with commas or spaces, from the highest power down to the constant, e.g. 1, -6, 11, -6)</label><input type="text" class="form-control" id="syCoeff" placeholder=""></div><div class="alert alert-secondary mt-3 mb-0" id="syRes">Enter the values — the result will appear here live.</div><div id="syTable" class="mt-3 small"></div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                <li>If the divisor is in the form (x - c), enter c. (x + 2) means c = -2.</li>
                <li>Enter the coefficients for each power, separated by commas — write 0 for any missing power.</li>
                <li>The table below shows the bring-down, multiply and add steps; the last number is the remainder.</li>
            </ol>
            <p class="small text-muted mb-0">Note: Remainder theorem: the remainder is the same as putting x = c into the polynomial, P(c). If the remainder is zero, (x - c) is a factor (factor theorem).</p>
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


function syCalc() {
    var c = num("syRoot"); var coeffs = parseList(txt("syCoeff"));
    var tbl = document.getElementById("syTable");
    if (c === null || coeffs.length < 2) { setT("syRes", "Enter the root (c) and at least 2 coefficients."); tbl.innerHTML = ""; return; }
    if (coeffs.length > 12) { setT("syRes", "Keep the coefficients to 12 or fewer (up to degree 11)."); tbl.innerHTML = ""; return; }
    var row = [coeffs[0]]; var mid = [""]; var steps = [];
    for (var i = 1; i < coeffs.length; i++) { var prod = row[i - 1] * c; mid.push(prod); var nxt = coeffs[i] + prod; row.push(nxt); steps.push("Step " + i + ": " + fmt(row[i - 1], 4) + " x " + fmt(c, 4) + " = " + fmt(prod, 4) + ", then " + fmt(coeffs[i], 4) + " + " + fmt(prod, 4) + " = " + fmt(nxt, 4)); }
    var quot = row.slice(0, row.length - 1); var rem = row[row.length - 1];
    var deg = coeffs.length - 1;
    var qStr = quot.map(function (q, idx) { var power = deg - 1 - idx; var base = power === 0 ? "" : (power === 1 ? "x" : "x^" + power); return fmt(q, 4) + base; }).join(" + ").replace(/\+ -/g, "- ");
    setT("syRes", "Quotient: " + qStr + " | Remainder: " + fmt(rem, 4) + (rem === 0 ? " — the remainder is zero, so (x - " + fmt(c, 4) + ") is a factor." : " | P(" + fmt(c, 4) + ") = " + fmt(rem, 4) + " (remainder theorem)"));
    tbl.innerHTML = "<p class=\"mb-1\"><strong>Top row (coefficients):</strong> " + coeffs.map(function (v) { return fmt(v, 4); }).join(" , ") + "</p><p class=\"mb-1\"><strong>Multiply row (c x previous result):</strong> " + mid.map(function (v) { return v === "" ? "-" : fmt(v, 4); }).join(" , ") + "</p><p class=\"mb-1\"><strong>Bottom row (sum):</strong> " + row.map(function (v) { return fmt(v, 4); }).join(" , ") + "</p><p class=\"mb-0\">" + steps.map(function (s) { return esc(s); }).join("<br>") + "</p>";
}
bind(["syRoot","syCoeff"], syCalc); syCalc();

})();
</script>
@endsection
