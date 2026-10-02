@extends('layouts.app')

@section('title', 'Fiber Intake Calculator — Free Online Tool')
@section('meta_description', 'Work out your recommended daily fiber grams based on age, sex and calorie intake')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Fiber Intake Calculator</h1>
            <p class="lead small text-muted">Fiber guidance comes two ways: 14 grams per 1,000 kcal eaten, and fixed age-and-sex values. This tool shows both.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3"><label class="form-label" for="sex">Sex</label><select class="form-select" id="sex"><option value="male">Male</option><option value="female">Female</option></select></div>                    <div class="mb-3"><label class="form-label" for="age">Age (years)</label><input type="number" class="form-control" id="age" value="30" step="any"></div>                    <div class="mb-3"><label class="form-label" for="kcal">Daily calorie intake (kcal)</label><input type="number" class="form-control" id="kcal" value="2000" step="any"></div>
                    <div id="result" class="border rounded p-3 mt-3">Enter your details above and the result will appear here.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Enter your sex and age.</li><li>Enter your usual daily calorie intake.</li><li>Both fiber targets appear instantly.</li></ol>
                    <h2 class="h5">Note</h2>
                    <p class="small text-muted mb-0">The 14 g per 1,000 kcal method is the basis of the published adequate intake values: men 19 to 50 about 38 g, men over 50 about 30 g, women 19 to 50 about 25 g, women over 50 about 21 g per day. Increase fiber gradually and drink enough water. Estimate only — not medical advice.</p>
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
        var sex = document.getElementById("sex").value;
        var age = num("age"), kcal = num("kcal");
        if (isNaN(age) || isNaN(kcal) || age <= 0 || kcal <= 0) { out("Please enter valid age and calorie values."); return; }
        var byKcal = kcal * 14 / 1000;
        var fixed;
        if (age < 19) { fixed = "Use the calorie-based figure — published fixed values are mainly set for adults"; }
        else if (sex === "male") { fixed = (age <= 50 ? "38 g per day (men 19 to 50)" : "30 g per day (men over 50)"); }
        else { fixed = (age <= 50 ? "25 g per day (women 19 to 50)" : "21 g per day (women over 50)"); }
        out("<strong>By calories (14 g per 1,000 kcal):</strong> about " + fmt(byKcal, 0) + " g per day<br><strong>Published age-based value:</strong> " + fixed);
    }
    bind(["sex", "age", "kcal"], calc); calc();
})();
</script>
@endsection
