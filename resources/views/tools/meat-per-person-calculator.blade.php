@extends('layouts.app')

@section('title', 'Meat Per Person Calculator — Free Online Tool')
@section('meta_description', 'Calculate how much meat to buy per person for a party, BBQ or roast.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h1 class="h3">Meat Per Person Calculator</h1>
                    <p class="lead small text-muted">Enter guests, meat type and appetite to get the raw weight to buy, with bone and cooking yield already accounted for.</p>
                    <div class="row g-3">
                        <div class="col-md-3"><label class="form-label" for="mpPeople">Guests</label><input type="number" class="form-control" id="mpPeople" value="10" min="0" step="1"></div>
                        <div class="col-md-3"><label class="form-label" for="mpMeat">Meat</label><select class="form-select" id="mpMeat"><option value="boneless">Boneless (beef / mutton / chicken)</option><option value="bonein">Bone-in cuts</option><option value="wholechicken">Whole chicken</option><option value="mince">Mince / minced meat dishes</option></select></div>
                        <div class="col-md-3"><label class="form-label" for="mpAppetite">Appetite</label><select class="form-select" id="mpAppetite"><option value="0.85">Light / many side dishes</option><option value="1" selected>Normal</option><option value="1.25">Hearty / meat is the star</option></select></div>
                        <div class="col-md-3"><label class="form-label" for="mpCooked">Cooked meat per person (g, editable)</label><input type="number" class="form-control" id="mpCooked" value="180" min="0" step="any"></div>
                    </div>                    <div class="alert alert-info mt-3 mb-0" id="mpOut">Enter values to see the result.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol>
                        <li>Enter the number of guests.</li>
                        <li>Choose the meat type — bone-in and whole chicken need more raw weight for the same cooked serving.</li>
                        <li>Buy the raw weight shown; the yield factor for that meat type is already included.</li>
                    </ol>
                    <p class="small text-muted mb-0">Yield guide used: boneless meat loses about 25 to 30 percent in cooking, bone-in cuts yield about 55 to 60 percent edible cooked meat, whole chicken about 50 percent, and mince dishes about 80 percent. Children typically eat about half an adult portion.</p>
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
        var people = parseFloat(el("mpPeople").value), cooked = parseFloat(el("mpCooked").value);
        var app = parseFloat(el("mpAppetite").value), type = el("mpMeat").value;
        var out = el("mpOut");
        if (isNaN(people) || people <= 0 || isNaN(cooked) || cooked <= 0) { out.textContent = "Please enter guests and a cooked portion greater than zero."; return; }
        var yields = { boneless: 0.72, bonein: 0.57, wholechicken: 0.5, mince: 0.8 };
        var rawPer = cooked * app / yields[type];
        var total = rawPer * people;
        out.innerHTML = "<strong>Buy:</strong> " + (total / 1000).toFixed(2) + " kg raw meat (" + rawPer.toFixed(0) + " g per guest)<br>This gives about " + cooked.toFixed(0) + " g of cooked meat per guest at the yield for the selected type.";
    }
    ["mpPeople", "mpMeat", "mpAppetite", "mpCooked"].forEach(function (id) { el(id).addEventListener("input", calc); el(id).addEventListener("change", calc); });
    calc();
})();
</script>
@endsection
