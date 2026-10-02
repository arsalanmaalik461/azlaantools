@extends('layouts.app')

@section('title', 'DMARC Record Generator - Azlaan Tools')
@section('meta_description', 'Generate a DMARC DNS record with policy and reporting options. Free online DMARC record generator.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">DMARC Record Generator</h1>
            <p class="lead text-muted">Create a DMARC DNS record for your domain — with policy, subdomain policy, and aggregate/forensic reporting options.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="domainName" class="form-label fw-semibold">Domain (without _dmarc prefix)</label>
                        <input type="text" class="form-control" id="domainName" placeholder="e.g. example.com">
                        <div class="form-text">The record hostname will be: <code>_dmarc.example.com</code> (TXT type).</div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="policy" class="form-label fw-semibold">Policy (p)</label>
                            <select class="form-select" id="policy">
                                <option value="none">none — monitor only</option>
                                <option value="quarantine">quarantine — spam folder</option>
                                <option value="reject">reject — block outright</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="subPolicy" class="form-label fw-semibold">Subdomain Policy (sp)</label>
                            <select class="form-select" id="subPolicy">
                                <option value="">same as p (default)</option>
                                <option value="none">none</option>
                                <option value="quarantine">quarantine</option>
                                <option value="reject">reject</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="pct" class="form-label fw-semibold">Percentage (pct)</label>
                            <input type="number" class="form-control" id="pct" value="100" min="1" max="100">
                            <div class="form-text">What % of mail the policy applies to — start with 25 for rollout.</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="reportInterval" class="form-label fw-semibold">Report Interval (ri, seconds)</label>
                            <input type="number" class="form-control" id="reportInterval" value="86400" min="3600" step="3600">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="rua" class="form-label fw-semibold">Aggregate Reports (rua) — comma separated emails</label>
                        <input type="text" class="form-control" id="rua" placeholder="e.g. dmarc-reports@example.com">
                    </div>
                    <div class="mb-3">
                        <label for="ruf" class="form-label fw-semibold">Forensic Reports (ruf) — comma separated emails</label>
                        <input type="text" class="form-control" id="ruf" placeholder="e.g. dmarc-fail@example.com">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="dkimAlign" class="form-label fw-semibold">DKIM Alignment (adkim)</label>
                            <select class="form-select" id="dkimAlign">
                                <option value="">relaxed (default)</option>
                                <option value="s">strict</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="spfAlign" class="form-label fw-semibold">SPF Alignment (aspf)</label>
                            <select class="form-select" id="spfAlign">
                                <option value="">relaxed (default)</option>
                                <option value="s">strict</option>
                            </select>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Generate DMARC Record</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <label for="recordOut" class="form-label fw-semibold">Host: <code id="hostOut"></code> &nbsp; Type: TXT</label>
                        <textarea class="form-control font-monospace" id="recordOut" rows="4" readonly></textarea>
                        <button type="button" class="btn btn-success w-100 mt-3" id="copyBtn">Copy Record</button>
                        <div class="alert alert-success mt-3 d-none" id="copiedMsg" role="alert">Copied to clipboard.</div>
                        <div class="alert alert-warning mt-3" id="warnBox" role="alert"></div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Type your domain and select a policy (first time choose <strong>none</strong>, then enforce after checking reports).</li>
                <li>Enter the report emails so you receive the aggregate reports.</li>
                <li>Click Generate, copy the record, and create a TXT record at the <code>_dmarc</code> host in your DNS provider.</li>
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
    var recordOut = document.getElementById('recordOut');
    var hostOut = document.getElementById('hostOut');
    var copiedMsg = document.getElementById('copiedMsg');
    var warnBox = document.getElementById('warnBox');

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function validDomain(d) {
        return /^(?!-)[a-z0-9-]{1,63}(?<!-)(\.[a-z0-9-]{1,63})*(\.[a-z]{2,})$/i.test(d);
    }
    function mailtoList(raw) {
        var out = [];
        raw.split(',').forEach(function (e) {
            e = e.trim();
            if (e && /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(e)) {
                out.push('mailto:' + e);
            }
        });
        return out.join(',');
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var domain = document.getElementById('domainName').value.trim().toLowerCase().replace(/^_dmarc\./, '');
        var policy = document.getElementById('policy').value;
        var subPolicy = document.getElementById('subPolicy').value;
        var pct = parseInt(document.getElementById('pct').value, 10);
        var ri = parseInt(document.getElementById('reportInterval').value, 10);
        var ruaRaw = document.getElementById('rua').value.trim();
        var rufRaw = document.getElementById('ruf').value.trim();
        var adkim = document.getElementById('dkimAlign').value;
        var aspf = document.getElementById('spfAlign').value;

        if (!validDomain(domain)) { showError('Please enter a valid domain, e.g. example.com.'); return; }
        if (!(pct >= 1 && pct <= 100)) { showError('Percentage must be between 1 and 100.'); return; }
        if (!(ri >= 3600)) { showError('Report interval must be at least 3600 seconds.'); return; }

        var parts = ['v=DMARC1', 'p=' + policy];
        if (subPolicy) parts.push('sp=' + subPolicy);
        if (pct !== 100) parts.push('pct=' + pct);
        var rua = mailtoList(ruaRaw);
        var ruf = mailtoList(rufRaw);
        if (rua) parts.push('rua=' + rua);
        if (ruf) parts.push('ruf=' + ruf);
        if (adkim) parts.push('adkim=' + adkim);
        if (aspf) parts.push('aspf=' + aspf);
        if (ri !== 86400) parts.push('ri=' + ri);

        var record = parts.join('; ') + ';';
        hostOut.textContent = '_dmarc.' + domain;
        recordOut.value = record;

        var warns = [];
        if (policy === 'none') warns.push('Policy "none" is for monitoring only — after checking reports, move to "quarantine" then "reject".');
        if (!rua) warns.push('No rua email given — you will not get aggregate reports. Be sure to add a report email.');
        warnBox.innerHTML = '';
        if (warns.length) {
            warns.forEach(function (w) {
                var div = document.createElement('div');
                div.textContent = w;
                warnBox.appendChild(div);
            });
            warnBox.classList.remove('d-none');
        } else {
            warnBox.classList.add('d-none');
        }
        results.classList.remove('d-none');
    });

    document.getElementById('copyBtn').addEventListener('click', function () {
        recordOut.select();
        recordOut.setSelectionRange(0, recordOut.value.length);
        function done() {
            copiedMsg.classList.remove('d-none');
            setTimeout(function () { copiedMsg.classList.add('d-none'); }, 2000);
        }
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(recordOut.value).then(done, function () {
                document.execCommand('copy');
                done();
            });
        } else {
            document.execCommand('copy');
            done();
        }
    });
})();
</script>
@endsection
