@extends('layouts.app')

@section('title', 'Fraction to Percent Converter — Free Online Tool')
@section('meta_description', 'Convert a fraction to a percent with the division steps shown.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Fraction to Percent Converter</h1>
            <p class="lead small text-muted">Enter the numerator and denominator — first the division, then multiply by 100 to get the percent answer with steps.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">                    <div class="col-md-4"><label class="form-label" for="fpNum">Numerator</label><input type="number" class="form-control inp" id="fpNum" placeholder="e.g. 45" step="any"></div>                    <div class="col-md-4"><label class="form-label" for="fpDen">Denominator (total)</label><input type="number" class="form-control inp" id="fpDen" placeholder="e.g. 60" step="any"></div></div>
                    <div id="result" class="mt-3"></div>
                    <div id="steps" class="mt-3"></div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Enter the numerator (marks or part).</li><li>Enter the denominator (total).</li><li>Percent = (numerator ÷ denominator) x 100.</li></ol>
                    <p class="small text-muted mb-0">Note: Percent means how much out of every 100. That is why it is the most common conversion in marks questions.</p>
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
        var n = num("fpNum"), d = num("fpDen");
        if (n === null || d === null) { err("Enter both numbers."); return; }
        if (d === 0) { err("Denominator cannot be 0."); return; }
        var dec = n / d, pct = dec * 100;
        show("<div class=\"alert alert-success mb-0 fs-5\"><strong>" + fmt(n, 4) + "/" + fmt(d, 4) + " = " + fmt(pct, 4) + "%</strong></div>");
        showSteps(["Step 1 (division): " + fmt(n, 4) + " ÷ " + fmt(d, 4) + " = " + fmt(dec, 8), "Step 2 (percent): " + fmt(dec, 8) + " x 100 = " + fmt(pct, 4) + "%"]);
    }
    bind(calc); calc();

})();
</script>
@endsection
