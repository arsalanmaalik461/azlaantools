@extends('layouts.app')

@section('title', 'Geometric Mean Calculator — Free Online Tool')
@section('meta_description', 'Find the geometric mean of numbers, useful for growth rate calculations')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Geometric Mean Calculator</h1>
            <p class="lead small text-muted">Enter numbers separated by commas (all must be positive) — get the geometric mean with product and nth root steps.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">                    <div class="col-md-8"><label class="form-label" for="gmNums">Numbers (comma separated)</label><input type="text" class="form-control inp" id="gmNums" placeholder="e.g. 2, 8, 32" step="any"></div></div>
                    <div id="result" class="mt-3"></div>
                    <div id="steps" class="mt-3"></div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Enter all numbers separated by commas.</li><li>Steps show the product and then its nth root.</li></ol>
                    <p class="small text-muted mb-0">Note: Geometric mean is the right average for growth rates and ratios. It is not defined for negative or zero values.</p>
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
        var raw = el("gmNums").value.trim(); if (raw === "") { err("Enter numbers, for example 2, 8, 32."); return; }
        var xs = raw.split(",").map(function (x) { return parseFloat(x); });
        if (xs.some(function (x) { return isNaN(x) || x <= 0; })) { err("All numbers must be positive."); return; }
        var logSum = xs.reduce(function (s, x) { return s + Math.log(x); }, 0);
        var gm = Math.exp(logSum / xs.length); var prod = xs.reduce(function (s, x) { return s * x; }, 1);
        show("<div class=\"alert alert-success mb-0 fs-5\"><strong>Geometric Mean = " + fmt(gm, 6) + "</strong></div>");
        showSteps(["Count of numbers n = " + xs.length, "Product = " + xs.join(" x ") + " = " + (isFinite(prod) ? fmt(prod, 6) : "too large (log method used)"), "GM = (product)^(1/" + xs.length + ") = " + fmt(gm, 6)]);
    }
    bind(calc); calc();

})();
</script>
@endsection
