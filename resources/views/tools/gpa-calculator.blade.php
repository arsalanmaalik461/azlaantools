@extends('layouts.app')

@section('title', 'GPA & CGPA Calculator (HEC Pakistan) — Azlaan Tools')
@section('meta_description', 'Free GPA and CGPA calculator for Pakistani university students using the HEC grade scale. Add subjects, credit hours and grades to calculate semester GPA and combined CGPA.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <h1 class="mb-2">GPA &amp; CGPA Calculator</h1>
            <p class="text-muted mb-4">Calculate your semester GPA using the HEC Pakistan grade scale — and optionally combine it with your previous CGPA to get a new combined CGPA.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 card-title mb-3">Semester GPA</h2>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle" id="gpaTable">
                            <thead>
                                <tr>
                                    <th>Subject (optional)</th>
                                    <th style="width:140px">Credit Hours</th>
                                    <th style="width:140px">Grade</th>
                                    <th style="width:50px"></th>
                                </tr>
                            </thead>
                            <tbody id="gpaBody"></tbody>
                        </table>
                    </div>
                    <button type="button" class="btn btn-outline-primary btn-sm" id="addRowBtn">+ Add Subject</button>
                    <button type="button" class="btn btn-primary btn-sm ms-2" id="calcGpaBtn">Calculate GPA</button>

                    <div class="row g-3 mt-3">
                        <div class="col-md-4"><div class="border rounded p-3 bg-light text-center"><div class="text-muted small">Semester GPA</div><div class="fs-3 fw-bold" id="gpaOut">—</div></div></div>
                        <div class="col-md-4"><div class="border rounded p-3 bg-light text-center"><div class="text-muted small">Total Credit Hours</div><div class="fs-3 fw-bold" id="hoursOut">—</div></div></div>
                        <div class="col-md-4"><div class="border rounded p-3 bg-light text-center"><div class="text-muted small">Quality Points</div><div class="fs-3 fw-bold" id="qpOut">—</div></div></div>
                    </div>
                    <p class="small text-muted mt-3 mb-0" id="pctNote">Percentage-equivalent estimate: GPA &times; 25 (rough estimate only — for example 3.2 GPA &asymp; 80%). This is only a rough estimate; every university has its own official conversion formula.</p>
                    <div class="alert alert-info mt-2 mb-0 d-none" id="pctOut"></div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 card-title mb-3">CGPA Mode — Combined CGPA</h2>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="prevCgpa" class="form-label">Previous CGPA</label>
                            <input type="number" class="form-control" id="prevCgpa" step="0.01" min="0" max="4" placeholder="e.g. 3.10">
                        </div>
                        <div class="col-md-6">
                            <label for="prevHours" class="form-label">Previous Total Credit Hours</label>
                            <input type="number" class="form-control" id="prevHours" step="any" min="0" placeholder="e.g. 60">
                        </div>
                    </div>
                    <p class="small text-muted mt-2">First enter this semester's subjects in the Semester GPA card above — this semester's GPA and hours will be used automatically.</p>
                    <button type="button" class="btn btn-primary btn-sm" id="calcCgpaBtn">Calculate Combined CGPA</button>
                    <div class="border rounded p-3 bg-light text-center mt-3">
                        <div class="text-muted small">Combined CGPA</div>
                        <div class="fs-3 fw-bold" id="cgpaOut">—</div>
                        <div class="small text-muted" id="cgpaDetail"></div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Add one row per subject — the subject name is optional.</li>
                        <li>Enter credit hours and select the grade from the dropdown (HEC scale: A+ / A = 4.0, A- = 3.7, B+ = 3.3, etc.).</li>
                        <li>Press &quot;Calculate GPA&quot; — your GPA, total credit hours and quality points will appear.</li>
                        <li>For combined CGPA, enter your previous CGPA and previous credit hours below, then press &quot;Calculate Combined CGPA&quot;.</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
var grades = [
    ['A+', 4.0], ['A', 4.0], ['A-', 3.7],
    ['B+', 3.3], ['B', 3.0], ['B-', 2.7],
    ['C+', 2.3], ['C', 2.0], ['C-', 1.7],
    ['D+', 1.3], ['D', 1.0], ['F', 0.0]
];
var gradeOptions = grades.map(function(g){ return '<option value="' + g[1] + '">' + g[0] + ' (' + g[1].toFixed(1) + ')</option>'; }).join('');

function addRow() {
    var tr = document.createElement('tr');
    tr.innerHTML = '<td><input type="text" class="form-control form-control-sm subject" placeholder="Subject name"></td>' +
        '<td><input type="number" class="form-control form-control-sm hours" min="0" step="any" placeholder="3"></td>' +
        '<td><select class="form-select form-select-sm grade">' + gradeOptions + '</select></td>' +
        '<td><button type="button" class="btn btn-outline-danger btn-sm remove-btn">&times;</button></td>';
    tr.querySelector('.remove-btn').addEventListener('click', function(){ tr.remove(); });
    document.getElementById('gpaBody').appendChild(tr);
}
function semesterTotals() {
    var rows = document.querySelectorAll('#gpaBody tr');
    var totalHours = 0, totalQP = 0;
    rows.forEach(function(tr){
        var h = parseFloat(tr.querySelector('.hours').value) || 0;
        var p = parseFloat(tr.querySelector('.grade').value) || 0;
        if (h > 0) { totalHours += h; totalQP += h * p; }
    });
    return { hours: totalHours, qp: totalQP, gpa: totalHours > 0 ? totalQP / totalHours : 0 };
}
document.getElementById('addRowBtn').addEventListener('click', addRow);
document.getElementById('calcGpaBtn').addEventListener('click', function(){
    var t = semesterTotals();
    document.getElementById('gpaOut').textContent = t.hours > 0 ? t.gpa.toFixed(2) : '—';
    document.getElementById('hoursOut').textContent = t.hours > 0 ? t.hours.toLocaleString('en-PK') : '—';
    document.getElementById('qpOut').textContent = t.hours > 0 ? t.qp.toFixed(2) : '—';
    var box = document.getElementById('pctOut');
    if (t.hours > 0) {
        box.classList.remove('d-none');
        box.textContent = 'Rough percentage equivalent: ' + (t.gpa * 25).toFixed(1) + '% (GPA x 25 — rough estimate only, not official).';
    } else { box.classList.add('d-none'); }
});
document.getElementById('calcCgpaBtn').addEventListener('click', function(){
    var prevCgpa = parseFloat(document.getElementById('prevCgpa').value) || 0;
    var prevHours = parseFloat(document.getElementById('prevHours').value) || 0;
    var t = semesterTotals();
    var totalHours = prevHours + t.hours;
    var out = document.getElementById('cgpaOut'), detail = document.getElementById('cgpaDetail');
    if (totalHours <= 0 || t.hours <= 0) { out.textContent = '—'; detail.textContent = 'First enter semester subjects and previous CGPA/hours.'; return; }
    if (prevCgpa < 0 || prevCgpa > 4) { out.textContent = '—'; detail.textContent = 'Previous CGPA must be between 0 and 4.0.'; return; }
    var combined = ((prevCgpa * prevHours) + t.qp) / totalHours;
    out.textContent = combined.toFixed(2);
    detail.textContent = 'Total credit hours: ' + totalHours.toLocaleString('en-PK') + ' | This semester GPA: ' + t.gpa.toFixed(2);
});
addRow(); addRow(); addRow();
</script>
@endsection
