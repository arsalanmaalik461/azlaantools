@extends('layouts.app')

@section('title', 'Cat Age Calculator — Free Online Tool')
@section('meta_description', 'Convert cat age to human years and find the life stage of your cat.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h1 class="h3">Cat Age Calculator</h1>
                    <p class="lead small text-muted">Enter your cat age in years and months to get the human year equivalent on the standard feline scale, plus its life stage from kitten to senior.</p>
                    <div class="row g-3">
                        <div class="col-md-4"><label class="form-label" for="ctYears">Cat age — years</label><input type="number" class="form-control" id="ctYears" value="3" min="0" step="1"></div>
                        <div class="col-md-4"><label class="form-label" for="ctMonths">Cat age — extra months</label><input type="number" class="form-control" id="ctMonths" value="0" min="0" max="11" step="1"></div>
                    </div>
                    <div class="alert alert-info mt-3 mb-0" id="ctOut">Enter the cat age to convert it.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol>
                        <li>Enter the cat age in years and months.</li>
                        <li>Read the human years equivalent and the life stage (kitten, junior, adult, mature, senior or geriatric).</li>
                    </ol>
                    <p class="small text-muted mb-0">The scale used is the widely published veterinary guide: the first year of a cat life equals about 15 human years, the second year adds about 9 (24 total), and each later year adds about 4 human years. Indoor cats commonly live 12 to 18 years. Estimate only — not medical advice.</p>
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
        var y = parseInt(el("ctYears").value, 10), m = parseInt(el("ctMonths").value, 10);
        var out = el("ctOut");
        if (isNaN(y) || y < 0) { y = 0; }
        if (isNaN(m) || m < 0) { m = 0; }
        var age = y + m / 12;
        if (age <= 0) { out.textContent = "Please enter an age greater than zero."; return; }
        var human;
        if (age <= 1) { human = 15 * age; } else if (age <= 2) { human = 15 + 9 * (age - 1); } else { human = 24 + 4 * (age - 2); }
        var stage = age < 0.5 ? "Kitten" : (age < 2 ? "Junior" : (age < 7 ? "Adult (prime)" : (age < 11 ? "Mature" : (age < 15 ? "Senior" : "Geriatric"))));
        out.innerHTML = "<strong>Human years equivalent:</strong> about " + human.toFixed(1) + " years &nbsp; <strong>Life stage:</strong> " + stage + "<br>A " + age.toFixed(1) + " year old cat is at the " + stage.toLowerCase() + " stage. Senior cats benefit from twice yearly vet checks.";
    }
    ["ctYears", "ctMonths"].forEach(function (id) { el(id).addEventListener("input", calc); });
    calc();
})();
</script>
@endsection
