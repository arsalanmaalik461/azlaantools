@extends('layouts.app')

@section('title', 'Sui Gas New Connection Guide - Azlaan Tools')
@section('meta_description', 'How to get a new Sui gas connection: SNGPL and SSGC application process, documents and fee guide.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Sui Gas New Connection Guide</h1>
            <p class="lead text-muted">The complete method to get a new gas connection — select SNGPL (Punjab/KPK) or SSGC (Sindh/Balochistan), follow the steps and keep the documents checklist ready.</p>

            <ul class="nav nav-pills mb-4" id="coTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="sngplTab" type="button" role="tab">SNGPL (Punjab / KPK)</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="ssgcTab" type="button" role="tab">SSGC (Sindh / Balochistan)</button>
                </li>
            </ul>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5" id="coTitle">SNGPL — New Connection Steps</h2>
                    <ol id="stepsList" class="mt-3"></ol>

                    <h3 class="h6 mt-4">Documents checklist <span class="badge bg-primary" id="docProgress">0 / 0 ready</span></h3>
                    <div class="progress mb-3" style="height: 8px;">
                        <div class="progress-bar" id="docBar" role="progressbar" style="width: 0%"></div>
                    </div>
                    <div id="docsList" class="list-group mb-4"></div>

                    <h3 class="h6">Fee calculation</h3>
                    <div class="alert alert-warning small">
                        <strong>Rates may change — confirm on the official website.</strong>
                        The demand notice fee depends on the connection type (domestic/commercial), pipeline length and your area. The exact fee is only known from the official demand notice or website.
                    </div>

                    <h3 class="h6">Official portals (real links)</h3>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="https://www.sngpl.com.pk" target="_blank" rel="noopener">www.sngpl.com.pk</a> — SNGPL official website (new connection application / customer portal)</li>
                        <li class="mb-2"><a href="https://www.ssgc.com.pk" target="_blank" rel="noopener">www.ssgc.com.pk</a> — SSGC official website (new connection application)</li>
                    </ul>
                    <p class="small text-muted mb-0">Note: this guide is general information, not live verification. Always check your application status on the official website or helpline.</p>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select your gas company tab (SNGPL or SSGC).</li>
                <li>Read the steps and tick the documents checklist.</li>
                <li>Apply online through the official website or visit the nearest regional office.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var data = {
        sngpl: {
            title: 'SNGPL — New Connection Steps',
            steps: [
                'Check that an SNGPL gas line exists in your area (most cities of Punjab and KPK have SNGPL).',
                'Apply online: fill the customer portal / new connection application form at sngpl.com.pk, or get a form from the nearest SNGPL regional office.',
                'Submit the required documents (see the checklist below).',
                'The SNGPL team does a site survey and issues a demand notice.',
                'Deposit the demand notice fee at the designated bank and keep the receipt safe.',
                'After the fee is paid, the service line and gas meter are installed, then the connection is activated.'
            ],
            docs: [
                'Copy of CNIC (of the applicant)',
                'Proof of ownership — registry / fard / allotment letter (if on rent: rent agreement + owner CNIC)',
                'Affidavit — as required by the office',
                'Neighbouring house gas bill (for reference, if asked)',
                '2 passport-size photographs',
                'Fee receipt (after the demand notice)'
            ]
        },
        ssgc: {
            title: 'SSGC — New Connection Steps',
            steps: [
                'Check that an SSGC gas line exists in your area (SSGC serves Sindh and Balochistan).',
                'Apply online: submit a new connection application at ssgc.com.pk, or visit the nearest SSGC customer facilitation center.',
                'Submit the required documents (see the checklist below).',
                'The SSGC team issues a demand notice after the survey.',
                'Deposit the demand notice fee at the designated bank.',
                'The gas connection is activated after the service line and meter are installed.'
            ],
            docs: [
                'Copy of CNIC (of the applicant)',
                'Proof of ownership — registry / allotment letter (if on rent: rent agreement)',
                'Affidavit — as required by the office',
                'Copy of a nearby gas bill (if asked)',
                '2 passport-size photographs',
                'Fee receipt (after the demand notice)'
            ]
        }
    };

    var current = 'sngpl';
    var stepsList = document.getElementById('stepsList');
    var docsList = document.getElementById('docsList');
    var coTitle = document.getElementById('coTitle');
    var sngplTab = document.getElementById('sngplTab');
    var ssgcTab = document.getElementById('ssgcTab');
    var docBar = document.getElementById('docBar');
    var docProgress = document.getElementById('docProgress');

    function updateProgress() {
        var boxes = docsList.querySelectorAll('input[type="checkbox"]');
        var done = 0;
        boxes.forEach(function (b) { if (b.checked) done++; });
        var total = boxes.length;
        docProgress.textContent = done + ' / ' + total + ' ready';
        docBar.style.width = (total ? Math.round(done / total * 100) : 0) + '%';
    }

    function render() {
        var d = data[current];
        coTitle.textContent = d.title;
        stepsList.innerHTML = '';
        d.steps.forEach(function (s) {
            var li = document.createElement('li');
            li.className = 'mb-2';
            li.textContent = s;
            stepsList.appendChild(li);
        });
        docsList.innerHTML = '';
        d.docs.forEach(function (doc, i) {
            var label = document.createElement('label');
            label.className = 'list-group-item d-flex align-items-center gap-2';
            var cb = document.createElement('input');
            cb.type = 'checkbox';
            cb.className = 'form-check-input mt-0';
            cb.id = 'doc_' + current + '_' + i;
            cb.addEventListener('change', updateProgress);
            var span = document.createElement('span');
            span.textContent = doc;
            label.appendChild(cb);
            label.appendChild(span);
            docsList.appendChild(label);
        });
        updateProgress();
    }

    sngplTab.addEventListener('click', function () {
        current = 'sngpl';
        sngplTab.classList.add('active');
        ssgcTab.classList.remove('active');
        render();
    });
    ssgcTab.addEventListener('click', function () {
        current = 'ssgc';
        ssgcTab.classList.add('active');
        sngplTab.classList.remove('active');
        render();
    });

    render();
})();
</script>
@endsection
