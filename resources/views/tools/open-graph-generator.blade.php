@extends('layouts.app')

@section('title', 'Open Graph Generator - Azlaan Tools')
@section('meta_description', 'Generate Open Graph and Twitter Card meta tags with a live social share preview. Free online tool.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Open Graph Generator</h1>
            <p class="lead text-muted">See what preview appears when your page is shared on Facebook, WhatsApp and Twitter — make meta tags and check the live preview.</p>

            <div class="row g-4">
                <div class="col-12 col-lg-6">
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="ogTitle" class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="ogTitle" placeholder="Page title" maxlength="120">
                                <div class="form-text"><span id="titleCount">0</span>/120 — up to 60 characters is ideal.</div>
                            </div>
                            <div class="mb-3">
                                <label for="ogDesc" class="form-label fw-semibold">Description</label>
                                <textarea class="form-control" id="ogDesc" rows="3" placeholder="Short description" maxlength="300"></textarea>
                                <div class="form-text"><span id="descCount">0</span>/300 — 150-160 characters is ideal.</div>
                            </div>
                            <div class="mb-3">
                                <label for="ogUrl" class="form-label fw-semibold">Page URL</label>
                                <input type="text" class="form-control" id="ogUrl" placeholder="https://example.com/page">
                            </div>
                            <div class="mb-3">
                                <label for="ogImage" class="form-label fw-semibold">Image URL</label>
                                <input type="text" class="form-control" id="ogImage" placeholder="https://example.com/image.jpg">
                                <div class="form-text">1200x630 px recommended.</div>
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label for="ogType" class="form-label fw-semibold">Type</label>
                                    <select class="form-select" id="ogType">
                                        <option value="website">website</option>
                                        <option value="article">article</option>
                                        <option value="product">product</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="twCard" class="form-label fw-semibold">Twitter Card</label>
                                    <select class="form-select" id="twCard">
                                        <option value="summary_large_image">summary_large_image</option>
                                        <option value="summary">summary</option>
                                    </select>
                                </div>
                            </div>
                            <button type="button" class="btn btn-primary w-100" id="goBtn">Generate Meta Tags</button>
                            <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                            <div id="results" class="d-none mt-4">
                                <label for="tagOut" class="form-label fw-semibold">Meta tags (paste in head)</label>
                                <textarea class="form-control font-monospace mb-2" id="tagOut" rows="10" readonly style="font-size:12px;"></textarea>
                                <button type="button" class="btn btn-outline-primary" id="copyBtn">Copy Tags</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-6">
                    <h5 class="mb-2">Live Preview</h5>
                    <p class="text-muted small">The preview updates as you type.</p>
                    <div class="card mb-3">
                        <div id="pvImgWrap" class="d-none">
                            <img id="pvImg" src="" alt="preview" class="card-img-top" style="max-height:220px; object-fit:cover;" onerror="this.parentNode.classList.add('d-none')">
                        </div>
                        <div class="card-body">
                            <div class="text-muted small text-uppercase" id="pvDomain">example.com</div>
                            <h6 class="card-title mb-1" id="pvTitle">Your title will appear here</h6>
                            <p class="card-text small text-muted mb-0" id="pvDesc">Description will appear here.</p>
                        </div>
                        <div class="card-footer text-muted small">Facebook / WhatsApp style preview</div>
                    </div>
                    <div class="border rounded p-3">
                        <div class="fw-semibold mb-1" id="twTitle">Your title</div>
                        <div class="text-muted small mb-2" id="twDesc">Description...</div>
                        <div class="text-muted small">Twitter Card style preview</div>
                    </div>
                </div>
            </div>

            <h2 class="mt-2">How to use</h2>
            <ol>
                <li>Enter the title, description, page URL and image link.</li>
                <li>Check the live preview on the right side.</li>
                <li>Press "Generate Meta Tags" and copy the tags into your website's <code>&lt;head&gt;</code>.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var ogTitle = document.getElementById('ogTitle');
    var ogDesc = document.getElementById('ogDesc');
    var ogUrl = document.getElementById('ogUrl');
    var ogImage = document.getElementById('ogImage');
    var ogType = document.getElementById('ogType');
    var twCard = document.getElementById('twCard');
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var tagOut = document.getElementById('tagOut');
    var copyBtn = document.getElementById('copyBtn');
    var titleCount = document.getElementById('titleCount');
    var descCount = document.getElementById('descCount');
    var pvTitle = document.getElementById('pvTitle');
    var pvDesc = document.getElementById('pvDesc');
    var pvDomain = document.getElementById('pvDomain');
    var pvImg = document.getElementById('pvImg');
    var pvImgWrap = document.getElementById('pvImgWrap');
    var twTitle = document.getElementById('twTitle');
    var twDesc = document.getElementById('twDesc');

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function escAttr(s) {
        return String(s).replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }
    function esc(s) { return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;'); }

    function livePreview() {
        var t = ogTitle.value.trim() || 'Your title will appear here';
        var d = ogDesc.value.trim() || 'Description will appear here.';
        var u = ogUrl.value.trim();
        var img = ogImage.value.trim();
        titleCount.textContent = ogTitle.value.length;
        descCount.textContent = ogDesc.value.length;
        pvTitle.textContent = t;
        pvDesc.textContent = d;
        twTitle.textContent = t;
        twDesc.textContent = d;
        if (u) {
            try { pvDomain.textContent = new URL(u).hostname; } catch (e) { pvDomain.textContent = u; }
        } else { pvDomain.textContent = 'example.com'; }
        if (img && /^https?:\/\//.test(img)) {
            pvImgWrap.classList.remove('d-none');
            if (pvImg.getAttribute('src') !== img) pvImg.setAttribute('src', img);
        } else {
            pvImgWrap.classList.add('d-none');
            pvImg.removeAttribute('src');
        }
    }
    ['input', 'change'].forEach(function (ev) {
        [ogTitle, ogDesc, ogUrl, ogImage].forEach(function (el) { el.addEventListener(ev, livePreview); });
    });
    livePreview();

    goBtn.addEventListener('click', function () {
        hideError();
        var t = ogTitle.value.trim();
        var d = ogDesc.value.trim();
        var u = ogUrl.value.trim();
        var img = ogImage.value.trim();
        if (!t) { showError('Please enter a title.'); return; }
        if (u && !/^https?:\/\/.+\..+/.test(u)) { showError('Page URL must start with https://.'); return; }
        if (img && !/^https?:\/\/.+/.test(img)) { showError('Image URL must start with https://.'); return; }

        var tags = [];
        tags.push('<meta property="og:title" content="' + escAttr(t) + '">');
        if (d) tags.push('<meta property="og:description" content="' + escAttr(d) + '">');
        if (u) tags.push('<meta property="og:url" content="' + escAttr(u) + '">');
        tags.push('<meta property="og:type" content="' + escAttr(ogType.value) + '">');
        if (img) tags.push('<meta property="og:image" content="' + escAttr(img) + '">');
        tags.push('<meta name="twitter:card" content="' + escAttr(twCard.value) + '">');
        tags.push('<meta name="twitter:title" content="' + escAttr(t) + '">');
        if (d) tags.push('<meta name="twitter:description" content="' + escAttr(d) + '">');
        if (img) tags.push('<meta name="twitter:image" content="' + escAttr(img) + '">');

        tagOut.value = tags.join('\n');
        results.classList.remove('d-none');
        results.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });

    copyBtn.addEventListener('click', function () {
        var txt = tagOut.value;
        if (!txt) return;
        function done() { copyBtn.textContent = 'Copied!'; setTimeout(function () { copyBtn.textContent = 'Copy Tags'; }, 1500); }
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(txt).then(done, function () { tagOut.select(); try { document.execCommand('copy'); done(); } catch (e) {} });
        } else { tagOut.select(); try { document.execCommand('copy'); done(); } catch (e) {} }
    });
})();
</script>
@endsection
