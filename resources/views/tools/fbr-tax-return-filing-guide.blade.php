@extends('layouts.app')

@section('title', 'FBR Tax Return Filing Guide - Azlaan Tools')
@section('meta_description', 'An easy step-by-step guide to filing your income tax return on FBR Iris, with a documents checklist and filer benefits. Free.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">FBR Tax Return Filing Guide</h1>
            <p class="lead text-muted">Learn to file your income tax return on FBR Iris yourself — from preparing your documents to submitting the declaration. This guide is for information only, not legal advice.</p>

            <div class="alert alert-warning small">Rates, deadlines and forms can change — confirm on the official website <strong>fbr.gov.pk</strong>.</div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5>Step 1: Required Documents Checklist</h5>
                    <p class="text-muted small">Tick the items below — the progress bar shows how ready you are.</p>
                    <div id="checklist"></div>
                    <div class="progress my-3" style="height: 22px;">
                        <div class="progress-bar bg-success" id="progBar" role="progressbar" style="width:0%">0%</div>
                    </div>

                    <h5 class="mt-4">Step 2: Iris Account</h5>
                    <p class="small">Register or log in at the FBR Iris portal <strong>iris.fbr.gov.pk</strong>. For registration you need CNIC + email + mobile. After your first IRIS enrollment, you can get your "Registration" → "NTN".</p>

                    <h5 class="mt-3">Step 3: File the Return</h5>
                    <ol class="small">
                        <li>After logging in to Iris, select <strong>Declaration</strong> → <strong>Income Tax Return</strong>.</li>
                        <li>Choose the correct <strong>tax year</strong> (the deadline is usually 30 September — FBR sometimes extends it).</li>
                        <li>Salary, business or both — fill the relevant tabs: salary slips, bank profit, rent income, expenses.</li>
                        <li>Do not forget to attach the wealth statement (assets/declarations) — property, car, bank balance.</li>
                        <li>Press <strong>Verify + Submit</strong>. Download the confirmation receipt PDF and keep it safe.</li>
                    </ol>

                    <h5 class="mt-3">Benefits of Being a Filer</h5>
                    <ul class="small">
                        <li>Lower withholding tax rates — on bank transactions, buying a car or property, and cash withdrawals, you pay half or even less than a non-filer.</li>
                        <li>Easier bank accounts and property deals — many tasks only a filer can do.</li>
                        <li>Your name on the Active Taxpayer List (ATL) — helps with govt contracts, tenders and loans.</li>
                    </ul>

                    <h5 class="mt-3">Official Links</h5>
                    <ul class="small">
                        <li>FBR Iris (online filing): <a href="https://iris.fbr.gov.pk" target="_blank" rel="noopener">iris.fbr.gov.pk</a></li>
                        <li>FBR official website: <a href="https://www.fbr.gov.pk" target="_blank" rel="noopener">fbr.gov.pk</a></li>
                        <li>FBR Helpline: 111-772-772 (Mon–Sat)</li>
                    </ul>
                    <p class="small text-muted mb-0">Note: this tool is only a guide and does not verify live status. Check your filer status with the Active Taxpayer List search on fbr.gov.pk.</p>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Tick the documents checklist items when you have them ready.</li>
                <li>Once it is 100% complete, go to the Iris portal and follow the steps.</li>
                <li>Submit the return and keep the receipt safe.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var checklist = document.getElementById('checklist');
    var progBar = document.getElementById('progBar');

    var docs = [
        'CNIC (front + back copy)',
        'NTN number or Iris enrollment',
        'Salary slips (whole year) or business income record',
        'Bank statements (all accounts, whole tax year)',
        'Tax deduction certificates (salary: from employer, bank: from bank)',
        'Property / vehicle documents (if you bought or sold any)',
        'Utility bills (electricity/gas) — for address proof',
        'Zakat / donation receipts (to claim tax credit)',
        'Rent agreements (if you have rental income or paid rent)',
        'Wealth statement details (assets: house, plot, car, bank balance)'
    ];

    var state = {};
    try { state = JSON.parse(localStorage.getItem('fbrDocsChecklist') || '{}'); } catch (e) { state = {}; }

    function render() {
        checklist.innerHTML = '';
        var done = 0;
        docs.forEach(function (d, i) {
            var wrap = document.createElement('div');
            wrap.className = 'form-check mb-2';
            var cb = document.createElement('input');
            cb.type = 'checkbox';
            cb.className = 'form-check-input';
            cb.id = 'doc' + i;
            cb.checked = !!state[i];
            if (cb.checked) done++;
            cb.addEventListener('change', function () {
                state[i] = cb.checked;
                try { localStorage.setItem('fbrDocsChecklist', JSON.stringify(state)); } catch (e) {}
                update();
            });
            var lb = document.createElement('label');
            lb.className = 'form-check-label small';
            lb.htmlFor = 'doc' + i;
            lb.textContent = d;
            wrap.appendChild(cb);
            wrap.appendChild(lb);
            checklist.appendChild(wrap);
        });
        var pct = Math.round(done / docs.length * 100);
        progBar.style.width = pct + '%';
        progBar.textContent = pct + '% (' + done + '/' + docs.length + ')';
    }

    function update() { render(); }
    render();
})();
</script>
@endsection
