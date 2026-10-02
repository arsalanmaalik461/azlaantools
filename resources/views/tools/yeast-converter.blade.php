@extends('layouts.app')

@section('title', 'Yeast Converter — Free Online Tool')
@section('meta_description', 'Enter the yeast amount and convert instantly between fresh, instant dry and active dry yeast.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-2">Yeast Converter</h1>
            <p class="lead small text-muted mb-4">Enter the yeast amount and convert instantly between fresh, instant dry and active dry yeast.</p>
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-md-6 mb-3"><label for="yeastAmt" class="form-label fw-semibold">Amount (grams)</label><input type="number" class="form-control" id="yeastAmt" value="30" step="any"></div>
                        <div class="col-md-6 mb-3"><label for="yeastType" class="form-label fw-semibold">Yeast type (the one you have)</label>
                            <select class="form-select" id="yeastType"><option value="fresh" selected>Fresh Yeast</option><option value="active">Active Dry Yeast</option><option value="instant">Instant Dry Yeast</option></select></div>
                    </div>
                    <div class="alert alert-info mb-0">
                        <div class="fw-bold" id="yeastOut">—</div>
                        <div class="small" id="yeastDetail"></div>
                    </div>
                </div>
            </div>
            <div class="card shadow-sm mt-4">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol class="mb-3">
                        <li>Enter your value in the boxes above or select an option.</li>
                        <li>The result updates live at once — no button needed.</li>
                        <li>Change the value or unit and the new result appears by itself.</li>
                    </ol>
                    <h3 class="h6">Note</h3>
                    <p class="small text-muted mb-0">Standard ratios: 100% fresh yeast = about 40% active dry = about 33% instant. For example, instead of 30 g fresh, use 12 g active dry or 10 g instant. Some bakers also use 40% for instant — adjust based on how long the dough takes to rise.</p>
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
    var ratios = { fresh: 1, active: 0.4, instant: 0.33 };
    var labels = { fresh: "Fresh Yeast", active: "Active Dry Yeast", instant: "Instant Dry Yeast" };
    function calc() {
        var amt = parseFloat(document.getElementById("yeastAmt").value);
        var from = document.getElementById("yeastType").value;
        var o = document.getElementById("yeastOut"), det = document.getElementById("yeastDetail");
        if (isNaN(amt) || amt <= 0) { o.textContent = "—"; det.textContent = "Enter a valid amount."; return; }
        var freshEquiv = amt / ratios[from];
        o.textContent = fmt(amt) + " g " + labels[from] + " equals:";
        det.textContent = "Fresh: " + fmt(freshEquiv) + " g — Active Dry: " + fmt(freshEquiv * 0.4) + " g — Instant: " + fmt(freshEquiv * 0.33) + " g. (1 tsp instant yeast is about 3.1 g.)";
    }
    document.getElementById("yeastAmt").addEventListener("input", calc);
    document.getElementById("yeastType").addEventListener("change", calc);
    calc();
})();
</script>
@endsection
