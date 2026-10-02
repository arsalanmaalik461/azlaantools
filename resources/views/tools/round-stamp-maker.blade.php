@extends('layouts.app')

@section('title', 'Round Stamp Maker - Azlaan Tools')
@section('meta_description', 'Make a round stamp online free. Create and download a stamp image with your name and city.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Round Stamp Maker</h1>
            <p class="lead text-muted">Make a round stamp for your shop or business — type the name, city and center mark, then download the stamp image.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="topText" class="form-label fw-semibold">Top text (business name)</label>
                                <input type="text" class="form-control" id="topText" value="AZLAAN ELECTRIC" maxlength="30" autocomplete="off">
                            </div>
                            <div class="mb-3">
                                <label for="bottomText" class="form-label fw-semibold">Bottom text (city)</label>
                                <input type="text" class="form-control" id="bottomText" value="FAISALABAD" maxlength="30" autocomplete="off">
                            </div>
                            <div class="mb-3">
                                <label for="centerText" class="form-label fw-semibold">Center mark / text</label>
                                <input type="text" class="form-control" id="centerText" value="★" maxlength="12" autocomplete="off">
                                <div class="form-text">Example: ★, ✓, or "EST 2020".</div>
                            </div>
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <label for="colorSelect" class="form-label fw-semibold">Color</label>
                                    <select class="form-select" id="colorSelect">
                                        <option value="#1d4ed8">Blue</option>
                                        <option value="#b91c1c">Red</option>
                                        <option value="#15803d">Green</option>
                                        <option value="#111111">Black</option>
                                        <option value="#7c3aed">Purple</option>
                                    </select>
                                </div>
                                <div class="col-6 mb-3">
                                    <label for="ringSelect" class="form-label fw-semibold">Ring style</label>
                                    <select class="form-select" id="ringSelect">
                                        <option value="double">Double ring</option>
                                        <option value="single">Single ring</option>
                                    </select>
                                </div>
                            </div>
                            <button type="button" class="btn btn-primary w-100" id="goBtn">Create / Update Stamp</button>
                            <button type="button" class="btn btn-success w-100 mt-2" id="downloadBtn">Download Stamp (PNG)</button>
                            <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>
                        </div>
                        <div class="col-md-6 text-center">
                            <div class="border rounded bg-white p-2 d-inline-block">
                                <canvas id="stampCanvas" width="640" height="640" style="max-width:100%; height:auto;"></canvas>
                            </div>
                            <p class="text-muted small mt-2">Live preview — the stamp updates instantly as you change anything.</p>
                        </div>
                    </div>
                    <div id="results" class="d-none mt-3">
                        <div class="alert alert-success mb-0" id="resultText"></div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Type your business name at the top and the city at the bottom.</li>
                <li>Type a mark (★) or short text in the center.</li>
                <li>Choose the color and ring style — watch the preview update.</li>
                <li>Click "Download Stamp" — you get a PNG image you can give for printing or making a rubber stamp.</li>
            </ol>
            <p class="text-muted small">Note: This is a digital stamp image. To make a real rubber stamp, give this image to a stamp maker.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var topText = document.getElementById('topText');
    var bottomText = document.getElementById('bottomText');
    var centerText = document.getElementById('centerText');
    var colorSelect = document.getElementById('colorSelect');
    var ringSelect = document.getElementById('ringSelect');
    var goBtn = document.getElementById('goBtn');
    var downloadBtn = document.getElementById('downloadBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var resultText = document.getElementById('resultText');
    var canvas = document.getElementById('stampCanvas');
    var ctx = canvas.getContext('2d');

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }

    function arcText(text, cx, cy, radius, centerAngle, spread, flip) {
        var n = text.length;
        for (var i = 0; i < n; i++) {
            var t = (n === 1) ? 0.5 : i / (n - 1);
            var a = centerAngle - spread / 2 + spread * t;
            var x = cx + radius * Math.cos(a);
            var y = cy + radius * Math.sin(a);
            ctx.save();
            ctx.translate(x, y);
            ctx.rotate(a + (flip ? -Math.PI / 2 : Math.PI / 2));
            ctx.fillText(text[i], 0, 0);
            ctx.restore();
        }
    }

    function drawStamp() {
        hideError();
        var top = topText.value.trim().toUpperCase();
        var bottom = bottomText.value.trim().toUpperCase();
        var center = centerText.value.trim();
        if (!top && !bottom && !center) {
            showError('Please enter at least one text.');
            return;
        }
        var color = colorSelect.value;
        var cx = 320, cy = 320;
        ctx.clearRect(0, 0, 640, 640);
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, 640, 640);
        ctx.strokeStyle = color;
        ctx.fillStyle = color;
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';

        ctx.lineWidth = 14;
        ctx.beginPath(); ctx.arc(cx, cy, 296, 0, Math.PI * 2); ctx.stroke();
        if (ringSelect.value === 'double') {
            ctx.lineWidth = 6;
            ctx.beginPath(); ctx.arc(cx, cy, 268, 0, Math.PI * 2); ctx.stroke();
        }
        ctx.lineWidth = 5;
        ctx.beginPath(); ctx.arc(cx, cy, 168, 0, Math.PI * 2); ctx.stroke();

        if (top) {
            var fsTop = Math.max(30, Math.min(64, Math.floor(1500 / top.length)));
            ctx.font = 'bold ' + fsTop + 'px Arial, sans-serif';
            var spreadTop = Math.min(Math.PI * 1.1, top.length * 0.16);
            arcText(top, cx, cy, 218, -Math.PI / 2, spreadTop, false);
        }
        if (bottom) {
            var fsBot = Math.max(30, Math.min(64, Math.floor(1500 / bottom.length)));
            ctx.font = 'bold ' + fsBot + 'px Arial, sans-serif';
            var spreadBot = Math.min(Math.PI * 1.1, bottom.length * 0.16);
            arcText(bottom, cx, cy, 218, Math.PI / 2, spreadBot, true);
        }
        ctx.font = 'bold 44px Arial, sans-serif';
        ctx.fillText('•', cx - 196, cy);
        ctx.fillText('•', cx + 196, cy);

        if (center) {
            var fsC = center.length <= 2 ? 120 : Math.max(44, Math.floor(560 / center.length));
            ctx.font = 'bold ' + fsC + 'px Arial, sans-serif';
            ctx.fillText(center, cx, cy);
        }
        resultText.textContent = 'Stamp is ready. Download it or make more changes.';
        results.classList.remove('d-none');
    }

    [topText, bottomText, centerText].forEach(function (el) {
        el.addEventListener('input', drawStamp);
    });
    colorSelect.addEventListener('change', drawStamp);
    ringSelect.addEventListener('change', drawStamp);

    goBtn.addEventListener('click', drawStamp);

    downloadBtn.addEventListener('click', function () {
        var a = document.createElement('a');
        a.href = canvas.toDataURL('image/png');
        a.download = 'round-stamp.png';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    });

    drawStamp();
})();
</script>
@endsection
