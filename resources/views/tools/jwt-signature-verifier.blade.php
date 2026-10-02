@extends('layouts.app')

@section('title', 'JWT Signature Verifier - Azlaan Tools')
@section('meta_description', 'Verify HS256, HS384 and HS512 JWT signatures with your secret, entirely in your browser. Free online.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">JWT Signature Verifier</h1>
            <p class="lead text-muted">Enter your JWT token and secret — the signature will be verified with <strong>HS256, HS384 or HS512</strong> HMAC, completely in your browser. The token and secret are never sent anywhere.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="tokenInput" class="form-label fw-semibold">JWT Token</label>
                        <textarea class="form-control font-monospace" id="tokenInput" rows="4" placeholder="eyJhbGciOiJIUzI1NiIs..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="secretInput" class="form-label fw-semibold">Secret key</label>
                        <input type="password" class="form-control font-monospace" id="secretInput" placeholder="Your HMAC secret">
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" id="showSecret">
                            <label class="form-check-label" for="showSecret">Show secret</label>
                        </div>
                    </div>
                    <div class="d-grid d-sm-flex gap-2">
                        <button type="button" class="btn btn-primary flex-sm-grow-1" id="goBtn">Verify Signature</button>
                        <button type="button" class="btn btn-outline-secondary" id="sampleBtn">Load Sample Token</button>
                    </div>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="alert" id="verdictBox" role="alert"></div>
                        <h5>Header</h5>
                        <pre class="bg-light border rounded p-3 small" id="headerOut"></pre>
                        <h5>Payload</h5>
                        <pre class="bg-light border rounded p-3 small" id="payloadOut"></pre>
                        <h5>Token checks</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <tbody id="checksRows"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Paste the <strong>JWT token</strong> (the three-part one: header.payload.signature).</li>
                <li>Enter the <strong>secret key</strong> — the same secret that was used when the token was created.</li>
                <li>Click <strong>Verify Signature</strong>. If the signature matches, you will see <span class="text-success fw-semibold">VALID</span>, otherwise <span class="text-danger fw-semibold">INVALID</span>.</li>
                <li>Below you will also see the decoded JSON of the header and payload, plus checks for expiry (exp), issued-at (iat) and not-before (nbf).</li>
            </ol>

            <div class="alert alert-info mt-4">
                <strong>Privacy:</strong> Verification happens in your browser using the WebCrypto API — the token and secret never go to a server. Only HS256, HS384 and HS512 (HMAC) algorithms are supported; RS256/ES256 need a public key, which this tool does not have.
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var tokenInput = document.getElementById('tokenInput');
    var secretInput = document.getElementById('secretInput');
    var showSecret = document.getElementById('showSecret');
    var goBtn = document.getElementById('goBtn');
    var sampleBtn = document.getElementById('sampleBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var verdictBox = document.getElementById('verdictBox');
    var headerOut = document.getElementById('headerOut');
    var payloadOut = document.getElementById('payloadOut');
    var checksRows = document.getElementById('checksRows');

    var SAMPLE = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyLCJleHAiOjQ3NjkyMzkwMjJ9.SflKxwRJSMeKKF2QT4fwpMeJf36POk6yJV_adQssw5c';

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function b64urlDecode(str) {
        var s = str.replace(/-/g, '+').replace(/_/g, '/');
        while (s.length % 4 !== 0) s += '=';
        var bin = atob(s);
        var bytes = new Uint8Array(bin.length);
        for (var i = 0; i < bin.length; i++) bytes[i] = bin.charCodeAt(i);
        return new TextDecoder().decode(bytes);
    }
    function b64urlToBytes(str) {
        var s = str.replace(/-/g, '+').replace(/_/g, '/');
        while (s.length % 4 !== 0) s += '=';
        var bin = atob(s);
        var bytes = new Uint8Array(bin.length);
        for (var i = 0; i < bin.length; i++) bytes[i] = bin.charCodeAt(i);
        return bytes;
    }
    function pretty(obj) {
        return JSON.stringify(obj, null, 2);
    }
    function fmtTime(sec) {
        var d = new Date(sec * 1000);
        return d.toLocaleString() + ' (unix: ' + sec + ')';
    }
    function addRow(label, value, ok) {
        var tr = document.createElement('tr');
        var th = document.createElement('th');
        th.style.width = '40%';
        th.textContent = label;
        var td = document.createElement('td');
        td.textContent = value;
        if (ok === true) td.className = 'text-success fw-semibold';
        if (ok === false) td.className = 'text-danger fw-semibold';
        tr.appendChild(th); tr.appendChild(td);
        checksRows.appendChild(tr);
    }

    showSecret.addEventListener('change', function () {
        secretInput.type = showSecret.checked ? 'text' : 'password';
    });
    sampleBtn.addEventListener('click', function () {
        tokenInput.value = SAMPLE;
        secretInput.value = 'your-256-bit-secret';
        hideError();
    });

    goBtn.addEventListener('click', function () {
        hideError();
        var token = tokenInput.value.trim();
        var secret = secretInput.value;
        if (!token) { showError('Please paste a JWT token.'); return; }
        if (!secret) { showError('Please enter the secret key.'); return; }
        if (!window.crypto || !crypto.subtle) {
            showError('Your browser does not support WebCrypto. Please use a newer browser.');
            return;
        }
        var parts = token.split('.');
        if (parts.length !== 3) { showError('The token is in the wrong format — it must have 3 parts separated by dots.'); return; }

        var header, payload;
        try {
            header = JSON.parse(b64urlDecode(parts[0]));
            payload = JSON.parse(b64urlDecode(parts[1]));
        } catch (e) {
            showError('Could not decode the header or payload — please check that the token is correct.');
            return;
        }
        if (typeof header !== 'object' || header === null) { showError('The header is not a valid JSON object.'); return; }

        var alg = (header.alg || '').toUpperCase();
        var hashName = { HS256: 'SHA-256', HS384: 'SHA-384', HS512: 'SHA-512' }[alg];
        if (!hashName) {
            showError('Only HS256, HS384 and HS512 are supported. This token uses "' + (header.alg || 'none') + '".');
            return;
        }
        var signatureBytes;
        try { signatureBytes = b64urlToBytes(parts[2]); }
        catch (e) { showError('Could not decode the signature part.'); return; }

        var enc = new TextEncoder();
        var data = enc.encode(parts[0] + '.' + parts[1]);
        crypto.subtle.importKey('raw', enc.encode(secret), { name: 'HMAC', hash: hashName }, false, ['verify'])
            .then(function (key) {
                return crypto.subtle.verify('HMAC', key, signatureBytes, data);
            })
            .then(function (valid) {
                headerOut.textContent = pretty(header);
                payloadOut.textContent = pretty(payload);
                checksRows.innerHTML = '';
                verdictBox.className = 'alert ' + (valid ? 'alert-success' : 'alert-danger');
                verdictBox.innerHTML = '';
                var strong = document.createElement('strong');
                strong.textContent = valid ? 'SIGNATURE VALID' : 'SIGNATURE INVALID';
                verdictBox.appendChild(strong);
                var span = document.createElement('span');
                span.textContent = valid
                    ? ' — the token was signed with this secret (' + alg + ').'
                    : ' — the signature did not match. The secret may be wrong or the token may have been changed.';
                verdictBox.appendChild(span);

                addRow('Algorithm', alg + ' (' + hashName + ')', true);
                var now = Math.floor(Date.now() / 1000);
                if (typeof payload.exp === 'number') {
                    addRow('Expires (exp)', fmtTime(payload.exp), payload.exp > now);
                } else {
                    addRow('Expires (exp)', 'not present — the token never expires', null);
                }
                if (typeof payload.iat === 'number') {
                    addRow('Issued at (iat)', fmtTime(payload.iat), true);
                }
                if (typeof payload.nbf === 'number') {
                    addRow('Not before (nbf)', fmtTime(payload.nbf), payload.nbf <= now);
                }
                results.classList.remove('d-none');
            })
            .catch(function () {
                showError('An error occurred during verification. Please check the token and secret again.');
            });
    });
})();
</script>
@endsection
