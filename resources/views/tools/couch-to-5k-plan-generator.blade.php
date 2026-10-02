@extends('layouts.app')

@section('title', 'Couch to 5K Plan Generator - Azlaan Tools')
@section('meta_description', 'A 9-week beginner walk-run plan that takes you to 5 km. Free personalized Couch to 5K schedule.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Couch to 5K Plan Generator</h1>
            <p class="lead text-muted">The famous NHS 9-week walk-run program for beginners — it slowly takes you up to running 30 minutes nonstop (5K). Pick your start date and your plan is ready.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="startDate" class="form-label fw-semibold">Start date</label>
                            <input type="date" class="form-control" id="startDate">
                        </div>
                        <div class="col-md-8">
                            <span class="form-label fw-semibold d-block">Run days (3 per week)</span>
                            <div class="d-flex flex-wrap gap-2" id="dayPicker">
                                <label class="form-check form-check-inline"><input class="form-check-input" type="checkbox" value="1" checked> Mon</label>
                                <label class="form-check form-check-inline"><input class="form-check-input" type="checkbox" value="3" checked> Wed</label>
                                <label class="form-check form-check-inline"><input class="form-check-input" type="checkbox" value="5"> Fri</label>
                                <label class="form-check form-check-inline"><input class="form-check-input" type="checkbox" value="6" checked> Sat</label>
                                <label class="form-check form-check-inline"><input class="form-check-input" type="checkbox" value="0"> Sun</label>
                                <label class="form-check form-check-inline"><input class="form-check-input" type="checkbox" value="2"> Tue</label>
                                <label class="form-check form-check-inline"><input class="form-check-input" type="checkbox" value="4"> Thu</label>
                            </div>
                            <div class="form-text">Select exactly 3 days, with a rest day in between.</div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100 mt-3" id="goBtn">Make My 9-Week Plan</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h2 class="h6 mb-0">Your 9-week schedule</h2>
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="printBtn">Print</button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered" id="planTable">
                                <thead class="table-light">
                                    <tr><th>Week</th><th>Run</th><th>Date</th><th>Workout</th><th>Total time</th></tr>
                                </thead>
                                <tbody id="planBody"></tbody>
                            </table>
                        </div>
                        <div class="alert alert-success small" id="summaryBox"></div>
                    </div>
                    <p class="small text-muted mt-3 mb-0">Disclaimer: this is general fitness information, not a replacement for treatment or medical advice. If you have a health problem, ask your doctor before starting.</p>
                </div>
            </div>

            <div id="printArea" class="d-none">
                <h2>Your Couch to 5K Plan</h2>
                <div id="printContent"></div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Pick a start date and select 3 run-days per week.</li>
                <li>Click "Make My 9-Week Plan" — you will get the date and workout details for every run.</li>
                <li>Print the plan, stick it on your fridge, and tick each run when done.</li>
            </ol>
            <h2>Program rules</h2>
            <ul>
                <li>Before every run, 5 minutes of brisk walk (warm-up); at the end, 5 minutes of walk (cool-down).</li>
                <li>Do not run so fast that you get out of breath — keep a pace where you can talk.</li>
                <li>Take at least 1 rest day per week; skip a run if you feel pain.</li>
            </ul>
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

    // NHS Couch to 5K style plan: each run = workout text + running minutes
    var plan = [
        [ { t: '8 × (60s run + 90s walk)', m: 8 },  { t: '8 × (60s run + 90s walk)', m: 8 },  { t: '8 × (60s run + 90s walk)', m: 8 } ],
        [ { t: '6 × (90s run + 2min walk)', m: 9 },  { t: '6 × (90s run + 2min walk)', m: 9 },  { t: '6 × (90s run + 2min walk)', m: 9 } ],
        [ { t: '90s run, 90s walk, 3min run, 3min walk ×2', m: 9 }, { t: '90s run, 90s walk, 3min run, 3min walk ×2', m: 9 }, { t: '90s run, 90s walk, 3min run, 3min walk ×2', m: 9 } ],
        [ { t: '3min run, 90s walk, 5min run, 2.5min walk, 3min run, 90s walk, 5min run', m: 16 }, { t: '3min run, 90s walk, 5min run, 2.5min walk, 3min run, 90s walk, 5min run', m: 16 }, { t: '3min run, 90s walk, 5min run, 2.5min walk, 3min run, 90s walk, 5min run', m: 16 } ],
        [ { t: '5min run, 3min walk, 5min run, 3min walk, 5min run', m: 15 }, { t: '8min run, 5min walk, 8min run', m: 16 }, { t: '20min continuous run (no walking!)', m: 20 } ],
        [ { t: '5min run, 3min walk, 8min run, 3min walk, 5min run', m: 18 }, { t: '5min run, 3min walk, 8min run, 3min walk, 5min run', m: 18 }, { t: '25min continuous run', m: 25 } ],
        [ { t: '25min continuous run', m: 25 }, { t: '25min continuous run', m: 25 }, { t: '25min continuous run', m: 25 } ],
        [ { t: '28min continuous run', m: 28 }, { t: '28min continuous run', m: 28 }, { t: '28min continuous run', m: 28 } ],
        [ { t: '30min continuous run — 5K!', m: 30 }, { t: '30min continuous run — 5K!', m: 30 }, { t: '30min continuous run — 5K!', m: 30 } ]
    ];

    document.getElementById('startDate').value = new Date().toISOString().slice(0, 10);

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function fmtDate(d) {
        return d.toLocaleDateString('en-GB', { weekday: 'short', day: 'numeric', month: 'short' });
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var startVal = document.getElementById('startDate').value;
        if (!startVal) { showError('Please pick a start date.'); return; }
        var days = [];
        document.querySelectorAll('#dayPicker input[type="checkbox"]:checked').forEach(function (cb) {
            days.push(parseInt(cb.value, 10));
        });
        days.sort(function (a, b) { return a - b; });
        if (days.length !== 3) { showError('Please select exactly 3 run-days (3 runs per week).'); return; }

        var start = new Date(startVal + 'T00:00:00');
        var tbody = document.getElementById('planBody');
        tbody.innerHTML = '';
        var totalRunMin = 0, totalSessions = 0;

        plan.forEach(function (week, wi) {
            var weekStart = new Date(start);
            weekStart.setDate(weekStart.getDate() + wi * 7);
            week.forEach(function (run, ri) {
                var targetDow = days[ri];
                var d = new Date(weekStart);
                var diff = (targetDow - d.getDay() + 7) % 7;
                d.setDate(d.getDate() + diff);
                totalRunMin += run.m;
                totalSessions++;
                var tr = document.createElement('tr');
                var cells = [
                    wi === 0 && ri === 0 ? 'Week 1' : (ri === 0 ? 'Week ' + (wi + 1) : ''),
                    'Run ' + (ri + 1),
                    fmtDate(d),
                    run.t + ' + 5min warm-up/cool-down walk',
                    (run.m + 10) + ' min (run: ' + run.m + ' min)'
                ];
                cells.forEach(function (c, ci) {
                    if (ci === 0 && !c) return; // covered by rowspan cell above
                    var td = document.createElement('td');
                    if (ci === 0) td.rowSpan = 3;
                    td.textContent = c;
                    tr.appendChild(td);
                });
                if (wi === 8 && ri === 2) tr.className = 'table-success';
                tbody.appendChild(tr);
            });
        });

        var summary = document.getElementById('summaryBox');
        summary.textContent = 'Total ' + totalSessions + ' runs, ' + totalRunMin + ' minutes of running. ' +
            'By the end of week 9 you will be able to run 30 minutes nonstop — that means 5K ready! ' +
            'Drink water and stretch after every run.';

        var printContent = document.getElementById('printContent');
        printContent.innerHTML = '';
        var ptable = document.createElement('table');
        ptable.className = 'table table-sm table-bordered';
        ptable.innerHTML = '<thead><tr><th>Week</th><th>Run</th><th>Date</th><th>Workout</th><th>Done</th></tr></thead>';
        var pbody = document.createElement('tbody');
        plan.forEach(function (week, wi) {
            var weekStart = new Date(start);
            weekStart.setDate(weekStart.getDate() + wi * 7);
            week.forEach(function (run, ri) {
                var d = new Date(weekStart);
                d.setDate(d.getDate() + (days[ri] - d.getDay() + 7) % 7);
                var tr = document.createElement('tr');
                tr.innerHTML = '<td>Week ' + (wi + 1) + '</td><td>Run ' + (ri + 1) + '</td><td>' +
                    fmtDate(d) + '</td><td>' + run.t + '</td><td style="width:60px"></td>';
                pbody.appendChild(tr);
            });
        });
        ptable.appendChild(pbody);
        printContent.appendChild(ptable);

        results.classList.remove('d-none');
        results.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });

    document.getElementById('printBtn').addEventListener('click', function () {
        var area = document.getElementById('printArea');
        area.classList.remove('d-none');
        window.print();
        setTimeout(function () { area.classList.add('d-none'); }, 500);
    });
})();
</script>
@endsection
