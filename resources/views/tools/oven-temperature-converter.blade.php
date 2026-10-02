@extends('layouts.app')

@section('title', 'Oven Temperature Converter — Free Online Tool')
@section('meta_description', 'Enter your oven setting and convert it to Celsius, Fahrenheit, fan oven and gas mark right away.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-2">Oven Temperature Converter</h1>
            <p class="lead small text-muted mb-4">Enter your oven setting and convert it to Celsius, Fahrenheit, fan oven and gas mark right away.</p>
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="mb-3"><label for="ovenC" class="form-label fw-semibold">Enter temperature in Celsius (°C)</label><input type="number" class="form-control form-control-lg" id="ovenC" value="180" step="any"></div>
                    <div class="row text-center g-2">
                        <div class="col-md-4"><div class="border rounded p-3"><div class="small text-muted">Fahrenheit</div><div class="fs-4 fw-bold" id="ovenF">—</div></div></div>
                        <div class="col-md-4"><div class="border rounded p-3"><div class="small text-muted">Fan Oven (°C)</div><div class="fs-4 fw-bold" id="ovenFan">—</div></div></div>
                        <div class="col-md-4"><div class="border rounded p-3"><div class="small text-muted">Gas Mark</div><div class="fs-4 fw-bold" id="ovenGas">—</div></div></div>
                    </div>
                </div>
            </div>
            <div class="card shadow-sm mt-4">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol class="mb-3">
                        <li>Enter your value in the box above.</li>
                        <li>The result updates live right away — no button press needed.</li>
                        <li>Change the value and the new result appears by itself.</li>
                    </ol>
                    <h3 class="h6">Note</h3>
                    <p class="small text-muted mb-0">Fan oven is usually set 20°C lower than a normal oven. The gas mark chart is standard: Gas 1 = 140°C, Gas 4 = 180°C, Gas 6 = 200°C, Gas 9 = 240°C. Every oven's actual heat may differ slightly.</p>
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
    var gasTable = [[140, 1], [150, 2], [170, 3], [180, 4], [190, 5], [200, 6], [220, 7], [230, 8], [240, 9]];
    function calc() {
        var c = parseFloat(document.getElementById("ovenC").value);
        var fEl = document.getElementById("ovenF"), fanEl = document.getElementById("ovenFan"), gEl = document.getElementById("ovenGas");
        if (isNaN(c)) { fEl.textContent = "—"; fanEl.textContent = "—"; gEl.textContent = "—"; return; }
        fEl.textContent = fmt(c * 9 / 5 + 32) + " °F";
        fanEl.textContent = fmt(c - 20) + " °C";
        var best = "—", bd = 1e9;
        gasTable.forEach(function (row) { var d = Math.abs(row[0] - c); if (d < bd) { bd = d; best = row[1]; } });
        gEl.textContent = bd <= 8 ? ("Gas " + best) : "— (out of range)";
    }
    document.getElementById("ovenC").addEventListener("input", calc);
    calc();
})();
</script>
@endsection
