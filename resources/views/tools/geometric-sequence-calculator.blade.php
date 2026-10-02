@extends('layouts.app')

@section('title', 'Geometric Sequence Calculator — Free Online Tool')
@section('meta_description', 'Calculate the nth term of a geometric sequence using the common ratio')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Geometric Sequence Calculator</h1>
            <p class="lead small text-muted">Enter the first term (a), common ratio (r) and term number (n) — get the nth term, the first n terms and the sum of terms.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">                    <div class="col-md-4"><label class="form-label" for="gsA">First term (a)</label><input type="number" class="form-control inp" id="gsA" placeholder="" step="any"></div>                    <div class="col-md-4"><label class="form-label" for="gsR">Common ratio (r)</label><input type="number" class="form-control inp" id="gsR" placeholder="" step="any"></div>                    <div class="col-md-4"><label class="form-label" for="gsN">n (term number)</label><input type="number" class="form-control inp" id="gsN" placeholder="e.g. 8" step="1"></div></div>
                    <div id="result" class="mt-3"></div>
                    <div id="steps" class="mt-3"></div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Enter a, r and n.</li><li>nth term = a x r^(n-1) will show with steps.</li></ol>
                    <p class="small text-muted mb-0">Note: If r = 1, all terms are equal and sum = n x a.</p>
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
        var a = num("gsA"), r = num("gsR"), n = intv("gsN");
        if (a === null || r === null || n === null || n < 1 || n > 200) { err("Enter a, r and n (1-200) correctly."); return; }
        var terms = []; for (var i = 0; i < Math.min(n, 50); i++) { terms.push(a * Math.pow(r, i)); }
        var nth = a * Math.pow(r, n - 1);
        var sum = r === 1 ? a * n : a * (Math.pow(r, n) - 1) / (r - 1);
        show("<div class=\"alert alert-success mb-0\">nth term (term " + n + ") = <strong>" + fmt(nth, 6) + "</strong><br>Sum of first " + n + " terms = <strong>" + fmt(sum, 6) + "</strong><div class=\"mt-2 small\">Sequence: " + terms.map(function (t) { return fmt(t, 4); }).join(", ") + (n > 50 ? ", ..." : "") + "</div></div>");
        showSteps(["nth term formula: aₙ = a x r^(n-1) = " + fmt(a, 4) + " x " + fmt(r, 4) + "^" + (n - 1) + " = " + fmt(nth, 6), "Sum formula: Sₙ = a(rⁿ − 1)/(r − 1) = " + fmt(sum, 6)]);
    }
    bind(calc); calc();

})();
</script>
@endsection
