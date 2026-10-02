@extends('layouts.app')

@section('title', 'Slope Grade Converter — Free Online Tool')
@section('meta_description', 'Enter a slope and instantly convert it to percent grade, degrees, and 1 in X ratio formats.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-2">Slope Grade Converter</h1>
            <p class="lead small text-muted mb-4">Enter a slope and instantly convert it to percent grade, degrees, and 1 in X ratio formats.</p>
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-md-6 mb-3"><label for="slRise" class="form-label fw-semibold">Rise</label><input type="number" class="form-control" id="slRise" value="1" step="any"></div>
                        <div class="col-md-6 mb-3"><label for="slRun" class="form-label fw-semibold">Run</label><input type="number" class="form-control" id="slRun" value="10" step="any"></div>
                        <div class="col-md-6 mb-3"><label for="slPct" class="form-label fw-semibold">Or Enter Percent Grade (%)</label><input type="number" class="form-control" id="slPct" placeholder="e.g. 10" step="any"></div>
                        <div class="col-md-6 mb-3"><label for="slDeg" class="form-label fw-semibold">Or Enter Angle (degrees)</label><input type="number" class="form-control" id="slDeg" placeholder="e.g. 5.71" step="any"></div>
                    </div>
                    <div class="alert alert-info mb-0">
                        <div class="fw-bold" id="slOut">—</div>
                        <div class="small" id="slDetail"></div>
                    </div>
                </div>
            </div>
            <div class="card shadow-sm mt-4">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol class="mb-3">
                        <li>Enter your value in the boxes above or select an option.</li>
                        <li>The result updates live at once — no button press needed.</li>
                        <li>Change the value or unit and the new result shows on its own.</li>
                    </ol>
                    <h3 class="h6">Note</h3>
                    <p class="small text-muted mb-0">Percent grade = rise/run x 100. A 100% grade means 45 degrees, not 200%. The common standard for a wheelchair ramp is 1:12 (8.33%).</p>
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
    var riseEl = document.getElementById("slRise"), runEl = document.getElementById("slRun");
    var pctEl = document.getElementById("slPct"), degEl = document.getElementById("slDeg");
    var o = document.getElementById("slOut"), det = document.getElementById("slDetail");
    function show(pct) {
        if (pct === null || !isFinite(pct)) { o.textContent = "—"; det.textContent = "Please enter a valid value."; return; }
        var deg = Math.atan(pct / 100) * 180 / Math.PI;
        var ratio = pct === 0 ? "level (0)" : ("1 in " + fmt(100 / pct));
        o.textContent = "Grade: " + fmt(pct) + "% — Angle: " + fmt(deg) + " degrees";
        det.textContent = "Ratio: " + ratio + " — Rise per 100 units of run: " + fmt(pct) + " units.";
    }
    function fromRiseRun() {
        var r = parseFloat(riseEl.value), u = parseFloat(runEl.value);
        if (isNaN(r) || isNaN(u) || u === 0) { show(null); return; }
        pctEl.value = ""; degEl.value = "";
        show(r / u * 100);
    }
    function fromPct() {
        var p = parseFloat(pctEl.value);
        if (isNaN(p)) { fromRiseRun(); return; }
        degEl.value = "";
        show(p);
    }
    function fromDeg() {
        var d = parseFloat(degEl.value);
        if (isNaN(d)) { fromRiseRun(); return; }
        pctEl.value = "";
        if (Math.abs(d) >= 90) { show(null); return; }
        show(Math.tan(d * Math.PI / 180) * 100);
    }
    riseEl.addEventListener("input", fromRiseRun);
    runEl.addEventListener("input", fromRiseRun);
    pctEl.addEventListener("input", fromPct);
    degEl.addEventListener("input", fromDeg);
    fromRiseRun();
})();
</script>
@endsection
