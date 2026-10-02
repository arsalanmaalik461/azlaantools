@extends('layouts.app')

@section('title', 'Nginx Config Generator - Azlaan Tools')
@section('meta_description', 'Generate nginx server blocks for reverse proxy, SSL and static sites. Free developer tool, no signup.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Nginx Config Generator</h1>
            <p class="lead text-muted">Generate a ready-to-use nginx <code>server</code> block for a reverse proxy, static website, or PHP site — with or without SSL.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="domainInput" class="form-label fw-semibold">Domain</label>
                            <input type="text" class="form-control" id="domainInput" placeholder="example.com" dir="ltr">
                        </div>
                        <div class="col-md-6">
                            <label for="typeSel" class="form-label fw-semibold">Site type</label>
                            <select class="form-select" id="typeSel">
                                <option value="proxy" selected>Reverse proxy (Node/Python/Java app)</option>
                                <option value="static">Static website (HTML files)</option>
                                <option value="php">PHP site (PHP-FPM)</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-3" id="proxyRow">
                        <div class="col-md-6">
                            <label for="backendInput" class="form-label fw-semibold">Backend address</label>
                            <input type="text" class="form-control" id="backendInput" value="127.0.0.1:3000" dir="ltr">
                        </div>
                        <div class="col-md-6">
                            <label for="bodySizeInput" class="form-label fw-semibold">client_max_body_size</label>
                            <input type="text" class="form-control" id="bodySizeInput" value="10M" dir="ltr">
                        </div>
                    </div>

                    <div class="row g-3 mb-3 d-none" id="rootRow">
                        <div class="col-md-6">
                            <label for="rootInput" class="form-label fw-semibold">Document root path</label>
                            <input type="text" class="form-control" id="rootInput" value="/var/www/html" dir="ltr">
                        </div>
                        <div class="col-md-6">
                            <label for="phpSockInput" class="form-label fw-semibold">PHP-FPM socket</label>
                            <input type="text" class="form-control" id="phpSockInput" value="unix:/run/php/php8.2-fpm.sock" dir="ltr">
                            <div class="form-text">Used only for a PHP site.</div>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <div class="form-check form-switch pt-4">
                                <input class="form-check-input" type="checkbox" id="sslCheck" checked>
                                <label class="form-check-label fw-semibold" for="sslCheck">SSL (HTTPS)</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check form-switch pt-4">
                                <input class="form-check-input" type="checkbox" id="wwwCheck">
                                <label class="form-check-label fw-semibold" for="wwwCheck">www redirect (www -&gt; non-www)</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check form-switch pt-4">
                                <input class="form-check-input" type="checkbox" id="gzipCheck" checked>
                                <label class="form-check-label fw-semibold" for="gzipCheck">gzip compression</label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 flex-wrap">
                        <button type="button" class="btn btn-primary flex-grow-1" id="goBtn">Generate Config</button>
                        <button type="button" class="btn btn-outline-secondary" id="copyBtn" disabled>Copy</button>
                        <button type="button" class="btn btn-outline-success" id="dlBtn" disabled>.conf Download</button>
                    </div>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-semibold">Generated nginx config</span>
                            <span class="badge bg-info" id="confName"></span>
                        </div>
                        <pre class="border rounded bg-light p-3 small overflow-auto" id="confOut" style="max-height:420px;"></pre>
                        <div class="alert alert-info small mt-2 mb-0">Save the file in <code>/etc/nginx/sites-available/&lt;domain&gt;</code>, test it with <code>nginx -t</code>, then run <code>nginx -s reload</code>. For SSL with Let's Encrypt: <code>certbot --nginx -d example.com</code>.</div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Enter the domain and choose the site type.</li>
                <li>For a reverse proxy, enter the backend address; for static/PHP, enter the document root.</li>
                <li>Select the SSL, www redirect, and gzip options.</li>
                <li>Press <strong>Generate Config</strong> — the config will appear below, ready to use.</li>
                <li>Copy or download it and use it on your server.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var domainInput = document.getElementById('domainInput');
    var typeSel = document.getElementById('typeSel');
    var proxyRow = document.getElementById('proxyRow');
    var rootRow = document.getElementById('rootRow');
    var backendInput = document.getElementById('backendInput');
    var bodySizeInput = document.getElementById('bodySizeInput');
    var rootInput = document.getElementById('rootInput');
    var phpSockInput = document.getElementById('phpSockInput');
    var sslCheck = document.getElementById('sslCheck');
    var wwwCheck = document.getElementById('wwwCheck');
    var gzipCheck = document.getElementById('gzipCheck');
    var goBtn = document.getElementById('goBtn');
    var copyBtn = document.getElementById('copyBtn');
    var dlBtn = document.getElementById('dlBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var confOut = document.getElementById('confOut');
    var confName = document.getElementById('confName');
    var lastConf = '';

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }

    typeSel.addEventListener('change', function () {
        var isProxy = typeSel.value === 'proxy';
        proxyRow.classList.toggle('d-none', !isProxy);
        rootRow.classList.toggle('d-none', isProxy);
    });

    goBtn.addEventListener('click', function () {
        hideError();
        var domain = domainInput.value.trim().toLowerCase().replace(/^https?:\/\//, '').replace(/\/.*$/, '');
        if (!/^[a-z0-9]([a-z0-9.-]*[a-z0-9])?\.[a-z]{2,}$/.test(domain)) {
            showError('Please enter a valid domain (for example example.com).'); return;
        }
        var type = typeSel.value;
        var ssl = sslCheck.checked, www = wwwCheck.checked, gzip = gzipCheck.checked;
        var backend = backendInput.value.trim() || '127.0.0.1:3000';
        var root = rootInput.value.trim() || '/var/www/html';
        var phpSock = phpSockInput.value.trim() || 'unix:/run/php/php8.2-fpm.sock';
        var bodySize = bodySizeInput.value.trim() || '10M';

        var L = [];
        L.push('# ' + domain + ' - generated by Azlaan Tools');
        L.push('# Test with: nginx -t  | Reload with: nginx -s reload');
        L.push('');

        if (www) {
            L.push('# www -> non-www redirect');
            L.push('server {');
            L.push('    listen 80;');
            if (ssl) L.push('    listen 443 ssl;');
            L.push('    server_name www.' + domain + ';');
            L.push('    return 301 http' + (ssl ? 's' : '') + '://' + domain + '$request_uri;');
            L.push('}');
            L.push('');
        }

        if (ssl) {
            L.push('# HTTP -> HTTPS redirect');
            L.push('server {');
            L.push('    listen 80;');
            L.push('    server_name ' + domain + ';');
            L.push('    return 301 https://$host$request_uri;');
            L.push('}');
            L.push('');
        }

        L.push('server {');
        if (ssl) {
            L.push('    listen 443 ssl;');
            L.push('    http2 on;');
            L.push('');
            L.push('    # Replace with your real certificate paths (e.g. from certbot):');
            L.push('    ssl_certificate /etc/letsencrypt/live/' + domain + '/fullchain.pem;');
            L.push('    ssl_certificate_key /etc/letsencrypt/live/' + domain + '/privkey.pem;');
            L.push('    ssl_protocols TLSv1.2 TLSv1.3;');
        } else {
            L.push('    listen 80;');
        }
        L.push('    server_name ' + domain + ';');
        L.push('');

        if (gzip) {
            L.push('    gzip on;');
            L.push('    gzip_types text/plain text/css application/json application/javascript text/xml application/xml;');
            L.push('    gzip_min_length 1024;');
            L.push('');
        }

        if (type === 'proxy') {
            L.push('    client_max_body_size ' + bodySize + ';');
            L.push('');
            L.push('    location / {');
            L.push('        proxy_pass http://' + backend + ';');
            L.push('        proxy_http_version 1.1;');
            L.push('        proxy_set_header Host $host;');
            L.push('        proxy_set_header X-Real-IP $remote_addr;');
            L.push('        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;');
            L.push('        proxy_set_header X-Forwarded-Proto $scheme;');
            L.push('        proxy_set_header Upgrade $http_upgrade;');
            L.push('        proxy_set_header Connection "upgrade";');
            L.push('    }');
        } else if (type === 'static') {
            L.push('    root ' + root + ';');
            L.push('    index index.html index.htm;');
            L.push('');
            L.push('    location / {');
            L.push('        try_files $uri $uri/ =404;');
            L.push('    }');
            L.push('');
            L.push('    # Cache static assets for 30 days');
            L.push('    location ~* \\.(jpg|jpeg|png|gif|ico|css|js|svg|woff2?)$ {');
            L.push('        expires 30d;');
            L.push('        add_header Cache-Control "public, immutable";');
            L.push('    }');
        } else {
            L.push('    root ' + root + ';');
            L.push('    index index.php index.html index.htm;');
            L.push('');
            L.push('    location / {');
            L.push('        try_files $uri $uri/ /index.php?$query_string;');
            L.push('    }');
            L.push('');
            L.push('    location ~ \\.php$ {');
            L.push('        include fastcgi_params;');
            L.push('        fastcgi_pass ' + phpSock + ';');
            L.push('        fastcgi_index index.php;');
            L.push('        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;');
            L.push('    }');
        }
        L.push('');
        L.push('    # Block hidden files (.git, .env, etc.)');
        L.push('    location ~ /\\. {');
        L.push('        deny all;');
        L.push('    }');
        L.push('}');

        lastConf = L.join('\n');
        confOut.textContent = lastConf;
        confName.textContent = domain + '.conf';
        copyBtn.disabled = false;
        dlBtn.disabled = false;
        results.classList.remove('d-none');
    });

    copyBtn.addEventListener('click', function () {
        if (!lastConf) return;
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(lastConf).then(function () {
                copyBtn.textContent = 'Copied!';
                setTimeout(function () { copyBtn.textContent = 'Copy'; }, 2000);
            });
        }
    });

    dlBtn.addEventListener('click', function () {
        if (!lastConf) return;
        var domain = domainInput.value.trim().toLowerCase().replace(/^https?:\/\//, '').replace(/\/.*$/, '');
        var blob = new Blob([lastConf], { type: 'text/plain' });
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = domain + '.conf';
        document.body.appendChild(a);
        a.click();
        setTimeout(function () { URL.revokeObjectURL(a.href); document.body.removeChild(a); }, 500);
    });
})();
</script>
@endsection
