@extends('layouts.app')
@section('title', 'Sitemap XML Validator - Check Sitemap Errors Free | Azlaan Tools')
@section('meta_description', 'Validate your sitemap.xml free in the browser: XML errors, missing loc tags, bad URLs, duplicate entries and size limits checked instantly.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-2">Sitemap XML Validator</h1>
            <p class="lead text-muted">Check if your sitemap XML is correct or not — find errors instantly. The file is checked right here in the browser, it is not uploaded.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="xmlInput" class="form-label fw-semibold">Paste sitemap XML</label>
                        <textarea class="form-control font-monospace" id="xmlInput" rows="8" placeholder="&lt;?xml version=&quot;1.0&quot; encoding=&quot;UTF-8&quot;?&gt;&#10;&lt;urlset xmlns=&quot;http://www.sitemaps.org/schemas/sitemap/0.9&quot;&gt;&#10;  ..."></textarea>
                    </div>
                    <div class="d-flex gap-2 flex-wrap align-items-center mb-3">
                        <label class="btn btn-outline-secondary mb-0" for="xmlFile">Upload XML File
                            <input type="file" id="xmlFile" class="d-none" accept=".xml,text/xml">
                        </label>
                        <button type="button" id="sampleBtn" class="btn btn-outline-secondary">Sample Sitemap</button>
                    </div>
                    <button type="button" class="btn btn-primary btn-lg w-100" id="validateBtn">Validate</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="row text-center g-2 mb-3">
                            <div class="col-6 col-md-3"><div class="border rounded p-2"><div class="small text-muted">Type</div><strong id="statType">-</strong></div></div>
                            <div class="col-6 col-md-3"><div class="border rounded p-2"><div class="small text-muted">URLs</div><strong id="statUrls">-</strong></div></div>
                            <div class="col-6 col-md-3"><div class="border rounded p-2"><div class="small text-muted">Errors</div><strong id="statErrors" class="text-danger">-</strong></div></div>
                            <div class="col-6 col-md-3"><div class="border rounded p-2"><div class="small text-muted">Warnings</div><strong id="statWarnings" class="text-warning">-</strong></div></div>
                        </div>
                        <div id="verdictBox"></div>
                        <h3 class="h6 mt-3">Errors</h3>
                        <div id="errorList"></div>
                        <h3 class="h6 mt-3">Warnings</h3>
                        <div id="warnList"></div>
                    </div>
                </div>
            </div>

            <h2>What this validator checks</h2>
            <ul>
                <li>Whether the XML is well-formed (tag mismatch, encoding problems).</li>
                <li>The root element is <code>&lt;urlset&gt;</code> or <code>&lt;sitemapindex&gt;</code>.</li>
                <li>Every entry has a <code>&lt;loc&gt;</code> with a valid absolute URL.</li>
                <li>The <code>&lt;lastmod&gt;</code> date, <code>&lt;changefreq&gt;</code> and <code>&lt;priority&gt;</code> values are correct.</li>
                <li>Duplicate URLs and the 50,000 URLs / 50 MB limit.</li>
            </ul>

            <h2>How to use</h2>
            <ol>
                <li>Paste your sitemap.xml content or upload a file.</li>
                <li>Press <strong>Validate</strong>.</li>
                <li>Fix the errors, then submit the sitemap in Google Search Console.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var xmlInput = document.getElementById('xmlInput');
    var xmlFile = document.getElementById('xmlFile');
    var sampleBtn = document.getElementById('sampleBtn');
    var validateBtn = document.getElementById('validateBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var statType = document.getElementById('statType');
    var statUrls = document.getElementById('statUrls');
    var statErrors = document.getElementById('statErrors');
    var statWarnings = document.getElementById('statWarnings');
    var verdictBox = document.getElementById('verdictBox');
    var errorList = document.getElementById('errorList');
    var warnList = document.getElementById('warnList');

    var CHANGEFREQ = ['always', 'hourly', 'daily', 'weekly', 'monthly', 'yearly', 'never'];
    var DATE_RE = /^\d{4}-\d{2}-\d{2}(T\d{2}:\d{2}(:\d{2})?(\.\d+)?(Z|[+-]\d{2}:?\d{2})?)?$/;

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() { errorBox.classList.add('d-none'); errorBox.textContent = ''; }

    function esc(s) {
        return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

    function childText(el, tag) {
        var kids = el.childNodes;
        for (var i = 0; i < kids.length; i++) {
            if (kids[i].nodeType === 1 && kids[i].localName === tag) {
                return kids[i].textContent || '';
            }
        }
        return null;
    }

    function validAbsUrl(u) {
        try {
            var url = new URL(u);
            return url.protocol === 'http:' || url.protocol === 'https:';
        } catch (e) { return false; }
    }

    sampleBtn.addEventListener('click', function () {
        hideError();
        xmlInput.value = '<?xml version="1.0" encoding="UTF-8"?>\n' +
            '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">\n' +
            '  <url>\n' +
            '    <loc>https://example.com/</loc>\n' +
            '    <lastmod>2026-10-01</lastmod>\n' +
            '    <changefreq>daily</changefreq>\n' +
            '    <priority>1.0</priority>\n' +
            '  </url>\n' +
            '  <url>\n' +
            '    <loc>https://example.com/about</loc>\n' +
            '    <lastmod>2026-13-45</lastmod>\n' +
            '    <priority>1.5</priority>\n' +
            '  </url>\n' +
            '  <url>\n' +
            '    <loc>https://example.com/about</loc>\n' +
            '    <changefreq>sometimes</changefreq>\n' +
            '  </url>\n' +
            '  <url>\n' +
            '    <loc>http://example.com/contact</loc>\n' +
            '  </url>\n' +
            '  <url>\n' +
            '  </url>\n' +
            '</urlset>';
    });

    xmlFile.addEventListener('change', function () {
        hideError();
        var f = xmlFile.files[0];
        if (!f) return;
        if (f.size > 5 * 1048576) { showError('File is larger than 5 MB — a sitemap this large cannot be checked in the browser.'); return; }
        var r = new FileReader();
        r.onload = function () { xmlInput.value = String(r.result || '').slice(0, 2000000); };
        r.onerror = function () { showError('There was a problem reading the file.'); };
        r.readAsText(f);
    });

    validateBtn.addEventListener('click', function () {
        hideError();
        var raw = xmlInput.value;
        if (!raw.trim()) { showError('Please paste the sitemap XML or upload a file first.'); return; }
        var errors = [];
        var warnings = [];
        var doc;
        try {
            var parser = new DOMParser();
            doc = parser.parseFromString(raw, 'text/xml');
        } catch (e) {
            showError('Could not parse XML: ' + e.message);
            return;
        }
        var perr = doc.getElementsByTagName('parsererror');
        if (perr.length) {
            showError('XML has a syntax error: ' + perr[0].textContent.slice(0, 300));
            return;
        }
        var root = doc.documentElement;
        var rootName = root.localName || root.nodeName;
        var isUrlset = rootName === 'urlset';
        var isIndex = rootName === 'sitemapindex';
        if (!isUrlset && !isIndex) {
            showError('Root element must be <urlset> or <sitemapindex>, found: <' + rootName + '>.');
            return;
        }
        var ns = root.getAttribute('xmlns') || '';
        if (ns.indexOf('sitemaps.org') === -1) {
            warnings.push('Official sitemap namespace (http://www.sitemaps.org/schemas/sitemap/0.9) is not declared on the root.');
        }
        var entryTag = isUrlset ? 'url' : 'sitemap';
        var entries = [];
        var kids = root.childNodes;
        for (var i = 0; i < kids.length; i++) {
            if (kids[i].nodeType === 1 && kids[i].localName === entryTag) entries.push(kids[i]);
        }
        var seen = {};
        for (var n = 0; n < entries.length; n++) {
            var el = entries[n];
            var label = 'Entry #' + (n + 1);
            var locRaw = childText(el, 'loc');
            if (locRaw === null || !locRaw.trim()) {
                errors.push(label + ': <loc> tag is missing or empty.');
                continue;
            }
            var loc = locRaw;
            if (loc !== loc.trim()) warnings.push(label + ': <loc> has extra space around it — trim it.');
            loc = loc.trim();
            if (!validAbsUrl(loc)) {
                errors.push(label + ': <loc> is not a valid absolute URL: ' + loc.slice(0, 80));
            } else {
                if (loc.indexOf('https://') !== 0) warnings.push(label + ': URL is not on https: ' + loc.slice(0, 80));
                if (loc.length > 2048) errors.push(label + ': URL is longer than 2048 characters.');
                if (seen[loc]) {
                    errors.push(label + ': duplicate URL — already listed: ' + loc.slice(0, 80));
                } else { seen[loc] = true; }
            }
            var lm = childText(el, 'lastmod');
            if (lm !== null && lm.trim() && !DATE_RE.test(lm.trim())) {
                errors.push(label + ': <lastmod> date format is wrong: ' + lm.trim().slice(0, 40) + ' — use YYYY-MM-DD.');
            }
            var cf = childText(el, 'changefreq');
            if (cf !== null && cf.trim() && CHANGEFREQ.indexOf(cf.trim().toLowerCase()) === -1) {
                errors.push(label + ': <changefreq> value is wrong: ' + cf.trim().slice(0, 30));
            }
            var pr = childText(el, 'priority');
            if (pr !== null && pr.trim()) {
                var pv = parseFloat(pr.trim());
                if (isNaN(pv) || pv < 0 || pv > 1) {
                    errors.push(label + ': <priority> must be between 0.0 and 1.0, found: ' + pr.trim().slice(0, 20));
                }
            }
            if (!isUrlset) {
                if (childText(el, 'lastmod') === null) warnings.push(label + ': sitemapindex entry has no <lastmod>.');
            }
        }
        if (entries.length === 0) errors.push('No <' + entryTag + '> entry found — sitemap is empty.');
        if (entries.length > 50000) errors.push('URL count is over 50,000 (' + entries.length + ') — split the sitemap.');

        statType.textContent = isUrlset ? 'urlset' : 'sitemapindex';
        statUrls.textContent = entries.length;
        statErrors.textContent = errors.length;
        statWarnings.textContent = warnings.length;

        if (errors.length === 0 && warnings.length === 0) {
            verdictBox.innerHTML = '<div class="alert alert-success mb-0"><strong>All good!</strong> Sitemap is fully correct — no errors or warnings.</div>';
        } else if (errors.length === 0) {
            verdictBox.innerHTML = '<div class="alert alert-warning mb-0"><strong>Almost correct.</strong> No errors, but there are some warnings — it is better to fix them too.</div>';
        } else {
            verdictBox.innerHTML = '<div class="alert alert-danger mb-0"><strong>' + errors.length + ' errors found.</strong> Google will not process the sitemap correctly without fixing these.</div>';
        }

        function listHtml(arr, emptyMsg, cls) {
            if (!arr.length) return '<p class="text-muted small">' + emptyMsg + '</p>';
            var h = '<ul class="list-group list-group-flush">';
            for (var k = 0; k < arr.length; k++) {
                h += '<li class="list-group-item ' + cls + ' small">' + esc(arr[k]) + '</li>';
            }
            return h + '</ul>';
        }
        errorList.innerHTML = listHtml(errors, 'No errors — great!', 'list-group-item-danger');
        warnList.innerHTML = listHtml(warnings, 'No warnings.', 'list-group-item-warning');
        results.classList.remove('d-none');
        results.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    });
})();
</script>
@endsection
