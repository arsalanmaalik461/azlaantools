@extends('layouts.app')

@section('title', 'Gitignore Generator - Azlaan Tools')
@section('meta_description', 'Make a .gitignore file from templates for Node, Python, Java, PHP and more languages. Free online.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Gitignore Generator</h1>
            <p class="lead text-muted">Select your project languages/OS — your ready <code>.gitignore</code> file will be created. Copy or download it.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h6>Languages / Frameworks</h6>
                    <div class="row" id="langChecks"></div>
                    <h6 class="mt-3">Operating Systems / Editors</h6>
                    <div class="row" id="osChecks"></div>

                    <button type="button" class="btn btn-primary w-100 mt-3" id="goBtn">Generate Gitignore</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <strong>.gitignore preview</strong>
                            <span class="small text-muted" id="lineCount"></span>
                        </div>
                        <pre class="bg-light border rounded p-3 small" id="preview" style="max-height: 400px; overflow:auto; white-space: pre-wrap;"></pre>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-primary" id="copyBtn">Copy</button>
                            <button type="button" class="btn btn-success" id="dlBtn">.gitignore Download</button>
                        </div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Tick the languages your project uses (e.g. Node + Python).</li>
                <li>Also select the OS / editor (Windows, macOS, VS Code).</li>
                <li>Press "Generate Gitignore", then copy or download it and save it as <code>.gitignore</code> in your project root.</li>
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

    var T = {
        node: '# Node\nnode_modules/\nnpm-debug.log*\nyarn-debug.log*\nyarn-error.log*\n.npm\n.yarn/\ndist/\nbuild/\n.next/\n.nuxt/\n.env\n.env.local\ncoverage/\n*.tgz',
        python: '# Python\n__pycache__/\n*.py[cod]\n*$py.class\n.venv/\nvenv/\nenv/\n*.egg-info/\ndist/\nbuild/\n.eggs/\n.pytest_cache/\n.mypy_cache/\n.env\n*.log',
        java: '# Java\n*.class\n*.jar\n*.war\n*.ear\ntarget/\nbuild/\n.gradle/\n.idea/\n*.iml\nout/',
        php: '# PHP\nvendor/\n*.log\n.env\n.env.backup\n.phpunit.result.cache\ncomposer.phar',
        laravel: '# Laravel\n/storage/*.key\n/public/storage\n/public/hot\n.env\n.env.backup\nHomestead.json\nHomestead.yaml\nnpm-debug.log\nyarn-error.log',
        django: '# Django\n*.log\n*.pot\nlocal_settings.py\ndb.sqlite3\ndb.sqlite3-journal\n/staticfiles/\nmedia/',
        go: '# Go\n*.exe\n*.exe~\n*.dll\n*.so\n*.dylib\n*.test\n*.out\n/bin/\n/pkg/',
        rust: '# Rust\n/target/\n**/*.rs.bk\nCargo.lock',
        dotnet: '# .NET\nbin/\nobj/\n*.user\n*.suo\n.vs/\npackages/\nTestResults/',
        react: '# React (Vite/CRA)\n/build/\n/dist/\n/.pnp\n.pnp.js\n/.web_modules/\n/.parcel-cache/',
        android: '# Android\n*.apk\n*.apks\n*.aab\n/local.properties\n/.gradle/\n/build/\n/captures\n.externalNativeBuild',
        flutter: '# Flutter\n.dart_tool/\n.flutter-plugins\n.flutter-plugins-dependencies\n.packages\n/build/\n*.jks\n*.keystore',
        windows: '# Windows\nThumbs.db\nehthumbs.db\nDesktop.ini\n$RECYCLE.BIN/',
        macos: '# macOS\n.DS_Store\n.AppleDouble\n.LSOverride\n._*',
        linux: '# Linux\n*~\n.fuse_hidden*\n.Trash-*',
        vscode: '# VS Code\n.vscode/*\n!.vscode/settings.json\n!.vscode/tasks.json\n!.vscode/launch.json\n!.vscode/extensions.json',
        jetbrains: '# JetBrains\n.idea/\n*.iml\n*.iws'
    };

    var langDefs = [
        ['node', 'Node.js'], ['python', 'Python'], ['java', 'Java'], ['php', 'PHP'],
        ['laravel', 'Laravel'], ['django', 'Django'], ['go', 'Go'], ['rust', 'Rust'],
        ['dotnet', '.NET'], ['react', 'React'], ['android', 'Android'], ['flutter', 'Flutter']
    ];
    var osDefs = [
        ['windows', 'Windows'], ['macos', 'macOS'], ['linux', 'Linux'],
        ['vscode', 'VS Code'], ['jetbrains', 'JetBrains']
    ];

    function buildChecks(containerId, defs, prefix) {
        var c = document.getElementById(containerId);
        defs.forEach(function (d) {
            var col = document.createElement('div');
            col.className = 'col-6 col-md-4';
            var wrap = document.createElement('div');
            wrap.className = 'form-check';
            var cb = document.createElement('input');
            cb.type = 'checkbox'; cb.className = 'form-check-input';
            cb.id = prefix + d[0]; cb.value = d[0];
            var lb = document.createElement('label');
            lb.className = 'form-check-label'; lb.htmlFor = prefix + d[0];
            lb.textContent = d[1];
            wrap.appendChild(cb); wrap.appendChild(lb); col.appendChild(wrap);
            c.appendChild(col);
        });
    }
    buildChecks('langChecks', langDefs, 't');
    buildChecks('osChecks', osDefs, 't');

    function showError(msg) { errorBox.textContent = msg; errorBox.classList.remove('d-none'); results.classList.add('d-none'); }
    function hideError() { errorBox.classList.add('d-none'); errorBox.textContent = ''; }

    goBtn.addEventListener('click', function () {
        hideError();
        var picked = [];
        document.querySelectorAll('#langChecks input:checked, #osChecks input:checked').forEach(function (cb) {
            picked.push(cb.value);
        });
        if (!picked.length) { showError('Select at least one template.'); return; }
        var seen = {};
        var lines = [];
        picked.forEach(function (k) {
            lines.push('');
            T[k].split('\n').forEach(function (ln) {
                if (ln === '') return;
                if (seen[ln]) return;
                seen[ln] = true;
                lines.push(ln);
            });
        });
        var out = '# Generated by Azlaan Tools - Gitignore Generator\n' + lines.join('\n').replace(/^\n/, '') + '\n';
        document.getElementById('preview').textContent = out;
        document.getElementById('lineCount').textContent = out.split('\n').length + ' lines';
        results.classList.remove('d-none');
        results.scrollIntoView({ behavior: 'smooth', block: 'center' });
    });

    document.getElementById('copyBtn').addEventListener('click', function () {
        var txt = document.getElementById('preview').textContent;
        if (navigator.clipboard) {
            navigator.clipboard.writeText(txt).then(function () {
                document.getElementById('copyBtn').textContent = 'Copied!';
                setTimeout(function () { document.getElementById('copyBtn').textContent = 'Copy'; }, 1500);
            });
        }
    });

    document.getElementById('dlBtn').addEventListener('click', function () {
        var blob = new Blob([document.getElementById('preview').textContent], { type: 'text/plain' });
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = '.gitignore';
        document.body.appendChild(a); a.click(); a.remove();
    });
})();
</script>
@endsection
