@extends('layouts.app')

@section('title', 'UTM Link Decoder - Azlaan Tools')
@section('meta_description', 'Decode and analyze UTM campaign parameters in any link instantly, free online.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">UTM Link Decoder</h1>
            <p class="lead text-muted">Read the UTM parameters of any link separately — easily see where the link came from and which campaign it belongs to.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="urlInput" class="form-label fw-semibold">Paste the link</label>
                        <input type="text" class="form-control" id="urlInput" placeholder="https://example.com/?utm_source=facebook&utm_medium=cpc&utm_campaign=sale">
                        <div class="form-text">Paste a marketing link with UTM here.</div>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Decode Link</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <h5>Campaign Parameters (UTM)</h5>
                        <table class="table table-bordered" id="utmTable">
                            <thead class="table-light"><tr><th>Parameter</th><th>Value</th><th>Meaning</th></tr></thead>
                            <tbody id="utmBody"></tbody>
                        </table>
                        <h5 class="mt-4">Other Parameters</h5>
                        <table class="table table-bordered">
                            <thead class="table-light"><tr><th>Parameter</th><th>Value</th></tr></thead>
                            <tbody id="otherBody"></tbody>
                        </table>
                        <h5 class="mt-4">Clean URL (without tracking)</h5>
                        <div class="input-group">
                            <input type="text" class="form-control" id="cleanUrl" readonly>
                            <button type="button" class="btn btn-outline-success" id="copyBtn">Copy</button>
                        </div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Paste a marketing link (with UTM).</li>
                <li>Press "Decode Link".</li>
                <li>See the meaning of each parameter, and if you want, remove the tracking and copy the clean link.</li>
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
    var urlInput = document.getElementById('urlInput');

    var UTM_MEANINGS = {
        utm_source: 'Source — where the traffic came from (example: facebook, google)',
        utm_medium: 'Medium — what kind of marketing (example: cpc, email, social)',
        utm_campaign: 'Campaign — the campaign name (example: summer_sale)',
        utm_term: 'Term — paid search keyword',
        utm_content: 'Content — which ad version (A/B testing)',
        utm_id: 'Campaign ID — Google Ads campaign ID'
    };

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function esc(s) {
        return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    function parseQuery(qs) {
        var params = [];
        if (!qs) { return params; }
        var pairs = qs.split('&');
        for (var i = 0; i < pairs.length; i++) {
            var p = pairs[i];
            if (!p) { continue; }
            var eq = p.indexOf('=');
            var name, val;
            if (eq === -1) { name = p; val = ''; } else { name = p.slice(0, eq); val = p.slice(eq + 1); }
            try { name = decodeURIComponent(name.replace(/\+/g, ' ')); } catch (e) {}
            try { val = decodeURIComponent(val.replace(/\+/g, ' ')); } catch (e) {}
            params.push({ name: name, value: val });
        }
        return params;
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var raw = urlInput.value.trim();
        if (!raw) { showError('Paste a link first.'); return; }

        var urlText = raw;
        if (!/^https?:\/\//i.test(urlText)) { urlText = 'https://' + urlText; }
        var qIndex = urlText.indexOf('?');
        var hashIndex = urlText.indexOf('#');
        if (hashIndex === -1) { hashIndex = urlText.length; }
        if (qIndex === -1 || qIndex > hashIndex) {
            showError('No query parameters found in this link.');
            return;
        }
        var qs = urlText.slice(qIndex + 1, hashIndex);
        var params = parseQuery(qs);
        if (!params.length) {
            showError('No query parameters found in this link.');
            return;
        }

        var utmRows = [], otherRows = [];
        var cleanParts = [];
        for (var i = 0; i < params.length; i++) {
            var p = params[i];
            if (/^utm_/i.test(p.name)) {
                var key = p.name.toLowerCase();
                var meaning = UTM_MEANINGS[key] || 'Custom UTM parameter';
                utmRows.push('<tr><td class="fw-semibold">' + esc(p.name) + '</td><td>' + esc(p.value || '(empty)') + '</td><td class="text-muted">' + esc(meaning) + '</td></tr>');
            } else {
                otherRows.push('<tr><td class="fw-semibold">' + esc(p.name) + '</td><td>' + esc(p.value || '(empty)') + '</td></tr>');
                cleanParts.push(encodeURIComponent(p.name) + '=' + encodeURIComponent(p.value));
            }
        }

        var utmBody = document.getElementById('utmBody');
        if (utmRows.length) {
            utmBody.innerHTML = utmRows.join('');
        } else {
            utmBody.innerHTML = '<tr><td colspan="3" class="text-muted">No UTM parameters found — this looks like a normal link.</td></tr>';
        }
        var otherBody = document.getElementById('otherBody');
        otherBody.innerHTML = otherRows.length ? otherRows.join('') : '<tr><td colspan="2" class="text-muted">No other parameters found.</td></tr>';

        var base = urlText.slice(0, qIndex);
        var hash = hashIndex < urlText.length ? urlText.slice(hashIndex) : '';
        document.getElementById('cleanUrl').value = base + (cleanParts.length ? '?' + cleanParts.join('&') : '') + hash;

        results.classList.remove('d-none');
    });

    document.getElementById('copyBtn').addEventListener('click', function () {
        var el = document.getElementById('cleanUrl');
        el.select();
        var btn = this;
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(el.value).then(function () {
                var old = btn.textContent; btn.textContent = 'Copied!';
                setTimeout(function () { btn.textContent = old; }, 1500);
            });
        } else {
            try { document.execCommand('copy'); } catch (e) {}
            var old2 = btn.textContent; btn.textContent = 'Copied!';
            setTimeout(function () { btn.textContent = old2; }, 1500);
        }
    });
})();
</script>
@endsection
