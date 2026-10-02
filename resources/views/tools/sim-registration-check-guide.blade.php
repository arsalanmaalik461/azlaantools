@extends('layouts.app')

@section('title', 'SIM Registration Check Guide - 668 SMS - Azlaan Tools')
@section('meta_description', 'Check SIMs registered on your CNIC in Pakistan. Step-by-step guide for 668 SMS and the PTA portal to find and block unknown SIMs.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">SIM Registration Check Guide</h1>
            <p class="lead text-muted">Check the SIMs registered on your CNIC and stay safe from fraud. Write your CNIC below — we will create the SMS text that you have to send to 668.</p>

            <div class="alert alert-info">
                <strong>Note:</strong> This guide only explains the method. Verification happens on PTA official systems (SMS 668 and the PTA portal) — we do not give any live check or owner details here.
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">Step 1: Create SMS text from your CNIC</h2>
                    <div class="mb-3">
                        <label for="cnicInput" class="form-label fw-semibold">Your CNIC number (without dashes, 13 digits)</label>
                        <input type="text" class="form-control" id="cnicInput" placeholder="3520112345678" maxlength="13" inputmode="numeric">
                        <div class="form-text">Example: 3520112345678 — no dashes or spaces.</div>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Create SMS Text</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h3 class="h6 fw-semibold">SMS to send to 668</h3>
                                <div class="input-group mb-2">
                                    <input type="text" class="form-control" id="smsText" readonly>
                                    <button type="button" class="btn btn-outline-primary" id="copyBtn">Copy</button>
                                </div>
                                <p class="small text-muted mb-0">Copy this text and send it as SMS to <strong>668</strong>. You will get the list of SIMs registered on your CNIC in reply. The operator charges for each SMS.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <h2>Method 1: Check by SMS (668)</h2>
            <ol>
                <li>Type your CNIC number (13 digits, no dashes) on your mobile.</li>
                <li>Send an SMS to 668.</li>
                <li>You will get a reply shortly — it will have the list of all SIMs registered in your name (Jazz, Telenor, Zong, Ufone — all).</li>
                <li>If there is any unknown number in the list, get it blocked at once (see below).</li>
            </ol>

            <h2>Method 2: PTA SIM Information Portal</h2>
            <ol>
                <li>Open the official portal: <a href="https://cnic.sims.pk" target="_blank" rel="noopener">cnic.sims.pk</a> (PTA SIM Information System).</li>
                <li>Enter your CNIC number and complete the verification.</li>
                <li>The portal will show the details of SIMs registered on your CNIC (a small charge applies per query).</li>
                <li>PTA official website: <a href="https://www.pta.gov.pk" target="_blank" rel="noopener">pta.gov.pk</a></li>
            </ol>

            <h2>What to do if you find an unknown SIM?</h2>
            <ol>
                <li>First call that operator&rsquo;s customer care helpline and request to block the SIM.</li>
                <li>If the operator does not help, file a complaint on the PTA helpline <strong>0800-55055</strong>.</li>
                <li>Keep your CNIC and SIM ownership record safe.</li>
            </ol>

            <div class="alert alert-warning">
                <strong>Stay safe from fraud:</strong> Never give your CNIC, OTP or bank details to any unknown number. PTA or banks never ask for OTP on the phone. Report suspicious calls on the PTA helpline.
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Enter your CNIC number above and press "Create SMS Text".</li>
                <li>Copy the given text and send an SMS to 668.</li>
                <li>Or check online through the <a href="https://cnic.sims.pk" target="_blank" rel="noopener">cnic.sims.pk</a> portal.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var cnicInput = document.getElementById('cnicInput');
    var goBtn = document.getElementById('goBtn');
    var copyBtn = document.getElementById('copyBtn');
    var smsText = document.getElementById('smsText');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var cnic = cnicInput.value.replace(/[\s-]/g, '').trim();
        if (!/^\d{13}$/.test(cnic)) {
            showError('Please enter a valid 13-digit CNIC without dashes.');
            return;
        }
        smsText.value = cnic;
        results.classList.remove('d-none');
        results.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    });

    copyBtn.addEventListener('click', function () {
        smsText.select();
        smsText.setSelectionRange(0, smsText.value.length);
        var done = function () {
            copyBtn.textContent = 'Copied!';
            setTimeout(function () { copyBtn.textContent = 'Copy'; }, 1500);
        };
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(smsText.value).then(done).catch(function () {
                document.execCommand('copy');
                done();
            });
        } else {
            document.execCommand('copy');
            done();
        }
    });
})();
</script>
@endsection
