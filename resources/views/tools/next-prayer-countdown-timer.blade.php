@extends('layouts.app')

@section('title', 'Next Prayer Countdown Timer - Azlaan Tools')
@section('meta_description', 'Free live prayer countdown timer: prayer times for your city in Pakistan with the Islamic Sciences Karachi method and a ticking countdown to the next prayer.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Next Prayer Countdown Timer</h1>
            <p class="lead text-muted">How much time is left until the next prayer — live countdown. For your city, using the University of Islamic Sciences Karachi method (Fajr/Isha 18°, Asr Hanafi).</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="citySel" class="form-label fw-semibold">Select your city</label>
                            <select class="form-select" id="citySel">
                                <option value="24.8607,67.0011">Karachi</option>
                                <option value="31.5497,74.3436" selected>Lahore</option>
                                <option value="33.6844,73.0479">Islamabad</option>
                                <option value="33.6007,73.0679">Rawalpindi</option>
                                <option value="31.4504,73.1350">Faisalabad</option>
                                <option value="30.1575,71.5249">Multan</option>
                                <option value="34.0151,71.5249">Peshawar</option>
                                <option value="30.1798,66.9750">Quetta</option>
                                <option value="25.3969,68.3578">Hyderabad</option>
                                <option value="32.4945,74.5229">Sialkot</option>
                                <option value="custom">Custom location (latitude/longitude)</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="latInput" class="form-label fw-semibold">Latitude</label>
                            <input type="number" class="form-control" id="latInput" step="0.0001" value="31.5497" dir="ltr">
                        </div>
                        <div class="col-md-3">
                            <label for="lngInput" class="form-label fw-semibold">Longitude</label>
                            <input type="number" class="form-control" id="lngInput" step="0.0001" value="74.3436" dir="ltr">
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100 mt-3" id="goBtn">Get Prayer Times</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="text-center mb-1 small text-muted"><span id="cityLabel">Lahore</span> • <span id="hijriLabel"></span></div>
                        <div class="alert alert-success text-center">
                            <div class="small">Next prayer: <strong id="nextName">-</strong></div>
                            <div class="fs-1 fw-bold font-monospace" id="countdown">--:--:--</div>
                            <div class="small text-muted" id="nextAt">-</div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle mb-0">
                                <thead class="table-light"><tr><th>Prayer</th><th>Time</th><th>Status</th></tr></thead>
                                <tbody id="timesBody"></tbody>
                            </table>
                        </div>
                        <div class="small text-muted mt-3">
                            Method: University of Islamic Sciences, Karachi — Fajr 18°, Isha 18°, Asr Hanafi (shadow factor 2). Calculated for the coordinates you entered; times may differ by a few minutes from your mosque schedule.
                        </div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select your city (or enter a custom latitude/longitude).</li>
                <li>Press "Get Prayer Times" — you will get today's prayer times and a live countdown to the next prayer.</li>
                <li>Keep the page open — the countdown updates every second.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var citySel = document.getElementById('citySel');
    var latInput = document.getElementById('latInput');
    var lngInput = document.getElementById('lngInput');
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var cityLabel = document.getElementById('cityLabel');
    var hijriLabel = document.getElementById('hijriLabel');
    var nextName = document.getElementById('nextName');
    var countdown = document.getElementById('countdown');
    var nextAt = document.getElementById('nextAt');
    var timesBody = document.getElementById('timesBody');

    var timerId = null;
    var prayerList = []; // {key,label,date:Date}

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }

    // ---- astronomy helpers (degrees) ----
    function dsin(d) { return Math.sin(d * Math.PI / 180); }
    function dcos(d) { return Math.cos(d * Math.PI / 180); }
    function dtan(d) { return Math.tan(d * Math.PI / 180); }
    function darcsin(x) { return Math.asin(Math.max(-1, Math.min(1, x))) * 180 / Math.PI; }
    function darccos(x) { return Math.acos(Math.max(-1, Math.min(1, x))) * 180 / Math.PI; }
    function darctan2(y, x) { return Math.atan2(y, x) * 180 / Math.PI; }
    function darccot(x) { return Math.atan(1 / x) * 180 / Math.PI; }
    function fixAngle(a) { a = a % 360; return a < 0 ? a + 360 : a; }
    function fixHour(h) { h = h % 24; return h < 0 ? h + 24 : h; }
    function julianDay(y, m, d) {
        if (m <= 2) { y -= 1; m += 12; }
        var A = Math.floor(y / 100);
        var B = 2 - A + Math.floor(A / 4);
        return Math.floor(365.25 * (y + 4716)) + Math.floor(30.6001 * (m + 1)) + d + B - 1524.5;
    }
    function sunPosition(jd) {
        var D = jd - 2451545.0;
        var g = fixAngle(357.529 + 0.98560028 * D);
        var q = fixAngle(280.459 + 0.98564736 * D);
        var L = fixAngle(q + 1.915 * dsin(g) + 0.020 * dsin(2 * g));
        var e = 23.439 - 0.00000036 * D;
        var RA = fixHour(darctan2(dcos(e) * dsin(L), dcos(L)) / 15);
        return { decl: darcsin(dsin(e) * dsin(L)), eqt: q / 15 - RA };
    }
    function hourAngle(angleDeg, lat, decl) {
        var num = dsin(angleDeg) - dsin(lat) * dsin(decl);
        var den = dcos(lat) * dcos(decl);
        return darccos(num / den) / 15; // hours
    }
    // Karachi method: fajr/isha 18 deg, asr Hanafi (shadow factor 2)
    function computeTimes(lat, lng, tz, y, mo, d) {
        var jd = julianDay(y, mo, d) - lng / (15 * 24);
        var sp = sunPosition(jd);
        var noon = fixHour(12 + tz - lng / 15 - sp.eqt);
        function t(angle, after) {
            var h = hourAngle(angle, lat, sp.decl);
            return fixHour(noon + (after ? h : -h));
        }
        var asrAngle = darccot(2 + dtan(Math.abs(lat - sp.decl))); // positive altitude: Hanafi shadow factor 2
        return {
            fajr: t(-18, false),
            sunrise: t(-0.833, false),
            dhuhr: noon,
            asr: t(asrAngle, true),
            maghrib: t(-0.833, true),
            isha: t(-18, true)
        };
    }
    function locNow(tz) {
        var n = new Date();
        return new Date(n.getTime() + n.getTimezoneOffset() * 60000 + tz * 3600000);
    }
    function fmtTime(h) {
        var hh = Math.floor(h), mm = Math.floor((h - hh) * 60);
        var ap = hh >= 12 ? 'PM' : 'AM';
        var h12 = hh % 12; if (h12 === 0) h12 = 12;
        return h12 + ':' + (mm < 10 ? '0' : '') + mm + ' ' + ap;
    }
    function pad(n) { return (n < 10 ? '0' : '') + n; }

    citySel.addEventListener('change', function () {
        if (citySel.value !== 'custom') {
            var p = citySel.value.split(',');
            latInput.value = p[0];
            lngInput.value = p[1];
        }
    });

    function buildPrayerList(lat, lng, tz, baseLoc) {
        var list = [];
        var defs = [
            ['fajr', 'Fajr'], ['sunrise', 'Sunrise'], ['dhuhr', 'Dhuhr'],
            ['asr', 'Asr'], ['maghrib', 'Maghrib'], ['isha', 'Isha']
        ];
        [0, 1].forEach(function (off) {
            var dt = new Date(baseLoc.getTime() + off * 86400000);
            var times = computeTimes(lat, lng, tz, dt.getUTCFullYear(), dt.getUTCMonth() + 1, dt.getUTCDate());
            defs.forEach(function (df) {
                var h = times[df[0]];
                var hh = Math.floor(h), mm = Math.floor((h - hh) * 60);
                list.push({
                    key: df[0], label: df[1],
                    date: new Date(Date.UTC(dt.getUTCFullYear(), dt.getUTCMonth(), dt.getUTCDate(), hh, mm))
                });
            });
        });
        list.sort(function (a, b) { return a.date - b.date; });
        return list;
    }

    function tick() {
        var nowMs = locNow(currentTz).getTime();
        var upcoming = null, current = null;
        for (var i = 0; i < prayerList.length; i++) {
            if (prayerList[i].date.getTime() <= nowMs) current = prayerList[i];
            else { upcoming = prayerList[i]; break; }
        }
        if (!upcoming) return;
        // next prayer (skip sunrise for countdown label but keep table)
        var target = upcoming.key === 'sunrise' ? null : upcoming;
        if (!target) {
            for (var j = 0; j < prayerList.length; j++) {
                if (prayerList[j].date.getTime() > nowMs && prayerList[j].key !== 'sunrise') { target = prayerList[j]; break; }
            }
        }
        if (!target) return;
        var diff = Math.floor((target.date.getTime() - nowMs) / 1000);
        countdown.textContent = pad(Math.floor(diff / 3600)) + ':' + pad(Math.floor(diff % 3600 / 60)) + ':' + pad(diff % 60);
        nextName.textContent = target.label;
        nextAt.textContent = 'Time: ' + fmtTime(target.date.getUTCHours() + target.date.getUTCMinutes() / 60);

        timesBody.innerHTML = '';
        var todayStr = prayerList[0].date.toISOString().slice(0, 10);
        prayerList.forEach(function (p) {
            if (p.date.toISOString().slice(0, 10) !== todayStr) return;
            var tr = document.createElement('tr');
            var td1 = document.createElement('td'); td1.textContent = p.label;
            var td2 = document.createElement('td'); td2.textContent = fmtTime(p.date.getUTCHours() + p.date.getUTCMinutes() / 60);
            var td3 = document.createElement('td');
            if (current && p.key === current.key && p.date.getTime() === current.date.getTime()) {
                td3.innerHTML = '<span class="badge bg-primary">In progress</span>';
                tr.className = 'table-primary';
            } else if (p.date.getTime() <= nowMs) {
                td3.innerHTML = '<span class="badge bg-secondary">Done</span>';
            } else if (target && p.date.getTime() === target.date.getTime()) {
                td3.innerHTML = '<span class="badge bg-success">Next</span>';
                tr.className = 'table-success';
            } else {
                td3.innerHTML = '<span class="badge bg-light text-dark">Upcoming</span>';
            }
            tr.appendChild(td1); tr.appendChild(td2); tr.appendChild(td3);
            timesBody.appendChild(tr);
        });
    }

    var currentTz = 5;

    goBtn.addEventListener('click', function () {
        hideError();
        var lat = parseFloat(latInput.value), lng = parseFloat(lngInput.value);
        if (isNaN(lat) || isNaN(lng) || lat < -90 || lat > 90 || lng < -180 || lng > 180) {
            showError('Please enter a valid latitude (-90 to 90) and longitude (-180 to 180).');
            return;
        }
        currentTz = citySel.value === 'custom' ? Math.round(lng / 15) : 5;
        var now = locNow(currentTz);
        prayerList = buildPrayerList(lat, lng, currentTz, now);
        var cname = citySel.value === 'custom' ? (lat.toFixed(2) + ', ' + lng.toFixed(2)) : citySel.options[citySel.selectedIndex].text;
        cityLabel.textContent = cname;
        try {
            hijriLabel.textContent = new Intl.DateTimeFormat('en-PK-u-ca-islamic-umalqura', { day: 'numeric', month: 'long', year: 'numeric' }).format(now);
        } catch (e) { hijriLabel.textContent = ''; }
        results.classList.remove('d-none');
        if (timerId) clearInterval(timerId);
        tick();
        timerId = setInterval(tick, 1000);
    });
})();
</script>
@endsection
