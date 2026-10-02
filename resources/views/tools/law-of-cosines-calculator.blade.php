@extends('layouts.app')

@section('title', 'Law of Cosines Calculator — Free Online Tool')
@section('meta_description', 'Use the cosine rule to calculate a missing side or angle of a triangle')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Law of Cosines Calculator</h1>
            <p class="lead small text-muted">Two parts: in A, give two sides and the angle between them to find the third side; in B, give all three sides to find any angle.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h6">A. Missing Side — c = √(a² + b² − 2ab cos C)</h2>
                    <div class="row g-3">                    <div class="col-md-4"><label class="form-label" for="lcA">Side a</label><input type="number" class="form-control inp" id="lcA" placeholder="" step="any"></div>                    <div class="col-md-4"><label class="form-label" for="lcB">Side b</label><input type="number" class="form-control inp" id="lcB" placeholder="" step="any"></div>                    <div class="col-md-4"><label class="form-label" for="lcAng">Included angle C (degrees)</label><input type="number" class="form-control inp" id="lcAng" placeholder="" step="any"></div></div>
                    <div id="lcResA" class="mt-2 fw-semibold"></div>
                    <hr>
                    <h2 class="h6">B. Missing Angle — cos C = (a² + b² − c²) / (2ab)</h2>
                    <div class="row g-3">                    <div class="col-md-4"><label class="form-label" for="lcA2">Side a</label><input type="number" class="form-control inp" id="lcA2" placeholder="" step="any"></div>                    <div class="col-md-4"><label class="form-label" for="lcB2">Side b</label><input type="number" class="form-control inp" id="lcB2" placeholder="" step="any"></div>                    <div class="col-md-4"><label class="form-label" for="lcC2">Side c (opposite angle C)</label><input type="number" class="form-control inp" id="lcC2" placeholder="" step="any"></div></div>
                    <div id="lcResB" class="mt-2 fw-semibold"></div>
                    <div id="result" class="mt-3"></div>
                    <div id="steps" class="mt-3"></div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Enter the numbers for the part you need.</li><li>Enter angles in degrees.</li></ol>
                    <p class="small text-muted mb-0">Note: Cosine rule: c² = a² + b² − 2ab cos C. It works on any triangle, not only right triangles.</p>
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
        var a = num("lcA"), b = num("lcB"), ang = num("lcAng");
        if (a !== null && b !== null && ang !== null && a > 0 && b > 0 && ang > 0 && ang < 180) { var c = Math.sqrt(a * a + b * b - 2 * a * b * Math.cos(ang * Math.PI / 180)); el("lcResA").textContent = "c = √(" + fmt(a * a, 3) + " + " + fmt(b * b, 3) + " − 2 x " + fmt(a, 3) + " x " + fmt(b, 3) + " x cos " + fmt(ang, 2) + "°) = " + fmt(c, 6); } else { el("lcResA").textContent = ""; }
        var a2 = num("lcA2"), b2 = num("lcB2"), c2 = num("lcC2");
        if (a2 !== null && b2 !== null && c2 !== null && a2 > 0 && b2 > 0 && c2 > 0) {
            if (a2 + b2 <= c2 || a2 + c2 <= b2 || b2 + c2 <= a2) { el("lcResB").textContent = "These sides cannot make a triangle."; }
            else { var cosC = (a2 * a2 + b2 * b2 - c2 * c2) / (2 * a2 * b2); var C = Math.acos(Math.max(-1, Math.min(1, cosC))) * 180 / Math.PI; el("lcResB").textContent = "cos C = " + fmt(cosC, 6) + " → Angle C = " + fmt(C, 4) + "°"; }
        } else { el("lcResB").textContent = ""; }
        show(""); showSteps([]);
    }
    bind(calc); calc();

})();
</script>
@endsection
