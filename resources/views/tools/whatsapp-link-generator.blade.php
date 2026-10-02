@extends('layouts.app')

@section('title', 'WhatsApp Link Generator - Azlaan Tools')
@section('meta_description', 'Free WhatsApp link generator for Pakistan. Create a wa.me chat link with a prefilled message, plus a QR code, from any mobile number.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">WhatsApp Link Generator</h1>
            <p class="lead text-muted">Create a click-to-chat WhatsApp link with a prefilled message. Pakistani numbers starting with 03 are converted to the international format automatically.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="phone" class="form-label fw-semibold">WhatsApp Number</label>
                        <input type="tel" class="form-control" id="phone" placeholder="0300 1234567">
                        <div class="form-text">Formats accepted: 03001234567, 3001234567, 923001234567 or +923001234567.</div>
                    </div>
                    <div class="mb-3">
                        <label for="prefill" class="form-label fw-semibold">Prefilled Message (optional)</label>
                        <textarea class="form-control" id="prefill" rows="3" placeholder="Assalam-o-Alaikum! I want to ask about..."></textarea>
                    </div>
                    <button type="button" class="btn btn-success w-100" id="generateBtn">Generate Link</button>
                    <div class="alert alert-danger mt-3 d-none" id="waError" role="alert"></div>

                    <div id="waResult" class="d-none mt-4">
                        <label class="form-label fw-semibold">Your WhatsApp Link</label>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" id="waLink" readonly>
                            <button type="button" class="btn btn-outline-primary" id="copyBtn">Copy</button>
                            <a href="#" target="_blank" rel="noopener" class="btn btn-success" id="openBtn">Open</a>
                        </div>
                        <div id="copyMsg" class="text-success small d-none mb-2">Link copied!</div>
                        <div class="text-center">
                            <div id="waQr" class="d-inline-block border rounded p-3 bg-white"></div>
                            <div class="text-muted small mt-2">Scan this QR code to open the chat</div>
                        </div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Enter the WhatsApp number, for example 03001234567.</li>
                <li>Optionally write a prefilled message that will appear in the chat box.</li>
                <li>Click <strong>Generate Link</strong>.</li>
                <li>Copy the link, open it to test, or share the QR code so people can scan and chat instantly.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
(function () {
    function normalizePhone(raw) {
        var digits = raw.replace(/\D/g, '');
        if (!digits) return null;
        if (digits.indexOf('0092') === 0) { digits = digits.slice(2); }
        if (digits.charAt(0) === '0') { digits = '92' + digits.slice(1); }
        else if (digits.indexOf('92') !== 0) { digits = '92' + digits; }
        if (digits.length !== 12) return null;
        return digits;
    }

    document.getElementById('generateBtn').addEventListener('click', function () {
        var err = document.getElementById('waError');
        var res = document.getElementById('waResult');
        err.classList.add('d-none');
        var phone = normalizePhone(document.getElementById('phone').value);
        if (!phone) { err.textContent = 'Please enter a valid Pakistani mobile number, for example 03001234567.'; err.classList.remove('d-none'); res.classList.add('d-none'); return; }
        var msg = document.getElementById('prefill').value.trim();
        var link = 'https://wa.me/' + phone;
        if (msg) { link += '?text=' + encodeURIComponent(msg); }
        document.getElementById('waLink').value = link;
        document.getElementById('openBtn').href = link;
        document.getElementById('copyMsg').classList.add('d-none');
        res.classList.remove('d-none');
        var qrBox = document.getElementById('waQr');
        qrBox.innerHTML = '';
        if (typeof QRCode !== 'undefined') {
            try { new QRCode(qrBox, { text: link, width: 200, height: 200, correctLevel: QRCode.CorrectLevel.M }); }
            catch (e) { qrBox.textContent = 'Message is too long for a QR code (the link above still works).'; }
        } else {
            qrBox.textContent = 'QR library failed to load.';
        }
    });

    document.getElementById('copyBtn').addEventListener('click', function () {
        var input = document.getElementById('waLink');
        var done = function () { document.getElementById('copyMsg').classList.remove('d-none'); };
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(input.value).then(done).catch(function () { input.select(); document.execCommand('copy'); done(); });
        } else {
            input.select(); document.execCommand('copy'); done();
        }
    });
})();
</script>
@endsection
