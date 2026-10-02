@extends('layouts.app')

@section('title', 'Brine Calculator — Free Online Tool')
@section('meta_description', 'Calculate salt and water amounts for wet and dry brines by meat weight.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h1 class="h3">Brine Calculator</h1>
                    <p class="lead small text-muted">Get exact salt amounts for a wet brine (salt dissolved in water) or a dry brine (salt rubbed on the meat), by meat weight and target salt percentage.</p>
                    <div class="row g-3">
                        <div class="col-md-4"><label class="form-label" for="brMeat">Meat weight (kg)</label><input type="number" class="form-control" id="brMeat" value="2" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="brMode">Brine type</label><select class="form-select" id="brMode"><option value="wet">Wet brine</option><option value="dry">Dry brine</option></select></div>
                        <div class="col-md-4"><label class="form-label" for="brPct">Salt percentage (%)</label><input type="number" class="form-control" id="brPct" value="5" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="brWaterPerKg">Water per kg meat (litres, wet brine)</label><input type="number" class="form-control" id="brWaterPerKg" value="1" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="brSugar">Add sugar (% of salt weight)</label><input type="number" class="form-control" id="brSugar" value="50" min="0" step="any"></div>
                    </div>                    <div class="alert alert-info mt-3 mb-0" id="brOut">Enter values to see the result.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol>
                        <li>Enter the meat weight in kilograms.</li>
                        <li>Choose wet or dry brine and set the salt percentage (wet brine is commonly 5 to 6 percent of the water, dry brine about 1 percent of the meat weight).</li>
                        <li>Read the salt, water and optional sugar amounts.</li>
                    </ol>
                    <p class="small text-muted mb-0">For a wet equilibrium style estimate on this page, salt is calculated as a percentage of the water weight. For dry brining, salt is a percentage of the meat weight. Always refrigerate while brining and follow food safety times for the cut of meat.</p>
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
    function el(id) { return document.getElementById(id); }
    function num(id) { var v = parseFloat(el(id).value); return isNaN(v) ? 0 : v; }
    function calc() {
        var meat = num("brMeat"), pct = num("brPct"), mode = el("brMode").value, wpk = num("brWaterPerKg"), sugarPct = num("brSugar");
        var out = el("brOut");
        if (meat <= 0 || pct <= 0) { out.textContent = "Please enter a meat weight and salt percentage greater than zero."; return; }
        if (mode === "wet") {
            var waterL = meat * wpk;
            var salt = waterL * 1000 * pct / 100;
            var sugar = salt * sugarPct / 100;
            out.innerHTML = "<strong>Water:</strong> " + waterL.toFixed(2) + " litres &nbsp; <strong>Salt:</strong> " + salt.toFixed(0) + " g (" + (salt / 17).toFixed(1) + " tbsp approx.) &nbsp; <strong>Sugar (optional):</strong> " + sugar.toFixed(0) + " g<br>Dissolve the salt fully in the water, chill the brine, then submerge the meat and refrigerate.";
        } else {
            var dsalt = meat * 1000 * pct / 100;
            var dsugar = dsalt * sugarPct / 100;
            out.innerHTML = "<strong>Salt:</strong> " + dsalt.toFixed(1) + " g (" + (dsalt / 5).toFixed(1) + " tsp approx.) &nbsp; <strong>Sugar (optional):</strong> " + dsugar.toFixed(1) + " g<br>Rub evenly over all surfaces, place on a rack in the fridge uncovered for crispy skin, typically 12 to 24 hours per 2 kg.";
        }
    }
    ["brMeat", "brMode", "brPct", "brWaterPerKg", "brSugar"].forEach(function (id) { el(id).addEventListener("input", calc); el(id).addEventListener("change", calc); });
    calc();
})();
</script>
@endsection
