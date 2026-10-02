@extends('layouts.app')

@section('title', 'Pet Feeding Calculator — Free Online Tool')
@section('meta_description', 'Estimate daily food amount for dogs and cats from weight, age and activity.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h1 class="h3">Pet Feeding Calculator</h1>
                    <p class="lead small text-muted">Enter pet type, weight, life stage and activity to get daily calories (RER and DER method) and the grams of dry food per day, split into meals.</p>
                    <div class="row g-3">
                        <div class="col-md-3"><label class="form-label" for="pfPet">Pet</label><select class="form-select" id="pfPet"><option value="dog">Dog</option><option value="cat">Cat</option></select></div>
                        <div class="col-md-3"><label class="form-label" for="pfWeight">Weight (kg)</label><input type="number" class="form-control" id="pfWeight" value="10" min="0" step="any"></div>
                        <div class="col-md-3"><label class="form-label" for="pfStage">Life stage / activity</label><select class="form-select" id="pfStage"></select></div>
                        <div class="col-md-3"><label class="form-label" for="pfKcal">Food energy (kcal per 100 g, editable)</label><input type="number" class="form-control" id="pfKcal" value="350" min="0" step="any"></div>
                        <div class="col-md-3"><label class="form-label" for="pfMeals">Meals per day</label><input type="number" class="form-control" id="pfMeals" value="2" min="1" step="1"></div>
                    </div>
                    <div class="alert alert-info mt-3 mb-0" id="pfOut">Enter pet details to estimate the daily food amount.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol>
                        <li>Choose dog or cat and enter the current weight in kilograms.</li>
                        <li>Pick the life stage and activity level, and check the food energy on your packet (kcal per 100 g).</li>
                        <li>Feed the grams per day shown, split across the meals, and adjust by body condition over 2 to 3 weeks.</li>
                    </ol>
                    <p class="small text-muted mb-0">Calories use the standard veterinary formulas: RER = 70 x (weight in kg)^0.75, and DER = RER multiplied by a life stage factor (for example 1.6 for a typical neutered adult dog, 1.2 to 1.4 for a neutered cat, higher for puppies, kittens and active dogs). Estimate only — not medical advice. Confirm amounts for growing, pregnant or unwell pets with a vet.</p>
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
    var stages = {
        dog: [["Neutered adult, typical activity", 1.6], ["Intact adult", 1.8], ["Inactive / needs weight loss", 1.2], ["Active / working dog", 2.5], ["Puppy under 4 months", 3], ["Puppy 4 to 12 months", 2]],
        cat: [["Neutered adult, typical", 1.2], ["Intact adult", 1.4], ["Inactive / indoor, weight loss", 1], ["Active outdoor cat", 1.6], ["Kitten under 4 months", 2.5], ["Kitten 4 to 12 months", 2]]
    };
    function fillStages() {
        var sel = el("pfStage");
        sel.innerHTML = "";
        stages[el("pfPet").value].forEach(function (s) { var o = document.createElement("option"); o.value = s[1]; o.textContent = s[0]; sel.appendChild(o); });
    }
    function calc() {
        var w = parseFloat(el("pfWeight").value), kcal100 = parseFloat(el("pfKcal").value), meals = parseInt(el("pfMeals").value, 10), factor = parseFloat(el("pfStage").value);
        var out = el("pfOut");
        if (isNaN(w) || w <= 0 || isNaN(kcal100) || kcal100 <= 0 || isNaN(factor)) { out.textContent = "Please enter a weight and food energy greater than zero."; return; }
        if (isNaN(meals) || meals < 1) { meals = 2; }
        var rer = 70 * Math.pow(w, 0.75);
        var der = rer * factor;
        var grams = der / (kcal100 / 100);
        out.innerHTML = "<strong>Daily calories:</strong> about " + Math.round(der) + " kcal (RER " + Math.round(rer) + " x " + factor + ")<br><strong>Dry food:</strong> about " + Math.round(grams) + " g per day, roughly " + Math.round(grams / meals) + " g per meal across " + meals + " meals. Always keep fresh water available.";
    }
    el("pfPet").addEventListener("change", function () { fillStages(); calc(); });
    ["pfWeight", "pfStage", "pfKcal", "pfMeals"].forEach(function (id) { el(id).addEventListener("input", calc); el(id).addEventListener("change", calc); });
    fillStages();
    calc();
})();
</script>
@endsection
