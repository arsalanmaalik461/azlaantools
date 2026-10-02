@extends('layouts.app')

@section('title', 'Calorie Calculator - Azlaan Tools')
@section('meta_description', 'Calculate daily calories for weight loss, gain or maintenance with macros, free online.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Calorie Calculator</h1>
            <p class="lead text-muted">Find out how many calories you need each day — to lose weight, gain weight, or stay the same.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label for="ageIn" class="form-label fw-semibold">Age (years)</label>
                            <input type="number" class="form-control" id="ageIn" min="10" max="100" placeholder="e.g. 30">
                        </div>
                        <div class="col-6 mb-3">
                            <label for="genderSel" class="form-label fw-semibold">Gender</label>
                            <select class="form-select" id="genderSel">
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label for="heightIn" class="form-label fw-semibold">Height (cm)</label>
                            <input type="number" class="form-control" id="heightIn" min="100" max="250" placeholder="e.g. 170">
                        </div>
                        <div class="col-6 mb-3">
                            <label for="weightIn" class="form-label fw-semibold">Weight (kg)</label>
                            <input type="number" class="form-control" id="weightIn" min="25" max="300" step="0.1" placeholder="e.g. 75">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="activitySel" class="form-label fw-semibold">Daily activity level</label>
                        <select class="form-select" id="activitySel">
                            <option value="1.2">Sedentary — desk job, no exercise</option>
                            <option value="1.375">Light — light walk, 1-2 days a week</option>
                            <option value="1.55" selected>Moderate — exercise 3-5 days a week</option>
                            <option value="1.725">Active — daily hard exercise or physical work</option>
                            <option value="1.9">Very active — athlete level, training twice a day</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="goalSel" class="form-label fw-semibold">Goal</label>
                        <select class="form-select" id="goalSel">
                            <option value="-500">Lose weight — ~0.5 kg/week</option>
                            <option value="-250">Lose weight slowly — ~0.25 kg/week</option>
                            <option value="0" selected>Maintain weight</option>
                            <option value="250">Gain weight slowly — ~0.25 kg/week</option>
                            <option value="500">Gain weight — ~0.5 kg/week</option>
                        </select>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Calculate Calories</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="row text-center g-3">
                            <div class="col-6 col-md-3">
                                <div class="border rounded p-3 bg-light">
                                    <div class="small text-muted">BMR</div>
                                    <div class="fs-4 fw-bold" id="rBmr">-</div>
                                    <div class="small">kcal/day</div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="border rounded p-3 bg-light">
                                    <div class="small text-muted">TDEE (maintenance)</div>
                                    <div class="fs-4 fw-bold" id="rTdee">-</div>
                                    <div class="small">kcal/day</div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="border rounded p-3 bg-light">
                                    <div class="small text-muted">Target calories</div>
                                    <div class="fs-4 fw-bold text-primary" id="rTarget">-</div>
                                    <div class="small">kcal/day</div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="border rounded p-3 bg-light">
                                    <div class="small text-muted">BMI</div>
                                    <div class="fs-4 fw-bold" id="rBmi">-</div>
                                    <div class="small" id="rBmiCat">-</div>
                                </div>
                            </div>
                        </div>
                        <h5 class="mt-4">Daily macros (estimated)</h5>
                        <table class="table table-bordered">
                            <thead><tr><th>Macro</th><th>Grams</th><th>Calories</th></tr></thead>
                            <tbody>
                                <tr><td>Protein</td><td id="mProtein">-</td><td id="mProteinC">-</td></tr>
                                <tr><td>Fat</td><td id="mFat">-</td><td id="mFatC">-</td></tr>
                                <tr><td>Carbs</td><td id="mCarbs">-</td><td id="mCarbsC">-</td></tr>
                            </tbody>
                        </table>
                        <p class="text-muted small mb-0">This is only an estimate, not medical advice. For any illness or special diet, talk to a doctor or dietitian.</p>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Enter your age, gender, height and weight.</li>
                <li>Select your daily activity and goal.</li>
                <li>Press Calculate — you will get your BMR, TDEE, target calories and macros.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var ageIn = document.getElementById('ageIn');
    var genderSel = document.getElementById('genderSel');
    var heightIn = document.getElementById('heightIn');
    var weightIn = document.getElementById('weightIn');
    var activitySel = document.getElementById('activitySel');
    var goalSel = document.getElementById('goalSel');
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function set(id, val) { document.getElementById(id).textContent = val; }

    goBtn.addEventListener('click', function () {
        hideError();
        var age = parseFloat(ageIn.value);
        var gender = genderSel.value;
        var h = parseFloat(heightIn.value);
        var w = parseFloat(weightIn.value);
        var act = parseFloat(activitySel.value);
        var adj = parseFloat(goalSel.value);

        if (!age || age < 10 || age > 100) { showError('Please enter a valid age (10-100).'); return; }
        if (!h || h < 100 || h > 250) { showError('Please enter a valid height in cm (100-250).'); return; }
        if (!w || w < 25 || w > 300) { showError('Please enter a valid weight in kg (25-300).'); return; }

        // Mifflin-St Jeor
        var bmr = 10 * w + 6.25 * h - 5 * age + (gender === 'male' ? 5 : -161);
        var tdee = bmr * act;
        var target = Math.max(1200, tdee + adj);

        var bmi = w / Math.pow(h / 100, 2);
        var cat = bmi < 18.5 ? 'Underweight' : bmi < 25 ? 'Normal' : bmi < 30 ? 'Overweight' : 'Obese';

        // Macros: protein by goal, fat 25% of calories, carbs remainder
        var protPerKg = adj >= 250 ? 2.0 : (adj <= -250 ? 1.8 : 1.6);
        var pG = Math.round(protPerKg * w);
        var pC = pG * 4;
        var fC = Math.round(target * 0.25);
        var fG = Math.round(fC / 9);
        var cC = Math.max(0, Math.round(target - pC - fC));
        var cG = Math.round(cC / 4);

        set('rBmr', Math.round(bmr).toLocaleString());
        set('rTdee', Math.round(tdee).toLocaleString());
        set('rTarget', Math.round(target).toLocaleString());
        set('rBmi', bmi.toFixed(1));
        set('rBmiCat', cat);
        set('mProtein', pG + ' g'); set('mProteinC', pC.toLocaleString());
        set('mFat', fG + ' g'); set('mFatC', fC.toLocaleString());
        set('mCarbs', cG + ' g'); set('mCarbsC', cC.toLocaleString());
        results.classList.remove('d-none');
    });
})();
</script>
@endsection
