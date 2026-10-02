@extends('layouts.app')

@section('title', 'Linear Equation Solver — Free Online Tool')
@section('meta_description', 'Solve the one-variable equation ax + b = c step by step')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Linear Equation Solver</h1>
            <p class="lead small text-muted">Enter a, b, c of the equation ax + b = c — each step (subtracting, dividing) is shown separately.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">                    <div class="col-md-4"><label class="form-label" for="lnA">a</label><input type="number" class="form-control inp" id="lnA" placeholder="" step="any"></div>                    <div class="col-md-4"><label class="form-label" for="lnB">b</label><input type="number" class="form-control inp" id="lnB" placeholder="" step="any"></div>                    <div class="col-md-4"><label class="form-label" for="lnC">c</label><input type="number" class="form-control inp" id="lnC" placeholder="" step="any"></div></div>
                    <div id="result" class="mt-3"></div>
                    <div id="steps" class="mt-3"></div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Enter all three values.</li><li>At the end the answer is put back into the original equation as verification.</li></ol>
                    <p class="small text-muted mb-0">Note: If a = 0, the equation is either true for every x (b = c) or never true — the tool explains this case separately.</p>
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
        var a = num("lnA"), b = num("lnB"), c = num("lnC");
        if ([a, b, c].some(function (v) { return v === null; })) { err("Enter all three: a, b and c."); return; }
        var st = ["Equation: " + fmt(a, 3) + "x + " + fmt(b, 3) + " = " + fmt(c, 3)];
        if (a === 0) { show("<div class=\"alert alert-info mb-0 fs-5\"><strong>" + (b === c ? "Every x satisfies this equation (infinite solutions)." : "No solution — the equation is never true.") + "</strong></div>"); showSteps(st); return; }
        var rhs = c - b; st.push("Subtract " + fmt(b, 3) + " from both sides: " + fmt(a, 3) + "x = " + fmt(c, 3) + " − " + fmt(b, 3) + " = " + fmt(rhs, 4));
        var x = rhs / a; st.push("Divide both sides by " + fmt(a, 3) + ": x = " + fmt(rhs, 4) + " / " + fmt(a, 3) + " = " + fmt(x, 6));
        st.push("Verification: " + fmt(a, 3) + " x " + fmt(x, 4) + " + " + fmt(b, 3) + " = " + fmt(a * x + b, 4) + " ✓ (c = " + fmt(c, 3) + ")");
        show("<div class=\"alert alert-success mb-0 fs-5\"><strong>x = " + fmt(x, 6) + "</strong></div>"); showSteps(st);
    }
    bind(calc); calc();

})();
</script>
@endsection
