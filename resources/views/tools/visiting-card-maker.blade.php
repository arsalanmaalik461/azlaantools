@extends('layouts.app')

@section('title', 'Visiting Card Maker - Azlaan Tools')
@section('meta_description', 'Design your own business visiting card online for free and download a print-ready PNG.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Visiting Card Maker</h1>
            <p class="lead text-muted">Design your own business card — write the name, number and address, pick a color, and download a print-ready card (3.5 x 2 inch, 300 DPI).</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-5">
                            <h5>Card information</h5>
                            <div class="mb-2">
                                <label for="bizInput" class="form-label fw-semibold">Business / shop name</label>
                                <input type="text" class="form-control" id="bizInput" placeholder="e.g. Azlaan Electric AC Solar Center" maxlength="40">
                            </div>
                            <div class="mb-2">
                                <label for="nameInput" class="form-label fw-semibold">Your name</label>
                                <input type="text" class="form-control" id="nameInput" placeholder="e.g. Muhammad Arslan" maxlength="30">
                            </div>
                            <div class="mb-2">
                                <label for="titleInput" class="form-label fw-semibold">Designation / Title</label>
                                <input type="text" class="form-control" id="titleInput" placeholder="e.g. Owner / Manager" maxlength="30">
                            </div>
                            <div class="mb-2">
                                <label for="phoneInput" class="form-label fw-semibold">Phone / Mobile</label>
                                <input type="text" class="form-control" id="phoneInput" placeholder="e.g. 0300-8987448" maxlength="25">
                            </div>
                            <div class="mb-2">
                                <label for="emailInput" class="form-label fw-semibold">Email (optional)</label>
                                <input type="text" class="form-control" id="emailInput" placeholder="e.g. info@example.com" maxlength="40">
                            </div>
                            <div class="mb-2">
                                <label for="addrInput" class="form-label fw-semibold">Address</label>
                                <input type="text" class="form-control" id="addrInput" placeholder="e.g. Main Market, Faisalabad" maxlength="50">
                            </div>
                            <div class="mb-3">
                                <label for="themeSelect" class="form-label fw-semibold">Card color (theme)</label>
                                <select class="form-select" id="themeSelect">
                                    <option value="navy">Navy Blue + Gold</option>
                                    <option value="teal">Teal + White</option>
                                    <option value="maroon">Maroon + Cream</option>
                                    <option value="black">Black + Amber</option>
                                    <option value="green">Dark Green + Lime</option>
                                </select>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="button" class="btn btn-primary" id="goBtn">Make Card (Preview)</button>
                                <button type="button" class="btn btn-success" id="downloadBtn" disabled>Download Card (PNG)</button>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <h5>Live preview</h5>
                            <canvas id="cardCanvas" class="img-fluid border rounded shadow-sm w-100" width="1050" height="600" style="background:#fff;"></canvas>
                            <p class="form-text">Print size: 3.5 x 2 inch @ 300 DPI (1050 x 600 px). Give this file to the print shop.</p>
                        </div>
                    </div>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>
                    <div id="results" class="d-none mt-3">
                        <div class="alert alert-success" id="resultInfo"></div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li><strong>Type the details</strong> — business name, your name, designation, phone, email and address.</li>
                <li><strong>Pick a theme</strong> — choose one of 5 professional colors that suits your business.</li>
                <li>Press <strong>Make Card</strong> — see the live preview on the right side.</li>
                <li>Use <strong>Download Card</strong> to save the PNG file and get it printed at a print shop.</li>
            </ol>

            <h2 class="mt-4">When you print</h2>
            <p class="text-muted">A standard visiting card is 3.5 x 2 inch. This file is made at 300 DPI, which is right for printing. Matte or glossy lamination makes the card look more professional.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var bizInput = document.getElementById('bizInput');
    var nameInput = document.getElementById('nameInput');
    var titleInput = document.getElementById('titleInput');
    var phoneInput = document.getElementById('phoneInput');
    var emailInput = document.getElementById('emailInput');
    var addrInput = document.getElementById('addrInput');
    var themeSelect = document.getElementById('themeSelect');
    var goBtn = document.getElementById('goBtn');
    var downloadBtn = document.getElementById('downloadBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var resultInfo = document.getElementById('resultInfo');
    var canvas = document.getElementById('cardCanvas');

    var THEMES = {
        navy:   { bg: '#ffffff', band: '#0f2a5c', accent: '#c9a227', name: '#0f2a5c', sub: '#4a5568' },
        teal:   { bg: '#ffffff', band: '#0f766e', accent: '#14b8a6', name: '#0f766e', sub: '#4a5568' },
        maroon: { bg: '#fffaf0', band: '#7f1d1d', accent: '#d4a017', name: '#7f1d1d', sub: '#57534e' },
        black:  { bg: '#ffffff', band: '#111111', accent: '#f59e0b', name: '#111111', sub: '#4b5563' },
        green:  { bg: '#ffffff', band: '#14532d', accent: '#84cc16', name: '#14532d', sub: '#4a5568' }
    };

    var cardReady = false;

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function drawPlaceholder() {
        var ctx = canvas.getContext('2d');
        ctx.fillStyle = '#f8f9fa';
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        ctx.fillStyle = '#9ca3af';
        ctx.font = '28px Arial';
        ctx.textAlign = 'center';
        ctx.fillText('Card preview will appear here', canvas.width / 2, canvas.height / 2 - 10);
        ctx.font = '20px Arial';
        ctx.fillText('Type the details on the left side, then press "Make Card"', canvas.width / 2, canvas.height / 2 + 30);
    }

    function drawCard() {
        var biz = bizInput.value.trim();
        var name = nameInput.value.trim();
        var title = titleInput.value.trim();
        var phone = phoneInput.value.trim();
        var email = emailInput.value.trim();
        var addr = addrInput.value.trim();
        var t = THEMES[themeSelect.value] || THEMES.navy;

        var ctx = canvas.getContext('2d');
        var W = canvas.width, H = canvas.height;

        ctx.fillStyle = t.bg;
        ctx.fillRect(0, 0, W, H);

        ctx.fillStyle = t.band;
        ctx.fillRect(0, 0, 260, H);
        ctx.fillStyle = t.accent;
        ctx.fillRect(260, 0, 14, H);

        ctx.fillStyle = t.band;
        ctx.fillRect(274, H - 90, W - 274, 90);

        ctx.fillStyle = '#ffffff';
        ctx.textAlign = 'left';
        ctx.font = 'bold 40px Arial';
        var bizText = biz || 'Your Business Name';
        if (ctx.measureText(bizText).width > W - 274 - 80) {
            ctx.font = 'bold 32px Arial';
        }
        ctx.fillText(bizText, 320, 110);

        ctx.fillStyle = t.accent;
        ctx.fillRect(320, 140, 120, 6);

        ctx.fillStyle = t.name;
        ctx.font = 'bold 52px Arial';
        ctx.fillText(name || 'Your Name', 320, 240);

        ctx.fillStyle = t.sub;
        ctx.font = '32px Arial';
        ctx.fillText(title || 'Your Title', 320, 295);

        ctx.font = '30px Arial';
        ctx.fillStyle = '#374151';
        var y = 370;
        var lines = [];
        if (phone) lines.push(['Phone:  ', phone]);
        if (email) lines.push(['Email:  ', email]);
        if (addr) lines.push(['Address: ', addr]);
        if (lines.length === 0) lines.push(['Phone:  ', '03XX-XXXXXXX']);
        lines.forEach(function (ln) {
            ctx.fillStyle = t.band;
            ctx.font = 'bold 30px Arial';
            ctx.fillText(ln[0], 320, y);
            var lw = ctx.measureText(ln[0]).width;
            ctx.fillStyle = '#374151';
            ctx.font = '30px Arial';
            var val = ln[1];
            var maxW = W - 320 - lw - 40;
            while (ctx.measureText(val).width > maxW && val.length > 4) {
                val = val.slice(0, -2);
            }
            ctx.fillText(val, 320 + lw, y);
            y += 52;
        });

        ctx.fillStyle = '#ffffff';
        ctx.font = 'bold 34px Arial';
        ctx.textAlign = 'center';
        ctx.fillText('BUSINESS CARD', (274 + W) / 2, H - 32);
        ctx.textAlign = 'left';

        ctx.fillStyle = t.accent;
        ctx.beginPath();
        ctx.arc(130, 120, 62, 0, Math.PI * 2);
        ctx.fill();
        ctx.fillStyle = t.band;
        ctx.font = 'bold 56px Arial';
        ctx.textAlign = 'center';
        var initial = (biz || name || 'B').trim().charAt(0).toUpperCase();
        ctx.fillText(initial, 130, 142);
        ctx.textAlign = 'left';

        ctx.strokeStyle = '#e5e7eb';
        ctx.lineWidth = 3;
        ctx.strokeRect(1.5, 1.5, W - 3, H - 3);
    }

    goBtn.addEventListener('click', function () {
        hideError();
        if (!nameInput.value.trim() && !bizInput.value.trim()) {
            showError('Please type at least your name or your business name.');
            return;
        }
        drawCard();
        cardReady = true;
        downloadBtn.disabled = false;
        resultInfo.textContent = 'Your card is ready — download it and get it printed at a print shop.';
        results.classList.remove('d-none');
    });

    downloadBtn.addEventListener('click', function () {
        if (!cardReady) return;
        var a = document.createElement('a');
        a.href = canvas.toDataURL('image/png');
        a.download = 'visiting-card.png';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    });

    [bizInput, nameInput, titleInput, phoneInput, emailInput, addrInput].forEach(function (el) {
        el.addEventListener('input', function () {
            if (cardReady) drawCard();
        });
    });
    themeSelect.addEventListener('change', function () {
        if (cardReady) drawCard();
    });

    drawPlaceholder();
})();
</script>
@endsection
