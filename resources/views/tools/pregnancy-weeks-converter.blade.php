@extends('layouts.app')

@section('title', 'Pregnancy Weeks to Months Converter — Free Online Tool')
@section('meta_description', 'Enter weeks and instantly convert your pregnancy to months and trimester.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-2">Pregnancy Weeks to Months Converter</h1>
            <p class="lead small text-muted mb-4">Enter weeks and instantly convert your pregnancy to months and trimester.</p>
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-md-6 mb-3"><label for="pgWeeks" class="form-label fw-semibold">Weeks</label><input type="number" class="form-control" id="pgWeeks" value="20" step="any" min="0" max="45"></div>
                        <div class="col-md-6 mb-3"><label for="pgDays" class="form-label fw-semibold">Extra Days (0–6)</label><input type="number" class="form-control" id="pgDays" value="0" step="1" min="0" max="6"></div>
                    </div>
                    <div class="alert alert-info mb-0">
                        <div class="fw-bold" id="pgOut">—</div>
                        <div class="small" id="pgDetail"></div>
                    </div>
                </div>
            </div>
            <div class="card shadow-sm mt-4">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol class="mb-3">
                        <li>Enter your value in the box above or select an option.</li>
                        <li>The result updates live at once — no button needed.</li>
                        <li>When you change the value or units, the new result shows automatically.</li>
                    </ol>
                    <h3 class="h6">Note</h3>
                    <p class="small text-muted mb-0">Pregnancy is usually counted as 40 weeks (280 days). The month count is approximate because months have different lengths — month = total days / 30.44. Estimate only — not medical advice. Confirm your real progress with your doctor.</p>
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
        var w = parseFloat(document.getElementById("pgWeeks").value);
        var d = parseFloat(document.getElementById("pgDays").value);
        var o = document.getElementById("pgOut"), det = document.getElementById("pgDetail");
        if (isNaN(w) || w < 0 || w > 45 || isNaN(d) || d < 0 || d > 6) { o.textContent = "—"; det.textContent = "Enter weeks 0–45 and days 0–6."; return; }
        var totalDays = w * 7 + d;
        var months = totalDays / 30.4375;
        var tri = w < 14 ? "First Trimester (weeks 1–13)" : (w < 28 ? "Second Trimester (weeks 14–27)" : "Third Trimester (weeks 28–40+)");
        var fullMonths = Math.floor(months);
        o.textContent = "About " + fmt(months) + " months — " + tri;
        det.textContent = "Total days: " + totalDays + " — Full months: " + fullMonths + " — Weeks left until 40 weeks: " + fmt(Math.max(0, 40 - w - d / 7)) + ". Estimate only — not medical advice.";
    }
    ["pgWeeks", "pgDays"].forEach(function (id) { document.getElementById(id).addEventListener("input", calc); });
    calc();
})();
</script>
@endsection
