@extends('layouts.app')

@section('title', 'Thread Pitch Converter — Free Online Tool')
@section('meta_description', 'Enter thread pitch and instantly convert mm pitch to TPI and TPI to mm pitch.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-2">Thread Pitch Converter</h1>
            <p class="lead small text-muted mb-4">Enter thread pitch and instantly convert mm pitch to TPI and TPI to mm pitch.</p>
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-md-6 mb-3"><label for="pitchMm" class="form-label fw-semibold">Metric Pitch (mm)</label><input type="number" class="form-control" id="pitchMm" value="1.5" step="any"></div>
                        <div class="col-md-6 mb-3"><label for="tpiIn" class="form-label fw-semibold">Or enter TPI</label><input type="number" class="form-control" id="tpiIn" placeholder="e.g. 16" step="any"></div>
                    </div>
                    <div class="alert alert-info mb-0">
                        <div class="fw-bold" id="tpOut">—</div>
                        <div class="small" id="tpDetail"></div>
                    </div>
                </div>
            </div>
            <div class="card shadow-sm mt-4">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol class="mb-3">
                        <li>Enter your value in the boxes above or choose an option.</li>
                        <li>The result updates live at once — you do not need to press any button.</li>
                        <li>If you change the value or unit, the new result shows on its own.</li>
                    </ol>
                    <h3 class="h6">Note</h3>
                    <p class="small text-muted mb-0">TPI = 25.4 / pitch (mm). Common metric coarse pitches: M6 = 1.0 mm, M8 = 1.25 mm, M10 = 1.5 mm, M12 = 1.75 mm. Bolt size is made from diameter and pitch together.</p>
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
    var mmEl = document.getElementById("pitchMm"), tpiEl = document.getElementById("tpiIn");
    var o = document.getElementById("tpOut"), det = document.getElementById("tpDetail");
    var standard = [0.5, 0.6, 0.7, 0.75, 0.8, 1, 1.25, 1.5, 1.75, 2, 2.5, 3, 3.5, 4];
    function nearestStd(p) {
        var best = standard[0], bd = 1e9;
        standard.forEach(function (s) { var d = Math.abs(s - p); if (d < bd) { bd = d; best = s; } });
        return bd / p < 0.03 ? best : null;
    }
    function fromMm() {
        var p = parseFloat(mmEl.value);
        if (isNaN(p) || p <= 0) { o.textContent = "—"; det.textContent = "Enter a correct pitch."; return; }
        o.textContent = "Pitch " + fmt(p) + " mm = " + fmt(25.4 / p) + " TPI";
        var ns = nearestStd(p);
        det.textContent = "Threads per cm: " + fmt(10 / p) + (ns ? " — This is a standard metric pitch (" + ns + " mm)." : " — This does not exactly match standard metric pitches.");
    }
    function fromTpi() {
        var t = parseFloat(tpiEl.value);
        if (isNaN(t) || t <= 0) { fromMm(); return; }
        var p = 25.4 / t;
        o.textContent = fmt(t) + " TPI = pitch " + fmt(p) + " mm";
        det.textContent = "Threads per cm: " + fmt(10 / p) + ". For imperial threads (UNC/UNF) the pitch often comes out as an odd value in mm — that is normal.";
    }
    mmEl.addEventListener("input", function () { tpiEl.value = ""; fromMm(); });
    tpiEl.addEventListener("input", fromTpi);
    fromMm();
})();
</script>
@endsection
