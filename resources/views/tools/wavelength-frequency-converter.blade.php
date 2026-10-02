@extends('layouts.app')

@section('title', 'Wavelength Frequency Converter — Free Online Tool')
@section('meta_description', 'Enter frequency or wavelength and instantly calculate both for light and radio waves.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-2">Wavelength Frequency Converter</h1>
            <p class="lead small text-muted mb-4">Enter frequency or wavelength and instantly calculate both for light and radio waves.</p>
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-md-6 mb-3"><label for="freqVal" class="form-label fw-semibold">Frequency</label><input type="number" class="form-control" id="freqVal" value="100" step="any"></div>
                        <div class="col-md-6 mb-3"><label for="freqUnit" class="form-label fw-semibold">Frequency Unit</label>
                            <select class="form-select" id="freqUnit"><option value="1">Hz</option><option value="1000">kHz</option><option value="1000000" selected>MHz</option><option value="1000000000">GHz</option><option value="1000000000000">THz</option></select></div>
                        <div class="col-md-6 mb-3"><label for="waveVal" class="form-label fw-semibold">Or enter Wavelength (metre)</label><input type="number" class="form-control" id="waveVal" placeholder="e.g. 3" step="any"></div>
                    </div>
                    <div class="alert alert-info mb-0">
                        <div class="fw-bold" id="wfOut">—</div>
                        <div class="small" id="wfDetail"></div>
                    </div>
                </div>
            </div>
            <div class="card shadow-sm mt-4">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol class="mb-3">
                        <li>Enter your value in the boxes above or select an option.</li>
                        <li>The result updates live at once — no need to press any button.</li>
                        <li>Change the value or unit and the new result will appear automatically.</li>
                    </ol>
                    <h3 class="h6">Note</h3>
                    <p class="small text-muted mb-0">Formula: wavelength = c / f, where c is the speed of light in vacuum, 299,792,458 m/s. FM radio at 100 MHz has a wavelength of about 3 metres, and WiFi at 2.4 GHz has a wavelength of about 12.5 cm.</p>
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
    var C = 299792458;
    var fEl = document.getElementById("freqVal"), uEl = document.getElementById("freqUnit"), wEl = document.getElementById("waveVal");
    var o = document.getElementById("wfOut"), det = document.getElementById("wfDetail");
    function show(freq) {
        var lam = C / freq;
        var lamStr = lam >= 1 ? fmt(lam) + " m" : (lam >= 0.01 ? fmt(lam * 100) + " cm" : fmt(lam * 1000) + " mm");
        o.textContent = "Frequency: " + fmt(freq) + " Hz — Wavelength: " + lamStr;
        det.textContent = "Wavelength in metres: " + fmt(lam) + " m. Quarter-wave length for an antenna: " + fmt(lam / 4) + " m.";
    }
    function fromFreq() {
        var f = parseFloat(fEl.value) * parseFloat(uEl.value);
        if (isNaN(f) || f <= 0) { o.textContent = "—"; det.textContent = "Please enter a valid frequency."; return; }
        show(f);
    }
    function fromWave() {
        var w = parseFloat(wEl.value);
        if (isNaN(w) || w <= 0) { fromFreq(); return; }
        show(C / w);
    }
    fEl.addEventListener("input", function () { wEl.value = ""; fromFreq(); });
    uEl.addEventListener("change", function () { wEl.value = ""; fromFreq(); });
    wEl.addEventListener("input", fromWave);
    fromFreq();
})();
</script>
@endsection
