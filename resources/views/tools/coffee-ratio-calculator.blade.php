@extends('layouts.app')

@section('title', 'Coffee Ratio Calculator — Free Online Tool')
@section('meta_description', 'Calculate coffee grams and water amount for any brew ratio and cup size.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h1 class="h3">Coffee Ratio Calculator</h1>
                    <p class="lead small text-muted">Dial in the golden ratio for pour over, French press or espresso: enter coffee or water and the brew ratio, and get the other amount instantly.</p>
                    <div class="row g-3">
                        <div class="col-md-4"><label class="form-label" for="cfMode">Calculate from</label><select class="form-select" id="cfMode"><option value="water">Water amount</option><option value="coffee">Coffee amount</option></select></div>
                        <div class="col-md-4"><label class="form-label" for="cfAmount">Amount</label><input type="number" class="form-control" id="cfAmount" value="500" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="cfUnit">Unit of amount</label><select class="form-select" id="cfUnit"><option value="ml">ml water / g coffee</option></select></div>
                        <div class="col-md-4"><label class="form-label" for="cfRatio">Ratio (1 part coffee to X parts water)</label><input type="number" class="form-control" id="cfRatio" value="15" min="1" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="cfPreset">Preset</label><select class="form-select" id="cfPreset"><option value="">Custom</option><option value="12">Strong pour over 1:12</option><option value="15">Golden ratio 1:15</option><option value="16.7">Classic drip 1:16.7</option><option value="8">French press strong 1:8</option><option value="10">French press 1:10</option><option value="2">Espresso 1:2</option></select></div>
                    </div>                    <div class="alert alert-info mt-3 mb-0" id="cfOut">Enter values to see the result.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol>
                        <li>Choose whether you are starting from water or from coffee.</li>
                        <li>Enter the amount and the ratio — or pick a preset such as the 1:15 golden ratio.</li>
                        <li>Read the matching coffee grams (and tablespoons) or water amount.</li>
                    </ol>
                    <p class="small text-muted mb-0">The Specialty Coffee Association golden ratio is about 1:15 to 1:18 for filter coffee. One level tablespoon of ground coffee is roughly 7 g, but weighing in grams is far more consistent.</p>
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
    function calc() {
        var mode = el("cfMode").value;
        var amt = parseFloat(el("cfAmount").value);
        var ratio = parseFloat(el("cfRatio").value);
        var out = el("cfOut");
        if (isNaN(amt) || amt <= 0 || isNaN(ratio) || ratio <= 0) { out.textContent = "Please enter an amount and a ratio greater than zero."; return; }
        if (mode === "water") {
            var coffee = amt / ratio;
            out.innerHTML = "<strong>Coffee needed:</strong> " + coffee.toFixed(1) + " g (about " + (coffee / 7).toFixed(1) + " tbsp) for " + amt + " ml of water at 1:" + ratio + ".";
        } else {
            var water = amt * ratio;
            out.innerHTML = "<strong>Water needed:</strong> " + water.toFixed(0) + " ml for " + amt + " g of coffee at 1:" + ratio + ".";
        }
    }
    el("cfPreset").addEventListener("change", function () { if (el("cfPreset").value) { el("cfRatio").value = el("cfPreset").value; } calc(); });
    ["cfMode", "cfAmount", "cfRatio"].forEach(function (id) { el(id).addEventListener("input", calc); el(id).addEventListener("change", calc); });
    calc();
})();
</script>
@endsection
