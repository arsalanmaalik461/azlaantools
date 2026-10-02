@extends('layouts.app')

@section('title', 'Breathing Exercise Timer - Azlaan Tools')
@section('meta_description', 'Free online guided timer for box breathing and the 4-7-8 technique. Breathing exercise to reduce stress.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Breathing Exercise Timer</h1>
            <p class="lead text-muted">Guided breathing exercise — Box Breathing or 4-7-8 technique. Follow the circle and reduce stress.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3 mb-4">
                        <div class="col-12 col-md-6">
                            <label for="techSel" class="form-label fw-semibold">Technique</label>
                            <select class="form-select" id="techSel">
                                <option value="box">Box Breathing (4-4-4-4)</option>
                                <option value="478">4-7-8 Relaxation</option>
                                <option value="relax">Relax (4 inhale, 6 exhale)</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="cycleSel" class="form-label fw-semibold">Rounds</label>
                            <select class="form-select" id="cycleSel">
                                <option value="3">3 rounds</option>
                                <option value="5" selected>5 rounds</option>
                                <option value="8">8 rounds</option>
                                <option value="10">10 rounds</option>
                            </select>
                        </div>
                    </div>

                    <div class="text-center mb-3">
                        <div id="breathCircleWrap" class="mx-auto" style="width:260px;height:260px;">
                            <div id="breathCircle" class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width:120px;height:120px;margin:70px auto;background:#dbeafe;border:4px solid #3b82f6;transition:width 1s linear,height 1s linear,margin 1s linear,background 0.8s;">
                                <span id="phaseLabel" class="fw-semibold text-primary">Ready</span>
                            </div>
                        </div>
                        <div class="display-4 fw-bold my-2" id="countLabel">&nbsp;</div>
                        <div class="text-muted" id="roundLabel">Press Start and sit comfortably</div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-primary flex-fill" id="startBtn">Start</button>
                        <button type="button" class="btn btn-outline-secondary flex-fill" id="stopBtn" disabled>Stop</button>
                    </div>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>
                    <div id="results" class="d-none mt-3">
                        <div class="alert alert-success mb-0" id="doneMsg"></div>
                    </div>
                    <small class="text-muted d-block mt-3">This exercise is a guide only, not a treatment. Stop if you feel dizzy.</small>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Choose a technique and rounds.</li>
                <li>Press Start — when the circle grows, breathe in (inhale); when it shrinks, breathe out (exhale); when it stops, hold.</li>
                <li>When the rounds are complete, you will see a session complete message.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var startBtn = document.getElementById('startBtn');
    var stopBtn = document.getElementById('stopBtn');
    var techSel = document.getElementById('techSel');
    var cycleSel = document.getElementById('cycleSel');
    var circle = document.getElementById('breathCircle');
    var phaseLabel = document.getElementById('phaseLabel');
    var countLabel = document.getElementById('countLabel');
    var roundLabel = document.getElementById('roundLabel');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var doneMsg = document.getElementById('doneMsg');

    var techs = {
        box:   { name: 'Box Breathing', phases: [['Inhale', 4], ['Hold', 4], ['Exhale', 4], ['Hold', 4]] },
        '478': { name: '4-7-8 Relaxation', phases: [['Inhale', 4], ['Hold', 7], ['Exhale', 8]] },
        relax: { name: 'Relax Breathing', phases: [['Inhale', 4], ['Exhale', 6]] }
    };
    var running = false;
    var timers = [];
    var audioCtx = null;

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function clearTimers() {
        for (var i = 0; i < timers.length; i++) { clearTimeout(timers[i]); clearInterval(timers[i]); }
        timers = [];
    }
    function beep(freq, dur) {
        try {
            if (!audioCtx) { audioCtx = new (window.AudioContext || window.webkitAudioContext)(); }
            var o = audioCtx.createOscillator();
            var g = audioCtx.createGain();
            o.connect(g); g.connect(audioCtx.destination);
            o.frequency.value = freq;
            g.gain.setValueAtTime(0.0001, audioCtx.currentTime);
            g.gain.exponentialRampToValueAtTime(0.3, audioCtx.currentTime + 0.02);
            g.gain.exponentialRampToValueAtTime(0.0001, audioCtx.currentTime + dur);
            o.start();
            o.stop(audioCtx.currentTime + dur + 0.05);
        } catch (e) { /* audio optional */ }
    }
    function setCircle(big, seconds, phase) {
        circle.style.transition = 'width ' + seconds + 's linear, height ' + seconds + 's linear, margin ' + seconds + 's linear, background 0.8s';
        circle.style.width = big ? '220px' : '120px';
        circle.style.height = big ? '220px' : '120px';
        circle.style.margin = big ? '20px auto' : '70px auto';
        circle.style.background = phase === 'Hold' ? '#fef3c7' : (big ? '#bfdbfe' : '#dbeafe');
    }
    function finish() {
        running = false;
        clearTimers();
        startBtn.disabled = false;
        stopBtn.disabled = true;
        techSel.disabled = false;
        cycleSel.disabled = false;
        setCircle(false, 1, '');
        phaseLabel.textContent = 'Done';
        countLabel.innerHTML = '&nbsp;';
        roundLabel.textContent = 'Session complete - Well done!';
        doneMsg.textContent = 'Exercise complete! Give yourself 1 more minute of calm.';
        results.classList.remove('d-none');
        beep(660, 0.25);
    }
    function stopNow() {
        running = false;
        clearTimers();
        startBtn.disabled = false;
        stopBtn.disabled = true;
        techSel.disabled = false;
        cycleSel.disabled = false;
        setCircle(false, 1, '');
        phaseLabel.textContent = 'Ready';
        countLabel.innerHTML = '&nbsp;';
        roundLabel.textContent = 'Press Start and sit comfortably';
        results.classList.add('d-none');
    }

    startBtn.addEventListener('click', function () {
        hideError();
        if (running) return;
        try {
            if (!audioCtx) { audioCtx = new (window.AudioContext || window.webkitAudioContext)(); }
        } catch (e) { /* optional */ }
        var tech = techs[techSel.value];
        var rounds = parseInt(cycleSel.value, 10);
        if (!tech || isNaN(rounds)) { showError('Please select a technique.'); return; }
        running = true;
        results.classList.add('d-none');
        startBtn.disabled = true;
        stopBtn.disabled = false;
        techSel.disabled = true;
        cycleSel.disabled = true;

        var schedule = [];
        for (var r = 0; r < rounds; r++) {
            for (var p = 0; p < tech.phases.length; p++) {
                schedule.push({ round: r + 1, name: tech.phases[p][0], secs: tech.phases[p][1] });
            }
        }
        var t = 300;
        beep(523, 0.2);
        for (var i = 0; i < schedule.length; i++) {
            (function (ph, startAt, isLast) {
                timers.push(setTimeout(function () {
                    if (!running) return;
                    var grow = ph.name === 'Inhale';
                    setCircle(grow, ph.secs, ph.name);
                    phaseLabel.textContent = ph.name;
                    roundLabel.textContent = tech.name + ' — Round ' + ph.round + ' of ' + rounds;
                    beep(ph.name === 'Inhale' ? 523 : (ph.name === 'Exhale' ? 392 : 440), 0.15);
                    var left = ph.secs;
                    countLabel.textContent = left;
                    var iv = setInterval(function () {
                        left--;
                        if (left > 0 && running) { countLabel.textContent = left; }
                        else { clearInterval(iv); }
                    }, 1000);
                    timers.push(iv);
                    if (isLast) {
                        timers.push(setTimeout(function () { if (running) finish(); }, ph.secs * 1000));
                    }
                }, startAt));
            })(schedule[i], t, i === schedule.length - 1);
            t += schedule[i].secs * 1000;
        }
    });

    stopBtn.addEventListener('click', stopNow);
})();
</script>
@endsection
