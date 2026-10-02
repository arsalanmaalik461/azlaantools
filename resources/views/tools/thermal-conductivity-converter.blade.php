@extends('layouts.app')

@section('title', 'Thermal Conductivity Converter — Free Online Tool')
@section('meta_description', 'Enter thermal conductivity and convert between W per mK, BTU units and calorie units.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-2">Thermal Conductivity Converter</h1>
            <p class="lead small text-muted mb-4">Enter thermal conductivity and convert between W per mK, BTU units and calorie units.</p>
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="val" class="form-label fw-semibold">Value</label>
                        <input type="number" class="form-control form-control-lg" id="val" value="1" step="any" placeholder="Enter value">
                    </div>
                    <div class="row g-2 align-items-end mb-3">
                        <div class="col-5">
                            <label for="fromU" class="form-label fw-semibold">From</label>
                            <select class="form-select" id="fromU"></select>
                        </div>
                        <div class="col-2 text-center">
                            <button type="button" class="btn btn-outline-secondary w-100" id="swapBtn" title="Swap units">&#8646;</button>
                        </div>
                        <div class="col-5">
                            <label for="toU" class="form-label fw-semibold">To</label>
                            <select class="form-select" id="toU"></select>
                        </div>
                    </div>
                    <div class="alert alert-info text-center mb-0">
                        <div class="fs-4 fw-bold" id="out">—</div>
                        <div class="small" id="rate"></div>
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
                    <p class="small text-muted mb-0">Lower value means better insulation. For example, foam has conductivity of about 0.03 W/m·K and concrete about 1 W/m·K.</p>
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
    var units = {
            wmk: { label: "Watt per Metre Kelvin (W/m·K)", factor: 1 },
            btuh: { label: "BTU per Hour Foot Fahrenheit", factor: 1.73073467 },
            btuin: { label: "BTU Inch per Hour Square Foot Fahrenheit", factor: 0.144227889 },
            cal: { label: "Calorie per Second Centimetre Celsius", factor: 418.68 },
            kcal: { label: "Kilocalorie per Hour Metre Celsius", factor: 1.163 }
    };
    var valEl = document.getElementById("val");
    var fromEl = document.getElementById("fromU");
    var toEl = document.getElementById("toU");
    var outEl = document.getElementById("out");
    var rateEl = document.getElementById("rate");
    function fmt(n) {
        if (n === null || n === undefined || !isFinite(n)) return "—";
        if (n !== 0 && (Math.abs(n) >= 1e12 || Math.abs(n) < 1e-6)) return n.toExponential(6);
        return parseFloat(n.toFixed(8)).toLocaleString("en-US", { maximumFractionDigits: 8 });
    }
    Object.keys(units).forEach(function (k) {
        var o1 = document.createElement("option"); o1.value = k; o1.textContent = units[k].label; fromEl.appendChild(o1);
        var o2 = document.createElement("option"); o2.value = k; o2.textContent = units[k].label; toEl.appendChild(o2);
    });
    var keys = Object.keys(units);
    fromEl.value = keys[0]; toEl.value = keys[1];
    function convert() {
        var v = parseFloat(valEl.value);
        if (isNaN(v)) { outEl.textContent = "—"; rateEl.textContent = "Enter a value."; return; }
        var r = v * units[fromEl.value].factor / units[toEl.value].factor;
        outEl.textContent = fmt(v) + " " + units[fromEl.value].label + " = " + fmt(r) + " " + units[toEl.value].label;
        rateEl.textContent = "1 " + units[fromEl.value].label + " = " + fmt(units[fromEl.value].factor / units[toEl.value].factor) + " " + units[toEl.value].label;
    }
    valEl.addEventListener("input", convert);
    fromEl.addEventListener("change", convert);
    toEl.addEventListener("change", convert);
    document.getElementById("swapBtn").addEventListener("click", function () {
        var tmp = fromEl.value; fromEl.value = toEl.value; toEl.value = tmp; convert();
    });
    convert();
})();
</script>
@endsection
