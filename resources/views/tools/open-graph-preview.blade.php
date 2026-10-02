@extends('layouts.app')

@section('title', 'Open Graph Preview - Azlaan Tools')
@section('meta_description', 'Free open graph preview tool: see how your link looks on Facebook, WhatsApp, LinkedIn and X before you share it.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Open Graph Preview</h1>
            <p class="lead text-muted">See how your link preview looks on Facebook, LinkedIn and WhatsApp — check here before you share.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="pageUrl" class="form-label fw-semibold">Page URL</label>
                            <input type="text" class="form-control" id="pageUrl" placeholder="https://example.com/article" dir="ltr">
                        </div>
                        <div class="col-md-6">
                            <label for="siteName" class="form-label fw-semibold">Site name (optional)</label>
                            <input type="text" class="form-control" id="siteName" placeholder="Azlaan Tools">
                        </div>
                        <div class="col-12">
                            <label for="ogTitle" class="form-label fw-semibold">Title (og:title)</label>
                            <input type="text" class="form-control" id="ogTitle" placeholder="Your page title">
                            <div class="form-text"><span id="titleCount">0</span>/60 characters — over 60 gets cut off on Facebook.</div>
                        </div>
                        <div class="col-12">
                            <label for="ogDesc" class="form-label fw-semibold">Description (og:description)</label>
                            <textarea class="form-control" id="ogDesc" rows="2" placeholder="Short page description"></textarea>
                            <div class="form-text"><span id="descCount">0</span> characters — 110 to 160 is best.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="ogImageUrl" class="form-label fw-semibold">Image URL (og:image)</label>
                            <input type="text" class="form-control" id="ogImageUrl" placeholder="https://example.com/image.jpg" dir="ltr">
                            <div class="form-text">1200 x 630 px recommended.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="ogImageFile" class="form-label fw-semibold">Or upload an image (for preview)</label>
                            <input type="file" class="form-control" id="ogImageFile" accept="image/*">
                        </div>
                        <div class="col-12">
                            <label for="htmlInput" class="form-label fw-semibold">Auto-extract from HTML (optional)</label>
                            <textarea class="form-control" id="htmlInput" rows="3" placeholder="Paste your page HTML source here — og:title, og:description, og:image will be extracted automatically" dir="ltr"></textarea>
                        </div>
                    </div>
                    <div class="d-flex flex-wrap gap-2 mt-3">
                        <button type="button" class="btn btn-outline-secondary" id="extractBtn">Extract from HTML</button>
                        <button type="button" class="btn btn-primary flex-grow-1" id="goBtn">Make Previews</button>
                    </div>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <h5>Checklist</h5>
                        <ul class="list-group mb-4" id="checkList"></ul>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <h6 class="fw-bold">Facebook</h6>
                                <div class="border rounded overflow-hidden" style="max-width:420px;">
                                    <div id="fbImg" class="bg-light d-flex align-items-center justify-content-center text-muted" style="height:200px;background-size:cover;background-position:center;">No image</div>
                                    <div class="p-2" style="background:#f0f2f5;">
                                        <div class="small text-muted text-uppercase" id="fbDomain">example.com</div>
                                        <div class="fw-bold" id="fbTitle" style="font-size:15px;">Title</div>
                                        <div class="small text-muted" id="fbDesc">Description</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6 class="fw-bold">WhatsApp</h6>
                                <div class="rounded p-3" style="background:#e7ffdb;max-width:420px;">
                                    <div class="rounded overflow-hidden border bg-white">
                                        <div id="waImg" class="bg-light d-flex align-items-center justify-content-center text-muted" style="height:150px;background-size:cover;background-position:center;">No image</div>
                                        <div class="p-2">
                                            <div class="fw-bold small" id="waTitle">Title</div>
                                            <div class="small text-muted" id="waDesc">Description</div>
                                            <div class="small text-muted" id="waDomain">example.com</div>
                                        </div>
                                    </div>
                                    <div class="small mt-1 text-primary" id="waUrl">https://example.com</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6 class="fw-bold">LinkedIn</h6>
                                <div class="border rounded overflow-hidden bg-white" style="max-width:420px;">
                                    <div id="liImg" class="bg-light d-flex align-items-center justify-content-center text-muted" style="height:180px;background-size:cover;background-position:center;">No image</div>
                                    <div class="p-2 border-top">
                                        <div class="fw-bold" style="font-size:14px;" id="liTitle">Title</div>
                                        <div class="small text-muted" id="liDomain">example.com</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6 class="fw-bold">X (Twitter) — Large Card</h6>
                                <div class="border rounded-3 overflow-hidden bg-white" style="max-width:420px;">
                                    <div id="xImg" class="bg-light d-flex align-items-center justify-content-center text-muted" style="height:190px;background-size:cover;background-position:center;">No image</div>
                                    <div class="p-2 border-top">
                                        <div style="font-size:14px;" id="xTitle">Title</div>
                                        <div class="small text-muted" id="xDesc">Description</div>
                                        <div class="small text-muted">🔗 <span id="xDomain">example.com</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="alert alert-info small mt-4 mb-0">These are <strong>mock previews</strong> — the real platform layouts may differ slightly. This tool does not fetch your page itself; the values were entered by you or extracted from HTML.</div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Enter the title, description and image URL — or paste your page HTML and press "Extract".</li>
                <li>Press "Make Previews" — cards for 4 platforms will appear together.</li>
                <li>Fix any checklist issues in your page's &lt;meta&gt; tags.</li>
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
    var siteName = document.getElementById('siteName');
    var ogTitle = document.getElementById('ogTitle');
    var ogDesc = document.getElementById('ogDesc');
    var ogImageUrl = document.getElementById('ogImageUrl');
    var ogImageFile = document.getElementById('ogImageFile');
    var htmlInput = document.getElementById('htmlInput');
    var extractBtn = document.getElementById('extractBtn');
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var checkList = document.getElementById('checkList');
    var titleCount = document.getElementById('titleCount');
    var descCount = document.getElementById('descCount');

    var uploadedImgUrl = '';
    var imgNatural = null;

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    ogTitle.addEventListener('input', function () { titleCount.textContent = ogTitle.value.length; });
    ogDesc.addEventListener('input', function () { descCount.textContent = ogDesc.value.length; });

    ogImageFile.addEventListener('change', function () {
        var f = ogImageFile.files[0];
        if (!f) return;
        uploadedImgUrl = URL.createObjectURL(f);
        var im = new Image();
        im.onload = function () { imgNatural = { w: im.naturalWidth, h: im.naturalHeight }; };
        im.src = uploadedImgUrl;
    });

    function metaContent(html, prop) {
        var re = new RegExp('<meta[^>]+(?:property|name)=["\']' + prop + '["\'][^>]*>', 'i');
        var m = html.match(re);
        if (!m) return '';
        var c = m[0].match(/content=["']([^"']*)["']/i);
        return c ? c[1] : '';
    }
    extractBtn.addEventListener('click', function () {
        hideError();
        var html = htmlInput.value;
        if (!html.trim()) { showError('Paste page HTML first.'); return; }
        var t = metaContent(html, 'og:title') || (html.match(/<title[^>]*>([^<]*)<\/title>/i) || [])[1] || '';
        var d = metaContent(html, 'og:description') || metaContent(html, 'description');
        var im = metaContent(html, 'og:image');
        var sn = metaContent(html, 'og:site_name');
        if (t) { ogTitle.value = t; titleCount.textContent = t.length; }
        if (d) { ogDesc.value = d; descCount.textContent = d.length; }
        if (im) ogImageUrl.value = im;
        if (sn) siteName.value = sn;
        if (!t && !d && !im) showError('No og: tags found. Enter the title/description by hand.');
    });

    function domainOf(u) {
        try { return new URL(u).hostname.replace(/^www\./, ''); }
        catch (e) { return u ? u.replace(/^https?:\/\//, '').split('/')[0] : 'example.com'; }
    }
    function setImg(el, url) {
        if (url) {
            el.style.backgroundImage = 'url("' + url.replace(/"/g, '') + '")';
            el.textContent = '';
        } else {
            el.style.backgroundImage = '';
            el.textContent = 'No image';
        }
    }
    function addCheck(ok, text) {
        var li = document.createElement('li');
        li.className = 'list-group-item d-flex gap-2 align-items-start';
        var badge = document.createElement('span');
        badge.className = 'badge ' + (ok ? 'bg-success' : 'bg-danger');
        badge.textContent = ok ? 'OK' : '!';
        var sp = document.createElement('span');
        sp.textContent = text;
        li.appendChild(badge); li.appendChild(sp);
        checkList.appendChild(li);
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var title = ogTitle.value.trim(), desc = ogDesc.value.trim();
        var img = uploadedImgUrl || ogImageUrl.value.trim();
        var url = pageUrl.value.trim() || 'https://example.com';
        var dom = domainOf(url);
        if (!title && !desc && !img) { showError('Please give at least a title, description or image.'); return; }

        document.getElementById('fbTitle').textContent = title || '(no title)';
        document.getElementById('fbDesc').textContent = desc || '';
        document.getElementById('fbDomain').textContent = dom;
        document.getElementById('waTitle').textContent = title || url;
        document.getElementById('waDesc').textContent = desc || '';
        document.getElementById('waDomain').textContent = dom;
        document.getElementById('waUrl').textContent = url;
        document.getElementById('liTitle').textContent = title || '(no title)';
        document.getElementById('liDomain').textContent = (siteName.value.trim() ? siteName.value.trim() + ' • ' : '') + dom;
        document.getElementById('xTitle').textContent = title || '(no title)';
        document.getElementById('xDesc').textContent = desc || '';
        document.getElementById('xDomain').textContent = dom;
        setImg(document.getElementById('fbImg'), img);
        setImg(document.getElementById('waImg'), img);
        setImg(document.getElementById('liImg'), img);
        setImg(document.getElementById('xImg'), img);

        checkList.innerHTML = '';
        addCheck(title.length > 0, title.length ? 'Title is present (' + title.length + ' chars)' : 'Title is missing — add og:title');
        addCheck(title.length > 0 && title.length <= 60, title.length <= 60 ? 'Title is within 60 characters' : 'Title is ' + title.length + ' chars — keep it to 60 or it will be cut off');
        addCheck(desc.length >= 50, desc.length ? 'Description is present (' + desc.length + ' chars)' : 'Description is missing — add og:description');
        addCheck(desc.length === 0 || (desc.length >= 110 && desc.length <= 160), 'Description is ideal at 110–160 characters (now ' + desc.length + ')');
        addCheck(!!img, img ? 'Image is present' : 'Image is missing — add og:image or the preview will look empty');
        if (imgNatural) {
            var ratio = imgNatural.w / imgNatural.h;
            addCheck(imgNatural.w >= 1200 && imgNatural.h >= 630, 'Uploaded image is ' + imgNatural.w + 'x' + imgNatural.h + ' px (recommended 1200x630)');
            addCheck(ratio > 1.7 && ratio < 2.1, 'Image ratio is ' + ratio.toFixed(2) + ':1 (ideal 1.91:1)');
        } else if (img) {
            addCheck(true, 'Image URL given — upload a file to check size');
        }
        addCheck(/^https:\/\//i.test(url), /^https:\/\//i.test(url) ? 'URL uses https' : 'URL should use https');
        results.classList.remove('d-none');
    });
})();
</script>
@endsection
