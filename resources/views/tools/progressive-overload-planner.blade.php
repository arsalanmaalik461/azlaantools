@extends('layouts.app')

@section('title', 'Progressive Overload Planner - Azlaan Tools')
@section('meta_description', 'Build a week-by-week progressive overload strength plan with increasing weight or reps. Free online planner.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Progressive Overload Planner</h1>
            <p class="lead text-muted">Build a step-by-step plan to increase weight or reps every week — an easy planner for strength gain.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="exName" class="form-label fw-semibold">Exercise name</label>
                            <input type="text" class="form-control" id="exName" placeholder="e.g. Squat, Bench Press">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="progMode" class="form-label fw-semibold">Progression mode</label>
                            <select class="form-select" id="progMode">
                                <option value="weight">Increase weight every week</option>
                                <option value="reps">Increase reps every week (then weight)</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4 mb-3">
                            <label for="startWeight" class="form-label fw-semibold">Start weight (kg)</label>
                            <input type="number" class="form-control" id="startWeight" placeholder="e.g. 60" min="0" step="any">
                        </div>
                        <div class="col-4 mb-3">
                            <label for="sets" class="form-label fw-semibold">Sets</label>
                            <input type="number" class="form-control" id="sets" placeholder="e.g. 4" min="1" max="20" step="1">
                        </div>
                        <div class="col-4 mb-3">
                            <label for="startReps" class="form-label fw-semibold">Reps per set</label>
                            <input type="number" class="form-control" id="startReps" placeholder="e.g. 8" min="1" max="50" step="1">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4 mb-3">
                            <label for="weeklyInc" class="form-label fw-semibold" id="incLabel">Weekly + kg</label>
                            <input type="number" class="form-control" id="weeklyInc" placeholder="e.g. 2.5" min="0" step="any">
                        </div>
                        <div class="col-4 mb-3">
                            <label for="weeks" class="form-label fw-semibold">Weeks</label>
                            <input type="number" class="form-control" id="weeks" placeholder="e.g. 8" min="2" max="24" step="1">
                        </div>
                        <div class="col-4 mb-3">
                            <label for="deload" class="form-label fw-semibold">Deload week</label>
                            <select class="form-select" id="deload">
                                <option value="0">Not needed</option>
                                <option value="4">Every 4th week (light)</option>
                            </select>
                        </div>
                    </div>
                    <div id="repCapWrap" class="mb-3 d-none">
                        <label for="repCap" class="form-label fw-semibold">Reps cap (weight increases after this)</label>
                        <input type="number" class="form-control" id="repCap" value="12" min="2" max="30" step="1">
                    </div>

                    <button type="button" class="btn btn-primary w-100" id="goBtn">Create Plan</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <h5 id="planTitle">Your plan</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="table-light">
                                    <tr><th>Week</th><th>Weight</th><th>Sets x Reps</th><th>Volume</th><th>Note</th></tr>
                                </thead>
                                <tbody id="planBody"></tbody>
                            </table>
                        </div>
                        <p class="text-muted small" id="planSummary"></p>
                        <button type="button" class="btn btn-outline-secondary w-100" id="printBtn">Print / Make PDF</button>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Enter the exercise, your current weight, sets and reps.</li>
                <li>Choose the progression mode — increase weight or increase reps.</li>
                <li>Enter the weeks and weekly increase, then click "Create Plan".</li>
            </ol>
            <div class="alert alert-warning mt-3">
                <strong>Disclaimer:</strong> This is only an estimate, not medical advice or treatment. Always lift with correct form, and contact a trainer or doctor if you feel pain.
            </div>
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
    var progMode = document.getElementById('progMode');

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function num(id) {
        var v = parseFloat(document.getElementById(id).value);
        return isNaN(v) ? null : v;
    }
    function roundHalf(x) { return Math.round(x * 2) / 2; }

    progMode.addEventListener('change', function () {
        var isReps = progMode.value === 'reps';
        document.getElementById('repCapWrap').classList.toggle('d-none', !isReps);
        document.getElementById('incLabel').textContent = isReps ? 'Weekly + reps' : 'Weekly + kg';
        document.getElementById('weeklyInc').placeholder = isReps ? 'e.g. 1' : 'e.g. 2.5';
    });

    goBtn.addEventListener('click', function () {
        hideError();
        var mode = progMode.value;
        var w0 = num('startWeight');
        var sets = num('sets');
        var r0 = num('startReps');
        var inc = num('weeklyInc');
        var weeks = num('weeks');
        var deloadEvery = parseInt(document.getElementById('deload').value, 10);
        var repCap = num('repCap') || 12;

        if (w0 === null || w0 < 0 || sets === null || sets < 1 || r0 === null || r0 < 1 ||
            inc === null || inc <= 0 || weeks === null || weeks < 2 || weeks > 24) {
            showError('Please enter valid values for all fields.');
            return;
        }
        if (mode === 'reps' && (repCap < r0)) {
            showError('Reps cap cannot be less than start reps.');
            return;
        }

        var body = document.getElementById('planBody');
        body.innerHTML = '';
        var w = w0, r = r0;
        var totalVol = 0;

        for (var wk = 1; wk <= weeks; wk++) {
            var note = '';
            var curW = w, curR = r;
            if (deloadEvery > 0 && wk % deloadEvery === 0) {
                curW = roundHalf(w * 0.6);
                note = 'Deload — light week (recovery)';
            } else if (wk > 1) {
                if (mode === 'weight') {
                    w = roundHalf(w + inc);
                    curW = w;
                } else {
                    r = r + inc;
                    if (r > repCap) {
                        w = roundHalf(w + 2.5);
                        r = r0;
                        curW = w;
                        curR = r;
                        note = 'Reps cap reached — weight increased';
                    } else {
                        curR = r;
                    }
                }
            }
            var vol = curW * sets * curR;
            totalVol += vol;
            var tr = document.createElement('tr');
            var cells = [
                'Week ' + wk,
                curW + ' kg',
                sets + ' x ' + curR,
                Math.round(vol) + ' kg',
                note
            ];
            cells.forEach(function (c) {
                var td = document.createElement('td');
                td.textContent = c;
                tr.appendChild(td);
            });
            body.appendChild(tr);
        }

        var ex = document.getElementById('exName').value.trim() || 'Exercise';
        document.getElementById('planTitle').textContent = ex + ' — ' + weeks + ' week plan';
        document.getElementById('planSummary').textContent =
            'Start: ' + w0 + ' kg x ' + sets + ' x ' + r0 + '. Total estimated volume: ' + Math.round(totalVol) + ' kg. ' +
            'If your form breaks down in any week, stay at the same weight for one more week.';
        results.classList.remove('d-none');
    });

    document.getElementById('printBtn').addEventListener('click', function () {
        window.print();
    });
})();
</script>
@endsection
