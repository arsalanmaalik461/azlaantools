@extends('layouts.app')

@section('title', 'Best Time to Post Guide - Azlaan Tools')
@section('meta_description', 'Find the best time to post. A posting guide by platform and day, free online.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Best Time to Post Guide</h1>
            <p class="lead text-muted">Choose a platform and day — get a guide to the best posting times. This is a summary of popular marketing studies, not live data.</p>

            <div class="alert alert-info small" role="alert">
                These times are general guidance (based on various published studies). Every audience is different — be sure to check your account's <strong>Insights / Analytics</strong>, where you will see your followers' real active times.
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="platformSelect" class="form-label fw-semibold">Platform</label>
                            <select class="form-select" id="platformSelect">
                                <option value="instagram">Instagram</option>
                                <option value="tiktok">TikTok</option>
                                <option value="facebook">Facebook</option>
                                <option value="youtube">YouTube</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="daySelect" class="form-label fw-semibold">Day</label>
                            <select class="form-select" id="daySelect">
                                <option value="0">Monday</option>
                                <option value="1">Tuesday</option>
                                <option value="2">Wednesday</option>
                                <option value="3">Thursday</option>
                                <option value="4">Friday</option>
                                <option value="5">Saturday</option>
                                <option value="6">Sunday</option>
                            </select>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">See Guide</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <h2 class="h5" id="resultTitle"></h2>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead><tr><th>Time (your local time)</th><th>Rating</th><th>Reason</th></tr></thead>
                                <tbody id="timeRows"></tbody>
                            </table>
                        </div>
                        <h2 class="h6 mt-3">Tips</h2>
                        <ul id="tipsList" class="small"></ul>
                        <div class="card bg-light mt-3">
                            <div class="card-body small">
                                <strong>Official analytics (see real data here):</strong>
                                <ul class="mb-0 mt-2">
                                    <li>Instagram Insights: <a href="https://help.instagram.com" target="_blank" rel="noopener">help.instagram.com</a></li>
                                    <li>TikTok Analytics: <a href="https://support.tiktok.com" target="_blank" rel="noopener">support.tiktok.com</a></li>
                                    <li>Facebook / Meta Business Suite: <a href="https://www.facebook.com/business/help" target="_blank" rel="noopener">facebook.com/business/help</a></li>
                                    <li>YouTube Analytics: <a href="https://support.google.com/youtube" target="_blank" rel="noopener">support.google.com/youtube</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Choose a platform (Instagram, TikTok, Facebook or YouTube).</li>
                <li>Choose a day.</li>
                <li>Press "See Guide" — you will get the best posting windows and tips for that day.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var platformSelect = document.getElementById('platformSelect');
    var daySelect = document.getElementById('daySelect');
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var resultTitle = document.getElementById('resultTitle');
    var timeRows = document.getElementById('timeRows');
    var tipsList = document.getElementById('tipsList');

    var dayNames = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

    var data = {
        instagram: {
            label: 'Instagram',
            windows: [
                { t: 'Morning 9 – 11 AM', r: 3, w: 'People check their feed after reaching work.' },
                { t: 'Midday 12 – 2 PM', r: 3, w: 'Lunch break — the most active time.' },
                { t: 'Evening 7 – 9 PM', r: 2, w: 'Scrolling time after getting back home.' }
            ],
            weekend: [
                { t: 'Morning 10 – 12 AM', r: 3, w: 'Best for audiences that wake up late on weekends.' },
                { t: 'Evening 6 – 8 PM', r: 2, w: 'Evening scrolling.' },
                { t: 'Midday 2 – 4 PM', r: 1, w: 'Decent engagement.' }
            ],
            tips: [
                'Reels get more reach than regular posts — post at least 3 reels per week.',
                'Reply to comments in the first 30 minutes — it signals the algorithm.',
                'Post stories daily to keep your profile on top.'
            ]
        },
        tiktok: {
            label: 'TikTok',
            windows: [
                { t: 'Morning 7 – 9 AM', r: 2, w: 'People who scroll right after waking up.' },
                { t: 'Midday 12 – 3 PM', r: 3, w: 'Peak time for lunch and school/college breaks.' },
                { t: 'Night 8 – 11 PM', r: 3, w: 'The biggest TikTok peak — most views at night.' }
            ],
            weekend: [
                { t: 'Morning 9 – 11 AM', r: 3, w: 'Weekend mornings are very active on TikTok.' },
                { t: 'Night 8 – 12 PM', r: 3, w: 'Watch time is highest on weekend nights.' },
                { t: 'Midday 1 – 4 PM', r: 2, w: 'Good engagement.' }
            ],
            tips: [
                'Hook the viewer in the first 2 seconds — or they will scroll past.',
                'Use trending sounds — your reach can grow many times over.',
                'Stay consistent with 1–3 videos per day.'
            ]
        },
        facebook: {
            label: 'Facebook',
            windows: [
                { t: 'Morning 8 – 10 AM', r: 3, w: 'The time when people check their feed before work.' },
                { t: 'Midday 1 – 3 PM', r: 2, w: 'Lunch break engagement.' },
                { t: 'Evening 7 – 9 PM', r: 2, w: 'Scrolling after family time.' }
            ],
            weekend: [
                { t: 'Morning 9 – 12 AM', r: 3, w: 'Facebook is most active on weekends.' },
                { t: 'Evening 5 – 7 PM', r: 2, w: 'Good reach.' },
                { t: 'Night 8 – 10 PM', r: 2, w: 'Decent engagement.' }
            ],
            tips: [
                'Videos and live streams get more reach than text posts.',
                'Post in Facebook Groups — you get more engagement than from a page.',
                'Posts that ask questions get more comments.'
            ]
        },
        youtube: {
            label: 'YouTube',
            windows: [
                { t: 'Midday 12 – 3 PM', r: 2, w: 'A good time to publish a video — it gets indexed by evening.' },
                { t: 'Evening 5 – 7 PM', r: 3, w: 'The best time to publish — just before peak viewing hours.' },
                { t: 'Night 8 – 10 PM', r: 3, w: 'The highest watch time.' }
            ],
            weekend: [
                { t: 'Morning 10 – 12 AM', r: 3, w: 'Publish in the morning on weekends — views will come all day.' },
                { t: 'Midday 2 – 5 PM', r: 2, w: 'Good watch time.' },
                { t: 'Night 8 – 11 PM', r: 3, w: 'Peak viewing.' }
            ],
            tips: [
                'Publish your video 2–3 hours BEFORE peak hours.',
                'Work hardest on your thumbnail and title — click rate is everything.',
                'Views in the first 24 hours decide the video ranking.'
            ]
        }
    };

    function stars(n) {
        var s = '';
        for (var i = 0; i < 3; i++) { s += (i < n) ? '★' : '☆'; }
        return s;
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

    function render() {
        hideError();
        var p = data[platformSelect.value];
        if (!p) { showError('Select a platform.'); return; }
        var day = parseInt(daySelect.value, 10);
        var isWeekend = (day === 5 || day === 6);
        var wins = isWeekend ? p.weekend : p.windows;
        resultTitle.textContent = p.label + ' — ' + dayNames[day] + (isWeekend ? ' (Weekend)' : ' (Weekday)');
        timeRows.innerHTML = '';
        wins.forEach(function (w) {
            var tr = document.createElement('tr');
            var tdT = document.createElement('td'); tdT.textContent = w.t;
            var tdR = document.createElement('td'); tdR.textContent = stars(w.r); tdR.className = 'text-warning';
            var tdW = document.createElement('td'); tdW.textContent = w.w; tdW.className = 'small';
            tr.appendChild(tdT); tr.appendChild(tdR); tr.appendChild(tdW);
            timeRows.appendChild(tr);
        });
        tipsList.innerHTML = '';
        p.tips.forEach(function (t) {
            var li = document.createElement('li');
            li.textContent = t;
            li.className = 'mb-1';
            tipsList.appendChild(li);
        });
        results.classList.remove('d-none');
    }

    goBtn.addEventListener('click', render);
    platformSelect.addEventListener('change', function () { if (!results.classList.contains('d-none')) { render(); } });
    daySelect.addEventListener('change', function () { if (!results.classList.contains('d-none')) { render(); } });
})();
</script>
@endsection
