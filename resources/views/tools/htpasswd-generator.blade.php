@extends('layouts.app')

@section('title', 'Htpasswd Generator - Azlaan Tools')
@section('meta_description', 'Generate Apache htpasswd entries online for free — for password protected directories.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Htpasswd Generator</h1>
            <p class="lead text-muted">Create entries for an Apache <code>.htpasswd</code> file — to protect a directory with a password. The hash is made in your browser only; your password is never sent anywhere.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="userInput" class="form-label fw-semibold">Username</label>
                            <input type="text" class="form-control" id="userInput" placeholder="e.g. admin">
                        </div>
                        <div class="col-md-6">
                            <label for="passInput" class="form-label fw-semibold">Password</label>
                            <input type="text" class="form-control" id="passInput" placeholder="strong password">
                        </div>
                    </div>
                    <div class="mb-3 mt-3">
                        <label for="algoSelect" class="form-label fw-semibold">Hash algorithm</label>
                        <select class="form-select" id="algoSelect">
                            <option value="apr1">$apr1$ — Apache MD5 (recommended, most compatible)</option>
                            <option value="sha">{SHA} — SHA-1 (old, simple)</option>
                        </select>
                        <div class="form-text">Apache 2.4 supports both. $apr1$ is more secure.</div>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-primary flex-fill" id="goBtn">Generate Entry</button>
                        <button type="button" class="btn btn-outline-secondary" id="randBtn" title="Random password">Random</button>
                    </div>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <label class="form-label fw-semibold">Generated entry</label>
                        <div class="input-group mb-2">
                            <input type="text" class="form-control font-monospace" id="entryOutput" readonly>
                            <button type="button" class="btn btn-outline-primary" id="copyBtn">Copy</button>
                        </div>
                        <button type="button" class="btn btn-success w-100" id="downloadBtn">Download .htpasswd File</button>
                        <div class="form-text mt-2">Each entry is added on a new line — you can build a file with many users.</div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Type a username and password (or press Random).</li>
                <li>Choose the hash algorithm and press "Generate Entry".</li>
                <li>Copy the entry or download the full <code>.htpasswd</code> file.</li>
                <li>Use it in Apache with <code>AuthUserFile</code> and <code>Require valid-user</code>.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var userInput = document.getElementById('userInput');
    var passInput = document.getElementById('passInput');
    var algoSelect = document.getElementById('algoSelect');
    var goBtn = document.getElementById('goBtn');
    var randBtn = document.getElementById('randBtn');
    var copyBtn = document.getElementById('copyBtn');
    var downloadBtn = document.getElementById('downloadBtn');
    var entryOutput = document.getElementById('entryOutput');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var entries = [];

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }

    // ---- Compact MD5 (for $apr1$) ----
    function md5cycle(x, k) {
        var a = x[0], b = x[1], c = x[2], d = x[3];
        function ff(a, b, c, d, x, s, t) { a = (a + ((b & c) | (~b & d)) + x + t) | 0; return ((a << s) | (a >>> (32 - s))) + b | 0; }
        function gg(a, b, c, d, x, s, t) { a = (a + ((b & d) | (c & ~d)) + x + t) | 0; return ((a << s) | (a >>> (32 - s))) + b | 0; }
        function hh(a, b, c, d, x, s, t) { a = (a + (b ^ c ^ d) + x + t) | 0; return ((a << s) | (a >>> (32 - s))) + b | 0; }
        function ii(a, b, c, d, x, s, t) { a = (a + (c ^ (b | ~d)) + x + t) | 0; return ((a << s) | (a >>> (32 - s))) + b | 0; }
        a = ff(a, b, c, d, k[0], 7, -680876936); d = ff(d, a, b, c, k[1], 12, -389564586);
        c = ff(c, d, a, b, k[2], 17, 606105819); b = ff(b, c, d, a, k[3], 22, -1044525330);
        a = ff(a, b, c, d, k[4], 7, -176418897); d = ff(d, a, b, c, k[5], 12, 1200080426);
        c = ff(c, d, a, b, k[6], 17, -1473231341); b = ff(b, c, d, a, k[7], 22, -45705983);
        a = ff(a, b, c, d, k[8], 7, 1770035416); d = ff(d, a, b, c, k[9], 12, -1958414417);
        c = ff(c, d, a, b, k[10], 17, -42063); b = ff(b, c, d, a, k[11], 22, -1990404162);
        a = ff(a, b, c, d, k[12], 7, 1804603682); d = ff(d, a, b, c, k[13], 12, -40341101);
        c = ff(c, d, a, b, k[14], 17, -1502002290); b = ff(b, c, d, a, k[15], 22, 1236535329);
        a = gg(a, b, c, d, k[1], 5, -165796510); d = gg(d, a, b, c, k[6], 9, -1069501632);
        c = gg(c, d, a, b, k[11], 14, 643717713); b = gg(b, c, d, a, k[0], 20, -373897302);
        a = gg(a, b, c, d, k[5], 5, -701558691); d = gg(d, a, b, c, k[10], 9, 38016083);
        c = gg(c, d, a, b, k[15], 14, -660478335); b = gg(b, c, d, a, k[4], 20, -405537848);
        a = gg(a, b, c, d, k[9], 5, 568446438); d = gg(d, a, b, c, k[14], 9, -1019803690);
        c = gg(c, d, a, b, k[3], 14, -187363961); b = gg(b, c, d, a, k[8], 20, 1163531501);
        a = gg(a, b, c, d, k[13], 5, -1444681467); d = gg(d, a, b, c, k[2], 9, -51403784);
        c = gg(c, d, a, b, k[7], 14, 1735328473); b = gg(b, c, d, a, k[12], 20, -1926607734);
        a = hh(a, b, c, d, k[5], 4, -378558); d = hh(d, a, b, c, k[8], 11, -2022574463);
        c = hh(c, d, a, b, k[11], 16, 1839030562); b = hh(b, c, d, a, k[14], 23, -35309556);
        a = hh(a, b, c, d, k[1], 4, -1530992060); d = hh(d, a, b, c, k[4], 11, 1272893353);
        c = hh(c, d, a, b, k[7], 16, -155497632); b = hh(b, c, d, a, k[10], 23, -1094730640);
        a = hh(a, b, c, d, k[13], 4, 681279174); d = hh(d, a, b, c, k[0], 11, -358537222);
        c = hh(c, d, a, b, k[3], 16, -722521979); b = hh(b, c, d, a, k[6], 23, 76029189);
        a = hh(a, b, c, d, k[9], 4, -640364487); d = hh(d, a, b, c, k[12], 11, -421815835);
        c = hh(c, d, a, b, k[15], 16, 530742520); b = hh(b, c, d, a, k[2], 23, -995338651);
        a = ii(a, b, c, d, k[0], 6, -198630844); d = ii(d, a, b, c, k[7], 10, 1126891415);
        c = ii(c, d, a, b, k[14], 15, -1416354905); b = ii(b, c, d, a, k[5], 21, -57434055);
        a = ii(a, b, c, d, k[12], 6, 1700485571); d = ii(d, a, b, c, k[3], 10, -1894986606);
        c = ii(c, d, a, b, k[10], 15, -1051523); b = ii(b, c, d, a, k[1], 21, -2054922799);
        a = ii(a, b, c, d, k[8], 6, 1873313359); d = ii(d, a, b, c, k[15], 10, -30611744);
        c = ii(c, d, a, b, k[6], 15, -1560198380); b = ii(b, c, d, a, k[13], 21, 1309151649);
        a = ii(a, b, c, d, k[4], 6, -145523070); d = ii(d, a, b, c, k[11], 10, -1120210379);
        c = ii(c, d, a, b, k[2], 15, 718787259); b = ii(b, c, d, a, k[9], 21, -343485551);
        x[0] = (a + x[0]) | 0; x[1] = (b + x[1]) | 0; x[2] = (c + x[2]) | 0; x[3] = (d + x[3]) | 0;
    }
    function md5blk(s) {
        var md5blks = [], i;
        for (i = 0; i < 64; i += 4) {
            md5blks[i >> 2] = s.charCodeAt(i) + (s.charCodeAt(i + 1) << 8) + (s.charCodeAt(i + 2) << 16) + (s.charCodeAt(i + 3) << 24);
        }
        return md5blks;
    }
    function md51(s) {
        var n = s.length, state = [1732584193, -271733879, -1732584194, 271733878], i, tail, tmp, lo, hi;
        for (i = 64; i <= n; i += 64) md5cycle(state, md5blk(s.substring(i - 64, i)));
        s = s.substring(i - 64);
        tail = new Array(16).fill(0);
        for (i = 0; i < s.length; i++) tail[i >> 2] |= s.charCodeAt(i) << ((i % 4) << 3);
        tail[i >> 2] |= 0x80 << ((i % 4) << 3);
        if (i > 55) { md5cycle(state, tail); tail = new Array(16).fill(0); }
        tmp = n * 8; lo = tmp & 0xffffffff; hi = Math.floor(tmp / 0x100000000);
        tail[14] = lo; tail[15] = hi;
        md5cycle(state, tail);
        return state;
    }
    function md5bin(s) {
        var st = md51(s), out = '';
        for (var i = 0; i < 4; i++) for (var j = 0; j < 4; j++) out += String.fromCharCode((st[i] >>> (j * 8)) & 0xff);
        return out;
    }
    var ITOA64 = './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    function to64(v, n) {
        var r = '';
        while (--n >= 0) { r += ITOA64.charAt(v & 0x3f); v >>>= 6; }
        return r;
    }
    function apr1(password, salt) {
        var magic = '$apr1$';
        var text = password + magic + salt;
        var bin = md5bin(password + salt + password);
        var i, pl = password.length;
        for (i = pl; i > 0; i -= 16) text += bin.substring(0, Math.min(16, i));
        for (i = pl; i > 0; i >>= 1) text += (i & 1) ? String.fromCharCode(0) : password.charAt(0);
        bin = md5bin(text);
        for (i = 0; i < 1000; i++) {
            var t = '';
            if (i & 1) t += password; else t += bin;
            if (i % 3) t += salt;
            if (i % 7) t += password;
            if (i & 1) t += bin; else t += password;
            bin = md5bin(t);
        }
        var out = '';
        out += to64((bin.charCodeAt(0) << 16) | (bin.charCodeAt(6) << 8) | bin.charCodeAt(12), 4);
        out += to64((bin.charCodeAt(1) << 16) | (bin.charCodeAt(7) << 8) | bin.charCodeAt(13), 4);
        out += to64((bin.charCodeAt(2) << 16) | (bin.charCodeAt(8) << 8) | bin.charCodeAt(14), 4);
        out += to64((bin.charCodeAt(3) << 16) | (bin.charCodeAt(9) << 8) | bin.charCodeAt(15), 4);
        out += to64((bin.charCodeAt(4) << 16) | (bin.charCodeAt(10) << 8) | bin.charCodeAt(5), 4);
        out += to64(bin.charCodeAt(11), 2);
        return magic + salt + '$' + out;
    }
    function randomSalt() {
        var chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789./';
        var s = '', arr = new Uint32Array(8);
        (window.crypto || {}).getRandomValues ? window.crypto.getRandomValues(arr) : null;
        for (var i = 0; i < 8; i++) s += chars.charAt((arr[i] || Math.floor(Math.random() * 4294967296)) % 64);
        return s;
    }
    function randomPassword() {
        var chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789!@#$%^&*';
        var s = '', arr = new Uint32Array(16);
        if (window.crypto && window.crypto.getRandomValues) window.crypto.getRandomValues(arr);
        for (var i = 0; i < 16; i++) s += chars.charAt((arr[i] || Math.floor(Math.random() * 4294967296)) % chars.length);
        return s;
    }
    function sha1b64(password) {
        var enc = new TextEncoder();
        return window.crypto.subtle.digest('SHA-1', enc.encode(password)).then(function (buf) {
            var bytes = new Uint8Array(buf), bin = '';
            for (var i = 0; i < bytes.length; i++) bin += String.fromCharCode(bytes[i]);
            return '{SHA}' + btoa(bin);
        });
    }

    randBtn.addEventListener('click', function () {
        passInput.value = randomPassword();
        hideError();
    });

    goBtn.addEventListener('click', function () {
        hideError();
        var user = userInput.value.trim();
        var pass = passInput.value;
        if (!user) { showError('Please enter a username.'); return; }
        if (/[:\s]/.test(user)) { showError('The username cannot have a space or colon (:).'); return; }
        if (!pass) { showError('Please enter a password or press Random.'); return; }
        var algo = algoSelect.value;
        function done(hash) {
            var entry = user + ':' + hash;
            entries.push(entry);
            entryOutput.value = entry;
            results.classList.remove('d-none');
        }
        if (algo === 'sha') {
            if (!window.crypto || !window.crypto.subtle) { showError('Your browser does not support WebCrypto — please choose $apr1$.'); return; }
            sha1b64(pass).then(done).catch(function () { showError('There was an error while making the hash.'); });
        } else {
            done(apr1(pass, randomSalt()));
        }
    });

    copyBtn.addEventListener('click', function () {
        entryOutput.select();
        entryOutput.setSelectionRange(0, entryOutput.value.length);
        function ok() { copyBtn.textContent = 'Copied!'; setTimeout(function () { copyBtn.textContent = 'Copy'; }, 1500); }
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(entryOutput.value).then(ok).catch(function () { document.execCommand('copy'); ok(); });
        } else { document.execCommand('copy'); ok(); }
    });

    downloadBtn.addEventListener('click', function () {
        if (!entries.length) { showError('Please generate an entry first.'); return; }
        var blob = new Blob([entries.join('\n') + '\n'], { type: 'text/plain' });
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = '.htpasswd';
        document.body.appendChild(a);
        a.click();
        setTimeout(function () { URL.revokeObjectURL(a.href); a.remove(); }, 2000);
    });
})();
</script>
@endsection
