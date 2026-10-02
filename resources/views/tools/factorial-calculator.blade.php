@extends('layouts.app')

@section('title', 'Factorial Calculator — Free Online Tool')
@section('meta_description', 'Calculate the factorial n! of any number, step by step.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Factorial Calculator</h1>
            <p class="lead small text-muted">Enter a number (0 to 170) — you will see the full multiplication chain and the result.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">                    <div class="col-md-4"><label class="form-label" for="faN">n (0 - 170)</label><input type="number" class="form-control inp" id="faN" placeholder="e.g. 5" step="1"></div></div>
                    <div id="result" class="mt-3"></div>
                    <div id="steps" class="mt-3"></div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Enter a whole number between 0 and 170.</li><li>See the expansion (n x (n-1) x ... x 1) and the final result.</li></ol>
                    <p class="small text-muted mb-0">Note: 0! = 1 by definition. Factorials larger than 170 go beyond the normal number range, so the limit is 170.</p>
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
        var n = intv("faN"); if (n === null || n < 0 || n > 170) { err("Enter a whole number n between 0 and 170."); return; }
        var r = 1; for (var i = 2; i <= n; i++) { r *= i; }
        var parts = []; for (var j = n; j >= 1; j--) { parts.push(j); }
        var exp = n <= 1 ? "1" : parts.join(" x ");
        if (parts.length > 25) { exp = parts.slice(0, 12).join(" x ") + " x ... x " + parts.slice(-5).join(" x "); }
        show("<div class=\"alert alert-success mb-0 fs-5\"><strong>" + n + "! = " + r.toLocaleString("en-US", { maximumFractionDigits: 0 }) + (r >= 1e21 ? " (" + r.toExponential(4) + ")" : "") + "</strong></div>");
        showSteps([n + "! = " + exp, "= " + r.toLocaleString("en-US", { maximumFractionDigits: 0 })]);
    }
    bind(calc); calc();

})();
</script>
@endsection
