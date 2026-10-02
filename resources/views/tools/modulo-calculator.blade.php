@extends('layouts.app')

@section('title', 'Modulo Calculator — Free Online Tool')
@section('meta_description', 'Find the remainder and quotient of a mod operation step by step.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Modulo Calculator</h1>
            <p class="lead small text-muted">Enter two numbers — you will get the remainder and quotient of a mod n, with steps to verify that a = q x n + r.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">                    <div class="col-md-4"><label class="form-label" for="moA">a (dividend)</label><input type="number" class="form-control inp" id="moA" placeholder="e.g. 29" step="any"></div>                    <div class="col-md-4"><label class="form-label" for="moN">n (divisor / modulus)</label><input type="number" class="form-control inp" id="moN" placeholder="e.g. 5" step="any"></div></div>
                    <div id="result" class="mt-3"></div>
                    <div id="steps" class="mt-3"></div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Enter dividend a and modulus n.</li><li>The result is the mathematical modulo — the remainder is always between 0 and n-1.</li></ol>
                    <p class="small text-muted mb-0">Note: In programming languages, the % operator takes the sign of the dividend for negative numbers, but in mathematics the modulo remainder is always non-negative. This tool gives the mathematical result.</p>
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
        var a = num("moA"), n = num("moN");
        if (a === null || n === null) { err("Please enter both numbers."); return; }
        if (n === 0) { err("Modulus cannot be 0."); return; }
        var nn = Math.abs(n); var q = Math.floor(a / nn); var r = a - q * nn;
        show("<div class=\"alert alert-success mb-0 fs-5\"><strong>" + fmt(a, 4) + " mod " + fmt(n, 4) + " = " + fmt(r, 6) + "</strong> &nbsp;|&nbsp; Quotient = " + q + "</div>");
        showSteps(["q = floor(" + fmt(a, 4) + " ÷ " + fmt(nn, 4) + ") = floor(" + fmt(a / nn, 6) + ") = " + q, "r = a − q x n = " + fmt(a, 4) + " − " + q + " x " + fmt(nn, 4) + " = " + fmt(r, 6), "Verification: " + q + " x " + fmt(nn, 4) + " + " + fmt(r, 4) + " = " + fmt(q * nn + r, 4) + " ✓"]);
    }
    bind(calc); calc();

})();
</script>
@endsection
