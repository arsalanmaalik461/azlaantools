@extends('layouts.app')

@section('title', 'Marathon Fueling Planner - Azlaan Tools')
@section('meta_description', 'Plan carbs, gels and hydration for your race. Free marathon and long run fueling schedule planner.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Marathon Fueling Planner</h1>
            <p class="lead text-muted">Plan carbs and water for your race — how many gels, when to drink water, and what to eat before the race. How to avoid hitting the wall.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <label for="distSel" class="form-label fw-semibold">Race distance</label>
                            <select class="form-select" id="distSel">
                                <option value="5">5K</option>
                                <option value="10">10K</option>
                                <option value="21.1" selected>Half Marathon (21.1 km)</option>
                                <option value="42.2">Marathon (42.2 km)</option>
                                <option value="custom">Custom km</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label for="customKm" class="form-label fw-semibold">Custom km</label>
                            <input type="number" class="form-control" id="customKm" min="1" max="200" step="0.1" placeholder="e.g. 15" disabled>
                        </div>
                        <div class="col-6">
                            <label for="goalH" class="form-label fw-semibold">Goal time — hours</label>
                            <input type="number" class="form-control" id="goalH" min="0" max="12" value="2">
                        </div>
                        <div class="col-6">
                            <label for="goalM" class="form-label fw-semibold">Goal time — minutes</label>
                            <input type="number" class="form-control" id="goalM" min="0" max="59" value="0">
                        </div>
                        <div class="col-6">
                            <label for="weightKg" class="form-label fw-semibold">Weight (kg)</label>
                            <input type="number" class="form-control" id="weightKg" min="30" max="200" value="70">
                        </div>
                        <div class="col-6">
                            <label for="levelSel" class="form-label fw-semibold">Experience</label>
                            <select class="form-select" id="levelSel">
                                <option value="rec" selected>Recreational runner</option>
                                <option value="trained">Trained (used to gels)</option>
                            </select>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100 mt-3" id="goBtn">Make My Fueling Plan</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="row g-3 mb-3">
                            <div class="col-4">
                                <div class="card text-center bg-light"><div class="card-body py-3">
                                    <div class="text-muted small">Gels (total)</div>
                                    <div class="h4 mb-0 text-primary" id="statGels">0</div>
                                </div></div>
                            </div>
                            <div class="col-4">
                                <div class="card text-center bg-light"><div class="card-body py-3">
                                    <div class="text-muted small">Carbs / hour</div>
                                    <div class="h4 mb-0 text-primary" id="statCarbs">0g</div>
                                </div></div>
                            </div>
                            <div class="col-4">
                                <div class="card text-center bg-light"><div class="card-body py-3">
                                    <div class="text-muted small">Water (total)</div>
                                    <div class="h4 mb-0 text-primary" id="statWater">0L</div>
                                </div></div>
                            </div>
                        </div>
                        <h2 class="h5">Race day schedule</h2>
                        <div class="table-responsive">
                            <table class="table table-striped table-sm">
                                <thead><tr><th>Time</th><th>KM</th><th>What to do</th></tr></thead>
                                <tbody id="schedRows"></tbody>
                            </table>
                        </div>
                        <h2 class="h5 mt-3">Before the race</h2>
                        <ul id="preList" class="small"></ul>
                        <p class="text-muted small mt-3 mb-0"><strong>Disclaimer:</strong> This is a general estimate based on common guidance, not a replacement for treatment or medical advice. If you have any illness or take medicine, ask a doctor. / This is general guidance, not medical advice.</p>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Enter your race distance and goal time.</li>
                <li>Choose your weight and experience, then press <strong>Make Plan</strong>.</li>
                <li>Follow the schedule for gels and water — do not try new gels in the race, test them in practice first.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var distSel = document.getElementById('distSel');
    var customKm = document.getElementById('customKm');
    var goalH = document.getElementById('goalH');
    var goalM = document.getElementById('goalM');
    var weightKg = document.getElementById('weightKg');
    var levelSel = document.getElementById('levelSel');
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var statGels = document.getElementById('statGels');
    var statCarbs = document.getElementById('statCarbs');
    var statWater = document.getElementById('statWater');
    var schedRows = document.getElementById('schedRows');
    var preList = document.getElementById('preList');

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function fmtTime(mins) {
        var h = Math.floor(mins / 60), m = Math.floor(mins % 60);
        return (h > 0 ? h + 'h ' : '') + m + 'm';
    }

    distSel.addEventListener('change', function () {
        customKm.disabled = distSel.value !== 'custom';
    });

    goBtn.addEventListener('click', function () {
        hideError();
        var dist = distSel.value === 'custom' ? parseFloat(customKm.value) : parseFloat(distSel.value);
        var hrs = parseFloat(goalH.value) || 0;
        var mins = parseFloat(goalM.value) || 0;
        var wt = parseFloat(weightKg.value);
        var trained = levelSel.value === 'trained';
        if (!(dist > 0)) { showError('Please enter a race distance.'); return; }
        var totalMin = hrs * 60 + mins;
        if (!(totalMin > 0)) { showError('Goal time must be more than 0.'); return; }
        if (!(wt >= 30 && wt <= 200)) { showError('Enter weight between 30 and 200 kg.'); return; }

        var totalHrs = totalMin / 60;
        var paceMinPerKm = totalMin / dist;
        // Carbs per hour: 30-60g recreational, up to 90 trained (sports nutrition guidelines)
        var carbsPerHr = trained ? 75 : 50;
        if (totalHrs < 1) carbsPerHr = 30; // short races need less
        var totalCarbs = Math.round(carbsPerHr * totalHrs);
        var gels = Math.max(0, Math.round(totalCarbs / 25)); // 1 gel ~= 25g carbs
        var waterMlPerHr = 600;
        var totalWaterL = (waterMlPerHr * totalHrs / 1000);

        statGels.textContent = gels;
        statCarbs.textContent = carbsPerHr + 'g';
        statWater.textContent = totalWaterL.toFixed(1) + 'L';

        // Build schedule: first fuel at ~35 min, then every 25-30 min
        schedRows.innerHTML = '';
        var interval = trained ? 25 : 30;
        var t = 35, n = 1;
        function addRow(timeMin, km, text) {
            var tr = document.createElement('tr');
            var td1 = document.createElement('td'); td1.textContent = fmtTime(Math.round(timeMin));
            var td2 = document.createElement('td'); td2.textContent = km.toFixed(1) + ' km';
            var td3 = document.createElement('td'); td3.textContent = text;
            tr.appendChild(td1); tr.appendChild(td2); tr.appendChild(td3);
            schedRows.appendChild(tr);
        }
        addRow(0, 0, 'Start — a sip of water, do not drink too much.');
        while (t < totalMin - 10 && n <= gels) {
            var km = t / paceMinPerKm;
            var what = 'Gel #' + n + ' + 150-200ml water';
            if (n % 3 === 0) what += ' (better with an electrolyte drink)';
            addRow(t, Math.min(km, dist), what);
            t += interval; n++;
        }
        if (gels === 0) {
            addRow(totalMin / 2, dist / 2, 'Water station halfway — 150ml water.');
        }
        addRow(totalMin, dist, 'Finish! Eat carbs + protein within 30 minutes.');

        preList.innerHTML = '';
        var carbLoad = Math.round(wt * (trained ? 9 : 7.5));
        var items = [
            'One day before the race: eat about ' + carbLoad + 'g carbs (rice, bread, pasta, dates).',
            '3 hours before the race: light breakfast (300-400 calories) — what you tried in practice.',
            '2 hours before the race: 400-500ml water; one sip 15 minutes before the start.',
            'Do not try new gels, new shoes or new food on RACE day.'
        ];
        items.forEach(function (it) {
            var li = document.createElement('li');
            li.textContent = it;
            preList.appendChild(li);
        });

        results.classList.remove('d-none');
    });
})();
</script>
@endsection
