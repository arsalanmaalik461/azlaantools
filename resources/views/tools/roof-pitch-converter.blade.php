@extends('layouts.app')

@section('title', 'Roof Pitch Converter — Free Online Tool')
@section('meta_description', 'Enter the roof rise and run to get the pitch ratio, angle in degrees and percent instantly.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-2">Roof Pitch Converter</h1>
            <p class="lead small text-muted mb-4">Enter the roof rise and run to get the pitch ratio, angle in degrees and percent instantly.</p>
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-md-6 mb-3"><label for="rise" class="form-label fw-semibold">Rise (height)</label><input type="number" class="form-control" id="rise" value="6" step="any"></div>
                        <div class="col-md-6 mb-3"><label for="run" class="form-label fw-semibold">Run (horizontal distance)</label><input type="number" class="form-control" id="run" value="12" step="any"></div>
                    </div>
                    <div class="alert alert-info mb-0">
                        <div class="fw-bold" id="roofOut">—</div>
                        <div class="small" id="roofDetail"></div>
                    </div>
                </div>
            </div>
            <div class="card shadow-sm mt-4">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol class="mb-3">
                        <li>Enter your values in the boxes above or pick an option.</li>
                        <li>The result updates live — no button to press.</li>
                        <li>Change a value or unit and the new result appears automatically.</li>
                    </ol>
                    <h3 class="h6">Note</h3>
                    <p class="small text-muted mb-0">Pitch is usually written as rise over a 12-unit run (for example 6/12). For the rafter length, the slope factor = sqrt(rise^2 + run^2) / run is used.</p>
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
        var rise = parseFloat(document.getElementById("rise").value);
        var run = parseFloat(document.getElementById("run").value);
        var o = document.getElementById("roofOut"), det = document.getElementById("roofDetail");
        if (isNaN(rise) || isNaN(run) || run <= 0 || rise < 0) { o.textContent = "—"; det.textContent = "Enter a valid rise and run."; return; }
        var angle = Math.atan2(rise, run) * 180 / Math.PI;
        var pct = rise / run * 100;
        var per12 = rise / run * 12;
        var factor = Math.sqrt(rise * rise + run * run) / run;
        o.textContent = "Pitch: " + fmt(per12) + " / 12 — Angle: " + fmt(angle) + " degrees";
        det.textContent = "Percent slope: " + fmt(pct) + "% — Slope factor (rafter): " + fmt(factor) + " — Ratio: 1 : " + fmt(run / (rise || 1));
    }
    ["rise", "run"].forEach(function (id) { document.getElementById(id).addEventListener("input", calc); });
    calc();
})();
</script>
@endsection
