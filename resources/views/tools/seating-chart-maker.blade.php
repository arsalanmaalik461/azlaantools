@extends('layouts.app')
@section('title', 'Classroom Seating Chart Maker - Azlaan Tools')
@section('meta_description', 'Make exam hall or classroom seating plans roll number wise. Free online seating chart maker.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Classroom Seating Chart Maker</h1>
            <p class="lead text-muted">Make a seating plan for the exam hall or the class — by roll number or by name. Print the ready chart and put it on the door.</p>

            <div class="card shadow-sm mb-4 no-print">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="className" class="form-label fw-semibold">Class / Exam name (optional)</label>
                        <input type="text" class="form-control" id="className" placeholder="e.g. Class 10-A, Board Exam Morning Shift" maxlength="80">
                    </div>

                    <span class="form-label fw-semibold d-block mb-2">How to enter students</span>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="entryMode" id="modeRoll" value="roll" checked>
                        <label class="form-check-label" for="modeRoll">By roll number range</label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="radio" name="entryMode" id="modeNames" value="names">
                        <label class="form-check-label" for="modeNames">From a name list (one name per line)</label>
                    </div>

                    <div class="row g-3 mb-3" id="rollInputs">
                        <div class="col-6">
                            <label for="rollStart" class="form-label fw-semibold">First roll no.</label>
                            <input type="number" class="form-control" id="rollStart" value="1" min="1" max="9999">
                        </div>
                        <div class="col-6">
                            <label for="rollEnd" class="form-label fw-semibold">Last roll no.</label>
                            <input type="number" class="form-control" id="rollEnd" value="30" min="1" max="9999">
                        </div>
                    </div>

                    <div class="mb-3 d-none" id="namesInput">
                        <label for="nameList" class="form-label fw-semibold">Names (one per line)</label>
                        <textarea class="form-control" id="nameList" rows="5" placeholder="Ali&#10;Sara&#10;Ahmed"></textarea>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-12 col-md-4">
                            <label for="colsSel" class="form-label fw-semibold">Seats per row</label>
                            <select class="form-select" id="colsSel">
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4" selected>4</option>
                                <option value="5">5</option>
                                <option value="6">6</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="dirSel" class="form-label fw-semibold">Fill direction</label>
                            <select class="form-select" id="dirSel">
                                <option value="snake" selected>Snake (alternate rows reversed)</option>
                                <option value="straight">Straight (each row from left)</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-4 d-flex align-items-end">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="teacherDesk" checked>
                                <label class="form-check-label" for="teacherDesk">Show teacher desk at front</label>
                            </div>
                        </div>
                    </div>

                    <button type="button" class="btn btn-primary w-100" id="goBtn">Make Seating Chart</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>
                </div>
            </div>

            <div id="results" class="d-none">
                <div class="d-flex justify-content-between align-items-center mb-3 no-print">
                    <h2 class="h5 mb-0">Seating Chart</h2>
                    <button type="button" class="btn btn-success" id="printBtn">Print</button>
                </div>
                <div id="chartPaper" class="border rounded p-3 p-md-4 bg-white">
                    <div class="text-center mb-3">
                        <h3 class="h5 mb-1" id="chartTitle">Seating Plan</h3>
                        <p class="text-muted small mb-0" id="chartMeta"></p>
                    </div>
                    <div id="teacherRow" class="mb-3 d-none">
                        <div class="mx-auto text-center border rounded py-2 bg-light" style="max-width: 220px;">
                            <strong>TEACHER DESK</strong>
                        </div>
                        <div class="text-center text-muted small mt-1">BOARD / FRONT</div>
                    </div>
                    <div id="chartGrid"></div>
                </div>
            </div>

            <h2 class="mt-4 no-print">How to use</h2>
            <ol class="no-print">
                <li>Enter a roll number range or a list of names.</li>
                <li>Choose the seats per row and the fill direction.</li>
                <li>Click "Make Seating Chart", then "Print".</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<style>
@media print {
    .no-print { display: none !important; }
    #chartPaper { border: none !important; }
    body { background: #fff !important; }
}
.seat-card {
    border: 2px solid #6d28d9;
    border-radius: 8px;
    padding: 8px 4px;
    text-align: center;
    background: #f8f7ff;
    min-height: 64px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    word-break: break-word;
}
.seat-card .seat-roll { font-weight: 700; font-size: 1.05rem; color: #4c1d95; }
.seat-card .seat-sub { font-size: 0.72rem; color: #6c757d; }
</style>
<script>
(function () {
    'use strict';
    var goBtn = document.getElementById('goBtn');
    var printBtn = document.getElementById('printBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var className = document.getElementById('className');
    var modeRoll = document.getElementById('modeRoll');
    var rollInputs = document.getElementById('rollInputs');
    var namesInput = document.getElementById('namesInput');
    var rollStart = document.getElementById('rollStart');
    var rollEnd = document.getElementById('rollEnd');
    var nameList = document.getElementById('nameList');
    var colsSel = document.getElementById('colsSel');
    var dirSel = document.getElementById('dirSel');
    var teacherDesk = document.getElementById('teacherDesk');
    var chartTitle = document.getElementById('chartTitle');
    var chartMeta = document.getElementById('chartMeta');
    var chartGrid = document.getElementById('chartGrid');
    var teacherRow = document.getElementById('teacherRow');

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }

    document.getElementsByName('entryMode').forEach(function (r) {
        r.addEventListener('change', function () {
            if (modeRoll.checked) {
                rollInputs.classList.remove('d-none');
                namesInput.classList.add('d-none');
            } else {
                rollInputs.classList.add('d-none');
                namesInput.classList.remove('d-none');
            }
        });
    });

    function collectStudents() {
        if (modeRoll.checked) {
            var s = parseInt(rollStart.value, 10);
            var e = parseInt(rollEnd.value, 10);
            if (isNaN(s) || isNaN(e)) return { err: 'Enter roll numbers.' };
            if (s < 1 || e < 1) return { err: 'Roll number must be 1 or more.' };
            if (e < s) return { err: 'The last roll number must be larger than the first.' };
            if (e - s + 1 > 500) return { err: 'Maximum 500 students in one chart.' };
            var arr = [];
            for (var i = s; i <= e; i++) arr.push({ main: String(i), sub: 'Roll No. ' + i });
            return { list: arr };
        }
        var lines = nameList.value.split('\n').map(function (x) { return x.trim(); }).filter(Boolean);
        if (!lines.length) return { err: 'Enter at least one name.' };
        if (lines.length > 500) return { err: 'Maximum 500 names.' };
        var arr2 = lines.map(function (n, i) { return { main: n, sub: 'Seat ' + (i + 1) }; });
        return { list: arr2 };
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var got = collectStudents();
        if (got.err) { showError(got.err); return; }
        var list = got.list;
        var cols = parseInt(colsSel.value, 10);
        var snake = dirSel.value === 'snake';

        var rows = Math.ceil(list.length / cols);
        chartGrid.innerHTML = '';
        var idx = 0;
        for (var r = 0; r < rows; r++) {
            var rowSeats = list.slice(idx, idx + cols);
            idx += rowSeats.length;
            if (snake && r % 2 === 1) rowSeats.reverse();
            var rowDiv = document.createElement('div');
            rowDiv.className = 'row g-2 mb-2';
            rowSeats.forEach(function (st) {
                var col = document.createElement('div');
                col.className = 'col';
                var card = document.createElement('div');
                card.className = 'seat-card';
                var main = document.createElement('div');
                main.className = 'seat-roll';
                main.textContent = st.main;
                var sub = document.createElement('div');
                sub.className = 'seat-sub';
                sub.textContent = st.sub;
                card.appendChild(main);
                card.appendChild(sub);
                col.appendChild(card);
                rowDiv.appendChild(col);
            });
            // keep every row the same width even if the last row is short
            for (var pad = rowSeats.length; pad < cols; pad++) {
                var empty = document.createElement('div');
                empty.className = 'col';
                rowDiv.appendChild(empty);
            }
            chartGrid.appendChild(rowDiv);
        }

        var t = className.value.trim();
        chartTitle.textContent = t ? t : 'Seating Plan';
        chartMeta.textContent = 'Total students: ' + list.length + '  |  Rows: ' + rows + '  |  Seats per row: ' + cols;
        if (teacherDesk.checked) { teacherRow.classList.remove('d-none'); }
        else { teacherRow.classList.add('d-none'); }

        results.classList.remove('d-none');
        results.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });

    printBtn.addEventListener('click', function () { window.print(); });
})();
</script>
@endsection
