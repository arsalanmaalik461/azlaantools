@extends('layouts.app')
@section('title', 'BMR Calculator — Azlaan Tools')
@section('meta_description', 'Free BMR calculator using Mifflin-St Jeor and Harris-Benedict formulas. Enter sex, age, weight and height to see your basal metabolic rate.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-2">BMR Calculator</h1>
            <p class="lead text-muted">Your Basal Metabolic Rate is the calories your body burns at complete rest. We show both trusted formulas side by side.</p>
            <div class="card shadow-sm mb-4"><div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label" for="sex">Sex</label><select class="form-select" id="sex"><option value="male" selected>Male</option><option value="female">Female</option></select></div>
                    <div class="col-md-6"><label class="form-label" for="age">Age (years)</label><input type="number" class="form-control" id="age" value="30"></div>
                    <div class="col-md-6"><label class="form-label" for="weight">Weight (kg)</label><input type="number" class="form-control" id="weight" value="70" step="any"></div>
                    <div class="col-md-6"><label class="form-label" for="height">Height (cm)</label><input type="number" class="form-control" id="height" value="170" step="any"></div>
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-md-6"><div class="border rounded p-3 bg-light text-center"><div class="text-muted small">Mifflin-St Jeor</div><div class="fs-4 fw-bold" id="mifflinOut">—</div><div class="small text-muted">Most accurate for most adults</div></div></div>
                    <div class="col-md-6"><div class="border rounded p-3 bg-light text-center"><div class="text-muted small">Harris-Benedict (Revised)</div><div class="fs-4 fw-bold" id="harrisOut">—</div><div class="small text-muted">Classic 1984 revision</div></div></div>
                </div>
                <p class="small text-muted mt-3 mb-0">This is an estimate for information only — not medical advice.</p>
            </div></div>
            <div class="card shadow-sm"><div class="card-body">
                <h2>How to use</h2>
                <ol><li>Select sex and enter age, weight (kg) and height (cm).</li><li>Both BMR results update instantly as you type.</li><li>Multiply BMR by your activity level for daily calorie needs (see our TDEE Calculator).</li></ol>
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
        if (age <= 0 || w <= 0 || h <= 0) { return; }
        var mifflin, harris;
        if (sex === 'male') {
            mifflin = (10 * w) + (6.25 * h) - (5 * age) + 5;
            harris = 88.362 + (13.397 * w) + (4.799 * h) - (5.677 * age);
        } else {
            mifflin = (10 * w) + (6.25 * h) - (5 * age) - 161;
            harris = 447.593 + (9.247 * w) + (3.098 * h) - (4.330 * age);
        }
        document.getElementById('mifflinOut').textContent = Math.round(mifflin) + ' kcal/day';
        document.getElementById('harrisOut').textContent = Math.round(harris) + ' kcal/day';
    }
    ['sex','age','weight','height'].forEach(function (id) { document.getElementById(id).addEventListener('input', calc); document.getElementById(id).addEventListener('change', calc); });
    calc();
})();
</script>
@endsection
