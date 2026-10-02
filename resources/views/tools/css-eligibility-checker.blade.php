@extends('layouts.app')

@section('title', 'CSS Eligibility Checker - Azlaan Tools')
@section('meta_description', 'Check if you are eligible for the CSS exam based on age, degree and attempts. Free online.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">CSS Eligibility Checker</h1>
            <p class="lead text-muted">Enter your date of birth, degree and past attempts — the tool will tell you if you are eligible for the CSS exam based on FPSC's basic rules.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="dob" class="form-label fw-semibold">Date of Birth</label>
                        <input type="date" class="form-control" id="dob">
                        <div class="form-text">Age is calculated up to 31 December (last year), as per FPSC's cut-off rule.</div>
                    </div>
                    <div class="mb-3">
                        <label for="degree" class="form-label fw-semibold">Highest Degree</label>
                        <select class="form-select" id="degree">
                            <option value="below">12th / Intermediate or less</option>
                            <option value="bachelor" selected>Bachelor (14 years of study, BA/BSc/B.Com etc.)</option>
                            <option value="master">Master / 16 years of study (MA/MSc/MBA etc.)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="attempts" class="form-label fw-semibold">How many times have you taken the CSS exam before?</label>
                        <select class="form-select" id="attempts">
                            <option value="0" selected>0 (first time)</option>
                            <option value="1">1 time</option>
                            <option value="2">2 times</option>
                            <option value="3">3 or more times</option>
                        </select>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="govtEmp">
                        <label class="form-check-label" for="govtEmp">I am a government employee / in an age-relaxation category</label>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Check Eligibility</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="alert" id="verdictBox" role="alert"></div>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light"><tr><th>Condition</th><th>Your position</th><th>Required</th><th>Status</th></tr></thead>
                                <tbody id="checksBody"></tbody>
                            </table>
                        </div>
                        <p class="text-muted small">This tool gives an estimate based on basic rules. Rules can change over time — be sure to read the latest advertisement on <a href="https://www.fpsc.gov.pk" target="_blank" rel="noopener">fpsc.gov.pk</a> for final confirmation.</p>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select your date of birth, degree and past attempts.</li>
                <li>If you are a government employee or in a relaxation category, check the box.</li>
                <li>Press "Check Eligibility" — each condition's result will appear in the table.</li>
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
    var verdictBox = document.getElementById('verdictBox');
    var checksBody = document.getElementById('checksBody');

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }

    function addRow(label, yours, required, ok) {
        var tr = document.createElement('tr');
        var td0 = document.createElement('td'); td0.textContent = label;
        var td1 = document.createElement('td'); td1.textContent = yours;
        var td2 = document.createElement('td'); td2.textContent = required;
        var td3 = document.createElement('td');
        var badge = document.createElement('span');
        badge.className = 'badge ' + (ok ? 'bg-success' : 'bg-danger');
        badge.textContent = ok ? 'Pass' : 'Fail';
        td3.appendChild(badge);
        tr.appendChild(td0); tr.appendChild(td1); tr.appendChild(td2); tr.appendChild(td3);
        checksBody.appendChild(tr);
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var dobStr = document.getElementById('dob').value;
        if (!dobStr) { showError('Please enter your Date of Birth first.'); return; }
        var dob = new Date(dobStr + 'T00:00:00');
        if (isNaN(dob.getTime())) { showError('The Date of Birth is not valid.'); return; }

        var now = new Date();
        var cutoffYear = now.getFullYear() - 1;
        var cutoff = new Date(cutoffYear, 11, 31);
        var age = cutoffYear - dob.getFullYear();
        var birthdayThisYear = new Date(cutoffYear, dob.getMonth(), dob.getDate());
        if (cutoff < birthdayThisYear) { age--; }
        if (age < 0 || age > 100) { showError('The Date of Birth looks wrong, please check it again.'); return; }

        var degree = document.getElementById('degree').value;
        var attempts = parseInt(document.getElementById('attempts').value, 10);
        var relaxed = document.getElementById('govtEmp').checked;

        var minAge = 21;
        var maxAge = relaxed ? 32 : 30;
        var ageOk = age >= minAge && age <= maxAge;
        var degreeOk = degree !== 'below';
        var attemptsOk = attempts < 3;

        var degreeLabel = degree === 'below' ? 'Intermediate or less' : (degree === 'bachelor' ? 'Bachelor (14 years)' : 'Master (16 years)');
        var attemptsLabel = attempts === 0 ? 'No attempts yet' : attempts + (attempts === 1 ? ' time' : attempts === 2 ? ' times' : ' or more');

        checksBody.innerHTML = '';
        addRow('Age (by 31 Dec ' + cutoffYear + ')', age + ' years', minAge + '–' + maxAge + ' years', ageOk);
        addRow('Degree', degreeLabel, 'At least Bachelor (14 years)', degreeOk);
        addRow('Attempts', attemptsLabel, 'Fewer than 3 attempts', attemptsOk);

        var allOk = ageOk && degreeOk && attemptsOk;
        verdictBox.className = 'alert ' + (allOk ? 'alert-success' : 'alert-warning');
        if (allOk) {
            verdictBox.textContent = 'Congratulations! Based on the basic rules, you look eligible for the CSS exam. Be sure to check the latest advertisement on fpsc.gov.pk.';
        } else {
            var reasons = [];
            if (!ageOk) { reasons.push('age limit (' + minAge + '–' + maxAge + ' years) is not met'); }
            if (!degreeOk) { reasons.push('at least a Bachelor degree is required'); }
            if (!attemptsOk) { reasons.push('the 3-attempt limit is already reached'); }
            verdictBox.textContent = 'Sorry! You do not look eligible right now — reason: ' + reasons.join('; ') + '. Confirm the final details on fpsc.gov.pk.';
        }
        results.classList.remove('d-none');
    });
})();
</script>
@endsection
