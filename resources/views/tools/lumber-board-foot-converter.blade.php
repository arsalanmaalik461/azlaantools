@extends('layouts.app')

@section('title', 'Lumber Board Foot Converter — Free Online Tool')
@section('meta_description', 'Enter wood dimensions and instantly convert to board feet, cubic feet and cubic metres.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-2">Lumber Board Foot Converter</h1>
            <p class="lead small text-muted mb-4">Enter wood dimensions and instantly convert to board feet, cubic feet and cubic metres.</p>
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-md-3 mb-3"><label for="thk" class="form-label fw-semibold">Thickness (inch)</label><input type="number" class="form-control" id="thk" value="2" step="any"></div>
                        <div class="col-md-3 mb-3"><label for="wid" class="form-label fw-semibold">Width (inch)</label><input type="number" class="form-control" id="wid" value="6" step="any"></div>
                        <div class="col-md-3 mb-3"><label for="len" class="form-label fw-semibold">Length (feet)</label><input type="number" class="form-control" id="len" value="10" step="any"></div>
                        <div class="col-md-3 mb-3"><label for="qty" class="form-label fw-semibold">Pieces</label><input type="number" class="form-control" id="qty" value="1" step="any"></div>
                    </div>
                    <div class="alert alert-info text-center mb-0">
                        <div class="fs-5 fw-bold" id="bfOut">—</div>
                        <div class="small" id="cfOut"></div>
                    </div>
                </div>
            </div>
            <div class="card shadow-sm mt-4">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol class="mb-3">
                        <li>Enter your values in the boxes above or select an option.</li>
                        <li>The result updates live — no button to press.</li>
                        <li>Change a value or unit and the new result shows by itself.</li>
                    </ol>
                    <h3 class="h6">Note</h3>
                    <p class="small text-muted mb-0">Board Foot = thickness (in) x width (in) x length (ft) / 12. 1 board foot = 144 cubic inch, and 12 board feet = 1 cubic foot. In timber markets the price is usually set per cubic foot.</p>
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
    var ids = ["thk", "wid", "len", "qty"];
    function calc() {
        var t = parseFloat(document.getElementById("thk").value);
        var w = parseFloat(document.getElementById("wid").value);
        var l = parseFloat(document.getElementById("len").value);
        var q = parseFloat(document.getElementById("qty").value);
        var bfEl = document.getElementById("bfOut"), cfEl = document.getElementById("cfOut");
        if (isNaN(t) || isNaN(w) || isNaN(l) || isNaN(q) || t <= 0 || w <= 0 || l <= 0 || q <= 0) {
            bfEl.textContent = "—"; cfEl.textContent = "Enter correct dimensions."; return;
        }
        var bf = t * w * l / 12 * q, cf = bf / 12, cm = cf * 0.0283168466;
        bfEl.textContent = "Board Feet: " + fmt(bf) + " BF";
        cfEl.textContent = "Cubic Feet: " + fmt(cf) + " cft — Cubic Metre: " + fmt(cm) + " m3 — Per piece: " + fmt(bf / q) + " BF";
    }
    ids.forEach(function (id) { document.getElementById(id).addEventListener("input", calc); });
    calc();
})();
</script>
@endsection
