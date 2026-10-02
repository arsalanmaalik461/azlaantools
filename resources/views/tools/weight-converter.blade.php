@extends('layouts.app')

@section('title', 'Weight Converter — Free Online Tool')
@section('meta_description', 'Enter a weight and convert it instantly between kilogram, gram, pound, ounce, stone and tonne.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-2">Weight Converter</h1>
            <p class="lead small text-muted mb-4">Enter a weight and convert it instantly between kilogram, gram, pound, ounce, stone and tonne.</p>
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
                        <li>Enter your value in the boxes above or select an option.</li>
                        <li>The result updates live right away — no button needed.</li>
                        <li>Change the value or unit and the new result shows on its own.</li>
                    </ol>
                    <h3 class="h6">Note</h3>
                    <p class="small text-muted mb-0">1 pound = 453.59237 grams exactly. Stone is mostly used in the UK for body weight (1 stone = 14 lb).</p>
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
            mg: { label: "Milligram (mg)", factor: 0.001 },
            g: { label: "Gram (g)", factor: 1 },
            kg: { label: "Kilogram (kg)", factor: 1000 },
            tonne: { label: "Tonne (metric)", factor: 1000000 },
            oz: { label: "Ounce (oz)", factor: 28.349523125 },
            lb: { label: "Pound (lb)", factor: 453.59237 },
            stone: { label: "Stone (st)", factor: 6350.29318 },
            uston: { label: "US Ton (short)", factor: 907184.74 },
            ukton: { label: "UK Ton (long)", factor: 1016046.9088 },
            carat: { label: "Carat (ct)", factor: 0.2 }
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
    fromEl.value = keys[2]; toEl.value = keys[5];
    function convert() {
        var v = parseFloat(valEl.value);
        if (isNaN(v)) { outEl.textContent = "—"; rateEl.textContent = "Please enter a value."; return; }
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
