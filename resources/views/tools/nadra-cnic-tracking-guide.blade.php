@extends('layouts.app')

@section('title', 'NADRA CNIC Tracking Guide - Azlaan Tools')
@section('meta_description', 'Full guide to check your CNIC or NICOP application status via SMS 8400, PakID app or nadra.gov.pk — free.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">NADRA CNIC Tracking Guide</h1>
            <p class="lead text-muted">Want to check your CNIC / NICOP application status? There are three methods — all official. This tool does no live verification; it only shows the correct method.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="mb-3">SMS Composer — prepare the message to send to 8400</h5>
                    <div class="mb-3">
                        <label for="trackId" class="form-label fw-semibold">Application Tracking ID (number written on the token)</label>
                        <input type="text" class="form-control" id="trackId" placeholder="e.g. 421015678901">
                        <div class="form-text">Enter the application or tracking ID written on the token/receipt. Send it from the mobile number you gave on the NADRA online application.</div>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Prepare SMS Message</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title">Copy your message and SMS it to 8400</h6>
                                <div class="d-flex align-items-center gap-2">
                                    <code id="smsText" class="fs-5 p-2 bg-white border rounded flex-grow-1"></code>
                                    <button type="button" class="btn btn-outline-primary btn-sm" id="copyBtn">Copy</button>
                                </div>
                                <p class="mb-0 mt-2 small text-muted">Send only from the SIM registered in your name. The reply SMS will contain the current status of your application.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <h2>Method 1 — SMS (8400)</h2>
            <ol>
                <li>Note down the <strong>tracking ID</strong> written on the application token/receipt.</li>
                <li>Send that tracking ID by SMS to <strong>8400</strong> from your registered mobile.</li>
                <li>The reply SMS will show the card status (print, dispatch or ready for collection).</li>
            </ol>

            <h2>Method 2 — PakID App</h2>
            <ol>
                <li>Download the <strong>PakID</strong> app from the Play Store / App Store (only the official publisher).</li>
                <li>Make an account with your CNIC number and mobile number.</li>
                <li>Enter the tracking ID in "Track Application" to see the status, and you can also apply for a new CNIC/NICOP.</li>
            </ol>

            <h2>Method 3 — NADRA Website</h2>
            <ol>
                <li>Open <a href="https://www.nadra.gov.pk" target="_blank" rel="noopener">nadra.gov.pk</a>.</li>
                <li>Enter your tracking ID in the CNIC services / tracking section.</li>
                <li>You will also see the expected delivery time along with the status.</li>
            </ol>

            <h2>Important notes</h2>
            <ul>
                <li>Online apply for NICOP or POC is done from <a href="https://www.nadra.gov.pk" target="_blank" rel="noopener">nadra.gov.pk</a>.</li>
                <li>If the status is delayed, visit the nearest NADRA center — they can check with the token number.</li>
                <li>Rates may change — confirm on the official website.</li>
            </ul>
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
        var v = document.getElementById('trackId').value.trim().replace(/[\s-]/g, '');
        if (!v) { showError('Please enter a value.'); return; }
        if (!/^[0-9]{5,20}$/.test(v)) {
            showError('Enter the tracking ID in digits only — copy the number written on the token/receipt.');
            return;
        }
        document.getElementById('smsText').textContent = v;
        results.classList.remove('d-none');
    });

    document.getElementById('copyBtn').addEventListener('click', function () {
        var txt = document.getElementById('smsText').textContent;
        function done() { this.textContent = 'Copied!'; var b = this; setTimeout(function(){ b.textContent = 'Copy'; }, 1500); }
        var b = this;
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(txt).then(function(){ done.call(b); });
        } else {
            var ta = document.createElement('textarea');
            ta.value = txt; document.body.appendChild(ta); ta.select();
            try { document.execCommand('copy'); done.call(b); } catch (e) { showError('Could not copy — write the message yourself.'); }
            document.body.removeChild(ta);
        }
    });
})();
</script>
@endsection
