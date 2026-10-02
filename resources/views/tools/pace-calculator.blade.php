@extends('layouts.app')

@section('title', 'Pace Calculator — Azlaan Tools')
@section('meta_description', 'Free running pace calculator. Enter distance and time to get pace per km and per mile plus speed, predict finish times from a target pace, and see finish times for 5K, 10K, half marathon and marathon.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-2">Pace Calculator</h1>
            <p class="lead text-muted">For runners and walkers — enter distance and time to get your pace per km and per mile instantly. Reverse calculation is also here: enter your target pace and see your finish time.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 card-title">1. Time + Distance &rarr; Pace</h2>
                    <div class="mb-3">
                        <label for="runDist" class="form-label fw-semibold">Distance (km)</label>
                        <input type="number" class="form-control form-control-lg" id="runDist" placeholder="e.g. 10" step="any">
                    </div>
                    <div class="row g-2">
                        <div class="col-4">
                            <label for="timeH" class="form-label">Hours</label>
                            <input type="number" class="form-control form-control-lg" id="timeH" placeholder="0" step="any" min="0">
                        </div>
                        <div class="col-4">
                            <label for="timeM" class="form-label">Minutes</label>
                            <input type="number" class="form-control form-control-lg" id="timeM" placeholder="e.g. 50" step="any" min="0">
                        </div>
                        <div class="col-4">
                            <label for="timeS" class="form-label">Seconds</label>
                            <input type="number" class="form-control form-control-lg" id="timeS" placeholder="0" step="any" min="0">
                        </div>
                    </div>
                    <div class="result-box mt-3">
                        <div class="row text-center g-3">
                            <div class="col-4">
                                <div class="text-muted small">Pace / km</div>
                                <div class="fs-4 fw-bold" id="paceKm">—</div>
                            </div>
                            <div class="col-4">
                                <div class="text-muted small">Pace / mile</div>
                                <div class="fs-4 fw-bold" id="paceMile">—</div>
                            </div>
                            <div class="col-4">
                                <div class="text-muted small">Speed</div>
                                <div class="fs-4 fw-bold" id="runSpeed">—</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 card-title">2. Pace + Distance &rarr; Finish Time</h2>
                    <div class="row g-2">
                        <div class="col-4">
                            <label for="revPaceMin" class="form-label">Pace min / km</label>
                            <input type="number" class="form-control form-control-lg" id="revPaceMin" placeholder="e.g. 5" step="any" min="0">
                        </div>
                        <div class="col-4">
                            <label for="revPaceSec" class="form-label">Pace sec / km</label>
                            <input type="number" class="form-control form-control-lg" id="revPaceSec" placeholder="0" step="any" min="0">
                        </div>
                        <div class="col-4">
                            <label for="revDist" class="form-label">Distance (km)</label>
                            <input type="number" class="form-control form-control-lg" id="revDist" placeholder="e.g. 21.1" step="any">
                        </div>
                    </div>
                    <div class="result-box mt-3 text-center">
                        <div class="text-muted small">Predicted Finish Time</div>
                        <div class="fs-4 fw-bold" id="finishTime">—</div>
                    </div>

                    <h2 class="h6 fw-semibold mt-4">Finish times at this pace</h2>
                    <p class="small text-muted">Expected times for popular races at the pace above (section 2 first, otherwise section 1):</p>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered mb-0">
                            <thead><tr><th>Race</th><th>Distance</th><th>Finish Time</th></tr></thead>
                            <tbody id="paceTableBody">
                                <tr><td colspan="3" class="text-muted">Enter a pace to fill the table.</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How it works</h2>
                    <p>Pace means the time it takes to cover one kilometre (or mile). Formula: <strong>Pace = Total Time &divide; Distance</strong>. In the reverse calculation, finish time = pace &times; distance. Speed and pace are opposites of each other: speed (km/h) = 60 &divide; pace (min/km).</p>
                    <p class="mb-1"><strong>Example 1:</strong> 10 km in 50 minutes — pace = 50 &divide; 10 = <strong>5:00 per km</strong>, speed = 12 km/h.</p>
                    <p class="mb-0"><strong>Example 2:</strong> At a 6:00/km pace, marathon (42.195 km) finish time = 6 &times; 42.195 &asymp; <strong>4 hours 13 minutes</strong>.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    function num(id) {
        var v = document.getElementById(id).value;
        if (v === '' || v === null) return 0;
        var p = parseFloat(v);
        return isFinite(p) ? p : 0;
    }
    function hasVal(id) {
        return document.getElementById(id).value !== '';
    }
    function fmtPace(secPerUnit) {
        if (!isFinite(secPerUnit) || secPerUnit <= 0) return '—';
        var total = Math.round(secPerUnit);
        var m = Math.floor(total / 60);
        var s = total % 60;
        return m + ':' + (s < 10 ? '0' : '') + s;
    }
    function fmtDuration(sec) {
        if (!isFinite(sec) || sec <= 0) return '—';
        var total = Math.round(sec);
        var h = Math.floor(total / 3600);
        var m = Math.floor((total % 3600) / 60);
        var s = total % 60;
        var out = '';
        if (h > 0) out += h + 'h ';
        out += m + 'm ';
        out += s + 's';
        return out.trim();
    }
    function currentPaceSecPerKm() {
        var revPace = num('revPaceMin') * 60 + num('revPaceSec');
        if ((hasVal('revPaceMin') || hasVal('revPaceSec')) && revPace > 0) return revPace;
        var dist = num('runDist');
        var secs = num('timeH') * 3600 + num('timeM') * 60 + num('timeS');
        if (dist > 0 && secs > 0) return secs / dist;
        return null;
    }
    function renderTable(paceSecKm) {
        var body = document.getElementById('paceTableBody');
        if (paceSecKm === null) {
            body.innerHTML = '<tr><td colspan="3" class="text-muted">Enter a pace to fill the table.</td></tr>';
            return;
        }
        var races = [
            ['5K', 5],
            ['10K', 10],
            ['Half Marathon', 21.0975],
            ['Marathon', 42.195]
        ];
        var rows = '';
        for (var i = 0; i < races.length; i++) {
            rows += '<tr><td>' + races[i][0] + '</td><td>' + races[i][1] + ' km</td><td class="fw-semibold">' + fmtDuration(paceSecKm * races[i][1]) + '</td></tr>';
        }
        body.innerHTML = rows;
    }
    function calc() {
        var dist = num('runDist');
        var secs = num('timeH') * 3600 + num('timeM') * 60 + num('timeS');
        var paceKmEl = document.getElementById('paceKm');
        var paceMileEl = document.getElementById('paceMile');
        var speedEl = document.getElementById('runSpeed');
        if (dist > 0 && secs > 0) {
            var spk = secs / dist;
            paceKmEl.textContent = fmtPace(spk) + ' /km';
            paceMileEl.textContent = fmtPace(spk * 1.609344) + ' /mi';
            speedEl.textContent = (dist / (secs / 3600)).toFixed(2) + ' km/h';
        } else {
            paceKmEl.textContent = '—';
            paceMileEl.textContent = '—';
            speedEl.textContent = '—';
        }
        var revPace = num('revPaceMin') * 60 + num('revPaceSec');
        var revDist = num('revDist');
        var finEl = document.getElementById('finishTime');
        if (revPace > 0 && revDist > 0) {
            finEl.textContent = fmtDuration(revPace * revDist);
        } else {
            finEl.textContent = '—';
        }
        renderTable(currentPaceSecPerKm());
    }
    var ids = ['runDist', 'timeH', 'timeM', 'timeS', 'revPaceMin', 'revPaceSec', 'revDist'];
    for (var i = 0; i < ids.length; i++) {
        document.getElementById(ids[i]).addEventListener('input', calc);
    }
    calc();
})();
</script>
@endsection
