@extends('layouts.app')

@section('title', 'Birth Certificate Apply Guide - Azlaan Tools')
@section('meta_description', 'An easy way to get a birth certificate for a child in Pakistan. Step by step guide for Union Council and NADRA, free.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Birth Certificate Apply Guide</h1>
            <p class="lead text-muted">An easy way to get a birth certificate for children from the Union Council or Municipal office. Select your area below, complete the checklist and follow the steps.</p>

            <div class="alert alert-warning small" role="alert">
                Fees and procedures can change over time — be sure to confirm with your Union Council or at <a href="https://www.nadra.gov.pk" target="_blank" rel="noopener">nadra.gov.pk</a>.
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="provinceSelect" class="form-label fw-semibold">Select your area</label>
                        <select class="form-select" id="provinceSelect">
                            <option value="punjab">Punjab</option>
                            <option value="sindh">Sindh</option>
                            <option value="kpk">Khyber Pakhtunkhwa</option>
                            <option value="balochistan">Balochistan</option>
                            <option value="ict">Islamabad (ICT)</option>
                            <option value="ajk">Azad Jammu and Kashmir</option>
                            <option value="gb">Gilgit-Baltistan</option>
                        </select>
                    </div>
                    <div class="alert alert-info" id="officeInfo" role="alert"></div>

                    <h2 class="h5 mt-4">Documents checklist</h2>
                    <p class="text-muted small">Tick every item — <span id="checkCount">0</span> / <span id="checkTotal">0</span> ready.</p>
                    <div class="progress mb-3" style="height: 10px;">
                        <div class="progress-bar" id="checkProgress" role="progressbar" style="width: 0%"></div>
                    </div>
                    <div id="checklist"></div>

                    <button type="button" class="btn btn-primary w-100 mt-3" id="goBtn">Show Steps</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <h2 class="h5">Step by step method</h2>
                        <ol id="stepsList" class="mb-3"></ol>
                        <h2 class="h5">For NADRA B-Form (CRC)</h2>
                        <ol id="bformList" class="mb-3"></ol>
                        <div class="card bg-light">
                            <div class="card-body small">
                                <strong>Official links (for information only):</strong>
                                <ul class="mb-0 mt-2">
                                    <li>NADRA official website: <a href="https://www.nadra.gov.pk" target="_blank" rel="noopener">www.nadra.gov.pk</a></li>
                                    <li>NADRA helpline: <strong>1777</strong> (from mobile)</li>
                                    <li>Find B-Form / CRC details and the nearest NADRA center on the NADRA website.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select your area to find the correct office.</li>
                <li>Tick the documents in the checklist.</li>
                <li>Press "Show Steps" and follow the steps.</li>
            </ol>

            <h2>Frequently asked questions</h2>
            <div class="accordion mb-4" id="faqAcc">
                <div class="accordion-item">
                    <h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">Can I register late (late entry)?</button></h2>
                    <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAcc"><div class="accordion-body">Yes, most areas allow late birth registration, but the fee is higher and more documents may be asked for. Confirm with your Union Council.</div></div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">What should I do for a home birth?</button></h2>
                    <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAcc"><div class="accordion-body">If there is no hospital slip, you must inform the Union Council with a written application and witness statements. A statement from the dai (midwife) can also help.</div></div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">What is the difference between a birth certificate and a B-Form?</button></h2>
                    <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAcc"><div class="accordion-body">A birth certificate is issued by the Union Council / Municipal office (the birth record). The B-Form (Child Registration Certificate / CRC) is issued by NADRA and is needed for school admission, passport and other work.</div></div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">Does this tool apply online?</button></h2>
                    <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAcc"><div class="accordion-body">No. This is only a guide — you must submit the real application yourself by visiting the Union Council or NADRA center.</div></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var provinceSelect = document.getElementById('provinceSelect');
    var officeInfo = document.getElementById('officeInfo');
    var checklist = document.getElementById('checklist');
    var checkCount = document.getElementById('checkCount');
    var checkTotal = document.getElementById('checkTotal');
    var checkProgress = document.getElementById('checkProgress');
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var stepsList = document.getElementById('stepsList');
    var bformList = document.getElementById('bformList');

    var offices = {
        punjab: 'In Punjab, birth registration is done at your <strong>Union Council</strong> (rural area) or <strong>Municipal Committee / Corporation</strong> (urban area).',
        sindh: 'In Sindh, birth registration is done at your <strong>Union Council / Union Committee</strong> or, in urban areas, the <strong>Municipal office</strong>.',
        kpk: 'In Khyber Pakhtunkhwa, the birth certificate is issued by the <strong>Village / Neighbourhood Council</strong> (rural) or <strong>Municipal office</strong> (urban).',
        balochistan: 'In Balochistan, contact your <strong>Union Council</strong> or the nearest <strong>Municipal Committee</strong>.',
        ict: 'In Islamabad, the birth certificate is issued by the <strong>Union Council (ICT)</strong>; in cantonment areas, by the <strong>Cantonment Board</strong>.',
        ajk: 'In Azad Kashmir, register the birth at your <strong>Union Council</strong> or <strong>District Council office</strong>.',
        gb: 'In Gilgit-Baltistan, get the birth certificate from your <strong>Union Council</strong>.'
    };

    var docs = [
        'Father CNIC (copy)',
        'Mother CNIC (copy)',
        'Hospital birth slip / discharge slip',
        'Nikah nama (if asked for)',
        'Old manual birth certificate (for late registration)',
        'Witness CNIC copies (for home birth)'
    ];

    var steps = [
        'After the birth, visit the relevant Union Council / Municipal office as soon as possible and get the birth registration form.',
        'Fill in the child name, date of birth, parent names and CNIC numbers in the form.',
        'Attach copies of the required documents with the form.',
        'Pay the fixed fee and keep the receipt safe (late registration costs more).',
        'Get the computerized birth certificate and check the name, date and other details carefully — if there is a mistake, get it fixed at once.'
    ];

    var bformSteps = [
        'After getting the birth certificate, one parent should visit the nearest NADRA center with the child.',
        'Fill in the B-Form (Child Registration Certificate / CRC) form and attach the birth certificate.',
        'Pay the fee and take the token / receipt.',
        'Collect the B-Form on the given date. It is needed for school admission and passport.'
    ];

    function renderOffice() {
        officeInfo.innerHTML = offices[provinceSelect.value] || offices.punjab;
    }

    function renderChecklist() {
        checklist.innerHTML = '';
        docs.forEach(function (d, i) {
            var div = document.createElement('div');
            div.className = 'form-check mb-2';
            var input = document.createElement('input');
            input.className = 'form-check-input';
            input.type = 'checkbox';
            input.id = 'doc' + i;
            input.addEventListener('change', updateProgress);
            var label = document.createElement('label');
            label.className = 'form-check-label';
            label.htmlFor = 'doc' + i;
            label.textContent = d;
            div.appendChild(input);
            div.appendChild(label);
            checklist.appendChild(div);
        });
        checkTotal.textContent = docs.length;
        updateProgress();
    }

    function updateProgress() {
        var boxes = checklist.querySelectorAll('input[type="checkbox"]');
        var done = 0;
        boxes.forEach(function (b) { if (b.checked) { done++; } });
        checkCount.textContent = done;
        checkProgress.style.width = Math.round((done / boxes.length) * 100) + '%';
    }

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }

    provinceSelect.addEventListener('change', renderOffice);

    goBtn.addEventListener('click', function () {
        hideError();
        stepsList.innerHTML = '';
        steps.forEach(function (s) {
            var li = document.createElement('li');
            li.textContent = s;
            li.className = 'mb-2';
            stepsList.appendChild(li);
        });
        bformList.innerHTML = '';
        bformSteps.forEach(function (s) {
            var li = document.createElement('li');
            li.textContent = s;
            li.className = 'mb-2';
            bformList.appendChild(li);
        });
        results.classList.remove('d-none');
        results.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });

    renderOffice();
    renderChecklist();
})();
</script>
@endsection
