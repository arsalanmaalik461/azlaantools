@extends('layouts.app')
@section('title', 'Spin the Wheel Decision Maker — Azlaan Tools')
@section('meta_description', 'Spin a colorful wheel to randomly pick names or choices. Free online wheel spinner decision maker — no signup.')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Spin the Wheel Decision Maker</h1>
            <p class="lead text-muted">Make hard decisions easy — write names or choices, spin the wheel, and let luck decide!</p>

            <div class="row g-4">
                <div class="col-12 col-md-5">
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <label for="choices" class="form-label fw-semibold">Choices (one per line)</label>
                            <textarea class="form-control" id="choices" rows="8" placeholder="Ali&#10;Sara&#10;Ahmed&#10;Fatima&#10;Pizza&#10;Biryani"></textarea>
                            <div class="form-text mb-3">At least 2, at most 20 choices.</div>
                            <div class="d-grid gap-2">
                                <button type="button" class="btn btn-primary" id="goBtn">Update Wheel</button>
                                <button type="button" class="btn btn-outline-secondary" id="shuffleBtn">Shuffle</button>
                                <button type="button" class="btn btn-outline-danger" id="clearBtn">Clear</button>
                            </div>
                            <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>
                            <div id="results" class="d-none mt-3">
                                <div class="alert alert-success mb-0">
                                    <div class="small text-muted">Winner</div>
                                    <div class="fs-3 fw-bold" id="winnerName">—</div>
                                    <button type="button" class="btn btn-sm btn-outline-success mt-2" id="removeBtn">Remove winner and spin again</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-7 text-center">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="position-relative d-inline-block">
                                <div style="position:absolute; top:-8px; left:50%; transform:translateX(-50%); width:0; height:0; border-left:14px solid transparent; border-right:14px solid transparent; border-top:26px solid #dc3545; z-index:2;"></div>
                                <canvas id="wheel" width="340" height="340" style="max-width:100%; height:auto;"></canvas>
                            </div>
                            <div class="mt-3">
                                <button type="button" class="btn btn-success btn-lg px-5" id="spinBtn">SPIN</button>
                            </div>
                            <p class="text-muted small mt-2 mb-0" id="wheelHint">First write choices, then press "Update Wheel".</p>
                        </div>
                    </div>
                </div>
            </div>

            <h2 class="mt-4">How to use</h2>
            <ol>
                <li>Write your choices on the left side (one per line).</li>
                <li>Press <strong>Update Wheel</strong> — the wheel will be built.</li>
                <li>Press <strong>SPIN</strong> and see where luck stops.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var COLORS = ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40', '#7ED321', '#F53240', '#50E3C2', '#B8E986',
                  '#F8E71C', '#9013FE', '#417505', '#D0021B', '#8B572A', '#4A90D9', '#BD10E0', '#F5A623', '#7B7B7B', '#00B8A9'];

    var canvas = document.getElementById('wheel');
    var ctx = canvas.getContext('2d');
    var choicesEl = document.getElementById('choices');
    var goBtn = document.getElementById('goBtn');
    var spinBtn = document.getElementById('spinBtn');
    var shuffleBtn = document.getElementById('shuffleBtn');
    var clearBtn = document.getElementById('clearBtn');
    var removeBtn = document.getElementById('removeBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var winnerName = document.getElementById('winnerName');
    var wheelHint = document.getElementById('wheelHint');

    var items = [];
    var angle = 0;      // current rotation (radians)
    var spinning = false;

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function readChoices() {
        return choicesEl.value.split('\n').map(function (s) { return s.trim(); }).filter(function (s) { return s.length > 0; });
    }

    function drawWheel() {
        var w = canvas.width, h = canvas.height, cx = w / 2, cy = h / 2, r = w / 2 - 6;
        ctx.clearRect(0, 0, w, h);
        if (!items.length) {
            ctx.fillStyle = '#e9ecef';
            ctx.beginPath(); ctx.arc(cx, cy, r, 0, Math.PI * 2); ctx.fill();
            ctx.fillStyle = '#6c757d';
            ctx.font = '16px sans-serif';
            ctx.textAlign = 'center'; ctx.textBaseline = 'middle';
            ctx.fillText('Write choices', cx, cy);
            return;
        }
        var seg = Math.PI * 2 / items.length;
        for (var i = 0; i < items.length; i++) {
            var a0 = angle + i * seg, a1 = a0 + seg;
            ctx.fillStyle = COLORS[i % COLORS.length];
            ctx.beginPath();
            ctx.moveTo(cx, cy);
            ctx.arc(cx, cy, r, a0, a1);
            ctx.closePath();
            ctx.fill();
            ctx.strokeStyle = '#ffffff';
            ctx.lineWidth = 2;
            ctx.stroke();
            // label
            ctx.save();
            ctx.translate(cx, cy);
            ctx.rotate(a0 + seg / 2);
            ctx.textAlign = 'right';
            ctx.textBaseline = 'middle';
            ctx.fillStyle = '#ffffff';
            var fs = items.length > 12 ? 11 : 14;
            ctx.font = 'bold ' + fs + 'px sans-serif';
            var label = items[i].length > 18 ? items[i].slice(0, 17) + '…' : items[i];
            ctx.fillText(label, r - 12, 0);
            ctx.restore();
        }
        // center hub
        ctx.fillStyle = '#ffffff';
        ctx.beginPath(); ctx.arc(cx, cy, 26, 0, Math.PI * 2); ctx.fill();
        ctx.strokeStyle = '#dee2e6'; ctx.lineWidth = 2; ctx.stroke();
        ctx.fillStyle = '#495057';
        ctx.font = 'bold 13px sans-serif';
        ctx.textAlign = 'center'; ctx.textBaseline = 'middle';
        ctx.fillText('SPIN', cx, cy);
    }

    function updateWheel() {
        hideError();
        results.classList.add('d-none');
        var c = readChoices();
        if (c.length < 2) { showError('Please write at least 2 choices.'); return false; }
        if (c.length > 20) { showError('Keep at most 20 choices.'); return false; }
        items = c;
        wheelHint.textContent = items.length + ' choices ready — press SPIN!';
        drawWheel();
        return true;
    }

    // pointer is at top (-PI/2). Winner = segment containing that angle.
    function winnerIndex() {
        var seg = Math.PI * 2 / items.length;
        var pointer = -Math.PI / 2;
        var rel = (pointer - angle) % (Math.PI * 2);
        if (rel < 0) { rel += Math.PI * 2; }
        return Math.floor(rel / seg) % items.length;
    }

    function spin() {
        hideError();
        if (spinning) { return; }
        if (!items.length) { if (!updateWheel()) { return; } }
        spinning = true;
        spinBtn.disabled = true;
        results.classList.add('d-none');
        var startAngle = angle;
        var extraSpins = 5 + Math.random() * 3; // full rotations
        var target = startAngle + extraSpins * Math.PI * 2 + Math.random() * Math.PI * 2;
        var dur = 4000 + Math.random() * 2000;
        var t0 = null;
        function frame(ts) {
            if (!t0) { t0 = ts; }
            var t = Math.min(1, (ts - t0) / dur);
            var ease = 1 - Math.pow(1 - t, 4); // easeOutQuart
            angle = startAngle + (target - startAngle) * ease;
            drawWheel();
            if (t < 1) {
                requestAnimationFrame(frame);
            } else {
                spinning = false;
                spinBtn.disabled = false;
                var w = items[winnerIndex()];
                winnerName.textContent = w;
                results.classList.remove('d-none');
            }
        }
        requestAnimationFrame(frame);
    }

    goBtn.addEventListener('click', updateWheel);
    spinBtn.addEventListener('click', spin);
    canvas.addEventListener('click', spin);

    shuffleBtn.addEventListener('click', function () {
        var c = readChoices();
        if (c.length < 2) { showError('Please write at least 2 choices first.'); return; }
        for (var i = c.length - 1; i > 0; i--) {
            var j = Math.floor(Math.random() * (i + 1));
            var t = c[i]; c[i] = c[j]; c[j] = t;
        }
        choicesEl.value = c.join('\n');
        updateWheel();
    });

    clearBtn.addEventListener('click', function () {
        choicesEl.value = '';
        items = [];
        results.classList.add('d-none');
        hideError();
        wheelHint.textContent = 'First write choices, then press "Update Wheel".';
        drawWheel();
    });

    removeBtn.addEventListener('click', function () {
        var w = winnerName.textContent;
        var c = readChoices().filter(function (s) { return s !== w; });
        choicesEl.value = c.join('\n');
        if (updateWheel()) { spin(); }
    });

    drawWheel();
})();
</script>
@endsection
