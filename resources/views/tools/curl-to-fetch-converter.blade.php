@extends('layouts.app')

@section('title', 'cURL to Fetch Converter - Azlaan Tools')
@section('meta_description', 'Paste a cURL command and get the equivalent JavaScript fetch code instantly. Free developer tool.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">cURL to Fetch Converter</h1>
            <p class="lead text-muted">Paste your cURL command and instantly get the matching JavaScript <code>fetch()</code> code — including method, headers, body and auth.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="curlInput" class="form-label fw-semibold">cURL command</label>
                        <textarea class="form-control font-monospace" id="curlInput" rows="5" placeholder="curl -X POST https://api.example.com/data -H &quot;Content-Type: application/json&quot; -d '{&quot;name&quot;:&quot;Ali&quot;}'"></textarea>
                        <div class="form-text">Multi-line cURL (with backslashes) also works.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold d-block">Code style</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="style" id="styleAsync" value="async" checked>
                            <label class="form-check-label" for="styleAsync">async / await</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="style" id="styleThen" value="then">
                            <label class="form-check-label" for="styleThen">.then() chain</label>
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col-6"><button type="button" class="btn btn-primary w-100" id="goBtn">Convert</button></div>
                        <div class="col-6"><button type="button" class="btn btn-outline-secondary w-100" id="sampleBtn">Fill Sample</button></div>
                    </div>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-semibold">JavaScript fetch code</span>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="copyBtn">Copy Code</button>
                        </div>
                        <pre class="bg-light border rounded p-3" style="white-space: pre-wrap; word-break: break-word;"><code id="outCode"></code></pre>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Paste your cURL command in the box above.</li>
                <li>Choose a code style (async/await or .then chain) and press Convert.</li>
                <li>Copy the ready fetch code and use it in your project.</li>
            </ol>
            <p class="text-muted small">Supported: -X/--request, -H/--header, -d/--data (-raw, --data-binary), -u/--user (basic auth), --compressed, -k/--insecure, -A/--user-agent, -b/--cookie. The -o flag is ignored. Very complex flags (like form upload) cannot be fully converted.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var goBtn = document.getElementById('goBtn');
    var sampleBtn = document.getElementById('sampleBtn');
    var copyBtn = document.getElementById('copyBtn');
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

    // Tokenize respecting single/double quotes, then merge line continuations.
    function tokenize(cmd) {
        cmd = cmd.replace(/\\\r?\n/g, ' ');
        var tokens = [];
        var cur = '';
        var quote = null;
        for (var i = 0; i < cmd.length; i++) {
            var ch = cmd[i];
            if (quote) {
                if (ch === quote) { quote = null; }
                else { cur += ch; }
            } else if (ch === '"' || ch === "'") {
                quote = ch;
            } else if (/\s/.test(ch)) {
                if (cur) { tokens.push(cur); cur = ''; }
            } else {
                cur += ch;
            }
        }
        if (cur) { tokens.push(cur); }
        return tokens;
    }

    function parseCurl(cmd) {
        var t = tokenize(cmd.trim());
        if (!t.length || t[0].toLowerCase() !== 'curl') {
            throw new Error('Command does not start with "curl".');
        }
        var r = { method: null, url: null, headers: [], data: null, user: null };
        var i = 1;
        while (i < t.length) {
            var a = t[i];
            if ((a === '-X' || a === '--request') && i + 1 < t.length) { r.method = t[i + 1].toUpperCase(); i += 2; }
            else if ((a === '-H' || a === '--header') && i + 1 < t.length) { r.headers.push(t[i + 1]); i += 2; }
            else if ((a === '-d' || a === '--data' || a === '--data-raw' || a === '--data-binary' || a === '--data-ascii') && i + 1 < t.length) {
                var d = t[i + 1];
                if (d.charAt(0) === '$') { d = d.slice(1); }
                r.data = (r.data ? r.data + '&' : '') + d;
                i += 2;
            }
            else if ((a === '-u' || a === '--user') && i + 1 < t.length) { r.user = t[i + 1]; i += 2; }
            else if ((a === '-A' || a === '--user-agent') && i + 1 < t.length) { r.headers.push('User-Agent: ' + t[i + 1]); i += 2; }
            else if ((a === '-b' || a === '--cookie') && i + 1 < t.length) { r.headers.push('Cookie: ' + t[i + 1]); i += 2; }
            else if (a === '--compressed' || a === '-k' || a === '--insecure' || a === '-s' || a === '--silent' || a === '-v' || a === '--verbose' || a === '-i' || a === '--include') { i += 1; }
            else if (a.charAt(0) === '-' && i + 1 < t.length && t[i + 1].charAt(0) !== '-') { i += 2; }
            else if (a.charAt(0) === '-') { i += 1; }
            else if (!r.url) { r.url = a; i += 1; }
            else { i += 1; }
        }
        if (!r.url) { throw new Error('No URL found.'); }
        if (!r.method) { r.method = r.data ? 'POST' : 'GET'; }
        return r;
    }

    function jsStr(s) {
        return "'" + String(s).replace(/\\/g, '\\\\').replace(/'/g, "\\'").replace(/\n/g, '\\n') + "'";
    }

    function buildFetch(r, style) {
        var lines = [];
        var opts = [];
        if (r.method !== 'GET') { opts.push("  method: " + jsStr(r.method)); }
        var headers = r.headers.slice();
        if (r.user) {
            var parts = r.user.split(':');
            var pw = parts.slice(1).join(':');
            headers.push('Authorization: Basic ' + btoa(unescape(encodeURIComponent(parts[0] + ':' + pw))));
        }
        if (headers.length) {
            var h = headers.map(function (x) {
                var idx = x.indexOf(':');
                var k = idx === -1 ? x : x.slice(0, idx).trim();
                var v = idx === -1 ? '' : x.slice(idx + 1).trim();
                return "    " + jsStr(k) + ": " + jsStr(v);
            });
            opts.push("  headers: {\n" + h.join(',\n') + "\n  }");
        }
        if (r.data) {
            var isJson = headers.some(function (x) { return /content-type:\s*application\/json/i.test(x); });
            var body = isJson ? r.data.trim() : r.data;
            if (isJson && body.charAt(0) === String.fromCharCode(123)) {
                try {
                    body = JSON.stringify(JSON.parse(body), null, 2).split('\n').map(function (l, ix) { return ix === 0 ? l : '    ' + l; }).join('\n');
                    opts.push("  body: JSON.stringify(" + body + ")");
                } catch (e) { opts.push("  body: " + jsStr(r.data)); }
            } else {
                opts.push("  body: " + jsStr(r.data));
            }
        }
        var call = "fetch(" + jsStr(r.url);
        if (opts.length) { call += ", {\n" + opts.join(',\n') + "\n}"; }
        call += ")";

        if (style === 'then') {
            lines.push(call);
            lines.push("  .then(function (response) {");
            lines.push("    if (!response.ok) { throw new Error('HTTP ' + response.status); }");
            lines.push("    return response.json();");
            lines.push("  })");
            lines.push("  .then(function (data) {");
            lines.push("    console.log(data);");
            lines.push("  })");
            lines.push("  .catch(function (error) {");
            lines.push("    console.error('Error:', error);");
            lines.push("  });");
        } else {
            lines.push("async function main() {");
            lines.push("  try {");
            lines.push("    const response = await " + call + ";");
            lines.push("    if (!response.ok) { throw new Error('HTTP ' + response.status); }");
            lines.push("    const data = await response.json();");
            lines.push("    console.log(data);");
            lines.push("  } catch (error) {");
            lines.push("    console.error('Error:', error);");
            lines.push("  }");
            lines.push("}");
            lines.push("");
            lines.push("main();");
        }
        return lines.join('\n');
    }

    sampleBtn.addEventListener('click', function () {
        document.getElementById('curlInput').value =
            'curl -X POST https://api.example.com/users \\\n' +
            '  -H "Content-Type: application/json" \\\n' +
            '  -H "Authorization: Bearer abc123" \\\n' +
            "  -d '{\"name\": \"Ali\", \"age\": 25}'";
        hideError();
    });

    goBtn.addEventListener('click', function () {
        hideError();
        var cmd = document.getElementById('curlInput').value.trim();
        if (!cmd) { showError('Paste a cURL command first.'); return; }
        try {
            var style = document.getElementById('styleThen').checked ? 'then' : 'async';
            var r = parseCurl(cmd);
            document.getElementById('outCode').textContent = buildFetch(r, style);
            results.classList.remove('d-none');
        } catch (e) {
            showError('Could not convert: ' + e.message);
        }
    });

    copyBtn.addEventListener('click', function () {
        hideError();
        var text = document.getElementById('outCode').textContent;
        var done = function () {
            copyBtn.textContent = 'Copied!';
            setTimeout(function () { copyBtn.textContent = 'Copy Code'; }, 2000);
        };
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(done, function () { showError('Could not copy.'); });
        } else {
            var ta = document.createElement('textarea');
            ta.value = text;
            document.body.appendChild(ta);
            ta.select();
            try { document.execCommand('copy'); done(); }
            catch (e) { showError('Could not copy.'); }
            document.body.removeChild(ta);
        }
    });
})();
</script>
@endsection
