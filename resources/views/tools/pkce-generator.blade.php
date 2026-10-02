@extends('layouts.app')

@section('title', 'PKCE Code Generator - Azlaan Tools')
@section('meta_description', 'Generate PKCE code verifier and SHA256 challenge pairs for OAuth 2.0 flows, free online.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">PKCE Code Generator</h1>
            <p class="lead text-muted">Generate PKCE code verifier and code challenge for OAuth 2.0 (S256 / SHA-256). Everything happens in your browser — nothing is sent anywhere.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="methodSel" class="form-label fw-semibold">Challenge method</label>
                        <select class="form-select" id="methodSel">
                            <option value="S256" selected>S256 (SHA-256) — recommended</option>
                            <option value="plain">plain</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="lenSel" class="form-label fw-semibold">Verifier length</label>
                        <select class="form-select" id="lenSel">
                            <option value="43">43 chars (minimum)</option>
                            <option value="64" selected>64 chars</option>
                            <option value="96">96 chars</option>
                            <option value="128">128 chars (maximum)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="verifierOut" class="form-label fw-semibold">Code verifier</label>
                        <div class="input-group">
                            <input type="text" class="form-control font-monospace" id="verifierOut" readonly>
                            <button type="button" class="btn btn-outline-secondary" id="copyVBtn">Copy</button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="challengeOut" class="form-label fw-semibold">Code challenge</label>
                        <div class="input-group">
                            <input type="text" class="form-control font-monospace" id="challengeOut" readonly>
                            <button type="button" class="btn btn-outline-secondary" id="copyCBtn">Copy</button>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Generate New Pair</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="alert alert-info mb-0">
                            <small><strong>How to use:</strong> send <code>code_challenge</code> in the authorization request (with <code>code_challenge_method=S256</code>), and <code>code_verifier</code> in the token request. Both are created in your browser — nothing went to a server.</small>
                        </div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Choose the challenge method (S256 recommended) and the verifier length.</li>
                <li>Press "Generate New Pair".</li>
                <li>Save the verifier in your app, and send the challenge in the authorization URL.</li>
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
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var methodSel = document.getElementById('methodSel');
    var lenSel = document.getElementById('lenSel');
    var verifierOut = document.getElementById('verifierOut');
    var challengeOut = document.getElementById('challengeOut');

    var CHARSET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-._~';

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }

    function randomVerifier(len) {
        var rand = new Uint8Array(len);
        if (window.crypto && window.crypto.getRandomValues) {
            window.crypto.getRandomValues(rand);
        } else {
            for (var i = 0; i < len; i++) { rand[i] = Math.floor(Math.random() * 256); }
        }
        var out = '';
        for (var j = 0; j < len; j++) { out += CHARSET.charAt(rand[j] % CHARSET.length); }
        return out;
    }

    function base64UrlEncode(buffer) {
        var bytes = new Uint8Array(buffer);
        var binary = '';
        for (var i = 0; i < bytes.length; i++) { binary += String.fromCharCode(bytes[i]); }
        var b64 = window.btoa(binary);
        return b64.replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/, '');
    }

    function sha256Challenge(verifier, done) {
        var enc = new TextEncoder();
        var data = enc.encode(verifier);
        if (window.crypto && window.crypto.subtle && window.crypto.subtle.digest) {
            window.crypto.subtle.digest('SHA-256', data).then(function (hash) {
                done(base64UrlEncode(hash), null);
            }).catch(function () {
                done(null, 'SHA-256 could not be computed (a secure context is needed).');
            });
        } else {
            done(null, 'Web Crypto API is not available in this browser - use the plain method.');
        }
    }

    function copyField(el, btn) {
        el.select();
        var label = btn.textContent;
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(el.value).then(function () {
                btn.textContent = 'Copied!';
                setTimeout(function () { btn.textContent = label; }, 1500);
            });
        } else {
            try { document.execCommand('copy'); } catch (e) {}
            btn.textContent = 'Copied!';
            setTimeout(function () { btn.textContent = label; }, 1500);
        }
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var len = parseInt(lenSel.value, 10);
        if (len < 43 || len > 128) { showError('Length must be between 43 and 128.'); return; }
        var verifier = randomVerifier(len);
        var method = methodSel.value;
        verifierOut.value = verifier;
        challengeOut.value = '';
        results.classList.add('d-none');

        if (method === 'plain') {
            challengeOut.value = verifier;
            results.classList.remove('d-none');
        } else {
            sha256Challenge(verifier, function (challenge, err) {
                if (err) { showError(err); return; }
                challengeOut.value = challenge;
                results.classList.remove('d-none');
            });
        }
    });

    document.getElementById('copyVBtn').addEventListener('click', function () {
        copyField(verifierOut, this);
    });
    document.getElementById('copyCBtn').addEventListener('click', function () {
        copyField(challengeOut, this);
    });

    // Generate an initial pair on load
    goBtn.click();
})();
</script>
@endsection
