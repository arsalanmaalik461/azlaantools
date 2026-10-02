@extends('layouts.app')

@section('title', 'Salinity Converter — Free Online Tool')
@section('meta_description', 'Enter the amount of salt and instantly convert between ppt, ppm, percent and specific gravity.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-2">Salinity Converter</h1>
            <p class="lead small text-muted mb-4">Enter the amount of salt and instantly convert between ppt, ppm, percent and specific gravity.</p>
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-md-6 mb-3"><label for="salVal" class="form-label fw-semibold">Value</label><input type="number" class="form-control" id="salVal" value="35" step="any"></div>
                        <div class="col-md-6 mb-3"><label for="salUnit" class="form-label fw-semibold">Unit</label>
                            <select class="form-select" id="salUnit"><option value="ppt" selected>ppt (parts per thousand)</option><option value="ppm">ppm</option><option value="percent">Percent (%)</option><option value="sg">Specific Gravity (SG)</option></select></div>
                    </div>
                    <div class="alert alert-info mb-0">
                        <div class="fw-bold" id="salOut">—</div>
                        <div class="small" id="salDetail"></div>
                    </div>
                </div>
            </div>
            <div class="card shadow-sm mt-4">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol class="mb-3">
                        <li>Enter your value in the boxes above or select an option.</li>
                        <li>The result updates live — no need to press any button.</li>
                        <li>Change the value or unit and the new result shows automatically.</li>
                    </ol>
                    <h3 class="h6">Note</h3>
                    <p class="small text-muted mb-0">Specific gravity is worked out with the approximate formula SG = 1 + 0.0007 x ppt (around 25°C, typical seawater). Seawater is usually 35 ppt. Use a refractometer for accurate measurement.</p>
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
    function fmt(n) {
        if (n === null || n === undefined || !isFinite(n)) return "—";
        if (n !== 0 && (Math.abs(n) >= 1e12 || Math.abs(n) < 1e-6)) return n.toExponential(6);
        return parseFloat(n.toFixed(8)).toLocaleString("en-US", { maximumFractionDigits: 8 });
    }
    function calc() {
        var v = parseFloat(document.getElementById("salVal").value);
        var u = document.getElementById("salUnit").value;
        var o = document.getElementById("salOut"), det = document.getElementById("salDetail");
        if (isNaN(v) || v < 0) { o.textContent = "—"; det.textContent = "Enter a valid value."; return; }
        var ppt;
        if (u === "ppt") { ppt = v; }
        else if (u === "ppm") { ppt = v / 1000; }
        else if (u === "percent") { ppt = v * 10; }
        else { ppt = (v - 1) / 0.0007; if (ppt < 0) { o.textContent = "—"; det.textContent = "SG cannot be below 1.000."; return; } }
        var sg = 1 + 0.0007 * ppt;
        o.textContent = fmt(ppt) + " ppt = " + fmt(ppt * 1000) + " ppm = " + fmt(ppt / 10) + " %";
        det.textContent = "Specific Gravity (approx): " + sg.toFixed(4) + " — Gram per litre (approx): " + fmt(ppt) + " g/L";
    }
    document.getElementById("salVal").addEventListener("input", calc);
    document.getElementById("salUnit").addEventListener("change", calc);
    calc();
})();
</script>
@endsection
