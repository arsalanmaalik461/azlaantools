@extends('layouts.app')
@section('title', 'Passport Tracking Guide Pakistan - 9988 SMS Status Check | Azlaan Tools')
@section('meta_description', 'Check your passport application status in Pakistan with your token number: SMS to 9988, the dgip.gov.pk portal and the helpline. Free guide.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-2">Passport Tracking Guide</h1>
            <p class="lead text-muted">An easy way to check your passport application status with your token number — SMS to 9988, the official portal and the helpline.</p>
            <div class="alert alert-warning"><strong>Note:</strong> this is a guide — this page does not check live status itself. You get the status from the SMS reply or the official website.</div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5">Step 1: Keep your token number ready</h2>
                    <p class="text-muted">The token number is 11 digits long and is written on your passport office receipt. Enter it below — the tool will check that the number is in the right format and prepare the SMS for you.</p>
                    <div class="mb-3">
                        <label for="tokenInput" class="form-label fw-semibold">Token Number (11 digits)</label>
                        <input type="text" class="form-control form-control-lg" id="tokenInput" placeholder="e.g. 12345678901" maxlength="11" inputmode="numeric">
                        <div class="form-text">Enter only the 11-digit value written as "Token Number" on the receipt.</div>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="checkBtn">Prepare SMS</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="alert alert-success">
                            <strong>Token number format is correct.</strong><br>
                            Now send this SMS to <strong>9988</strong> — you will get the status reply in a few moments.
                        </div>
                        <div class="card bg-light mb-3">
                            <div class="card-body">
                                <div class="small text-muted mb-1">SMS text (copy it and send to 9988):</div>
                                <p class="fs-4 fw-bold mb-2 font-monospace" id="smsText">-</p>
                                <div class="d-flex gap-2 flex-wrap">
                                    <a href="#" id="smsLink" class="btn btn-success">Open in SMS App</a>
                                    <button type="button" id="copyBtn" class="btn btn-outline-secondary">Copy</button>
                                </div>
                                <div class="small text-muted mt-2" id="copyMsg"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <h2>Method 1: Tracking by SMS (fastest)</h2>
            <ol>
                <li>Write a new SMS in your phone messaging app.</li>
                <li>Type only your <strong>11-digit token number</strong> — no extra words.</li>
                <li>Send it to <strong>9988</strong>.</li>
                <li>You will get the status reply SMS in a few moments.</li>
            </ol>
            <p class="text-muted small">SMS charges apply at your mobile network normal rate. No internet needed — it works on any phone.</p>

            <h2>Method 2: On the official website</h2>
            <ol>
                <li>Open <a href="https://dgip.gov.pk" target="_blank" rel="noopener">dgip.gov.pk</a> — the official website of the Directorate General of Immigration and Passports.</li>
                <li>On the homepage, select the <strong>"Track Your Application"</strong> option.</li>
                <li>Enter your token number (and your registered mobile number if asked).</li>
                <li>The current stage of your application will appear on the screen.</li>
            </ol>

            <h2>Method 3: Call the helpline</h2>
            <p>Call the DGIP helpline <strong>051-111-344-777</strong> and tell them your token number — the agent will tell you the status. You can also get information with your CNIC.</p>

            <h2>Common status meanings</h2>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light"><tr><th>Status</th><th>Meaning</th></tr></thead>
                    <tbody>
                        <tr><td>Under Process / In Process</td><td>Work on your application is in progress — printing or verification is happening.</td></tr>
                        <tr><td>Ready for Collection / Ready for Delivery</td><td>Your passport is ready — collect it from the office or wait for dispatch.</td></tr>
                        <tr><td>Dispatched</td><td>Your passport has been sent by courier or post.</td></tr>
                        <tr><td>No Record Found</td><td>The token number is wrong or the system has not updated yet — try again after 24 hours.</td></tr>
                    </tbody>
                </table>
            </div>

            <h2>Important points</h2>
            <ul>
                <li>Keep your token receipt safe — it is the key to tracking.</li>
                <li>If you lose the token slip, visit your nearest passport office with your CNIC.</li>
                <li>When you get the "Ready for Collection" notice, collect your passport within 30 days.</li>
                <li>Never share your token or CNIC on unofficial tracking websites.</li>
                <li>Fees and timing change over time — confirm on the <a href="https://dgip.gov.pk" target="_blank" rel="noopener">official website</a>.</li>
            </ul>
            <div class="alert alert-info mt-4"><strong>Disclaimer:</strong> this is an information guide, not a government service. Always confirm the status through official channels (9988 SMS or dgip.gov.pk).</div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var tokenInput = document.getElementById('tokenInput');
    var checkBtn = document.getElementById('checkBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var smsText = document.getElementById('smsText');
    var smsLink = document.getElementById('smsLink');
    var copyBtn = document.getElementById('copyBtn');
    var copyMsg = document.getElementById('copyMsg');

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }

    tokenInput.addEventListener('input', function () {
        tokenInput.value = tokenInput.value.replace(/[^0-9]/g, '').slice(0, 11);
    });

    checkBtn.addEventListener('click', function () {
        hideError();
        copyMsg.textContent = '';
        var v = tokenInput.value.trim();
        if (!v) { showError('Please enter your token number first.'); return; }
        if (!/^[0-9]{11}$/.test(v)) {
            showError('Token number must be 11 digits. You entered ' + v.length + ' digits — please check your receipt.');
            return;
        }
        smsText.textContent = v;
        smsLink.href = 'sms:9988?body=' + encodeURIComponent(v);
        results.classList.remove('d-none');
        results.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    });

    copyBtn.addEventListener('click', function () {
        var v = smsText.textContent;
        function done() { copyMsg.textContent = 'Copied — now paste it into an SMS to 9988 and send.'; }
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(v).then(done, function () { fallback(); });
        } else { fallback(); }
        function fallback() {
            var ta = document.createElement('textarea');
            ta.value = v;
            document.body.appendChild(ta);
            ta.select();
            try { document.execCommand('copy'); done(); }
            catch (e) { copyMsg.textContent = 'Copy failed — please type the number yourself: ' + v; }
            document.body.removeChild(ta);
        }
    });
})();
</script>
@endsection
