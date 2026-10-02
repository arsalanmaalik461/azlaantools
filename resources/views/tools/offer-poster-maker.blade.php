@extends('layouts.app')

@section('title', 'Offer Poster Maker - Azlaan Tools')
@section('meta_description', 'Design a sale or offer poster for WhatsApp status and your shop - free online poster maker.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Offer Poster Maker</h1>
            <p class="lead text-muted">Design a beautiful sale or offer poster — ready for WhatsApp status and your shop. Enter the details below and the poster will be created automatically.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="shopName" class="form-label fw-semibold">Shop / Brand name</label>
                            <input type="text" class="form-control" id="shopName" placeholder="Azlaan Electric Store" maxlength="40">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="themeSelect" class="form-label fw-semibold">Poster color</label>
                            <select class="form-select" id="themeSelect">
                                <option value="violet" selected>Violet (brand)</option>
                                <option value="red">Red Sale</option>
                                <option value="green">Green Fresh</option>
                                <option value="blue">Blue Trust</option>
                                <option value="gold">Golden Eid</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="headline" class="form-label fw-semibold">Main headline</label>
                        <input type="text" class="form-control" id="headline" placeholder="EID MEGA SALE" maxlength="30">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="discount" class="form-label fw-semibold">Discount (for example 50% OFF)</label>
                            <input type="text" class="form-control" id="discount" placeholder="50% OFF" maxlength="12">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="detailLine" class="form-label fw-semibold">Offer details</label>
                            <input type="text" class="form-control" id="detailLine" placeholder="Special discount on all solar panels" maxlength="60">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="oldPrice" class="form-label fw-semibold">Old price (optional)</label>
                            <input type="text" class="form-control" id="oldPrice" placeholder="Rs 5,000" maxlength="16">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="newPrice" class="form-label fw-semibold">New price (optional)</label>
                            <input type="text" class="form-control" id="newPrice" placeholder="Rs 3,999" maxlength="16">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="validTill" class="form-label fw-semibold">Offer valid till</label>
                            <input type="text" class="form-control" id="validTill" placeholder="till 31 Oct" maxlength="24">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="phoneNum" class="form-label fw-semibold">Contact number</label>
                        <input type="text" class="form-control" id="phoneNum" placeholder="0300-1234567" maxlength="20">
                    </div>

                    <button type="button" class="btn btn-primary w-100 mb-2" id="goBtn">Create Poster</button>
                    <button type="button" class="btn btn-success w-100 d-none" id="dlBtn">Download Poster (PNG)</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <p class="fw-semibold">Preview:</p>
                        <canvas id="posterCanvas" width="1080" height="1080" class="img-fluid rounded border w-100"></canvas>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Enter the shop name, headline, discount and prices.</li>
                <li>Choose your favorite color and press <strong>Create Poster</strong>.</li>
                <li>Check the preview, then press <strong>Download</strong> and use it for WhatsApp status or printing.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var shopName = document.getElementById('shopName');
    var themeSelect = document.getElementById('themeSelect');
    var headline = document.getElementById('headline');
    var discount = document.getElementById('discount');
    var detailLine = document.getElementById('detailLine');
    var oldPrice = document.getElementById('oldPrice');
    var newPrice = document.getElementById('newPrice');
    var validTill = document.getElementById('validTill');
    var phoneNum = document.getElementById('phoneNum');
    var goBtn = document.getElementById('goBtn');
    var dlBtn = document.getElementById('dlBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var canvas = document.getElementById('posterCanvas');

    var THEMES = {
        violet: ['#7c3aed', '#4c1d95', '#fbbf24'],
        red: ['#dc2626', '#7f1d1d', '#fde68a'],
        green: ['#059669', '#064e3b', '#fef3c7'],
        blue: ['#2563eb', '#1e3a8a', '#fbbf24'],
        gold: ['#b45309', '#451a03', '#fef3c7']
    };

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function fitText(ctx, text, maxWidth, baseSize, weight) {
        var size = baseSize;
        ctx.font = weight + ' ' + size + 'px Arial, sans-serif';
        while (size > 20 && ctx.measureText(text).width > maxWidth) {
            size -= 4;
            ctx.font = weight + ' ' + size + 'px Arial, sans-serif';
        }
        return size;
    }

    function drawPoster() {
        var ctx = canvas.getContext('2d');
        var W = 1080, H = 1080;
        var theme = THEMES[themeSelect.value] || THEMES.violet;
        var grad = ctx.createLinearGradient(0, 0, W, H);
        grad.addColorStop(0, theme[0]);
        grad.addColorStop(1, theme[1]);
        ctx.fillStyle = grad;
        ctx.fillRect(0, 0, W, H);

        // decorative circles
        ctx.globalAlpha = 0.12;
        ctx.fillStyle = '#ffffff';
        ctx.beginPath(); ctx.arc(150, 200, 220, 0, Math.PI * 2); ctx.fill();
        ctx.beginPath(); ctx.arc(950, 850, 280, 0, Math.PI * 2); ctx.fill();
        ctx.beginPath(); ctx.arc(900, 180, 120, 0, Math.PI * 2); ctx.fill();
        ctx.globalAlpha = 1;

        ctx.textAlign = 'center';
        ctx.fillStyle = '#ffffff';

        // shop name top
        var shop = shopName.value.trim() || 'YOUR SHOP NAME';
        fitText(ctx, shop.toUpperCase(), 900, 54, 'bold');
        ctx.fillText(shop.toUpperCase(), W / 2, 130);

        // divider line
        ctx.fillStyle = theme[2];
        ctx.fillRect(W / 2 - 140, 160, 280, 6);

        // headline
        var head = headline.value.trim() || 'MEGA SALE';
        ctx.fillStyle = '#ffffff';
        fitText(ctx, head.toUpperCase(), 920, 110, '900');
        ctx.fillText(head.toUpperCase(), W / 2, 300);

        // discount badge circle
        var disc = discount.value.trim() || '50% OFF';
        ctx.beginPath(); ctx.arc(W / 2, 500, 130, 0, Math.PI * 2);
        ctx.fillStyle = theme[2]; ctx.fill();
        ctx.lineWidth = 8; ctx.strokeStyle = '#ffffff'; ctx.stroke();
        ctx.fillStyle = theme[1];
        fitText(ctx, disc.toUpperCase(), 220, 64, '900');
        ctx.fillText(disc.toUpperCase(), W / 2, 522);

        // detail line
        var det = detailLine.value.trim();
        if (det) {
            ctx.fillStyle = '#ffffff';
            fitText(ctx, det, 900, 44, 'normal');
            ctx.fillText(det, W / 2, 700);
        }

        // prices
        var oldP = oldPrice.value.trim(), newP = newPrice.value.trim();
        if (newP) {
            var y = 800;
            if (oldP) {
                ctx.fillStyle = 'rgba(255,255,255,0.75)';
                fitText(ctx, oldP, 400, 52, 'normal');
                var ow = ctx.measureText(oldP).width;
                ctx.fillText(oldP, W / 2 - 160, y);
                ctx.strokeStyle = '#ffffff'; ctx.lineWidth = 5;
                ctx.beginPath();
                ctx.moveTo(W / 2 - 160 - ow / 2 - 10, y - 18);
                ctx.lineTo(W / 2 - 160 + ow / 2 + 10, y - 18);
                ctx.stroke();
                ctx.fillStyle = theme[2];
                fitText(ctx, newP, 420, 76, '900');
                ctx.fillText(newP, W / 2 + 200, y + 12);
            } else {
                ctx.fillStyle = theme[2];
                fitText(ctx, newP, 700, 84, '900');
                ctx.fillText(newP, W / 2, y + 12);
            }
        }

        // validity
        var valid = validTill.value.trim();
        if (valid) {
            ctx.fillStyle = '#ffffff';
            fitText(ctx, 'Offer: ' + valid, 700, 40, 'bold');
            ctx.fillText('Offer: ' + valid, W / 2, 905);
        }

        // footer bar with phone
        ctx.fillStyle = 'rgba(0,0,0,0.35)';
        ctx.fillRect(0, H - 130, W, 130);
        var phone = phoneNum.value.trim();
        ctx.fillStyle = '#ffffff';
        fitText(ctx, phone ? 'Contact: ' + phone : 'Write your contact number', 800, 52, 'bold');
        ctx.fillText(phone ? 'Contact: ' + phone : 'Write your contact number', W / 2, H - 55);
    }

    goBtn.addEventListener('click', function () {
        hideError();
        if (!headline.value.trim() && !discount.value.trim() && !detailLine.value.trim()) {
            showError('Please enter at least a headline or discount.');
            return;
        }
        drawPoster();
        results.classList.remove('d-none');
        dlBtn.classList.remove('d-none');
        results.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    });

    dlBtn.addEventListener('click', function () {
        var a = document.createElement('a');
        a.href = canvas.toDataURL('image/png');
        var name = (shopName.value.trim() || 'offer') .replace(/[^a-z0-9]+/gi, '-').toLowerCase();
        a.download = 'poster-' + name + '.png';
        document.body.appendChild(a);
        a.click();
        a.remove();
    });
})();
</script>
@endsection
