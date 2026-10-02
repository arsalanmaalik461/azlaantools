@extends('layouts.app')

@section('title', 'Definite Integral Calculator — Free Online Tool')
@section('meta_description', 'Calculate the definite integral of a polynomial with limits')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Definite Integral Calculator</h1>
            <p class="lead small text-muted">Enter the polynomial coefficients (highest degree first) and the lower/upper limits — the answer comes with the antiderivative and F(b) − F(a) steps.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">                    <div class="col-md-6"><label class="form-label" for="inCoef">Coefficients (comma separated)</label><input type="text" class="form-control inp" id="inCoef" placeholder="e.g. 3, 0, 2" step="any"></div>                    <div class="col-md-4"><label class="form-label" for="inA">Lower limit (a)</label><input type="number" class="form-control inp" id="inA" placeholder="" step="any"></div>                    <div class="col-md-4"><label class="form-label" for="inB">Upper limit (b)</label><input type="number" class="form-control inp" id="inB" placeholder="" step="any"></div></div>
                    <div id="result" class="mt-3"></div>
                    <div id="steps" class="mt-3"></div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Write the coefficients separated by commas, with 0 for missing powers.</li><li>Enter the limits a and b.</li><li>The result is given in decimal form.</li></ol>
                    <p class="small text-muted mb-0">Note: This tool only does the definite integral of polynomials (the reverse of the power rule). Trig or exponential functions are not included.</p>
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

    function tstr(c, p) { if (c === 0) { return ""; } if (p === 0) { return fmt(c, 4); } var cs = Math.abs(c) === 1 ? (c < 0 ? "-" : "") : fmt(c, 4); return cs + "x" + (p === 1 ? "" : "^" + p); }
    function pstr(co) { var deg = co.length - 1, out = ""; co.forEach(function (c, i) { var t = tstr(c, deg - i); if (!t) { return; } out = out === "" ? t : out + (t.charAt(0) === "-" ? " - " + t.slice(1) : " + " + t); }); return out || "0"; }
    function peval(co, x) { var deg = co.length - 1, v = 0; co.forEach(function (c, i) { v += c * Math.pow(x, deg - i); }); return v; }
    function calc() {
        var raw = el("inCoef").value.trim(), a = num("inA"), b = num("inB");
        if (raw === "" || a === null || b === null) { err("Enter the coefficients and both limits."); return; }
        var co = raw.split(",").map(function (x) { return parseFloat(x); });
        if (co.some(function (x) { return isNaN(x); })) { err("Write coefficients as numbers separated by commas."); return; }
        var deg = co.length - 1, anti = [], st = ["f(x) = " + pstr(co)];
        co.forEach(function (c, i) { var p = deg - i; anti.push(c / (p + 1)); st.push("∫ " + fmt(c, 4) + "x^" + p + " dx = " + fmt(c, 4) + " x^" + (p + 1) + " / " + (p + 1) + " = " + tstr(c / (p + 1), p + 1)); });
        var Fb = peval(anti, b), Fa = peval(anti, a), val = Fb - Fa;
        st.push("F(x) = " + pstr(anti)); st.push("F(" + fmt(b, 3) + ") = " + fmt(Fb, 6)); st.push("F(" + fmt(a, 3) + ") = " + fmt(Fa, 6)); st.push("Integral = F(b) − F(a) = " + fmt(val, 6));
        show("<div class=\"alert alert-success mb-0 fs-5\"><strong>∫<sub>" + fmt(a, 2) + "</sub><sup>" + fmt(b, 2) + "</sup> (" + pstr(co) + ") dx = " + fmt(val, 6) + "</strong></div>");
        showSteps(st);
    }
    bind(calc); calc();

})();
</script>
@endsection
