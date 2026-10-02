@extends('layouts.app')

@section('title', 'Twitter Card Preview - Azlaan Tools')
@section('meta_description', 'Preview how your link will look as a Twitter/X card before you share it. Free design mockup, no signup.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Twitter Card Preview</h1>
            <p class="lead text-muted">See how your card will look before you share a link on X (Twitter) — a design preview of the title, image and description.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="cardType" class="form-label fw-semibold">Card type</label>
                        <select class="form-select" id="cardType">
                            <option value="large" selected>Summary Card with Large Image (2:1 image)</option>
                            <option value="small">Summary Card (square image)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="pageUrl" class="form-label fw-semibold">Page URL</label>
                        <input type="text" class="form-control" id="pageUrl" placeholder="https://example.com/my-article" dir="ltr">
                    </div>
                    <div class="mb-3">
                        <label for="cardTitle" class="form-label fw-semibold">Card title (og:title)</label>
                        <input type="text" class="form-control" id="cardTitle" placeholder="A catchy title for my post" maxlength="120">
                        <div class="form-text"><span id="titleLen">0</span>/120 - long titles get cut off on X.</div>
                    </div>
                    <div class="mb-3">
                        <label for="cardDesc" class="form-label fw-semibold">Description (og:description)</label>
                        <textarea class="form-control" id="cardDesc" rows="2" placeholder="Short description that appears on the card" maxlength="220"></textarea>
                        <div class="form-text"><span id="descLen">0</span>/220 characters.</div>
                    </div>
                    <div class="mb-3">
                        <label for="imgUrl" class="form-label fw-semibold">Image URL (or upload a file)</label>
                        <input type="text" class="form-control mb-2" id="imgUrl" placeholder="https://example.com/image.jpg" dir="ltr">
                        <input type="file" class="form-control" id="imgFile" accept="image/*">
                        <div class="form-text">1200 x 628 px is best for a large card, 400 x 400 px for a summary card.</div>
                    </div>

                    <button type="button" class="btn btn-primary w-100" id="goBtn">Create Card Preview</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="fw-semibold mb-2">Mockup preview:</div>
                        <div class="border rounded overflow-hidden" style="max-width:500px;" id="mockCard">
                            <img id="mockImg" class="w-100 d-block" alt="Card image preview">
                            <div class="p-3 bg-light border-top">
                                <div class="small text-muted" id="mockDomain"></div>
                                <div class="fw-bold" id="mockTitle"></div>
                                <div class="text-muted" id="mockDesc"></div>
                            </div>
                        </div>
                        <div class="alert alert-info mt-3 small" id="checklist"></div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Write the card type, page URL, title and description.</li>
                <li>Give the image URL or upload a file.</li>
                <li>Press <strong>Create Card Preview</strong> — the mockup appears below.</li>
                <li>Read the checklist and fix your page meta tags.</li>
            </ol>
            <p class="small text-muted">Important: this is only a <strong>design mockup</strong>. X builds the real card when your page is public and has <code>twitter:card</code>, <code>og:title</code>, <code>og:description</code>, <code>og:image</code> meta tags. Always do the final check with the official X Card Validator.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var cardType = document.getElementById('cardType');
    var pageUrl = document.getElementById('pageUrl');
    var cardTitle = document.getElementById('cardTitle');
    var cardDesc = document.getElementById('cardDesc');
    var imgUrl = document.getElementById('imgUrl');
    var imgFile = document.getElementById('imgFile');
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var mockCard = document.getElementById('mockCard');
    var mockImg = document.getElementById('mockImg');
    var mockDomain = document.getElementById('mockDomain');
    var mockTitle = document.getElementById('mockTitle');
    var mockDesc = document.getElementById('mockDesc');
    var checklist = document.getElementById('checklist');
    var titleLen = document.getElementById('titleLen');
    var descLen = document.getElementById('descLen');

    var uploadedDataUrl = null;

    cardTitle.addEventListener('input', function () { titleLen.textContent = cardTitle.value.length; });
    cardDesc.addEventListener('input', function () { descLen.textContent = cardDesc.value.length; });

    imgFile.addEventListener('change', function () {
        var f = imgFile.files[0];
        if (!f || !f.type.match(/^image\//)) return;
        var r = new FileReader();
        r.onload = function (e) { uploadedDataUrl = e.target.result; imgUrl.value = ''; };
        r.readAsDataURL(f);
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
    function domainOf(u) {
        var m = u.match(/^https?:\/\/([^/]+)/i);
        return m ? m[1].replace(/^www\./, '') : '';
    }
    function trunc(s, n) {
        return s.length > n ? s.slice(0, n - 1) + '...' : s;
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var t = cardTitle.value.trim();
        var d = cardDesc.value.trim();
        var u = pageUrl.value.trim();
        var iu = imgUrl.value.trim();
        if (!t) { showError('Please enter a card title.'); return; }
        if (!iu && !uploadedDataUrl) { showError('Please add an image URL or upload one.'); return; }

        var imgSrc = uploadedDataUrl || iu;
        var large = cardType.value === 'large';
        mockImg.src = imgSrc;
        mockImg.style.aspectRatio = large ? '2 / 1' : '1 / 1';
        mockImg.style.objectFit = 'cover';
        mockDomain.textContent = u ? domainOf(u) : 'example.com';
        mockTitle.textContent = large ? trunc(t, 70) : trunc(t, 60);
        mockDesc.textContent = large ? trunc(d, 140) : trunc(d, 100);

        var tips = [];
        tips.push('Meta tags to add to your page: twitter:card="' + (large ? 'summary_large_image' : 'summary') + '"');
        tips.push('og:title, og:description and og:image are required. The image URL must be absolute (https://...).');
        tips.push(large ? 'The best image size is 1200 x 628 px.' : 'The best image size is 400 x 400 px.');
        if (t.length > 70) tips.push('Note: your title is long, it may get cut off on X.');
        tips.push('Live check: enter your URL in the X Card Validator to see the official preview.');
        checklist.innerHTML = '<strong>SEO Checklist:</strong><ul class="mb-0"><li>' + tips.join('</li><li>') + '</li></ul>';

        results.classList.remove('d-none');
    });
})();
</script>
@endsection
