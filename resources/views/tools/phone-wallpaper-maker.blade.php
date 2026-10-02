@extends('layouts.app')

@section('title', 'Phone Wallpaper Maker - Azlaan Tools')
@section('meta_description', 'Create beautiful gradient wallpapers for mobile and desktop free online with one click.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Phone Wallpaper Maker</h1>
            <p class="lead text-muted">Make beautiful gradient wallpapers for your mobile or desktop — pick the colors and design yourself, then download for free. No signup needed.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label for="wpSize" class="form-label fw-semibold">Wallpaper Size</label>
                            <select class="form-select" id="wpSize">
                                <option value="1080x1920">Mobile (1080 x 1920)</option>
                                <option value="1920x1080">Desktop (1920 x 1080)</option>
                                <option value="1080x1080">Square (1080 x 1080)</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="wpStyle" class="form-label fw-semibold">Design Style</label>
                            <select class="form-select" id="wpStyle">
                                <option value="gradient">Smooth Gradient</option>
                                <option value="diagonal">Diagonal Stripes</option>
                                <option value="waves">Soft Waves</option>
                                <option value="bubbles">Floating Bubbles</option>
                                <option value="sunset">Sunset Glow</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="wpPalette" class="form-label fw-semibold">Color Theme</label>
                            <select class="form-select" id="wpPalette">
                                <option value="ocean">Ocean Blue</option>
                                <option value="sunsetc">Sunset Orange</option>
                                <option value="forest">Forest Green</option>
                                <option value="purple">Royal Purple</option>
                                <option value="rose">Rose Pink</option>
                                <option value="mono">Midnight Dark</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="wpText" class="form-label fw-semibold">Text on Wallpaper (optional)</label>
                            <input type="text" class="form-control" id="wpText" maxlength="30" placeholder="e.g. Stay Positive">
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-3 flex-wrap">
                        <button type="button" class="btn btn-secondary flex-fill" id="shuffleBtn">Shuffle Design</button>
                        <button type="button" class="btn btn-success flex-fill" id="downloadBtn">Download PNG</button>
                    </div>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="mt-4">
                        <p class="fw-semibold mb-2">Preview</p>
                        <div class="text-center bg-light rounded p-3">
                            <canvas id="wpCanvas" class="img-fluid border rounded" style="max-height:480px;"></canvas>
                        </div>
                        <small class="text-muted d-block mt-2">Every click creates a new unique design. The download button gives you a full-resolution PNG.</small>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Choose the wallpaper size (Mobile, Desktop or Square).</li>
                <li>Select the design style and color theme — if you want, type your own text too.</li>
                <li>Click Shuffle Design to see different variations.</li>
                <li>If you like it, press Download PNG and set it on your phone.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var sizeSel = document.getElementById('wpSize');
    var styleSel = document.getElementById('wpStyle');
    var palSel = document.getElementById('wpPalette');
    var textInput = document.getElementById('wpText');
    var shuffleBtn = document.getElementById('shuffleBtn');
    var downloadBtn = document.getElementById('downloadBtn');
    var errorBox = document.getElementById('errorBox');
    var canvas = document.getElementById('wpCanvas');
    var ctx = canvas.getContext('2d');
    var seed = Date.now();

    var palettes = {
        ocean:   ['#0ea5e9', '#0369a1', '#164e63', '#7dd3fc'],
        sunsetc: ['#f97316', '#dc2626', '#7c2d12', '#fdba74'],
        forest:  ['#22c55e', '#15803d', '#14532d', '#86efac'],
        purple:  ['#a855f7', '#7c3aed', '#3b0764', '#d8b4fe'],
        rose:    ['#f43f5e', '#be123c', '#4c0519', '#fda4af'],
        mono:    ['#64748b', '#1e293b', '#020617', '#94a3b8']
    };

    function rand() {
        seed = (seed * 1103515245 + 12345) % 2147483648;
        return seed / 2147483648;
    }
    function pick(arr) { return arr[Math.floor(rand() * arr.length)]; }

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }

    function drawGradientBase(w, h, cols) {
        var g = ctx.createLinearGradient(0, 0, w, h);
        g.addColorStop(0, cols[0]);
        g.addColorStop(0.55, cols[1]);
        g.addColorStop(1, cols[2]);
        ctx.fillStyle = g;
        ctx.fillRect(0, 0, w, h);
    }

    function drawDesign() {
        hideError();
        var dims = sizeSel.value.split('x');
        var w = parseInt(dims[0], 10), h = parseInt(dims[1], 10);
        var cols = palettes[palSel.value] || palettes.ocean;
        var style = styleSel.value;
        canvas.width = w; canvas.height = h;
        drawGradientBase(w, h, cols);

        if (style === 'diagonal') {
            ctx.save();
            ctx.globalAlpha = 0.35;
            for (var i = -h; i < w + h; i += Math.floor(w / 9)) {
                ctx.strokeStyle = pick(cols);
                ctx.lineWidth = Math.floor(w / 18) + 6;
                ctx.beginPath();
                ctx.moveTo(i, 0); ctx.lineTo(i + h, h);
                ctx.stroke();
            }
            ctx.restore();
        } else if (style === 'waves') {
            ctx.save();
            ctx.globalAlpha = 0.45;
            for (var l = 0; l < 5; l++) {
                ctx.strokeStyle = pick(cols);
                ctx.lineWidth = 18 + rand() * 40;
                ctx.beginPath();
                var baseY = h * (0.2 + 0.15 * l);
                for (var x = 0; x <= w; x += 8) {
                    var y = baseY + Math.sin(x / 130 + l * 1.7) * (40 + rand() * 30);
                    if (x === 0) ctx.moveTo(x, y); else ctx.lineTo(x, y);
                }
                ctx.stroke();
            }
            ctx.restore();
        } else if (style === 'bubbles') {
            ctx.save();
            for (var b = 0; b < 40; b++) {
                var bx = rand() * w, by = rand() * h, r = 8 + rand() * (w / 10);
                var rg = ctx.createRadialGradient(bx - r / 3, by - r / 3, r / 8, bx, by, r);
                var c = pick(cols);
                rg.addColorStop(0, 'rgba(255,255,255,0.85)');
                rg.addColorStop(0.4, c + 'aa');
                rg.addColorStop(1, c + '22');
                ctx.fillStyle = rg;
                ctx.beginPath(); ctx.arc(bx, by, r, 0, Math.PI * 2); ctx.fill();
            }
            ctx.restore();
        } else if (style === 'sunset') {
            var sunX = w * (0.3 + rand() * 0.4), sunY = h * (0.25 + rand() * 0.3);
            var sr = Math.min(w, h) * (0.18 + rand() * 0.12);
            var sg = ctx.createRadialGradient(sunX, sunY, sr / 6, sunX, sunY, sr);
            sg.addColorStop(0, 'rgba(255,244,220,0.98)');
            sg.addColorStop(0.5, 'rgba(255,200,130,0.75)');
            sg.addColorStop(1, 'rgba(255,180,120,0)');
            ctx.fillStyle = sg;
            ctx.beginPath(); ctx.arc(sunX, sunY, sr, 0, Math.PI * 2); ctx.fill();
            ctx.save(); ctx.globalAlpha = 0.25;
            for (var s = 0; s < 26; s++) {
                ctx.fillStyle = '#ffffff';
                var sx = rand() * w, sy = rand() * h * 0.6, sw = 1 + rand() * 3;
                ctx.beginPath(); ctx.arc(sx, sy, sw, 0, Math.PI * 2); ctx.fill();
            }
            ctx.restore();
        }

        var txt = textInput.value.trim();
        if (txt) {
            ctx.save();
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            var fs = Math.floor(Math.min(w, h) / 9);
            ctx.font = '600 ' + fs + 'px system-ui, sans-serif';
            ctx.shadowColor = 'rgba(0,0,0,0.45)';
            ctx.shadowBlur = 18;
            ctx.fillStyle = '#ffffff';
            var maxW = w * 0.86;
            var words = txt.split(' ');
            var lines = [''];
            for (var k = 0; k < words.length; k++) {
                var trial = (lines[lines.length - 1] + ' ' + words[k]).trim();
                if (ctx.measureText(trial).width > maxW && lines[lines.length - 1]) {
                    lines.push(words[k]);
                } else {
                    lines[lines.length - 1] = trial;
                }
            }
            var startY = h / 2 - ((lines.length - 1) * fs * 0.65);
            for (var m = 0; m < lines.length; m++) {
                ctx.fillText(lines[m], w / 2, startY + m * fs * 1.3);
            }
            ctx.restore();
        }
    }

    shuffleBtn.addEventListener('click', function () {
        seed = Date.now() + Math.floor(Math.random() * 99999);
        drawDesign();
    });
    sizeSel.addEventListener('change', drawDesign);
    styleSel.addEventListener('change', drawDesign);
    palSel.addEventListener('change', drawDesign);
    textInput.addEventListener('input', drawDesign);

    downloadBtn.addEventListener('click', function () {
        hideError();
        canvas.toBlob(function (blob) {
            if (!blob) { showError('There was a problem making the image. Please try again.'); return; }
            var url = URL.createObjectURL(blob);
            var a = document.createElement('a');
            a.href = url;
            a.download = 'wallpaper-' + sizeSel.value + '.png';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            setTimeout(function () { URL.revokeObjectURL(url); }, 4000);
        }, 'image/png');
    });

    drawDesign();
})();
</script>
@endsection
