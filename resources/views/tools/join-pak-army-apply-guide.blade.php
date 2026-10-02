@extends('layouts.app')

@section('title', 'Join Pak Army Apply Guide - Azlaan Tools')
@section('meta_description', 'Complete guide for applying online in the Pak Army: eligibility check, test and selection process — free.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Join Pak Army Apply Guide</h1>
            <p class="lead text-muted">Understand the full process of applying to the Pak Army: choose your category below and check your eligibility. This guide is for information only — the final decision is made by the Army Selection Center.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="catSelect" class="form-label fw-semibold">In which category do you want to apply?</label>
                        <select class="form-select" id="catSelect">
                            <option value="">-- Choose a category --</option>
                            <option value="pma">PMA Long Course (Officer)</option>
                            <option value="tcc">Technical Cadet Course (TCC)</option>
                            <option value="amc">AMC Cadet (Medical)</option>
                            <option value="soldier">Soldier / Sipahi</option>
                            <option value="clerk">Clerk / Cook / Driver (Technical Trades)</option>
                            <option value="lady">Lady Cadet Course (LCC)</option>
                        </select>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="ageInput" class="form-label fw-semibold">Age (years)</label>
                            <input type="number" class="form-control" id="ageInput" min="15" max="35" placeholder="for example 19">
                        </div>
                        <div class="col-md-6">
                            <label for="eduSelect" class="form-label fw-semibold">Education</label>
                            <select class="form-select" id="eduSelect">
                                <option value="matric">Matric</option>
                                <option value="inter">Intermediate (FA/FSc)</option>
                                <option value="grad">Graduation / Bachelor</option>
                                <option value="masters">Masters / MBBS / Equivalent</option>
                            </select>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100 mt-3" id="goBtn">Check Eligibility</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4"></div>
                </div>
            </div>

            <h2>How to Apply Online (Steps)</h2>
            <ol>
                <li><strong>Open the official website:</strong> <a href="https://www.joinpakarmy.gov.pk" target="_blank" rel="noopener">www.joinpakarmy.gov.pk</a> — this is the official Pak Army recruitment website. Registration happens here.</li>
                <li><strong>Register:</strong> select your category (PMA, Soldier, TCC, etc.), fill the online form, and get your roll number slip.</li>
                <li><strong>AS&amp;RC visit:</strong> go to the nearest Army Selection and Recruitment Center (AS&amp;RC) for the test and interview — the list of centers is on the official website.</li>
                <li><strong>Initial tests:</strong> verbal/non-verbal intelligence test, academic test, and physical test.</li>
                <li><strong>Medical + ISSB:</strong> after clearing the initial tests, a medical checkup, then for officer entries the ISSB (Inter Services Selection Board) test.</li>
                <li><strong>Final selection:</strong> the merit list is announced on the official website.</li>
            </ol>
            <div class="alert alert-warning">
                <strong>Important note:</strong> This tool gives general information only; it does not do live verification or registration. ALWAYS apply on the official website <a href="https://www.joinpakarmy.gov.pk" target="_blank" rel="noopener">joinpakarmy.gov.pk</a> — do not give money to any agent or fake website. Age and education limits can change over time, so read the official advertisement.
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select your category, age, and education.</li>
                <li>Click "Check Eligibility".</li>
                <li>Read the basic requirements and next steps in the result.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var catSelect = document.getElementById('catSelect');
    var ageInput = document.getElementById('ageInput');
    var eduSelect = document.getElementById('eduSelect');
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');

    // General guidance (approximate, changes per advertisement)
    var cats = {
        pma:     { label: 'PMA Long Course', ageMin: 17, ageMax: 23, edu: ['inter', 'grad', 'masters'],
                   detail: 'Intermediate or graduation. Height at least 5 ft 4 inch. ISSB is required.' },
        tcc:     { label: 'Technical Cadet Course', ageMin: 17, ageMax: 21, edu: ['inter'],
                   detail: 'FSc (Pre-Engineering) with at least 65% marks. ISSB is required.' },
        amc:     { label: 'AMC Cadet (Medical)', ageMin: 17, ageMax: 21, edu: ['inter'],
                   detail: 'FSc (Pre-Medical) with at least 70% marks. Admission to Army medical colleges.' },
        soldier: { label: 'Soldier / Sipahi', ageMin: 17, ageMax: 23, edu: ['matric', 'inter', 'grad', 'masters'],
                   detail: 'At least Matric. Height 5 ft 6 inch (approx). Physical test is required.' },
        clerk:   { label: 'Clerk / Cook / Driver', ageMin: 17, ageMax: 23, edu: ['matric', 'inter', 'grad', 'masters'],
                   detail: 'Matric or middle (depends on the trade). There is a trade test.' },
        lady:    { label: 'Lady Cadet Course', ageMin: 17, ageMax: 28, edu: ['masters'],
                   detail: 'Masters or equivalent degree (depends on the subject). For ladies only.' }
    };

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

    goBtn.addEventListener('click', function () {
        hideError();
        var cat = catSelect.value;
        var age = parseInt(ageInput.value, 10);
        var edu = eduSelect.value;
        if (!cat) { showError('Please select a category first.'); return; }
        if (isNaN(age) || age < 15 || age > 35) { showError('Please enter a valid age (15-35).'); return; }
        var info = cats[cat];
        var ageOk = age >= info.ageMin && age <= info.ageMax;
        var eduOk = info.edu.indexOf(edu) !== -1;
        var eduNames = { matric: 'Matric', inter: 'Intermediate', grad: 'Graduation', masters: 'Masters/Equivalent' };
        var minEdu = info.edu.map(function (e) { return eduNames[e]; }).join(' / ');

        var html = '<div class="card border"><div class="card-body">';
        html += '<h5 class="card-title">' + esc(info.label) + '</h5>';
        html += '<ul class="list-group list-group-flush mb-3">';
        html += '<li class="list-group-item">' + (ageOk ? '&#9989;' : '&#10060;') +
                ' Age ' + info.ageMin + '-' + info.ageMax + ' years (you: ' + age + ')</li>';
        html += '<li class="list-group-item">' + (eduOk ? '&#9989;' : '&#10060;') +
                ' Education: ' + esc(minEdu) + ' (you: ' + esc(eduNames[edu]) + ')</li>';
        html += '</ul>';
        if (ageOk && eduOk) {
            html += '<div class="alert alert-success mb-0">You meet the basic requirements! Register on the official website <a href="https://www.joinpakarmy.gov.pk" target="_blank" rel="noopener">joinpakarmy.gov.pk</a> and prepare for the AS&amp;RC test.</div>';
        } else {
            html += '<div class="alert alert-warning mb-0">Sorry — the requirements are not met. Detail: ' + esc(info.detail) + '</div>';
        }
        html += '<p class="small text-muted mt-2 mb-0">Basic information: ' + esc(info.detail) + ' Requirements can change a little in each advertisement.</p>';
        html += '</div></div>';
        results.innerHTML = html;
        results.classList.remove('d-none');
    });
})();
</script>
@endsection
