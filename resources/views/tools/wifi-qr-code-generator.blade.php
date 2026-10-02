@extends('layouts.app')

@section('title', 'WiFi QR Code Generator - Azlaan Tools')
@section('meta_description', 'Make a QR code guests scan to join your WiFi. Let them connect without telling the password, free online tool.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">WiFi QR Code Generator</h1>
            <p class="lead text-muted">Make a QR code for your WiFi — guests scan it and connect instantly, no need to share the password.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="ssidInput" class="form-label fw-semibold">WiFi name (SSID)</label>
                        <input type="text" class="form-control" id="ssidInput" placeholder="e.g. AzlaanHome" autocomplete="off">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="securitySelect" class="form-label fw-semibold">Security type</label>
                            <select class="form-select" id="securitySelect">
                                <option value="WPA">WPA / WPA2 (this is the usual one)</option>
                                <option value="WEP">WEP (old)</option>
                                <option value="nopass">No password (open network)</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="passInput" class="form-label fw-semibold">WiFi password</label>
                            <input type="text" class="form-control" id="passInput" placeholder="type your WiFi password" autocomplete="off">
                        </div>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="hiddenCheck">
                        <label class="form-check-label" for="hiddenCheck">Hidden network (SSID is not broadcast)</label>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Make QR Code</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4 text-center">
                        <div class="d-inline-block p-3 border rounded bg-white" id="qrBox"></div>
                        <p class="text-muted small mt-2 mb-1">Scan this QR with your phone camera or scanner.</p>
                        <p class="small"><code id="qrString" class="text-break"></code></p>
                        <button type="button" class="btn btn-success" id="downloadBtn">Download QR Code (PNG)</button>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Type your WiFi name (SSID) and password.</li>
                <li>Choose the security type — usually WPA/WPA2.</li>
                <li>Press "Make QR Code".</li>
                <li>Download and print the QR code or show it to guests — scanning it connects the WiFi right away.</li>
            </ol>
            <p class="text-muted small">Note: This page never sends your password anywhere — the QR code is made only in your browser. Still, print the QR code and put it where only guests can see it.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
(function () {
    'use strict';
    var ssidInput = document.getElementById('ssidInput');
    var securitySelect = document.getElementById('securitySelect');
    var passInput = document.getElementById('passInput');
    var hiddenCheck = document.getElementById('hiddenCheck');
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var qrBox = document.getElementById('qrBox');
    var qrString = document.getElementById('qrString');
    var downloadBtn = document.getElementById('downloadBtn');

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function escapeWifi(s) {
        return s.replace(/\\/g, '\\\\').replace(/;/g, '\\;').replace(/,/g, '\\,').replace(/:/g, '\\:').replace(/"/g, '\\"');
    }

    securitySelect.addEventListener('change', function () {
        var open = securitySelect.value === 'nopass';
        passInput.disabled = open;
        if (open) { passInput.value = ''; }
    });

    goBtn.addEventListener('click', function () {
        hideError();
        var ssid = ssidInput.value.trim();
        var sec = securitySelect.value;
        var pass = passInput.value;
        if (!ssid) { showError('Please enter your WiFi name (SSID).'); return; }
        if (sec !== 'nopass' && !pass) { showError('Please enter the password or choose "No password".'); return; }
        var str = 'WIFI:T:' + sec + ';S:' + escapeWifi(ssid) + ';';
        if (sec !== 'nopass') { str += 'P:' + escapeWifi(pass) + ';'; }
        if (hiddenCheck.checked) { str += 'H:true;'; }
        str += ';';
        qrBox.innerHTML = '';
        try {
            new QRCode(qrBox, {
                text: str,
                width: 256,
                height: 256,
                colorDark: '#000000',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.M
            });
        } catch (e) {
            showError('There was a problem making the QR code. Please try again.');
            return;
        }
        qrString.textContent = str;
        results.classList.remove('d-none');
    });

    downloadBtn.addEventListener('click', function () {
        var img = qrBox.querySelector('img');
        var canvas = qrBox.querySelector('canvas');
        var dataUrl = '';
        if (img && img.src) { dataUrl = img.src; }
        else if (canvas) { dataUrl = canvas.toDataURL('image/png'); }
        if (!dataUrl) { showError('Please make the QR code first.'); return; }
        var a = document.createElement('a');
        a.href = dataUrl;
        a.download = 'wifi-qr-code.png';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    });
})();
</script>
@endsection
