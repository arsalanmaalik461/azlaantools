@extends('layouts.app')

@section('title', 'Dog Age Calculator — Free Online Tool')
@section('meta_description', 'Convert dog age to human years by breed size using the modern scale.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h1 class="h3">Dog Age Calculator</h1>
                    <p class="lead small text-muted">The times seven myth is wrong — this calculator uses the modern logarithmic scale plus breed size to give a realistic human age and life stage for your dog.</p>
                    <div class="row g-3">
                        <div class="col-md-4"><label class="form-label" for="dgYears">Dog age — years</label><input type="number" class="form-control" id="dgYears" value="3" min="0" step="1"></div>
                        <div class="col-md-4"><label class="form-label" for="dgMonths">Dog age — extra months</label><input type="number" class="form-control" id="dgMonths" value="0" min="0" max="11" step="1"></div>
                        <div class="col-md-4"><label class="form-label" for="dgSize">Breed size</label><select class="form-select" id="dgSize"><option value="small">Small (under 10 kg)</option><option value="medium" selected>Medium (10 to 25 kg)</option><option value="large">Large (over 25 kg)</option></select></div>
                    </div>
                    <div class="alert alert-info mt-3 mb-0" id="dgOut">Enter the dog age to convert it.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol>
                        <li>Enter the dog age in years and months and choose the breed size.</li>
                        <li>Read the human years equivalent from the modern scale and the size adjusted note.</li>
                    </ol>
                    <p class="small text-muted mb-0">For dogs over 1 year this uses the University of California San Diego formula published in 2019: human years = 16 x ln(dog age) + 31. Puppies under 1 year are interpolated towards 15 human years at age 1. Large breeds age faster in later life, which the size note reflects. Estimate only — not medical advice.</p>
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
        var y = parseInt(el("dgYears").value, 10), m = parseInt(el("dgMonths").value, 10);
        var out = el("dgOut");
        if (isNaN(y) || y < 0) { y = 0; }
        if (isNaN(m) || m < 0) { m = 0; }
        var age = y + m / 12;
        if (age <= 0) { out.textContent = "Please enter an age greater than zero."; return; }
        var human = age < 1 ? 15 * age : 16 * Math.log(age) + 31;
        var size = el("dgSize").value;
        var stage = age < 1 ? "Puppy" : (age < 2 ? "Young adult" : (age < 7 ? "Adult" : (age < 10 ? "Mature" : "Senior")));
        var note = size === "large" ? "Large breeds mature faster and are usually considered senior from about 7 to 8 years." : (size === "small" ? "Small breeds live longest on average and often stay active past 12 years." : "Medium breeds are typically senior from about 10 years.");
        out.innerHTML = "<strong>Human years equivalent:</strong> about " + human.toFixed(1) + " years &nbsp; <strong>Life stage:</strong> " + stage + "<br>" + note;
    }
    ["dgYears", "dgMonths", "dgSize"].forEach(function (id) { el(id).addEventListener("input", calc); el(id).addEventListener("change", calc); });
    calc();
})();
</script>
@endsection
