@extends('layouts.app')

@section('title', 'Waist to Hip Ratio Calculator — Free Online Tool')
@section('meta_description', 'Calculate your waist to hip ratio and see where it falls on the standard WHO chart')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Waist to Hip Ratio Calculator</h1>
            <p class="lead small text-muted">Waist-to-hip ratio shows how fat is distributed. Enter waist and hip measurements to see your ratio on the standard chart.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3"><label class="form-label" for="waist">Waist circumference (cm)</label><input type="number" class="form-control" id="waist" value="85" step="any"></div>                    <div class="mb-3"><label class="form-label" for="hip">Hip circumference (cm)</label><input type="number" class="form-control" id="hip" value="100" step="any"></div>                    <div class="mb-3"><label class="form-label" for="sex">Sex</label><select class="form-select" id="sex"><option value="male">Male</option><option value="female">Female</option></select></div>
                    <div id="result" class="border rounded p-3 mt-3">Enter your details above and the result will appear here.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Measure your waist at its narrowest point and your hips at their widest.</li><li>Enter both in the same unit and choose your sex.</li><li>Your ratio and risk band appear instantly.</li></ol>
                    <h2 class="h5">Note</h2>
                    <p class="small text-muted mb-0">Standard chart bands: men — low 0.95 or below, moderate 0.96 to 1.00, high above 1.00; women — low 0.80 or below, moderate 0.81 to 0.85, high above 0.85. The WHO also uses 0.90 (men) and 0.85 (women) as abdominal obesity cutoffs. Estimate only — not medical advice.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    function num(id) { var v = parseFloat(document.getElementById(id).value); return isFinite(v) ? v : NaN; }
    function fmt(n, d) { return Number(n).toLocaleString("en-US", { minimumFractionDigits: d, maximumFractionDigits: d }); }
    function bind(ids, fn) { ids.forEach(function (id) { var el = document.getElementById(id); el.addEventListener("input", fn); el.addEventListener("change", fn); }); }
    function out(html) { document.getElementById("result").innerHTML = html; }
    function calc() {
        var wa = num("waist"), hip = num("hip"), sex = document.getElementById("sex").value;
        if (isNaN(wa) || isNaN(hip) || wa <= 0 || hip <= 0) { out("Please enter valid waist and hip values."); return; }
        var r = wa / hip, band;
        if (sex === "male") { band = r <= 0.95 ? "Low health risk band" : r <= 1.0 ? "Moderate health risk band" : "High health risk band"; }
        else { band = r <= 0.80 ? "Low health risk band" : r <= 0.85 ? "Moderate health risk band" : "High health risk band"; }
        out("<strong>Waist to hip ratio:</strong> " + fmt(r, 2) + "<br><strong>Band:</strong> " + band);
    }
    bind(["waist", "hip", "sex"], calc); calc();
})();
</script>
@endsection
