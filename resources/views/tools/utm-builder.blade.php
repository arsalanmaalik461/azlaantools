@extends('layouts.app')

@section('title', 'UTM Campaign Builder - Azlaan Tools')
@section('meta_description', 'Add UTM tracking tags to your links for Google Analytics campaign tracking. Free UTM link builder.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">UTM Campaign Builder</h1>
            <p class="lead text-muted">Add UTM tags to your email, social and ads links so you can track every campaign's performance in Google Analytics.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="baseUrl" class="form-label fw-semibold">Website URL <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="baseUrl" placeholder="https://example.com/page">
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label for="utmSource" class="form-label fw-semibold">Campaign Source <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="utmSource" placeholder="facebook, newsletter">
                        </div>
                        <div class="col-md-4">
                            <label for="utmMedium" class="form-label fw-semibold">Campaign Medium <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="utmMedium" placeholder="social, email, cpc">
                        </div>
                        <div class="col-md-4">
                            <label for="utmCampaign" class="form-label fw-semibold">Campaign Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="utmCampaign" placeholder="eid_sale_2026">
                        </div>
                        <div class="col-md-6">
                            <label for="utmTerm" class="form-label fw-semibold">Campaign Term (optional)</label>
                            <input type="text" class="form-control" id="utmTerm" placeholder="paid keywords">
                        </div>
                        <div class="col-md-6">
                            <label for="utmContent" class="form-label fw-semibold">Campaign Content (optional)</label>
                            <input type="text" class="form-control" id="utmContent" placeholder="banner_a vs banner_b">
                        </div>
                    </div>
                    <div class="form-text mb-3">Spaces are automatically changed to underscores (_) and everything becomes lowercase.</div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Build UTM Link</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <label for="outUrl" class="form-label fw-semibold">Your tracked link</label>
                        <textarea class="form-control mb-3" id="outUrl" rows="3" readonly></textarea>
                        <div class="d-flex gap-2 flex-wrap mb-3">
                            <button type="button" class="btn btn-outline-primary" id="copyBtn">Copy Link</button>
                            <button type="button" class="btn btn-outline-secondary" id="testBtn">Open Link</button>
                        </div>
                        <h6>Parameters added:</h6>
                        <table class="table table-sm table-bordered" id="paramTable">
                            <thead><tr><th>Parameter</th><th>Value</th></tr></thead>
                            <tbody id="paramBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Enter your page URL (with https://).</li>
                <li>Write the Source (where it is shared), the Medium (which channel) and the Campaign name.</li>
                <li>Press "Build UTM Link", copy the link and use it in your email or social post.</li>
            </ol>
            <p class="text-muted small">This link will show in the "Traffic acquisition" report in Google Analytics 4.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var baseUrl = document.getElementById('baseUrl');
    var utmSource = document.getElementById('utmSource');
    var utmMedium = document.getElementById('utmMedium');
    var utmCampaign = document.getElementById('utmCampaign');
    var utmTerm = document.getElementById('utmTerm');
    var utmContent = document.getElementById('utmContent');
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var outUrl = document.getElementById('outUrl');
    var copyBtn = document.getElementById('copyBtn');
    var testBtn = document.getElementById('testBtn');
    var paramBody = document.getElementById('paramBody');

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function clean(v) {
        return v.trim().toLowerCase().replace(/\s+/g, '_').replace(/[^a-z0-9_\-+.]/g, '');
    }
    function esc(s) { return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;'); }

    goBtn.addEventListener('click', function () {
        hideError();
        var url = baseUrl.value.trim();
        var src = clean(utmSource.value);
        var med = clean(utmMedium.value);
        var camp = clean(utmCampaign.value);
        var term = clean(utmTerm.value);
        var content = clean(utmContent.value);

        if (!url) { showError('Enter your website URL first.'); return; }
        if (!/^https?:\/\/.+\..+/.test(url)) { showError('The URL must start with https:// or http://.'); return; }
        if (!src) { showError('Campaign Source is required (e.g. facebook, newsletter).'); return; }
        if (!med) { showError('Campaign Medium is required (e.g. social, email, cpc).'); return; }
        if (!camp) { showError('Campaign Name is required (e.g. eid_sale_2026).'); return; }

        var params = [['utm_source', src], ['utm_medium', med], ['utm_campaign', camp]];
        if (term) params.push(['utm_term', term]);
        if (content) params.push(['utm_content', content]);

        var sep = url.indexOf('?') === -1 ? '?' : '&';
        var finalUrl = url + sep + params.map(function (p) {
            return encodeURIComponent(p[0]) + '=' + encodeURIComponent(p[1]);
        }).join('&');

        outUrl.value = finalUrl;
        var rows = '';
        params.forEach(function (p) {
            rows += '<tr><td><code>' + esc(p[0]) + '</code></td><td>' + esc(p[1]) + '</td></tr>';
        });
        paramBody.innerHTML = rows;
        results.classList.remove('d-none');
        results.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });

    copyBtn.addEventListener('click', function () {
        var txt = outUrl.value;
        if (!txt) return;
        function done() { copyBtn.textContent = 'Copied!'; setTimeout(function () { copyBtn.textContent = 'Copy Link'; }, 1500); }
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(txt).then(done, function () { fallbackCopy(txt, done); });
        } else { fallbackCopy(txt, done); }
    });
    function fallbackCopy(txt, done) {
        outUrl.select();
        try { document.execCommand('copy'); done(); } catch (e) {}
    }
    testBtn.addEventListener('click', function () {
        var txt = outUrl.value;
        if (txt) window.open(txt, '_blank', 'noopener');
    });
})();
</script>
@endsection
