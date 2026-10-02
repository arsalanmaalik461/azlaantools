@extends('layouts.app')
@section('title', 'Follower Milestone Tracker — Azlaan Tools')
@section('meta_description', 'Track your follower growth and see progress toward 1K, 10K, 100K milestones. Free online follower milestone tracker with growth estimates — no signup.')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Follower Milestone Tracker</h1>
            <p class="lead text-muted">Track your followers — see your progress toward 1K, 10K, 100K milestones and estimate when the next milestone will come.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="platform" class="form-label fw-semibold">Platform</label>
                            <select class="form-select" id="platform">
                                <option value="Instagram">Instagram</option>
                                <option value="TikTok">TikTok</option>
                                <option value="YouTube">YouTube</option>
                                <option value="Facebook">Facebook</option>
                                <option value="X">X (Twitter)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="handle" class="form-label fw-semibold">Handle / name (optional)</label>
                            <input type="text" class="form-control" id="handle" placeholder="e.g. @azlaansolar">
                        </div>
                        <div class="col-12">
                            <label for="followers" class="form-label fw-semibold">Current followers</label>
                            <input type="number" class="form-control" id="followers" placeholder="e.g. 1250" min="0" step="1">
                            <div class="form-text">Check your follower count on the platform and write it here.</div>
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-3">
                        <button type="button" class="btn btn-primary flex-fill" id="goBtn">Show Progress</button>
                        <button type="button" class="btn btn-outline-success" id="logBtn" title="Save todays count">Save today</button>
                    </div>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <h2 class="h5" id="rTitle">Milestones</h2>
                        <div id="milestones"></div>
                        <div class="alert alert-info mt-3" id="etaBox"></div>
                        <h3 class="h6 mt-4">Saved history</h3>
                        <p class="text-muted small">Press "Save today" once a day or once a week — this gives a daily growth estimate. The data stays only in your browser.</p>
                        <table class="table table-sm table-bordered" id="histTable">
                            <thead><tr><th>Date</th><th>Followers</th><th>Change</th><th></th></tr></thead>
                            <tbody id="histBody"></tbody>
                        </table>
                        <button type="button" class="btn btn-outline-danger btn-sm" id="clearBtn">Clear history</button>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Choose your platform and write your current follower count.</li>
                <li>Press <strong>Show Progress</strong> — you will see a progress bar for each milestone.</li>
                <li>Press <strong>Save today</strong> daily to track growth.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var MILESTONES = [1000, 10000, 50000, 100000, 500000, 1000000, 10000000];
    var KEY = 'follower_milestones_v1';

    var goBtn = document.getElementById('goBtn');
    var logBtn = document.getElementById('logBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var milestonesDiv = document.getElementById('milestones');
    var etaBox = document.getElementById('etaBox');
    var histBody = document.getElementById('histBody');

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function fmt(n) {
        if (n >= 1000000) { return (n / 1000000).toFixed(n % 1000000 === 0 ? 0 : 1) + 'M'; }
        if (n >= 1000) { return (n / 1000).toFixed(n % 1000 === 0 ? 0 : 1) + 'K'; }
        return String(n);
    }
    function fmtFull(n) { return n.toLocaleString('en-US'); }
    function todayStr() {
        var d = new Date();
        return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
    }

    function loadHist() {
        try { var r = localStorage.getItem(KEY); return r ? JSON.parse(r) : []; }
        catch (e) { return []; }
    }
    function saveHist(h) {
        try { localStorage.setItem(KEY, JSON.stringify(h)); } catch (e) { /* ignore */ }
    }

    function avgDaily() {
        var h = loadHist();
        if (h.length < 2) { return null; }
        var first = h[0], last = h[h.length - 1];
        var d1 = new Date(first.date + 'T00:00:00'), d2 = new Date(last.date + 'T00:00:00');
        var days = Math.round((d2 - d1) / 86400000);
        if (days <= 0) { return null; }
        return (last.count - first.count) / days;
    }

    function renderHistory() {
        var h = loadHist();
        histBody.innerHTML = '';
        for (var i = h.length - 1; i >= 0; i--) {
            (function (idx) {
                var tr = document.createElement('tr');
                var tdD = document.createElement('td'); tdD.textContent = h[idx].date;
                var tdC = document.createElement('td'); tdC.textContent = fmtFull(h[idx].count);
                var tdCh = document.createElement('td');
                if (idx > 0) {
                    var diff = h[idx].count - h[idx - 1].count;
                    tdCh.textContent = (diff >= 0 ? '+' : '') + fmtFull(diff);
                    tdCh.className = diff >= 0 ? 'text-success' : 'text-danger';
                } else { tdCh.textContent = '—'; }
                var tdX = document.createElement('td');
                var b = document.createElement('button');
                b.type = 'button'; b.className = 'btn btn-sm btn-outline-danger'; b.textContent = '×';
                b.addEventListener('click', function () {
                    var hh = loadHist(); hh.splice(idx, 1); saveHist(hh); renderHistory();
                });
                tdX.appendChild(b);
                tr.appendChild(tdD); tr.appendChild(tdC); tr.appendChild(tdCh); tr.appendChild(tdX);
                histBody.appendChild(tr);
            })(i);
        }
        if (!h.length) {
            var tr0 = document.createElement('tr');
            var td0 = document.createElement('td'); td0.colSpan = 4;
            td0.className = 'text-muted text-center'; td0.textContent = 'No history saved yet.';
            tr0.appendChild(td0); histBody.appendChild(tr0);
        }
    }

    function render() {
        hideError();
        var count = parseInt(document.getElementById('followers').value, 10);
        if (isNaN(count) || count < 0) { showError('Enter a valid follower count (0 or more).'); return; }
        var platform = document.getElementById('platform').value;
        var handle = document.getElementById('handle').value.trim();
        document.getElementById('rTitle').textContent = 'Milestones' + (handle ? ' — ' + handle : '') + ' (' + platform + ')';

        milestonesDiv.innerHTML = '';
        var nextMilestone = null;
        for (var i = 0; i < MILESTONES.length; i++) {
            var m = MILESTONES[i];
            var done = count >= m;
            if (!done && nextMilestone === null) { nextMilestone = m; }
            var prev = i === 0 ? 0 : MILESTONES[i - 1];
            var pct = done ? 100 : Math.max(0, Math.min(100, (count - prev) / (m - prev) * 100));
            var wrap = document.createElement('div');
            wrap.className = 'mb-3';
            var head = document.createElement('div');
            head.className = 'd-flex justify-content-between small mb-1';
            var left = document.createElement('span');
            left.innerHTML = '<strong>' + fmt(m) + '</strong> followers' + (done ? ' <span class="badge bg-success">Done! Completed</span>' : '');
            var right = document.createElement('span');
            right.className = 'text-muted';
            right.textContent = done ? fmtFull(count) + ' / ' + fmtFull(m) : fmtFull(count) + ' / ' + fmtFull(m) + ' — ' + fmtFull(m - count) + ' left';
            head.appendChild(left); head.appendChild(right);
            var bar = document.createElement('div');
            bar.className = 'progress';
            bar.style.height = '18px';
            var fill = document.createElement('div');
            fill.className = 'progress-bar' + (done ? ' bg-success' : '');
            fill.style.width = pct.toFixed(1) + '%';
            fill.textContent = pct.toFixed(0) + '%';
            bar.appendChild(fill);
            wrap.appendChild(head); wrap.appendChild(bar);
            milestonesDiv.appendChild(wrap);
        }

        var g = avgDaily();
        if (nextMilestone === null) {
            etaBox.textContent = 'Great! You have crossed all milestones — ' + fmtFull(count) + ' followers. Set your next big goal yourself.';
        } else if (g !== null && g > 0) {
            var days = Math.ceil((nextMilestone - count) / g);
            var eta = new Date(); eta.setDate(eta.getDate() + days);
            etaBox.innerHTML = 'At the current speed of <strong>' + g.toFixed(1) + ' followers/day</strong>, the next milestone of <strong>' +
                fmt(nextMilestone) + '</strong> will be reached in about <strong>' + days + ' days</strong> (' + eta.toLocaleDateString('en-GB') + '). <span class="text-muted">This is only an estimate — growth is never a straight line.</span>';
        } else {
            etaBox.innerHTML = 'Next milestone: <strong>' + fmt(nextMilestone) + '</strong> — ' + fmtFull(nextMilestone - count) + ' followers left. ' +
                'To estimate the time, at least 2 days of saved data is needed (save it in the history below).';
        }
        renderHistory();
        results.classList.remove('d-none');
    }

    goBtn.addEventListener('click', render);

    logBtn.addEventListener('click', function () {
        hideError();
        var count = parseInt(document.getElementById('followers').value, 10);
        if (isNaN(count) || count < 0) { showError('Enter your follower count first, then save.'); return; }
        var h = loadHist();
        var t = todayStr();
        var found = false;
        for (var i = 0; i < h.length; i++) {
            if (h[i].date === t) { h[i].count = count; found = true; break; }
        }
        if (!found) { h.push({ date: t, count: count }); }
        saveHist(h);
        render();
    });

    document.getElementById('clearBtn').addEventListener('click', function () {
        saveHist([]);
        renderHistory();
    });

    renderHistory();
})();
</script>
@endsection
