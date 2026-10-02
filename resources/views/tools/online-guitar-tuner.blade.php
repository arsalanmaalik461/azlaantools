@extends('layouts.app')

@section('title', 'Online Guitar Tuner - Azlaan Tools')
@section('meta_description', 'Tune your guitar with your mic for free — live pitch detection with a string-wise needle display, right in the browser.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Online Guitar Tuner</h1>
            <p class="lead text-muted">Turn on your mic and play any string — this tool will detect it and tell you if the string is in tune. Free, no download.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Select a string (auto-detect works too)</label>
                        <div class="d-flex flex-wrap gap-2" id="stringBtns" role="group" aria-label="Guitar strings">
                            <button type="button" class="btn btn-outline-primary string-btn active" data-string="0">E2 (low)</button>
                            <button type="button" class="btn btn-outline-primary string-btn" data-string="1">A2</button>
                            <button type="button" class="btn btn-outline-primary string-btn" data-string="2">D3</button>
                            <button type="button" class="btn btn-outline-primary string-btn" data-string="3">G3</button>
                            <button type="button" class="btn btn-outline-primary string-btn" data-string="4">B3</button>
                            <button type="button" class="btn btn-outline-primary string-btn" data-string="5">E4 (high)</button>
                        </div>
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" id="autoMode" checked>
                            <label class="form-check-label" for="autoMode">Auto-detect string (detects the string you play)</label>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Start Mic</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="text-center">
                            <div class="display-4 fw-bold" id="noteName">—</div>
                            <div class="fs-5 text-muted"><span id="freqVal">0.0</span> Hz (target: <span id="targetVal">—</span> Hz)</div>
                            <div class="my-3 position-relative" style="height: 60px;">
                                <div class="border rounded" style="height: 14px; margin-top: 23px; background: linear-gradient(to right, #dc3545 0%, #ffc107 40%, #198754 50%, #ffc107 60%, #dc3545 100%);"></div>
                                <div id="needle" style="position:absolute; top:8px; left:50%; width:3px; height:44px; background:#6c757d; transition: left 0.08s linear;"></div>
                                <div style="position:absolute; top:32px; left:50%; width:2px; height:12px; background:#6c757d; transform:translateX(-1px);"></div>
                            </div>
                            <div class="fs-4 fw-semibold" id="tuneStatus"><span class="text-muted">Play a string…</span></div>
                            <small class="text-muted">Needle in the middle (green) = perfect tune. Left = too low (tighten), right = too high (loosen).</small>
                        </div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Press <strong>Start Mic</strong> and allow mic access in the browser (works over HTTPS).</li>
                <li>Play any guitar string — the needle shows if the string is too high or too low.</li>
                <li>When the needle is in the middle on green, the string is perfectly tuned. Works better in a quiet place.</li>
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
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');

    var STRINGS = [
        { name: 'E2', freq: 82.41 },
        { name: 'A2', freq: 110.00 },
        { name: 'D3', freq: 146.83 },
        { name: 'G3', freq: 196.00 },
        { name: 'B3', freq: 246.94 },
        { name: 'E4', freq: 329.63 }
    ];
    var selected = 0;
    var audioCtx = null, analyser = null, rafId = null, running = false;
    var noteName = document.getElementById('noteName');
    var freqVal = document.getElementById('freqVal');
    var targetVal = document.getElementById('targetVal');
    var needle = document.getElementById('needle');
    var tuneStatus = document.getElementById('tuneStatus');
    var autoMode = document.getElementById('autoMode');

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }

    var btns = document.querySelectorAll('.string-btn');
    btns.forEach(function (b) {
        b.addEventListener('click', function () {
            btns.forEach(function (x) { x.classList.remove('active'); });
            b.classList.add('active');
            selected = parseInt(b.getAttribute('data-string'), 10);
            updateTarget();
        });
    });

    function updateTarget() {
        targetVal.textContent = STRINGS[selected].freq.toFixed(2);
        noteName.textContent = STRINGS[selected].name;
    }
    updateTarget();

    function autoCorrelate(buf, sampleRate) {
        var SIZE = buf.length;
        var rms = 0, i, j;
        for (i = 0; i < SIZE; i++) { var v = buf[i]; rms += v * v; }
        rms = Math.sqrt(rms / SIZE);
        if (rms < 0.01) return -1;
        var r1 = 0, r2 = SIZE - 1;
        var thres = 0.2;
        for (i = 0; i < SIZE / 2; i++) { if (Math.abs(buf[i]) < thres) { r1 = i; break; } }
        for (i = 1; i < SIZE / 2; i++) { if (Math.abs(buf[SIZE - i]) < thres) { r2 = SIZE - i; break; } }
        var buf2 = buf.slice(r1, r2);
        if (buf2.length < 64) return -1;
        var n = buf2.length;
        var c = new Array(n).fill(0);
        for (i = 0; i < n; i++) {
            for (j = 0; j < n - i; j++) c[i] += buf2[j] * buf2[j + i];
        }
        var d = 0;
        while (c[d] > c[d + 1] && d + 1 < n) d++;
        var maxval = -1, maxpos = -1;
        for (i = d; i < n; i++) { if (c[i] > maxval) { maxval = c[i]; maxpos = i; } }
        var T0 = maxpos;
        if (T0 > 0 && T0 < n - 1) {
            var x1 = c[T0 - 1], x2 = c[T0], x3 = c[T0 + 1];
            var a = (x1 + x3 - 2 * x2) / 2, b = (x3 - x1) / 2;
            if (a) T0 = T0 - b / (2 * a);
        }
        return sampleRate / T0;
    }

    function nearestString(freq) {
        var best = 0, bestDiff = Infinity, i;
        for (i = 0; i < STRINGS.length; i++) {
            var diff = Math.abs(1200 * Math.log2(freq / STRINGS[i].freq));
            if (diff < bestDiff) { bestDiff = diff; best = i; }
        }
        return best;
    }

    function loop() {
        if (!running) return;
        var buf = new Float32Array(analyser.fftSize);
        analyser.getFloatTimeDomainData(buf);
        var freq = autoCorrelate(buf, audioCtx.sampleRate);
        if (freq > 40 && freq < 1000) {
            if (autoMode.checked) {
                selected = nearestString(freq);
                btns.forEach(function (x) { x.classList.remove('active'); });
                btns[selected].classList.add('active');
            }
            var target = STRINGS[selected].freq;
            var cents = 1200 * Math.log2(freq / target);
            freqVal.textContent = freq.toFixed(1);
            updateTarget();
            var clamped = Math.max(-50, Math.min(50, cents));
            var pct = 50 + clamped; // 0..100
            needle.style.left = pct + '%';
            if (Math.abs(cents) <= 5) {
                tuneStatus.innerHTML = '<span class="text-success">Perfect! ' + STRINGS[selected].name + ' is in tune.</span>';
            } else if (cents < 0) {
                tuneStatus.innerHTML = '<span class="text-danger">Too low — tighten by ' + Math.abs(cents).toFixed(0) + ' cents.</span>';
            } else {
                tuneStatus.innerHTML = '<span class="text-danger">Too high — loosen by ' + cents.toFixed(0) + ' cents.</span>';
            }
        }
        rafId = requestAnimationFrame(loop);
    }

    goBtn.addEventListener('click', function () {
        hideError();
        if (running) {
            running = false;
            cancelAnimationFrame(rafId);
            if (audioCtx) audioCtx.close();
            audioCtx = null;
            goBtn.textContent = 'Start Mic';
            tuneStatus.innerHTML = '<span class="text-muted">Tuner off.</span>';
            return;
        }
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            showError('Your browser does not support the mic. Use Chrome or Firefox.');
            return;
        }
        navigator.mediaDevices.getUserMedia({ audio: true })
            .then(function (stream) {
                audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                var source = audioCtx.createMediaStreamSource(stream);
                analyser = audioCtx.createAnalyser();
                analyser.fftSize = 2048;
                source.connect(analyser);
                running = true;
                results.classList.remove('d-none');
                goBtn.textContent = 'Stop Tuner';
                loop();
            })
            .catch(function () {
                showError('Mic permission was not given. Allow the mic in your browser settings and try again.');
            });
    });
})();
</script>
@endsection
