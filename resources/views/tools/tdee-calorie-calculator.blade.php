@extends('layouts.app')
@section('title', 'TDEE & Calorie Calculator — Azlaan Tools')
@section('meta_description', 'Free TDEE calculator. Enter sex, age, weight and height to get BMR, maintenance calories and daily targets to lose or gain weight.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-2">TDEE &amp; Calorie Calculator</h1>
            <p class="lead text-muted">Find how many calories you burn each day (TDEE) and how much to eat to lose, maintain or gain weight.</p>
            <div class="card shadow-sm mb-4"><div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label" for="sex">Sex</label><select class="form-select" id="sex"><option value="male" selected>Male</option><option value="female">Female</option></select></div>
                    <div class="col-md-6"><label class="form-label" for="age">Age (years)</label><input type="number" class="form-control" id="age" value="30"></div>
                    <div class="col-md-6"><label class="form-label" for="weight">Weight (kg)</label><input type="number" class="form-control" id="weight" value="70" step="any"></div>
                    <div class="col-md-6"><label class="form-label" for="height">Height (cm)</label><input type="number" class="form-control" id="height" value="170" step="any"></div>
                    <div class="col-12"><label class="form-label" for="activity">Activity Level</label><select class="form-select" id="activity"><option value="1.2" selected>Sedentary — little or no exercise</option><option value="1.375">Light — exercise 1–3 days/week</option><option value="1.55">Moderate — exercise 3–5 days/week</option><option value="1.725">Active — exercise 6–7 days/week</option><option value="1.9">Athlete — very hard exercise / physical job</option></select></div>
                </div>
                <div class="row g-3 mt-2" id="results">
                    <div class="col-md-6"><div class="border rounded p-3 bg-light text-center"><div class="text-muted small">BMR (resting burn)</div><div class="fs-4 fw-bold" id="bmrOut">—</div></div></div>
                    <div class="col-md-6"><div class="border rounded p-3 bg-primary text-white text-center"><div class="small">TDEE — Maintenance</div><div class="fs-4 fw-bold" id="tdeeOut">—</div></div></div>
                    <div class="col-md-4"><div class="border rounded p-3 text-center"><div class="text-muted small">Lose 0.25 kg/week</div><div class="fs-5 fw-bold" id="lose025">—</div></div></div>
                    <div class="col-md-4"><div class="border rounded p-3 text-center"><div class="text-muted small">Lose 0.5 kg/week</div><div class="fs-5 fw-bold" id="lose05">—</div></div></div>
                    <div class="col-md-4"><div class="border rounded p-3 text-center"><div class="text-muted small">Gain 0.5 kg/week</div><div class="fs-5 fw-bold" id="gain05">—</div></div></div>
                </div>
                <p class="small text-muted mt-3 mb-0">This is an estimate for information only — not medical advice.</p>
            </div></div>
            <div class="card shadow-sm"><div class="card-body">
                <h2>How to use</h2>
                <ol><li>Select your sex and enter age, weight (kg) and height (cm).</li><li>Choose the activity level closest to your routine.</li><li>Your BMR, maintenance calories and lose/gain targets update instantly.</li></ol>
            </div></div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    function calc() {
        var sex = document.getElementById('sex').value;
        var age = parseFloat(document.getElementById('age').value) || 0;
        var w = parseFloat(document.getElementById('weight').value) || 0;
        var h = parseFloat(document.getElementById('height').value) || 0;
        var act = parseFloat(document.getElementById('activity').value) || 1.2;
        if (age <= 0 || w <= 0 || h <= 0) { return; }
        var bmr = (10 * w) + (6.25 * h) - (5 * age) + (sex === 'male' ? 5 : -161);
        var tdee = bmr * act;
        document.getElementById('bmrOut').textContent = Math.round(bmr) + ' kcal/day';
        document.getElementById('tdeeOut').textContent = Math.round(tdee) + ' kcal/day';
        document.getElementById('lose025').textContent = Math.round(tdee - 275) + ' kcal';
        document.getElementById('lose05').textContent = Math.round(tdee - 550) + ' kcal';
        document.getElementById('gain05').textContent = Math.round(tdee + 550) + ' kcal';
    }
    ['sex','age','weight','height','activity'].forEach(function (id) { document.getElementById(id).addEventListener('input', calc); document.getElementById(id).addEventListener('change', calc); });
    calc();
})();
</script>
@endsection
