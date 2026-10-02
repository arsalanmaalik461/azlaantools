@extends('layouts.app')

@section('title', 'Temperature Converter — Free Online Tool')
@section('meta_description', 'Enter the temperature and instantly convert between Celsius, Fahrenheit and Kelvin.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-2">Temperature Converter</h1>
            <p class="lead small text-muted mb-4">Enter the temperature and instantly convert between Celsius, Fahrenheit and Kelvin.</p>
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="mb-3"><label for="tempVal" class="form-label fw-semibold">Temperature</label><input type="number" class="form-control form-control-lg" id="tempVal" value="37" step="any"></div>
                    <div class="row g-2 align-items-end mb-3">
                        <div class="col-5"><label for="tempFrom" class="form-label fw-semibold">From</label>
                            <select class="form-select" id="tempFrom"><option value="c" selected>Celsius (°C)</option><option value="f">Fahrenheit (°F)</option><option value="k">Kelvin (K)</option><option value="r">Rankine (°R)</option></select></div>
                        <div class="col-2 text-center"><button type="button" class="btn btn-outline-secondary w-100" id="tempSwap" title="Swap units">&#8646;</button></div>
                        <div class="col-5"><label for="tempTo" class="form-label fw-semibold">To</label>
                            <select class="form-select" id="tempTo"><option value="c">Celsius (°C)</option><option value="f" selected>Fahrenheit (°F)</option><option value="k">Kelvin (K)</option><option value="r">Rankine (°R)</option></select></div>
                    </div>
                    <div class="alert alert-info text-center mb-0">
                        <div class="fs-4 fw-bold" id="tempOut">—</div>
                        <div class="small" id="tempAll"></div>
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
                    <p class="small text-muted mb-0">Formulas: °F = °C x 9/5 + 32, K = °C + 273.15. Normal human body temperature is 37°C = 98.6°F, and water boils at 100°C = 212°F (at sea level).</p>
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
    var valEl = document.getElementById("tempVal"), fromEl = document.getElementById("tempFrom"), toEl = document.getElementById("tempTo");
    var outEl = document.getElementById("tempOut"), allEl = document.getElementById("tempAll");
    var labels = { c: "°C", f: "°F", k: "K", r: "°R" };
    function toC(v, u) { if (u === "c") return v; if (u === "f") return (v - 32) * 5 / 9; if (u === "k") return v - 273.15; return (v - 491.67) * 5 / 9; }
    function fromC(v, u) { if (u === "c") return v; if (u === "f") return v * 9 / 5 + 32; if (u === "k") return v + 273.15; return (v + 273.15) * 9 / 5; }
    function calc() {
        var v = parseFloat(valEl.value);
        if (isNaN(v)) { outEl.textContent = "—"; allEl.textContent = "Enter a value."; return; }
        var c = toC(v, fromEl.value);
        if (c < -273.15 - 0.001) { outEl.textContent = "—"; allEl.textContent = "Temperature below absolute zero (-273.15 °C) is not possible."; return; }
        var r = fromC(c, toEl.value);
        outEl.textContent = fmt(v) + " " + labels[fromEl.value] + " = " + fmt(r) + " " + labels[toEl.value];
        allEl.textContent = "Celsius: " + fmt(c) + " °C — Fahrenheit: " + fmt(fromC(c, "f")) + " °F — Kelvin: " + fmt(fromC(c, "k")) + " K — Rankine: " + fmt(fromC(c, "r")) + " °R";
    }
    valEl.addEventListener("input", calc);
    fromEl.addEventListener("change", calc);
    toEl.addEventListener("change", calc);
    document.getElementById("tempSwap").addEventListener("click", function () { var t = fromEl.value; fromEl.value = toEl.value; toEl.value = t; calc(); });
    calc();
})();
</script>
@endsection
