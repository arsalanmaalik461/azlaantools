@extends('layouts.app')
@section('title', 'Quran Reading Planner - Khatam Schedule Free | Azlaan Tools')
@section('meta_description', 'Free plan to finish reading the Quran: set a target date, see how many pages to read daily, and track your progress.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-2">Quran Reading Planner</h1>
            <p class="lead text-muted">Plan to finish reading the Quran — set a target date and see how many pages to read daily.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-4">
                            <label for="startDate" class="form-label fw-semibold">Start date</label>
                            <input type="date" class="form-control" id="startDate">
                        </div>
                        <div class="col-sm-4">
                            <label for="targetDate" class="form-label fw-semibold">Target finish date</label>
                            <input type="date" class="form-control" id="targetDate">
                        </div>
                        <div class="col-sm-4">
                            <label for="currentPage" class="form-label fw-semibold">Current page (1–604)</label>
                            <input type="number" class="form-control" id="currentPage" min="1" max="604" value="1">
                            <div class="form-text">Where you want to start reading from.</div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary btn-lg w-100 mt-3" id="planBtn">Create Plan</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="alert alert-success" id="planSummary"></div>
                        <div class="row text-center g-2 mb-3">
                            <div class="col-4"><div class="border rounded p-2"><div class="small text-muted">Pages per day</div><strong id="statPpd" class="fs-4">-</strong></div></div>
                            <div class="col-4"><div class="border rounded p-2"><div class="small text-muted">Daily (approx. juz)</div><strong id="statJpd" class="fs-4">-</strong></div></div>
                            <div class="col-4"><div class="border rounded p-2"><div class="small text-muted">Total days</div><strong id="statDays" class="fs-4">-</strong></div></div>
                        </div>

                        <h3 class="h6">Daily schedule</h3>
                        <p class="small text-muted" id="scheduleNote"></p>
                        <div class="table-responsive" style="max-height: 320px; overflow-y: auto;">
                            <table class="table table-bordered table-sm">
                                <thead class="table-light" style="position: sticky; top: 0;"><tr><th>Date</th><th>Pages</th><th>Approx. Juz</th></tr></thead>
                                <tbody id="scheduleBody"></tbody>
                            </table>
                        </div>

                        <h3 class="h6 mt-4">Track your progress</h3>
                        <div class="card bg-light">
                            <div class="card-body">
                                <div class="progress mb-2" style="height: 22px;">
                                    <div class="progress-bar" id="progressBar" role="progressbar" style="width: 0%;">0%</div>
                                </div>
                                <p class="small mb-2" id="progressText">No progress saved yet.</p>
                                <div class="row g-2 align-items-end">
                                    <div class="col-6">
                                        <label for="pagesToday" class="form-label small fw-semibold mb-1">Pages read today?</label>
                                        <input type="number" class="form-control" id="pagesToday" min="1" max="604" value="5">
                                    </div>
                                    <div class="col-6 d-flex gap-2">
                                        <button type="button" id="addProgressBtn" class="btn btn-success flex-grow-1">Add</button>
                                        <button type="button" id="resetBtn" class="btn btn-outline-danger">Reset</button>
                                    </div>
                                </div>
                                <p class="small text-muted mt-2 mb-0">Your progress is saved in your browser — it stays on this device only.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Choose the start and finish dates, and enter your current page.</li>
                <li>Press <strong>Create Plan</strong> — the schedule will show how many pages to read daily.</li>
                <li>After reading each day, add your pages to track progress.</li>
            </ol>
            <p class="small text-muted">Note: total pages are 604 (Madani mushaf); juz divisions are shown as approximate.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var TOTAL_PAGES = 604;
    var JUZ_COUNT = 30;
    var PAGES_PER_JUZ = TOTAL_PAGES / JUZ_COUNT;
    var LS_KEY = 'quran_plan_v1';

    var startDate = document.getElementById('startDate');
    var targetDate = document.getElementById('targetDate');
    var currentPage = document.getElementById('currentPage');
    var planBtn = document.getElementById('planBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var planSummary = document.getElementById('planSummary');
    var statPpd = document.getElementById('statPpd');
    var statJpd = document.getElementById('statJpd');
    var statDays = document.getElementById('statDays');
    var scheduleNote = document.getElementById('scheduleNote');
    var scheduleBody = document.getElementById('scheduleBody');
    var pagesToday = document.getElementById('pagesToday');
    var addProgressBtn = document.getElementById('addProgressBtn');
    var resetBtn = document.getElementById('resetBtn');
    var progressBar = document.getElementById('progressBar');
    var progressText = document.getElementById('progressText');

    var plan = null; // {startMs, days, startPage, remaining, ppd}

    var MONTHS = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    function fmtDate(ms) {
        var d = new Date(ms);
        return d.getDate() + ' ' + MONTHS[d.getMonth()] + ' ' + d.getFullYear();
    }
    function parseDateInput(v) {
        var p = v.split('-');
        if (p.length !== 3) return null;
        return new Date(parseInt(p[0], 10), parseInt(p[1], 10) - 1, parseInt(p[2], 10)).getTime();
    }
    function todayLocal() {
        var d = new Date();
        return new Date(d.getFullYear(), d.getMonth(), d.getDate()).getTime();
    }
    function toInputVal(ms) {
        var d = new Date(ms);
        var m = d.getMonth() + 1, day = d.getDate();
        return d.getFullYear() + '-' + (m < 10 ? '0' + m : m) + '-' + (day < 10 ? '0' + day : day);
    }
    function juzOf(page) {
        return Math.min(JUZ_COUNT, Math.floor((page - 1) / PAGES_PER_JUZ) + 1);
    }

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() { errorBox.classList.add('d-none'); errorBox.textContent = ''; }

    function loadSaved() {
        try {
            var s = localStorage.getItem(LS_KEY);
            return s ? JSON.parse(s) : null;
        } catch (e) { return null; }
    }
    function saveProgress(pagesDone) {
        try { localStorage.setItem(LS_KEY, JSON.stringify({ pagesDone: pagesDone, at: Date.now() })); }
        catch (e) { /* storage unavailable */ }
    }

    function renderProgress() {
        var saved = loadSaved();
        var done = saved && saved.pagesDone ? saved.pagesDone : 0;
        if (!plan) {
            progressText.textContent = 'No progress saved yet.';
            progressBar.style.width = '0%';
            progressBar.textContent = '0%';
            return;
        }
        done = Math.min(done, plan.remaining);
        var pct = plan.remaining > 0 ? Math.round(done / plan.remaining * 100) : 100;
        progressBar.style.width = pct + '%';
        progressBar.textContent = pct + '%';
        var left = plan.remaining - done;
        if (left <= 0) {
            progressText.textContent = 'Congratulations! You have read all ' + plan.remaining + ' pages — finished.';
        } else {
            progressText.textContent = done + ' / ' + plan.remaining + ' pages read. Left: ' + left +
                ' pages — keep going at ' + Math.ceil(left / Math.max(1, plan.days)) + ' pages per day.';
        }
    }

    planBtn.addEventListener('click', function () {
        hideError();
        var s = parseDateInput(startDate.value);
        var t = parseDateInput(targetDate.value);
        var cur = parseInt(currentPage.value, 10);
        if (s === null) { showError('Choose the start date.'); return; }
        if (t === null) { showError('Choose the target finish date.'); return; }
        if (t < s) { showError('The target date cannot be before the start date.'); return; }
        if (isNaN(cur) || cur < 1 || cur > TOTAL_PAGES) { showError('Enter a current page between 1 and 604.'); return; }

        var days = Math.round((t - s) / 86400000) + 1;
        var remaining = TOTAL_PAGES - cur + 1;
        var ppd = Math.ceil(remaining / days);
        plan = { startMs: s, days: days, startPage: cur, remaining: remaining, ppd: ppd };

        statPpd.textContent = ppd;
        statJpd.textContent = (ppd / PAGES_PER_JUZ).toFixed(2);
        statDays.textContent = days;
        planSummary.innerHTML = '<strong>Plan ready!</strong> From page ' + cur + ' to ' + TOTAL_PAGES +
            ' (' + remaining + ' pages) in ' + days + ' days — read <strong>' + ppd + ' pages daily</strong>.';

        var html = '';
        var page = cur;
        var weekly = days > 90;
        var chunk = weekly ? 7 : 1;
        scheduleNote.textContent = weekly
            ? 'The time period is long, so the schedule is shown by week.'
            : 'The pages for each day are below — follow it like a checklist.';
        for (var d = 0; d < days && page <= TOTAL_PAGES; d += chunk) {
            var from = page;
            var to = Math.min(TOTAL_PAGES, page + ppd * chunk - 1);
            var dStart = s + d * 86400000;
            var dEnd = s + Math.min(d + chunk - 1, days - 1) * 86400000;
            var dateStr = weekly ? fmtDate(dStart) + ' – ' + fmtDate(dEnd) : fmtDate(dStart);
            html += '<tr><td>' + dateStr + '</td><td>Page ' + from + ' – ' + to + '</td><td>' + juzOf(from) + ' – ' + juzOf(to) + '</td></tr>';
            page = to + 1;
        }
        scheduleBody.innerHTML = html;
        results.classList.remove('d-none');
        renderProgress();
        results.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    });

    addProgressBtn.addEventListener('click', function () {
        if (!plan) { showError('Press Create Plan first.'); return; }
        hideError();
        var n = parseInt(pagesToday.value, 10);
        if (isNaN(n) || n < 1) { showError('Enter a valid number of pages.'); return; }
        var saved = loadSaved();
        var done = (saved && saved.pagesDone ? saved.pagesDone : 0) + n;
        saveProgress(done);
        renderProgress();
        pagesToday.value = '5';
    });

    resetBtn.addEventListener('click', function () {
        try { localStorage.removeItem(LS_KEY); } catch (e) { /* ignore */ }
        renderProgress();
    });

    (function init() {
        var now = todayLocal();
        startDate.value = toInputVal(now);
        targetDate.value = toInputVal(now + 30 * 86400000);
    })();
})();
</script>
@endsection
