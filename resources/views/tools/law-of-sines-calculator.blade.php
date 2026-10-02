@extends('layouts.app')

@section('title', 'Law of Sines Calculator — Free Online Tool')
@section('meta_description', 'Use the sine rule to find a missing side or angle of any triangle')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Law of Sines Calculator</h1>
            <p class="lead small text-muted">If one side and its opposite angle are known, the sine rule a/sin A = b/sin B gives every missing side or angle. Both parts are below.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h6">A. Missing Side b — when a, A and B are known</h2>
                    <div class="row g-3">                    <div class="col-md-4"><label class="form-label" for="lsA">Side a</label><input type="number" class="form-control inp" id="lsA" placeholder="" step="any"></div>                    <div class="col-md-4"><label class="form-label" for="lsAA">Angle A (degrees, opposite side a)</label><input type="number" class="form-control inp" id="lsAA" placeholder="" step="any"></div>                    <div class="col-md-4"><label class="form-label" for="lsAB">Angle B (degrees)</label><input type="number" class="form-control inp" id="lsAB" placeholder="" step="any"></div></div>
                    <div id="lsResA" class="mt-2 fw-semibold"></div>
                    <hr>
                    <h2 class="h6">B. Missing Angle B — when a, A and b are known</h2>
                    <div class="row g-3">                    <div class="col-md-4"><label class="form-label" for="lsA2">Side a</label><input type="number" class="form-control inp" id="lsA2" placeholder="" step="any"></div>                    <div class="col-md-4"><label class="form-label" for="lsAA2">Angle A (degrees)</label><input type="number" class="form-control inp" id="lsAA2" placeholder="" step="any"></div>                    <div class="col-md-4"><label class="form-label" for="lsB2">Side b</label><input type="number" class="form-control inp" id="lsB2" placeholder="" step="any"></div></div>
                    <div id="lsResB" class="mt-2 fw-semibold"></div>
                    <div id="result" class="mt-3"></div>
                    <div id="steps" class="mt-3"></div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Enter the known values; angles must be in degrees.</li><li>In part B the second possible answer (ambiguous case) is also checked.</li></ol>
                    <p class="small text-muted mb-0">Note: Sine rule: a/sin A = b/sin B = c/sin C. A solved angle can have two possible answers (acute and obtuse) — the tool shows both when both are possible.</p>
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
        var a = num("lsA"), A = num("lsAA"), B = num("lsAB"); var rad = Math.PI / 180;
        if (a !== null && A !== null && B !== null && a > 0 && A > 0 && A < 180 && B > 0 && B < 180 && A + B < 180) { var b = a * Math.sin(B * rad) / Math.sin(A * rad); var C = 180 - A - B; var c = a * Math.sin(C * rad) / Math.sin(A * rad); el("lsResA").textContent = "b = " + fmt(a, 3) + " x sin " + fmt(B, 2) + "° / sin " + fmt(A, 2) + "° = " + fmt(b, 6) + "  |  Third angle C = " + fmt(C, 3) + "°, side c = " + fmt(c, 6); } else { el("lsResA").textContent = ""; }
        var a2 = num("lsA2"), A2 = num("lsAA2"), b2 = num("lsB2");
        if (a2 !== null && A2 !== null && b2 !== null && a2 > 0 && b2 > 0 && A2 > 0 && A2 < 180) {
            var s = b2 * Math.sin(A2 * rad) / a2;
            if (s > 1) { el("lsResB").textContent = "This triangle is not possible — sin B comes out larger than 1."; }
            else { var B1 = Math.asin(s) / rad; var B2 = 180 - B1; var txt = "Angle B = " + fmt(B1, 4) + "°"; if (Math.abs(B2 - B1) > 0.01 && A2 + B2 < 180) { txt += "  |  Second possible answer (ambiguous case): B = " + fmt(B2, 4) + "°"; } el("lsResB").textContent = txt; }
        } else { el("lsResB").textContent = ""; }
        show(""); showSteps([]);
    }
    bind(calc); calc();

})();
</script>
@endsection
