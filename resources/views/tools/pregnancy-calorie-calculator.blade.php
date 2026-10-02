@extends('layouts.app')

@section('title', 'Pregnancy Calorie Calculator — Free Online Tool')
@section('meta_description', 'Estimate daily calorie needs per trimester based on your maintenance calories')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Pregnancy Calorie Calculator</h1>
            <p class="lead small text-muted">Calorie needs rise gradually in pregnancy. Enter your pre-pregnancy maintenance calories and trimester for an estimated daily target.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3"><label class="form-label" for="maint">Your maintenance calories per day (kcal)</label><input type="number" class="form-control" id="maint" value="2000" step="any"></div>                    <div class="mb-3"><label class="form-label" for="tri">Trimester</label><select class="form-select" id="tri"><option value="0">First trimester — plus about 0 kcal</option><option value="340">Second trimester — plus about 340 kcal</option><option value="452">Third trimester — plus about 452 kcal</option></select></div>
                    <div id="result" class="border rounded p-3 mt-3">Enter your details above and the result will appear here.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Enter your normal maintenance calories (for example from a TDEE calculator).</li><li>Choose your trimester.</li><li>Your estimated daily target appears instantly.</li></ol>
                    <h2 class="h5">Note</h2>
                    <p class="small text-muted mb-0">Increments follow published dietary reference values: no increase in the first trimester, about 340 kcal in the second and about 452 kcal in the third. Eating for two is a myth — nutrient quality matters more than quantity. Estimate only — not medical advice.</p>
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
        var m = num("maint"), add = num("tri");
        if (isNaN(m) || m <= 0) { out("Please enter valid maintenance calories."); return; }
        out("<strong>Estimated daily target:</strong> about " + fmt(m + add, 0) + " kcal per day (maintenance " + fmt(m, 0) + " plus " + fmt(add, 0) + " kcal)");
    }
    bind(["maint", "tri"], calc); calc();
})();
</script>
@endsection
