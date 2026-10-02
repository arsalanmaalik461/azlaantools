@extends('layouts.app')
@section('title', 'Attendance Register Tracker — Azlaan Tools')
@section('meta_description', 'Mark daily class attendance online, saved in your browser. Monthly summary and attendance percentage for every student — free, no signup.')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Attendance Register Tracker</h1>
            <p class="lead text-muted">Online attendance register — mark daily attendance, see the monthly summary and percentage. Your data stays saved only in your browser.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <label for="className" class="form-label fw-semibold">Class / section name</label>
                            <input type="text" class="form-control" id="className" placeholder="e.g. Class 5-A">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="button" class="btn btn-outline-primary w-100" id="saveClassBtn">Save Name</button>
                        </div>
                    </div>

                    <h2 class="h6">Add Students</h2>
                    <div class="input-group mb-2">
                        <input type="text" class="form-control" id="studentName" placeholder="Write student name">
                        <button type="button" class="btn btn-primary" id="addStudentBtn">Add</button>
                    </div>
                    <div class="d-flex flex-wrap gap-2 mb-4" id="studentChips"></div>

                    <h2 class="h6">Today's Attendance</h2>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="attDate" class="form-label fw-semibold">Date</label>
                            <input type="date" class="form-control" id="attDate">
                        </div>
                        <div class="col-md-6 d-flex align-items-end gap-2">
                            <button type="button" class="btn btn-outline-success" id="allPresentBtn">All present</button>
                            <button type="button" class="btn btn-outline-secondary" id="allAbsentBtn">All absent</button>
                        </div>
                    </div>
                    <div id="markList" class="list-group mb-3"></div>
                    <button type="button" class="btn btn-success w-100" id="saveDayBtn">Save attendance for this day</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>
                    <div class="alert alert-success mt-3 d-none" id="okBox" role="alert"></div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h6">Monthly summary</h2>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="repMonth" class="form-label fw-semibold">Select month</label>
                            <input type="month" class="form-control" id="repMonth">
                        </div>
                        <div class="col-md-6 d-flex align-items-end gap-2">
                            <button type="button" class="btn btn-primary" id="goBtn">View Report</button>
                            <button type="button" class="btn btn-outline-secondary" id="csvBtn">CSV Download</button>
                        </div>
                    </div>
                    <div id="results" class="d-none">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead class="table-light">
                                    <tr><th>Student</th><th>Present (P)</th><th>Absent (A)</th><th>Total Days</th><th>%</th></tr>
                                </thead>
                                <tbody id="repBody"></tbody>
                            </table>
                        </div>
                        <p class="text-muted small mb-0">Percentage = present days ÷ total marked days × 100.</p>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Write the class name and add students.</li>
                <li>Select the date, press <strong>Present</strong> or <strong>Absent</strong> for each student, then save.</li>
                <li>Select a month and click <strong>View Report</strong> — you will see each student's percentage.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var KEY = 'attendance_register_v1';

    var classNameEl = document.getElementById('className');
    var studentNameEl = document.getElementById('studentName');
    var studentChips = document.getElementById('studentChips');
    var attDateEl = document.getElementById('attDate');
    var markList = document.getElementById('markList');
    var repMonthEl = document.getElementById('repMonth');
    var repBody = document.getElementById('repBody');
    var errorBox = document.getElementById('errorBox');
    var okBox = document.getElementById('okBox');
    var results = document.getElementById('results');

    var state = { className: '', students: [], records: {} }; // records: { 'YYYY-MM-DD': { studentIdx: 'P'|'A' } }
    var marks = {}; // working marks for selected date: idx -> 'P'|'A'

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        okBox.classList.add('d-none');
    }
    function showOk(msg) {
        okBox.textContent = msg;
        okBox.classList.remove('d-none');
        errorBox.classList.add('d-none');
    }
    function hideMsgs() {
        errorBox.classList.add('d-none');
        okBox.classList.add('d-none');
    }
    function todayStr() {
        var d = new Date();
        return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
    }

    function load() {
        try {
            var r = localStorage.getItem(KEY);
            if (r) { state = JSON.parse(r); }
        } catch (e) { /* ignore */ }
        if (!state.students) { state.students = []; }
        if (!state.records) { state.records = {}; }
    }
    function save() {
        try { localStorage.setItem(KEY, JSON.stringify(state)); } catch (e) { /* ignore */ }
    }

    function renderChips() {
        studentChips.innerHTML = '';
        if (!state.students.length) {
            studentChips.innerHTML = '<span class="text-muted small">No students added yet.</span>';
            return;
        }
        for (var i = 0; i < state.students.length; i++) {
            (function (idx) {
                var chip = document.createElement('span');
                chip.className = 'badge bg-light text-dark border d-inline-flex align-items-center gap-2 p-2';
                var nm = document.createElement('span');
                nm.textContent = state.students[idx];
                var x = document.createElement('button');
                x.type = 'button';
                x.className = 'btn-close btn-close-sm';
                x.setAttribute('aria-label', 'Remove');
                x.addEventListener('click', function () {
                    state.students.splice(idx, 1);
                    // fix records keys
                    var rec2 = {};
                    Object.keys(state.records).forEach(function (d) {
                        var day = {};
                        Object.keys(state.records[d]).forEach(function (k) {
                            var ki = parseInt(k, 10);
                            if (ki < idx) { day[k] = state.records[d][k]; }
                            else if (ki > idx) { day[ki - 1] = state.records[d][k]; }
                        });
                        rec2[d] = day;
                    });
                    state.records = rec2;
                    save(); renderChips(); renderMarkList();
                });
                chip.appendChild(nm);
                chip.appendChild(x);
                studentChips.appendChild(chip);
            })(i);
        }
    }

    function renderMarkList() {
        markList.innerHTML = '';
        marks = {};
        var date = attDateEl.value;
        var saved = (date && state.records[date]) ? state.records[date] : {};
        if (!state.students.length) {
            markList.innerHTML = '<div class="list-group-item text-muted">Add students first.</div>';
            return;
        }
        for (var i = 0; i < state.students.length; i++) {
            (function (idx) {
                var cur = saved[idx] || 'P';
                marks[idx] = cur;
                var item = document.createElement('div');
                item.className = 'list-group-item d-flex justify-content-between align-items-center gap-2 flex-wrap';
                var nm = document.createElement('span');
                nm.className = 'fw-semibold';
                nm.textContent = state.students[idx];
                var grp = document.createElement('div');
                grp.className = 'btn-group';
                grp.setAttribute('role', 'group');
                var bP = document.createElement('button');
                bP.type = 'button';
                bP.className = 'btn btn-sm ' + (cur === 'P' ? 'btn-success' : 'btn-outline-success');
                bP.textContent = 'Present';
                var bA = document.createElement('button');
                bA.type = 'button';
                bA.className = 'btn btn-sm ' + (cur === 'A' ? 'btn-danger' : 'btn-outline-danger');
                bA.textContent = 'Absent';
                bP.addEventListener('click', function () {
                    marks[idx] = 'P';
                    bP.className = 'btn btn-sm btn-success';
                    bA.className = 'btn btn-sm btn-outline-danger';
                });
                bA.addEventListener('click', function () {
                    marks[idx] = 'A';
                    bA.className = 'btn btn-sm btn-danger';
                    bP.className = 'btn btn-sm btn-outline-success';
                });
                grp.appendChild(bP); grp.appendChild(bA);
                item.appendChild(nm); item.appendChild(grp);
                markList.appendChild(item);
            })(i);
        }
    }

    document.getElementById('saveClassBtn').addEventListener('click', function () {
        state.className = classNameEl.value.trim();
        save();
        showOk(state.className ? 'Class name saved: ' + state.className : 'Class name cleared.');
    });

    function addStudent() {
        hideMsgs();
        var n = studentNameEl.value.trim();
        if (!n) { showError('Write the student name.'); return; }
        if (state.students.indexOf(n) >= 0) { showError('This name already exists.'); return; }
        state.students.push(n);
        studentNameEl.value = '';
        save(); renderChips(); renderMarkList();
    }
    document.getElementById('addStudentBtn').addEventListener('click', addStudent);
    studentNameEl.addEventListener('keydown', function (e) { if (e.key === 'Enter') { addStudent(); } });

    attDateEl.addEventListener('change', function () { hideMsgs(); renderMarkList(); });

    document.getElementById('allPresentBtn').addEventListener('click', function () {
        for (var i = 0; i < state.students.length; i++) { marks[i] = 'P'; }
        renderMarkList();
    });
    document.getElementById('allAbsentBtn').addEventListener('click', function () {
        for (var i = 0; i < state.students.length; i++) { marks[i] = 'A'; }
        renderMarkList();
    });

    document.getElementById('saveDayBtn').addEventListener('click', function () {
        hideMsgs();
        var date = attDateEl.value;
        if (!date) { showError('Select a date first.'); return; }
        if (!state.students.length) { showError('Add students first.'); return; }
        var day = {};
        for (var i = 0; i < state.students.length; i++) { day[i] = marks[i] || 'P'; }
        state.records[date] = day;
        save();
        showOk('Attendance for ' + date + ' saved.');
    });

    document.getElementById('goBtn').addEventListener('click', function () {
        hideMsgs();
        var ym = repMonthEl.value;
        if (!ym) { showError('Select a month first.'); return; }
        if (!state.students.length) { showError('Add students first.'); return; }
        repBody.innerHTML = '';
        var anyDay = false;
        for (var i = 0; i < state.students.length; i++) {
            var p = 0, a = 0;
            Object.keys(state.records).forEach(function (d) {
                if (d.indexOf(ym) === 0 && state.records[d][i]) {
                    anyDay = true;
                    if (state.records[d][i] === 'P') { p++; } else { a++; }
                }
            });
            var total = p + a;
            var pct = total ? (p / total * 100) : 0;
            var tr = document.createElement('tr');
            var tdN = document.createElement('td'); tdN.textContent = state.students[i];
            var tdP = document.createElement('td'); tdP.textContent = p;
            var tdA = document.createElement('td'); tdA.textContent = a;
            var tdT = document.createElement('td'); tdT.textContent = total;
            var tdPct = document.createElement('td');
            var badge = document.createElement('span');
            badge.className = 'badge ' + (pct >= 75 ? 'bg-success' : (pct >= 50 ? 'bg-warning text-dark' : 'bg-danger'));
            badge.textContent = pct.toFixed(1) + '%';
            tdPct.appendChild(badge);
            tr.appendChild(tdN); tr.appendChild(tdP); tr.appendChild(tdA); tr.appendChild(tdT); tr.appendChild(tdPct);
            repBody.appendChild(tr);
        }
        if (!anyDay) { showError('No attendance saved for this month.'); return; }
        results.classList.remove('d-none');
    });

    document.getElementById('csvBtn').addEventListener('click', function () {
        hideMsgs();
        var ym = repMonthEl.value;
        if (!ym) { showError('Select a month first.'); return; }
        var rows = [['Student', 'Present', 'Absent', 'Total', 'Percent']];
        Object.keys(state.records).forEach(function () { /* noop */ });
        for (var i = 0; i < state.students.length; i++) {
            var p = 0, a = 0;
            Object.keys(state.records).forEach(function (d) {
                if (d.indexOf(ym) === 0 && state.records[d][i]) {
                    if (state.records[d][i] === 'P') { p++; } else { a++; }
                }
            });
            var total = p + a;
            rows.push([state.students[i], p, a, total, total ? (p / total * 100).toFixed(1) : '0.0']);
        }
        var csv = rows.map(function (r) {
            return r.map(function (c) { return '"' + String(c).replace(/"/g, '""') + '"'; }).join(',');
        }).join('\n');
        var blob = new Blob(["\uFEFF" + csv], { type: 'text/csv;charset=utf-8' });
        var aEl = document.createElement('a');
        aEl.href = URL.createObjectURL(blob);
        aEl.download = 'attendance-' + ym + '.csv';
        document.body.appendChild(aEl);
        aEl.click();
        setTimeout(function () { URL.revokeObjectURL(aEl.href); aEl.remove(); }, 500);
    });

    load();
    classNameEl.value = state.className || '';
    attDateEl.value = todayStr();
    (function () {
        var d = new Date();
        repMonthEl.value = d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0');
    })();
    renderChips();
    renderMarkList();
})();
</script>
@endsection
