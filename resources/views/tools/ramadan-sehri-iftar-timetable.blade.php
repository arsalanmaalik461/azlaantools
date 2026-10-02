@extends('layouts.app')

@section('title', 'Ramadan Sehri Iftar Timetable - Azlaan Tools')
@section('meta_description', 'Generate a full 30-day Ramadan Sehri and Iftar timetable for your city in Pakistan. Select city, print or save. Free online tool.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Ramadan Sehri Iftar Timetable</h1>
            <p class="lead text-muted">Make a Sehri and Iftar timetable for the whole Ramadan: select your city, enter the first date of Ramadan, and print it. Before Iftar, also match the times with your mosque announcement.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="citySel" class="form-label fw-semibold">Select your city</label>
                            <select class="form-select" id="citySel"></select>
                        </div>
                        <div class="col-md-6">
                            <label for="startDate" class="form-label fw-semibold">First day of Ramadan</label>
                            <input type="date" class="form-control" id="startDate">
                            <div class="form-text">When the moon is sighted, adjust it with the actual date.</div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Create Timetable</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="mb-0" id="ttTitle"></h5>
                            <button type="button" class="btn btn-success btn-sm" id="printBtn">Print / PDF</button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-sm text-center" id="ttTable">
                                <thead class="table-light">
                                    <tr><th>Fast</th><th>Date</th><th>Sehri Ends</th><th>Iftar</th></tr>
                                </thead>
                                <tbody id="ttBody"></tbody>
                            </table>
                        </div>
                        <div class="alert alert-warning small mb-0">These times are estimates (astronomical calculation): Sehri ends at Fajr time, Iftar at sunset. Be sure to match them with the moon date and your local mosque announcement.</div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select your city.</li>
                <li>Select the first day of Ramadan (the day after the moon is sighted).</li>
                <li>Press <strong>Create Timetable</strong>, then print the 30-day table.</li>
            </ol>
        </div>
    </div>
</div>
<style>
@media print {
    body * { visibility: hidden; }
    #ttTable, #ttTable * { visibility: visible; }
    #ttTable { position: absolute; left: 0; top: 0; width: 100%; }
    #ttTitle { visibility: visible; }
}
</style>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var citySel = document.getElementById('citySel');
    var startDate = document.getElementById('startDate');
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var ttBody = document.getElementById('ttBody');
    var ttTitle = document.getElementById('ttTitle');
    var printBtn = document.getElementById('printBtn');

    var CITIES = [
        { n: 'Karachi', lat: 24.86, lng: 67.01 },
        { n: 'Lahore', lat: 31.55, lng: 74.34 },
        { n: 'Islamabad', lat: 33.68, lng: 73.04 },
        { n: 'Rawalpindi', lat: 33.60, lng: 73.05 },
        { n: 'Faisalabad', lat: 31.42, lng: 73.09 },
        { n: 'Multan', lat: 30.16, lng: 71.52 },
        { n: 'Peshawar', lat: 34.02, lng: 71.54 },
        { n: 'Quetta', lat: 30.18, lng: 66.99 },
        { n: 'Hyderabad', lat: 25.38, lng: 68.37 },
        { n: 'Sialkot', lat: 32.49, lng: 74.53 },
        { n: 'Gujranwala', lat: 32.19, lng: 74.19 },
        { n: 'Sukkur', lat: 27.70, lng: 68.86 },
        { n: 'Bahawalpur', lat: 29.40, lng: 71.68 },
        { n: 'Sargodha', lat: 32.08, lng: 72.67 },
        { n: 'Abbottabad', lat: 34.15, lng: 73.22 },
        { n: 'Mardan', lat: 34.20, lng: 72.04 }
    ];
    CITIES.forEach(function (c, i) {
        var o = document.createElement('option');
        o.value = i;
        o.textContent = c.n;
        citySel.appendChild(o);
    });
    startDate.value = '2026-02-18';

    function d2r(x) { return x * Math.PI / 180; }
    function r2d(x) { return x * 180 / Math.PI; }
    function dayOfYear(d) {
        var s = new Date(d.getFullYear(), 0, 0);
        return Math.floor((d - s) / 864e5);
    }

    function sunTime(lat, lng, date, zenith, isRise) {
        var N = dayOfYear(date);
        var lngHour = lng / 15;
        var t = isRise ? N + ((6 - lngHour) / 24) : N + ((18 - lngHour) / 24);
        var M = (0.9856 * t) - 3.289;
        var L = M + (1.916 * Math.sin(d2r(M))) + (0.020 * Math.sin(d2r(2 * M))) + 282.634;
        L = ((L % 360) + 360) % 360;
        var RA = r2d(Math.atan(0.91764 * Math.tan(d2r(L))));
        RA = ((RA % 360) + 360) % 360;
        RA = RA + (Math.floor(L / 90) * 90) - (Math.floor(RA / 90) * 90);
        RA = RA / 15;
        var sinDec = 0.39782 * Math.sin(d2r(L));
        var cosDec = Math.cos(Math.asin(sinDec));
        var cosH = (Math.cos(d2r(zenith)) - (sinDec * Math.sin(d2r(lat)))) / (cosDec * Math.cos(d2r(lat)));
        if (cosH > 1 || cosH < -1) return null;
        var H = isRise ? 360 - r2d(Math.acos(cosH)) : r2d(Math.acos(cosH));
        H = H / 15;
        var T = H + RA - (0.06571 * t) - 6.622;
        var UT = T - lngHour;
        UT = ((UT % 24) + 24) % 24;
        var tzH = -date.getTimezoneOffset() / 60;
        var localT = UT + tzH;
        var d = new Date(date.getFullYear(), date.getMonth(), date.getDate());
        d.setMinutes(Math.round(localT * 60));
        return d;
    }

    function fmtTime(d) {
        if (!d) return '-';
        var h = d.getHours(), m = d.getMinutes();
        var ap = h >= 12 ? 'PM' : 'AM';
        h = h % 12; if (h === 0) h = 12;
        return h + ':' + (m < 10 ? '0' + m : m) + ' ' + ap;
    }

    function fmtDate(d) {
        var days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        return days[d.getDay()] + ', ' + d.getDate() + ' ' + months[d.getMonth()];
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var city = CITIES[parseInt(citySel.value, 10)];
        var sd = startDate.value;
        if (!sd) { showError('Please select the first day of Ramadan.'); return; }
        var parts = sd.split('-');
        var base = new Date(parseInt(parts[0], 10), parseInt(parts[1], 10) - 1, parseInt(parts[2], 10));
        if (isNaN(base.getTime())) { showError('The date is invalid.'); return; }
        ttTitle.textContent = 'Ramadan Timetable - ' + city.n;
        ttBody.innerHTML = '';
        var i;
        for (i = 0; i < 30; i++) {
            var d = new Date(base.getFullYear(), base.getMonth(), base.getDate() + i);
            var sehri = sunTime(city.lat, city.lng, d, 108, true);
            var iftar = sunTime(city.lat, city.lng, d, 90.833, false);
            var tr = document.createElement('tr');
            var tds = [(i + 1), fmtDate(d), fmtTime(sehri), fmtTime(iftar)];
            tds.forEach(function (v, j) {
                var td = document.createElement('td');
                if (j === 0) { var th = document.createElement('th'); th.textContent = v; tr.appendChild(th); return; }
                td.textContent = v;
                if (j === 2) td.className = 'fw-semibold text-primary';
                if (j === 3) td.className = 'fw-semibold text-success';
                tr.appendChild(td);
            });
            ttBody.appendChild(tr);
        }
        results.classList.remove('d-none');
    });

    printBtn.addEventListener('click', function () { window.print(); });

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
})();
</script>
@endsection
