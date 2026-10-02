@extends('layouts.app')
@section('title', 'Daily Step Goal Planner Online Free — Azlaan Tools')
@section('meta_description', 'Get a personalized daily step goal based on your age and routine. Free step target planner with a safe 4-week walking ramp-up plan.')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Daily Step Goal Planner</h1>
            <p class="lead text-muted">Set a daily step target based on your age and routine — with a safe 4-week plan. Personalized daily step target, completely free.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="age" class="form-label fw-semibold">Age (years)</label>
                            <input type="number" class="form-control" id="age" min="10" max="100" placeholder="e.g. 35">
                        </div>
                        <div class="col-md-6">
                            <label for="current" class="form-label fw-semibold">Current daily steps (average)</label>
                            <input type="number" class="form-control" id="current" min="0" max="60000" placeholder="e.g. 4000">
                            <div class="form-text">Check your phone health app for an estimate.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="activity" class="form-label fw-semibold">Activity level</label>
                            <select class="form-select" id="activity">
                                <option value="sedentary">Sedentary — sitting most of the time</option>
                                <option value="light">Light — light movement</option>
                                <option value="moderate">Moderate — short walk daily</option>
                                <option value="active">Active — daily exercise</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="purpose" class="form-label fw-semibold">Main goal</label>
                            <select class="form-select" id="purpose">
                                <option value="health">General health</option>
                                <option value="weight">Weight loss</option>
                                <option value="fitness">Fitness / stamina</option>
                            </select>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100 mt-3" id="goBtn">Plan My Step Goal</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="alert alert-success">
                            <div class="fs-5">Your daily step goal: <strong id="goalOut">—</strong></div>
                            <div class="small" id="goalNote"></div>
                        </div>
                        <h3 class="h5">4-week safe ramp-up plan</h3>
                        <p class="text-muted small">Do not increase by more than 1,000 steps per week — give your body time to adjust.</p>
                        <table class="table table-bordered">
                            <thead class="table-light"><tr><th>Week</th><th>Daily target</th><th>≈ Time</th><th>≈ Distance</th></tr></thead>
                            <tbody id="planRows"></tbody>
                        </table>
                        <p class="text-muted small mb-0">Estimates: 100 steps ≈ 1 minute walk, 1 step ≈ 0.7 metre, 1 step ≈ 0.04 kcal (at 70 kg weight).</p>
                    </div>
                </div>
            </div>

            <div class="alert alert-warning">
                <strong>Health note:</strong> This is an estimate, not a medical treatment. If you have heart, joint or any other health problem, consult a doctor before increasing your walking.
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Write your age, current daily steps, activity level and goal.</li>
                <li>Press <strong>Plan My Step Goal</strong> — you will get your personalized daily target and 4-week plan.</li>
                <li>Increase your steps gradually every week according to the plan.</li>
            </ol>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
(function () {
    'use strict';
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

    function baseTarget(age, purpose) {
        var t;
        if (age < 30) { t = 10000; }
        else if (age < 45) { t = 9500; }
        else if (age < 60) { t = 8500; }
        else if (age < 75) { t = 7500; }
        else { t = 6500; }
        if (purpose === 'weight') { t += 1000; }
        else if (purpose === 'fitness') { t += 500; }
        if (t > 12000) { t = 12000; }
        return Math.round(t / 500) * 500;
    }

    function fmt(n) {
        return Math.round(n).toLocaleString('en-PK');
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var ageV = document.getElementById('age').value.trim();
        var curV = document.getElementById('current').value.trim();
        if (ageV === '' || curV === '') {
            showError('Please enter age and current daily steps.');
            return;
        }
        var age = parseInt(ageV, 10);
        var cur = parseInt(curV, 10);
        if (isNaN(age) || age < 10 || age > 100) {
            showError('Please enter age between 10 and 100.');
            return;
        }
        if (isNaN(cur) || cur < 0 || cur > 60000) {
            showError('Please enter current daily steps between 0 and 60000.');
            return;
        }

        var activity = document.getElementById('activity').value;
        var purpose = document.getElementById('purpose').value;
        var target = baseTarget(age, purpose);

        var note;
        if (cur >= target) {
            target = Math.round((cur + 500) / 500) * 500;
            note = 'Congratulations! You are already at the recommended level — just keep up this routine.';
        } else if (activity === 'sedentary' || activity === 'light') {
            note = 'Your activity level is low, so the plan will start slowly — this is the safest way.';
        } else {
            note = 'You are already active — you will reach the target soon.';
        }

        document.getElementById('goalOut').textContent = fmt(target) + ' steps';
        document.getElementById('goalNote').textContent = note + ' Daily ≈ ' + fmt(target * 0.0007) + ' km and ≈ ' + fmt(target * 0.04) + ' kcal (estimate).';

        var tbody = document.getElementById('planRows');
        tbody.innerHTML = '';
        var step = 1000;
        for (var w = 1; w <= 4; w++) {
            var t = cur + step * w;
            if (t > target) { t = target; }
            t = Math.round(t / 100) * 100;
            var mins = Math.round(t / 100);
            var km = (t * 0.0007).toFixed(1);
            var tr = document.createElement('tr');
            var cells = ['Week ' + w, fmt(t) + ' steps', '≈ ' + mins + ' min', '≈ ' + km + ' km'];
            cells.forEach(function (c) {
                var td = document.createElement('td');
                td.textContent = c;
                tr.appendChild(td);
            });
            tbody.appendChild(tr);
            if (t >= target) { break; }
        }

        results.classList.remove('d-none');
    });
})();
</script>
@endsection
