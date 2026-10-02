@extends('layouts.app')
@section('title', 'Review QR Generator - Azlaan Tools')
@section('meta_description', 'Generate a QR code for your Google review link so customers can scan and leave a review. Free online.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Review QR Generator</h1>
            <p class="lead text-muted">Make a QR code for your Google review link. Customers can scan and leave a review instantly — best for shops, restaurants and offices.</p>

            <div class="alert alert-info">
                <strong>Where to get the review link?</strong> Search your business on Google, right-click "Write a review" in the reviews section and copy the link. The link usually starts with <code>google.com/maps</code> or <code>g.page</code>.
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="reviewLink" class="form-label fw-semibold">Google review link</label>
                        <input type="url" class="form-control" id="reviewLink" placeholder="https://g.page/r/..../review or https://www.google.com/maps/...">
                        <div class="form-text">This link will be saved in the QR code.</div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-12 col-md-6">
                            <label for="bizName" class="form-label fw-semibold">Business name (optional)</label>
                            <input type="text" class="form-control" id="bizName" placeholder="e.g. Azlaan Electric AC Solar Center" maxlength="60">
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="qrSize" class="form-label fw-semibold">QR code size</label>
                            <select class="form-select" id="qrSize">
                                <option value="200">Small (200px)</option>
                                <option value="300" selected>Medium (300px)</option>
                                <option value="500">Large (500px - for print)</option>
                            </select>
                        </div>
                    </div>

                    <button type="button" class="btn btn-primary w-100" id="goBtn">Create QR Code</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4 text-center">
                        <div class="d-inline-block p-3 border rounded bg-white" id="qrWrap">
                            <div id="qrBox"></div>
                            <div id="qrCaption" class="mt-2 fw-semibold small text-break" style="max-width: 300px;"></div>
                        </div>
                        <p class="text-muted small mt-2 mb-3">Scan to review us on Google</p>
                        <div class="d-flex gap-2 justify-content-center flex-wrap">
                            <button type="button" class="btn btn-success" id="dlBtn">Download PNG</button>
                            <button type="button" class="btn btn-outline-secondary" id="copyBtn">Copy Link</button>
                        </div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Paste your Google review link.</li>
                <li>Choose a size and press "Create QR Code".</li>
                <li>Download the PNG, print it and display it in your shop.</li>
            </ol>
            <h2>Tips</h2>
            <ul>
                <li>For print, the <strong>Large (500px)</strong> size is best.</li>
                <li>Writing "Scan to leave a review" under the QR code encourages customers.</li>
            </ul>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
(function () {
    'use strict';
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var reviewLink = document.getElementById('reviewLink');
    var bizName = document.getElementById('bizName');
    var qrSize = document.getElementById('qrSize');
    var qrBox = document.getElementById('qrBox');
    var qrCaption = document.getElementById('qrCaption');
    var dlBtn = document.getElementById('dlBtn');
    var copyBtn = document.getElementById('copyBtn');

    var currentLink = '';

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function looksLikeReviewLink(url) {
        return /^https?:\/\//i.test(url) &&
            (url.indexOf('google.') !== -1 || url.indexOf('g.page') !== -1 || url.indexOf('goo.gl') !== -1);
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var link = reviewLink.value.trim();
        if (!link) { showError('First paste your Google review link.'); return; }
        if (typeof QRCode === 'undefined') {
            showError('QR library did not load. Check your internet and reload the page.');
            return;
        }
        if (!looksLikeReviewLink(link)) {
            showError('This does not look like a Google review link. Only use a google.com/maps, g.page or goo.gl link.');
            return;
        }
        currentLink = link;
        var size = parseInt(qrSize.value, 10);
        qrBox.innerHTML = '';
        try {
            new QRCode(qrBox, {
                text: link,
                width: size,
                height: size,
                correctLevel: QRCode.CorrectLevel.M
            });
        } catch (e) {
            showError('There was a problem creating the QR code. Check the link and try again.');
            return;
        }
        var name = bizName.value.trim();
        qrCaption.textContent = name || '';
        qrCaption.style.display = name ? 'block' : 'none';
        results.classList.remove('d-none');
    });

    dlBtn.addEventListener('click', function () {
        var canvas = qrBox.querySelector('canvas');
        var img = qrBox.querySelector('img');
        var src = null;
        if (canvas) { src = canvas.toDataURL('image/png'); }
        else if (img && img.src) { src = img.src; }
        if (!src) { showError('First create the QR code.'); return; }
        var a = document.createElement('a');
        a.href = src;
        var safe = (bizName.value.trim() || 'review-qr').toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '') || 'review-qr';
        a.download = safe + '-qr.png';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    });

    copyBtn.addEventListener('click', function () {
        if (!currentLink) { showError('First create the QR code.'); return; }
        var btn = this;
        function ok() {
            btn.textContent = 'Copied!';
            setTimeout(function () { btn.textContent = 'Copy Link'; }, 1200);
        }
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(currentLink).then(ok, ok);
        } else {
            var ta = document.createElement('textarea');
            ta.value = currentLink;
            document.body.appendChild(ta);
            ta.select();
            try { document.execCommand('copy'); } catch (e) {}
            document.body.removeChild(ta);
            ok();
        }
    });
})();
</script>
@endsection
