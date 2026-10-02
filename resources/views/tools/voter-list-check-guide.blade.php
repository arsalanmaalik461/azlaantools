@extends('layouts.app')
@section('title', 'Voter List Check Guide Pakistan — Azlaan Tools')
@section('meta_description', 'Learn how to check your vote in Pakistan: SMS your CNIC to 8300, find your polling station and block code. Step-by-step guide with official ECP links.')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Voter List Check Guide</h1>
            <p class="lead text-muted">Checking your vote is very easy — send an SMS with your CNIC number or check on the ECP website. The step-by-step method is given below.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">CNIC format helper (format check only)</h2>
                    <div class="mb-3">
                        <label for="cnicInput" class="form-label fw-semibold">Enter your CNIC number (without dashes)</label>
                        <input type="text" class="form-control" id="cnicInput" placeholder="e.g. 3520212345678" maxlength="15" inputmode="numeric">
                        <div class="form-text">Example: 35202-1234567-8. This tool only checks the number format — it does not verify your vote.</div>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Check CNIC Format &amp; Get SMS Text</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="alert alert-success" id="formatMsg"></div>
                        <p class="fw-semibold mb-2">SMS text (copy it and send to 8300):</p>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" id="smsText" readonly>
                            <button type="button" class="btn btn-outline-secondary" id="copyBtn">Copy</button>
                        </div>
                        <p class="text-muted small">In the reply you will get: your constituency (electoral area), block code, serial number, and the name/address of your polling station.</p>
                    </div>
                </div>
            </div>

            <h2>Ways to check your vote</h2>
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <h3 class="h5">1. By SMS (easiest)</h3>
                    <ol>
                        <li>Open the message app on your mobile.</li>
                        <li>Type your 13-digit CNIC number (without dashes), for example <strong>3520212345678</strong>.</li>
                        <li>Send it to <strong>8300</strong>.</li>
                        <li>You will get a reply in a short time — it will have your constituency, block code and polling station.</li>
                    </ol>
                    <p class="text-muted small mb-0">Note: SMS charges may apply based on your mobile package.</p>
                </div>
            </div>

            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <h3 class="h5">2. On the official ECP website</h3>
                    <ol>
                        <li>Open the Election Commission of Pakistan website: <a href="https://www.ecp.gov.pk" target="_blank" rel="noopener">www.ecp.gov.pk</a>.</li>
                        <li>There, open the voter verification / electoral rolls section and enter your CNIC.</li>
                        <li>Your vote details will appear on the screen.</li>
                    </ol>
                </div>
            </div>

            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <h3 class="h5">3. ECP helpline</h3>
                    <p class="mb-1">To get help, contact the ECP helpline:</p>
                    <ul class="mb-0">
                        <li>Helpline: <strong>051-111-327-000</strong></li>
                        <li>Website: <a href="https://www.ecp.gov.pk" target="_blank" rel="noopener">www.ecp.gov.pk</a></li>
                    </ul>
                </div>
            </div>

            <div class="alert alert-warning">
                <strong>Important note:</strong> This page is only a guide — this tool does <strong>not verify</strong> your vote from ECP records. To truly confirm your vote, send an SMS to 8300 or use the official ECP website.
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Enter your CNIC number above and check the format.</li>
                <li>Press <strong>Copy</strong> and paste the number into your phone's message box.</li>
                <li>Send it to 8300 — you will get the polling station and block code in the reply.</li>
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
    var formatMsg = document.getElementById('formatMsg');

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }

    function digitsOnly(s) {
        var out = '';
        for (var i = 0; i < s.length; i++) {
            var c = s.charAt(i);
            if (c >= '0' && c <= '9') { out += c; }
        }
        return out;
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var raw = cnicInput.value.trim();
        var digits = digitsOnly(raw);
        if (digits.length === 0) {
            showError('Please enter your CNIC number first.');
            return;
        }
        if (digits.length !== 13) {
            showError('Your CNIC must have 13 digits — you entered ' + digits.length + ' digits. Please check again.');
            return;
        }
        formatMsg.textContent = 'Format is correct: ' + digits.slice(0, 5) + '-' + digits.slice(5, 12) + '-' + digits.slice(12) + '. Now send this number by SMS to 8300.';
        smsText.value = digits;
        results.classList.remove('d-none');
    });

    copyBtn.addEventListener('click', function () {
        smsText.select();
        try {
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(smsText.value);
            } else {
                document.execCommand('copy');
            }
            copyBtn.textContent = 'Copied!';
            setTimeout(function () { copyBtn.textContent = 'Copy'; }, 2000);
        } catch (e) {
            document.execCommand('copy');
        }
    });
})();
</script>
@endsection
