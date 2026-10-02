@extends('layouts.app')

@section('title', 'Pasta Quantity Calculator — Free Online Tool')
@section('meta_description', 'Calculate dry pasta amount per person as a main or side dish.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h1 class="h3">Pasta Quantity Calculator</h1>
                    <p class="lead small text-muted">Stop guessing the pasta packet — enter people, appetite and whether pasta is the main or a side, and get the exact dry weight plus cooked yield.</p>
                    <div class="row g-3">
                        <div class="col-md-3"><label class="form-label" for="paPeople">People</label><input type="number" class="form-control" id="paPeople" value="4" min="0" step="1"></div>
                        <div class="col-md-3"><label class="form-label" for="paType">Dish type</label><select class="form-select" id="paType"><option value="main">Main dish</option><option value="side">Side dish</option><option value="soup">Soup / small portion</option></select></div>
                        <div class="col-md-3"><label class="form-label" for="paAppetite">Appetite</label><select class="form-select" id="paAppetite"><option value="0.85">Light</option><option value="1" selected>Normal</option><option value="1.2">Hearty</option></select></div>
                        <div class="col-md-3"><label class="form-label" for="paBase">Base per person — main (g, editable)</label><input type="number" class="form-control" id="paBase" value="100" min="0" step="any"></div>
                    </div>                    <div class="alert alert-info mt-3 mb-0" id="paOut">Enter values to see the result.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol>
                        <li>Enter the number of people eating.</li>
                        <li>Choose main dish, side dish or soup, and the appetite level.</li>
                        <li>Weigh the dry pasta shown — side dishes use 60 percent and soups 40 percent of the main amount.</li>
                    </ol>
                    <p class="small text-muted mb-0">Standard dry pasta portions are 90 to 110 g per person as a main. Pasta roughly doubles in weight when cooked (about 2.3 times for spaghetti). Cook in at least 1 litre of well salted water per 100 g of pasta.</p>
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
        var people = parseFloat(el("paPeople").value), base = parseFloat(el("paBase").value);
        var type = el("paType").value, app = parseFloat(el("paAppetite").value);
        var out = el("paOut");
        if (isNaN(people) || people <= 0 || isNaN(base) || base <= 0) { out.textContent = "Please enter people and a base amount greater than zero."; return; }
        var factor = type === "main" ? 1 : (type === "side" ? 0.6 : 0.4);
        var per = base * factor * app;
        var total = per * people;
        out.innerHTML = "<strong>Dry pasta:</strong> " + total.toFixed(0) + " g total (" + per.toFixed(0) + " g per person)<br><strong>Cooked yield:</strong> about " + (total * 2.3).toFixed(0) + " g &nbsp; <strong>Water:</strong> at least " + (total / 100).toFixed(1) + " litres, well salted.";
    }
    ["paPeople", "paType", "paAppetite", "paBase"].forEach(function (id) { el(id).addEventListener("input", calc); el(id).addEventListener("change", calc); });
    calc();
})();
</script>
@endsection
