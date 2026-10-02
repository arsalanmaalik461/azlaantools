@extends('layouts.app')
@section('title', 'Dockerfile Generator - Azlaan Tools')
@section('meta_description', 'Generate starter Dockerfiles for Node.js, Python, PHP, Go and Java applications — free online, no signup.')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Dockerfile Generator</h1>
            <p class="lead text-muted">Create a starter Dockerfile for your Node, Python, PHP, Go or Java app — select the options, then copy or download.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="langSel" class="form-label fw-semibold">Language / runtime</label>
                            <select id="langSel" class="form-select">
                                <option value="node" selected>Node.js</option>
                                <option value="python">Python</option>
                                <option value="php">PHP (Apache)</option>
                                <option value="go">Go</option>
                                <option value="java">Java (Maven)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="verSel" class="form-label fw-semibold">Base image version</label>
                            <select id="verSel" class="form-select"></select>
                        </div>
                        <div class="col-md-6">
                            <label for="portInput" class="form-label fw-semibold">App port (EXPOSE)</label>
                            <input type="number" class="form-control" id="portInput" min="1" max="65535" value="3000">
                        </div>
                        <div class="col-md-6">
                            <label for="workdirInput" class="form-label fw-semibold">Work directory</label>
                            <input type="text" class="form-control" id="workdirInput" value="/app">
                        </div>
                        <div class="col-md-6">
                            <label for="installInput" class="form-label fw-semibold">Install / build command</label>
                            <input type="text" class="form-control" id="installInput" value="">
                            <div class="form-text">Leave empty to use the default.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="runInput" class="form-label fw-semibold">Start command (CMD)</label>
                            <input type="text" class="form-control" id="runInput" value="">
                            <div class="form-text">Leave empty to use the default.</div>
                        </div>
                    </div>

                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="multiStageCheck" checked>
                        <label class="form-check-label fw-semibold" for="multiStageCheck">Multi-stage build (smaller final image — recommended)</label>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="nonRootCheck" checked>
                        <label class="form-check-label fw-semibold" for="nonRootCheck">Run as non-root user (safer)</label>
                    </div>

                    <button type="button" class="btn btn-primary w-100" id="goBtn">Generate Dockerfile</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="d-flex flex-wrap gap-2 mb-2">
                            <button type="button" class="btn btn-success btn-sm" id="copyBtn">Copy Dockerfile</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="downloadBtn">Download Dockerfile</button>
                        </div>
                        <pre id="dockerOut" class="border rounded p-3 bg-light" style="white-space: pre-wrap; font-size: 13px; max-height: 480px; overflow: auto;"></pre>
                        <h5 class="mt-3">Build &amp; run commands</h5>
                        <pre id="cmdOut" class="border rounded p-3 bg-light" style="white-space: pre-wrap; font-size: 13px;"></pre>
                    </div>
                </div>
            </div>

            <div class="alert alert-info">
                <strong>Tip:</strong> Be sure to create a <code>.dockerignore</code> file in your project folder (node_modules, .git, __pycache__ etc.) so the image is smaller and the build is faster.
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select the language and version, and type the port and work directory.</li>
                <li>Change the install and start commands to match your app (or leave the defaults).</li>
                <li>Click <strong>Generate Dockerfile</strong>, then copy or download and save it in your project as <code>Dockerfile</code>.</li>
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
    var langSel = document.getElementById('langSel');
    var verSel = document.getElementById('verSel');

    var LANGS = {
        node: {
            versions: ['22-alpine', '20-alpine', '22-slim', '20-slim'],
            port: 3000, install: 'npm ci --only=production', run: 'node server.js',
            gen: function (o) { return nodeGen(o); }
        },
        python: {
            versions: ['3.12-slim', '3.11-slim', '3.12-alpine'],
            port: 8000, install: 'pip install --no-cache-dir -r requirements.txt', run: 'python app.py',
            gen: function (o) { return pythonGen(o); }
        },
        php: {
            versions: ['8.3-apache', '8.2-apache', '8.3-fpm'],
            port: 80, install: 'docker-php-ext-install pdo pdo_mysql', run: 'apache2-foreground',
            gen: function (o) { return phpGen(o); }
        },
        go: {
            versions: ['1.23-alpine', '1.22-alpine'],
            port: 8080, install: 'go build -o /app/server .', run: '/app/server',
            gen: function (o) { return goGen(o); }
        },
        java: {
            versions: ['21-jdk-slim', '17-jdk-slim'],
            port: 8080, install: 'mvn -q -DskipTests package', run: 'java -jar target/app.jar',
            gen: function (o) { return javaGen(o); }
        }
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
    function cmdArray(s) {
        var parts = s.trim().split(/\s+/);
        return '["' + parts.join('", "') + '"]';
    }

    function refreshVersions() {
        var L = LANGS[langSel.value];
        verSel.innerHTML = '';
        for (var i = 0; i < L.versions.length; i++) {
            var op = document.createElement('option');
            op.value = L.versions[i];
            op.textContent = L.versions[i];
            verSel.appendChild(op);
        }
        document.getElementById('portInput').value = L.port;
        document.getElementById('installInput').placeholder = 'Default: ' + L.install;
        document.getElementById('runInput').placeholder = 'Default: ' + L.run;
    }

    function opts() {
        var L = LANGS[langSel.value];
        var wd = document.getElementById('workdirInput').value.trim() || '/app';
        var port = parseInt(document.getElementById('portInput').value, 10);
        if (!port || port < 1 || port > 65535) { return null; }
        var install = document.getElementById('installInput').value.trim() || L.install;
        var run = document.getElementById('runInput').value.trim() || L.run;
        return {
            ver: verSel.value, wd: wd, port: port, install: install, run: run,
            multi: document.getElementById('multiStageCheck').checked,
            nonroot: document.getElementById('nonRootCheck').checked
        };
    }

    function userLine(o) {
        if (!o.nonroot) { return ''; }
        return 'RUN addgroup -S appgroup 2>/dev/null || groupadd -r appgroup; ' +
            'adduser -S appuser -G appgroup 2>/dev/null || useradd -r -g appgroup appuser\n' +
            'USER appuser\n';
    }

    function nodeGen(o) {
        var img = 'node:' + o.ver, out = '';
        if (o.multi) {
            out += 'FROM ' + img + ' AS build\nWORKDIR ' + o.wd +
                '\nCOPY package*.json ./\nRUN ' + o.install +
                '\nCOPY . .\n\nFROM ' + img + '\nWORKDIR ' + o.wd +
                '\nCOPY --from=build ' + o.wd + ' ' + o.wd + '\n';
        } else {
            out += 'FROM ' + img + '\nWORKDIR ' + o.wd +
                '\nCOPY package*.json ./\nRUN ' + o.install + '\nCOPY . .\n';
        }
        out += 'EXPOSE ' + o.port + '\n' + userLine(o) + 'CMD ' + cmdArray(o.run) + '\n';
        return out;
    }
    function pythonGen(o) {
        var img = 'python:' + o.ver, out = '';
        if (o.multi) {
            out += 'FROM ' + img + ' AS build\nWORKDIR ' + o.wd +
                '\nCOPY requirements.txt ./\nRUN ' + o.install +
                '\n\nFROM ' + img + '\nWORKDIR ' + o.wd +
                '\nCOPY --from=build /usr/local/lib/python' + o.ver.split('-')[0] + '/site-packages /usr/local/lib/python' + o.ver.split('-')[0] + '/site-packages\nCOPY . .\n';
        } else {
            out += 'FROM ' + img + '\nWORKDIR ' + o.wd +
                '\nCOPY requirements.txt ./\nRUN ' + o.install + '\nCOPY . .\n';
        }
        out += 'EXPOSE ' + o.port + '\n' + userLine(o) + 'CMD ' + cmdArray(o.run) + '\n';
        return out;
    }
    function phpGen(o) {
        var img = 'php:' + o.ver;
        var out = 'FROM ' + img + '\nWORKDIR /var/www/html\nCOPY . /var/www/html/\nRUN ' + o.install + '\nEXPOSE ' + o.port + '\nCMD ' + cmdArray(o.run) + '\n';
        return out;
    }
    function goGen(o) {
        var img = 'golang:' + o.ver, out = '';
        if (o.multi) {
            out += 'FROM ' + img + ' AS build\nWORKDIR ' + o.wd +
                '\nCOPY go.mod go.sum ./\nRUN go mod download\nCOPY . .\nRUN ' + o.install +
                '\n\nFROM alpine:latest\nWORKDIR ' + o.wd + '\nCOPY --from=build /app/server ./server\n';
        } else {
            out += 'FROM ' + img + '\nWORKDIR ' + o.wd +
                '\nCOPY go.mod go.sum ./\nRUN go mod download\nCOPY . .\nRUN ' + o.install + '\n';
        }
        out += 'EXPOSE ' + o.port + '\n' + userLine(o) + 'CMD ' + cmdArray(o.run) + '\n';
        return out;
    }
    function javaGen(o) {
        var img = 'maven:3.9-eclipse-temurin-' + o.ver.replace('jdk-', ''), out = '';
        if (o.multi) {
            out += 'FROM ' + img + ' AS build\nWORKDIR ' + o.wd +
                '\nCOPY pom.xml ./\nRUN mvn -q dependency:go-offline\nCOPY src ./src\nRUN ' + o.install +
                '\n\nFROM eclipse-temurin:' + o.ver + '\nWORKDIR ' + o.wd +
                '\nCOPY --from=build ' + o.wd + '/target/*.jar app.jar\n';
        } else {
            out += 'FROM ' + img + '\nWORKDIR ' + o.wd +
                '\nCOPY pom.xml ./\nRUN mvn -q dependency:go-offline\nCOPY src ./src\nRUN ' + o.install + '\n';
        }
        out += 'EXPOSE ' + o.port + '\n' + userLine(o) + 'CMD ' + cmdArray(o.run) + '\n';
        return out;
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var o = opts();
        if (!o) { showError('Port must be between 1 and 65535.'); return; }
        var df = LANGS[langSel.value].gen(o);
        var header = '# Generated by Azlaan Tools — Dockerfile Generator\n# Language: ' + langSel.options[langSel.selectedIndex].text + ' (' + o.ver + ')\n\n';
        document.getElementById('dockerOut').textContent = header + df;
        document.getElementById('cmdOut').textContent =
            '# Build the image\ndocker build -t myapp:latest .\n\n# Run the container\ndocker run -d -p ' + o.port + ':' + o.port + ' --name myapp myapp:latest';
        results.classList.remove('d-none');
        results.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });

    document.getElementById('copyBtn').addEventListener('click', function () {
        var txt = document.getElementById('dockerOut').textContent;
        var btn = document.getElementById('copyBtn');
        function done() {
            var old = btn.textContent;
            btn.textContent = 'Copied!';
            setTimeout(function () { btn.textContent = old; }, 1200);
        }
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(txt).then(done, done);
        } else { done(); }
    });
    document.getElementById('downloadBtn').addEventListener('click', function () {
        var blob = new Blob([document.getElementById('dockerOut').textContent], { type: 'text/plain;charset=utf-8' });
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'Dockerfile';
        document.body.appendChild(a);
        a.click();
        setTimeout(function () { URL.revokeObjectURL(a.href); a.remove(); }, 500);
    });

    langSel.addEventListener('change', refreshVersions);
    refreshVersions();
})();
</script>
@endsection
