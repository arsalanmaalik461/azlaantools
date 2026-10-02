@extends('layouts.app')

@section('title', 'Report Card Generator - Azlaan Tools')
@section('meta_description', 'Create a printable student report card with marks, percentage and grades, free online.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Report Card Generator</h1>
            <p class="lead text-muted">Make a student result card — enter the marks, and the percentage and grades will be calculated automatically. Print or save as PDF.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="studentName" class="form-label fw-semibold">Student name</label>
                            <input type="text" class="form-control" id="studentName" placeholder="e.g. Ahmed Ali">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="schoolName" class="form-label fw-semibold">School name</label>
                            <input type="text" class="form-control" id="schoolName" placeholder="e.g. City Public School">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="className" class="form-label fw-semibold">Class</label>
                            <input type="text" class="form-control" id="className" placeholder="e.g. 8th">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="termName" class="form-label fw-semibold">Term / Exam</label>
                            <input type="text" class="form-control" id="termName" placeholder="e.g. First Term">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="yearName" class="form-label fw-semibold">Year</label>
                            <input type="text" class="form-control" id="yearName" value="2026">
                        </div>
                    </div>

                    <h5>Subjects &amp; marks</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light"><tr><th>Subject</th><th style="width:120px">Obtained</th><th style="width:120px">Total</th><th style="width:40px"></th></tr></thead>
                            <tbody id="subjectsBody"></tbody>
                        </table>
                    </div>
                    <button type="button" class="btn btn-outline-secondary btn-sm mb-3" id="addSubBtn">+ Add subject</button>

                    <div class="mb-3">
                        <label for="remarks" class="form-label fw-semibold">Teacher remarks (optional)</label>
                        <input type="text" class="form-control" id="remarks" placeholder="e.g. Excellent performance, keep it up!">
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Generate Report Card</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="border rounded p-4 bg-white" id="cardPreview"></div>
                        <button type="button" class="btn btn-success w-100 mt-3" id="printBtn">Print / Save as PDF</button>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Enter the student, school, class and term details.</li>
                <li>Enter the obtained and total marks for each subject.</li>
                <li>Press "Generate Report Card" — the percentage, grade and result will be calculated for you.</li>
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
    var subjectsBody = document.getElementById('subjectsBody');

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

    function addSubRow(name) {
        var tr = document.createElement('tr');
        tr.innerHTML =
            '<td><input type="text" class="form-control form-control-sm sub-name" value="' + esc(name || '') + '" placeholder="Subject name"></td>' +
            '<td><input type="number" class="form-control form-control-sm sub-obt" min="0" step="0.5" placeholder="0"></td>' +
            '<td><input type="number" class="form-control form-control-sm sub-tot" min="1" step="1" value="100"></td>' +
            '<td><button type="button" class="btn btn-sm btn-outline-danger rm-sub">&times;</button></td>';
        tr.querySelector('.rm-sub').addEventListener('click', function () { tr.remove(); });
        subjectsBody.appendChild(tr);
    }
    document.getElementById('addSubBtn').addEventListener('click', function () { addSubRow(''); });
    ['English', 'Urdu', 'Mathematics', 'Science', 'Islamiat'].forEach(addSubRow);

    function gradeOf(pct) {
        if (pct >= 90) return 'A+';
        if (pct >= 80) return 'A';
        if (pct >= 70) return 'B';
        if (pct >= 60) return 'C';
        if (pct >= 50) return 'D';
        if (pct >= 40) return 'E';
        return 'F';
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var student = document.getElementById('studentName').value.trim();
        var school = document.getElementById('schoolName').value.trim();
        var cls = document.getElementById('className').value.trim();
        var term = document.getElementById('termName').value.trim();
        var year = document.getElementById('yearName').value.trim();
        var remarks = document.getElementById('remarks').value.trim();

        if (!student) { showError('Please enter the student name.'); return; }

        var rows = subjectsBody.querySelectorAll('tr');
        var subs = [];
        for (var i = 0; i < rows.length; i++) {
            var sn = rows[i].querySelector('.sub-name').value.trim();
            var obt = parseFloat(rows[i].querySelector('.sub-obt').value);
            var tot = parseFloat(rows[i].querySelector('.sub-tot').value);
            if (!sn && isNaN(obt)) continue; // skip empty rows
            if (!sn) { showError('Please enter the name of every subject.'); return; }
            if (isNaN(obt) || obt < 0) { showError('Please enter the obtained marks for "' + sn + '".'); return; }
            if (isNaN(tot) || tot <= 0) { showError('Total marks for "' + sn + '" must be above zero.'); return; }
            if (obt > tot) { showError('Obtained marks for "' + sn + '" cannot be more than the total marks.'); return; }
            subs.push({ name: sn, obt: obt, tot: tot });
        }
        if (subs.length === 0) { showError('Please enter marks for at least one subject.'); return; }

        var tObt = 0, tTot = 0, failCount = 0, html = '';
        for (var j = 0; j < subs.length; j++) {
            var pct = subs[j].tot > 0 ? (subs[j].obt / subs[j].tot * 100) : 0;
            tObt += subs[j].obt; tTot += subs[j].tot;
            if (pct < 40) failCount++;
            html += '<tr><td>' + esc(subs[j].name) + '</td>' +
                '<td class="text-center">' + subs[j].obt + '</td>' +
                '<td class="text-center">' + subs[j].tot + '</td>' +
                '<td class="text-center">' + pct.toFixed(1) + '%</td>' +
                '<td class="text-center fw-bold">' + gradeOf(pct) + '</td></tr>';
        }
        var totalPct = tTot > 0 ? tObt / tTot * 100 : 0;
        var overall = gradeOf(totalPct);
        var result = failCount === 0 ? 'PASS' : 'FAIL';
        var resultClass = failCount === 0 ? 'text-success' : 'text-danger';

        var card =
            '<div class="text-center mb-3">' +
            (school ? '<h3 class="mb-0">' + esc(school) + '</h3>' : '<h3 class="mb-0">Report Card</h3>') +
            '<div class="text-muted">Student Progress Report</div></div>' +
            '<table class="table table-sm"><tbody>' +
            '<tr><th style="width:25%">Student</th><td>' + esc(student) + '</td><th style="width:25%">Class</th><td>' + esc(cls || '-') + '</td></tr>' +
            '<tr><th>Term</th><td>' + esc(term || '-') + '</td><th>Year</th><td>' + esc(year || '-') + '</td></tr>' +
            '</tbody></table>' +
            '<table class="table table-bordered"><thead class="table-light"><tr><th>Subject</th><th>Obtained</th><th>Total</th><th>%</th><th>Grade</th></tr></thead>' +
            '<tbody>' + html + '</tbody><tfoot class="table-light">' +
            '<tr><th>Total</th><th class="text-center">' + tObt + '</th><th class="text-center">' + tTot + '</th>' +
            '<th class="text-center">' + totalPct.toFixed(1) + '%</th><th class="text-center">' + overall + '</th></tr>' +
            '</tfoot></table>' +
            '<div class="row text-center my-3">' +
            '<div class="col-4"><div class="small text-muted">Percentage</div><div class="fw-bold fs-5">' + totalPct.toFixed(1) + '%</div></div>' +
            '<div class="col-4"><div class="small text-muted">Overall Grade</div><div class="fw-bold fs-5">' + overall + '</div></div>' +
            '<div class="col-4"><div class="small text-muted">Result</div><div class="fw-bold fs-5 ' + resultClass + '">' + result + '</div></div>' +
            '</div>' +
            (remarks ? '<p><strong>Remarks:</strong> ' + esc(remarks) + '</p>' : '') +
            '<div class="row mt-5"><div class="col-6 text-center"><br>__________________<br>Class Teacher</div>' +
            '<div class="col-6 text-center"><br>__________________<br>Principal</div></div>';

        document.getElementById('cardPreview').innerHTML = card;
        results.classList.remove('d-none');
    });

    document.getElementById('printBtn').addEventListener('click', function () {
        var w = window.open('', '_blank');
        w.document.write('<!DOCTYPE html><html><head><title>Report Card</title>' +
            '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"></head>' +
            '<body><div class="container py-4">' + document.getElementById('cardPreview').innerHTML + '</div>' +
            '<script>window.onload=function(){window.print();}<\/script></body></html>');
        w.document.close();
    });
})();
</script>
@endsection
