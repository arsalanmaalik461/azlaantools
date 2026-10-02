@extends('layouts.app')

@section('title', 'Online Chess Clock - Azlaan Tools')
@section('meta_description', 'Free two-player online chess clock with tap-to-switch and time controls. Works in your browser.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Online Chess Clock</h1>
            <p class="lead text-muted">Two player chess timer with tap to switch. A timer for chess — choose a time control, press start, and tap your side on your turn.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3 mb-3">
                        <div class="col-6 col-md-4">
                            <label for="tcPreset" class="form-label fw-semibold">Time control</label>
                            <select class="form-select" id="tcPreset">
                                <option value="1|0">Bullet 1+0</option>
                                <option value="3|2" selected>Blitz 3+2</option>
                                <option value="5|0">Blitz 5+0</option>
                                <option value="5|3">Blitz 5+3</option>
                                <option value="10|0">Rapid 10+0</option>
                                <option value="15|10">Rapid 15+10</option>
                                <option value="30|0">Classical 30+0</option>
                                <option value="custom">Custom...</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-4">
                            <label for="customMin" class="form-label fw-semibold">Custom minutes</label>
                            <input type="number" class="form-control" id="customMin" value="10" min="1" max="180" disabled>
                        </div>
                        <div class="col-6 col-md-4">
                            <label for="customInc" class="form-label fw-semibold">Custom increment (sec)</label>
                            <input type="number" class="form-control" id="customInc" value="0" min="0" max="60" disabled>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-2 mb-3 justify-content-center">
                        <button type="button" class="btn btn-success" id="startBtn">Start Game</button>
                        <button type="button" class="btn btn-outline-secondary" id="pauseBtn" disabled>Pause</button>
                        <button type="button" class="btn btn-outline-danger" id="resetBtn">Reset</button>
                    </div>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div class="row g-2" id="clockBoard">
                        <div class="col-6">
                            <button type="button" id="clockTop" class="btn w-100 py-5 border rounded bg-light" disabled>
                                <div class="fw-semibold">Player 2 (Black)</div>
                                <div class="display-5 fw-bold" id="timeTop">3:00</div>
                            </button>
                        </div>
                        <div class="col-6">
                            <button type="button" id="clockBottom" class="btn w-100 py-5 border rounded bg-light" disabled>
                                <div class="fw-semibold">Player 1 (White)</div>
                                <div class="display-5 fw-bold" id="timeBottom">3:00</div>
                            </button>
                        </div>
                    </div>
                    <p class="text-center text-muted small mt-2 mb-0">Tap your side after your move — the clock will switch to the other player.</p>

                    <div id="results" class="d-none mt-4"></div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Choose a time control (e.g. Blitz 3+2) or set a custom time.</li>
                <li>Press "Start Game" — the White (bottom) clock runs first.</li>
                <li>Tap your side after each move; when the time runs out, the flag falls.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var tcPreset = document.getElementById('tcPreset');
    var customMin = document.getElementById('customMin');
    var customInc = document.getElementById('customInc');
    var startBtn = document.getElementById('startBtn');
    var pauseBtn = document.getElementById('pauseBtn');
    var resetBtn = document.getElementById('resetBtn');
    var errorBox = document.getElementById('errorBox');
    var clockTop = document.getElementById('clockTop');
    var clockBottom = document.getElementById('clockBottom');
    var timeTop = document.getElementById('timeTop');
    var timeBottom = document.getElementById('timeBottom');
    var results = document.getElementById('results');

    var msTop = 0, msBottom = 0, incMs = 0;
    var active = null; // 'top' | 'bottom'
    var lastTick = 0, rafId = null, paused = true, gameOver = false;
    var audioCtx = null, warned = { top: false, bottom: false };

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function fmt(ms) {
        ms = Math.max(0, ms);
        var totalSec = Math.ceil(ms / 1000);
        var m = Math.floor(totalSec / 60);
        var s = totalSec % 60;
        return m + ':' + (s < 10 ? '0' : '') + s;
    }
    function beep(freq, dur) {
        try {
            if (!audioCtx) audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            var osc = audioCtx.createOscillator();
            var gain = audioCtx.createGain();
            osc.connect(gain); gain.connect(audioCtx.destination);
            osc.frequency.value = freq || 880;
            osc.type = 'sine';
            var t = audioCtx.currentTime;
            gain.gain.setValueAtTime(0.25, t);
            gain.gain.exponentialRampToValueAtTime(0.001, t + (dur || 0.25));
            osc.start(t);
            osc.stop(t + (dur || 0.25) + 0.05);
        } catch (e) { /* ignore */ }
    }

    tcPreset.addEventListener('change', function () {
        var custom = tcPreset.value === 'custom';
        customMin.disabled = !custom;
        customInc.disabled = !custom;
        if (!gameOver) applyPreset();
    });

    function applyPreset() {
        var v = tcPreset.value, mins, inc;
        if (v === 'custom') {
            mins = parseInt(customMin.value, 10) || 10;
            inc = parseInt(customInc.value, 10) || 0;
        } else {
            var parts = v.split('|');
            mins = parseInt(parts[0], 10);
            inc = parseInt(parts[1], 10);
        }
        msTop = msBottom = mins * 60000;
        incMs = inc * 1000;
        render();
    }

    function render() {
        timeTop.textContent = fmt(msTop);
        timeBottom.textContent = fmt(msBottom);
        clockTop.classList.remove('bg-primary', 'text-white', 'bg-light', 'bg-danger', 'text-white');
        clockBottom.classList.remove('bg-primary', 'text-white', 'bg-light', 'bg-danger', 'text-white');
        if (gameOver) return;
        if (active === 'top') {
            clockTop.classList.add('bg-primary', 'text-white');
            clockBottom.classList.add('bg-light');
        } else if (active === 'bottom') {
            clockBottom.classList.add('bg-primary', 'text-white');
            clockTop.classList.add('bg-light');
        } else {
            clockTop.classList.add('bg-light');
            clockBottom.classList.add('bg-light');
        }
        if (msTop <= 10000 && msTop > 0 && !warned.top) { warned.top = true; beep(660, 0.15); }
        if (msBottom <= 10000 && msBottom > 0 && !warned.bottom) { warned.bottom = true; beep(660, 0.15); }
        if (msTop <= 10000 && msTop > 0) timeTop.classList.add('text-warning');
        if (msBottom <= 10000 && msBottom > 0) timeBottom.classList.add('text-warning');
    }

    function flag(side) {
        gameOver = true;
        paused = true;
        if (rafId) cancelAnimationFrame(rafId);
        rafId = null;
        beep(440, 0.6);
        var loser = side === 'top' ? clockTop : clockBottom;
        loser.classList.remove('bg-primary', 'bg-light');
        loser.classList.add('bg-danger', 'text-white');
        clockTop.disabled = true;
        clockBottom.disabled = true;
        pauseBtn.disabled = true;
        var winner = side === 'top' ? 'Player 1 (White)' : 'Player 2 (Black)';
        results.classList.remove('d-none');
        results.innerHTML = '<div class="alert alert-warning text-center fw-bold">TIME! ' + winner + ' wins on time.</div>';
    }

    function loop(now) {
        if (paused || gameOver) return;
        var dt = now - lastTick;
        lastTick = now;
        if (active === 'top') {
            msTop -= dt;
            if (msTop <= 0) { msTop = 0; render(); flag('top'); return; }
        } else if (active === 'bottom') {
            msBottom -= dt;
            if (msBottom <= 0) { msBottom = 0; render(); flag('bottom'); return; }
        }
        render();
        rafId = requestAnimationFrame(loop);
    }

    function press(side) {
        if (paused || gameOver) return;
        if (active !== side) return;
        if (side === 'top') { msTop += incMs; active = 'bottom'; }
        else { msBottom += incMs; active = 'top'; }
        beep(990, 0.08);
        lastTick = performance.now();
        render();
    }

    clockTop.addEventListener('click', function () { press('top'); });
    clockBottom.addEventListener('click', function () { press('bottom'); });

    startBtn.addEventListener('click', function () {
        hideError();
        if (gameOver) { showError('The game is over — press Reset for a new game.'); return; }
        applyPreset();
        warned = { top: false, bottom: false };
        timeTop.classList.remove('text-warning');
        timeBottom.classList.remove('text-warning');
        active = 'bottom';
        paused = false;
        clockTop.disabled = false;
        clockBottom.disabled = false;
        pauseBtn.disabled = false;
        pauseBtn.textContent = 'Pause';
        startBtn.disabled = true;
        lastTick = performance.now();
        beep(880, 0.15);
        render();
        rafId = requestAnimationFrame(loop);
    });

    pauseBtn.addEventListener('click', function () {
        if (gameOver) return;
        if (paused) {
            paused = false;
            lastTick = performance.now();
            rafId = requestAnimationFrame(loop);
            pauseBtn.textContent = 'Pause';
        } else {
            paused = true;
            if (rafId) cancelAnimationFrame(rafId);
            rafId = null;
            pauseBtn.textContent = 'Resume';
        }
    });

    resetBtn.addEventListener('click', function () {
        if (rafId) cancelAnimationFrame(rafId);
        rafId = null;
        gameOver = false;
        paused = true;
        active = null;
        warned = { top: false, bottom: false };
        timeTop.classList.remove('text-warning');
        timeBottom.classList.remove('text-warning');
        clockTop.classList.remove('bg-danger', 'text-white');
        clockBottom.classList.remove('bg-danger', 'text-white');
        clockTop.disabled = true;
        clockBottom.disabled = true;
        pauseBtn.disabled = true;
        pauseBtn.textContent = 'Pause';
        startBtn.disabled = false;
        hideError();
        results.classList.add('d-none');
        results.innerHTML = '';
        applyPreset();
    });

    applyPreset();
})();
</script>
@endsection
