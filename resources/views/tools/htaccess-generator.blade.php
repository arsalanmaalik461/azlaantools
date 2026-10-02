@extends('layouts.app')
@section('title', 'Htaccess Generator - Azlaan Tools')
@section('meta_description', 'Generate Apache .htaccess rules online: force HTTPS, www or non-www redirects, browser caching, GZIP and hotlink protection. Free.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Htaccess Generator</h1>
            <p class="lead text-muted">Make Apache <code>.htaccess</code> rules for your website — HTTPS redirect, www setting, caching and security, without writing code.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="siteDomain" class="form-label fw-semibold">Your domain (without https://)</label>
                        <input type="text" class="form-control" id="siteDomain" placeholder="example.com">
                    </div>

                    <h2 class="h6 fw-bold mt-4 mb-3">Redirects</h2>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="optHttps" checked>
                        <label class="form-check-label" for="optHttps">Force HTTPS (http → https 301 redirect)</label>
                    </div>
                    <div class="mb-3">
                        <label for="optWww" class="form-label fw-semibold">WWW preference</label>
                        <select class="form-select" id="optWww">
                            <option value="leave">No change</option>
                            <option value="www">Always add www (example.com → www.example.com)</option>
                            <option value="nonwww">Always remove www (www.example.com → example.com)</option>
                        </select>
                    </div>

                    <h2 class="h6 fw-bold mt-4 mb-3">Performance</h2>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="optCache" checked>
                        <label class="form-check-label" for="optCache">Browser caching (expiry headers for images, CSS, JS)</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="optGzip" checked>
                        <label class="form-check-label" for="optGzip">GZIP compression (mod_deflate — reduces page size)</label>
                    </div>

                    <h2 class="h6 fw-bold mt-4 mb-3">Security</h2>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="optIndexes" checked>
                        <label class="form-check-label" for="optIndexes">Disable directory listing (Options -Indexes)</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="optHotlink">
                        <label class="form-check-label" for="optHotlink">Block image hotlinking (so other sites cannot use your images)</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="optProtect" checked>
                        <label class="form-check-label" for="optProtect">Block sensitive files (.env, .git, .htaccess itself)</label>
                    </div>
                    <div class="mb-3">
                        <label for="path404" class="form-label fw-semibold">Custom 404 page (leave empty if not needed)</label>
                        <input type="text" class="form-control" id="path404" placeholder="/404.html">
                    </div>

                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-primary" id="genBtn">Generate .htaccess</button>
                        <button type="button" class="btn btn-outline-secondary d-none" id="copyBtn">Copy</button>
                        <button type="button" class="btn btn-outline-secondary d-none" id="dlBtn">Download .htaccess</button>
                    </div>

                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <label class="form-label fw-semibold" for="outArea">Generated .htaccess</label>
                        <textarea class="form-control font-monospace" id="outArea" rows="18" readonly style="font-size: 0.85rem;"></textarea>
                        <div class="alert alert-info mt-3 mb-0 small">
                            Upload this file to your website's <strong>root folder</strong> (where the index file is) with the name <code>.htaccess</code>. Take a backup of the old file first — a wrong rule can take your site down. These rules only work on <strong>Apache</strong> servers, not on Nginx.
                        </div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Enter your domain and select the options you need.</li>
                <li>Press <strong>Generate .htaccess</strong>.</li>
                <li>Copy the code or download the file, and save it in your hosting root folder as <code>.htaccess</code>.</li>
                <li>Open your site and check that everything works fine.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var siteDomain = document.getElementById('siteDomain');
    var optHttps = document.getElementById('optHttps');
    var optWww = document.getElementById('optWww');
    var optCache = document.getElementById('optCache');
    var optGzip = document.getElementById('optGzip');
    var optIndexes = document.getElementById('optIndexes');
    var optHotlink = document.getElementById('optHotlink');
    var optProtect = document.getElementById('optProtect');
    var path404 = document.getElementById('path404');
    var genBtn = document.getElementById('genBtn');
    var copyBtn = document.getElementById('copyBtn');
    var dlBtn = document.getElementById('dlBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var outArea = document.getElementById('outArea');
    var lastOutput = '';

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function escDomain(d) {
        return d.replace(/\./g, '\\.');
    }

    genBtn.addEventListener('click', function () {
        hideError();
        var domain = siteDomain.value.trim().toLowerCase()
            .replace(/^https?:\/\//, '').replace(/^www\./, '').replace(/\/.*$/, '');
        var needDomain = optHttps.checked || optWww.value !== 'leave' || optHotlink.checked;
        if (needDomain && !/^[a-z0-9-]+(\.[a-z0-9-]+)+$/i.test(domain)) {
            showError('Enter a valid domain for the redirect options, for example example.com');
            return;
        }
        var L = [];
        L.push('# .htaccess generated with Azlaan Tools - Htaccess Generator');
        L.push('# Upload this file to your website root folder as ".htaccess"');
        L.push('');

        if (optProtect.checked) {
            L.push('# Block access to sensitive files');
            L.push('<FilesMatch "(^\\.env$|^\\.git|^\\.htaccess|wp-config\\.php$)">');
            L.push('    Require all denied');
            L.push('</FilesMatch>');
            L.push('');
        }
        if (optIndexes.checked) {
            L.push('# Disable directory listing');
            L.push('Options -Indexes');
            L.push('');
        }

        var rewrite = [];
        if (optHttps.checked) {
            rewrite.push('    # Force HTTPS');
            rewrite.push('    RewriteCond %{HTTPS} off');
            rewrite.push('    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]');
        }
        if (optWww.value === 'www') {
            rewrite.push('    # Force www');
            rewrite.push('    RewriteCond %{HTTP_HOST} ^' + escDomain(domain) + ' [NC]');
            rewrite.push('    RewriteRule ^(.*)$ https://www.' + domain + '/$1 [L,R=301]');
        } else if (optWww.value === 'nonwww') {
            rewrite.push('    # Force non-www');
            rewrite.push('    RewriteCond %{HTTP_HOST} ^www\\.' + escDomain(domain) + ' [NC]');
            rewrite.push('    RewriteRule ^(.*)$ https://' + domain + '/$1 [L,R=301]');
        }
        if (optHotlink.checked) {
            rewrite.push('    # Block image hotlinking');
            rewrite.push('    RewriteCond %{HTTP_REFERER} !^$');
            rewrite.push('    RewriteCond %{HTTP_REFERER} !^https?://(www\\.)?' + escDomain(domain) + ' [NC]');
            rewrite.push('    RewriteRule \\.(jpg|jpeg|png|gif|webp|svg)$ - [F,NC]');
        }
        if (rewrite.length) {
            L.push('<IfModule mod_rewrite.c>');
            L.push('    RewriteEngine On');
            L = L.concat(rewrite);
            L.push('</IfModule>');
            L.push('');
        }

        if (optCache.checked) {
            L.push('# Browser caching');
            L.push('<IfModule mod_expires.c>');
            L.push('    ExpiresActive On');
            L.push('    ExpiresByType image/jpg "access plus 1 year"');
            L.push('    ExpiresByType image/jpeg "access plus 1 year"');
            L.push('    ExpiresByType image/png "access plus 1 year"');
            L.push('    ExpiresByType image/gif "access plus 1 year"');
            L.push('    ExpiresByType image/webp "access plus 1 year"');
            L.push('    ExpiresByType image/svg+xml "access plus 1 month"');
            L.push('    ExpiresByType text/css "access plus 1 month"');
            L.push('    ExpiresByType application/javascript "access plus 1 month"');
            L.push('    ExpiresByType application/pdf "access plus 1 month"');
            L.push('</IfModule>');
            L.push('');
        }
        if (optGzip.checked) {
            L.push('# GZIP compression');
            L.push('<IfModule mod_deflate.c>');
            L.push('    AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css');
            L.push('    AddOutputFilterByType DEFLATE application/javascript application/json application/xml');
            L.push('    AddOutputFilterByType DEFLATE image/svg+xml font/woff font/woff2');
            L.push('</IfModule>');
            L.push('');
        }
        var p404 = path404.value.trim();
        if (p404) {
            if (p404.charAt(0) !== '/') p404 = '/' + p404;
            L.push('# Custom 404 page');
            L.push('ErrorDocument 404 ' + p404);
            L.push('');
        }

        lastOutput = L.join('\n');
        outArea.value = lastOutput;
        results.classList.remove('d-none');
        copyBtn.classList.remove('d-none');
        dlBtn.classList.remove('d-none');
        results.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    });

    copyBtn.addEventListener('click', function () {
        outArea.select();
        try { document.execCommand('copy'); } catch (e) { /* noop */ }
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(lastOutput).catch(function () { /* noop */ });
        }
        copyBtn.textContent = 'Copied!';
        setTimeout(function () { copyBtn.textContent = 'Copy'; }, 1500);
    });

    dlBtn.addEventListener('click', function () {
        var blob = new Blob([lastOutput], { type: 'text/plain' });
        var url = URL.createObjectURL(blob);
        var a = document.createElement('a');
        a.href = url;
        a.download = 'htaccess.txt';
        document.body.appendChild(a);
        a.click();
        setTimeout(function () { document.body.removeChild(a); URL.revokeObjectURL(url); }, 500);
    });
})();
</script>
@endsection
