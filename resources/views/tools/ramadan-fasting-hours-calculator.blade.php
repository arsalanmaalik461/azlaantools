@extends('layouts.app')

@section('title', 'Ramadan Fasting Hours Calculator - Azlaan Tools')
@section('meta_description', 'Calculate Ramadan fasting duration for your city with sehri and iftar times, free online.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Ramadan Fasting Hours Calculator</h1>
            <p class="lead text-muted">Find out how long your fast will be in your city. Select your city and date — get the Sehri (Fajr) and Iftar (Maghrib) times and the total fasting duration.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="citySelect" class="form-label fw-semibold">Select city</label>
                            <select class="form-select" id="citySelect">
                                <option value="24.8607,67.0011">Karachi</option>
                                <option value="31.5497,74.3436">Lahore</option>
                                <option value="33.6844,73.0479" selected>Islamabad</option>
                                <option value="33.5651,73.0169">Rawalpindi</option>
                                <option value="31.4187,73.0791">Faisalabad</option>
                                <option value="30.1575,71.5249">Multan</option>
                                <option value="34.0151,71.5249">Peshawar</option>
                                <option value="30.1798,66.9750">Quetta</option>
                                <option value="25.3792,68.3683">Hyderabad</option>
                                <option value="32.4945,74.5229">Sialkot</option>
                                <option value="32.1617,74.1883">Gujranwala</option>
                                <option value="27.7052,68.8574">Sukkur</option>
                                <option value="29.3956,71.6722">Bahawalpur</option>
                                <option value="32.0836,72.6711">Sargodha</option>
                                <option value="34.1688,73.2215">Abbottabad</option>
                                <option value="custom">Custom location (latitude/longitude)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="dateInput" class="form-label fw-semibold">Date</label>
                            <input type="date" class="form-control" id="dateInput">
                        </div>
                    </div>
                    <div class="row g-3 mt-1 d-none" id="customRow">
                        <div class="col-md-4">
                            <label for="latInput" class="form-label fw-semibold">Latitude</label>
                            <input type="number" class="form-control" id="latInput" placeholder="e.g. 33.68" step="0.0001" min="-90" max="90">
                        </div>
                        <div class="col-md-4">
                            <label for="lngInput" class="form-label fw-semibold">Longitude</label>
                            <input type="number" class="form-control" id="lngInput" placeholder="e.g. 73.05" step="0.0001" min="-180" max="180">
                        </div>
                        <div class="col-md-4">
                            <label for="tzInput" class="form-label fw-semibold">Timezone (UTC offset)</label>
                            <input type="number" class="form-control" id="tzInput" value="5" step="0.5" min="-12" max="14">
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100 mt-3" id="goBtn">Calculate Fasting Hours</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="alert alert-success text-center">
                            <div class="small">Total fasting time</div>
                            <div class="display-6 fw-bold" id="resDuration"></div>
                            <div class="small" id="resDate"></div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr><th class="w-50">Sehri ends (Fajr)</th><td id="resFajr"></td></tr>
                                    <tr><th>Sunrise</th><td id="resSunrise"></td></tr>
                                    <tr><th>Iftar (Maghrib / Sunset)</th><td id="resMaghrib"></td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="alert alert-warning">
                <strong>Note:</strong> These times are an estimate from astronomical calculation. Confirm the actual Sehri/Iftar time with your mosque or your city Ramadan timetable — it may differ by a minute or two.
            </div>

            <h2>How to use</h2>
            <ol>
                <li><strong>Select your city</strong> — the list has the major cities of Pakistan, or enter latitude/longitude with Custom location.</li>
                <li><strong>Select the date</strong> — you can check the duration of any day in Ramadan.</li>
                <li><strong>Press the button</strong> — you will see the Sehri time (Fajr), sunrise, Iftar time (Maghrib) and the total fasting time in hours and minutes.</li>
            </ol>

            <h2 class="mt-4">Why do fasting hours change?</h2>
            <p class="text-muted">In summer the days are long, so a fast can be 14–15 hours, while in winter it is 10–11 hours. The further north a city is, the longer the fast is in summer — that is why Peshawar has a slightly longer fast than Karachi.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var citySelect = document.getElementById('citySelect');
    var dateInput = document.getElementById('dateInput');
    var customRow = document.getElementById('customRow');
    var latInput = document.getElementById('latInput');
    var lngInput = document.getElementById('lngInput');
    var tzInput = document.getElementById('tzInput');
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var resDuration = document.getElementById('resDuration');
    var resDate = document.getElementById('resDate');
    var resFajr = document.getElementById('resFajr');
    var resSunrise = document.getElementById('resSunrise');
    var resMaghrib = document.getElementById('resMaghrib');

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function deg2rad(d) { return d * Math.PI / 180; }
    function rad2deg(r) { return r * 180 / Math.PI; }
    function norm360(a) { a = a % 360; return a < 0 ? a + 360 : a; }
    function norm24(h) { h = h % 24; return h < 0 ? h + 24 : h; }

    function dayOfYear(y, m, d) {
        var dt = new Date(Date.UTC(y, m - 1, d));
        var start = new Date(Date.UTC(y, 0, 0));
        return Math.floor((dt - start) / 86400000);
    }

    function sunTime(y, m, d, lat, lng, zenith, isRise, tz) {
        var N = dayOfYear(y, m, d);
        var lngHour = lng / 15;
        var t = N + ((isRise ? 6 : 18) - lngHour) / 24;
        var M = 0.9856 * t - 3.289;
        var L = norm360(M + 1.916 * Math.sin(deg2rad(M)) + 0.020 * Math.sin(deg2rad(2 * M)) + 282.634);
        var RA = norm360(rad2deg(Math.atan(0.91764 * Math.tan(deg2rad(L)))));
        RA = RA / 15;
        var Lq = Math.floor(L / 90) * 90;
        var RAq = Math.floor(RA * 15 / 90) * 90;
        RA = RA + (Lq - RAq) / 15;
        var sinDec = 0.39782 * Math.sin(deg2rad(L));
        var cosDec = Math.cos(Math.asin(sinDec));
        var cosH = (Math.cos(deg2rad(zenith)) - sinDec * Math.sin(deg2rad(lat))) / (cosDec * Math.cos(deg2rad(lat)));
        if (cosH > 1 || cosH < -1) return null;
        var H = isRise ? 360 - rad2deg(Math.acos(cosH)) : rad2deg(Math.acos(cosH));
        H = H / 15;
        var T = H + RA - 0.06571 * t - 6.622;
        var UT = norm24(T - lngHour);
        return norm24(UT + tz);
    }

    function fmtTime(h) {
        var hh = Math.floor(h);
        var mm = Math.floor((h - hh) * 60);
        var ap = hh >= 12 ? 'PM' : 'AM';
        var h12 = hh % 12;
        if (h12 === 0) h12 = 12;
        var mms = mm < 10 ? '0' + mm : '' + mm;
        return h12 + ':' + mms + ' ' + ap;
    }

    citySelect.addEventListener('change', function () {
        customRow.classList.toggle('d-none', citySelect.value !== 'custom');
    });

    (function initDate() {
        var now = new Date();
        var s = now.getFullYear() + '-' +
            (now.getMonth() + 1 < 10 ? '0' : '') + (now.getMonth() + 1) + '-' +
            (now.getDate() < 10 ? '0' : '') + now.getDate();
        dateInput.value = s;
    })();

    goBtn.addEventListener('click', function () {
        hideError();
        var lat, lng, tz, cityName;
        if (citySelect.value === 'custom') {
            lat = parseFloat(latInput.value);
            lng = parseFloat(lngInput.value);
            tz = parseFloat(tzInput.value);
            if (!(lat >= -90 && lat <= 90)) { showError('Please enter latitude between -90 and 90.'); return; }
            if (!(lng >= -180 && lng <= 180)) { showError('Please enter longitude between -180 and 180.'); return; }
            if (!(tz >= -12 && tz <= 14)) { showError('Please enter timezone offset between -12 and 14.'); return; }
            cityName = 'Custom location';
        } else {
            var parts = citySelect.value.split(',');
            lat = parseFloat(parts[0]); lng = parseFloat(parts[1]); tz = 5;
            cityName = citySelect.options[citySelect.selectedIndex].text;
        }
        var dv = dateInput.value;
        if (!dv) { showError('Please choose a date.'); return; }
        var dp = dv.split('-');
        var y = parseInt(dp[0], 10), mo = parseInt(dp[1], 10), da = parseInt(dp[2], 10);

        var fajr = sunTime(y, mo, da, lat, lng, 108, true, tz);
        var sunrise = sunTime(y, mo, da, lat, lng, 90.833, true, tz);
        var maghrib = sunTime(y, mo, da, lat, lng, 90.833, false, tz);
        if (fajr === null || maghrib === null) {
            showError('Could not calculate the times for this location and date (polar day/night).');
            return;
        }
        var durH = maghrib - fajr;
        if (durH < 0) durH += 24;
        var h = Math.floor(durH);
        var min = Math.round((durH - h) * 60);
        if (min === 60) { h++; min = 0; }

        resDuration.textContent = h + ' hours ' + min + ' minutes';
        var dObj = new Date(y, mo - 1, da);
        resDate.textContent = cityName + ' — ' + dObj.toLocaleDateString('en-GB', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
        resFajr.textContent = fmtTime(fajr);
        resSunrise.textContent = sunrise === null ? '—' : fmtTime(sunrise);
        resMaghrib.textContent = fmtTime(maghrib);
        results.classList.remove('d-none');
    });
})();
</script>
@endsection
