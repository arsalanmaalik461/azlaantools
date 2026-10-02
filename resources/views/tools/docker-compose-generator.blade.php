@extends('layouts.app')
@section('title', 'Docker Compose Generator — Azlaan Tools')
@section('meta_description', 'Generate a docker-compose.yml with app, database, cache and queue services. Free online Docker Compose file generator — copy or download the YAML.')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Docker Compose Generator</h1>
            <p class="lead text-muted">Create a docker-compose.yml with app, database, cache, and queue services. Fill in the form — copy or download the ready YAML.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h6">Project</h2>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="projectName" class="form-label fw-semibold">Project name</label>
                            <input type="text" class="form-control" id="projectName" placeholder="e.g. myapp" value="myapp">
                        </div>
                        <div class="col-md-6">
                            <label for="restartPol" class="form-label fw-semibold">Restart policy</label>
                            <select class="form-select" id="restartPol">
                                <option value="unless-stopped" selected>unless-stopped</option>
                                <option value="always">always</option>
                                <option value="on-failure">on-failure</option>
                                <option value="no">no</option>
                            </select>
                        </div>
                    </div>

                    <h2 class="h6">App service</h2>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label for="appName" class="form-label fw-semibold">Service name</label>
                            <input type="text" class="form-control" id="appName" value="app">
                        </div>
                        <div class="col-md-4">
                            <label for="appImage" class="form-label fw-semibold">Image</label>
                            <select class="form-select" id="appImage">
                                <option value="node:20-alpine" selected>node:20-alpine</option>
                                <option value="python:3.12-slim">python:3.12-slim</option>
                                <option value="php:8.3-apache">php:8.3-apache</option>
                                <option value="nginx:alpine">nginx:alpine</option>
                                <option value="custom">Custom...</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="appImageCustom" class="form-label fw-semibold">Custom image</label>
                            <input type="text" class="form-control" id="appImageCustom" placeholder="e.g. myrepo/myapp:latest" disabled>
                        </div>
                        <div class="col-md-6">
                            <label for="appPort" class="form-label fw-semibold">Container port</label>
                            <input type="number" class="form-control" id="appPort" value="3000" min="1" max="65535">
                        </div>
                        <div class="col-md-6">
                            <label for="hostPort" class="form-label fw-semibold">Host port</label>
                            <input type="number" class="form-control" id="hostPort" value="3000" min="1" max="65535">
                        </div>
                        <div class="col-12">
                            <label for="appEnv" class="form-label fw-semibold">Environment variables (one per line, KEY=VALUE)</label>
                            <textarea class="form-control" id="appEnv" rows="3" placeholder="NODE_ENV=production&#10;API_KEY=secret123"></textarea>
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="appVolume" checked>
                                <label class="form-check-label" for="appVolume">Create a named volume for app data</label>
                            </div>
                        </div>
                    </div>

                    <h2 class="h6">Database</h2>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="dbType" class="form-label fw-semibold">Database</label>
                            <select class="form-select" id="dbType">
                                <option value="none">None</option>
                                <option value="postgres" selected>PostgreSQL</option>
                                <option value="mysql">MySQL</option>
                                <option value="mariadb">MariaDB</option>
                                <option value="mongo">MongoDB</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="dbName" class="form-label fw-semibold">Database name</label>
                            <input type="text" class="form-control" id="dbName" value="appdb">
                        </div>
                        <div class="col-md-6">
                            <label for="dbUser" class="form-label fw-semibold">DB user</label>
                            <input type="text" class="form-control" id="dbUser" value="appuser">
                        </div>
                        <div class="col-md-6">
                            <label for="dbPass" class="form-label fw-semibold">DB password</label>
                            <input type="text" class="form-control" id="dbPass" value="changeme-strong-password">
                            <div class="form-text">Use a strong password in production.</div>
                        </div>
                    </div>

                    <h2 class="h6">Cache &amp; Queue</h2>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="cacheType" class="form-label fw-semibold">Cache</label>
                            <select class="form-select" id="cacheType">
                                <option value="none">None</option>
                                <option value="redis" selected>Redis</option>
                                <option value="memcached">Memcached</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="queueType" class="form-label fw-semibold">Queue worker</label>
                            <select class="form-select" id="queueType">
                                <option value="none" selected>None</option>
                                <option value="app">Same app image, worker command</option>
                            </select>
                        </div>
                        <div class="col-12 d-none" id="queueCmdWrap">
                            <label for="queueCmd" class="form-label fw-semibold">Worker command</label>
                            <input type="text" class="form-control" id="queueCmd" placeholder="e.g. npm run worker" value="npm run worker">
                        </div>
                    </div>

                    <button type="button" class="btn btn-primary w-100" id="goBtn">Generate Compose File</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                            <h2 class="h5 mb-0">docker-compose.yml</h2>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-outline-primary btn-sm" id="copyBtn">Copy</button>
                                <button type="button" class="btn btn-success btn-sm" id="dlBtn">Download .yml</button>
                            </div>
                        </div>
                        <pre class="bg-light border rounded p-3" style="max-height: 480px; overflow: auto;"><code id="yamlOut"></code></pre>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Fill in the project, app image, and ports.</li>
                <li>Choose database and cache (leave None if not needed).</li>
                <li>Click <strong>Generate Compose File</strong>, then copy or download.</li>
                <li>Run <code>docker compose up -d</code> on your server.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var DB_IMAGES = {
        postgres: { image: 'postgres:16-alpine', port: '5432', env: function (n, u, p) {
            return ['POSTGRES_DB=' + n, 'POSTGRES_USER=' + u, 'POSTGRES_PASSWORD=' + p]; } },
        mysql: { image: 'mysql:8.4', port: '3306', env: function (n, u, p) {
            return ['MYSQL_DATABASE=' + n, 'MYSQL_USER=' + u, 'MYSQL_PASSWORD=' + p, 'MYSQL_ROOT_PASSWORD=' + p + '-root']; } },
        mariadb: { image: 'mariadb:11', port: '3306', env: function (n, u, p) {
            return ['MYSQL_DATABASE=' + n, 'MYSQL_USER=' + u, 'MYSQL_PASSWORD=' + p, 'MYSQL_ROOT_PASSWORD=' + p + '-root']; } },
        mongo: { image: 'mongo:7', port: '27017', env: function (n, u, p) {
            return ['MONGO_INITDB_ROOT_USERNAME=' + u, 'MONGO_INITDB_ROOT_PASSWORD=' + p, 'MONGO_INITDB_DATABASE=' + n]; } }
    };
    var CACHE_IMAGES = { redis: 'redis:7-alpine', memcached: 'memcached:alpine' };

    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var yamlOut = document.getElementById('yamlOut');
    var appImage = document.getElementById('appImage');
    var appImageCustom = document.getElementById('appImageCustom');
    var queueType = document.getElementById('queueType');
    var queueCmdWrap = document.getElementById('queueCmdWrap');

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function slug(s) { return s.toLowerCase().replace(/[^a-z0-9_-]+/g, '-').replace(/^-+|-+$/g, '') || 'app'; }
    function esc(v) {
        // quote values that contain YAML-special characters
        if (/[:#\[\]{},&*!|>'"%@`]/.test(v) || /^\s|\s$/.test(v) || v === '') { return '"' + v.replace(/\\/g, '\\\\').replace(/"/g, '\\"') + '"'; }
        return v;
    }

    appImage.addEventListener('change', function () {
        appImageCustom.disabled = appImage.value !== 'custom';
        if (appImage.value !== 'custom') { appImageCustom.value = ''; }
    });
    queueType.addEventListener('change', function () {
        queueCmdWrap.classList.toggle('d-none', queueType.value !== 'app');
    });

    function parseEnv(text) {
        var out = [];
        var lines = text.split('\n');
        for (var i = 0; i < lines.length; i++) {
            var t = lines[i].trim();
            if (!t || t.charAt(0) === '#') { continue; }
            var eq = t.indexOf('=');
            if (eq > 0) { out.push(t.slice(0, eq).trim() + '=' + t.slice(eq + 1).trim()); }
        }
        return out;
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var project = slug(document.getElementById('projectName').value.trim() || 'myapp');
        var app = slug(document.getElementById('appName').value.trim() || 'app');
        var image = appImage.value === 'custom' ? document.getElementById('appImageCustom').value.trim() : appImage.value;
        if (!image) { showError('Type the custom image name or choose an image from the list.'); return; }
        var cPort = parseInt(document.getElementById('appPort').value, 10);
        var hPort = parseInt(document.getElementById('hostPort').value, 10);
        if (isNaN(cPort) || isNaN(hPort)) { showError('Enter correct port numbers.'); return; }
        var restart = document.getElementById('restartPol').value;
        var envs = parseEnv(document.getElementById('appEnv').value);
        var wantAppVol = document.getElementById('appVolume').checked;

        var dbType = document.getElementById('dbType').value;
        var dbName = document.getElementById('dbName').value.trim() || 'appdb';
        var dbUser = document.getElementById('dbUser').value.trim() || 'appuser';
        var dbPass = document.getElementById('dbPass').value.trim() || 'changeme';
        var cacheType = document.getElementById('cacheType').value;
        var wantWorker = queueType.value === 'app';
        var workerCmd = document.getElementById('queueCmd').value.trim();

        var L = [];
        L.push('name: ' + project);
        L.push('');
        L.push('services:');
        // app
        L.push('  ' + app + ':');
        L.push('    image: ' + image);
        L.push('    container_name: ' + project + '-' + app);
        L.push('    restart: ' + restart);
        L.push('    ports:');
        L.push('      - "' + hPort + ':' + cPort + '"');
        var deps = [];
        if (dbType !== 'none') { deps.push('db'); }
        if (cacheType !== 'none') { deps.push('cache'); }
        if (envs.length || deps.length) {
            if (deps.length) {
                for (var d = 0; d < deps.length; d++) {
                    var svc = deps[d] === 'db' ? dbName : 'cache';
                    envs.push(deps[d] === 'db' ? 'DATABASE_URL=' + deps[d] + '://' + dbUser + ':' + dbPass + '@db:' + DB_IMAGES[dbType].port + '/' + dbName
                                              : 'REDIS_URL=redis://cache:6379');
                }
            }
            L.push('    environment:');
            for (var e = 0; e < envs.length; e++) { L.push('      - ' + esc(envs[e])); }
            if (deps.length) {
                L.push('    depends_on:');
                for (var d2 = 0; d2 < deps.length; d2++) { L.push('      - ' + deps[d2]); }
            }
        }
        if (wantAppVol) { L.push('    volumes:'); L.push('      - ' + app + '-data:/data'); }
        L.push('    networks:');
        L.push('      - ' + project + '-net');
        // db
        if (dbType !== 'none') {
            var db = DB_IMAGES[dbType];
            L.push('  db:');
            L.push('    image: ' + db.image);
            L.push('    container_name: ' + project + '-db');
            L.push('    restart: ' + restart);
            L.push('    environment:');
            var denv = db.env(dbName, dbUser, dbPass);
            for (var de = 0; de < denv.length; de++) { L.push('      - ' + esc(denv[de])); }
            L.push('    volumes:');
            L.push('      - db-data:/var/lib/' + (dbType === 'mongo' ? 'mongodb' : dbType === 'postgres' ? 'postgresql/data' : 'mysql'));
            L.push('    networks:');
            L.push('      - ' + project + '-net');
        }
        // cache
        if (cacheType !== 'none') {
            L.push('  cache:');
            L.push('    image: ' + CACHE_IMAGES[cacheType]);
            L.push('    container_name: ' + project + '-cache');
            L.push('    restart: ' + restart);
            L.push('    networks:');
            L.push('      - ' + project + '-net');
        }
        // worker
        if (wantWorker) {
            if (!workerCmd) { showError('Type the worker command.'); return; }
            L.push('  worker:');
            L.push('    image: ' + image);
            L.push('    container_name: ' + project + '-worker');
            L.push('    restart: ' + restart);
            L.push('    command: ' + esc(workerCmd));
            if (envs.length) {
                L.push('    environment:');
                for (var we = 0; we < envs.length; we++) { L.push('      - ' + esc(envs[we])); }
            }
            if (deps.length) {
                L.push('    depends_on:');
                for (var wd = 0; wd < deps.length; wd++) { L.push('      - ' + deps[wd]); }
            }
            L.push('    networks:');
            L.push('      - ' + project + '-net');
        }
        // volumes + networks
        var vols = [];
        if (wantAppVol) { vols.push('  ' + app + '-data:'); }
        if (dbType !== 'none') { vols.push('  db-data:'); }
        if (vols.length) { L.push(''); L.push('volumes:'); for (var v = 0; v < vols.length; v++) { L.push(vols[v]); } }
        L.push('');
        L.push('networks:');
        L.push('  ' + project + '-net:');
        L.push('    driver: bridge');

        yamlOut.textContent = L.join('\n');
        results.classList.remove('d-none');
    });

    document.getElementById('copyBtn').addEventListener('click', function () {
        var t = yamlOut.textContent;
        var btn = document.getElementById('copyBtn');
        function done() { btn.textContent = 'Copied!'; setTimeout(function () { btn.textContent = 'Copy'; }, 1500); }
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(t).then(done);
        } else {
            var ta = document.createElement('textarea');
            ta.value = t; document.body.appendChild(ta); ta.select();
            try { document.execCommand('copy'); } catch (e) { /* ignore */ }
            document.body.removeChild(ta); done();
        }
    });

    document.getElementById('dlBtn').addEventListener('click', function () {
        var blob = new Blob([yamlOut.textContent], { type: 'text/yaml' });
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'docker-compose.yml';
        document.body.appendChild(a);
        a.click();
        setTimeout(function () { URL.revokeObjectURL(a.href); a.remove(); }, 500);
    });
})();
</script>
@endsection
