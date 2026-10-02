@extends('layouts.app')

@section('title', 'Spin Wheel Random Picker - Azlaan Tools')
@section('meta_description', 'Write names and spin the wheel to pick a random winner — free online spin wheel picker.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Spin Wheel Random Picker</h1>
            <p class="lead text-muted">Write names, spin the wheel — luck will decide! Great for class, giveaways or games.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="namesInput" class="form-label fw-semibold">Write names (one per line)</label>
                        <textarea class="form-control" id="namesInput" rows="6" placeholder="Ahmed&#10;Fatima&#10;Ali&#10;Sara"></textarea>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <button type="button" class="btn btn-primary flex-fill" id="goBtn">Build Wheel</button>
                        <button type="button" class="btn btn-success flex-fill d-none" id="spinBtn">🎡 SPIN!</button>
                    </div>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4 text-center">
                        <div style="position:relative;display:inline-block;max-width:100%;">
                            <div style="position:absolute;top:-8px;left:50%;transform:translateX(-50%);z-index:2;width:0;height:0;border-left:14px solid transparent;border-right:14px solid transparent;border-top:24px solid #dc3545;"></div>
                            <canvas id="wheel" width="520" height="520" style="max-width:100%;height:auto;"></canvas>
                        </div>
                        <div id="winnerBox" class="alert alert-success mt-3 d-none">
                            <h4 class="mb-1">🎉 Winner!</h4>
                            <div class="h3 mb-0" id="winnerName"></div>
                        </div>
                        <div class="d-flex gap-2 mt-3 flex-wrap justify-content-center">
                            <button type="button" class="btn btn-outline-secondary" id="removeBtn">Remove winner and spin again</button>
                            <button type="button" class="btn btn-outline-secondary" id="shuffleBtn">Shuffle names</button>
                        </div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Write names — one per line (at least 2).</li>
                <li>Press <strong>Build Wheel</strong>.</li>
                <li>Press <strong>SPIN</strong> — the winner name will appear when the wheel stops.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var goBtn = document.getElementById('goBtn');
    var spinBtn = document.getElementById('spinBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var wheel = document.getElementById('wheel');
    var ctx = wheel.getContext('2d');
    var winnerBox = document.getElementById('winnerBox');
    var winnerName = document.getElementById('winnerName');

    var names = [];
    var angle = 0, spinning = false;
    var COLORS = ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#6f42c1', '#fd7e14', '#20c997', '#e83e8c', '#0dcaf0', '#6c757d'];

    function showError(msg) { errorBox.textContent = msg; errorBox.classList.remove('d-none'); }
    function hideError() { errorBox.classList.add('d-none'); errorBox.textContent = ''; }

    function drawWheel() {
        var n = names.length;
        if (!n) return;
        var R = wheel.width / 2, cx = R, cy = R, arc = (Math.PI * 2) / n;
        ctx.clearRect(0, 0, wheel.width, wheel.height);
        for (var i = 0; i < n; i++) {
            var a0 = angle + i * arc, a1 = a0 + arc;
            ctx.beginPath();
            ctx.moveTo(cx, cy);
            ctx.arc(cx, cy, R - 4, a0, a1);
            ctx.closePath();
            ctx.fillStyle = COLORS[i % COLORS.length];
            ctx.fill();
            ctx.strokeStyle = '#fff'; ctx.lineWidth = 3; ctx.stroke();
            // label
            ctx.save();
            ctx.translate(cx, cy);
            ctx.rotate(a0 + arc / 2);
            ctx.textAlign = 'right';
            ctx.fillStyle = '#fff';
            var fs = Math.max(13, Math.min(24, Math.floor(200 / n) + 10));
            ctx.font = 'bold ' + fs + 'px sans-serif';
            ctx.shadowColor = 'rgba(0,0,0,.4)'; ctx.shadowBlur = 4;
            var label = names[i].length > 14 ? names[i].slice(0, 13) + '…' : names[i];
            ctx.fillText(label, R - 18, fs / 3);
            ctx.restore();
        }
        // hub
        ctx.beginPath(); ctx.arc(cx, cy, 34, 0, Math.PI * 2);
        ctx.fillStyle = '#212529'; ctx.fill();
        ctx.fillStyle = '#fff'; ctx.font = 'bold 16px sans-serif'; ctx.textAlign = 'center';
        ctx.fillText('SPIN', cx, cy + 6);
    }

    goBtn.addEventListener('click', function () {
        hideError();
        winnerBox.classList.add('d-none');
        names = document.getElementById('namesInput').value.split('\n')
            .map(function (s) { return s.trim(); })
            .filter(function (s) { return s.length > 0; });
        // dedupe
        var seen = {};
        names = names.filter(function (s) { var k = s.toLowerCase(); if (seen[k]) return false; seen[k] = 1; return true; });
        if (names.length < 2) { showError('Please enter at least 2 names.'); return; }
        if (names.length > 40) { showError('Maximum 40 names.'); return; }
        angle = 0;
        drawWheel();
        results.classList.remove('d-none');
        spinBtn.classList.remove('d-none');
        results.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    });

    spinBtn.addEventListener('click', function () {
        hideError();
        if (spinning || names.length < 2) return;
        spinning = true;
        winnerBox.classList.add('d-none');
        spinBtn.disabled = true;
        var n = names.length, arc = (Math.PI * 2) / n;
        var targetIndex = Math.floor(Math.random() * n);
        // pointer at top (-PI/2). Final angle such that segment targetIndex center lands under pointer.
        var pointerAngle = -Math.PI / 2;
        var currentMod = ((angle % (Math.PI * 2)) + Math.PI * 2) % (Math.PI * 2);
        var desiredMod = ((pointerAngle - (targetIndex * arc + arc / 2)) % (Math.PI * 2) + Math.PI * 2) % (Math.PI * 2);
        var delta = (desiredMod - currentMod + Math.PI * 2) % (Math.PI * 2);
        var totalRotation = Math.PI * 2 * (5 + Math.floor(Math.random() * 3)) + delta;
        var startAngle = angle, start = null, duration = 4200 + Math.random() * 1500;

        function easeOut(t) { return 1 - Math.pow(1 - t, 4); }
        function frame(ts) {
            if (!start) start = ts;
            var t = Math.min(1, (ts - start) / duration);
            angle = startAngle + totalRotation * easeOut(t);
            drawWheel();
            if (t < 1) { requestAnimationFrame(frame); }
            else {
                spinning = false;
                spinBtn.disabled = false;
                winnerName.textContent = names[targetIndex];
                winnerBox.classList.remove('d-none');
                winnerBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        }
        requestAnimationFrame(frame);
    });

    document.getElementById('removeBtn').addEventListener('click', function () {
        hideError();
        var w = winnerName.textContent;
        if (!w || winnerBox.classList.contains('d-none')) { showError('First spin to pick a winner.'); return; }
        names = names.filter(function (s) { return s !== w; });
        if (names.length < 2) { showError('Fewer than 2 names remain — please add new names.'); return; }
        winnerBox.classList.add('d-none');
        drawWheel();
    });

    document.getElementById('shuffleBtn').addEventListener('click', function () {
        hideError();
        if (!names.length) { showError('Please build the wheel first.'); return; }
        for (var i = names.length - 1; i > 0; i--) {
            var j = Math.floor(Math.random() * (i + 1));
            var tmp = names[i]; names[i] = names[j]; names[j] = tmp;
        }
        winnerBox.classList.add('d-none');
        drawWheel();
    });

    window.addEventListener('resize', function () { if (names.length) drawWheel(); });
})();
</script>
@endsection
