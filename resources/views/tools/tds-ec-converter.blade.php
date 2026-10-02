@extends('layouts.app')

@section('title', 'TDS EC Converter — Free Online Tool')
@section('meta_description', 'Enter a TDS ppm or EC value and instantly see the conversion across different meter scales.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-2">TDS EC Converter</h1>
            <p class="lead small text-muted mb-4">Enter a TDS ppm or EC value and instantly see the conversion across different meter scales.</p>
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-md-6 mb-3"><label for="ecIn" class="form-label fw-semibold">EC (uS/cm)</label><input type="number" class="form-control" id="ecIn" value="1000" step="any"></div>
                        <div class="col-md-6 mb-3"><label for="ecScale" class="form-label fw-semibold">Meter Scale</label>
                            <select class="form-select" id="ecScale"><option value="0.5" selected>500 scale (NaCl) — factor 0.50</option><option value="0.64">640 scale (442) — factor 0.64</option><option value="0.7">700 scale (KCl) — factor 0.70</option></select></div>
                        <div class="col-md-6 mb-3"><label for="ppmIn" class="form-label fw-semibold">Or enter TDS (ppm)</label><input type="number" class="form-control" id="ppmIn" placeholder="e.g. 500" step="any"></div>
                    </div>
                    <div class="alert alert-info mb-0">
                        <div class="fw-bold" id="tdsOut">—</div>
                        <div class="small" id="tdsDetail"></div>
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
                    <p class="small text-muted mb-0">A TDS meter actually measures EC and then makes ppm from it with a factor — that is why the same water gives a different ppm reading on different scales. Confirm your meter scale from its manual.</p>
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
    var ecEl = document.getElementById("ecIn"), ppmEl = document.getElementById("ppmIn"), scEl = document.getElementById("ecScale");
    var o = document.getElementById("tdsOut"), det = document.getElementById("tdsDetail");
    function fromEC() {
        var ec = parseFloat(ecEl.value), f = parseFloat(scEl.value);
        if (isNaN(ec) || ec < 0) { o.textContent = "—"; det.textContent = "Enter a correct EC value."; return; }
        o.textContent = "TDS = " + fmt(ec * f) + " ppm — EC = " + fmt(ec) + " uS/cm (" + fmt(ec / 1000) + " mS/cm)";
        det.textContent = "On all three scales: 500 scale = " + fmt(ec * 0.5) + " ppm, 640 scale = " + fmt(ec * 0.64) + " ppm, 700 scale = " + fmt(ec * 0.7) + " ppm.";
    }
    function fromPPM() {
        var ppm = parseFloat(ppmEl.value), f = parseFloat(scEl.value);
        if (isNaN(ppm)) { fromEC(); return; }
        if (ppm < 0) { o.textContent = "—"; det.textContent = "Enter a correct ppm value."; return; }
        var ec = ppm / f;
        o.textContent = "EC = " + fmt(ec) + " uS/cm (" + fmt(ec / 1000) + " mS/cm) — TDS = " + fmt(ppm) + " ppm";
        det.textContent = "EC is worked out from the selected scale (factor " + f + ").";
    }
    ecEl.addEventListener("input", function () { ppmEl.value = ""; fromEC(); });
    ppmEl.addEventListener("input", fromPPM);
    scEl.addEventListener("change", function () { if (ppmEl.value) { fromPPM(); } else { fromEC(); } });
    fromEC();
})();
</script>
@endsection
