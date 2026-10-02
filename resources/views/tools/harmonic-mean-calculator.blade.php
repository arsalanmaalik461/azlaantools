@extends('layouts.app')

@section('title', 'Harmonic Mean Calculator — Free Online Tool')
@section('meta_description', 'Calculate the harmonic mean of speeds and rates')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Harmonic Mean Calculator</h1>
            <p class="lead small text-muted">Enter speeds or rates separated by commas — get the harmonic mean with reciprocal steps, which is the correct answer for average speed questions.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">                    <div class="col-md-8"><label class="form-label" for="hmNums">Numbers (comma separated)</label><input type="text" class="form-control inp" id="hmNums" placeholder="e.g. 40, 60" step="any"></div></div>
                    <div id="result" class="mt-3"></div>
                    <div id="steps" class="mt-3"></div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Write all values separated by commas.</li><li>The result will show the n ÷ (sum of reciprocals) calculation.</li></ol>
                    <p class="small text-muted mb-0">Note: The average speed of two speeds over equal distances is not the arithmetic mean, it is the harmonic mean — for example the average speed of 40 and 60 is 48, not 50.</p>
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
        var raw = el("hmNums").value.trim(); if (raw === "") { err("Enter numbers, e.g. 40, 60."); return; }
        var xs = raw.split(",").map(function (x) { return parseFloat(x); });
        if (xs.some(function (x) { return isNaN(x) || x === 0; })) { err("All numbers must be non-zero."); return; }
        var rec = xs.map(function (x) { return 1 / x; }); var s = rec.reduce(function (a, b) { return a + b; }, 0);
        var hm = xs.length / s;
        show("<div class=\"alert alert-success mb-0 fs-5\"><strong>Harmonic Mean = " + fmt(hm, 6) + "</strong></div>");
        showSteps(["Reciprocals: " + rec.map(function (r) { return fmt(r, 6); }).join(", "), "Sum of reciprocals = " + fmt(s, 6), "HM = n ÷ sum = " + xs.length + " ÷ " + fmt(s, 6) + " = " + fmt(hm, 6), "Comparison: arithmetic mean = " + fmt(xs.reduce(function (a, b) { return a + b; }, 0) / xs.length, 4) + " (this is the wrong answer for rates)"]);
    }
    bind(calc); calc();

})();
</script>
@endsection
