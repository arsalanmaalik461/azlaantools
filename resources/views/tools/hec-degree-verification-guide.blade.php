@extends('layouts.app')
@section('title', 'HEC Degree Verification Guide - Azlaan Tools')
@section('meta_description', 'Full guide to HEC degree attestation and verification: online apply, fee, required documents and tracking — step by step guide, free.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">HEC Degree Verification Guide</h1>
            <p class="lead text-muted">The complete online process for HEC (Higher Education Commission) degree attestation / verification — fee, documents and tracking, all in one place.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h2 class="h5 mb-0">Your preparation checklist</h2>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="resetBtn">Reset</button>
                    </div>
                    <p class="text-muted small">Tick each step as you complete it — your progress shows below.</p>
                    <div class="progress mb-3" style="height: 22px;">
                        <div class="progress-bar" id="progBar" role="progressbar" style="width: 0%;" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">0%</div>
                    </div>

                    <div class="list-group" id="stepsList">
                        <label class="list-group-item d-flex gap-3">
                            <input class="form-check-input mt-1 stepChk" type="checkbox">
                            <span><strong>Step 1 — Create an account on the HEC e-portal.</strong><br>
                            <span class="text-muted small">Go to hec.gov.pk and register on the e-services portal (email + CNIC). You will use this account for every application.</span></span>
                        </label>
                        <label class="list-group-item d-flex gap-3">
                            <input class="form-check-input mt-1 stepChk" type="checkbox">
                            <span><strong>Step 2 — Add your degree details to your profile.</strong><br>
                            <span class="text-muted small">Write your university, program, passing year and roll number exactly as on your transcript — a wrong entry can get your application rejected.</span></span>
                        </label>
                        <label class="list-group-item d-flex gap-3">
                            <input class="form-check-input mt-1 stepChk" type="checkbox">
                            <span><strong>Step 3 — Submit your attestation application and choose a mode.</strong><br>
                            <span class="text-muted small">Create a "Degree Attestation" application on the portal. Choose a mode: <em>Walk-in</em> (visit an HEC office / regional center yourself) or <em>TCS courier</em> (send your documents from home).</span></span>
                        </label>
                        <label class="list-group-item d-flex gap-3">
                            <input class="form-check-input mt-1 stepChk" type="checkbox">
                            <span><strong>Step 4 — Pay the fee.</strong><br>
                            <span class="text-muted small">Pay the fee as per the challan/bank details given on the portal. The fee is per document (degree and transcript are separate). Fees change over time — confirm the latest fee on the portal.</span></span>
                        </label>
                        <label class="list-group-item d-flex gap-3">
                            <input class="form-check-input mt-1 stepChk" type="checkbox">
                            <span><strong>Step 5 — Prepare the required documents.</strong><br>
                            <span class="text-muted small">Original degree + transcripts, a copy of your CNIC, and for first-time attestation your Matric/Inter certificates are also asked for. Keep a set of photocopies with you too.</span></span>
                        </label>
                        <label class="list-group-item d-flex gap-3">
                            <input class="form-check-input mt-1 stepChk" type="checkbox">
                            <span><strong>Step 6 — Track your application and receive your attested documents.</strong><br>
                            <span class="text-muted small">Keep checking your application status on the portal. With walk-in you get your attested documents back the same day or on the given date; with TCS they arrive by courier.</span></span>
                        </label>
                    </div>

                    <div class="d-flex flex-wrap gap-2 mt-3">
                        <button type="button" class="btn btn-primary" id="printBtn">Print Checklist</button>
                    </div>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5">Required documents (usually)</h2>
                    <ul class="mb-0">
                        <li>Original degree and transcripts (issued by the university)</li>
                        <li>CNIC photocopy</li>
                        <li>For first attestation: Matric and Intermediate certificates</li>
                        <li>Fee challan / payment proof</li>
                        <li>For TCS mode: the portal tells you the return envelope details for sending the attested documents back</li>
                    </ul>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5">Official links and contact</h2>
                    <ul class="mb-0">
                        <li>HEC official website: <a href="https://www.hec.gov.pk" target="_blank" rel="noopener">hec.gov.pk</a></li>
                        <li>HEC e-services portal: <a href="https://eservices.hec.gov.pk" target="_blank" rel="noopener">eservices.hec.gov.pk</a></li>
                        <li>Regional centers: Islamabad, Karachi, Lahore, Peshawar, Quetta</li>
                    </ul>
                    <p class="text-muted small mt-2 mb-0">This guide is for information only — it does not claim live verification. Fees and procedures can change over time; confirm on the official website before you apply.</p>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Read the checklist above — tick each step when it is done.</li>
                <li>Prepare your file set using the documents list.</li>
                <li>Go to the HEC portal through the official links and submit your application.</li>
                <li>Use <strong>Print Checklist</strong> to print this page and keep it with your documents.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var checks = document.querySelectorAll('.stepChk');
    var progBar = document.getElementById('progBar');
    var resetBtn = document.getElementById('resetBtn');
    var printBtn = document.getElementById('printBtn');
    var errorBox = document.getElementById('errorBox');

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function update() {
        var done = 0, i;
        for (i = 0; i < checks.length; i++) { if (checks[i].checked) done++; }
        var pct = Math.round(done / checks.length * 100);
        progBar.style.width = pct + '%';
        progBar.setAttribute('aria-valuenow', pct);
        progBar.textContent = pct + '%';
        if (pct === 100) {
            progBar.classList.remove('bg-primary');
            progBar.classList.add('bg-success');
        } else {
            progBar.classList.add('bg-primary');
            progBar.classList.remove('bg-success');
        }
    }
    var i;
    for (i = 0; i < checks.length; i++) {
        checks[i].addEventListener('change', function () { hideError(); update(); });
    }
    resetBtn.addEventListener('click', function () {
        var j;
        for (j = 0; j < checks.length; j++) { checks[j].checked = false; }
        hideError();
        update();
    });
    printBtn.addEventListener('click', function () {
        hideError();
        window.print();
    });
    update();
})();
</script>
@endsection
