@extends('layouts.app')

@section('title', 'Long Multiplication Calculator — Free Online Tool')
@section('meta_description', 'Show multiplication of large numbers with every step')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Long Multiplication Calculator</h1>
            <p class="lead small text-muted">Enter two whole numbers — see the partial product for every digit of the second number and their sum, just like the school column method.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">                    <div class="col-md-4"><label class="form-label" for="lmA">First number</label><input type="number" class="form-control inp" id="lmA" placeholder="e.g. 246" step="any"></div>                    <div class="col-md-4"><label class="form-label" for="lmB">Second number</label><input type="number" class="form-control inp" id="lmB" placeholder="e.g. 35" step="any"></div></div>
                    <div id="result" class="mt-3"></div>
                    <div id="steps" class="mt-3"></div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Enter two whole numbers.</li><li>See the list of partial products and the sum of all of them at the end.</li></ol>
                    <p class="small text-muted mb-0">Note: Every partial product comes from one digit of the second number, with zeros added based on its place (tens, hundreds).</p>
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
        var a = intv("lmA"), b = intv("lmB");
        if (a === null || b === null) { err("Enter both whole numbers."); return; }
        var neg = (a < 0) !== (b < 0); var aa = Math.abs(a), bb = Math.abs(b);
        var s = String(bb).split("").reverse(), st = [], parts = [];
        s.forEach(function (dg, i) { var d = parseInt(dg, 10); var p = aa * d * Math.pow(10, i); parts.push(p); st.push(fmt(aa, 0) + " x " + d + (i > 0 ? " (" + Math.pow(10, i) + " place, x " + Math.pow(10, i) + ")" : "") + " = " + fmt(p, 0)); });
        var total = parts.reduce(function (x, y) { return x + y; }, 0);
        st.push("Sum of all partial products: " + parts.map(function (p) { return fmt(p, 0); }).join(" + ") + " = " + fmt(total, 0));
        show("<div class=\"alert alert-success mb-0 fs-5\"><strong>" + a + " x " + b + " = " + (neg && total !== 0 ? "-" : "") + fmt(total, 0) + "</strong></div>");
        showSteps(st);
    }
    bind(calc); calc();

})();
</script>
@endsection
