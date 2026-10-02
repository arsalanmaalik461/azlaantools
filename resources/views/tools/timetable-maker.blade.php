@extends('layouts.app')

@section('title', 'Class Timetable Maker - Azlaan Tools')
@section('meta_description', 'Make and print a weekly class timetable for school and college. Free online timetable maker.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Class Timetable Maker</h1>
            <p class="lead text-muted">Make and print a weekly class timetable — for school and college. Choose the days, write the periods, press print.</p>

            <div class="card shadow-sm mb-4 d-print-none">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="schoolInput" class="form-label fw-semibold">School / Class name (optional)</label>
                        <input type="text" class="form-control" id="schoolInput" placeholder="Example: City School — Class 8-A">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold d-block">Days</label>
                        <div id="dayChecks" class="d-flex flex-wrap gap-2"></div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="periodCount" class="form-label fw-semibold">Periods per day</label>
                            <input type="number" class="form-control" id="periodCount" value="6" min="1" max="12">
                        </div>
                        <div class="col-md-6">
                            <label for="breakAfter" class="form-label fw-semibold">Break after which period? (0 = no break)</label>
                            <input type="number" class="form-control" id="breakAfter" value="3" min="0" max="12">
                        </div>
                    </div>
                    <div class="row g-2 mt-3">
                        <div class="col-md-4"><button type="button" class="btn btn-primary w-100" id="goBtn">Make Timetable</button></div>
                        <div class="col-md-4"><button type="button" class="btn btn-outline-secondary w-100" id="sampleBtn">Fill Sample</button></div>
                        <div class="col-md-4"><button type="button" class="btn btn-success w-100" id="printBtn">Print / PDF</button></div>
                    </div>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <p class="fw-semibold">Write the subject in each box — the timetable saves by itself:</p>
                        <div class="table-responsive">
                            <table class="table table-bordered" id="ttTable">
                                <thead class="table-light"><tr id="ttHead"></tr></thead>
                                <tbody id="ttBody"></tbody>
                            </table>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger" id="clearBtn">Clear Timetable</button>
                    </div>
                </div>
            </div>

            <div class="d-none d-print-block text-center mb-3" id="printHead">
                <h2 id="printTitle">Class Timetable</h2>
            </div>

            <h2 class="d-print-none">How to use</h2>
            <ol class="d-print-none">
                <li>Write the school/class name and choose the days of the week.</li>
                <li>Set the number of periods per day and the break, then press "Make Timetable".</li>
                <li>Write the subject in each box — use "Print / PDF" to print or save a PDF.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
@media print {
    #ttTable input.cell { border: none !important; }
    #ttTable td, #ttTable th { padding: 10px !important; }
}
#ttTable input.cell { width: 100%; border: none; background: transparent; }
#ttTable input.cell:focus { outline: 2px solid #0d6efd; background: #f8f9fa; }
</style>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var goBtn = document.getElementById('goBtn');
    var sampleBtn = document.getElementById('sampleBtn');
    var printBtn = document.getElementById('printBtn');
    var clearBtn = document.getElementById('clearBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var dayChecks = document.getElementById('dayChecks');
    var STORAGE_KEY = 'azlaan_timetable_maker_v1';

    var days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    var dayCbs = [];

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function esc(s) {
        return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    days.forEach(function (d, i) {
        var wrap = document.createElement('div');
        wrap.className = 'form-check form-check-inline';
        var cb = document.createElement('input');
        cb.type = 'checkbox';
        cb.className = 'form-check-input';
        cb.id = 'day' + i;
        cb.value = d;
        cb.checked = i < 5;
        var lb = document.createElement('label');
        lb.className = 'form-check-label';
        lb.setAttribute('for', 'day' + i);
        lb.textContent = days[i];
        wrap.appendChild(cb);
        wrap.appendChild(lb);
        dayChecks.appendChild(wrap);
        dayCbs.push(cb);
    });

    function selectedDays() {
        var out = [];
        for (var i = 0; i < days.length; i++) {
            if (dayCbs[i] && dayCbs[i].checked) { out.push({ en: days[i] }); }
        }
        return out;
    }

    function buildTable() {
        hideError();
        var sel = selectedDays();
        if (!sel.length) { showError('Please choose at least one day.'); return; }
        var periods = parseInt(document.getElementById('periodCount').value, 10);
        if (isNaN(periods) || periods < 1 || periods > 12) { showError('Periods must be between 1 and 12.'); return; }
        var breakAfter = parseInt(document.getElementById('breakAfter').value, 10) || 0;

        var head = '<th>Day</th>';
        for (var p = 1; p <= periods; p++) {
            head += '<th>Period ' + p + '</th>';
            if (p === breakAfter) { head += '<th class="table-warning">Break</th>'; }
        }
        document.getElementById('ttHead').innerHTML = head;

        var body = '';
        sel.forEach(function (d, di) {
            body += '<tr><th class="table-light">' + esc(d.en) + '</th>';
            for (var p = 1; p <= periods; p++) {
                body += '<td><input type="text" class="cell" data-day="' + di + '" data-p="' + p + '" placeholder="-"></td>';
                if (p === breakAfter) { body += '<td class="table-warning text-center small">Break</td>'; }
            }
            body += '</tr>';
        });
        document.getElementById('ttBody').innerHTML = body;

        bindCells();
        saveState();
        results.classList.remove('d-none');
    }

    function bindCells() {
        var cells = document.querySelectorAll('#ttBody input.cell');
        for (var i = 0; i < cells.length; i++) {
            cells[i].addEventListener('input', saveState);
        }
    }

    function collectState() {
        var st = {
            school: document.getElementById('schoolInput').value,
            days: [],
            periods: document.getElementById('periodCount').value,
            breakAfter: document.getElementById('breakAfter').value,
            cells: {}
        };
        for (var i = 0; i < days.length; i++) {
            if (dayCbs[i] && dayCbs[i].checked) { st.days.push(i); }
        }
        var cells = document.querySelectorAll('#ttBody input.cell');
        for (var j = 0; j < cells.length; j++) {
            var v = cells[j].value.trim();
            if (v) { st.cells[cells[j].getAttribute('data-day') + '_' + cells[j].getAttribute('data-p')] = v; }
        }
        return st;
    }

    function saveState() {
        try { localStorage.setItem(STORAGE_KEY, JSON.stringify(collectState())); } catch (e) { /* ignore */ }
    }

    function loadState() {
        var raw = null;
        try { raw = localStorage.getItem(STORAGE_KEY); } catch (e) { return false; }
        if (!raw) { return false; }
        var st;
        try { st = JSON.parse(raw); } catch (e) { return false; }
        if (!st || !st.days || !st.days.length) { return false; }

        document.getElementById('schoolInput').value = st.school || '';
        document.getElementById('periodCount').value = st.periods || 6;
        document.getElementById('breakAfter').value = st.breakAfter || 0;
        for (var i = 0; i < days.length; i++) {
            dayCbs[i].checked = st.days.indexOf(i) !== -1;
        }
        buildTable();
        if (st.cells) {
            for (var key in st.cells) {
                if (st.cells.hasOwnProperty(key)) {
                    var parts = key.split('_');
                    var inp = document.querySelector('#ttBody input[data-day="' + parts[0] + '"][data-p="' + parts[1] + '"]');
                    if (inp) { inp.value = st.cells[key]; }
                }
            }
        }
        return true;
    }

    goBtn.addEventListener('click', buildTable);

    sampleBtn.addEventListener('click', function () {
        document.getElementById('schoolInput').value = 'City School — Class 8-A';
        document.getElementById('periodCount').value = 6;
        document.getElementById('breakAfter').value = 3;
        for (var i = 0; i < days.length; i++) {
            dayCbs[i].checked = i < 5;
        }
        buildTable();
        var sample = ['Urdu', 'English', 'Maths', 'Science', 'Islamiat', 'Computer'];
        var cells = document.querySelectorAll('#ttBody input.cell');
        for (var j = 0; j < cells.length; j++) {
            cells[j].value = sample[(parseInt(cells[j].getAttribute('data-p'), 10) + parseInt(cells[j].getAttribute('data-day'), 10)) % sample.length];
        }
        saveState();
    });

    printBtn.addEventListener('click', function () {
        hideError();
        var cells = document.querySelectorAll('#ttBody input.cell');
        if (!cells.length) { showError('Please press "Make Timetable" first.'); return; }
        var title = document.getElementById('schoolInput').value.trim();
        document.getElementById('printTitle').textContent = title ? title + ' — Timetable' : 'Class Timetable';
        saveState();
        window.print();
    });

    clearBtn.addEventListener('click', function () {
        var cells = document.querySelectorAll('#ttBody input.cell');
        for (var i = 0; i < cells.length; i++) { cells[i].value = ''; }
        try { localStorage.removeItem(STORAGE_KEY); } catch (e) { /* ignore */ }
    });

    document.getElementById('schoolInput').addEventListener('input', saveState);

    loadState();
})();
</script>
@endsection
