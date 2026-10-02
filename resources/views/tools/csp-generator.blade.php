@extends('layouts.app')

@section('title', 'Content Security Policy Generator - Azlaan Tools')
@section('meta_description', 'Build a Content Security Policy header with an interactive directive picker. Free online CSP generator.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Content Security Policy Generator</h1>
            <p class="lead text-muted">Build a Content Security Policy header for your website — select directives, enter sources, and your header is ready.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <p class="text-muted small">Enter the allowed sources next to each directive (separated by spaces, e.g. <code>'self' https://cdn.example.com</code>). If you leave one empty, that directive will not be included in the header.</p>
                    <div id="directiveList"></div>

                    <div class="form-check mb-2 mt-3">
                        <input class="form-check-input" type="checkbox" id="optUpgrade">
                        <label class="form-check-label" for="optUpgrade">upgrade-insecure-requests</label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="optMixed">
                        <label class="form-check-label" for="optMixed">block-all-mixed-content</label>
                    </div>
                    <div class="mb-3">
                        <label for="reportUri" class="form-label fw-semibold">report-uri (optional)</label>
                        <input type="text" class="form-control" id="reportUri" placeholder="e.g. https://example.com/csp-report">
                    </div>

                    <button type="button" class="btn btn-primary w-100" id="goBtn">Generate CSP Header</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <h5>HTTP Header</h5>
                        <div class="input-group mb-2">
                            <textarea class="form-control" id="headerOut" rows="4" readonly></textarea>
                            <button type="button" class="btn btn-outline-secondary" id="copyHeader">Copy</button>
                        </div>
                        <h5 class="mt-3">Meta tag version</h5>
                        <div class="input-group mb-3">
                            <textarea class="form-control" id="metaOut" rows="4" readonly></textarea>
                            <button type="button" class="btn btn-outline-secondary" id="copyMeta">Copy</button>
                        </div>
                        <div class="alert alert-info">
                            Add the header to your server config (Nginx/Apache), or place the meta tag in the <code>&lt;head&gt;</code>. Before deploying, test in <strong>Report-Only</strong> mode so your site is not blocked.
                        </div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Enter your base policy in default-src (usually <code>'self'</code>).</li>
                <li>Enter sources in the directives you need, leave the rest empty.</li>
                <li>Press "Generate CSP Header" and copy the header to your server.</li>
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

    var directives = [
        ['default-src', "'self'", 'Fallback for all other directives'],
        ['script-src', '', 'JavaScript sources'],
        ['style-src', '', 'CSS sources'],
        ['img-src', '', 'Image sources'],
        ['connect-src', '', 'fetch / XHR / WebSocket'],
        ['font-src', '', 'Font sources'],
        ['media-src', '', 'Audio / video sources'],
        ['frame-src', '', 'iframe sources'],
        ['object-src', '', 'Plugins (set to none for safety)'],
        ['base-uri', '', 'Base tag URLs'],
        ['form-action', '', 'Where forms may submit']
    ];

    var list = document.getElementById('directiveList');
    directives.forEach(function (d) {
        var wrap = document.createElement('div');
        wrap.className = 'mb-2';
        var label = document.createElement('label');
        label.className = 'form-label fw-semibold';
        label.setAttribute('for', 'dir-' + d[0]);
        label.textContent = d[0];
        var hint = document.createElement('span');
        hint.className = 'text-muted small ms-2';
        hint.textContent = d[2];
        label.appendChild(hint);
        var input = document.createElement('input');
        input.type = 'text';
        input.className = 'form-control';
        input.id = 'dir-' + d[0];
        input.placeholder = "e.g. 'self' https://cdn.example.com";
        if (d[1]) { input.value = d[1]; }
        wrap.appendChild(label);
        wrap.appendChild(input);
        list.appendChild(wrap);
    });

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function copyText(text, btn, doneLabel) {
        function done() {
            var old = btn.textContent;
            btn.textContent = doneLabel;
            setTimeout(function () { btn.textContent = old; }, 1500);
        }
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(done).catch(done);
        } else {
            done();
        }
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var parts = [];
        directives.forEach(function (d) {
            var id = 'dir-' + d[0];
            var v = document.getElementById(id).value.trim();
            if (v) {
                if (/[\r\n;]/.test(v)) { return; }
                parts.push(d[0] + ' ' + v);
            }
        });
        if (document.getElementById('optUpgrade').checked) { parts.push('upgrade-insecure-requests'); }
        if (document.getElementById('optMixed').checked) { parts.push('block-all-mixed-content'); }
        var report = document.getElementById('reportUri').value.trim();
        if (report) { parts.push('report-uri ' + report); }
        if (parts.length === 0) {
            showError('Please fill at least one directive.');
            return;
        }
        var policy = parts.join('; ') + ';';
        document.getElementById('headerOut').value = 'Content-Security-Policy: ' + policy;
        document.getElementById('metaOut').value = '<meta http-equiv="Content-Security-Policy" content="' + policy.replace(/"/g, '&quot;') + '">';
        results.classList.remove('d-none');
    });

    document.getElementById('copyHeader').addEventListener('click', function () {
        copyText(document.getElementById('headerOut').value, this, 'Copied!');
    });
    document.getElementById('copyMeta').addEventListener('click', function () {
        copyText(document.getElementById('metaOut').value, this, 'Copied!');
    });
})();
</script>
@endsection
