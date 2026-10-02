@extends('layouts.app')

@section('title', 'Target GPA Calculator - Azlaan Tools')
@section('meta_description', 'Find out what grades you need to reach your target GPA - free online GPA planner for students.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Target GPA Calculator</h1>
            <p class="lead text-muted">What grades do you need in your remaining semesters to reach your target GPA? Work out the required average.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="curGpa" class="form-label fw-semibold">Current CGPA</label>
                            <input type="number" class="form-control" id="curGpa" placeholder="Example: 2.8" step="0.01" min="0" max="4">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="doneCredits" class="form-label fw-semibold">Completed credit hours</label>
                            <input type="number" class="form-control" id="doneCredits" placeholder="Example: 60" step="1" min="0">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="targetGpa" class="form-label fw-semibold">Target GPA</label>
                            <input type="number" class="form-control" id="targetGpa" placeholder="Example: 3.2" step="0.01" min="0" max="4">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="leftCredits" class="form-label fw-semibold">Remaining credit hours</label>
                            <input type="number" class="form-control" id="leftCredits" placeholder="Example: 70" step="1" min="1">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="leftCourses" class="form-label fw-semibold">Number of remaining courses <span class="text-muted fw-normal">(optional - for the grade plan)</span></label>
                        <input type="number" class="form-control" id="leftCourses" placeholder="Example: 20" step="1" min="1">
                        <div class="form-text">If you fill this in, we will show how many A, B, and so on you need.</div>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Calculate</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="alert" id="verdictBox"></div>
                        <div class="table-responsive mb-3">
                            <table class="table table-striped">
                                <tbody id="resBody"></tbody>
                            </table>
                        </div>
                        <div id="gradePlanWrap" class="d-none">
                            <h5>Grade Plan (for remaining courses)</h5>
                            <div class="table-responsive">
                                <table class="table table-striped table-sm">
                                    <thead class="table-light"><tr><th>Grade</th><th>Points</th><th>How many courses</th></tr></thead>
                                    <tbody id="gradePlanBody"></tbody>
                                </table>
                            </div>
                            <p class="text-muted small">This is one possible combination — we split the grades from A=4.0 downward until the required average is met.</p>
                        </div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Enter your current CGPA and completed credit hours.</li>
                <li>Enter the target GPA and remaining credit hours.</li>
                <li><strong>Calculate</strong> — you will get the required average and a grade plan.</li>
            </ol>
            <p class="text-muted small">This calculator uses the 4.0 scale and assumes all courses have equal credit hours. Be sure to match it with your university's grading rules.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var curGpa = document.getElementById('curGpa');
    var doneCredits = document.getElementById('doneCredits');
    var targetGpa = document.getElementById('targetGpa');
    var leftCredits = document.getElementById('leftCredits');
    var leftCourses = document.getElementById('leftCourses');
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var verdictBox = document.getElementById('verdictBox');
    var resBody = document.getElementById('resBody');
    var gradePlanWrap = document.getElementById('gradePlanWrap');
    var gradePlanBody = document.getElementById('gradePlanBody');

    var GRADES = [
        { g: 'A', p: 4.0 }, { g: 'A-', p: 3.7 }, { g: 'B+', p: 3.3 }, { g: 'B', p: 3.0 },
        { g: 'B-', p: 2.7 }, { g: 'C+', p: 2.3 }, { g: 'C', p: 2.0 }, { g: 'C-', p: 1.7 },
        { g: 'D+', p: 1.3 }, { g: 'D', p: 1.0 }, { g: 'F', p: 0.0 }
    ];

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function num(el) {
        var v = parseFloat(el.value);
        return isNaN(v) ? null : v;
    }
    function addRow(k, v) {
        var tr = document.createElement('tr');
        var th = document.createElement('th'); th.textContent = k; th.style.width = '55%';
        var td = document.createElement('td'); td.textContent = v;
        tr.appendChild(th); tr.appendChild(td);
        resBody.appendChild(tr);
    }
    function gradeFor(avg) {
        for (var i = 0; i < GRADES.length; i++) {
            if (avg >= GRADES[i].p - 0.001) { return GRADES[i]; }
        }
        return GRADES[GRADES.length - 1];
    }

    goBtn.addEventListener('click', function () {
        hideError();
        results.classList.add('d-none');
        gradePlanWrap.classList.add('d-none');
        resBody.innerHTML = '';
        gradePlanBody.innerHTML = '';

        var cg = num(curGpa), done = num(doneCredits), tg = num(targetGpa), left = num(leftCredits);
        if (cg === null || cg < 0 || cg > 4) { showError('Enter your current CGPA between 0 and 4.'); return; }
        if (done === null || done < 0) { showError('Enter your completed credit hours correctly.'); return; }
        if (tg === null || tg < 0 || tg > 4) { showError('Enter the target GPA between 0 and 4.'); return; }
        if (left === null || left <= 0) { showError('Enter remaining credit hours greater than 0.'); return; }

        var totalCredits = done + left;
        var currentPoints = cg * done;
        var neededTotal = tg * totalCredits;
        var neededPoints = neededTotal - currentPoints;
        var requiredAvg = neededPoints / left;

        addRow('Current grade points', currentPoints.toFixed(2));
        addRow('Total points needed for the target', neededTotal.toFixed(2));
        addRow('Needed in remaining courses', neededPoints.toFixed(2) + ' points');
        addRow('Required average (in remaining)', requiredAvg.toFixed(2));

        verdictBox.className = 'alert';
        if (requiredAvg > 4.0) {
            verdictBox.classList.add('alert-danger');
            verdictBox.textContent = 'Hard: this target needs an average of ' + requiredAvg.toFixed(2) + ', which is above 4.0 — the target is not possible with this plan. Lower the target or take more credits.';
        } else if (requiredAvg <= 0) {
            verdictBox.classList.add('alert-success');
            verdictBox.textContent = 'Well done! You have already reached the target — just avoid failing in your remaining courses.';
        } else {
            var g = gradeFor(requiredAvg);
            verdictBox.classList.add('alert-success');
            var note = requiredAvg >= 3.9 ? ' (you need almost all A grades — hard work needed)' : '';
            verdictBox.textContent = 'Possible! You need an average of ' + requiredAvg.toFixed(2) + ' in your remaining credit hours — that means mostly grade ' + g.g + note + '.';
        }
        results.classList.remove('d-none');

        // Grade plan across remaining courses: exact two-grade mix around the required average
        var coursesRaw = leftCourses.value.trim();
        if (coursesRaw !== '' && requiredAvg > 0 && requiredAvg <= 4.0) {
            var courses = parseInt(coursesRaw, 10);
            if (!isNaN(courses) && courses > 0 && courses <= 200) {
                var hi = GRADES[0], lo = GRADES[GRADES.length - 1];
                for (var i = 0; i < GRADES.length - 1; i++) {
                    if (requiredAvg <= GRADES[i].p && requiredAvg >= GRADES[i + 1].p) {
                        hi = GRADES[i]; lo = GRADES[i + 1]; break;
                    }
                }
                var n1 = courses, n2 = 0;
                if (hi.p !== lo.p) {
                    n1 = Math.ceil(courses * (requiredAvg - lo.p) / (hi.p - lo.p));
                    if (n1 < 0) { n1 = 0; }
                    if (n1 > courses) { n1 = courses; }
                    n2 = courses - n1;
                }
                var achieved = (n1 * hi.p + n2 * lo.p) / courses;
                var planRows = [];
                if (n1 > 0) { planRows.push([hi.g, hi.p.toFixed(1), n1 + ' courses']); }
                if (n2 > 0) { planRows.push([lo.g, lo.p.toFixed(1), n2 + ' courses']); }
                planRows.forEach(function (r) {
                    var tr = document.createElement('tr');
                    r.forEach(function (v) {
                        var td = document.createElement('td'); td.textContent = v; tr.appendChild(td);
                    });
                    gradePlanBody.appendChild(tr);
                });
                var tr2 = document.createElement('tr');
                var td2 = document.createElement('td');
                td2.colSpan = 3; td2.className = 'text-muted small';
                td2.textContent = 'This combination gives an average of ' + achieved.toFixed(2) + ' (required: ' + requiredAvg.toFixed(2) + ').';
                tr2.appendChild(td2);
                gradePlanBody.appendChild(tr2);
                gradePlanWrap.classList.remove('d-none');
            }
        }
    });
})();
</script>
@endsection
