@extends('layouts.app')

@section('title', 'Distance Between Two Points Calculator — Free Online Tool')
@section('meta_description', 'Calculate the distance between two points using the distance formula')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Distance Between Two Points Calculator</h1>
            <p class="lead small text-muted">Enter the coordinates of two points (x₁, y₁) and (x₂, y₂) — the distance is calculated with steps.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">                    <div class="col-md-4"><label class="form-label" for="dfX1">x₁</label><input type="number" class="form-control inp" id="dfX1" placeholder="" step="any"></div>                    <div class="col-md-4"><label class="form-label" for="dfY1">y₁</label><input type="number" class="form-control inp" id="dfY1" placeholder="" step="any"></div>                    <div class="col-md-4"><label class="form-label" for="dfX2">x₂</label><input type="number" class="form-control inp" id="dfX2" placeholder="" step="any"></div>                    <div class="col-md-4"><label class="form-label" for="dfY2">y₂</label><input type="number" class="form-control inp" id="dfY2" placeholder="" step="any"></div></div>
                    <div id="result" class="mt-3"></div>
                    <div id="steps" class="mt-3"></div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Enter the coordinates of both points.</li><li>The result shows steps with Δx, Δy and √((Δx)² + (Δy)²).</li></ol>
                    <p class="small text-muted mb-0">Note: The distance formula comes from the Pythagoras theorem: d = √((x₂−x₁)² + (y₂−y₁)²).</p>
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
        var x1 = num("dfX1"), y1 = num("dfY1"), x2 = num("dfX2"), y2 = num("dfY2");
        if ([x1, y1, x2, y2].some(function (v) { return v === null; })) { err("Enter all four coordinates."); return; }
        var dx = x2 - x1, dy = y2 - y1, d = Math.sqrt(dx * dx + dy * dy);
        show("<div class=\"alert alert-success mb-0 fs-5\"><strong>Distance = " + fmt(d, 6) + " units</strong></div>");
        showSteps(["Δx = x₂ − x₁ = " + fmt(x2, 4) + " − " + fmt(x1, 4) + " = " + fmt(dx, 4), "Δy = y₂ − y₁ = " + fmt(y2, 4) + " − " + fmt(y1, 4) + " = " + fmt(dy, 4), "d = √((" + fmt(dx, 4) + ")² + (" + fmt(dy, 4) + ")²) = √(" + fmt(dx * dx, 4) + " + " + fmt(dy * dy, 4) + ") = √" + fmt(dx * dx + dy * dy, 4), "d = " + fmt(d, 6)]);
    }
    bind(calc); calc();

})();
</script>
@endsection
