@extends('layouts.app')
@section('title', 'XML Sitemap Generator - Azlaan Tools')
@section('meta_description', 'Build a valid XML sitemap from a list of your page URLs, ready to submit to Google. Free online.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">XML Sitemap Generator</h1>
            <p class="lead text-muted">Build a standard XML sitemap from a list of your page URLs — ready to submit to Google Search Console. Write one URL per line.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="urls" class="form-label fw-semibold">URLs (one per line, with https://)</label>
                        <textarea class="form-control" id="urls" rows="8" placeholder="https://example.com/&#10;https://example.com/about&#10;https://example.com/contact"></textarea>
                        <div class="form-text">Duplicate or bad URLs are filtered out automatically.</div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="changefreq" class="form-label fw-semibold">Change frequency</label>
                            <select class="form-select" id="changefreq">
                                <option value="">(none)</option>
                                <option value="daily">daily</option>
                                <option value="weekly" selected>weekly</option>
                                <option value="monthly">monthly</option>
                                <option value="yearly">yearly</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="priority" class="form-label fw-semibold">Priority</label>
                            <select class="form-select" id="priority">
                                <option value="">(none)</option>
                                <option value="1.0">1.0</option>
                                <option value="0.9">0.9</option>
                                <option value="0.8" selected>0.8</option>
                                <option value="0.7">0.7</option>
                                <option value="0.6">0.6</option>
                                <option value="0.5">0.5</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="lastmod" class="form-label fw-semibold">Last modified</label>
                            <input type="date" class="form-control" id="lastmod">
                            <div class="form-text">Leave empty to use today&apos;s date.</div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100 mt-3" id="goBtn">Generate Sitemap</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="alert alert-success" id="smInfo" role="alert"></div>
                        <label for="smOutput" class="form-label fw-semibold">sitemap.xml</label>
                        <textarea class="form-control font-monospace" id="smOutput" rows="10" readonly style="font-size:12px;"></textarea>
                        <div class="d-flex gap-2 mt-3">
                            <button type="button" class="btn btn-success flex-fill" id="smDownload">Download sitemap.xml</button>
                            <button type="button" class="btn btn-outline-secondary flex-fill" id="smCopy">Copy to Clipboard</button>
                        </div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Write your page URLs in the list (one per line).</li>
                <li>Pick change frequency, priority and lastmod.</li>
                <li>Press Generate, then download sitemap.xml, upload it to your site&apos;s root and submit it in Google Search Console.</li>
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
    function validUrl(s) {
        try {
            var u = new URL(s);
            return (u.protocol === 'http:' || u.protocol === 'https:') ? u.href : null;
        } catch (e) { return null; }
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var lines = document.getElementById('urls').value.split('\n');
        var seen = {}, clean = [];
        lines.forEach(function (l) {
            var t = l.trim();
            if (!t) return;
            var u = validUrl(t);
            if (u && !seen[u]) { seen[u] = 1; clean.push(u); }
        });
        if (!clean.length) {
            showError('Please enter at least one valid URL. Write the full URL with https:// on each line.');
            return;
        }
        var freq = document.getElementById('changefreq').value;
        var prio = document.getElementById('priority').value;
        var lm = document.getElementById('lastmod').value;
        if (!lm) {
            var d = new Date();
            lm = d.getFullYear() + '-' + ('0' + (d.getMonth() + 1)).slice(-2) + '-' + ('0' + d.getDate()).slice(-2);
        }
        var xml = '<?xml version="1.0" encoding="UTF-8"?>\n<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">\n';
        clean.forEach(function (u) {
            xml += '  <url>\n    <loc>' + escXml(u) + '</loc>\n    <lastmod>' + lm + '</lastmod>\n';
            if (freq) xml += '    <changefreq>' + freq + '</changefreq>\n';
            if (prio) xml += '    <priority>' + prio + '</priority>\n';
            xml += '  </url>\n';
        });
        xml += '</urlset>';
        document.getElementById('smOutput').value = xml;
        document.getElementById('smInfo').textContent = clean.length + ' URLs — your sitemap is ready.';
        results.classList.remove('d-none');
    });

    document.getElementById('smDownload').addEventListener('click', function () {
        var xml = document.getElementById('smOutput').value;
        if (!xml) return;
        var blob = new Blob([xml], { type: 'application/xml' });
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'sitemap.xml';
        document.body.appendChild(a);
        a.click();
        a.remove();
        setTimeout(function () { URL.revokeObjectURL(a.href); }, 3000);
    });

    document.getElementById('smCopy').addEventListener('click', function () {
        var t = document.getElementById('smOutput');
        t.select();
        try {
            document.execCommand('copy');
            var b = document.getElementById('smCopy');
            b.textContent = 'Copied!';
            setTimeout(function () { b.textContent = 'Copy to Clipboard'; }, 1500);
        } catch (e) {
            if (navigator.clipboard) navigator.clipboard.writeText(t.value);
        }
    });
})();
</script>
@endsection
