@extends('layouts.app')

@section('title', 'Ring Size Converter — Free Online Tool')
@section('meta_description', 'Enter your ring size and instantly convert between UK, US and EU sizes and the inner diameter in mm.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-2">Ring Size Converter</h1>
            <p class="lead small text-muted mb-4">Enter your ring size and instantly convert between UK, US and EU sizes and the inner diameter in mm.</p>
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="mb-3"><label for="ringMm" class="form-label fw-semibold">Finger circumference (mm) — wrap a thread around your finger to measure</label><input type="number" class="form-control form-control-lg" id="ringMm" value="57" step="any"></div>
                    <div class="alert alert-info mb-0">
                        <div class="fw-bold" id="ringOut">—</div>
                        <div class="small" id="ringDetail"></div>
                    </div>
                </div>
            </div>
            <div class="card shadow-sm mt-4">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol class="mb-3">
                        <li>Enter your value in the box above or pick an option.</li>
                        <li>The result updates live — no button to press.</li>
                        <li>Change the value or unit and the new result appears automatically.</li>
                    </ol>
                    <h3 class="h6">Note</h3>
                    <p class="small text-muted mb-0">Sizes are matched to the closest row of the standard international chart. The EU size is simply the circumference in mm. Fingers can swell a little in the evening — always double-check the size and confirm with a jeweller if in doubt.</p>
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
    // rows: [circumference mm, US, UK, JP]
    var chart = [
        [44.2, 3, "F"], [45.5, 3.5, "G"], [46.8, 4, "H"], [48.0, 4.5, "I"], [49.3, 5, "J"],
        [50.6, 5.5, "K"], [51.9, 6, "L"], [53.1, 6.5, "M"], [54.4, 7, "N"], [55.7, 7.5, "O"],
        [57.0, 8, "P"], [58.3, 8.5, "Q"], [59.5, 9, "R"], [60.8, 9.5, "S"], [62.1, 10, "T"],
        [63.4, 10.5, "U"], [64.6, 11, "V"], [65.9, 11.5, "W"], [67.2, 12, "X"], [68.5, 12.5, "Y"], [69.7, 13, "Z"]
    ];
    function fmt(n) {
        if (n === null || n === undefined || !isFinite(n)) return "—";
        if (n !== 0 && (Math.abs(n) >= 1e12 || Math.abs(n) < 1e-6)) return n.toExponential(6);
        return parseFloat(n.toFixed(8)).toLocaleString("en-US", { maximumFractionDigits: 8 });
    }
    function calc() {
        var c = parseFloat(document.getElementById("ringMm").value);
        var o = document.getElementById("ringOut"), det = document.getElementById("ringDetail");
        if (isNaN(c) || c < 40 || c > 75) { o.textContent = "—"; det.textContent = "Enter a circumference of 40-75 mm."; return; }
        var best = chart[0], bd = 1e9;
        chart.forEach(function (r) { var d = Math.abs(r[0] - c); if (d < bd) { bd = d; best = r; } });
        var dia = c / Math.PI;
        var jp = Math.round(best[1] + 12);
        o.textContent = "US " + best[1] + " — UK " + best[2] + " — EU " + fmt(best[0]);
        det.textContent = "Inner diameter: " + fmt(dia) + " mm — Japan size (approx): " + jp + " — Chart row circumference: " + best[0] + " mm.";
    }
    document.getElementById("ringMm").addEventListener("input", calc);
    calc();
})();
</script>
@endsection
