@extends('layouts.app')

@section('title', 'Study Hours Tracker - Azlaan Tools')
@section('meta_description', 'Keep a record of how much you study each day. Study hours tracker with weekly chart, free online.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Study Hours Tracker</h1>
            <p class="lead text-muted">How much you studied each day — keep a record and see your progress in the weekly chart. The data is saved only in your browser.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-md-5">
                            <label for="subjectInput" class="form-label fw-semibold">Subject</label>
                            <input type="text" class="form-control" id="subjectInput" placeholder="e.g. Maths" autocomplete="off">
                        </div>
                        <div class="col-md-3">
                            <label for="dateInput" class="form-label fw-semibold">Date</label>
                            <input type="date" class="form-control" id="dateInput">
                        </div>
                        <div class="col-6 col-md-2">
                            <label for="hoursInput" class="form-label fw-semibold">Hours</label>
                            <input type="number" class="form-control" id="hoursInput" min="0" max="24" value="1">
                        </div>
                        <div class="col-6 col-md-2">
                            <label for="minutesInput" class="form-label fw-semibold">Minutes</label>
                            <input type="number" class="form-control" id="minutesInput" min="0" max="59" value="0">
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100 mt-3" id="goBtn">Add Entry</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="mt-4">
                        <div class="row text-center g-2 mb-3">
                            <div class="col-6 col-md-3"><div class="card bg-light"><div class="card-body py-2"><div class="small text-muted">Today</div><div class="fw-bold" id="statToday">0h 0m</div></div></div></div>
                            <div class="col-6 col-md-3"><div class="card bg-light"><div class="card-body py-2"><div class="small text-muted">Last 7 days</div><div class="fw-bold" id="statWeek">0h 0m</div></div></div></div>
                            <div class="col-6 col-md-3"><div class="card bg-light"><div class="card-body py-2"><div class="small text-muted">Daily average (7 days)</div><div class="fw-bold" id="statAvg">0h 0m</div></div></div></div>
                            <div class="col-6 col-md-3"><div class="card bg-light"><div class="card-body py-2"><div class="small text-muted">Total time</div><div class="fw-bold" id="statTotal">0h 0m</div></div></div></div>
                        </div>
                        <h2 class="h6">Last 7 days chart</h2>
                        <canvas id="weekChart" width="640" height="260" style="width:100%; height:auto;" class="border rounded bg-white"></canvas>
                        <h2 class="h6 mt-4">Entries</h2>
                        <div class="table-responsive">
                            <table class="table table-striped table-sm">
                                <thead><tr><th>Date</th><th>Subject</th><th class="text-end">Time</th><th></th></tr></thead>
                                <tbody id="entryRows"></tbody>
                            </table>
                        </div>
                        <p class="text-muted small" id="emptyMsg">No entries yet — add your first entry above.</p>
                        <button type="button" class="btn btn-sm btn-outline-danger" id="clearBtn">Delete All Entries</button>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Write the subject, date and how long you studied — then press "Add Entry".</li>
                <li>The stats and the 7-day chart above will update by themselves.</li>
                <li>To delete an entry, press the button in front of it.</li>
            </ol>
            <p class="text-muted small">The data stays in your browser's local storage — it is safe on this device and is never uploaded anywhere.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var subjectInput = document.getElementById('subjectInput');
    var dateInput = document.getElementById('dateInput');
    var hoursInput = document.getElementById('hoursInput');
    var minutesInput = document.getElementById('minutesInput');
    var goBtn = document.getElementById('goBtn');
    var clearBtn = document.getElementById('clearBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var statToday = document.getElementById('statToday');
    var statWeek = document.getElementById('statWeek');
    var statAvg = document.getElementById('statAvg');
    var statTotal = document.getElementById('statTotal');
    var entryRows = document.getElementById('entryRows');
    var emptyMsg = document.getElementById('emptyMsg');
    var weekChart = document.getElementById('weekChart');
    var chartCtx = weekChart.getContext('2d');
    var KEY = 'studyHoursTrackerV1';

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function load() {
        try {
            var raw = localStorage.getItem(KEY);
            return raw ? JSON.parse(raw) : [];
        } catch (e) { return []; }
    }
    function save(entries) {
        try { localStorage.setItem(KEY, JSON.stringify(entries)); } catch (e) { /* storage full */ }
    }
    function fmt(mins) {
        var h = Math.floor(mins / 60), m = mins % 60;
        return h + 'h ' + m + 'm';
    }
    function todayStr() {
        var d = new Date();
        return d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate());
    }
    function pad(n) { return (n < 10 ? '0' : '') + n; }
    function parseDate(s) {
        var p = s.split('-');
        return new Date(parseInt(p[0], 10), parseInt(p[1], 10) - 1, parseInt(p[2], 10));
    }
    function dateStr(d) {
        return d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate());
    }

    function render() {
        var entries = load();
        entries.sort(function (a, b) { return b.date < a.date ? -1 : (b.date > a.date ? 1 : b.id - a.id); });
        var t = todayStr();
        var todayM = 0, weekM = 0, totalM = 0;
        var dayMap = {};
        for (var i = 6; i >= 0; i--) {
            var d = new Date(); d.setDate(d.getDate() - i);
            dayMap[dateStr(d)] = 0;
        }
        entries.forEach(function (e) {
            totalM += e.mins;
            if (e.date === t) { todayM += e.mins; }
            if (dayMap.hasOwnProperty(e.date)) { dayMap[e.date] += e.mins; weekM += e.mins; }
        });
        statToday.textContent = fmt(todayM);
        statWeek.textContent = fmt(weekM);
        statAvg.textContent = fmt(Math.round(weekM / 7));
        statTotal.textContent = fmt(totalM);

        entryRows.innerHTML = '';
        entries.forEach(function (e) {
            var tr = document.createElement('tr');
            var tdD = document.createElement('td'); tdD.textContent = e.date;
            var tdS = document.createElement('td'); tdS.textContent = e.subject;
            var tdT = document.createElement('td'); tdT.className = 'text-end'; tdT.textContent = fmt(e.mins);
            var tdX = document.createElement('td'); tdX.className = 'text-end';
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'btn btn-sm btn-outline-danger';
            btn.textContent = 'Delete';
            btn.setAttribute('data-id', e.id);
            btn.addEventListener('click', function () {
                var all = load().filter(function (x) { return String(x.id) !== btn.getAttribute('data-id'); });
                save(all);
                render();
            });
            tdX.appendChild(btn);
            tr.appendChild(tdD); tr.appendChild(tdS); tr.appendChild(tdT); tr.appendChild(tdX);
            entryRows.appendChild(tr);
        });
        emptyMsg.style.display = entries.length ? 'none' : 'block';
        drawChart(dayMap);
    }

    function drawChart(dayMap) {
        var W = 640, H = 260, padL = 44, padB = 30, padT = 12;
        chartCtx.clearRect(0, 0, W, H);
        var keys = Object.keys(dayMap);
        var vals = keys.map(function (k) { return dayMap[k]; });
        var max = Math.max.apply(null, vals.concat([60]));
        var names = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        var bw = (W - padL - 16) / 7;
        chartCtx.strokeStyle = '#dee2e6';
        chartCtx.fillStyle = '#6c757d';
        chartCtx.font = '11px sans-serif';
        chartCtx.textAlign = 'right';
        for (var g = 0; g <= 4; g++) {
            var v = Math.round(max * g / 4);
            var y = padT + (H - padT - padB) * (1 - g / 4);
            chartCtx.beginPath(); chartCtx.moveTo(padL, y); chartCtx.lineTo(W - 8, y); chartCtx.stroke();
            chartCtx.fillText(fmt(v), padL - 6, y + 4);
        }
        chartCtx.textAlign = 'center';
        for (var i = 0; i < 7; i++) {
            var mins = vals[i];
            var bh = (H - padT - padB) * (mins / max);
            var x = padL + i * bw + 8;
            var y0 = H - padB - bh;
            chartCtx.fillStyle = keys[i] === todayStr() ? '#0d6efd' : '#7db3ff';
            chartCtx.fillRect(x, y0, bw - 16, bh);
            var d = parseDate(keys[i]);
            chartCtx.fillStyle = '#495057';
            chartCtx.fillText(names[d.getDay()], x + (bw - 16) / 2, H - 12);
            if (mins > 0) {
                chartCtx.fillStyle = '#212529';
                chartCtx.fillText(fmt(mins), x + (bw - 16) / 2, y0 - 6);
            }
        }
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var subject = subjectInput.value.trim();
        var date = dateInput.value;
        var h = parseInt(hoursInput.value, 10) || 0;
        var m = parseInt(minutesInput.value, 10) || 0;
        if (!subject) { showError('Please enter the subject.'); return; }
        if (!date) { showError('Please pick a date.'); return; }
        if (h < 0 || m < 0 || m > 59) { showError('Please enter a valid time (minutes 0-59).'); return; }
        var mins = h * 60 + m;
        if (mins <= 0) { showError('The time must be more than 0.'); return; }
        if (mins > 1440) { showError('One day cannot have more than 24 hours.'); return; }
        var entries = load();
        entries.push({ id: Date.now(), subject: subject, date: date, mins: mins });
        save(entries);
        subjectInput.value = '';
        hoursInput.value = '1';
        minutesInput.value = '0';
        render();
    });

    clearBtn.addEventListener('click', function () {
        if (confirm('Do you really want to delete all entries?')) {
            save([]);
            render();
        }
    });

    dateInput.value = todayStr();
    render();
})();
</script>
@endsection
