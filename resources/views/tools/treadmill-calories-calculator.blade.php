@extends('layouts.app')

@section('title', 'Treadmill Calories Calculator — Free Online Tool')
@section('meta_description', 'Estimate treadmill calories burned from speed, incline, duration and body weight')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Treadmill Calories Calculator</h1>
            <p class="lead small text-muted">Treadmill displays often overstate burn. This tool uses the published ACSM walking and running equations, which account for speed and incline grade.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3"><label class="form-label" for="mode">Mode</label><select class="form-select" id="mode"><option value="walk">Walking</option><option value="run">Running</option></select></div>                    <div class="mb-3"><label class="form-label" for="speed">Speed (km/h)</label><input type="number" class="form-control" id="speed" value="6" step="any"></div>                    <div class="mb-3"><label class="form-label" for="incline">Incline (%)</label><input type="number" class="form-control" id="incline" value="5" step="any"></div>                    <div class="mb-3"><label class="form-label" for="mins">Duration (minutes)</label><input type="number" class="form-control" id="mins" value="30" step="any"></div>                    <div class="mb-3"><label class="form-label" for="weight">Body weight (kg)</label><input type="number" class="form-control" id="weight" value="70" step="any"></div>
                    <div id="result" class="border rounded p-3 mt-3">Enter your details above and the result will appear here.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Choose walking or running.</li><li>Enter speed, incline and duration.</li><li>Enter your weight and read the estimated burn and METs.</li></ol>
                    <h2 class="h5">Note</h2>
                    <p class="small text-muted mb-0">Uses the ACSM metabolic equations: walking VO2 = 0.1 x speed + 1.8 x speed x grade + 3.5; running VO2 = 0.2 x speed + 0.9 x speed x grade + 3.5, with speed in metres per minute and grade as a fraction. Holding the handrails reduces actual burn below these figures. Estimate only — not medical advice.</p>
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
        var mode = document.getElementById("mode").value;
        var kmh = num("speed"), inc = num("incline"), mins = num("mins"), w = num("weight");
        if ([kmh, inc, mins, w].some(isNaN) || kmh <= 0 || mins <= 0 || w <= 0 || inc < 0) { out("Please enter valid speed, incline, duration and weight values."); return; }
        var v = kmh * 1000 / 60, grade = inc / 100;
        var vo2 = mode === "walk" ? 0.1 * v + 1.8 * v * grade + 3.5 : 0.2 * v + 0.9 * v * grade + 3.5;
        var kcalMin = vo2 * w / 1000 * 5;
        out("<strong>Estimated calories burned:</strong> about " + fmt(kcalMin * mins, 0) + " kcal in " + fmt(mins, 0) + " minutes<br><strong>Intensity:</strong> " + fmt(vo2 / 3.5, 1) + " METs (VO2 " + fmt(vo2, 1) + " ml/kg/min)");
    }
    bind(["mode", "speed", "incline", "mins", "weight"], calc); calc();
})();
</script>
@endsection
