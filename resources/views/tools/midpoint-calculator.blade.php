@extends('layouts.app')

@section('title', 'Midpoint Calculator — Free Online Tool')
@section('meta_description', 'Find the middle point of two points using the midpoint formula.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Midpoint Calculator</h1>
            <p class="lead small text-muted">Enter the coordinates of two points — you will get the midpoint with formula steps.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">                    <div class="col-md-4"><label class="form-label" for="mpX1">x₁</label><input type="number" class="form-control inp" id="mpX1" placeholder="" step="any"></div>                    <div class="col-md-4"><label class="form-label" for="mpY1">y₁</label><input type="number" class="form-control inp" id="mpY1" placeholder="" step="any"></div>                    <div class="col-md-4"><label class="form-label" for="mpX2">x₂</label><input type="number" class="form-control inp" id="mpX2" placeholder="" step="any"></div>                    <div class="col-md-4"><label class="form-label" for="mpY2">y₂</label><input type="number" class="form-control inp" id="mpY2" placeholder="" step="any"></div></div>
                    <div id="result" class="mt-3"></div>
                    <div id="steps" class="mt-3"></div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Enter the coordinates of both points.</li><li>Midpoint = average of the two x values, average of the two y values.</li></ol>
                    <p class="small text-muted mb-0">Note: The midpoint divides a line segment into two equal parts. This formula is also the basis of the section formula.</p>
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
        var x1 = num("mpX1"), y1 = num("mpY1"), x2 = num("mpX2"), y2 = num("mpY2");
        if ([x1, y1, x2, y2].some(function (v) { return v === null; })) { err("Please enter all four coordinates."); return; }
        var mx = (x1 + x2) / 2, my = (y1 + y2) / 2;
        show("<div class=\"alert alert-success mb-0 fs-5\"><strong>Midpoint = (" + fmt(mx, 6) + ", " + fmt(my, 6) + ")</strong></div>");
        showSteps(["x-coordinate: (" + fmt(x1, 3) + " + " + fmt(x2, 3) + ") / 2 = " + fmt(mx, 6), "y-coordinate: (" + fmt(y1, 3) + " + " + fmt(y2, 3) + ") / 2 = " + fmt(my, 6)]);
    }
    bind(calc); calc();

})();
</script>
@endsection
