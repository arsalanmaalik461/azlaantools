@extends('layouts.app')

@section('title', 'Logarithm Calculator — Free Online Tool')
@section('meta_description', 'Calculate the log of any base with formula and steps')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Logarithm Calculator</h1>
            <p class="lead small text-muted">Enter the value and base — you get the log with change-of-base formula steps, plus ln and log₁₀ too.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">                    <div class="col-md-4"><label class="form-label" for="lgVal">Value (x)</label><input type="number" class="form-control inp" id="lgVal" placeholder="e.g. 1000" step="any"></div>                    <div class="col-md-4"><label class="form-label" for="lgBase">Base (b)</label><input type="number" class="form-control inp" id="lgBase" placeholder="e.g. 10" step="any"></div></div>
                    <div id="result" class="mt-3"></div>
                    <div id="steps" class="mt-3"></div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Value must be positive and base must be positive (not 1).</li><li>The result shows the calculation logᵦ x = ln x / ln b.</li></ol>
                    <p class="small text-muted mb-0">Note: Definition of log: logᵦ x = y means bʸ = x. The base-10 log is usually called log and the base-e log is called ln.</p>
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
        var x = num("lgVal"), b = num("lgBase");
        if (x === null || b === null) { err("Enter both value and base."); return; }
        if (x <= 0) { err("Log is only for positive numbers."); return; }
        if (b <= 0 || b === 1) { err("Base must be positive and cannot be 1."); return; }
        var res = Math.log(x) / Math.log(b);
        show("<div class=\"alert alert-success mb-0\"><strong>log<sub>" + fmt(b, 3) + "</sub>(" + fmt(x, 4) + ") = " + fmt(res, 8) + "</strong><br><span class=\"small\">ln(" + fmt(x, 3) + ") = " + fmt(Math.log(x), 6) + " &nbsp;|&nbsp; log₁₀(" + fmt(x, 3) + ") = " + fmt(Math.log10(x), 6) + " &nbsp;|&nbsp; log₂(" + fmt(x, 3) + ") = " + fmt(Math.log2(x), 6) + "</span></div>");
        showSteps(["Change of base: logᵦ x = ln x / ln b", "= ln(" + fmt(x, 4) + ") / ln(" + fmt(b, 4) + ") = " + fmt(Math.log(x), 6) + " / " + fmt(Math.log(b), 6) + " = " + fmt(res, 8), "Check: " + fmt(b, 3) + "^" + fmt(res, 4) + " ≈ " + fmt(Math.pow(b, res), 4) + " ✓"]);
    }
    bind(calc); calc();

})();
</script>
@endsection
