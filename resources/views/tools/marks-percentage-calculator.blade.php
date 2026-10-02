@extends('layouts.app')

@section('title', 'Marks Percentage Calculator - Subject Wise Result | Azlaan Tools')
@section('meta_description', 'Free marks percentage calculator: add subjects with obtained and total marks to get overall percentage, per-subject percentage and grade instantly. No signup needed.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="mb-3">Marks Percentage Calculator</h1>
            <p class="lead text-muted">Enter obtained and total marks for each subject — overall percentage, per-subject percentage and grade are calculated live.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm align-middle">
                            <thead><tr><th>Subject (optional)</th><th style="width:150px">Obtained Marks</th><th style="width:150px">Total Marks</th><th style="width:110px">Subject %</th><th style="width:50px"></th></tr></thead>
                            <tbody id="marksBody"></tbody>
                        </table>
                    </div>
                    <button type="button" class="btn btn-outline-primary btn-sm" id="addRowBtn">+ Add Subject</button>
                    <div class="alert alert-warning mt-3 d-none" id="marksMsg"></div>

                    <div class="row g-3 mt-3">
                        <div class="col-md-3"><div class="border rounded p-3 bg-light text-center"><div class="text-muted small">Total Obtained</div><div class="fs-4 fw-bold" id="totalObtOut">—</div></div></div>
                        <div class="col-md-3"><div class="border rounded p-3 bg-light text-center"><div class="text-muted small">Total Marks</div><div class="fs-4 fw-bold" id="totalMarksOut">—</div></div></div>
                        <div class="col-md-3"><div class="border rounded p-3 bg-light text-center"><div class="text-muted small">Overall Percentage</div><div class="fs-4 fw-bold text-primary" id="overallPctOut">—</div></div></div>
                        <div class="col-md-3"><div class="border rounded p-3 bg-light text-center"><div class="text-muted small">Grade (common scale)</div><div class="fs-4 fw-bold text-success" id="gradeOut">—</div></div></div>
                    </div>
                    <p class="small text-muted mt-3 mb-0">Grade scale (common scale — each board/university may use a different scale): A+ 90%+, A 80%+, B 70%+, C 60%+, D 50%+, F below 50%.</p>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Keep one row for each subject — the subject name is optional.</li>
                        <li>Enter obtained and total marks; the subject percentage appears on its own.</li>
                        <li>Use + Add Subject to add more rows if needed, or the cross button to remove a row.</li>
                        <li>Total obtained, total marks, overall percentage and grade below update live.</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    var body = document.getElementById('marksBody');
    function gradeFor(pct) {
        if (pct >= 90) return 'A+';
        if (pct >= 80) return 'A';
        if (pct >= 70) return 'B';
        if (pct >= 60) return 'C';
        if (pct >= 50) return 'D';
        return 'F';
    }
    function calculate() {
        var rows = body.querySelectorAll('tr');
        var totalObt = 0, totalMarks = 0, hasError = false, hasData = false;
        rows.forEach(function (tr) {
            var obtEl = tr.querySelector('.obtained');
            var totEl = tr.querySelector('.total');
            var pctEl = tr.querySelector('.subject-pct');
            var obt = parseFloat(obtEl.value);
            var tot = parseFloat(totEl.value);
            if (isNaN(obt) && isNaN(tot)) { pctEl.textContent = '—'; return; }
            if (isNaN(obt) || isNaN(tot) || tot <= 0 || obt < 0 || obt > tot) { pctEl.textContent = 'Invalid'; hasError = true; return; }
            hasData = true;
            totalObt += obt; totalMarks += tot;
            pctEl.textContent = ((obt / tot) * 100).toFixed(2) + '% (' + gradeFor((obt / tot) * 100) + ')';
        });
        var msg = document.getElementById('marksMsg');
        if (hasError) { msg.textContent = 'Some rows have errors: obtained marks cannot be more than total, and total must be more than 0.'; msg.classList.remove('d-none'); }
        else { msg.classList.add('d-none'); }
        if (hasData && totalMarks > 0) {
            var overall = (totalObt / totalMarks) * 100;
            document.getElementById('totalObtOut').textContent = totalObt.toLocaleString('en-PK');
            document.getElementById('totalMarksOut').textContent = totalMarks.toLocaleString('en-PK');
            document.getElementById('overallPctOut').textContent = overall.toFixed(2) + '%';
            document.getElementById('gradeOut').textContent = gradeFor(overall);
        } else {
            document.getElementById('totalObtOut').textContent = '—'; document.getElementById('totalMarksOut').textContent = '—'; document.getElementById('overallPctOut').textContent = '—'; document.getElementById('gradeOut').textContent = '—';
        }
    }
    function addRow() {
        var tr = document.createElement('tr');
        tr.innerHTML = '<td><input type="text" class="form-control form-control-sm subj-name" placeholder="e.g. Mathematics"></td>' +
            '<td><input type="number" class="form-control form-control-sm obtained" min="0" step="any" placeholder="85"></td>' +
            '<td><input type="number" class="form-control form-control-sm total" min="0" step="any" placeholder="100"></td>' +
            '<td class="subject-pct fw-semibold">—</td>' +
            '<td><button type="button" class="btn btn-outline-danger btn-sm remove-btn">&times;</button></td>';
        tr.querySelector('.remove-btn').addEventListener('click', function () { tr.remove(); calculate(); });
        tr.querySelector('.obtained').addEventListener('input', calculate);
        tr.querySelector('.total').addEventListener('input', calculate);
        body.appendChild(tr);
    }
    document.getElementById('addRowBtn').addEventListener('click', function () { addRow(); });
    addRow(); addRow(); addRow(); addRow(); addRow();
    calculate();
})();
</script>
@endsection
