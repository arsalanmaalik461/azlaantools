@extends('layouts.app')

@section('title', 'Ideal Weight Calculator — Free Online Tool')
@section('meta_description', 'Find your ideal body weight using Devine, Robinson, Miller and Hamwi formulas side by side')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Ideal Weight Calculator</h1>
            <p class="lead small text-muted">Four classic height-based formulas, shown side by side so you can see the range they suggest rather than a single magic number.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3"><label class="form-label" for="height">Height (cm)</label><input type="number" class="form-control" id="height" value="170" step="any"></div>                    <div class="mb-3"><label class="form-label" for="sex">Sex</label><select class="form-select" id="sex"><option value="male">Male</option><option value="female">Female</option></select></div>
                    <div id="result" class="border rounded p-3 mt-3">Enter your details above and the result will appear here.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Enter your height in centimetres.</li><li>Choose your sex.</li><li>All four formula results appear instantly.</li></ol>
                    <h2 class="h5">Note</h2>
                    <p class="small text-muted mb-0">All four formulas start from a base weight at 5 feet (152.4 cm) and add a fixed amount per extra inch. They are population formulas from drug-dosing and actuarial work, not personal targets. Estimate only — not medical advice.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    function num(id) { var v = parseFloat(document.getElementById(id).value); return isFinite(v) ? v : NaN; }
    function fmt(n, d) { return Number(n).toLocaleString("en-US", { minimumFractionDigits: d, maximumFractionDigits: d }); }
    function bind(ids, fn) { ids.forEach(function (id) { var el = document.getElementById(id); el.addEventListener("input", fn); el.addEventListener("change", fn); }); }
    function out(html) { document.getElementById("result").innerHTML = html; }
    function calc() {
        var h = num("height"), sex = document.getElementById("sex").value;
        if (isNaN(h) || h <= 0) { out("Please enter a valid height."); return; }
        var over = (h - 152.4) / 2.54;
        var devine, robinson, miller, hamwi;
        if (sex === "male") { devine = 50 + 2.3 * over; robinson = 52 + 1.9 * over; miller = 56.2 + 1.41 * over; hamwi = 48 + 2.7 * over; }
        else { devine = 45.5 + 2.3 * over; robinson = 49 + 1.7 * over; miller = 53.1 + 1.36 * over; hamwi = 45.5 + 2.2 * over; }
        out("<strong>Devine:</strong> " + fmt(devine, 1) + " kg<br><strong>Robinson:</strong> " + fmt(robinson, 1) + " kg<br><strong>Miller:</strong> " + fmt(miller, 1) + " kg<br><strong>Hamwi:</strong> " + fmt(hamwi, 1) + " kg");
    }
    bind(["height", "sex"], calc); calc();
})();
</script>
@endsection
