@extends('layouts.app')
@section('title', 'Pakistan Child Vaccination Schedule - EPI Vaccine Chart | Azlaan Tools')
@section('meta_description', 'Pakistan EPI immunization schedule for children from birth to 15 months. Enter your child date of birth to get exact vaccine due dates — free and private.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Pakistan Child Vaccination Schedule</h1>
            <p class="lead text-muted">Vaccination schedule for children as per EPI Pakistan — from birth to 15 months. Enter your child date of birth and get the exact due date of each vaccine.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-6">
                            <label for="dobInput" class="form-label fw-semibold">Child date of birth</label>
                            <input type="date" class="form-control" id="dobInput">
                            <div class="form-text">Entering the date will calculate the due date of each vaccine.</div>
                        </div>
                        <div class="col-md-6">
                            <button type="button" class="btn btn-primary w-100" id="goBtn">Calculate Due Dates</button>
                        </div>
                    </div>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <h2 class="h5 mb-3">Vaccine Due Dates — <span id="dobLabel"></span></h2>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Visit</th>
                                        <th>Due Date</th>
                                        <th>Vaccines</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="schedBody"></tbody>
                            </table>
                        </div>
                        <p class="text-muted small mt-2">Status: a vaccine can be given on or after that date. Get any missed vaccines done right away at your nearest EPI center.</p>
                    </div>
                </div>
            </div>

            <h2>Full EPI Pakistan Schedule (Birth – 15 Months)</h2>
            <div class="table-responsive mb-4">
                <table class="table table-striped table-bordered align-middle">
                    <thead class="table-light">
                        <tr><th>Age</th><th>Vaccines</th><th>Protects Against</th></tr>
                    </thead>
                    <tbody>
                        <tr><td><strong>At Birth</strong></td><td>BCG, OPV-0, Hepatitis B (birth dose)</td><td>TB, Polio, Hepatitis B</td></tr>
                        <tr><td><strong>6 Weeks</strong></td><td>Pentavalent-1, OPV-1, PCV-1, Rotavirus-1</td><td>Diphtheria, Pertussis, Tetanus, Hep B, Hib, Polio, Pneumonia, Rotavirus</td></tr>
                        <tr><td><strong>10 Weeks</strong></td><td>Pentavalent-2, OPV-2, PCV-2, Rotavirus-2</td><td>Same diseases — booster doses</td></tr>
                        <tr><td><strong>14 Weeks</strong></td><td>Pentavalent-3, OPV-3, PCV-3, IPV-1</td><td>Full protection + inactivated polio</td></tr>
                        <tr><td><strong>9 Months</strong></td><td>Measles-1 (MR)</td><td>Measles, Rubella</td></tr>
                        <tr><td><strong>15 Months</strong></td><td>Measles-2 (MR)</td><td>Long-term measles &amp; rubella immunity</td></tr>
                    </tbody>
                </table>
            </div>

            <div class="alert alert-success">
                <strong>Free vaccines:</strong> All these vaccines are given completely free at government hospitals and EPI centers in Pakistan. Official info: <a href="https://epi.gov.pk" target="_blank" rel="noopener">epi.gov.pk</a> — Sehat Tahaffuz Helpline: <strong>1166</strong>.
            </div>
            <div class="alert alert-warning">
                <strong>Note:</strong> This schedule follows the standard EPI Pakistan schedule. Always consult a doctor about your child health — this page is not a substitute for medical advice.
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select your child date of birth.</li>
                <li>Press <strong>Calculate Due Dates</strong>.</li>
                <li>See the due date and status (upcoming / due / missed) of each vaccine.</li>
                <li>Get missed vaccines done right away at your nearest EPI center or government hospital — or call 1166.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var dobInput = document.getElementById('dobInput');
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var schedBody = document.getElementById('schedBody');
    var dobLabel = document.getElementById('dobLabel');

    var visits = [
        { age: 'At Birth', addDays: 0, vaccines: 'BCG, OPV-0, Hepatitis B (birth dose)', note: 'TB, Polio, Hepatitis B' },
        { age: '6 Weeks', addDays: 42, vaccines: 'Pentavalent-1, OPV-1, PCV-1, Rotavirus-1', note: 'Diphtheria, Pertussis, Tetanus, Hep B, Hib, Polio, Pneumonia' },
        { age: '10 Weeks', addDays: 70, vaccines: 'Pentavalent-2, OPV-2, PCV-2, Rotavirus-2', note: 'Booster doses' },
        { age: '14 Weeks', addDays: 98, vaccines: 'Pentavalent-3, OPV-3, PCV-3, IPV-1', note: 'Full protection + inactivated polio' },
        { age: '9 Months', addMonths: 9, vaccines: 'Measles-1 (MR)', note: 'Measles, Rubella' },
        { age: '15 Months', addMonths: 15, vaccines: 'Measles-2 (MR)', note: 'Long-term immunity' }
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
    function addMonths(date, m) {
        var d = new Date(date.getTime());
        d.setMonth(d.getMonth() + m);
        return d;
    }
    function fmtDate(d) {
        return d.toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' });
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var val = dobInput.value;
        if (!val) { showError('Please select the date of birth.'); return; }
        var dob = new Date(val + 'T00:00:00');
        if (isNaN(dob.getTime())) { showError('Invalid date.'); return; }
        var today = new Date();
        today.setHours(0, 0, 0, 0);
        if (dob > today) { showError('Date of birth cannot be in the future.'); return; }

        dobLabel.textContent = fmtDate(dob);
        schedBody.innerHTML = '';

        visits.forEach(function (v) {
            var due = v.addDays !== undefined
                ? new Date(dob.getTime() + v.addDays * 86400000)
                : addMonths(dob, v.addMonths);
            var diffDays = Math.round((today - due) / 86400000);
            var badge, cls;
            if (diffDays < -7) { badge = 'Upcoming'; cls = 'bg-secondary'; }
            else if (diffDays <= 7) { badge = 'Due Now'; cls = 'bg-warning text-dark'; }
            else if (diffDays <= 30) { badge = 'Overdue - get the vaccine now'; cls = 'bg-danger'; }
            else { badge = 'Missed - get it done now'; cls = 'bg-danger'; }

            var tr = document.createElement('tr');
            tr.innerHTML = '<td><strong></strong></td><td></td><td></td><td><span class="badge ' + cls + '"></span></td>';
            tr.children[0].querySelector('strong').textContent = v.age;
            tr.children[1].textContent = fmtDate(due);
            tr.children[2].innerHTML = '';
            var b = document.createElement('strong');
            b.textContent = v.vaccines;
            var small = document.createElement('div');
            small.className = 'small text-muted';
            small.textContent = v.note;
            tr.children[2].appendChild(b);
            tr.children[2].appendChild(small);
            tr.children[3].querySelector('span').textContent = badge;
            schedBody.appendChild(tr);
        });

        results.classList.remove('d-none');
    });
})();
</script>
@endsection
