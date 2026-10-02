@extends('layouts.app')

@section('title', 'Rice Water Ratio Calculator — Free Online Tool')
@section('meta_description', 'Calculate rice and water amounts by rice type, servings and cooking method.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h1 class="h3">Rice Water Ratio Calculator</h1>
                    <p class="lead small text-muted">Basmati, sella or brown rice — get the uncooked rice grams, the exact water amount and the expected cooked yield for your servings and method.</p>
                    <div class="row g-3">
                        <div class="col-md-3"><label class="form-label" for="rcServings">Servings (people)</label><input type="number" class="form-control" id="rcServings" value="4" min="0" step="any"></div>
                        <div class="col-md-3"><label class="form-label" for="rcPer">Dry rice per person (g)</label><input type="number" class="form-control" id="rcPer" value="75" min="0" step="any"></div>
                        <div class="col-md-3"><label class="form-label" for="rcType">Rice type</label><select class="form-select" id="rcType"><option value="1.5">Basmati (1 : 1.5)</option><option value="1.75">Sella / parboiled (1 : 1.75)</option><option value="1.25">Jasmine (1 : 1.25)</option><option value="2.25">Brown rice (1 : 2.25)</option><option value="1.5">Broken / tota rice (1 : 1.5)</option></select></div>
                        <div class="col-md-3"><label class="form-label" for="rcMethod">Method</label><select class="form-select" id="rcMethod"><option value="1">Absorption / dum (ratio as shown)</option><option value="0">Boil and drain (excess water)</option></select></div>
                    </div>                    <div class="alert alert-info mt-3 mb-0" id="rcOut">Enter values to see the result.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol>
                        <li>Enter the number of servings and the dry rice per person (75 g is a standard main serving).</li>
                        <li>Choose the rice type — its standard water ratio is applied automatically.</li>
                        <li>Measure the rice and water shown. For boil and drain, use plenty of water and drain when just tender.</li>
                    </ol>
                    <p class="small text-muted mb-0">Ratios are water to rice by volume-equivalent weight for the absorption method and assume the rice is rinsed (and basmati soaked 20 to 30 minutes). Cooked rice weighs roughly 2.5 to 3 times the dry weight.</p>
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
        var servings = parseFloat(el("rcServings").value), per = parseFloat(el("rcPer").value);
        var ratio = parseFloat(el("rcType").value), method = el("rcMethod").value;
        var out = el("rcOut");
        if (isNaN(servings) || servings <= 0 || isNaN(per) || per <= 0) { out.textContent = "Please enter servings and rice per person greater than zero."; return; }
        var rice = servings * per;
        var cups = rice / 185;
        if (method === "0") {
            out.innerHTML = "<strong>Dry rice:</strong> " + rice.toFixed(0) + " g (about " + cups.toFixed(2) + " cups) &nbsp; <strong>Water:</strong> use 4 to 5 times the rice volume in a large pot, salt well, boil and drain.<br>Expected cooked rice: about " + (rice * 2.7).toFixed(0) + " g.";
        } else {
            var water = rice * ratio;
            out.innerHTML = "<strong>Dry rice:</strong> " + rice.toFixed(0) + " g (about " + cups.toFixed(2) + " cups) &nbsp; <strong>Water:</strong> " + water.toFixed(0) + " ml (about " + (water / 240).toFixed(2) + " cups)<br>Expected cooked rice: about " + (rice * 2.7).toFixed(0) + " g. Rest covered for 10 minutes after cooking before fluffing.";
        }
    }
    ["rcServings", "rcPer", "rcType", "rcMethod"].forEach(function (id) { el(id).addEventListener("input", calc); el(id).addEventListener("change", calc); });
    calc();
})();
</script>
@endsection
