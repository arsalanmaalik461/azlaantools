@extends('layouts.app')

@section('title', 'kW to Amps Converter — Free Online Tool')
@section('meta_description', 'Enter power in kW, voltage and single or three phase, and get the amps instantly.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-2">kW to Amps Converter</h1>
            <p class="lead small text-muted mb-4">Enter power in kW, voltage and single or three phase, and get the amps instantly.</p>
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-md-4 mb-3">
                            <label for="kw" class="form-label fw-semibold">Power (kW)</label>
                            <input type="number" class="form-control" id="kw" value="10" step="any">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="volt" class="form-label fw-semibold">Voltage (V)</label>
                            <input type="number" class="form-control" id="volt" value="400" step="any">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="phase" class="form-label fw-semibold">System</label>
                            <select class="form-select" id="phase">
                                <option value="dc">DC</option>
                                <option value="single">AC Single Phase</option>
                                <option value="three" selected>AC Three Phase</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="pf" class="form-label fw-semibold">Power Factor (PF)</label>
                            <input type="number" class="form-control" id="pf" value="0.8" step="any" min="0.1" max="1">
                        </div>
                    </div>
                    <div class="alert alert-info text-center mb-0">
                        <div class="fs-4 fw-bold" id="ampsOut">—</div>
                        <div class="small" id="ampsDetail"></div>
                    </div>
                </div>
            </div>
            <div class="card shadow-sm mt-4">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol class="mb-3">
                        <li>Enter your values in the boxes above or select an option.</li>
                        <li>The result updates live — no button press is needed.</li>
                        <li>Change any value to see the new result right away.</li>
                    </ol>
                    <h3 class="h6">Note</h3>
                    <p class="small text-muted mb-0">Formula: for DC, I = P / V; for single phase, I = P / (V x PF); for three phase, I = P / (1.732 x V x PF). Always choose a breaker and cable size a little above the load, and get the final work done by a certified electrician.</p>
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
    var kwEl = document.getElementById("kw"), voltEl = document.getElementById("volt");
    var phaseEl = document.getElementById("phase"), pfEl = document.getElementById("pf");
    var outEl = document.getElementById("ampsOut"), detEl = document.getElementById("ampsDetail");
    function calc() {
        var kw = parseFloat(kwEl.value), v = parseFloat(voltEl.value), pf = parseFloat(pfEl.value);
        if (isNaN(kw) || isNaN(v) || v <= 0) { outEl.textContent = "—"; detEl.textContent = "Enter valid kW and voltage."; return; }
        var watts = kw * 1000, amps, label;
        if (phaseEl.value === "dc") { amps = watts / v; label = "DC: I = P / V"; }
        else if (phaseEl.value === "single") {
            if (isNaN(pf) || pf <= 0 || pf > 1) { outEl.textContent = "—"; detEl.textContent = "Keep the power factor between 0.1 and 1."; return; }
            amps = watts / (v * pf); label = "Single phase: I = P / (V x PF)";
        } else {
            if (isNaN(pf) || pf <= 0 || pf > 1) { outEl.textContent = "—"; detEl.textContent = "Keep the power factor between 0.1 and 1."; return; }
            amps = watts / (Math.sqrt(3) * v * pf); label = "Three phase: I = P / (1.732 x V x PF)";
        }
        outEl.textContent = "Current = " + fmt(amps) + " Amps";
        detEl.textContent = label + " — " + fmt(kw) + " kW at " + fmt(v) + " V";
    }
    [kwEl, voltEl, pfEl].forEach(function (el) { el.addEventListener("input", calc); });
    phaseEl.addEventListener("change", calc);
    calc();
})();
</script>
@endsection
