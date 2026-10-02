@extends('layouts.app')

@section('title', 'Speed Distance Time Calculator — Azlaan Tools')
@section('meta_description', 'Free speed distance time calculator. Fill any two values and get the third instantly, with unit conversion between km/h, m/s, mph, kilometres, metres, miles, hours, minutes and seconds.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-2">Speed Distance Time Calculator</h1>
            <p class="lead text-muted">Enter any two of the three values — the third one will be calculated by itself. You can also choose your own units; conversion is automatic.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-2 align-items-end mb-3">
                        <div class="col-7">
                            <label for="speedVal" class="form-label fw-semibold">Speed</label>
                            <input type="number" class="form-control form-control-lg" id="speedVal" placeholder="e.g. 50" step="any">
                        </div>
                        <div class="col-5">
                            <label for="speedUnit" class="form-label">Unit</label>
                            <select class="form-select form-select-lg" id="speedUnit">
                                <option value="kmh" selected>km/h</option>
                                <option value="ms">m/s</option>
                                <option value="mph">mph</option>
                            </select>
                        </div>
                    </div>
                    <div class="row g-2 align-items-end mb-3">
                        <div class="col-7">
                            <label for="distVal" class="form-label fw-semibold">Distance</label>
                            <input type="number" class="form-control form-control-lg" id="distVal" placeholder="e.g. 100" step="any">
                        </div>
                        <div class="col-5">
                            <label for="distUnit" class="form-label">Unit</label>
                            <select class="form-select form-select-lg" id="distUnit">
                                <option value="km" selected>Kilometres (km)</option>
                                <option value="m">Metres (m)</option>
                                <option value="mi">Miles</option>
                            </select>
                        </div>
                    </div>
                    <div class="row g-2 align-items-end">
                        <div class="col-7">
                            <label for="timeVal" class="form-label fw-semibold">Time</label>
                            <input type="number" class="form-control form-control-lg" id="timeVal" placeholder="e.g. 2" step="any">
                        </div>
                        <div class="col-5">
                            <label for="timeUnit" class="form-label">Unit</label>
                            <select class="form-select form-select-lg" id="timeUnit">
                                <option value="h" selected>Hours</option>
                                <option value="min">Minutes</option>
                                <option value="s">Seconds</option>
                            </select>
                        </div>
                    </div>
                    <p class="small text-muted mt-2 mb-0">Tip: leave the third box empty to calculate it. If all three are filled, their consistency is checked.</p>

                    <div class="result-box mt-3">
                        <div class="fs-5 fw-bold" id="sdtResult">Enter any two values — the third one will appear here.</div>
                        <p class="mb-1 mt-2" id="sdtFormula"></p>
                        <p class="small text-muted mb-0" id="sdtBreakdown"></p>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How it works</h2>
                    <p>The three basic formulas are: <strong>Speed = Distance &divide; Time</strong>, <strong>Distance = Speed &times; Time</strong> and <strong>Time = Distance &divide; Speed</strong>. The calculator first converts all values to metres and seconds, does the calculation, then shows the answer in every common unit.</p>
                    <p class="mb-1"><strong>Example 1:</strong> 100 km distance, 50 km/h speed — Time = 100 &divide; 50 = <strong>2 hours</strong>.</p>
                    <p class="mb-0"><strong>Example 2:</strong> travelling 30 minutes at 60 km/h gives Distance = 60 &times; 0.5 = <strong>30 km</strong>. Remember: 1 m/s = 3.6 km/h.</p>
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
        if (v === '' || v === null) return null;
        var p = parseFloat(v);
        return isFinite(p) ? p : null;
    }
    function fmt(n, dec) {
        if (!isFinite(n)) return '—';
        var d = (typeof dec === 'number') ? dec : 2;
        return Number(n.toFixed(d)).toLocaleString('en-PK', { maximumFractionDigits: d });
    }
    function speedToMs(v, u) {
        if (u === 'kmh') return v / 3.6;
        if (u === 'mph') return v * 0.44704;
        return v;
    }
    function distToM(v, u) {
        if (u === 'km') return v * 1000;
        if (u === 'mi') return v * 1609.344;
        return v;
    }
    function timeToS(v, u) {
        if (u === 'h') return v * 3600;
        if (u === 'min') return v * 60;
        return v;
    }
    function timeBreakdown(sec) {
        var total = Math.round(sec);
        var h = Math.floor(total / 3600);
        var m = Math.floor((total % 3600) / 60);
        var s = total % 60;
        var parts = [];
        if (h > 0) parts.push(h + ' h');
        if (m > 0) parts.push(m + ' min');
        if (s > 0 || parts.length === 0) parts.push(s + ' sec');
        return parts.join(' ');
    }
    function calc() {
        var resEl = document.getElementById('sdtResult');
        var fEl = document.getElementById('sdtFormula');
        var bEl = document.getElementById('sdtBreakdown');
        var sv = num('speedVal'), dv = num('distVal'), tv = num('timeVal');
        var su = document.getElementById('speedUnit').value;
        var du = document.getElementById('distUnit').value;
        var tu = document.getElementById('timeUnit').value;
        var filled = (sv !== null ? 1 : 0) + (dv !== null ? 1 : 0) + (tv !== null ? 1 : 0);
        if (filled < 2) {
            resEl.textContent = 'Enter any two values — the third one will appear here.';
            fEl.textContent = ''; bEl.textContent = '';
            return;
        }
        var ms = (sv !== null) ? speedToMs(sv, su) : null;
        var metres = (dv !== null) ? distToM(dv, du) : null;
        var secs = (tv !== null) ? timeToS(tv, tu) : null;

        if (sv === null) {
            if (secs === 0) { resEl.textContent = 'Speed cannot be calculated when time is zero.'; fEl.textContent = ''; bEl.textContent = ''; return; }
            ms = metres / secs;
            resEl.textContent = 'Speed = ' + fmt(ms * 3.6) + ' km/h';
            fEl.textContent = 'Formula: Speed = Distance ÷ Time = ' + fmt(metres / 1000) + ' km ÷ ' + fmt(secs / 3600) + ' h';
            bEl.textContent = 'Also: ' + fmt(ms) + ' m/s · ' + fmt(ms / 0.44704) + ' mph';
        } else if (dv === null) {
            metres = ms * secs;
            resEl.textContent = 'Distance = ' + fmt(metres / 1000) + ' km';
            fEl.textContent = 'Formula: Distance = Speed × Time = ' + fmt(ms * 3.6) + ' km/h × ' + fmt(secs / 3600) + ' h';
            bEl.textContent = 'Also: ' + fmt(metres) + ' m · ' + fmt(metres / 1609.344) + ' miles';
        } else if (tv === null) {
            if (ms === 0) { resEl.textContent = 'Time cannot be calculated when speed is zero.'; fEl.textContent = ''; bEl.textContent = ''; return; }
            secs = metres / ms;
            resEl.textContent = 'Time = ' + timeBreakdown(secs);
            fEl.textContent = 'Formula: Time = Distance ÷ Speed = ' + fmt(metres / 1000) + ' km ÷ ' + fmt(ms * 3.6) + ' km/h = ' + fmt(secs / 3600) + ' h';
            bEl.textContent = 'Also: ' + fmt(secs / 3600) + ' hours · ' + fmt(secs / 60) + ' minutes · ' + fmt(secs, 0) + ' seconds';
        } else {
            if (secs === 0 || ms === 0) { resEl.textContent = 'Check all three values — cannot calculate with zero speed/time.'; fEl.textContent = ''; bEl.textContent = ''; return; }
            var expectedDist = ms * secs;
            var diffPct = Math.abs(expectedDist - metres) / metres * 100;
            resEl.textContent = 'All three values are filled — with this speed and time the expected distance is ' + fmt(expectedDist / 1000) + ' km.';
            fEl.textContent = 'Formula: Distance = Speed × Time (check)';
            bEl.textContent = diffPct < 1 ? 'All three values are consistent with each other ✓ (difference less than 1%).' : 'Difference: ' + fmt(diffPct, 1) + '% — one value looks wrong. Empty one box and calculate again.';
        }
    }
    var ids = ['speedVal', 'distVal', 'timeVal'];
    var sels = ['speedUnit', 'distUnit', 'timeUnit'];
    for (var i = 0; i < ids.length; i++) document.getElementById(ids[i]).addEventListener('input', calc);
    for (var j = 0; j < sels.length; j++) document.getElementById(sels[j]).addEventListener('change', calc);
    calc();
})();
</script>
@endsection
