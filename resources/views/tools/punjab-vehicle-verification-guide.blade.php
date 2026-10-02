@extends('layouts.app')

@section('title', 'Punjab Vehicle Verification Guide - Azlaan Tools')
@section('meta_description', 'Verify a car or bike in Punjab via the MTMIS portal and SMS 8785 before buying. Free guide.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Punjab Vehicle Verification Guide</h1>
            <p class="lead text-muted">Before buying a used car or bike, always check its record — stay safe from stolen vehicles, duplicate papers or unpaid token tax.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">Check via SMS 8785</h2>
                    <p class="text-muted">Type your vehicle number below, then press <strong>Check Number</strong>. We will prepare the SMS text for you to send to <strong>8785</strong>.</p>
                    <div class="mb-3">
                        <label for="vehNum" class="form-label fw-semibold">Vehicle Registration Number</label>
                        <input type="text" class="form-control" id="vehNum" placeholder="e.g. LEA-20-1234 or ICT-123">
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Check Number</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="alert alert-success">
                            <p class="mb-2 fw-semibold">Open your SMS app and send this text to <strong>8785</strong>:</p>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <code id="smsText" class="fs-5 p-2 bg-light border rounded"></code>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="copySms">Copy</button>
                                <a href="#" class="btn btn-sm btn-success" id="sendSms">Open SMS App</a>
                            </div>
                            <small class="text-muted d-block mt-2">In the reply you will get the vehicle owner name, model, engine/chassis number and tax status. SMS charges may apply as per your operator.</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">Check on the online MTMIS portal</h2>
                    <ol class="mb-0">
                        <li class="mb-2">Open the <strong>Punjab Excise MTMIS portal</strong>: <a href="https://mtmis.excise.punjab.gov.pk" target="_blank" rel="noopener">mtmis.excise.punjab.gov.pk</a></li>
                        <li class="mb-2">Type your vehicle number in the Vehicle Number field (it works without dashes too).</li>
                        <li class="mb-2">Press <strong>Search</strong> — the owner name, registration date, token tax status and engine/chassis number will show.</li>
                        <li class="mb-2">Match the record shown on screen with the seller documents (registration book, smart card).</li>
                    </ol>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">Checklist before buying</h2>
                    <ul class="mb-0">
                        <li class="mb-1">Match the engine number and chassis number on the physical vehicle with the documents.</li>
                        <li class="mb-1">Token tax must be paid — unpaid tax is transferred to the buyer.</li>
                        <li class="mb-1">Always do the biometric transfer from an Excise office or facilitation center.</li>
                        <li class="mb-1">Avoid buying a vehicle on open transfer / open letter.</li>
                    </ul>
                </div>
            </div>

            <h2>Official Links</h2>
            <ul>
                <li><a href="https://mtmis.excise.punjab.gov.pk" target="_blank" rel="noopener">MTMIS Punjab — Vehicle Verification</a></li>
                <li><a href="https://excise.punjab.gov.pk" target="_blank" rel="noopener">Excise &amp; Taxation Department Punjab</a></li>
            </ul>
            <p class="small text-muted">Note: This page is a guide and does not do live verification. Always check the record on the official MTMIS portal or via 8785 SMS.</p>

            <h2>How to use</h2>
            <ol>
                <li>Type your vehicle number in the box above and press Check Number.</li>
                <li>Click Open SMS App — the number and text will already be set, just press send.</li>
                <li>Match the reply record with the seller documents.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var goBtn = document.getElementById('goBtn');
    var vehNum = document.getElementById('vehNum');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var smsText = document.getElementById('smsText');
    var copySms = document.getElementById('copySms');
    var sendSms = document.getElementById('sendSms');

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function normalizeNumber(raw) {
        return raw.toUpperCase().replace(/[^A-Z0-9]/g, '').replace(/\s+/g, '');
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var raw = vehNum.value.trim();
        if (!raw) { showError('Please enter your vehicle number.'); return; }
        var num = normalizeNumber(raw);
        if (num.length < 5) { showError('The number is too short. Please write the correct registration number.'); return; }
        var display = raw.toUpperCase().trim();
        smsText.textContent = display;
        sendSms.href = 'sms:8785?body=' + encodeURIComponent(display);
        results.classList.remove('d-none');
    });

    copySms.addEventListener('click', function () {
        var t = smsText.textContent;
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(t).then(function () {
                copySms.textContent = 'Copied!';
                setTimeout(function () { copySms.textContent = 'Copy'; }, 1500);
            });
        } else {
            var ta = document.createElement('textarea');
            ta.value = t;
            document.body.appendChild(ta);
            ta.select();
            try { document.execCommand('copy'); copySms.textContent = 'Copied!'; } catch (e) { /* noop */ }
            document.body.removeChild(ta);
            setTimeout(function () { copySms.textContent = 'Copy'; }, 1500);
        }
    });
})();
</script>
@endsection
