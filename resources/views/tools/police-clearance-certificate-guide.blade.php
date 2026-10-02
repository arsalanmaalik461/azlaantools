@extends('layouts.app')

@section('title', 'Police Clearance Certificate Guide - Azlaan Tools')
@section('meta_description', 'Complete guide to apply online for a police character or clearance certificate in Pakistan for visa and jobs, with province-wise steps and a documents checklist.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Police Clearance Certificate Guide</h1>
            <p class="lead text-muted">An easy guide to apply for a police character / clearance certificate for visa, immigration or jobs. Select your province, then see the steps and documents.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="provinceSel" class="form-label fw-semibold">Select your province / area</label>
                        <select class="form-select" id="provinceSel">
                            <option value="punjab">Punjab</option>
                            <option value="sindh">Sindh</option>
                            <option value="kpk">Khyber Pakhtunkhwa</option>
                            <option value="balochistan">Balochistan</option>
                            <option value="islamabad">Islamabad (ICT)</option>
                            <option value="other">Azad Kashmir / Gilgit-Baltistan</option>
                        </select>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">See Guide</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <h5 id="provTitle"></h5>
                        <p class="text-muted small" id="provNote"></p>
                        <h6 class="mt-3">Steps to apply</h6>
                        <ol id="stepsList"></ol>
                        <h6 class="mt-3">Official portal</h6>
                        <p id="portalLink"></p>
                        <h6 class="mt-3">Documents checklist <span class="badge bg-success" id="checkCount">0/0 ready</span></h6>
                        <div id="docsList" class="list-group mb-3"></div>
                        <div class="alert alert-warning small mb-0">
                            Fees and processing time may change over time — confirm with the official website or your nearest Police Khidmat Markaz. This page is a guide and does not do live verification.
                        </div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select your province or area.</li>
                <li>Press <strong>See Guide</strong> — you will get the steps and the official portal link.</li>
                <li>Tick the documents in the checklist that are ready.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var provinceSel = document.getElementById('provinceSel');
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var provTitle = document.getElementById('provTitle');
    var provNote = document.getElementById('provNote');
    var stepsList = document.getElementById('stepsList');
    var portalLink = document.getElementById('portalLink');
    var docsList = document.getElementById('docsList');
    var checkCount = document.getElementById('checkCount');

    var COMMON_DOCS = [
        'Original CNIC + photocopy',
        'Passport size photos (2)',
        'Passport copy (for visa)',
        'Proof of address (electricity/gas bill or rent agreement)',
        'Fee challan or receipt',
        'Application form (download from portal or get from Khidmat Markaz)'
    ];

    var DATA = {
        punjab: {
            title: 'Punjab Police - Character Certificate',
            note: 'In Punjab, the character certificate is issued through Police Khidmat Markaz (PKM). You can also take an online token/appointment.',
            portal: 'https://www.punjabpolice.gov.pk',
            steps: [
                'Open the Police Khidmat Markaz services section on the Punjab Police official website.',
                'Download the Character Certificate form or apply online.',
                'Prepare the required documents and the fee challan.',
                'Go to the nearest Police Khidmat Markaz and give biometrics (thumb impression).',
                'The certificate will be issued after verification - check the status by SMS or on the portal.'
            ]
        },
        sindh: {
            title: 'Sindh Police - Character Verification Certificate',
            note: 'In Sindh, the character verification certificate is applied for online and is issued after police station verification.',
            portal: 'https://www.sindhpolice.gov.pk',
            steps: [
                'Fill the online character verification form on the Sindh Police official website.',
                'Upload or submit copies of your CNIC and documents.',
                'Verification is done by the relevant police station.',
                'Deposit the fee and keep the receipt safe.',
                'Collect the certificate after verification is complete.'
            ]
        },
        kpk: {
            title: 'Khyber Pakhtunkhwa Police - Police Clearance Certificate',
            note: 'KP has an online system and Police Assistance Lines for the police clearance certificate.',
            portal: 'https://kppolice.gov.pk',
            steps: [
                'Open the clearance certificate section on the KP Police official website.',
                'Fill the online form and attach the documents.',
                'Deposit the fee challan in the bank.',
                'Get verification done from the relevant DPO/SP office or khidmat markaz.',
                'Collect the certificate when it is issued.'
            ]
        },
        balochistan: {
            title: 'Balochistan Police - Character Certificate',
            note: 'In Balochistan, the character certificate is issued by the relevant district police office.',
            portal: 'https://balochistanpolice.gov.pk',
            steps: [
                'Get information from the Balochistan Police official website.',
                'Submit the application at the District Police Office (DPO office).',
                'Fill the form with your CNIC, photo and proof of address.',
                'Get the police station level verification done.',
                'Pay the fee and collect the certificate.'
            ]
        },
        islamabad: {
            title: 'Islamabad Police - Police Character Certificate',
            note: 'In Islamabad, the police character certificate is applied for online and collected from the Khidmat Markaz.',
            portal: 'https://islamabadpolice.gov.pk',
            steps: [
                'Open the online character certificate form on the Islamabad Police official website.',
                'Fill the form and upload your CNIC and documents.',
                'Generate the fee challan and deposit it in the bank.',
                'Give biometrics at the Police Khidmat Markaz.',
                'Collect the certificate from the Khidmat Markaz after verification.'
            ]
        },
        other: {
            title: 'Azad Kashmir / Gilgit-Baltistan',
            note: 'In these areas the online portal is limited - most work is done through the relevant police station or SP office.',
            portal: '',
            steps: [
                'Contact your relevant police station or SP/DPO office.',
                'Take the character certificate application form and fill it.',
                'Attach a CNIC copy, 2 photos and proof of address.',
                'Get verification done from the station clerk.',
                'Pay the fee and collect the certificate.'
            ]
        }
    };

    function renderDocs() {
        docsList.innerHTML = '';
        COMMON_DOCS.forEach(function (d, i) {
            var label = document.createElement('label');
            label.className = 'list-group-item d-flex align-items-center gap-2';
            var cb = document.createElement('input');
            cb.type = 'checkbox';
            cb.className = 'form-check-input doc-check';
            cb.id = 'doc' + i;
            cb.addEventListener('change', updateCount);
            var sp = document.createElement('span');
            sp.textContent = d;
            label.appendChild(cb);
            label.appendChild(sp);
            docsList.appendChild(label);
        });
        updateCount();
    }

    function updateCount() {
        var boxes = docsList.querySelectorAll('.doc-check');
        var done = 0;
        boxes.forEach(function (b) { if (b.checked) done++; });
        checkCount.textContent = done + '/' + boxes.length + ' ready';
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var key = provinceSel.value;
        var d = DATA[key];
        if (!d) { showError('Please select an area.'); return; }
        provTitle.textContent = d.title;
        provNote.textContent = d.note;
        stepsList.innerHTML = '';
        d.steps.forEach(function (s) {
            var li = document.createElement('li');
            li.textContent = s;
            stepsList.appendChild(li);
        });
        portalLink.innerHTML = '';
        if (d.portal) {
            var a = document.createElement('a');
            a.href = d.portal;
            a.target = '_blank';
            a.rel = 'noopener';
            a.textContent = d.portal.replace('https://www.', '').replace('https://', '');
            portalLink.appendChild(a);
            var hint = document.createElement('span');
            hint.className = 'text-muted small d-block';
            hint.textContent = 'This is the official website - the link will open in a new tab.';
            portalLink.appendChild(hint);
        } else {
            portalLink.textContent = 'The online portal for this area is limited - please contact the nearest police office.';
        }
        renderDocs();
        results.classList.remove('d-none');
    });

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
})();
</script>
@endsection
