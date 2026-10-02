@extends('layouts.app')

@section('title', 'HMAC Generator - Azlaan Tools')
@section('meta_description', 'Compute HMAC SHA-256 or SHA-512 authentication codes with your secret key — free online.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">HMAC Generator</h1>
            <p class="lead text-muted">Create an <strong>HMAC</strong> (Hash-based Message Authentication Code) with your secret key — SHA-256, SHA-384 or SHA-512. Everything is computed in your browser; your key never leaves it.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="msgInput" class="form-label fw-semibold">Message</label>
                        <textarea class="form-control" id="msgInput" rows="3" placeholder="Type the text you want to make an HMAC of..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="keyInput" class="form-label fw-semibold">Secret Key</label>
                        <input type="text" class="form-control" id="keyInput" placeholder="Enter your secret key" autocomplete="off">
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="keyFormat" class="form-label fw-semibold">Key format</label>
                            <select class="form-select" id="keyFormat">
                                <option value="text">Plain text</option>
                                <option value="hex">Hex string</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="algoSel" class="form-label fw-semibold">Hash algorithm</label>
                            <select class="form-select" id="algoSel">
                                <option value="SHA-256">HMAC-SHA-256</option>
                                <option value="SHA-384">HMAC-SHA-384</option>
                                <option value="SHA-512">HMAC-SHA-512</option>
                            </select>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100 mt-3" id="goBtn">Generate HMAC</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Hex</label>
                            <div class="input-group">
                                <input type="text" class="form-control font-monospace" id="outHex" readonly>
                                <button class="btn btn-outline-secondary" type="button" data-copy="outHex">Copy</button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Base64</label>
                            <div class="input-group">
                                <input type="text" class="form-control font-monospace" id="outB64" readonly>
                                <button class="btn btn-outline-secondary" type="button" data-copy="outB64">Copy</button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Base64 URL-safe</label>
                            <div class="input-group">
                                <input type="text" class="form-control font-monospace" id="outB64u" readonly>
                                <button class="btn btn-outline-secondary" type="button" data-copy="outB64u">Copy</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title">Verify a Signature</h5>
                    <p class="text-muted small">Check a given signature (in hex format) against the key and message above.</p>
                    <div class="mb-3">
                        <label for="verifySig" class="form-label fw-semibold">Signature (hex)</label>
                        <input type="text" class="form-control font-monospace" id="verifySig" placeholder="Paste the signature to verify">
                    </div>
                    <button type="button" class="btn btn-outline-primary w-100" id="verifyBtn">Verify</button>
                    <div class="alert mt-3 d-none" id="verifyOut" role="status"></div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Enter the message and secret key, then select an algorithm.</li>
                <li>Press <strong>Generate HMAC</strong> — you will get hex, base64 and base64url all three.</li>
                <li>Copy the signature with the Copy button, or check it with the verify tool below.</li>
            </ol>
            <h2>What is HMAC used for?</h2>
            <p>HMAC proves a message is <strong>authentic</strong> — for example signing API requests, verifying webhooks, or creating password reset tokens. Only someone with the secret key can create a valid HMAC.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var msgInput = document.getElementById('msgInput');
    var keyInput = document.getElementById('keyInput');
    var keyFormat = document.getElementById('keyFormat');
    var algoSel = document.getElementById('algoSel');
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var outHex = document.getElementById('outHex');
    var outB64 = document.getElementById('outB64');
    var outB64u = document.getElementById('outB64u');
    var verifySig = document.getElementById('verifySig');
    var verifyBtn = document.getElementById('verifyBtn');
    var verifyOut = document.getElementById('verifyOut');

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }

    function getKeyBytes(keyStr, format) {
        if (format === 'hex') {
            var clean = keyStr.replace(/[^0-9a-fA-F]/g, '');
            if (clean.length === 0 || clean.length % 2 !== 0) {
                throw new Error('Hex key must have an even number of hex digits.');
            }
            var bytes = new Uint8Array(clean.length / 2);
            for (var i = 0; i < bytes.length; i++) {
                bytes[i] = parseInt(clean.substr(i * 2, 2), 16);
            }
            return bytes;
        }
        return new TextEncoder().encode(keyStr);
    }

    function toHex(bytes) {
        var hex = '';
        for (var i = 0; i < bytes.length; i++) {
            var h = bytes[i].toString(16);
            hex += (h.length === 1 ? '0' + h : h);
        }
        return hex;
    }

    function toBase64(bytes) {
        var bin = '';
        var chunk = 32768;
        for (var i = 0; i < bytes.length; i += chunk) {
            bin += String.fromCharCode.apply(null, bytes.subarray(i, i + chunk));
        }
        return btoa(bin);
    }

    async function computeHmac() {
        var keyStr = keyInput.value;
        if (!keyStr) { throw new Error('Please enter a secret key.'); }
        var keyBytes = getKeyBytes(keyStr, keyFormat.value);
        var algo = algoSel.value;
        var cryptoKey = await window.crypto.subtle.importKey('raw', keyBytes, { name: 'HMAC', hash: { name: algo } }, false, ['sign']);
        var sig = await window.crypto.subtle.sign('HMAC', cryptoKey, new TextEncoder().encode(msgInput.value));
        var bytes = new Uint8Array(sig);
        var b64 = toBase64(bytes);
        return {
            hex: toHex(bytes),
            b64: b64,
            b64u: b64.replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/, '')
        };
    }

    goBtn.addEventListener('click', function () {
        hideError();
        if (!window.crypto || !window.crypto.subtle) {
            showError('Your browser does not support Web Crypto. Open this page on HTTPS or localhost.');
            return;
        }
        goBtn.disabled = true;
        computeHmac().then(function (r) {
            outHex.value = r.hex;
            outB64.value = r.b64;
            outB64u.value = r.b64u;
            results.classList.remove('d-none');
            goBtn.disabled = false;
        }).catch(function (e) {
            goBtn.disabled = false;
            showError(e && e.message ? e.message : 'HMAC could not be computed.');
        });
    });

    verifyBtn.addEventListener('click', function () {
        var sig = verifySig.value.trim().toLowerCase().replace(/[^0-9a-f]/g, '');
        if (!sig) {
            verifyOut.className = 'alert alert-danger mt-3';
            verifyOut.textContent = 'Enter the signature you want to verify.';
            return;
        }
        if (!window.crypto || !window.crypto.subtle) {
            verifyOut.className = 'alert alert-danger mt-3';
            verifyOut.textContent = 'Your browser does not support Web Crypto.';
            return;
        }
        verifyBtn.disabled = true;
        computeHmac().then(function (r) {
            verifyBtn.disabled = false;
            if (r.hex === sig) {
                verifyOut.className = 'alert alert-success mt-3';
                verifyOut.textContent = 'MATCH — the signature is correct.';
            } else {
                verifyOut.className = 'alert alert-danger mt-3';
                verifyOut.textContent = 'NO MATCH — the signature is wrong or the key/message is different.';
            }
        }).catch(function (e) {
            verifyBtn.disabled = false;
            verifyOut.className = 'alert alert-danger mt-3';
            verifyOut.textContent = e && e.message ? e.message : 'Could not verify.';
        });
    });

    var copyBtns = document.querySelectorAll('[data-copy]');
    for (var i = 0; i < copyBtns.length; i++) {
        (function (btn) {
            btn.addEventListener('click', function () {
                var target = document.getElementById(btn.getAttribute('data-copy'));
                if (!target || !target.value) { return; }
                function done() {
                    var t = btn.textContent;
                    btn.textContent = 'Copied';
                    setTimeout(function () { btn.textContent = t; }, 1200);
                }
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(target.value).then(done, done);
                } else {
                    target.select();
                    try { document.execCommand('copy'); } catch (e) {}
                    done();
                }
            });
        })(copyBtns[i]);
    }
})();
</script>
@endsection
