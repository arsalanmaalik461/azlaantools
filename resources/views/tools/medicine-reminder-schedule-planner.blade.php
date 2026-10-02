@extends('layouts.app')

@section('title', 'Medicine Reminder Schedule Planner - Azlaan Tools')
@section('meta_description', 'Plan a daily medicine timetable with timings, free online schedule planner.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Medicine Reminder Schedule Planner</h1>
            <p class="lead text-muted">Make a daily schedule for your medicine timings and print it to stick on your fridge.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label for="medName" class="form-label fw-semibold">Medicine name</label>
                            <input type="text" class="form-control" id="medName" placeholder="e.g. Panadol">
                        </div>
                        <div class="col-6 mb-3">
                            <label for="doseNote" class="form-label fw-semibold">Dose (as doctor prescribed)</label>
                            <input type="text" class="form-control" id="doseNote" placeholder="e.g. 1 tablet">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Timings (at what time to take it)</label>
                        <div class="d-flex flex-wrap gap-2" id="timeChips">
                            <button type="button" class="btn btn-sm btn-outline-primary time-chip" data-t="08:00">Morning 8:00</button>
                            <button type="button" class="btn btn-sm btn-outline-primary time-chip" data-t="14:00">Afternoon 2:00</button>
                            <button type="button" class="btn btn-sm btn-outline-primary time-chip" data-t="18:00">Evening 6:00</button>
                            <button type="button" class="btn btn-sm btn-outline-primary time-chip" data-t="21:00">Night 9:00</button>
                            <input type="time" class="form-control form-control-sm w-auto" id="customTime" aria-label="Custom time">
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="addCustomTime">+ Add</button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="foodSel" class="form-label fw-semibold">Food note</label>
                        <select class="form-select" id="foodSel">
                            <option value="">--</option>
                            <option value="Before food">Before food</option>
                            <option value="After food">After food</option>
                            <option value="With food">With food</option>
                            <option value="Empty stomach">Empty stomach</option>
                        </select>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="addBtn">Add to Schedule</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>
                </div>
            </div>

            <div class="card shadow-sm mb-4 d-none" id="schedCard">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="h5 mb-0">Daily Schedule</h2>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-outline-success" id="printBtn">Print</button>
                            <button type="button" class="btn btn-sm btn-outline-danger" id="clearAllBtn">Clear All</button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle" id="schedTable">
                            <thead class="table-light">
                                <tr><th>Time</th><th>Medicine</th><th>Dose</th><th>Food</th><th></th></tr>
                            </thead>
                            <tbody id="schedBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <p class="text-muted small">This is only a timing planner — it does not give medicine or dose advice. Always keep the dose and timings as your doctor told you.</p>

            <h2>How to use</h2>
            <ol>
                <li>Enter the medicine name and the dose your doctor told you.</li>
                <li>Press the timing chips (or add your own time) and choose a food note.</li>
                <li>Press Add to Schedule — the schedule will be sorted by time and saved.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var LS_KEY = 'azlaan_med_schedule_v1';
    var medName = document.getElementById('medName');
    var doseNote = document.getElementById('doseNote');
    var foodSel = document.getElementById('foodSel');
    var customTime = document.getElementById('customTime');
    var addCustomTime = document.getElementById('addCustomTime');
    var addBtn = document.getElementById('addBtn');
    var errorBox = document.getElementById('errorBox');
    var schedCard = document.getElementById('schedCard');
    var schedBody = document.getElementById('schedBody');
    var printBtn = document.getElementById('printBtn');
    var clearAllBtn = document.getElementById('clearAllBtn');
    var timeChips = document.getElementById('timeChips');

    var selectedTimes = [];
    var schedule = [];

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

    function save() {
        try { localStorage.setItem(LS_KEY, JSON.stringify(schedule)); } catch (e) {}
    }
    function load() {
        try {
            var raw = localStorage.getItem(LS_KEY);
            if (raw) schedule = JSON.parse(raw) || [];
        } catch (e) { schedule = []; }
    }

    function render() {
        schedBody.innerHTML = '';
        var rows = schedule.slice().sort(function (a, b) { return a.time < b.time ? -1 : 1; });
        rows.forEach(function (r, idx) {
            var tr = document.createElement('tr');
            tr.innerHTML = '<td class="fw-bold">' + esc(r.time) + '</td>' +
                '<td>' + esc(r.name) + '</td>' +
                '<td>' + esc(r.dose || '-') + '</td>' +
                '<td>' + esc(r.food || '-') + '</td>';
            var td = document.createElement('td');
            var del = document.createElement('button');
            del.type = 'button';
            del.className = 'btn btn-sm btn-outline-danger';
            del.textContent = 'Remove';
            del.addEventListener('click', function () {
                var i = schedule.indexOf(r);
                if (i !== -1) schedule.splice(i, 1);
                save(); render();
            });
            td.appendChild(del);
            tr.appendChild(td);
            schedBody.appendChild(tr);
        });
        schedCard.classList.toggle('d-none', schedule.length === 0);
    }

    function refreshChips() {
        var chips = timeChips.querySelectorAll('.time-chip');
        chips.forEach(function (c) {
            var on = selectedTimes.indexOf(c.getAttribute('data-t')) !== -1;
            c.classList.toggle('btn-primary', on);
            c.classList.toggle('btn-outline-primary', !on);
        });
    }

    timeChips.addEventListener('click', function (e) {
        var chip = e.target.closest('.time-chip');
        if (!chip) return;
        var t = chip.getAttribute('data-t');
        var i = selectedTimes.indexOf(t);
        if (i === -1) selectedTimes.push(t); else selectedTimes.splice(i, 1);
        refreshChips();
    });

    addCustomTime.addEventListener('click', function () {
        var t = customTime.value;
        if (!t) { showError('Please select a time first.'); return; }
        hideError();
        if (selectedTimes.indexOf(t) === -1) {
            selectedTimes.push(t);
            var chip = document.createElement('button');
            chip.type = 'button';
            chip.className = 'btn btn-sm btn-outline-primary time-chip';
            chip.setAttribute('data-t', t);
            chip.textContent = t;
            timeChips.insertBefore(chip, customTime);
            refreshChips();
        }
    });

    addBtn.addEventListener('click', function () {
        hideError();
        var name = medName.value.trim();
        var dose = doseNote.value.trim();
        var food = foodSel.value;
        if (!name) { showError('Please enter the medicine name.'); return; }
        if (!selectedTimes.length) { showError('Please select at least one timing.'); return; }
        selectedTimes.forEach(function (t) {
            schedule.push({ name: name, dose: dose, food: food, time: t });
        });
        save(); render();
        medName.value = ''; doseNote.value = ''; foodSel.value = '';
        selectedTimes = [];
        var chips = timeChips.querySelectorAll('.time-chip');
        chips.forEach(function (c) { c.classList.remove('btn-primary'); c.classList.add('btn-outline-primary'); });
    });

    clearAllBtn.addEventListener('click', function () {
        if (!confirm('Delete the whole schedule?')) return;
        schedule = []; save(); render();
    });

    printBtn.addEventListener('click', function () {
        var rows = schedule.slice().sort(function (a, b) { return a.time < b.time ? -1 : 1; });
        var html = rows.map(function (r) {
            return '<tr><td><b>' + esc(r.time) + '</b></td><td>' + esc(r.name) + '</td><td>' +
                esc(r.dose || '-') + '</td><td>' + esc(r.food || '-') + '</td></tr>';
        }).join('');
        var w = window.open('', '_blank');
        w.document.write('<!doctype html><html><head><title>Medicine Schedule</title>' +
            '<style>table{border-collapse:collapse;width:100%}td,th{border:1px solid #333;padding:8px}</style>' +
            '</head><body><h2>Daily Medicine Schedule</h2>' +
            '<table><tr><th>Time</th><th>Medicine</th><th>Dose</th><th>Food</th></tr>' + html + '</table>' +
            '<p><small>Timing planner only — medicine and dose as your doctor told you.</small></p></body></html>');
        w.document.close();
        w.focus();
        w.print();
    });

    load();
    render();
})();
</script>
@endsection
