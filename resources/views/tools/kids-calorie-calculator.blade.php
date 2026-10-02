@extends('layouts.app')

@section('title', 'Kids Calorie Calculator — Free Online Tool')
@section('meta_description', 'Estimate daily calorie needs for children and teens by age, sex and activity level')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Kids Calorie Calculator</h1>
            <p class="lead small text-muted">Uses the published Estimated Energy Requirement (EER) equations for ages 3 to 18, which account for growth as well as activity.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3"><label class="form-label" for="sex">Sex</label><select class="form-select" id="sex"><option value="boy">Boy</option><option value="girl">Girl</option></select></div>                    <div class="mb-3"><label class="form-label" for="age">Age (years, 3 to 18)</label><input type="number" class="form-control" id="age" value="10" step="any"></div>                    <div class="mb-3"><label class="form-label" for="weight">Weight (kg)</label><input type="number" class="form-control" id="weight" value="32" step="any"></div>                    <div class="mb-3"><label class="form-label" for="height">Height (cm)</label><input type="number" class="form-control" id="height" value="138" step="any"></div>                    <div class="mb-3"><label class="form-label" for="activity">Activity level</label><select class="form-select" id="activity"><option value="sed">Sedentary</option><option value="low">Low active — light play most days</option><option value="active">Active — daily active play or sport</option><option value="very">Very active — hard training most days</option></select></div>
                    <div id="result" class="border rounded p-3 mt-3">Enter your details above and the result will appear here.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Enter sex and age (3 to 18 years).</li><li>Enter weight in kilograms and height in centimetres.</li><li>Choose the activity level and read the estimated daily calories.</li></ol>
                    <h2 class="h5">Note</h2>
                    <p class="small text-muted mb-0">Uses the Institute of Medicine EER equations for ages 3 to 18, including the standard growth allowance (20 kcal under age 9, 25 kcal from age 9). These equations are not designed for under-3s. Appetite and growth curves matter more than any single number. Estimate only — not medical advice.</p>
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
        var sex = document.getElementById("sex").value, act = document.getElementById("activity").value;
        var age = num("age"), w = num("weight"), h = num("height");
        if ([age, w, h].some(isNaN) || age < 3 || age > 18 || w <= 0 || h <= 0) { out("Please enter an age between 3 and 18 with valid weight and height."); return; }
        var hm = h / 100, pa, eer;
        var growth = age < 9 ? 20 : 25;
        if (sex === "boy") { pa = act === "sed" ? 1.0 : act === "low" ? 1.13 : act === "active" ? 1.26 : 1.42; eer = 88.5 - 61.9 * age + pa * (26.7 * w + 903 * hm) + growth; }
        else { pa = act === "sed" ? 1.0 : act === "low" ? 1.16 : act === "active" ? 1.31 : 1.56; eer = 135.3 - 30.8 * age + pa * (10.0 * w + 934 * hm) + growth; }
        out("<strong>Estimated daily energy needs (EER):</strong> about " + fmt(eer, 0) + " kcal per day");
    }
    bind(["sex", "age", "weight", "height", "activity"], calc); calc();
})();
</script>
@endsection
