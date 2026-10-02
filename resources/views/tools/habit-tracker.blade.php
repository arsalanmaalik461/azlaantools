@extends('layouts.app')

@section('title', 'Habit Tracker - Azlaan Tools')
@section('meta_description', 'Build good daily habits — daily habit tracker with streaks, free online.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Habit Tracker</h1>
            <p class="lead text-muted">Add your daily habits and tick them each day — watch your streaks grow to stay motivated. Your data is saved only in your browser.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" id="habitInput" placeholder="Type a new habit, e.g. 30 min walk" maxlength="60">
                        <button type="button" class="btn btn-primary" id="goBtn">Add</button>
                    </div>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-2">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="mb-0">Today Habits <small class="text-muted" id="todayLabel"></small></h5>
                            <span class="badge bg-success" id="doneCount">0 done</span>
                        </div>
                        <div id="habitList" class="list-group mb-3"></div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-danger btn-sm" id="clearDoneBtn">Clear old (30+ days) habits</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="exportBtn">Download Backup</button>
                        </div>
                    </div>
                </div>
            </div>

            <div id="statsCard" class="card shadow-sm mb-4 d-none">
                <div class="card-body">
                    <h5>This Week Summary</h5>
                    <div id="weekGrid"></div>
                    <p class="small text-muted mb-0 mt-2">Each row is one habit, each column is one day (today is the rightmost).</p>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Type a habit in the box above and press "Add".</li>
                <li>Tick the checkbox next to a habit when you complete it for the day.</li>
                <li>Watch your streak grow — do not break the chain!</li>
                <li>You can save your data with "Download Backup".</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var habitInput = document.getElementById('habitInput');
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var habitList = document.getElementById('habitList');
    var todayLabel = document.getElementById('todayLabel');
    var doneCount = document.getElementById('doneCount');
    var statsCard = document.getElementById('statsCard');
    var weekGrid = document.getElementById('weekGrid');
    var clearDoneBtn = document.getElementById('clearDoneBtn');
    var exportBtn = document.getElementById('exportBtn');
    var LS_KEY = 'azlaan_habit_tracker_v1';

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function esc(s) {
        return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }
    function dayKey(d) {
        return d.getFullYear() + '-' + ('0' + (d.getMonth() + 1)).slice(-2) + '-' + ('0' + d.getDate()).slice(-2);
    }
    function load() {
        try { return JSON.parse(localStorage.getItem(LS_KEY) || '[]'); }
        catch (e) { return []; }
    }
    function save(habits) {
        try { localStorage.setItem(LS_KEY, JSON.stringify(habits)); }
        catch (e) { showError('Browser storage full — download a backup.'); }
    }
    function streakOf(habit) {
        var s = 0, d = new Date();
        if (!habit.days[dayKey(d)]) d.setDate(d.getDate() - 1); // allow today pending
        while (habit.days[dayKey(d)]) { s++; d.setDate(d.getDate() - 1); }
        return s;
    }
    function totalDone(habit) {
        return Object.keys(habit.days).filter(function (k) { return habit.days[k]; }).length;
    }

    function render() {
        var habits = load();
        var today = dayKey(new Date());
        todayLabel.textContent = '(' + new Date().toLocaleDateString('en-GB', { day: 'numeric', month: 'short' }) + ')';
        if (!habits.length) {
            results.classList.add('d-none');
            statsCard.classList.add('d-none');
            return;
        }
        results.classList.remove('d-none');

        var done = 0;
        habitList.innerHTML = '';
        habits.forEach(function (h, i) {
            var checked = !!h.days[today];
            if (checked) done++;
            var st = streakOf(h);
            var item = document.createElement('div');
            item.className = 'list-group-item d-flex align-items-center gap-2';
            var flame = st >= 7 ? ' &#128293;' : '';
            item.innerHTML =
                '<input type="checkbox" class="form-check-input m-0" data-idx="' + i + '"' + (checked ? ' checked' : '') + ' style="width:1.3em;height:1.3em;">' +
                '<div class="flex-fill"><div class="' + (checked ? 'text-decoration-line-through text-muted' : 'fw-semibold') + '">' + esc(h.name) + '</div>' +
                '<div class="small text-muted">Streak: <strong>' + st + '</strong> days' + flame + ' &middot; Total: ' + totalDone(h) + ' days</div></div>' +
                '<button type="button" class="btn btn-sm btn-outline-danger" data-del="' + i + '" title="Delete">&times;</button>';
            habitList.appendChild(item);
        });
        doneCount.textContent = done + ' / ' + habits.length + ' done';

        habitList.querySelectorAll('input[type=checkbox]').forEach(function (cb) {
            cb.addEventListener('change', function () {
                var hs = load();
                var h = hs[parseInt(cb.getAttribute('data-idx'), 10)];
                if (cb.checked) h.days[today] = true; else delete h.days[today];
                save(hs); render();
            });
        });
        habitList.querySelectorAll('[data-del]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                if (!confirm('Delete this habit?')) return;
                var hs = load();
                hs.splice(parseInt(btn.getAttribute('data-del'), 10), 1);
                save(hs); render();
            });
        });

        // Week grid
        var html = '<div class="table-responsive"><table class="table table-sm table-bordered text-center mb-0"><thead><tr><th class="text-start">Habit</th>';
        var cols = [];
        for (var d = 6; d >= 0; d--) {
            var dt = new Date(); dt.setDate(dt.getDate() - d);
            cols.push(dayKey(dt));
            html += '<th class="small">' + dt.toLocaleDateString('en-GB', { weekday: 'narrow' }) + '<br><span class="text-muted">' + dt.getDate() + '</span></th>';
        }
        html += '</tr></thead><tbody>';
        habits.forEach(function (h) {
            html += '<tr><td class="text-start small">' + esc(h.name.length > 18 ? h.name.slice(0, 18) + '...' : h.name) + '</td>';
            cols.forEach(function (k) {
                html += '<td>' + (h.days[k] ? '<span class="text-success fw-bold">&#10003;</span>' : '<span class="text-muted">&middot;</span>') + '</td>';
            });
            html += '</tr>';
        });
        html += '</tbody></table></div>';
        weekGrid.innerHTML = html;
        statsCard.classList.remove('d-none');
    }

    function addHabit() {
        hideError();
        var name = habitInput.value.trim();
        if (!name) { showError('Type the habit name first.'); return; }
        var habits = load();
        if (habits.length >= 30) { showError('Maximum 30 habits — delete old ones first.'); return; }
        var dupe = habits.some(function (h) { return h.name.toLowerCase() === name.toLowerCase(); });
        if (dupe) { showError('This habit already exists.'); return; }
        habits.push({ name: name, days: {}, created: dayKey(new Date()) });
        save(habits);
        habitInput.value = '';
        render();
    }

    goBtn.addEventListener('click', addHabit);
    habitInput.addEventListener('keydown', function (e) { if (e.key === 'Enter') addHabit(); });

    clearDoneBtn.addEventListener('click', function () {
        var habits = load();
        var cutoff = new Date(); cutoff.setDate(cutoff.getDate() - 30);
        var kept = habits.filter(function (h) {
            var keys = Object.keys(h.days);
            if (!keys.length) return new Date(h.created) > cutoff;
            var last = keys.sort().pop().split('-');
            return new Date(parseInt(last[0], 10), parseInt(last[1], 10) - 1, parseInt(last[2], 10)) > cutoff;
        });
        var removed = habits.length - kept.length;
        save(kept); render();
        hideError();
        if (removed > 0) { errorBox.className = 'alert alert-success mt-3'; errorBox.textContent = removed + ' old habit(s) cleared.'; errorBox.classList.remove('d-none'); setTimeout(hideError, 2500); }
    });

    exportBtn.addEventListener('click', function () {
        var blob = new Blob([JSON.stringify(load(), null, 2)], { type: 'application/json' });
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'habit-tracker-backup.json';
        document.body.appendChild(a);
        a.click();
        setTimeout(function () { URL.revokeObjectURL(a.href); a.remove(); }, 2000);
    });

    render();
})();
</script>
@endsection
