@extends('layouts.app')

@section('title', 'Body Frame Size Calculator — Free Online Tool')
@section('meta_description', 'Work out whether you have a small, medium or large body frame from wrist size and height')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Body Frame Size Calculator</h1>
            <p class="lead small text-muted">The height-to-wrist ratio is the classic method used alongside ideal weight charts to judge frame size.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3"><label class="form-label" for="height">Height (cm)</label><input type="number" class="form-control" id="height" value="170" step="any"></div>                    <div class="mb-3"><label class="form-label" for="wrist">Wrist circumference (cm)</label><input type="number" class="form-control" id="wrist" value="17" step="any"></div>                    <div class="mb-3"><label class="form-label" for="sex">Sex</label><select class="form-select" id="sex"><option value="male">Male</option><option value="female">Female</option></select></div>
                    <div id="result" class="border rounded p-3 mt-3">Enter your details above and the result will appear here.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Measure your wrist circumference just above the wrist bone.</li><li>Enter your height and wrist size.</li><li>Your frame size appears instantly.</li></ol>
                    <h2 class="h5">Note</h2>
                    <p class="small text-muted mb-0">Frame = height divided by wrist circumference. Men: above 10.4 small, 9.6 to 10.4 medium, below 9.6 large. Women: above 10.9 small, 9.9 to 10.9 medium, below 9.9 large. Estimate only — not medical advice.</p>
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
        var h = num("height"), wr = num("wrist"), sex = document.getElementById("sex").value;
        if (isNaN(h) || isNaN(wr) || h <= 0 || wr <= 0) { out("Please enter valid height and wrist values."); return; }
        var r = h / wr, size;
        if (sex === "male") { size = r > 10.4 ? "Small frame" : (r >= 9.6 ? "Medium frame" : "Large frame"); }
        else { size = r > 10.9 ? "Small frame" : (r >= 9.9 ? "Medium frame" : "Large frame"); }
        out("<strong>Height to wrist ratio:</strong> " + fmt(r, 2) + "<br><strong>Frame size:</strong> " + size);
    }
    bind(["height", "wrist", "sex"], calc); calc();
})();
</script>
@endsection
