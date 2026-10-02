@extends('layouts.app')

@section('title', 'Cholesterol Ratio Calculator — Free Online Tool')
@section('meta_description', 'Calculate total to HDL, LDL to HDL and triglyceride to HDL ratios from your lipid panel')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Cholesterol Ratio Calculator</h1>
            <p class="lead small text-muted">Enter your lipid panel numbers to calculate the three ratios doctors commonly look at alongside the raw values.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3"><label class="form-label" for="tc">Total cholesterol (mg/dL)</label><input type="number" class="form-control" id="tc" value="200" step="any"></div>                    <div class="mb-3"><label class="form-label" for="hdl">HDL cholesterol (mg/dL)</label><input type="number" class="form-control" id="hdl" value="50" step="any"></div>                    <div class="mb-3"><label class="form-label" for="ldl">LDL cholesterol (mg/dL)</label><input type="number" class="form-control" id="ldl" value="120" step="any"></div>                    <div class="mb-3"><label class="form-label" for="tg">Triglycerides (mg/dL)</label><input type="number" class="form-control" id="tg" value="150" step="any"></div>
                    <div id="result" class="border rounded p-3 mt-3">Enter your details above and the result will appear here.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Copy the four values from your lipid panel report.</li><li>The three ratios appear instantly.</li><li>Compare them with the general reference bands in the result.</li></ol>
                    <h2 class="h5">Note</h2>
                    <p class="small text-muted mb-0">Ratios are simple divisions shown with commonly published reference bands as general information only. Total to HDL below about 3.5 is often cited as desirable, LDL to HDL below about 2.5, and triglycerides to HDL below about 2. Interpretation belongs with your full clinical picture. Estimate only — not medical advice.</p>
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
        var tc = num("tc"), hdl = num("hdl"), ldl = num("ldl"), tg = num("tg");
        if ([tc, hdl, ldl, tg].some(isNaN) || hdl <= 0 || tc <= 0) { out("Please enter valid panel values — HDL must be above zero."); return; }
        out("<strong>Total / HDL:</strong> " + fmt(tc / hdl, 2) + " (often cited desirable: under 3.5)<br><strong>LDL / HDL:</strong> " + fmt(ldl / hdl, 2) + " (often cited desirable: under 2.5)<br><strong>Triglycerides / HDL:</strong> " + fmt(tg / hdl, 2) + " (often cited desirable: under 2)");
    }
    bind(["tc", "hdl", "ldl", "tg"], calc); calc();
})();
</script>
@endsection
