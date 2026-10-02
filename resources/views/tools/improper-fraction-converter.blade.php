@extends('layouts.app')

@section('title', 'Improper Fraction Converter — Free Online Tool')
@section('meta_description', 'Convert improper fractions to mixed numbers and mixed numbers to improper fractions')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Improper Fraction Converter</h1>
            <p class="lead small text-muted">Both conversions on one page: improper to mixed at the top, mixed number to improper below — with steps.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h6">A. Improper → Mixed Number</h2>
                    <div class="row g-3">                    <div class="col-md-4"><label class="form-label" for="imN1">Numerator</label><input type="number" class="form-control inp" id="imN1" placeholder="e.g. 17" step="any"></div>                    <div class="col-md-4"><label class="form-label" for="imD1">Denominator</label><input type="number" class="form-control inp" id="imD1" placeholder="e.g. 5" step="any"></div></div>
                    <div id="resA" class="mt-2 fw-semibold"></div>
                    <hr>
                    <h2 class="h6">B. Mixed Number → Improper</h2>
                    <div class="row g-3">                    <div class="col-md-4"><label class="form-label" for="imW">Whole number</label><input type="number" class="form-control inp" id="imW" placeholder="e.g. 3" step="any"></div>                    <div class="col-md-4"><label class="form-label" for="imN2">Numerator</label><input type="number" class="form-control inp" id="imN2" placeholder="e.g. 2" step="any"></div>                    <div class="col-md-4"><label class="form-label" for="imD2">Denominator</label><input type="number" class="form-control inp" id="imD2" placeholder="e.g. 5" step="any"></div></div>
                    <div id="resB" class="mt-2 fw-semibold"></div>
                    <div id="result" class="mt-3"></div>
                    <div id="steps" class="mt-3"></div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Enter the numbers for the conversion you need.</li><li>Both results update live as you type.</li></ol>
                    <p class="small text-muted mb-0">Note: In an improper fraction the numerator is bigger than or equal to the denominator. Mixed number = whole number + proper fraction.</p>
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
        var n1 = intv("imN1"), d1 = intv("imD1"), w = intv("imW"), n2 = intv("imN2"), d2 = intv("imD2");
        var outA = el("resA"), outB = el("resB");
        if (n1 !== null && d1 !== null && d1 !== 0) { var sgn = (n1 < 0) !== (d1 < 0) ? "-" : ""; var an = Math.abs(n1), ad = Math.abs(d1); var whole = Math.floor(an / ad), rem = an % ad; outA.textContent = n1 + "/" + d1 + " = " + sgn + whole + " " + rem + "/" + ad + "  (steps: " + an + " ÷ " + ad + " = " + whole + ", remainder " + rem + ")"; }
        else { outA.textContent = d1 === 0 ? "Denominator cannot be 0." : ""; }
        if (w !== null && n2 !== null && d2 !== null && d2 !== 0) { var imp = Math.abs(w) * Math.abs(d2) + Math.abs(n2); var sg = w < 0 ? "-" : ""; outB.textContent = w + " " + n2 + "/" + d2 + " = " + sg + imp + "/" + Math.abs(d2) + "  (steps: " + Math.abs(w) + " x " + Math.abs(d2) + " + " + Math.abs(n2) + " = " + imp + ")"; }
        else { outB.textContent = d2 === 0 ? "Denominator cannot be 0." : ""; }
        show(""); showSteps([]);
    }
    bind(calc); calc();

})();
</script>
@endsection
