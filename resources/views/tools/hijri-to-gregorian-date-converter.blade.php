@extends('layouts.app')
@section('title', 'Hijri to Gregorian Date Converter - Azlaan Tools')
@section('meta_description', 'Convert a Hijri date to an English (Gregorian) date — free online Islamic date converter.')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Hijri to Gregorian Date Converter</h1>
            <p class="lead text-muted">Convert a Hijri (Islamic) date to an English (Gregorian) date — or the other way. In 1 click, free.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <ul class="nav nav-tabs mb-3" id="modeTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="tabH2G" type="button">Hijri → Gregorian</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tabG2H" type="button">Gregorian → Hijri</button>
                        </li>
                    </ul>

                    <div id="h2gPane">
                        <div class="row g-3 mb-3">
                            <div class="col-4">
                                <label for="hDay" class="form-label fw-semibold">Day</label>
                                <input type="number" class="form-control" id="hDay" min="1" max="30" value="1">
                            </div>
                            <div class="col-8">
                                <label for="hMonth" class="form-label fw-semibold">Hijri month</label>
                                <select id="hMonth" class="form-select"></select>
                            </div>
                            <div class="col-12">
                                <label for="hYear" class="form-label fw-semibold">Hijri year</label>
                                <input type="number" class="form-control" id="hYear" min="1" max="1600" value="1448">
                            </div>
                        </div>
                    </div>

                    <div id="g2hPane" class="d-none">
                        <div class="mb-3">
                            <label for="gDate" class="form-label fw-semibold">Gregorian date</label>
                            <input type="date" class="form-control" id="gDate">
                        </div>
                    </div>

                    <button type="button" class="btn btn-primary w-100" id="goBtn">Convert</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="card border-success">
                            <div class="card-body text-center">
                                <div class="text-muted small mb-1" id="resLabel"></div>
                                <div class="fs-3 fw-bold text-success" id="resDate"></div>
                                <div class="text-muted mt-1" id="resWeekday"></div>
                                <div class="text-muted small mt-2" id="resAlt"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="alert alert-warning">
                <strong>Note:</strong> This is based on the tabular (calculated) Islamic calendar — it can differ by 1-2 days from the real moon-sighting announcement. For fasting, Eid and similar days, follow the official moon announcement in your area.
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Choose a direction: Hijri → Gregorian or Gregorian → Hijri.</li>
                <li>Enter the date and press <strong>Convert</strong>.</li>
                <li>The result also shows the day of the week.</li>
            </ol>

            <h2>Hijri months</h2>
            <p class="text-muted">Muharram, Safar, Rabi-ul-Awwal, Rabi-us-Sani, Jamadi-ul-Awwal, Jamadi-us-Sani, Rajab, Shaban, Ramadan, Shawwal, Zil-Qad, Zil-Hajj.</p>
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
    var mode = 'h2g';

    var HIJRI_MONTHS = ['Muharram', 'Safar', 'Rabi-ul-Awwal', 'Rabi-us-Sani', 'Jamadi-ul-Awwal', 'Jamadi-us-Sani', 'Rajab', 'Shaban', 'Ramadan', 'Shawwal', 'Zil-Qad', 'Zil-Hajj'];
    var GREG_MONTHS = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    var WEEKDAYS = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

    var hMonth = document.getElementById('hMonth');
    for (var i = 0; i < HIJRI_MONTHS.length; i++) {
        var op = document.createElement('option');
        op.value = i + 1;
        op.textContent = (i + 1) + ' - ' + HIJRI_MONTHS[i];
        hMonth.appendChild(op);
    }

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }

    // Tabular Islamic calendar: Hijri -> Julian Day Number
    function hijriToJd(y, m, d) {
        return d + Math.ceil(29.5 * (m - 1)) + (y - 1) * 354 +
            Math.floor((3 + 11 * y) / 30) + 1948439.5 - 1;
    }
    // Julian Day Number -> Gregorian date
    function jdToGregorian(jd) {
        var z = Math.floor(jd + 0.5);
        var a = Math.floor((z - 1867216.25) / 36524.25);
        a = z + 1 + a - Math.floor(a / 4);
        var b = a + 1524;
        var c = Math.floor((b - 122.1) / 365.25);
        var d = Math.floor(365.25 * c);
        var e = Math.floor((b - d) / 30.6001);
        var day = b - d - Math.floor(30.6001 * e);
        var month = e < 14 ? e - 1 : e - 13;
        var year = month > 2 ? c - 4716 : c - 4715;
        return { y: year, m: month, d: day };
    }
    // Gregorian -> Julian Day Number
    function gregorianToJd(y, m, d) {
        var a = Math.floor((14 - m) / 12);
        var y2 = y + 4800 - a;
        var m2 = m + 12 * a - 3;
        return d + Math.floor((153 * m2 + 2) / 5) + 365 * y2 +
            Math.floor(y2 / 4) - Math.floor(y2 / 100) + Math.floor(y2 / 400) - 32045;
    }
    // Julian Day Number -> Hijri date (tabular)
    function jdToHijri(jd) {
        var l = Math.floor(jd) - 1948440 + 10632;
        var n = Math.floor((l - 1) / 10631);
        l = l - 10631 * n + 354;
        var j = Math.floor((10985 - l) / 5316) * Math.floor((50 * l) / 17719) +
            Math.floor(l / 5670) * Math.floor((43 * l) / 15238);
        l = l - Math.floor((30 - j) / 15) * Math.floor((17719 * j) / 50) -
            Math.floor(j / 16) * Math.floor((15238 * j) / 43) + 29;
        var m = Math.floor((24 * l) / 709);
        var d = l - Math.floor((709 * m) / 24);
        var y = 30 * n + j - 30;
        return { y: y, m: m, d: d };
    }
    function weekdayOfJd(jd) {
        return WEEKDAYS[((Math.floor(jd + 1.5)) % 7 + 7) % 7];
    }
    function fmtGregorian(g) {
        return g.d + ' ' + GREG_MONTHS[g.m - 1] + ' ' + g.y;
    }
    function fmtHijri(h) {
        return h.d + ' ' + HIJRI_MONTHS[h.m - 1] + ' ' + h.y + ' AH';
    }

    function setMode(m) {
        mode = m;
        hideError();
        results.classList.add('d-none');
        document.getElementById('tabH2G').classList.toggle('active', m === 'h2g');
        document.getElementById('tabG2H').classList.toggle('active', m === 'g2h');
        document.getElementById('h2gPane').classList.toggle('d-none', m !== 'h2g');
        document.getElementById('g2hPane').classList.toggle('d-none', m !== 'g2h');
    }
    document.getElementById('tabH2G').addEventListener('click', function () { setMode('h2g'); });
    document.getElementById('tabG2H').addEventListener('click', function () { setMode('g2h'); });

    (function initDates() {
        var now = new Date();
        var jd = gregorianToJd(now.getFullYear(), now.getMonth() + 1, now.getDate());
        var h = jdToHijri(jd);
        document.getElementById('hDay').value = h.d;
        document.getElementById('hMonth').value = h.m;
        document.getElementById('hYear').value = h.y;
        document.getElementById('gDate').value =
            now.getFullYear() + '-' + String(now.getMonth() + 1).padStart(2, '0') + '-' + String(now.getDate()).padStart(2, '0');
    })();

    goBtn.addEventListener('click', function () {
        hideError();
        var jd, wd;
        if (mode === 'h2g') {
            var d = parseInt(document.getElementById('hDay').value, 10);
            var m = parseInt(document.getElementById('hMonth').value, 10);
            var y = parseInt(document.getElementById('hYear').value, 10);
            if (!d || d < 1 || d > 30) { showError('Please enter a Hijri day between 1 and 30.'); return; }
            if (!y || y < 1 || y > 1600) { showError('Please enter a Hijri year between 1 and 1600.'); return; }
            jd = hijriToJd(y, m, d);
            var g = jdToGregorian(jd);
            wd = weekdayOfJd(jd);
            document.getElementById('resLabel').textContent = fmtHijri({ y: y, m: m, d: d }) + ' =';
            document.getElementById('resDate').textContent = fmtGregorian(g);
            document.getElementById('resAlt').textContent = 'Julian Day: ' + Math.floor(jd);
        } else {
            var gv = document.getElementById('gDate').value;
            if (!gv) { showError('Please select a Gregorian date.'); return; }
            var parts = gv.split('-');
            jd = gregorianToJd(parseInt(parts[0], 10), parseInt(parts[1], 10), parseInt(parts[2], 10));
            var h = jdToHijri(jd);
            wd = weekdayOfJd(jd);
            document.getElementById('resLabel').textContent = fmtGregorian({ y: parseInt(parts[0], 10), m: parseInt(parts[1], 10), d: parseInt(parts[2], 10) }) + ' =';
            document.getElementById('resDate').textContent = fmtHijri(h);
            document.getElementById('resAlt').textContent = 'Julian Day: ' + Math.floor(jd);
        }
        document.getElementById('resWeekday').textContent = wd;
        results.classList.remove('d-none');
    });
})();
</script>
@endsection
