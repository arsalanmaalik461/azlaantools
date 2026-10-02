@extends('layouts.app')

@section('title', 'Weighted Gradebook Calculator - Azlaan Tools')
@section('meta_description', 'Enter tests, quizzes and assignments with weights to compute final grades for the whole class, free online.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Weighted Gradebook Calculator</h1>
            <p class="lead text-muted">Set weights for tests, quizzes and assignments, enter marks for the whole class — get final grades and class statistics instantly.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5>Categories &amp; weights <small class="text-muted">(the total must be 100%)</small></h5>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light"><tr><th>Category</th><th style="width:140px">Weight %</th><th style="width:40px"></th></tr></thead>
                            <tbody id="catBody"></tbody>
                            <tfoot><tr><th class="text-end">Total</th><th id="weightTotal" class="text-center">0%</th><th></th></tr></tfoot>
                        </table>
                    </div>
                    <button type="button" class="btn btn-outline-secondary btn-sm mb-3" id="addCatBtn">+ Add category</button>

                    <h5>Students &amp; scores</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle" id="studentsTable">
                            <thead class="table-light" id="studentsHead"></thead>
                            <tbody id="studentsBody"></tbody>
                        </table>
                    </div>
                    <button type="button" class="btn btn-outline-secondary btn-sm mb-3" id="addStudentBtn">+ Add student</button>

                    <button type="button" class="btn btn-primary w-100" id="goBtn">Calculate Grades</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <h5>Results</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered" id="resultsTable">
                                <thead class="table-light"><tr><th>Student</th><th>Final %</th><th>Grade</th></tr></thead>
                                <tbody id="resultsBody"></tbody>
                            </table>
                        </div>
                        <div class="row" id="statsRow"></div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Type the categories (Tests, Quizzes...) and their weights — the total must be 100%.</li>
                <li>Enter each student name and their score in each category (0-100).</li>
                <li>Press "Calculate Grades" — see the final percentage, grade and class stats.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var catBody = document.getElementById('catBody');
    var studentsHead = document.getElementById('studentsHead');
    var studentsBody = document.getElementById('studentsBody');

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
        return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

    function updateWeightTotal() {
        var sum = 0;
        var ws = catBody.querySelectorAll('.cat-weight');
        for (var i = 0; i < ws.length; i++) sum += parseFloat(ws[i].value) || 0;
        var el = document.getElementById('weightTotal');
        el.textContent = sum + '%';
        el.className = 'text-center ' + (Math.abs(sum - 100) < 0.001 ? 'text-success fw-bold' : 'text-danger fw-bold');
    }

    function rebuildStudentHeader() {
        var names = catBody.querySelectorAll('.cat-name');
        var html = '<tr><th>Student name</th>';
        for (var i = 0; i < names.length; i++) {
            html += '<th>' + esc(names[i].value.trim() || 'Cat ' + (i + 1)) + '</th>';
        }
        html += '<th style="width:40px"></th></tr>';
        studentsHead.innerHTML = html;
        var rows = studentsBody.querySelectorAll('tr');
        for (var r = 0; r < rows.length; r++) syncStudentRow(rows[r]);
    }

    function syncStudentRow(tr) {
        var count = catBody.querySelectorAll('.cat-name').length;
        var inputs = tr.querySelectorAll('.stu-score');
        if (inputs.length < count) {
            for (var i = inputs.length; i < count; i++) {
                var td = document.createElement('td');
                td.innerHTML = '<input type="number" class="form-control form-control-sm stu-score" min="0" max="100" step="0.1" placeholder="0-100">';
                tr.insertBefore(td, tr.lastElementChild);
            }
        } else if (inputs.length > count) {
            for (var j = inputs.length - 1; j >= count; j--) inputs[j].parentNode.remove();
        }
    }

    function addCatRow(name, weight) {
        var tr = document.createElement('tr');
        tr.innerHTML =
            '<td><input type="text" class="form-control form-control-sm cat-name" value="' + esc(name || '') + '" placeholder="e.g. Tests"></td>' +
            '<td><input type="number" class="form-control form-control-sm cat-weight" min="0" max="100" step="0.1" value="' + (weight || 0) + '"></td>' +
            '<td><button type="button" class="btn btn-sm btn-outline-danger rm-cat">&times;</button></td>';
        tr.querySelector('.rm-cat').addEventListener('click', function () {
            if (catBody.querySelectorAll('tr').length <= 1) { showError('At least one category is needed.'); return; }
            tr.remove(); updateWeightTotal(); rebuildStudentHeader();
        });
        var nm = tr.querySelector('.cat-name'), wt = tr.querySelector('.cat-weight');
        nm.addEventListener('input', rebuildStudentHeader);
        wt.addEventListener('input', updateWeightTotal);
        catBody.appendChild(tr);
        updateWeightTotal(); rebuildStudentHeader();
    }

    function addStudentRow(name) {
        var tr = document.createElement('tr');
        tr.innerHTML = '<td><input type="text" class="form-control form-control-sm stu-name" value="' + esc(name || '') + '" placeholder="Student name"></td>' +
            '<td><button type="button" class="btn btn-sm btn-outline-danger rm-stu">&times;</button></td>';
        tr.querySelector('.rm-stu').addEventListener('click', function () { tr.remove(); });
        studentsBody.appendChild(tr);
        syncStudentRow(tr);
    }

    document.getElementById('addCatBtn').addEventListener('click', function () {
        if (catBody.querySelectorAll('tr').length >= 6) { showError('Maximum 6 categories.'); return; }
        addCatRow('', 0);
    });
    document.getElementById('addStudentBtn').addEventListener('click', function () { addStudentRow(''); });

    addCatRow('Tests', 40); addCatRow('Quizzes', 30); addCatRow('Assignments', 30);
    addStudentRow(''); addStudentRow(''); addStudentRow('');

    function gradeOf(pct) {
        if (pct >= 90) return 'A+';
        if (pct >= 80) return 'A';
        if (pct >= 70) return 'B';
        if (pct >= 60) return 'C';
        if (pct >= 50) return 'D';
        return 'F';
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var nameEls = catBody.querySelectorAll('.cat-name');
        var weightEls = catBody.querySelectorAll('.cat-weight');
        var cats = [], totalW = 0;
        for (var i = 0; i < nameEls.length; i++) {
            var nm = nameEls[i].value.trim() || ('Category ' + (i + 1));
            var w = parseFloat(weightEls[i].value) || 0;
            if (w <= 0) { showError('Each category weight must be above 0.'); return; }
            cats.push({ name: nm, weight: w });
            totalW += w;
        }
        if (Math.abs(totalW - 100) > 0.01) {
            showError('Weights must total 100% (currently ' + totalW + '%).');
            return;
        }

        var rows = studentsBody.querySelectorAll('tr');
        var students = [];
        for (var r = 0; r < rows.length; r++) {
            var sName = rows[r].querySelector('.stu-name').value.trim();
            if (!sName) continue;
            var scoreEls = rows[r].querySelectorAll('.stu-score');
            var final = 0, ok = true;
            for (var c = 0; c < cats.length; c++) {
                var sc = scoreEls[c] ? parseFloat(scoreEls[c].value) : NaN;
                if (isNaN(sc) || sc < 0 || sc > 100) { ok = false; break; }
                final += sc * cats[c].weight / 100;
            }
            if (!ok) { showError('"' + sName + '": all scores must be between 0-100.'); return; }
            students.push({ name: sName, pct: final, grade: gradeOf(final) });
        }
        if (students.length === 0) { showError('Enter at least one student name and scores.'); return; }

        students.sort(function (a, b) { return b.pct - a.pct; });
        var html = '';
        for (var s = 0; s < students.length; s++) {
            html += '<tr><td>' + esc(students[s].name) + '</td>' +
                '<td class="text-end">' + students[s].pct.toFixed(1) + '%</td>' +
                '<td class="text-center fw-bold">' + students[s].grade + '</td></tr>';
        }
        document.getElementById('resultsBody').innerHTML = html;

        var pcts = students.map(function (x) { return x.pct; }).sort(function (a, b) { return a - b; });
        var sum = pcts.reduce(function (a, b) { return a + b; }, 0);
        var avg = sum / pcts.length;
        var med = pcts.length % 2 ? pcts[(pcts.length - 1) / 2] : (pcts[pcts.length / 2 - 1] + pcts[pcts.length / 2]) / 2;
        var counts = { 'A+/A': 0, B: 0, C: 0, D: 0, F: 0 };
        for (var g = 0; g < students.length; g++) {
            var gr = students[g].grade;
            if (gr === 'A+' || gr === 'A') counts['A+/A']++;
            else counts[gr]++;
        }
        document.getElementById('statsRow').innerHTML =
            '<div class="col-6 col-md-3 mb-2"><div class="card text-center"><div class="card-body p-2"><div class="small text-muted">Class average</div><div class="fw-bold">' + avg.toFixed(1) + '%</div></div></div></div>' +
            '<div class="col-6 col-md-3 mb-2"><div class="card text-center"><div class="card-body p-2"><div class="small text-muted">Median</div><div class="fw-bold">' + med.toFixed(1) + '%</div></div></div></div>' +
            '<div class="col-6 col-md-3 mb-2"><div class="card text-center"><div class="card-body p-2"><div class="small text-muted">Highest / Lowest</div><div class="fw-bold">' + pcts[pcts.length - 1].toFixed(1) + '% / ' + pcts[0].toFixed(1) + '%</div></div></div></div>' +
            '<div class="col-6 col-md-3 mb-2"><div class="card text-center"><div class="card-body p-2"><div class="small text-muted">A/A+ : B : C : D : F</div><div class="fw-bold">' + counts['A+/A'] + ' : ' + counts.B + ' : ' + counts.C + ' : ' + counts.D + ' : ' + counts.F + '</div></div></div></div>';

        results.classList.remove('d-none');
    });
})();
</script>
@endsection
