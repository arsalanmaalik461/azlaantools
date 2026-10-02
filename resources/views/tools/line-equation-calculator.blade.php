@extends('layouts.app')

@section('title', 'Equation of a Line Calculator — Free Online Tool')
@section('meta_description', 'Make the equation of a straight line from two points or from slope and a point')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Equation of a Line Calculator</h1>
            <p class="lead small text-muted">Two ways: give two points in A, slope and one point in B — both build the slope-intercept equation y = mx + c with steps.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h6">A. From Two Points</h2>
                    <div class="row g-3">                    <div class="col-md-4"><label class="form-label" for="leX1">x₁</label><input type="number" class="form-control inp" id="leX1" placeholder="" step="any"></div>                    <div class="col-md-4"><label class="form-label" for="leY1">y₁</label><input type="number" class="form-control inp" id="leY1" placeholder="" step="any"></div>                    <div class="col-md-4"><label class="form-label" for="leX2">x₂</label><input type="number" class="form-control inp" id="leX2" placeholder="" step="any"></div>                    <div class="col-md-4"><label class="form-label" for="leY2">y₂</label><input type="number" class="form-control inp" id="leY2" placeholder="" step="any"></div></div>
                    <div id="leResA" class="mt-2 fw-semibold"></div>
                    <hr>
                    <h2 class="h6">B. From Slope and One Point</h2>
                    <div class="row g-3">                    <div class="col-md-4"><label class="form-label" for="leM">Slope (m)</label><input type="number" class="form-control inp" id="leM" placeholder="" step="any"></div>                    <div class="col-md-4"><label class="form-label" for="lePx">Point x</label><input type="number" class="form-control inp" id="lePx" placeholder="" step="any"></div>                    <div class="col-md-4"><label class="form-label" for="lePy">Point y</label><input type="number" class="form-control inp" id="lePy" placeholder="" step="any"></div></div>
                    <div id="leResB" class="mt-2 fw-semibold"></div>
                    <div id="result" class="mt-3"></div>
                    <div id="steps" class="mt-3"></div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Enter the numbers for your method.</li><li>For a vertical line (x₁ = x₂) the equation becomes x = constant and the slope is undefined.</li></ol>
                    <p class="small text-muted mb-0">Note: Slope m = (y₂−y₁)/(x₂−x₁). Then c is found from y − y₁ = m(x − x₁).</p>
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

    function eqStr(m, c) { return "y = " + fmt(m, 4) + "x " + (c < 0 ? "− " + fmt(-c, 4) : "+ " + fmt(c, 4)); }
    function calc() {
        var x1 = num("leX1"), y1 = num("leY1"), x2 = num("leX2"), y2 = num("leY2");
        if ([x1, y1, x2, y2].every(function (v) { return v !== null; })) {
            if (x1 === x2) { el("leResA").textContent = "Vertical line: x = " + fmt(x1, 4) + " (slope undefined)"; }
            else if (y1 === y2) { el("leResA").textContent = "Horizontal line: y = " + fmt(y1, 4) + " (slope = 0)"; }
            else { var m = (y2 - y1) / (x2 - x1); var c = y1 - m * x1; el("leResA").textContent = "Slope m = (" + fmt(y2, 2) + " − " + fmt(y1, 2) + ") / (" + fmt(x2, 2) + " − " + fmt(x1, 2) + ") = " + fmt(m, 4) + "  →  " + eqStr(m, c); }
        } else { el("leResA").textContent = ""; }
        var mm = num("leM"), px = num("lePx"), py = num("lePy");
        if ([mm, px, py].every(function (v) { return v !== null; })) { var cc = py - mm * px; el("leResB").textContent = "y − " + fmt(py, 2) + " = " + fmt(mm, 3) + "(x − " + fmt(px, 2) + ")  →  " + eqStr(mm, cc); } else { el("leResB").textContent = ""; }
        show(""); showSteps([]);
    }
    bind(calc); calc();

})();
</script>
@endsection
