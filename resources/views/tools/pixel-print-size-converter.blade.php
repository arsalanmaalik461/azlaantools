@extends('layouts.app')

@section('title', 'Pixel to Print Size Converter — Free Online Tool')
@section('meta_description', 'Enter image pixels and DPI and instantly get the print size in inches and cm.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-2">Pixel to Print Size Converter</h1>
            <p class="lead small text-muted mb-4">Enter image pixels and DPI and instantly get the print size in inches and cm.</p>
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-md-4 mb-3"><label for="pxW" class="form-label fw-semibold">Width (pixels)</label><input type="number" class="form-control" id="pxW" value="3000" step="any"></div>
                        <div class="col-md-4 mb-3"><label for="pxH" class="form-label fw-semibold">Height (pixels)</label><input type="number" class="form-control" id="pxH" value="2000" step="any"></div>
                        <div class="col-md-4 mb-3"><label for="dpi" class="form-label fw-semibold">DPI</label><input type="number" class="form-control" id="dpi" value="300" step="any"></div>
                    </div>
                    <div class="alert alert-info mb-0">
                        <div class="fw-bold" id="printOut">—</div>
                        <div class="small" id="printDetail"></div>
                    </div>
                </div>
            </div>
            <div class="card shadow-sm mt-4">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol class="mb-3">
                        <li>Enter your value in the boxes above or select an option.</li>
                        <li>The result updates live right away — no button needed.</li>
                        <li>Change the value or unit and the new result shows automatically.</li>
                    </ol>
                    <h3 class="h6">Note</h3>
                    <p class="small text-muted mb-0">Print size = pixels / DPI. For a good quality photo print use 300 DPI, for a big poster 150 DPI, and for screens 72–96 DPI is common.</p>
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
        var w = parseFloat(document.getElementById("pxW").value);
        var h = parseFloat(document.getElementById("pxH").value);
        var d = parseFloat(document.getElementById("dpi").value);
        var o = document.getElementById("printOut"), det = document.getElementById("printDetail");
        if (isNaN(w) || isNaN(h) || isNaN(d) || d <= 0 || w <= 0 || h <= 0) { o.textContent = "—"; det.textContent = "Please enter valid pixels and DPI."; return; }
        var wi = w / d, hi = h / d;
        o.textContent = "Print size: " + fmt(wi) + " x " + fmt(hi) + " inch";
        det.textContent = "In centimetres: " + fmt(wi * 2.54) + " x " + fmt(hi * 2.54) + " cm. At 150 DPI this image prints " + fmt(w / 150) + " x " + fmt(h / 150) + " inch.";
    }
    ["pxW", "pxH", "dpi"].forEach(function (id) { document.getElementById(id).addEventListener("input", calc); });
    calc();
})();
</script>
@endsection
