@extends('layouts.app')

@section('title', 'Sitemap XML Generator - Azlaan Tools')
@section('meta_description', 'Turn a URL list into a ready sitemap.xml for your website. Free SEO tool, no signup.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Sitemap XML Generator</h1>
            <p class="lead text-muted">Paste your URL list — you will get a ready <code>sitemap.xml</code>. Submit it in Google Search Console for SEO.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="urlInput" class="form-label fw-semibold">URL list (one URL per line)</label>
                        <textarea class="form-control font-monospace" id="urlInput" rows="8" placeholder="https://example.com/&#10;https://example.com/about&#10;https://example.com/contact"></textarea>
                        <div class="form-text">Write the domain on the first line — the other URLs should be on the same domain.</div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label for="freqSel" class="form-label fw-semibold">changefreq</label>
                            <select class="form-select" id="freqSel">
                                <option value="always">always</option>
                                <option value="hourly">hourly</option>
                                <option value="daily" selected>daily</option>
                                <option value="weekly">weekly</option>
                                <option value="monthly">monthly</option>
                                <option value="yearly">yearly</option>
                                <option value="never">never</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="prioSel" class="form-label fw-semibold">priority</label>
                            <select class="form-select" id="prioSel">
                                <option value="1.0">1.0 (homepage)</option>
                                <option value="0.8" selected>0.8</option>
                                <option value="0.5">0.5</option>
                                <option value="0.3">0.3</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="dateSel" class="form-label fw-semibold">lastmod</label>
                            <select class="form-select" id="dateSel">
                                <option value="today" selected>Today</option>
                                <option value="none">Do not include</option>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex gap-2 flex-wrap">
                        <button type="button" class="btn btn-primary flex-grow-1" id="goBtn">Generate Sitemap</button>
                        <button type="button" class="btn btn-outline-secondary" id="copyBtn" disabled>Copy</button>
                        <button type="button" class="btn btn-outline-success" id="dlBtn" disabled>Download sitemap.xml</button>
                    </div>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-semibold">Generated sitemap.xml</span>
                            <span class="badge bg-info" id="urlCount"></span>
                        </div>
                        <pre class="border rounded bg-light p-3 small overflow-auto" id="xmlOut" style="max-height:380px; white-space:pre-wrap;"></pre>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Paste all your website URLs (one per line).</li>
                <li>Choose the changefreq, priority and lastmod settings.</li>
                <li>Press <strong>Generate Sitemap</strong> — the XML will appear below.</li>
                <li>Download the file and upload it to your website root as <code>sitemap.xml</code>.</li>
                <li>Submit it in Google Search Console.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var urlInput = document.getElementById('urlInput');
    var freqSel = document.getElementById('freqSel');
    var prioSel = document.getElementById('prioSel');
    var dateSel = document.getElementById('dateSel');
    var goBtn = document.getElementById('goBtn');
    var copyBtn = document.getElementById('copyBtn');
    var dlBtn = document.getElementById('dlBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var xmlOut = document.getElementById('xmlOut');
    var urlCount = document.getElementById('urlCount');
    var lastXml = '';

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function escXml(s) {
        return s.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&apos;');
    }
    function todayISO() {
        var n = new Date();
        var m = String(n.getMonth() + 1).padStart(2, '0');
        var d = String(n.getDate()).padStart(2, '0');
        return n.getFullYear() + '-' + m + '-' + d;
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var lines = urlInput.value.split('\n').map(function (l) { return l.trim(); }).filter(function (l) { return l.length > 0; });
        if (lines.length === 0) { showError('Please enter at least one URL.'); return; }
        if (lines.length > 50000) { showError('Please keep it under 50,000 URLs.'); return; }
        var bad = [];
        var seen = {};
        var valid = [];
        lines.forEach(function (u) {
            if (!/^https?:\/\/[^\s/$.?#].[^\s]*$/i.test(u)) { bad.push(u); return; }
            var key = u.toLowerCase();
            if (!seen[key]) { seen[key] = true; valid.push(u); }
        });
        if (valid.length === 0) { showError('No valid URL found — every URL must start with http(s)://.'); return; }

        var freq = freqSel.value, prio = prioSel.value;
        var lm = dateSel.value === 'today' ? todayISO() : null;
        var xml = '<?xml version="1.0" encoding="UTF-8"?>\n<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">\n';
        valid.forEach(function (u) {
            xml += '  <url>\n    <loc>' + escXml(u) + '</loc>\n';
            if (lm) xml += '    <lastmod>' + lm + '</lastmod>\n';
            xml += '    <changefreq>' + freq + '</changefreq>\n    <priority>' + prio + '</priority>\n  </url>\n';
        });
        xml += '</urlset>';
        lastXml = xml;
        xmlOut.textContent = xml;
        urlCount.textContent = valid.length + ' URLs' + (bad.length ? ' (' + bad.length + ' invalid skipped)' : '');
        copyBtn.disabled = false;
        dlBtn.disabled = false;
        results.classList.remove('d-none');
    });

    copyBtn.addEventListener('click', function () {
        if (!lastXml) return;
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(lastXml).then(function () {
                copyBtn.textContent = 'Copied!';
                setTimeout(function () { copyBtn.textContent = 'Copy'; }, 2000);
            });
        } else {
            var ta = document.createElement('textarea');
            ta.value = lastXml;
            document.body.appendChild(ta);
            ta.select();
            document.execCommand('copy');
            document.body.removeChild(ta);
        }
    });

    dlBtn.addEventListener('click', function () {
        if (!lastXml) return;
        var blob = new Blob([lastXml], { type: 'application/xml' });
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'sitemap.xml';
        document.body.appendChild(a);
        a.click();
        setTimeout(function () { URL.revokeObjectURL(a.href); document.body.removeChild(a); }, 500);
    });
})();
</script>
@endsection
