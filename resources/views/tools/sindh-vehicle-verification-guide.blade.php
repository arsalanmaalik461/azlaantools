@extends('layouts.app')
@section('title', 'Sindh Vehicle Verification Guide - Free | Azlaan Tools')
@section('meta_description', 'How to check Sindh vehicle registration and owner verification - official excise portal and SMS 8147. Free step-by-step guide.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Sindh Vehicle Verification Guide</h1>
            <p class="lead text-muted">The official way to verify your car or bike registration and owner in Sindh — through the Excise portal and SMS 8147. Step-by-step guide, totally free.</p>

            <div class="alert alert-warning" role="alert">
                <strong>Note:</strong> This is only a guide — there is no live verification on this page. The real record can only be confirmed through the official portal or SMS.
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">📩 Method 1: Verify by SMS 8147</h2>
                    <ol>
                        <li>Open the SMS app on your mobile.</li>
                        <li>Type your vehicle <strong>registration number</strong> (example: <code>KZ-1234</code>).</li>
                        <li>Send this number to <strong class="fs-5">8147</strong>.</li>
                        <li>You will get a reply shortly with the registered vehicle owner, engine number and chassis number.</li>
                    </ol>
                    <div class="mb-3">
                        <label for="regInput" class="form-label fw-semibold">Write your registration number (for copying)</label>
                        <input type="text" class="form-control" id="regInput" placeholder="e.g. KZ-1234" maxlength="20">
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-primary" id="copyBtn">📋 Copy Registration Number</button>
                        <button type="button" class="btn btn-outline-secondary" id="openSmsBtn">✉️ Open SMS App</button>
                    </div>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>
                    <div class="form-text mt-2">SMS charges may apply at your network normal rate. Write the number exactly as it appears on the registration card.</div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">🌐 Method 2: Official Excise Portal</h2>
                    <ol>
                        <li>Open the official website of the Sindh Excise, Taxation &amp; Narcotics Control Department: <a href="https://www.excise.gos.pk" target="_blank" rel="noopener">excise.gos.pk</a></li>
                        <li>Select the <strong>Vehicle Verification</strong> or online services section there.</li>
                        <li>Enter the registration number and submit — the record will appear on screen.</li>
                    </ol>
                    <a class="btn btn-outline-primary" href="https://www.excise.gos.pk" target="_blank" rel="noopener">Open excise.gos.pk</a>
                    <div class="form-text mt-2">Trust only the official domain — do not give your data on any third-party site.</div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">🏢 Method 3: Visit the Excise office yourself</h2>
                    <ul class="mb-0">
                        <li>Take your <strong>original registration book / smart card</strong>, CNIC and sale documents.</li>
                        <li>Get your record checked at the verification counter of the nearest Sindh Excise office (Karachi, Hyderabad, Sukkur etc.).</li>
                        <li>When buying a second-hand vehicle, always verify the owner name, dues and token tax.</li>
                    </ul>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">❓ Frequently asked questions</h2>
                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faqH1">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqC1" aria-expanded="false" aria-controls="faqC1">
                                    What should I do if I do not get a reply on SMS 8147?
                                </button>
                            </h2>
                            <div id="faqC1" class="accordion-collapse collapse" aria-labelledby="faqH1" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">Check the number spelling (try without the dash too, example: KZ1234), then send again. If you still do not get a reply, check on excise.gos.pk or contact the Excise office.</div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faqH2">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqC2" aria-expanded="false" aria-controls="faqC2">
                                    Can a vehicle from another province also be verified with 8147?
                                </button>
                            </h2>
                            <div id="faqC2" class="accordion-collapse collapse" aria-labelledby="faqH2" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">8147 is only for Sindh registered vehicles. For a Punjab vehicle there is the 8785 SMS system, and for an Islamabad vehicle 8528.</div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faqH3">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqC3" aria-expanded="false" aria-controls="faqC3">
                                    What must I verify when buying a second-hand vehicle?
                                </button>
                            </h2>
                            <div id="faqC3" class="accordion-collapse collapse" aria-labelledby="faqH3" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">The owner name, engine/chassis number, payment of token tax and dues, and the current transfer status. Do not pay any advance without verification.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="alert alert-info mt-4">
                <strong>Official links:</strong>
                <a href="https://www.excise.gos.pk" target="_blank" rel="noopener">excise.gos.pk</a> — Excise, Taxation &amp; Narcotics Control Dept., Government of Sindh. SMS short code: <strong>8147</strong>.
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Easiest: write your registration number above, copy it and SMS it to <strong>8147</strong>.</li>
                <li>Or open <strong>excise.gos.pk</strong> and enter the number in the online vehicle verification section.</li>
                <li>Match the owner, engine and chassis number in the record — if they do not match, contact the Excise office.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var regInput = document.getElementById('regInput');
    var copyBtn = document.getElementById('copyBtn');
    var openSmsBtn = document.getElementById('openSmsBtn');
    var errorBox = document.getElementById('errorBox');

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }

    copyBtn.addEventListener('click', function () {
        hideError();
        var v = regInput.value.trim();
        if (!v) {
            showError('Please enter your registration number first.');
            return;
        }
        function done() {
            copyBtn.textContent = '✓ Copied!';
            setTimeout(function () { copyBtn.textContent = '📋 Copy Registration Number'; }, 2000);
        }
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(v).then(done, function () {
                showError('Could not copy — select the number yourself and copy it.');
            });
        } else {
            regInput.select();
            try {
                document.execCommand('copy');
                done();
            } catch (e) {
                showError('Could not copy — select the number yourself and copy it.');
            }
        }
    });

    openSmsBtn.addEventListener('click', function () {
        hideError();
        var v = regInput.value.trim();
        // sms: URI opens the messaging app addressed to 8147 with the number pre-filled
        var uri = 'sms:8147' + (v ? '?body=' + encodeURIComponent(v) : '');
        window.location.href = uri;
    });
})();
</script>
@endsection
