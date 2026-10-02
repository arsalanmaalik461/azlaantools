@extends('layouts.app')

@section('title', 'JWT Decoder Online Free - Decode JSON Web Tokens | Azlaan Tools')
@section('meta_description', 'Free JWT decoder: decode header and payload, see issued and expiry times in local and UTC time and check if a token is expired. Decode only, runs 100% in your browser.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">JWT Decoder</h1>
            <p class="lead text-muted">Paste a JSON Web Token to decode its header and payload instantly. This tool runs 100% in your browser — nothing is uploaded or saved.</p>
            <div class="alert alert-warning"><strong>Important:</strong> This tool only <strong>decodes</strong> the token. Signatures are <strong>NOT verified</strong> here, so never trust a token based on this page alone — always verify it on your server.</div>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <label class="form-label fw-semibold" for="jwtInput">JWT Token</label>
                    <textarea id="jwtInput" class="form-control font-monospace" rows="5" placeholder="Paste eyJhbGciOi... token here"></textarea>
                    <div class="d-flex gap-2 mt-3 flex-wrap">
                        <button type="button" class="btn btn-primary" id="decodeBtn">Decode</button>
                        <button type="button" class="btn btn-outline-secondary" id="sampleBtn">Load Sample</button>
                        <button type="button" class="btn btn-outline-danger" id="clearBtn">Clear</button>
                    </div>
                    <div id="jwtError" class="alert alert-danger mt-3 d-none"></div>
                    <div id="jwtResult" class="d-none mt-3">
                        <div class="mb-3"><span class="fw-semibold me-2">Status:</span><span id="expBadge" class="badge bg-secondary">No expiry claim</span></div>
                        <label class="form-label fw-semibold">Header</label>
                        <pre id="headerOut" class="bg-light border rounded p-3 font-monospace small"></pre>
                        <button type="button" class="btn btn-success btn-sm mb-3" data-copy="headerOut">Copy Header JSON</button>
                        <label class="form-label fw-semibold d-block">Payload</label>
                        <pre id="payloadOut" class="bg-light border rounded p-3 font-monospace small"></pre>
                        <button type="button" class="btn btn-success btn-sm mb-3" data-copy="payloadOut">Copy Payload JSON</button>
                        <div id="timesBox" class="alert alert-light border small"></div>
                        <label class="form-label fw-semibold d-block">Signature (raw, not verified)</label>
                        <div id="sigOut" class="border rounded p-2 font-monospace small text-break"></div>
                    </div>
                </div>
            </div>
            <h2>How to use</h2>
            <ol>
                <li>Paste your JWT into the box and click <strong>Decode</strong> (decoding also happens as you type).</li>
                <li>Read the header and payload as pretty-printed JSON, and copy either one with its copy button.</li>
                <li>Check the status badge and times box for issued-at (iat) and expiry (exp) in your local time and UTC.</li>
                <li>Remember: the signature is shown for reference only and is not verified by this tool.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    var input = document.getElementById('jwtInput');
    var errEl = document.getElementById('jwtError');
    var resultEl = document.getElementById('jwtResult');
    function b64urlDecode(str) {
        var s = str.replace(/-/g, '+').replace(/_/g, '/');
        while (s.length % 4) s += '=';
        var bin = atob(s);
        var bytes = new Uint8Array(bin.length);
        for (var i = 0; i < bin.length; i++) bytes[i] = bin.charCodeAt(i);
        return new TextDecoder('utf-8').decode(bytes);
    }
    function fmtTime(sec) {
        var d = new Date(sec * 1000);
        return 'Local: ' + d.toLocaleString() + ' | UTC: ' + d.toUTCString();
    }
    function relative(sec) {
        var diff = sec - Math.floor(Date.now() / 1000); var abs = Math.abs(diff);
        var unit = 'seconds'; var val = abs;
        if (abs >= 86400) { val = Math.floor(abs / 86400); unit = 'days'; } else if (abs >= 3600) { val = Math.floor(abs / 3600); unit = 'hours'; } else if (abs >= 60) { val = Math.floor(abs / 60); unit = 'minutes'; }
        return diff >= 0 ? 'in ' + val + ' ' + unit : val + ' ' + unit + ' ago';
    }
    function decode() {
        errEl.classList.add('d-none'); resultEl.classList.add('d-none');
        var token = input.value.trim(); if (!token) return;
        var parts = token.split('.');
        if (parts.length !== 3) { errEl.textContent = 'Invalid JWT: expected 3 parts separated by dots, found ' + parts.length + '.'; errEl.classList.remove('d-none'); return; }
        try {
            var header = JSON.parse(b64urlDecode(parts[0]));
            var payload = JSON.parse(b64urlDecode(parts[1]));
            document.getElementById('headerOut').textContent = JSON.stringify(header, null, 2);
            document.getElementById('payloadOut').textContent = JSON.stringify(payload, null, 2);
            document.getElementById('sigOut').textContent = parts[2] || '(empty)';
            var badge = document.getElementById('expBadge'); var lines = [];
            if (payload.iat) lines.push('Issued at (iat): ' + fmtTime(payload.iat));
            if (payload.nbf) lines.push('Not before (nbf): ' + fmtTime(payload.nbf));
            if (payload.exp) {
                lines.push('Expires at (exp): ' + fmtTime(payload.exp) + ' (' + relative(payload.exp) + ')');
                var expired = payload.exp * 1000 < Date.now();
                badge.textContent = expired ? 'EXPIRED' : 'Not expired — expires ' + relative(payload.exp);
                badge.className = 'badge ' + (expired ? 'bg-danger' : 'bg-success');
            } else { badge.textContent = 'No expiry claim (exp)'; badge.className = 'badge bg-secondary'; }
            document.getElementById('timesBox').textContent = lines.length ? lines.join('\n') : 'No iat / exp / nbf time claims found in this token.';
            document.getElementById('timesBox').style.whiteSpace = 'pre-line';
            resultEl.classList.remove('d-none');
        } catch (e) { errEl.textContent = 'Could not decode this token: ' + e.message; errEl.classList.remove('d-none'); }
    }
    document.getElementById('decodeBtn').addEventListener('click', decode);
    input.addEventListener('input', decode);
    document.getElementById('clearBtn').addEventListener('click', function () { input.value = ''; resultEl.classList.add('d-none'); errEl.classList.add('d-none'); });
    document.getElementById('sampleBtn').addEventListener('click', function () {
        function enc(obj) { return btoa(JSON.stringify(obj)).replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/, ''); }
        var now = Math.floor(Date.now() / 1000);
        input.value = enc({ alg: 'HS256', typ: 'JWT' }) + '.' + enc({ sub: '1234567890', name: 'Azlaan User', iat: now, exp: now + 3600 }) + '.sample-signature-not-real';
        decode();
    });
    document.querySelectorAll('[data-copy]').forEach(function (btn) { btn.addEventListener('click', function () { var el = document.getElementById(btn.getAttribute('data-copy')); if (navigator.clipboard) navigator.clipboard.writeText(el.textContent); }); });
})();
</script>
@endsection
