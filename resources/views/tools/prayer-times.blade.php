@extends('layouts.app')

@section('title', 'Prayer Times Pakistan - Prayer Timings Today | Azlaan Tools')
@section('meta_description', 'Free prayer times for Pakistani cities: Fajr, Sunrise, Dhuhr, Asr, Maghrib and Isha with next prayer countdown and Hijri date. No signup needed.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="mb-3">Prayer Times</h1>
            <p class="lead text-muted">Today's prayer timings for major Pakistani cities with a live countdown to the next prayer — free, no signup.</p>
            <div class="card shadow-sm mb-4"><div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-7"><label class="form-label" for="citySelect">Select City</label><select class="form-select" id="citySelect"></select></div>
                    <div class="col-md-5"><button type="button" class="btn btn-outline-primary w-100" id="locBtn">Use My Location</button></div>
                </div>
                <div class="text-center mt-3"><div class="text-muted small" id="hijriOut">Loading...</div><div class="fs-5 fw-bold" id="nextOut"></div><div class="fs-3 fw-bold text-primary" id="countdown"></div></div>
                <div class="alert alert-danger mt-3 d-none" id="errBox">Could not load prayer times. Please check your internet connection and try again.</div>
                <div class="row g-3 mt-2" id="prayerCards"></div>
            </div></div>
            <div class="card shadow-sm"><div class="card-body">
                <h2>How to use</h2>
                <ol><li>Select your city from the dropdown.</li><li>Timings load automatically using the Karachi calculation method.</li><li>The next prayer is highlighted with a live countdown.</li><li>Or click Use My Location for timings at your exact position.</li></ol>
                <p class="small text-muted mb-0">Timings are provided by the Aladhan API and may differ slightly from your local mosque announcement.</p>
            </div></div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    var cities = {
        'Karachi': [24.8608, 67.0011], 'Lahore': [31.5497, 74.3436], 'Islamabad': [33.6844, 73.0479],
        'Faisalabad': [31.4504, 73.1350], 'Rawalpindi': [33.5651, 73.0169], 'Multan': [30.1575, 71.5249],
        'Peshawar': [34.0151, 71.5785], 'Quetta': [30.1798, 66.9750], 'Hyderabad': [25.3960, 68.3578],
        'Sialkot': [32.4945, 74.5229], 'Gujranwala': [32.1877, 74.1945], 'Bahawalpur': [29.3544, 71.6911]
    };
    var names = ['Fajr', 'Sunrise', 'Dhuhr', 'Asr', 'Maghrib', 'Isha'];
    var sel = document.getElementById('citySelect');
    Object.keys(cities).forEach(function (c) { var o = document.createElement('option'); o.value = c; o.textContent = c; sel.appendChild(o); });
    var timings = null, timer = null;
    function render() {
        var wrap = document.getElementById('prayerCards'); wrap.innerHTML = '';
        var now = new Date(); var nextName = null, nextDate = null;
        names.forEach(function (n) {
            var t = timings[n]; var parts = t.split(':'); var d = new Date(now.getFullYear(), now.getMonth(), now.getDate(), parseInt(parts[0], 10), parseInt(parts[1], 10));
            if (!nextName && d > now && n !== 'Sunrise') { nextName = n; nextDate = d; }
            var col = document.createElement('div'); col.className = 'col-6 col-md-4';
            col.innerHTML = '<div class="border rounded p-3 text-center prayer-card" data-name="' + n + '"><div class="text-muted small">' + n + '</div><div class="fs-5 fw-bold">' + t + '</div></div>';
            wrap.appendChild(col);
        });
        if (!nextName) { nextName = 'Fajr'; var fp = timings.Fajr.split(':'); nextDate = new Date(now.getFullYear(), now.getMonth(), now.getDate() + 1, parseInt(fp[0], 10), parseInt(fp[1], 10)); }
        document.getElementById('nextOut').textContent = 'Next Prayer: ' + nextName + ' at ' + timings[nextName];
        document.querySelectorAll('.prayer-card').forEach(function (c) { if (c.getAttribute('data-name') === nextName) { c.classList.add('bg-primary', 'text-white'); c.querySelector('.text-muted').classList.remove('text-muted'); } });
        if (timer) clearInterval(timer);
        timer = setInterval(function () {
            var diff = nextDate - new Date(); if (diff <= 0) { render(); return; }
            var h = Math.floor(diff / 3600000), m = Math.floor(diff % 3600000 / 60000), s = Math.floor(diff % 60000 / 1000);
            document.getElementById('countdown').textContent = 'Countdown: ' + String(h).padStart(2, '0') + ':' + String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
        }, 1000);
    }
    function load(lat, lng) {
        var unix = Math.floor(Date.now() / 1000);
        var url = 'https://api.aladhan.com/v1/timings/' + unix + '?latitude=' + lat + '&longitude=' + lng + '&method=1&school=1';
        fetch(url).then(function (r) { return r.json(); }).then(function (j) {
            timings = {}; names.forEach(function (n) { timings[n] = (j.data.timings[n] || '').split(' ')[0]; });
            var h = j.data.date.hijri;
            document.getElementById('hijriOut').textContent = 'Hijri: ' + h.day + ' ' + h.month.en + ' ' + h.year + ' AH | ' + j.data.date.readable;
            document.getElementById('errBox').classList.add('d-none'); render();
        }).catch(function () { document.getElementById('errBox').classList.remove('d-none'); document.getElementById('hijriOut').textContent = 'Failed to load.'; });
    }
    sel.addEventListener('change', function () { var c = cities[sel.value]; load(c[0], c[1]); });
    document.getElementById('locBtn').addEventListener('click', function () {
        if (!navigator.geolocation) { alert('Geolocation is not supported by this browser.'); return; }
        navigator.geolocation.getCurrentPosition(function (p) { load(p.coords.latitude, p.coords.longitude); }, function () { alert('Location permission denied. Please select a city instead.'); });
    });
    load(cities.Karachi[0], cities.Karachi[1]);
})();
</script>
@endsection
