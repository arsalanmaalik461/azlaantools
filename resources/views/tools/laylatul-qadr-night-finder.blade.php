@extends('layouts.app')

@section('title', 'Laylatul Qadr Night Finder - Azlaan Tools')
@section('meta_description', 'Find the dates of the odd nights (21, 23, 25, 27, 29) of the last ten nights of Ramadan — free.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Laylatul Qadr Night Finder</h1>
            <p class="lead text-muted">The odd nights (21, 23, 25, 27, 29) of the last ten nights of Ramadan — Laylatul Qadr is sought in these nights. Enter the date of 1st Ramadan and find the Gregorian dates of all five nights.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="ramadanDate" class="form-label fw-semibold">Date of 1st Ramadan</label>
                            <input type="date" class="form-control" id="ramadanDate">
                            <div class="form-text">Enter according to the moon sighting in your area.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="countrySelect" class="form-label fw-semibold">Region</label>
                            <select class="form-select" id="countrySelect">
                                <option value="Pakistan">Pakistan</option>
                                <option value="India">India</option>
                                <option value="Bangladesh">Bangladesh</option>
                                <option value="Saudi Arabia">Saudi Arabia</option>
                                <option value="UAE">UAE</option>
                                <option value="UK">UK</option>
                                <option value="USA">USA</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100 mt-3" id="goBtn">Find the Odd Nights</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4"></div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select the date of 1st Ramadan this year (the date the moon was sighted).</li>
                <li>Choose your region and press the button.</li>
                <li>See the dates of all five odd nights and a worship preparation list.</li>
            </ol>
            <div class="alert alert-warning small">
                <strong>Note:</strong> Dates depend on moon sighting — there can be a 1-day difference. Adjust according to your local scholars or the moon-sighting announcement in your area. In the Islamic calendar the night comes first: the night of 21st Ramadan starts on the evening of the 20th at Maghrib.
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var ramadanDate = document.getElementById('ramadanDate');
    var countrySelect = document.getElementById('countrySelect');
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');

    var duas = [
        'Allahumma innaka afuwwun tuhibbul afwa fa\u2019fu anni — "O Allah, You are Forgiving, You love to forgive, so forgive me." (Tirmidhi)',
        'Recite the Quran and reflect on its translation',
        'Nawafil prayers: Tahajjud, Salat-ut-Tasbeeh',
        'Istighfar and dua — for yourself, your parents and the ummah',
        'Charity (sadaqah) — charity given on this night earns better reward than a thousand months'
    ];

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function fmt(d) {
        var days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        var months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        return days[d.getDay()] + ', ' + d.getDate() + ' ' + months[d.getMonth()] + ' ' + d.getFullYear();
    }
    function addDays(d, n) {
        var c = new Date(d.getTime());
        c.setDate(c.getDate() + n);
        return c;
    }

    // Prefill with a reasonable guess? No — user must enter. Set max/min sensibly.
    goBtn.addEventListener('click', function () {
        hideError();
        var val = ramadanDate.value;
        if (!val) { showError('Please select the date of 1st Ramadan first.'); return; }
        var parts = val.split('-');
        var first = new Date(parseInt(parts[0], 10), parseInt(parts[1], 10) - 1, parseInt(parts[2], 10));
        if (isNaN(first.getTime())) { showError('The date is not valid.'); return; }

        var odds = [21, 23, 25, 27, 29];
        var html = '<h5>Odd Nights — Ramadan ' + first.getFullYear() + ' (' + countrySelect.value + ')</h5>';
        html += '<p class="small text-muted">The night starts at Maghrib — worship time is from the evening shown below until the next morning.</p>';
        html += '<div class="list-group mb-4">';
        odds.forEach(function (n, idx) {
            // Night of (n)th Ramadan = evening of (n-1)th Ramadan date
            var nightStart = addDays(first, n - 2); // date of (n-1)th Ramadan
            var nightDate = addDays(first, n - 1);  // the (n)th Ramadan day
            var badge = (n === 27) ? ' <span class="badge bg-primary ms-2">often the 27th night</span>' : '';
            html += '<div class="list-group-item d-flex justify-content-between align-items-center flex-wrap">';
            html += '<div><strong>Night ' + n + '</strong>' + badge + '<br>';
            html += '<span class="small text-muted">' + fmt(nightStart) + ' evening (Maghrib) to ' + fmt(nightDate) + ' morning</span></div>';
            html += '<span class="badge bg-light text-dark border">' + (idx + 1) + ' / 5</span>';
            html += '</div>';
        });
        html += '</div>';

        html += '<h5>What to Do on These Nights</h5><ul class="list-group">';
        duas.forEach(function (d) { html += '<li class="list-group-item small">' + d + '</li>'; });
        html += '</ul>';
        html += '<p class="small text-muted mt-3 mb-0">Hadith: "Seek Laylatul Qadr in the odd nights of the last ten nights of Ramadan." (Bukhari)</p>';

        results.innerHTML = html;
        results.classList.remove('d-none');
    });
})();
</script>
@endsection
