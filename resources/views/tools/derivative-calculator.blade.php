@extends('layouts.app')

@section('title', 'Derivative Calculator — Free Online Tool')
@section('meta_description', 'Find the derivative of polynomial functions step by step using the power rule')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Derivative Calculator</h1>
            <p class="lead small text-muted">Write the polynomial coefficients starting from the highest degree, separated by commas. For example for 3x³ − 5x + 2 write: 3, 0, -5, 2.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">                    <div class="col-md-8"><label class="form-label" for="dvCoef">Coefficients (comma separated)</label><input type="text" class="form-control inp" id="dvCoef" placeholder="e.g. 3, 0, -5, 2" step="any"></div>                    <div class="col-md-4"><label class="form-label" for="dvAt">Derivative at x (optional)</label><input type="number" class="form-control inp" id="dvAt" placeholder="" step="any" value=""></div></div>
                    <div id="result" class="mt-3"></div>
                    <div id="steps" class="mt-3"></div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Write coefficients in decreasing order of degree (the highest power of x first).</li><li>Write 0 for any missing power in between.</li><li>The power rule (d/dx xⁿ = n xⁿ⁻¹) is applied to each term and the steps are shown.</li></ol>
                    <p class="small text-muted mb-0">Note: This tool only works for polynomials and uses the power rule. Trig, log and exponential functions are not supported.</p>
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

    function term(c, p) { if (c === 0) { return ""; } var cs = (p > 0 && Math.abs(c) === 1) ? (c < 0 ? "-" : "") : fmt(c, 4); if (p === 0) { return fmt(c, 4); } if (p === 1) { return cs + "x"; } return cs + "x^" + p; }
    function polyStr(co) { var deg = co.length - 1; var out = ""; co.forEach(function (c, i) { var t = term(c, deg - i); if (t === "") { return; } if (out === "") { out = t; } else { out += (t.charAt(0) === "-" ? " - " + t.slice(1) : " + " + t); } }); return out === "" ? "0" : out; }
    function calc() {
        var raw = el("dvCoef").value.trim(); if (raw === "") { err("Enter coefficients, for example 3, 0, -5, 2."); return; }
        var co = raw.split(",").map(function (x) { return parseFloat(x); });
        if (co.some(function (x) { return isNaN(x); }) || co.length < 2) { err("Enter at least 2 coefficients separated by commas."); return; }
        var deg = co.length - 1, st = ["f(x) = " + polyStr(co)], dco = [];
        co.forEach(function (c, i) { var p = deg - i; if (p === 0) { st.push("Derivative of constant " + fmt(c, 4) + " = 0"); return; } if (c === 0) { return; } dco.push(c * p); st.push("Power rule: d/dx [" + fmt(c, 4) + "x^" + p + "] = " + p + " x " + fmt(c, 4) + " x^" + (p - 1) + " = " + term(c * p, p - 1)); });
        while (dco.length && dco[dco.length - 1] === 0) { dco.pop(); }
        var res = "f′(x) = " + polyStr(dco.length ? dco : [0]);
        var at = num("dvAt"); var extra = "";
        if (at !== null) { var v = 0; dco.forEach(function (c, i) { v += c * Math.pow(at, dco.length - 1 - i); }); extra = "<div class=\"mt-2\">x = " + fmt(at, 4) + " slope: <strong>f′(" + fmt(at, 4) + ") = " + fmt(v, 6) + "</strong></div>"; st.push("Putting x = " + fmt(at, 4) + " in the derivative gives slope = " + fmt(v, 6)); }
        show("<div class=\"alert alert-success mb-0 fs-5\"><strong>" + res + "</strong></div>" + extra); showSteps(st);
    }
    bind(calc); calc();

})();
</script>
@endsection
