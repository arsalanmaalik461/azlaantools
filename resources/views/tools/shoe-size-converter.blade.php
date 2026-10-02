@extends('layouts.app')

@section('title', 'Shoe Size Converter — Free Online Tool')
@section('meta_description', 'Enter your shoe size and instantly convert to UK, US, EU sizes and cm foot length.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-2">Shoe Size Converter</h1>
            <p class="lead small text-muted mb-4">Enter your shoe size and instantly convert to UK, US, EU sizes and cm foot length.</p>
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-md-6 mb-3"><label for="footCm" class="form-label fw-semibold">Foot Length (cm)</label><input type="number" class="form-control" id="footCm" value="27" step="any"></div>
                        <div class="col-md-6 mb-3"><label for="gender" class="form-label fw-semibold">Category</label>
                            <select class="form-select" id="gender"><option value="men" selected>Men</option><option value="women">Women</option></select></div>
                    </div>
                    <div class="alert alert-info mb-0">
                        <div class="fw-bold" id="shoeOut">—</div>
                        <div class="small" id="shoeDetail"></div>
                    </div>
                </div>
            </div>
            <div class="card shadow-sm mt-4">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol class="mb-3">
                        <li>Enter your value in the boxes above or select an option.</li>
                        <li>The result updates live — no need to press any button.</li>
                        <li>Change the value or unit and the new result appears automatically.</li>
                    </ol>
                    <h3 class="h6">Note</h3>
                    <p class="small text-muted mb-0">Sizes are taken from the closest row of a well-known international chart. Every brand's sizing can differ slightly — always check the brand's own size chart before ordering. Measure your foot length while standing, in the evening.</p>
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
    // rows: [footCm, eu, ukMen, usMen, ukWomen, usWomen]
    var chart = [
        [22.7, 36, 3, 3.5, 3, 5], [23.2, 36.5, 3.5, 4, 3.5, 5.5], [23.7, 37.5, 4, 4.5, 4, 6],
        [24.1, 38, 4.5, 5, 4.5, 6.5], [24.5, 38.5, 5, 5.5, 5, 7], [24.9, 39.5, 5.5, 6, 5.5, 7.5],
        [25.4, 40, 6, 6.5, 6, 8], [25.8, 40.5, 6.5, 7, 6.5, 8.5], [26.2, 41, 7, 7.5, 7, 9],
        [26.7, 42, 7.5, 8, 7.5, 9.5], [27.1, 42.5, 8, 8.5, 8, 10], [27.5, 43, 8.5, 9, 8.5, 10.5],
        [27.9, 44, 9, 9.5, 9, 11], [28.3, 44.5, 9.5, 10, 9.5, 11.5], [28.8, 45, 10, 10.5, 10, 12],
        [29.2, 45.5, 10.5, 11, 10.5, 12.5], [29.6, 46.5, 11, 11.5, 11, 13], [30.1, 47, 11.5, 12, 11.5, 13.5],
        [30.5, 48, 12, 12.5, 12, 14]
    ];
    function fmt(n) {
        if (n === null || n === undefined || !isFinite(n)) return "—";
        if (n !== 0 && (Math.abs(n) >= 1e12 || Math.abs(n) < 1e-6)) return n.toExponential(6);
        return parseFloat(n.toFixed(8)).toLocaleString("en-US", { maximumFractionDigits: 8 });
    }
    function calc() {
        var cm = parseFloat(document.getElementById("footCm").value);
        var g = document.getElementById("gender").value;
        var o = document.getElementById("shoeOut"), det = document.getElementById("shoeDetail");
        if (isNaN(cm) || cm < 15 || cm > 35) { o.textContent = "—"; det.textContent = "Enter a foot length between 15–35 cm."; return; }
        var best = chart[0], bd = 1e9;
        chart.forEach(function (r) { var d = Math.abs(r[0] - cm); if (d < bd) { bd = d; best = r; } });
        var uk = g === "men" ? best[2] : best[4];
        var us = g === "men" ? best[3] : best[5];
        o.textContent = "EU " + best[1] + " — UK " + uk + " — US " + us + (g === "men" ? " (Men)" : " (Women)");
        det.textContent = "Closest chart row: foot " + best[0] + " cm. Your entered length: " + fmt(cm) + " cm.";
    }
    document.getElementById("footCm").addEventListener("input", calc);
    document.getElementById("gender").addEventListener("change", calc);
    calc();
})();
</script>
@endsection
