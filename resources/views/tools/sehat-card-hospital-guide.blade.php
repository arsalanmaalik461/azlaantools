@extends('layouts.app')

@section('title', 'Sehat Card Hospital Guide - Azlaan Tools')
@section('meta_description', 'List of Sehat Card panel hospitals and how to get free treatment — with official steps, free guide.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Sehat Card Hospital Guide</h1>
            <p class="lead text-muted">How to get free treatment with Sehat Card: check your eligibility, see the list of panel hospitals, and understand the card process. This guide is for information only — this tool does no live verification.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">Step 1: Check your eligibility by SMS</h2>
                    <p>Send your 13-digit CNIC number without dashes to <strong>8500</strong> by SMS. The reply will tell you if you are eligible for Sehat Card.</p>
                    <div class="mb-3">
                        <label for="cnicInput" class="form-label fw-semibold">Write your CNIC number (for format check only)</label>
                        <input type="text" class="form-control" id="cnicInput" placeholder="e.g. 3410198765432" maxlength="15" inputmode="numeric">
                        <div class="form-text">Only the format is checked — no data is sent anywhere. The real check is done by SMS to 8500.</div>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="checkBtn">Prepare SMS Text</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>
                    <div id="results" class="d-none mt-4">
                        <div class="alert alert-success" id="smsBox" role="alert"></div>
                        <p class="mb-1"><strong>Now do this:</strong></p>
                        <ol class="mb-0">
                            <li>Open the SMS app on your mobile.</li>
                            <li>In the recipient field write <strong>8500</strong>.</li>
                            <li>In the message write your CNIC number (without dashes) and send it.</li>
                            <li>You will get the eligibility reply shortly.</li>
                        </ol>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">Step 2: See the list of panel hospitals</h2>
                    <p>The latest list of panel (approved) hospitals is only on the official website. Open the official links below and find the hospital in your city:</p>
                    <ul>
                        <li><a href="https://www.sehatsahulat.com.pk" target="_blank" rel="noopener">sehatsahulat.com.pk</a> — official website of the Sehat Sahulat Program (panel hospitals list)</li>
                        <li><a href="https://www.pmhealth.gov.pk" target="_blank" rel="noopener">pmhealth.gov.pk</a> — Prime Minister Health Programme</li>
                    </ul>
                    <p class="mb-0">Helpline: <strong>0800-09009</strong> (free). Details may change over time — confirm on the official website.</p>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">Step 3: How to get treatment at the hospital</h2>
                    <ol class="mb-0">
                        <li>After your eligibility is confirmed, go to the nearest <strong>panel hospital</strong>.</li>
                        <li>Take your <strong>original CNIC</strong> with you — show it at the Sehat Card counter.</li>
                        <li>The hospital's Sehat Card desk will verify your eligibility and admit you.</li>
                        <li>Treatment, medicines and required tests will be <strong>free</strong> within the program limit.</li>
                        <li>No bill to pay at discharge — the payment comes directly from the program.</li>
                    </ol>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">Important points</h2>
                    <ul class="mb-0">
                        <li>Sehat Card works only at <strong>panel hospitals</strong> — check the list first.</li>
                        <li>Each year there is a <strong>fixed limit</strong> for treatment (per family) — check with the helpline or website.</li>
                        <li>Cosmetic or unnecessary treatment is not included.</li>
                        <li>If any agent or middleman asks for a fee, complain on helpline 0800-09009.</li>
                    </ul>
                    <p class="text-muted small mt-3 mb-0">Disclaimer: This page is only for guidance and does no live verification. For the latest and final information, contact the official website or helpline.</p>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Write your CNIC above and press "Prepare SMS Text".</li>
                <li>Send the SMS to 8500 as shown above to confirm your eligibility.</li>
                <li>Find the panel hospital in your city on the official website.</li>
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
    var checkBtn = document.getElementById('checkBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var smsBox = document.getElementById('smsBox');

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }

    cnicInput.addEventListener('input', function () {
        cnicInput.value = cnicInput.value.replace(/[^0-9-]/g, '').slice(0, 15);
    });

    checkBtn.addEventListener('click', function () {
        hideError();
        var raw = cnicInput.value.trim();
        var digits = raw.replace(/-/g, '');
        if (!/^\d{13}$/.test(digits)) {
            showError('CNIC must have 13 digits (without dashes). Example: 3410198765432');
            return;
        }
        var pretty = digits.slice(0, 5) + '-' + digits.slice(5, 12) + '-' + digits.slice(12);
        smsBox.innerHTML = '';
        var strong1 = document.createElement('strong');
        strong1.textContent = 'SMS is ready: ';
        var span1 = document.createElement('span');
        span1.textContent = 'Recipient: 8500';
        var br = document.createElement('br');
        var span2 = document.createElement('span');
        span2.textContent = 'Message: ' + digits + '  (CNIC: ' + pretty + ')';
        smsBox.appendChild(strong1);
        smsBox.appendChild(span1);
        smsBox.appendChild(br);
        smsBox.appendChild(span2);
        results.classList.remove('d-none');
    });
})();
</script>
@endsection
