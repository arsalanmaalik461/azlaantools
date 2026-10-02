@extends('layouts.app')

@section('title', 'Calories Burned by Activity Calculator — Free Online Tool')
@section('meta_description', 'Estimate calories burned walking, running, cycling, swimming and more by duration and weight')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Calories Burned by Activity Calculator</h1>
            <p class="lead small text-muted">Pick an activity, enter your weight and how long you did it, and see the estimated calories burned using published MET values.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3"><label class="form-label" for="activity">Activity</label><select class="form-select" id="activity"><option value="2.8">Walking, slow (about 3 km/h) — MET 2.8</option><option value="3.5">Walking, moderate (about 4.8 km/h) — MET 3.5</option><option value="4.3">Walking, brisk (about 5.6 km/h) — MET 4.3</option><option value="8.3">Running, about 8 km/h — MET 8.3</option><option value="9.8">Running, about 9.7 km/h — MET 9.8</option><option value="11.0">Running, about 11 km/h — MET 11.0</option><option value="6.0">Cycling, leisure (16-19 km/h) — MET 6.0</option><option value="8.0">Cycling, moderate (19-22 km/h) — MET 8.0</option><option value="10.0">Cycling, fast (22-25 km/h) — MET 10.0</option><option value="6.0">Swimming, leisure — MET 6.0</option><option value="8.3">Swimming, freestyle laps moderate — MET 8.3</option><option value="9.8">Swimming, freestyle laps vigorous — MET 9.8</option><option value="7.0">Aerobics, general — MET 7.0</option><option value="5.0">Weight training, general — MET 5.0</option><option value="7.0">Football, casual play — MET 7.0</option><option value="4.8">Cricket, batting and bowling — MET 4.8</option><option value="7.5">Hiking, cross country — MET 7.5</option></select></div>                    <div class="mb-3"><label class="form-label" for="weight">Your weight (kg)</label><input type="number" class="form-control" id="weight" value="70" step="any"></div>                    <div class="mb-3"><label class="form-label" for="minutes">Duration (minutes)</label><input type="number" class="form-control" id="minutes" value="30" step="any"></div>
                    <div id="result" class="border rounded p-3 mt-3">Enter your details above and the result will appear here.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Choose your activity from the list.</li><li>Enter your body weight in kilograms.</li><li>Enter the duration in minutes and read the estimated burn.</li></ol>
                    <h2 class="h5">Note</h2>
                    <p class="small text-muted mb-0">Uses the standard MET formula: kcal per minute = MET x 3.5 x weight (kg) / 200, with MET values from the Compendium of Physical Activities. Actual burn varies with intensity and fitness. Estimate only — not medical advice.</p>
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
        var met = num("activity"), w = num("weight"), mins = num("minutes");
        if ([met, w, mins].some(isNaN) || w <= 0 || mins <= 0) { out("Please enter a valid weight and duration."); return; }
        var kcal = met * 3.5 * w / 200 * mins;
        out("<strong>Estimated calories burned:</strong> " + fmt(kcal, 0) + " kcal<br>That is about " + fmt(kcal / mins, 1) + " kcal per minute at MET " + fmt(met, 1) + ".");
    }
    bind(["activity", "weight", "minutes"], calc); calc();
})();
</script>
@endsection
