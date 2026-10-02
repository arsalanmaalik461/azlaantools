@extends('layouts.app')

@section('title', 'Active Taxpayer Status Guide - Azlaan Tools')
@section('meta_description', 'Learn how to check your FBR Active Taxpayer List status by SMS to 9966 or the online portal. Free step-by-step guide.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Active Taxpayer Status Guide</h1>
            <p class="lead text-muted">The easy way to check your name in the FBR Active Taxpayer List (ATL) — by SMS to 9966 or the online portal.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="cnicInput" class="form-label fw-semibold">Enter your 13-digit CNIC (without dashes)</label>
                        <input type="text" class="form-control" id="cnicInput" inputmode="numeric" placeholder="e.g. 3520212345678" maxlength="15">
                        <div class="form-text">This tool does NOT check your status — it only builds the SMS text you need to send to 9966.</div>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Make SMS Text</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <h5>Your SMS</h5>
                        <p class="text-muted small">Copy the text below and SMS it to <strong>9966</strong>. The reply will give you your Active or Inactive status.</p>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" id="smsText" readonly>
                            <button type="button" class="btn btn-outline-secondary" id="copyBtn">Copy</button>
                        </div>
                        <div class="alert alert-info">
                            For a Company or AOP: write <strong>ATL</strong>, then the 7-digit NTN, and send to 9966.
                        </div>
                    </div>
                </div>
            </div>

            <h2>Online method (FBR portal)</h2>
            <ol>
                <li>Open <a href="https://www.fbr.gov.pk" target="_blank" rel="noopener">fbr.gov.pk</a> and go to the <strong>Active Taxpayer List (ATL)</strong> section, or use Online Verification at <a href="https://iris.fbr.gov.pk" target="_blank" rel="noopener">iris.fbr.gov.pk</a>.</li>
                <li>Select and enter your CNIC (13 digits, no dashes) or NTN.</li>
                <li>Enter the captcha code and click Verify.</li>
                <li>The result will show your name and Active/Inactive status.</li>
            </ol>

            <h2>SMS method (without internet)</h2>
            <ol>
                <li>On your mobile, type <strong>ATL</strong>, then a space, then your 13-digit CNIC (no dashes).</li>
                <li>Send it to <strong>9966</strong>.</li>
                <li>You will get a reply in a few moments telling you whether you are Active or not.</li>
            </ol>

            <h2>Benefits of becoming a filer</h2>
            <ul>
                <li><strong>Lower withholding tax</strong> on bank profit, property and vehicle purchase.</li>
                <li>The filer rate applies on bank and business payments.</li>
                <li>After filing the return, the ATL is usually updated within a few days.</li>
            </ul>

            <div class="alert alert-warning mt-4">
                <strong>Note:</strong> This is only a guide — this page does not verify your live status. Always confirm from SMS 9966 or the official FBR portals. Do not enter your CNIC on any non-government website.
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var cnicInput = document.getElementById('cnicInput');
    var smsText = document.getElementById('smsText');
    var copyBtn = document.getElementById('copyBtn');

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
        cnicInput.value = cnicInput.value.replace(/[^0-9]/g, '');
    });

    goBtn.addEventListener('click', function () {
        hideError();
        var cnic = cnicInput.value.replace(/[^0-9]/g, '');
        if (!/^[0-9]{13}$/.test(cnic)) {
            showError('Please enter a valid 13-digit CNIC without dashes.');
            return;
        }
        smsText.value = 'ATL ' + cnic;
        results.classList.remove('d-none');
    });

    copyBtn.addEventListener('click', function () {
        var txt = smsText.value;
        if (!txt) { return; }
        function done() {
            copyBtn.textContent = 'Copied!';
            setTimeout(function () { copyBtn.textContent = 'Copy'; }, 1500);
        }
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(txt).then(done).catch(function () { fallbackCopy(txt, done); });
        } else {
            fallbackCopy(txt, done);
        }
    });

    function fallbackCopy(txt, done) {
        smsText.select();
        try { document.execCommand('copy'); } catch (e) { /* ignore */ }
        done();
    }
})();
</script>
@endsection
