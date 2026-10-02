@extends('layouts.app')
@section('title', 'Domicile Certificate Guide Pakistan — Azlaan Tools')
@section('meta_description', 'Complete process for getting a domicile certificate in Pakistan: required documents, step-by-step process, and fee information. Free online guide.')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Domicile Certificate Guide</h1>
            <p class="lead text-muted">How to get a domicile certificate, required documents, and fees — choose your province tab. This guide is for general information; fees and process can change over time.</p>
            <div class="alert alert-warning">
                <strong>Important note:</strong> A domicile is always made from <strong>the district where you live</strong>. Fees and timelines can change — confirm on the official website.
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <label class="form-label fw-semibold">Choose your province / area</label>
                    <div class="d-flex flex-wrap gap-2 mb-4" id="provBtns" role="tablist">
                        <button type="button" class="btn btn-outline-primary prov-btn" data-prov="punjab">Punjab</button>
                        <button type="button" class="btn btn-outline-primary prov-btn" data-prov="sindh">Sindh</button>
                        <button type="button" class="btn btn-outline-primary prov-btn" data-prov="kp">Khyber Pakhtunkhwa</button>
                        <button type="button" class="btn btn-outline-primary prov-btn" data-prov="balochistan">Balochistan</button>
                        <button type="button" class="btn btn-outline-primary prov-btn" data-prov="ict">Islamabad (ICT)</button>
                    </div>

                    <h2 class="h5" id="provTitle"></h2>
                    <p class="text-muted" id="provWhere"></p>

                    <h3 class="h6 mt-4">Step-by-step process</h3>
                    <ol id="provSteps" class="mb-4"></ol>

                    <h3 class="h6">Required documents — checklist</h3>
                    <p class="text-muted small">Tick each document you have ready. Your progress is saved in the browser.</p>
                    <div class="progress mb-3" style="height: 20px;">
                        <div class="progress-bar" id="docProgress" role="progressbar" style="width: 0%">0%</div>
                    </div>
                    <div id="docList" class="list-group mb-4"></div>

                    <h3 class="h6">Fees and time</h3>
                    <p id="provFee" class="mb-4"></p>

                    <h3 class="h6">Official links</h3>
                    <ul id="provLinks" class="mb-2"></ul>
                    <p class="text-muted small">These are information links only — online application availability differs by province, so check the website before visiting the office.</p>

                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Click your province button.</li>
                <li>Read the steps and tick the document checklist as you go.</li>
                <li>See the fee estimate and verify on the official website.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var DATA = {
        punjab: {
            title: 'Punjab — Domicile Certificate',
            where: 'In Punjab, a domicile is made at the <strong>e-Khidmat Markaz</strong> or your district <strong>Deputy Commissioner (DC) office</strong>.',
            steps: [
                'Go to the nearest e-Khidmat Markaz or DC office and get the domicile token/form.',
                'Fill in the form — write your name, father name, permanent address, and district correctly.',
                'Attach photocopies of the required documents (see the checklist below).',
                'Pay the fee and keep the receipt safe.',
                'After biometric / verification you will be given a date — collect your domicile on that date.'
            ],
            docs: [
                'Copy of CNIC or B-Form (B-Form for under 18)',
                'Copy of father or guardian CNIC',
                'Electricity/gas bill or rent agreement (proof of address)',
                '2 passport size photographs',
                'Copy of matric or last education certificate (if available)'
            ],
            fee: 'The fee is usually a few hundred rupees and can differ by district. Tip: always take the fee receipt. <strong>Rates can change — confirm on the official website.</strong>',
            links: [
                ['Punjab Government Portal', 'https://www.punjab.gov.pk'],
                ['e-Khidmat / Citizen Portal information', 'https://www.punjab.gov.pk']
            ]
        },
        sindh: {
            title: 'Sindh — Domicile Certificate',
            where: 'In Sindh, a domicile is made through the <strong>DC office</strong> or <strong>Mukhtiarkar office</strong>.',
            steps: [
                'Get the domicile form from your district DC office or Mukhtiarkar office.',
                'Fill in the form and attach attested copies of the documents.',
                'Get verification (attestation) from the concerned officer.',
                'Deposit the fee in the bank through a challan.',
                'Collect the domicile certificate on the given date.'
            ],
            docs: [
                'Copy of CNIC or B-Form',
                'Copy of father CNIC',
                'Proof of address (electricity bill / locality verification on the domicile form)',
                '2 passport size photographs',
                'Copies of education certificates (if available)'
            ],
            fee: 'Fees differ by district. <strong>Rates can change — confirm on the official website.</strong>',
            links: [
                ['Sindh Government Portal', 'https://www.sindh.gov.pk'],
                ['NADRA (for CNIC/B-Form)', 'https://www.nadra.gov.pk']
            ]
        },
        kp: {
            title: 'Khyber Pakhtunkhwa — Domicile Certificate',
            where: 'In KP, a domicile is made at the <strong>DC office</strong> or <strong>Citizen Facilitation Center</strong>.',
            steps: [
                'Get the form from the DC office or Citizen Facilitation Center.',
                'Fill in the form and attach the documents.',
                'Get address verification from the patwari / concerned officer.',
                'Pay the fee and take the receipt.',
                'Collect your domicile on the fixed date.'
            ],
            docs: [
                'Copy of CNIC or B-Form',
                'Copy of father CNIC',
                'Proof of address (bill or patwari verification)',
                '2 passport size photographs',
                'Copy of education certificate (if available)'
            ],
            fee: 'Fees differ by district. <strong>Rates can change — confirm on the official website.</strong>',
            links: [
                ['KP Government Portal', 'https://www.kp.gov.pk'],
                ['NADRA (for CNIC/B-Form)', 'https://www.nadra.gov.pk']
            ]
        },
        balochistan: {
            title: 'Balochistan — Domicile Certificate',
            where: 'In Balochistan, a domicile is made through the <strong>DC office</strong>.',
            steps: [
                'Get the domicile form from your district DC office.',
                'Fill in the form and attach attested copies of the documents.',
                'Get verification from the concerned officer.',
                'Pay the fee and take the receipt.',
                'Collect the certificate on the given date.'
            ],
            docs: [
                'Copy of CNIC or B-Form',
                'Copy of father CNIC',
                'Proof of address',
                '2 passport size photographs',
                'Copies of education certificates (if available)'
            ],
            fee: 'Fees differ by district. <strong>Rates can change — confirm on the official website.</strong>',
            links: [
                ['Balochistan Government Portal', 'https://www.balochistan.gov.pk'],
                ['NADRA (for CNIC/B-Form)', 'https://www.nadra.gov.pk']
            ]
        },
        ict: {
            title: 'Islamabad (ICT) — Domicile Certificate',
            where: 'In Islamabad, a domicile is made at the <strong>Deputy Commissioner office, G-11/4</strong> or <strong>Citizen Facilitation Center</strong>.',
            steps: [
                'Get the form from the DC office Islamabad or the facilitation center.',
                'Fill in the form — a permanent Islamabad address is required.',
                'Attach attested copies of the documents.',
                'Pay the fee and take the receipt.',
                'After verification, collect your domicile on the fixed date.'
            ],
            docs: [
                'Copy of CNIC or B-Form',
                'Copy of father CNIC',
                'Proof of Islamabad address (bill / rent agreement)',
                '2 passport size photographs',
                'Copy of education certificate (if available)'
            ],
            fee: 'Fees can change over time. <strong>Rates can change — confirm on the official website.</strong>',
            links: [
                ['Islamabad Capital Territory Portal', 'https://www.ict.gov.pk'],
                ['NADRA (for CNIC/B-Form)', 'https://www.nadra.gov.pk']
            ]
        }
    };

    var provBtns = document.querySelectorAll('.prov-btn');
    var provTitle = document.getElementById('provTitle');
    var provWhere = document.getElementById('provWhere');
    var provSteps = document.getElementById('provSteps');
    var docList = document.getElementById('docList');
    var docProgress = document.getElementById('docProgress');
    var provFee = document.getElementById('provFee');
    var provLinks = document.getElementById('provLinks');
    var errorBox = document.getElementById('errorBox');
    var current = 'punjab';

    function storageKey(prov) { return 'domicile_docs_' + prov; }
    function loadChecked(prov) {
        try {
            var raw = localStorage.getItem(storageKey(prov));
            return raw ? JSON.parse(raw) : [];
        } catch (e) { return []; }
    }
    function saveChecked(prov, arr) {
        try { localStorage.setItem(storageKey(prov), JSON.stringify(arr)); } catch (e) { /* ignore */ }
    }

    function updateProgress() {
        var boxes = docList.querySelectorAll('input[type="checkbox"]');
        var done = 0;
        for (var i = 0; i < boxes.length; i++) { if (boxes[i].checked) { done++; } }
        var pct = boxes.length ? Math.round(done / boxes.length * 100) : 0;
        docProgress.style.width = pct + '%';
        docProgress.textContent = pct + '% (' + done + '/' + boxes.length + ' ready)';
    }

    function render(prov) {
        current = prov;
        var d = DATA[prov];
        if (!d) {
            errorBox.textContent = 'Information not found.';
            errorBox.classList.remove('d-none');
            return;
        }
        errorBox.classList.add('d-none');
        for (var b = 0; b < provBtns.length; b++) {
            provBtns[b].classList.toggle('btn-primary', provBtns[b].getAttribute('data-prov') === prov);
            provBtns[b].classList.toggle('btn-outline-primary', provBtns[b].getAttribute('data-prov') !== prov);
        }
        provTitle.textContent = d.title;
        provWhere.innerHTML = d.where;
        provSteps.innerHTML = '';
        for (var s = 0; s < d.steps.length; s++) {
            var li = document.createElement('li');
            li.textContent = d.steps[s];
            provSteps.appendChild(li);
        }
        var checked = loadChecked(prov);
        docList.innerHTML = '';
        for (var i = 0; i < d.docs.length; i++) {
            (function (idx) {
                var label = document.createElement('label');
                label.className = 'list-group-item d-flex align-items-start gap-2';
                var cb = document.createElement('input');
                cb.type = 'checkbox';
                cb.className = 'form-check-input mt-1';
                cb.checked = checked.indexOf(idx) >= 0;
                cb.addEventListener('change', function () {
                    var arr = [];
                    var boxes = docList.querySelectorAll('input[type="checkbox"]');
                    for (var j = 0; j < boxes.length; j++) { if (boxes[j].checked) { arr.push(j); } }
                    saveChecked(current, arr);
                    updateProgress();
                });
                var span = document.createElement('span');
                span.textContent = d.docs[idx];
                label.appendChild(cb);
                label.appendChild(span);
                docList.appendChild(label);
            })(i);
        }
        provFee.innerHTML = d.fee;
        provLinks.innerHTML = '';
        for (var l = 0; l < d.links.length; l++) {
            var li2 = document.createElement('li');
            var a = document.createElement('a');
            a.href = d.links[l][1];
            a.target = '_blank';
            a.rel = 'noopener';
            a.textContent = d.links[l][0];
            li2.appendChild(a);
            provLinks.appendChild(li2);
        }
        updateProgress();
    }

    for (var k = 0; k < provBtns.length; k++) {
        provBtns[k].addEventListener('click', function () {
            render(this.getAttribute('data-prov'));
        });
    }
    render('punjab');
})();
</script>
@endsection
