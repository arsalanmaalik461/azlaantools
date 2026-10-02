@extends('layouts.app')

@section('title', 'Baby Sleep Schedule Planner - Azlaan Tools')
@section('meta_description', 'Get an age-based baby sleep schedule with naps, wake windows and bedtime suggestions, free online.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Baby Sleep Schedule Planner</h1>
            <p class="lead text-muted">Build a sleep routine for your baby by age — naps, wake window and bedtime.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="bsAge" class="form-label fw-semibold">Baby age (months)</label>
                            <input type="number" class="form-control" id="bsAge" placeholder="e.g. 8" min="0" max="60" step="1">
                        </div>
                        <div class="col-md-6">
                            <label for="bsWake" class="form-label fw-semibold">Morning wake-up time</label>
                            <input type="time" class="form-control" id="bsWake" value="07:00">
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100 mt-3" id="goBtn">Make Schedule</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <h2 class="h5" id="bsTitle"></h2>
                        <div class="row text-center g-2 mb-3">
                            <div class="col-6 col-md-3"><div class="card bg-light"><div class="card-body py-2"><small class="text-muted">Total sleep</small><div class="fw-bold" id="bsTotal"></div></div></div></div>
                            <div class="col-6 col-md-3"><div class="card bg-light"><div class="card-body py-2"><small class="text-muted">Naps</small><div class="fw-bold" id="bsNaps"></div></div></div></div>
                            <div class="col-6 col-md-3"><div class="card bg-light"><div class="card-body py-2"><small class="text-muted">Wake window</small><div class="fw-bold" id="bsWindow"></div></div></div></div>
                            <div class="col-6 col-md-3"><div class="card bg-light"><div class="card-body py-2"><small class="text-muted">Bedtime</small><div class="fw-bold" id="bsBed"></div></div></div></div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-striped">
                                <thead class="table-light"><tr><th>Time</th><th>What to do</th></tr></thead>
                                <tbody id="bsTable"></tbody>
                            </table>
                        </div>
                        <div class="alert alert-info small mb-0" id="bsTips"></div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Type your baby's age (in months) and the morning wake-up time.</li>
                <li>Press <strong>Make Schedule</strong> — you get the nap chart and bedtime.</li>
                <li>Every baby is different — pay more attention to your baby's sleepy signs (yawning, fussiness) than to the schedule.</li>
            </ol>
            <p class="small text-muted">This is an estimate, not a medical treatment. If your baby has trouble sleeping, talk to your pediatrician.</p>
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
    function hideError() { errorBox.classList.add('d-none'); errorBox.textContent = ''; }

    // Age bands: months, ranges based on widely used pediatric guidance (approx values).
    var BANDS = [
        { max: 1,  label: 'Newborn (0-1 month)',   total: '14-17 hours', naps: 'No fixed naps (short sleep bursts)', windowMin: 45, windowMax: 60,  napsCount: 0, napLen: '', bedOffsetH: 13,
          tips: 'A newborn needs a feed every 2-3 hours. To teach the difference between day and night, keep light in the day and quiet at night.' },
        { max: 3,  label: 'Infant (1-3 months)',   total: '14-17 hours', naps: '4-6 short naps', windowMin: 60, windowMax: 90,  napsCount: 4, napLen: '30-60 min', bedOffsetH: 12,
          tips: 'Keep short wake windows (1-1.5h). An over-tired baby is harder to settle.' },
        { max: 6,  label: 'Baby (4-6 months)',     total: '12-16 hours', naps: '3-4 naps', windowMin: 90, windowMax: 120, napsCount: 3, napLen: '45-90 min', bedOffsetH: 12,
          tips: 'The morning nap is usually the longest. Start a soothing bedtime routine (bath, book, lullaby).' },
        { max: 12, label: 'Baby (7-12 months)',    total: '12-16 hours', naps: '2-3 naps', windowMin: 120, windowMax: 180, napsCount: 2, napLen: '1-2 hours', bedOffsetH: 12,
          tips: 'At 9-10 months most babies shift to 2 naps. Keep the 3rd nap short in the afternoon so bedtime is not late.' },
        { max: 24, label: 'Toddler (1-2 years)',   total: '11-14 hours', naps: '1-2 naps', windowMin: 180, windowMax: 240, napsCount: 1, napLen: '1-2 hours', bedOffsetH: 12,
          tips: 'At 12-18 months most babies drop to one nap (midday). Bedtime around 7-8 works best.' },
        { max: 36, label: 'Toddler (2-3 years)',   total: '10-13 hours', naps: '1 nap', windowMin: 240, windowMax: 300, napsCount: 1, napLen: '1-2 hours', bedOffsetH: 12,
          tips: 'Keep the nap near 1-2 in the afternoon. A late nap pushes bedtime back.' },
        { max: 60, label: 'Preschooler (3-5 years)', total: '10-13 hours', naps: '0-1 nap', windowMin: 360, windowMax: 420, napsCount: 0, napLen: '', bedOffsetH: 12,
          tips: 'At 3-4 years the nap usually ends. Quiet time (no screens) can take the place of the nap.' }
    ];

    function bandFor(age) {
        for (var i = 0; i < BANDS.length; i++) { if (age <= BANDS[i].max) return BANDS[i]; }
        return BANDS[BANDS.length - 1];
    }
    function toMin(t) {
        var p = t.split(':');
        return Number(p[0]) * 60 + Number(p[1]);
    }
    function fmtT(min) {
        min = ((min % 1440) + 1440) % 1440;
        var h = Math.floor(min / 60), m = min % 60;
        return ('0' + h).slice(-2) + ':' + ('0' + m).slice(-2);
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var ageRaw = document.getElementById('bsAge').value.trim();
        var wakeRaw = document.getElementById('bsWake').value;
        var age = Number(ageRaw);
        if (ageRaw === '' || isNaN(age) || age < 0 || age > 60) {
            showError('Enter age between 0 and 60 months.');
            return;
        }
        if (!wakeRaw) { showError('Pick a morning wake time.'); return; }

        var b = bandFor(age);
        var wake = toMin(wakeRaw);
        var midWindow = (b.windowMin + b.windowMax) / 2;
        var bedtime = wake + b.bedOffsetH * 60;

        document.getElementById('bsTitle').textContent = 'Schedule: ' + b.label;
        document.getElementById('bsTotal').textContent = b.total;
        document.getElementById('bsNaps').textContent = b.naps;
        document.getElementById('bsWindow').textContent = (b.windowMin / 60) + '-' + (b.windowMax / 60) + ' hours';
        document.getElementById('bsBed').textContent = fmtT(bedtime);

        var rows = [];
        rows.push([fmtT(wake), 'Wake up + feed']);
        if (b.napsCount > 0) {
            var t = wake + midWindow;
            for (var n = 1; n <= b.napsCount; n++) {
                rows.push([fmtT(t), 'Nap ' + n + ' (' + b.napLen + ')']);
                t = t + midWindow + 60; // nap duration + next wake window approx
                if (n < b.napsCount) rows.push([fmtT(t - midWindow), 'Feed + playtime']);
            }
        }
        rows.push([fmtT(bedtime - 30), 'Bedtime routine (bath, lullaby)']);
        rows.push([fmtT(bedtime), 'Sleep (bedtime)']);

        var tbody = document.getElementById('bsTable');
        tbody.innerHTML = '';
        rows.forEach(function (r) {
            var tr = document.createElement('tr');
            var td1 = document.createElement('td'); td1.textContent = r[0];
            var td2 = document.createElement('td'); td2.textContent = r[1];
            tr.appendChild(td1); tr.appendChild(td2);
            tbody.appendChild(tr);
        });

        document.getElementById('bsTips').textContent = 'Tip: ' + b.tips;
        results.classList.remove('d-none');
    });
})();
</script>
@endsection
