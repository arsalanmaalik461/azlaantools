@extends('layouts.app')

@section('title', 'Ellipse Area Calculator — Free Online Tool')
@section('meta_description', 'Calculate the area and perimeter of an ellipse from its two axes')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Ellipse Area Calculator</h1>
            <p class="lead small text-muted">Enter the semi-major axis (a) and semi-minor axis (b) — you will get the area and perimeter (Ramanujan approximation) with steps.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">                    <div class="col-md-4"><label class="form-label" for="elA">Semi-major axis (a)</label><input type="number" class="form-control inp" id="elA" placeholder="" step="any"></div>                    <div class="col-md-4"><label class="form-label" for="elB">Semi-minor axis (b)</label><input type="number" class="form-control inp" id="elB" placeholder="" step="any"></div></div>
                    <div id="result" class="mt-3"></div>
                    <div id="steps" class="mt-3"></div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Enter both semi axes (half of the full length).</li><li>See the result for Area = πab and perimeter.</li></ol>
                    <p class="small text-muted mb-0">Note: The famous Ramanujan approximation is used for the perimeter because there is no exact simple formula for the perimeter of an ellipse.</p>
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
        var a = num("elA"), b = num("elB");
        if (a === null || b === null || a <= 0 || b <= 0) { err("Enter both axes as positive numbers."); return; }
        var area = Math.PI * a * b; var h = Math.pow((a - b) / (a + b), 2);
        var per = Math.PI * (a + b) * (1 + (3 * h) / (10 + Math.sqrt(4 - 3 * h)));
        show("<div class=\"alert alert-success mb-0\">Area = <strong>" + fmt(area, 4) + " sq units</strong><br>Perimeter ≈ <strong>" + fmt(per, 4) + " units</strong></div>");
        showSteps(["Area = π x a x b = π x " + fmt(a, 4) + " x " + fmt(b, 4) + " = " + fmt(area, 4), "h = ((a − b)/(a + b))² = " + fmt(h, 6), "Perimeter (Ramanujan) = π(a + b)(1 + 3h / (10 + √(4 − 3h))) ≈ " + fmt(per, 4), "Eccentricity e = √(1 − b²/a²) = " + fmt(Math.sqrt(Math.abs(1 - (b * b) / (a * a))), 4)]);
    }
    bind(calc); calc();

})();
</script>
@endsection
