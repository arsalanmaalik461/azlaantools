@extends('layouts.app')

@section('title', 'Fraction Simplifier — Free Online Tool')
@section('meta_description', 'Simplify a fraction to its lowest terms with GCD steps.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Fraction Simplifier</h1>
            <p class="lead small text-muted">Write the numerator and denominator — get the simplified fraction with GCD Euclidean steps.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">                    <div class="col-md-4"><label class="form-label" for="fsNum">Numerator</label><input type="number" class="form-control inp" id="fsNum" placeholder="e.g. 24" step="any"></div>                    <div class="col-md-4"><label class="form-label" for="fsDen">Denominator</label><input type="number" class="form-control inp" id="fsDen" placeholder="e.g. 36" step="any"></div></div>
                    <div id="result" class="mt-3"></div>
                    <div id="steps" class="mt-3"></div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Enter the numerator and denominator.</li><li>See the GCD steps and the simplified result; an improper fraction also shows the mixed number.</li></ol>
                    <p class="small text-muted mb-0">Note: The GCD is found with the Euclidean algorithm: keep dividing the big number by the small number and taking the remainder until the remainder is 0.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    "use strict";
    var el = function (id) { return document.getElementById(id); };
    function fmt(n, d) { if (typeof d === "undefined") { d = 6; } if (!isFinite(n)) { return "—"; } return Number(n.toFixed(d)).toLocaleString("en-US", { maximumFractionDigits: d }); }
    function num(id) { var v = parseFloat(el(id).value); return isNaN(v) ? null : v; }
    function intv(id) { var v = parseInt(el(id).value, 10); return isNaN(v) ? null : v; }
    function gcd(a, b) { a = Math.abs(a); b = Math.abs(b); while (b) { var t = b; b = a % b; a = t; } return a || 1; }
    function show(html) { el("result").innerHTML = html; }
    function showSteps(arr) { el("steps").innerHTML = arr.length ? "<h2 class=\"h6\">Steps / Breakdown</h2><ol>" + arr.map(function (s) { return "<li>" + s + "</li>"; }).join("") + "</ol>" : ""; }
    function err(msg) { show("<div class=\"alert alert-warning mb-0\">" + msg + "</div>"); showSteps([]); }
    function bind(fn) { document.querySelectorAll(".inp").forEach(function (i) { i.addEventListener("input", fn); i.addEventListener("change", fn); }); }

    function calc() {
        var n = intv("fsNum"), d = intv("fsDen");
        if (n === null || d === null) { err("Write both the numerator and denominator as whole numbers."); return; }
        if (d === 0) { err("The denominator cannot be 0."); return; }
        var st = []; var a = Math.abs(n), b = Math.abs(d);
        st.push("GCD(" + a + ", " + b + ") Euclidean steps:");
        while (b) { st.push(a + " = " + b + " x " + Math.floor(a / b) + " + " + (a % b)); var t = b; b = a % b; a = t; }
        var g = a || 1; st.push("GCD = " + g);
        var sign = (n < 0) !== (d < 0) ? "-" : ""; var n2 = Math.abs(n) / g, d2 = Math.abs(d) / g;
        st.push("Divide both by " + g + ": " + n + "/" + d + " = " + sign + n2 + "/" + d2);
        var mixed = ""; if (d2 !== 1 && n2 > d2) { mixed = " &nbsp;|&nbsp; Mixed number = <strong>" + sign + Math.floor(n2 / d2) + " " + (n2 % d2) + "/" + d2 + "</strong>"; st.push("Mixed number: " + sign + Math.floor(n2 / d2) + " " + (n2 % d2) + "/" + d2); }
        show("<div class=\"alert alert-success mb-0 fs-5\"><strong>" + n + "/" + d + " = " + sign + n2 + "/" + d2 + "</strong>" + mixed + "</div>"); showSteps(st);
    }
    bind(calc); calc();

})();
</script>
@endsection
