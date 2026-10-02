@extends('layouts.app')
@section('title', 'Ehsaas 8171 Eligibility Guide - Azlaan Tools')
@section('meta_description', 'How to check Ehsaas or BISP eligibility on 8171 with your CNIC, official portals, and tips to avoid fraud — free guide.')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Ehsaas 8171 Eligibility Guide</h1>
            <p class="lead text-muted">The right way to check Ehsaas or BISP (Benazir Income Support Programme) eligibility on 8171 — through official channels only, and how to stay safe from fake websites.</p>

            <div class="alert alert-info">
                <strong>Important:</strong> This tool does not check eligibility live. The real check is done only through 8171 SMS or the official portal. Our information is only a guide.
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">Step 1 — Get your CNIC ready (correct format)</h2>
                    <p>Write your 13-digit CNIC number without dashes, like <code>3520112345678</code>.</p>
                    <div class="mb-3">
                        <label for="cnicInput" class="form-label fw-semibold">Check your CNIC number</label>
                        <input type="text" class="form-control" id="cnicInput" placeholder="e.g. 3520112345678" inputmode="numeric" maxlength="15">
                        <div class="form-text">This is only a format check — only 8171 or the official portal decides eligibility.</div>
                    </div>
                    <div class="alert alert-danger d-none" id="errorBox" role="alert"></div>
                    <div id="results" class="d-none"></div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Check CNIC Format</button>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">Step 2 — Send an SMS to 8171</h2>
                    <ol>
                        <li>In your mobile message app, write <strong>your 13-digit CNIC number</strong> (no dashes).</li>
                        <li>Send it to <strong>8171</strong>.</li>
                        <li>The reply SMS will tell you your eligibility or status. The reply can take a little time.</li>
                    </ol>
                    <p class="mb-1 fw-semibold">If you are on your mobile, the button below can open the SMS app:</p>
                    <a class="btn btn-outline-success mb-3" id="smsBtn" href="sms:8171">Open SMS to 8171</a>
                    <div class="alert alert-warning mb-0"><strong>Remember:</strong> An SMS to 8171 is completely free — there is no charge. If any agent or shopkeeper asks you for money for this, that is wrong.</div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">Step 3 — Official portal (online check)</h2>
                    <p>Only check online on the official website:</p>
                    <ul>
                        <li><strong>8171 BISP Portal:</strong> <a href="https://8171.bisp.gov.pk" target="_blank" rel="noopener">8171.bisp.gov.pk</a> — enter your CNIC to see your eligibility.</li>
                        <li><strong>BISP main website:</strong> <a href="https://bisp.gov.pk" target="_blank" rel="noopener">bisp.gov.pk</a> — programme details and helpline.</li>
                    </ul>
                    <p class="text-muted small">These links are official government domains (.gov.pk). We do not claim that this tool itself does live verification.</p>
                </div>
            </div>

            <div class="card shadow-sm mb-4 border-danger">
                <div class="card-body">
                    <h2 class="h5 mb-3 text-danger">⚠️ Beware of fraud</h2>
                    <ul class="mb-0">
                        <li>Ehsaas/BISP <strong>registration and eligibility check are completely free</strong> — there are no fees, tokens, or "form charges".</li>
                        <li>Any websites, Facebook pages or WhatsApp numbers that ask you for money, bank details or OTP are <strong>fake</strong>.</li>
                        <li>Enter your CNIC and mobile number only on official 8171 or the official portal, nowhere else.</li>
                        <li>No BISP agent will come to your home to ask for money. To complain, contact the official helpline <strong>0800-26477</strong>.</li>
                    </ul>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Enter your number in the CNIC format check above so you do not send a wrong SMS.</li>
                <li>Send your CNIC to 8171 by SMS or check on the official portal.</li>
                <li>Do not give money to any fake website or agent.</li>
            </ol>
            <p class="text-muted small">Disclaimer: This guide is only for information. Only BISP and official channels decide eligibility. Rates and methods can change over time — confirm on the official website.</p>
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
    var smsBtn = document.getElementById('smsBtn');

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
        results.classList.add('d-none');
        var v = cnicInput.value.replace(/[-\s]/g, '').trim();
        if (!v) { showError('Please enter your CNIC number first.'); return; }
        if (!/^\d{13}$/.test(v)) {
            showError('The CNIC must be 13 digits (no dashes). You entered ' + v.length + ' digits.');
            return;
        }
        var masked = v.slice(0, 5) + '-XXXXXXX-' + v.slice(12);
        smsBtn.href = 'sms:8171?body=' + v;
        results.classList.remove('d-none');
        results.innerHTML =
            '<div class="alert alert-success mt-3"><strong>Format is correct:</strong> ' + masked +
            '<br>Now send this number (no dashes: <code>' + v + '</code>) to 8171 by SMS, or check it on the <a href="https://8171.bisp.gov.pk" target="_blank" rel="noopener">official portal</a>.' +
            '<br><span class="small">Remember: this was only a format check — only the official channel decides eligibility.</span></div>';
    });
})();
</script>
@endsection
