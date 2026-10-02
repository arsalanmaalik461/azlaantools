@extends('layouts.app')

@section('title', 'Invoice Payment QR Code - Azlaan Tools')
@section('meta_description', 'Free payment QR code for invoices. Make a QR for Easypaisa, JazzCash or a bank account so your client can pay easily. No signup.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Invoice Payment QR Code</h1>
            <p class="lead text-muted">A payment QR to put on your invoice — the client scans it and pays to your Easypaisa, JazzCash or bank account. Your data stays in your browser only, nothing is uploaded.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="qrTitle" class="form-label fw-semibold">Account title (name)</label>
                            <input type="text" class="form-control" id="qrTitle" placeholder="e.g. Azlaan Electric AC Solar Center">
                        </div>
                        <div class="col-md-6">
                            <label for="qrMethod" class="form-label fw-semibold">Payment method</label>
                            <select class="form-select" id="qrMethod">
                                <option value="Easypaisa">Easypaisa</option>
                                <option value="JazzCash">JazzCash</option>
                                <option value="Bank">Bank account (IBAN)</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="qrNumber" class="form-label fw-semibold">Number / IBAN</label>
                            <input type="text" class="form-control" id="qrNumber" placeholder="e.g. 0300-1234567 or PK36...">
                        </div>
                        <div class="col-md-6">
                            <label for="qrAmount" class="form-label fw-semibold">Amount (optional)</label>
                            <input type="number" class="form-control" id="qrAmount" min="0" step="0.01" placeholder="Leave empty if the amount is different for every invoice">
                        </div>
                        <div class="col-md-6">
                            <label for="qrRef" class="form-label fw-semibold">Reference (optional)</label>
                            <input type="text" class="form-control" id="qrRef" placeholder="e.g. Invoice number">
                        </div>
                        <div class="col-md-6">
                            <label for="qrSize" class="form-label fw-semibold">QR size</label>
                            <select class="form-select" id="qrSize">
                                <option value="200">Medium (200 x 200)</option>
                                <option value="300" selected>Large (300 x 300)</option>
                                <option value="512">Print (512 x 512)</option>
                            </select>
                        </div>
                    </div>
                    <div class="alert alert-danger mt-3 d-none" id="qrErrorBox" role="alert"></div>
                    <button type="button" class="btn btn-primary w-100 mt-3" id="qrGenBtn">Make QR Code</button>
                </div>
            </div>

            <div class="card shadow-sm mb-4 d-none" id="qrResultCard">
                <div class="card-body text-center">
                    <h2 class="h5 mb-3">Your payment QR</h2>
                    <div id="qrOutput" class="d-inline-block border rounded p-3 bg-white"></div>
                    <div class="mt-3">
                        <div class="small text-muted mb-1">The QR contains these details:</div>
                        <code id="qrPayloadText" class="d-block text-start bg-light p-2 rounded small"></code>
                    </div>
                    <div class="d-flex gap-2 justify-content-center mt-3 flex-wrap">
                        <button type="button" class="btn btn-success" id="qrDownloadBtn">Download PNG</button>
                        <button type="button" class="btn btn-outline-secondary" id="qrCopyBtn">Copy details</button>
                    </div>
                    <p class="small text-muted mt-3 mb-0">Stick this QR image on the print of the invoice made with the Invoice Maker and send it to your client.</p>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Enter the account title, method (Easypaisa / JazzCash / Bank) and number.</li>
                <li>If the QR is always for the same amount, enter the amount — otherwise leave it empty.</li>
                <li>Press <strong>Make QR Code</strong>, then save the image with <strong>Download PNG</strong>.</li>
            </ol>
            <p class="small text-muted">Note: this QR only stores the payment details (account number etc.) — in Easypaisa/JazzCash apps the number must be entered manually. Your data stays in your browser only.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
(function () {
    'use strict';
    var $ = function (id) { return document.getElementById(id); };
    var qrTitle = $('qrTitle'), qrMethod = $('qrMethod'), qrNumber = $('qrNumber'),
        qrAmount = $('qrAmount'), qrRef = $('qrRef'), qrSize = $('qrSize'),
        errorBox = $('qrErrorBox'), resultCard = $('qrResultCard'),
        qrOutput = $('qrOutput'), qrPayloadText = $('qrPayloadText');

    function showError(msg) { errorBox.textContent = msg; errorBox.classList.remove('d-none'); }
    function hideError() { errorBox.classList.add('d-none'); errorBox.textContent = ''; }
    function esc(s) {
        return String(s == null ? '' : s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    function buildPayload() {
        var lines = [];
        lines.push('PAY TO: ' + qrTitle.value.trim());
        lines.push(qrMethod.value + ': ' + qrNumber.value.trim());
        var amt = Number(qrAmount.value) || 0;
        if (amt > 0) lines.push('AMOUNT: Rs ' + amt.toLocaleString('en-PK'));
        if (qrRef.value.trim()) lines.push('REF: ' + qrRef.value.trim());
        return lines.join('\n');
    }

    $('qrGenBtn').addEventListener('click', function () {
        hideError();
        if (!qrTitle.value.trim()) { showError('Please enter the account title.'); return; }
        if (!qrNumber.value.trim()) { showError('Please enter the number / IBAN.'); return; }
        if (typeof QRCode === 'undefined') {
            showError('The QR library could not be loaded — please check your internet and try again.');
            return;
        }
        var payload = buildPayload();
        qrOutput.innerHTML = '';
        var size = Number(qrSize.value) || 300;
        try {
            new QRCode(qrOutput, {
                text: payload,
                width: size,
                height: size,
                correctLevel: QRCode.CorrectLevel.M
            });
        } catch (e) {
            showError('There was a problem making the QR. Please try again.');
            return;
        }
        qrPayloadText.textContent = payload;
        resultCard.classList.remove('d-none');
        resultCard.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });

    $('qrDownloadBtn').addEventListener('click', function () {
        hideError();
        var canvas = qrOutput.querySelector('canvas');
        var img = qrOutput.querySelector('img');
        var url = null;
        if (canvas) { url = canvas.toDataURL('image/png'); }
        else if (img && img.src) { url = img.src; }
        if (!url) { showError('Please make the QR code first.'); return; }
        var a = document.createElement('a');
        a.href = url;
        a.download = 'payment-qr.png';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    });

    $('qrCopyBtn').addEventListener('click', function () {
        hideError();
        var text = qrPayloadText.textContent;
        if (!text) { showError('Please make the QR code first.'); return; }
        var done = function () {
            $('qrCopyBtn').textContent = 'Copied ✓';
            setTimeout(function () { $('qrCopyBtn').textContent = 'Copy details'; }, 2000);
        };
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(done, function () { showError('Could not copy.'); });
        } else {
            var ta = document.createElement('textarea');
            ta.value = text;
            document.body.appendChild(ta);
            ta.select();
            try { document.execCommand('copy'); done(); }
            catch (e) { showError('Could not copy.'); }
            document.body.removeChild(ta);
        }
    });
})();
</script>
@endsection
