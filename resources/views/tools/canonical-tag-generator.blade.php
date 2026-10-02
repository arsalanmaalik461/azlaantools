@extends('layouts.app')
@section('title', 'Canonical Tag Generator - Azlaan Tools')
@section('meta_description', 'Generate the canonical link tag to protect your pages from duplicate content issues. Free SEO tool — copy-ready code in seconds.')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Canonical Tag Generator</h1>
            <p class="lead text-muted">Create the canonical link tag code to protect your pages from duplicate content. Enter the URL, choose the options, and copy the ready code.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="canonUrl" class="form-label fw-semibold">Canonical URL (master version of the page)</label>
                        <input type="text" class="form-control" id="canonUrl" placeholder="https://example.com/blog/my-article/">
                        <div class="form-text">Always enter the full URL — with https. Do not use relative URLs (/page).</div>
                    </div>

                    <div class="mb-3">
                        <span class="form-label fw-semibold d-block">Options</span>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="optSelf" checked>
                            <label class="form-check-label" for="optSelf">Self-referencing tag — also add this tag on this URL</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="optTrailing">
                            <label class="form-check-label" for="optTrailing">Add trailing slash (a / at the end of the URL)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="optLower">
                            <label class="form-check-label" for="optLower">Make the URL lowercase (uppercase creates duplicate pages)</label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="pageList" class="form-label fw-semibold">Paginated / duplicate URLs (optional)</label>
                        <textarea class="form-control" id="pageList" rows="4" placeholder="One URL per line — the pages where you want to use this canonical:&#10;https://example.com/blog/my-article/?page=2&#10;https://example.com/blog/my-article?utm_source=fb"></textarea>
                        <div class="form-text">For every line you enter, you will get one ready-to-paste tag set.</div>
                    </div>

                    <button type="button" class="btn btn-primary w-100" id="goBtn">Generate Canonical Tag</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <span class="form-label fw-semibold d-block mb-2">Generated code</span>
                        <textarea class="form-control font-monospace small mb-2" id="codeOut" rows="3" readonly></textarea>
                        <button type="button" class="btn btn-outline-primary btn-sm mb-3" id="copyBtn">Copy Code</button>

                        <div id="pagedOut" class="d-none">
                            <span class="form-label fw-semibold d-block mb-2">Tags for paginated / duplicate URLs</span>
                            <textarea class="form-control font-monospace small mb-2" id="pageCodeOut" rows="8" readonly></textarea>
                            <button type="button" class="btn btn-outline-primary btn-sm mb-3" id="copyPagesBtn">Copy All</button>
                        </div>

                        <div class="alert alert-info small mb-0">
                            <strong>Where to place it:</strong> Paste this tag in each page's <code>&lt;head&gt;</code> section, before <code>&lt;/head&gt;</code>. In WordPress, the RankMath/Yoast plugin does this automatically — no need to add it manually.
                        </div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Enter the page's canonical (original/master) URL — full https URL.</li>
                <li>Tick the options you need and, if you want, give a list of paginated URLs.</li>
                <li>Press Generate, copy the code, and paste it into the page's <code>&lt;head&gt;</code>.</li>
            </ol>
            <p class="text-muted small">Tip: the canonical tag is a suggestion, not an order — Google usually follows it, but if the content is really different, the pages may still be indexed separately.</p>
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
    var canonUrl = document.getElementById('canonUrl');
    var optSelf = document.getElementById('optSelf');
    var optTrailing = document.getElementById('optTrailing');
    var optLower = document.getElementById('optLower');
    var pageList = document.getElementById('pageList');
    var codeOut = document.getElementById('codeOut');
    var copyBtn = document.getElementById('copyBtn');
    var pagedOut = document.getElementById('pagedOut');
    var pageCodeOut = document.getElementById('pageCodeOut');
    var copyPagesBtn = document.getElementById('copyPagesBtn');

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function esc(s) {
        return String(s).replace(/&/g, '&amp;').replace(/"/g, '&quot;');
    }

    function cleanUrl(raw) {
        var u = raw.trim();
        if (!u) return '';
        if (!/^https?:\/\//i.test(u)) u = 'https://' + u;
        if (optLower.checked) {
            try {
                var parsed = new URL(u);
                u = parsed.protocol + '//' + parsed.host.toLowerCase() + parsed.pathname.toLowerCase() + parsed.search + parsed.hash;
            } catch (e) { u = u.toLowerCase(); }
        }
        if (optTrailing.checked) {
            try {
                var p2 = new URL(u);
                if (!/\.[a-z0-9]+$/i.test(p2.pathname) && p2.pathname.slice(-1) !== '/') p2.pathname += '/';
                u = p2.toString();
            } catch (e) { if (u.slice(-1) !== '/') u += '/'; }
        }
        return u;
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var raw = canonUrl.value.trim();
        if (!raw) { showError('Please enter a canonical URL.'); return; }
        var canon = cleanUrl(raw);
        try { new URL(canon); } catch (e) {
            showError('This URL does not look valid. Enter the full URL, like https://example.com/page/');
            return;
        }
        var tag = '<link rel="canonical" href="' + esc(canon) + '" />';
        var notes = [];
        if (optSelf.checked) notes.push('<!-- self-referencing canonical: also add this tag on this URL -->');
        codeOut.value = (notes.length ? notes.join('\n') + '\n' : '') + tag;
        results.classList.remove('d-none');

        var lines = pageList.value.split('\n').map(function (l) { return l.trim(); }).filter(function (l) { return l.length > 0; });
        if (lines.length) {
            var bad = [];
            var out = [];
            lines.forEach(function (l) {
                var c = cleanUrl(l);
                try { new URL(c); out.push('<!-- page: ' + esc(l) + ' -->\n' + tag); }
                catch (e) { bad.push(l); }
            });
            if (bad.length) {
                showError('Some URLs were wrong and were skipped: ' + bad.join(', '));
                results.classList.remove('d-none');
            }
            if (out.length) {
                pageCodeOut.value = out.join('\n\n');
                pagedOut.classList.remove('d-none');
            } else {
                pagedOut.classList.add('d-none');
            }
        } else {
            pagedOut.classList.add('d-none');
        }
    });

    function copyText(el, btn) {
        navigator.clipboard.writeText(el.value).then(function () {
            var old = btn.textContent;
            btn.textContent = 'Copied!';
            setTimeout(function () { btn.textContent = old; }, 1500);
        }).catch(function () { showError('Could not copy - select the text and press Ctrl+C.'); });
    }
    copyBtn.addEventListener('click', function () { copyText(codeOut, copyBtn); });
    copyPagesBtn.addEventListener('click', function () { copyText(pageCodeOut, copyPagesBtn); });
})();
</script>
@endsection
