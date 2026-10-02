@extends('layouts.app')

@section('title', 'Social Share Link Generator - Azlaan Tools')
@section('meta_description', 'Generate Facebook, X, LinkedIn, WhatsApp and Telegram share links for any page in one click. Free online tool.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Social Share Link Generator</h1>
            <p class="lead text-muted">Generate share links for Facebook, X, LinkedIn, WhatsApp and Telegram for any page in one click. Copy the link and add it to your website.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="pageUrl" class="form-label fw-semibold">Page URL</label>
                        <input type="url" class="form-control" id="pageUrl" placeholder="https://example.com/article">
                    </div>
                    <div class="mb-3">
                        <label for="shareText" class="form-label fw-semibold">Share text / message</label>
                        <input type="text" class="form-control" id="shareText" placeholder="You must read this article!">
                        <div class="form-text">This text will go with WhatsApp, X and Telegram.</div>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Generate Share Links</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <h5>Generated Share Links</h5>
                        <div id="linksList"></div>
                        <h6 class="mt-4">HTML Buttons Snippet</h6>
                        <p class="text-muted small">Paste this code on your website to get ready share buttons:</p>
                        <pre class="bg-light border rounded p-3 small" id="htmlSnippet" style="white-space:pre-wrap;word-break:break-all;"></pre>
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="copyHtmlBtn">Copy HTML</button>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Type the full URL of the page you want to share.</li>
                <li>Type the text that goes with the share.</li>
                <li>Press <strong>Generate Share Links</strong>, copy each network's link or open it to test.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var pageUrl = document.getElementById('pageUrl');
    var shareText = document.getElementById('shareText');
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var linksList = document.getElementById('linksList');
    var htmlSnippet = document.getElementById('htmlSnippet');
    var copyHtmlBtn = document.getElementById('copyHtmlBtn');

    function validUrl(u) {
        try {
            var x = new URL(u);
            return x.protocol === 'http:' || x.protocol === 'https:';
        } catch (e) { return false; }
    }

    function copyText(t, btn) {
        function done() {
            var old = btn.textContent;
            btn.textContent = 'Copied!';
            setTimeout(function () { btn.textContent = old; }, 1200);
        }
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(t).then(done, function () { fallbackCopy(t); done(); });
        } else { fallbackCopy(t); done(); }
    }
    function fallbackCopy(t) {
        var ta = document.createElement('textarea');
        ta.value = t;
        document.body.appendChild(ta);
        ta.select();
        try { document.execCommand('copy'); } catch (e) {}
        document.body.removeChild(ta);
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var u = pageUrl.value.trim();
        var txt = shareText.value.trim();
        if (!u) { showError('Please enter the page URL first.'); return; }
        if (!validUrl(u)) { showError('The URL is wrong. Type the full URL, e.g. https://example.com/page'); return; }
        var eu = encodeURIComponent(u);
        var et = encodeURIComponent(txt);
        var nets = [
            { name: 'Facebook', link: 'https://www.facebook.com/sharer/sharer.php?u=' + eu, color: '#1877F2' },
            { name: 'X (Twitter)', link: 'https://x.com/intent/tweet?url=' + eu + (txt ? '&text=' + et : ''), color: '#000000' },
            { name: 'LinkedIn', link: 'https://www.linkedin.com/sharing/share-offsite/?url=' + eu, color: '#0A66C2' },
            { name: 'WhatsApp', link: 'https://wa.me/?text=' + encodeURIComponent((txt ? txt + ' ' : '') + u), color: '#25D366' },
            { name: 'Telegram', link: 'https://t.me/share/url?url=' + eu + (txt ? '&text=' + et : ''), color: '#229ED9' },
            { name: 'Email', link: 'mailto:?subject=' + et + '&body=' + eu, color: '#6c757d' }
        ];
        linksList.innerHTML = '';
        nets.forEach(function (n) {
            var row = document.createElement('div');
            row.className = 'border rounded p-2 mb-2';
            var head = document.createElement('div');
            head.className = 'd-flex justify-content-between align-items-center mb-1';
            var nm = document.createElement('strong');
            nm.textContent = n.name;
            var btns = document.createElement('div');
            var cp = document.createElement('button');
            cp.type = 'button'; cp.className = 'btn btn-sm btn-outline-primary me-1';
            cp.textContent = 'Copy';
            cp.addEventListener('click', function () { copyText(n.link, cp); });
            var op = document.createElement('a');
            op.className = 'btn btn-sm btn-outline-secondary';
            op.textContent = 'Open';
            op.href = n.link;
            op.target = '_blank';
            op.rel = 'noopener';
            btns.appendChild(cp); btns.appendChild(op);
            head.appendChild(nm); head.appendChild(btns);
            var inp = document.createElement('input');
            inp.type = 'text'; inp.className = 'form-control form-control-sm text-muted';
            inp.value = n.link; inp.readOnly = true;
            inp.addEventListener('focus', function () { inp.select(); });
            row.appendChild(head); row.appendChild(inp);
            linksList.appendChild(row);
        });
        var snippet = nets.map(function (n) {
            return '<a href="' + n.link + '" target="_blank" rel="noopener" style="display:inline-block;padding:8px 14px;margin:4px;border-radius:6px;color:#fff;text-decoration:none;background:' + n.color + ';">Share on ' + n.name + '</a>';
        }).join('\n');
        htmlSnippet.textContent = snippet;
        results.classList.remove('d-none');
    });

    copyHtmlBtn.addEventListener('click', function () {
        copyText(htmlSnippet.textContent, copyHtmlBtn);
    });

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
})();
</script>
@endsection
