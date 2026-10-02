@extends('layouts.app')
@section('title', 'PTA Mobile Registration Guide - Azlaan Tools')
@section('meta_description', 'Step-by-step guide to register a mobile brought from abroad in the PTA DIRBS system, with IMEI check and tax payment method.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">PTA Mobile Registration Guide</h1>
            <p class="lead text-muted">To use a mobile brought from abroad in Pakistan, it must be registered in the PTA DIRBS system. Check your IMEI below and read the step-by-step method.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">IMEI Number Check</h2>
                    <div class="mb-3">
                        <label for="imei" class="form-label fw-semibold">Enter your mobile IMEI (15 digits)</label>
                        <input type="text" class="form-control" id="imei" placeholder="e.g. 352099001761481" maxlength="15" inputmode="numeric">
                        <div class="form-text">You can see your IMEI by dialing <code>*#06#</code>. If your phone has dual SIM, check both.</div>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Check IMEI</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="alert alert-success d-none" id="imeiOk" role="alert"></div>
                        <div class="alert alert-warning d-none" id="imeiBad" role="alert"></div>
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0">
                                <tbody>
                                    <tr><th style="width:40%">TAC (first 8 digits)</th><td id="tacCell"></td></tr>
                                    <tr><th>Serial (next 6 digits)</th><td id="serialCell"></td></tr>
                                    <tr><th>Check digit (last digit)</th><td id="checkCell"></td></tr>
                                    <tr><th>Luhn validation</th><td id="luhnCell"></td></tr>
                                </tbody>
                            </table>
                        </div>
                        <p class="small text-muted mt-2 mb-0">This check only validates the IMEI format. Whether it is registered or PTA-approved can only be confirmed on the official DIRBS portal or by SMS to <strong>8484</strong>.</p>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5">Step by Step: PTA Registration (DIRBS)</h2>
                    <ol>
                        <li><strong>Verify your IMEI:</strong> send your mobile <code>IMEI</code> (15 digits) by SMS to <strong>8484</strong>, or check on the official portal <a href="https://dirbs.pta.gov.pk" target="_blank" rel="noopener">dirbs.pta.gov.pk</a> to check. The result will show whether the device is PTA-compliant.</li>
                        <li><strong>Create a DIRBS account:</strong> go to <a href="https://dirbs.pta.gov.pk" target="_blank" rel="noopener">dirbs.pta.gov.pk</a> and register your account (your CNIC/NICOP and passport number are required if the mobile was brought from abroad).</li>
                        <li><strong>Register the device:</strong> log in, enter your mobile IMEI under the "Individual/Traveler" option and give your passport/travel details.</li>
                        <li><strong>Pay the tax/fee:</strong> the system will show the tax based on your mobile price. Submit the challan at a bank or through online banking. <span class="text-danger">Rates can change — confirm on the official website.</span></li>
                        <li><strong>Wait for confirmation:</strong> once the payment is verified, the device becomes PTA-registered and starts working with Pakistani SIMs.</li>
                    </ol>
                    <p class="small text-muted mb-1"><strong>Important points:</strong></p>
                    <ul class="small text-muted">
                        <li>Each person can bring one device duty-free per year (traveler quota) — rules change often, so check the official site.</li>
                        <li>Never buy a mobile with a patched or cloned IMEI — it can be blocked.</li>
                        <li>Official links: <a href="https://www.pta.gov.pk" target="_blank" rel="noopener">pta.gov.pk</a> aur <a href="https://dirbs.pta.gov.pk" target="_blank" rel="noopener">dirbs.pta.gov.pk</a>. Never enter your CNIC or payment details on any other site.</li>
                    </ul>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Enter your IMEI number above to check the format.</li>
                <li>Read the step-by-step guide and register on the DIRBS portal.</li>
                <li>Always confirm the final tax and status on the official portal or by SMS to 8484.</li>
            </ol>
            <p class="small text-muted">Rates can change — confirm on the official website. This tool does not provide live verification.</p>
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
    var imeiOk = document.getElementById('imeiOk');
    var imeiBad = document.getElementById('imeiBad');

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }

    function luhnValid(s) {
        var sum = 0;
        for (var i = 0; i < 15; i++) {
            var d = parseInt(s.charAt(14 - i), 10);
            if (i % 2 === 1) { d *= 2; if (d > 9) d -= 9; }
            sum += d;
        }
        return sum % 10 === 0;
    }

    goBtn.addEventListener('click', function () {
        hideError();
        imeiOk.classList.add('d-none');
        imeiBad.classList.add('d-none');
        var v = document.getElementById('imei').value.replace(/\D/g, '');
        if (v.length !== 15) { showError('Please enter a 15-digit IMEI.'); return; }
        document.getElementById('tacCell').textContent = v.slice(0, 8);
        document.getElementById('serialCell').textContent = v.slice(8, 14);
        document.getElementById('checkCell').textContent = v.charAt(14);
        if (luhnValid(v)) {
            document.getElementById('luhnCell').textContent = 'Valid (correct format)';
            imeiOk.textContent = 'The IMEI format is correct. Registration or block status can only be checked on dirbs.pta.gov.pk or by SMS to 8484.';
            imeiOk.classList.remove('d-none');
        } else {
            document.getElementById('luhnCell').textContent = 'Invalid (wrong format)';
            imeiBad.textContent = 'This IMEI number looks wrong (the check digit does not match). Dial *#06# on your mobile and check again.';
            imeiBad.classList.remove('d-none');
        }
        results.classList.remove('d-none');
    });
})();
</script>
@endsection
