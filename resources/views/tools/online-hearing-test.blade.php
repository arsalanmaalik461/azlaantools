@extends('layouts.app')
@section('title', 'Online Hearing Test - Check Your Hearing Range Free | Azlaan Tools')
@section('meta_description', 'Free online hearing test: check which frequencies and volumes you can hear with tones from 250 Hz to 8000 Hz. Instant result chart, no signup.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-2">Online Hearing Test</h1>
            <p class="lead text-muted">Find out how quiet a sound you can hear at different frequencies — this test estimates your hearing.</p>
            <div class="alert alert-danger"><strong>Disclaimer:</strong> This is only an estimate, not medical treatment. Every device has different volume, so this is not a medical audiogram. For a real test, contact an audiologist or ENT doctor.</div>

            <div class="card shadow-sm mb-4" id="startCard">
                <div class="card-body">
                    <h2 class="h5">Before the test</h2>
                    <ol>
                        <li>Wear <strong>headphones or earphones</strong> — you will get a better result.</li>
                        <li>Choose a <strong>quiet room</strong>.</li>
                        <li>Set your mobile or computer <strong>volume to 50%</strong> and do not change it during the test.</li>
                        <li>Listen carefully to each tone — if you hear it press "I Heard It", otherwise press "I Did Not Hear It".</li>
                    </ol>
                    <div class="d-flex gap-2 flex-wrap">
                        <button type="button" id="soundCheckBtn" class="btn btn-outline-secondary">Check Sound</button>
                        <button type="button" id="startBtn" class="btn btn-primary btn-lg flex-grow-1">Start Test</button>
                    </div>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>
                    <p class="small text-muted mt-2 mb-0">The test has 6 frequencies: 250, 500, 1000, 2000, 4000 and 8000 Hz. Time: about 3 minutes.</p>
                </div>
            </div>

            <div class="card shadow-sm mb-4 d-none" id="testCard">
                <div class="card-body text-center">
                    <p class="text-muted mb-1" id="progressText">Frequency 1 of 6</p>
                    <h2 class="h4 mb-1" id="freqLabel">250 Hz</h2>
                    <p class="text-muted small" id="levelLabel">Volume level: loud</p>
                    <div class="my-3">
                        <button type="button" id="playBtn" class="btn btn-primary btn-lg px-5">Play Tone</button>
                    </div>
                    <p class="small text-muted">Answer after hearing the tone:</p>
                    <div class="d-flex gap-2 justify-content-center">
                        <button type="button" id="heardBtn" class="btn btn-success btn-lg" disabled>I Heard It</button>
                        <button type="button" id="missedBtn" class="btn btn-outline-danger btn-lg" disabled>I Did Not Hear It</button>
                    </div>
                    <p class="small text-muted mt-3 mb-0">Even if you do not hear the tone, still press "I Did Not Hear It" — that is part of the test.</p>
                </div>
            </div>

            <div class="card shadow-sm mb-4 d-none" id="resultCard">
                <div class="card-body">
                    <h2 class="h5">Your result</h2>
                    <p class="text-muted small">The graph below shows the quietest sound you heard at each frequency. <strong>Lower = better hearing.</strong> These levels are relative, not medical dB.</p>
                    <canvas id="audioCanvas" width="640" height="380" class="w-100 border rounded bg-white" style="max-width: 640px;"></canvas>
                    <div class="table-responsive mt-3">
                        <table class="table table-bordered table-sm">
                            <thead class="table-light"><tr><th>Frequency</th><th>Quietest sound heard</th></tr></thead>
                            <tbody id="resultTableBody"></tbody>
                        </table>
                    </div>
                    <div class="alert alert-info" id="interpText"></div>
                    <button type="button" id="restartBtn" class="btn btn-outline-primary w-100">Test Again</button>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Wear headphones and set the volume to 50%.</li>
                <li>Press <strong>Start Test</strong>.</li>
                <li>Play the tone at each frequency and answer honestly whether you heard it or not.</li>
                <li>See your result chart at the end.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var startCard = document.getElementById('startCard');
    var testCard = document.getElementById('testCard');
    var resultCard = document.getElementById('resultCard');
    var startBtn = document.getElementById('startBtn');
    var soundCheckBtn = document.getElementById('soundCheckBtn');
    var errorBox = document.getElementById('errorBox');
    var progressText = document.getElementById('progressText');
    var freqLabel = document.getElementById('freqLabel');
    var levelLabel = document.getElementById('levelLabel');
    var playBtn = document.getElementById('playBtn');
    var heardBtn = document.getElementById('heardBtn');
    var missedBtn = document.getElementById('missedBtn');
    var resultTableBody = document.getElementById('resultTableBody');
    var interpText = document.getElementById('interpText');
    var restartBtn = document.getElementById('restartBtn');
    var audioCanvas = document.getElementById('audioCanvas');

    var FREQS = [250, 500, 1000, 2000, 4000, 8000];
    var LEVELS = [0, -10, -20, -30, -40, -50]; // dB relative
    var BASE_GAIN = 0.25;
    var TONE_SEC = 1.2;

    var actx = null;
    var fi = 0;       // frequency index
    var li = 0;       // level index (0 = loudest)
    var played = false;
    var thresholds = [];

    function showError(m) { errorBox.textContent = m; errorBox.classList.remove('d-none'); }
    function hideError() { errorBox.classList.add('d-none'); errorBox.textContent = ''; }

    function ensureAudio() {
        if (!actx) {
            var AC = window.AudioContext || window.webkitAudioContext;
            if (!AC) { showError('Your browser does not support Web Audio.'); return false; }
            actx = new AC();
        }
        if (actx.state === 'suspended') actx.resume();
        return true;
    }

    function playTone(freq, db) {
        if (!ensureAudio()) return;
        var t = actx.currentTime;
        var osc = actx.createOscillator();
        var g = actx.createGain();
        var gainVal = BASE_GAIN * Math.pow(10, db / 20);
        osc.type = 'sine';
        osc.frequency.value = freq;
        g.gain.setValueAtTime(0.0001, t);
        g.gain.exponentialRampToValueAtTime(Math.max(0.0002, gainVal), t + 0.08);
        g.gain.setValueAtTime(Math.max(0.0002, gainVal), t + TONE_SEC - 0.1);
        g.gain.exponentialRampToValueAtTime(0.0001, t + TONE_SEC);
        osc.connect(g);
        g.connect(actx.destination);
        osc.start(t);
        osc.stop(t + TONE_SEC + 0.05);
    }

    function levelName(db) {
        if (db === 0) return 'very loud';
        if (db === -10) return 'loud';
        if (db === -20) return 'medium';
        if (db === -30) return 'soft';
        if (db === -40) return 'very soft';
        return 'extremely soft';
    }

    function updateStep() {
        progressText.textContent = 'Frequency ' + (fi + 1) + ' of ' + FREQS.length;
        freqLabel.textContent = FREQS[fi] + ' Hz';
        levelLabel.textContent = 'Volume level: ' + levelName(LEVELS[li]) + ' (' + LEVELS[li] + ' dB relative)';
        played = false;
        playBtn.disabled = false;
        heardBtn.disabled = true;
        missedBtn.disabled = true;
    }

    function finishFrequency(thresholdDb, note) {
        thresholds.push({ freq: FREQS[fi], db: thresholdDb, note: note });
        fi++;
        li = 0;
        if (fi >= FREQS.length) { showResults(); }
        else { updateStep(); }
    }

    playBtn.addEventListener('click', function () {
        playTone(FREQS[fi], LEVELS[li]);
        played = true;
        playBtn.disabled = true;
        heardBtn.disabled = false;
        missedBtn.disabled = false;
    });

    heardBtn.addEventListener('click', function () {
        if (!played) return;
        if (li >= LEVELS.length - 1) {
            finishFrequency(LEVELS[li], 'heard even at the quietest level');
        } else {
            li++;
            updateStep();
        }
    });

    missedBtn.addEventListener('click', function () {
        if (!played) return;
        if (li === 0) {
            finishFrequency(LEVELS[0], 'not heard even at the loudest level');
        } else {
            finishFrequency(LEVELS[li - 1], '');
        }
    });

    soundCheckBtn.addEventListener('click', function () {
        hideError();
        playTone(1000, -10);
    });

    startBtn.addEventListener('click', function () {
        hideError();
        if (!ensureAudio()) return;
        fi = 0; li = 0; thresholds = [];
        startCard.classList.add('d-none');
        resultCard.classList.add('d-none');
        testCard.classList.remove('d-none');
        updateStep();
    });

    restartBtn.addEventListener('click', function () {
        resultCard.classList.add('d-none');
        testCard.classList.add('d-none');
        startCard.classList.remove('d-none');
    });

    function dbLabel(r) {
        if (r.note === 'not heard even at the loudest level') return 'Not heard at max';
        return r.db + ' dB relative';
    }

    function showResults() {
        testCard.classList.add('d-none');
        resultCard.classList.remove('d-none');
        var html = '';
        for (var i = 0; i < thresholds.length; i++) {
            var r = thresholds[i];
            html += '<tr><td>' + r.freq + ' Hz</td><td>' + dbLabel(r) +
                (r.note && r.note !== 'not heard even at the loudest level' ? ' <span class="text-muted small">(' + r.note + ')</span>' : '') + '</td></tr>';
        }
        resultTableBody.innerHTML = html;
        drawChart();
        var worst = thresholds[0];
        for (var j = 1; j < thresholds.length; j++) {
            if (thresholds[j].db > worst.db) worst = thresholds[j];
        }
        var msg = 'Your hearing seems relatively weaker at ' + worst.freq + ' Hz (' + dbLabel(worst) + '). ';
        msg += 'If daily conversation is hard, you often turn the TV volume up, or your ears ring, get a checkup from an ENT doctor. ';
        msg += 'This is only a first estimate — only a professional hearing test gives a final answer.';
        interpText.textContent = msg;
        resultCard.scrollIntoView({ behavior: 'smooth' });
    }

    function drawChart() {
        var ctx = audioCanvas.getContext('2d');
        var W = audioCanvas.width, H = audioCanvas.height;
        var padL = 56, padR = 20, padT = 24, padB = 44;
        ctx.clearRect(0, 0, W, H);
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, W, H);
        function xPos(i) { return padL + i * (W - padL - padR) / (FREQS.length - 1); }
        function yPos(db) { return padT + (0 - db) / 50 * (H - padT - padB); }
        ctx.strokeStyle = '#dee2e6';
        ctx.fillStyle = '#6c757d';
        ctx.font = '12px sans-serif';
        ctx.lineWidth = 1;
        for (var g = 0; g >= -50; g -= 10) {
            var y = yPos(g);
            ctx.beginPath(); ctx.moveTo(padL, y); ctx.lineTo(W - padR, y); ctx.stroke();
            ctx.fillText(g + ' dB', 8, y + 4);
        }
        for (var f = 0; f < FREQS.length; f++) {
            ctx.fillText(FREQS[f] + '', xPos(f) - 12, H - 24);
        }
        ctx.fillText('Frequency (Hz)', padL, H - 6);
        ctx.save();
        ctx.translate(14, H / 2 + 40);
        ctx.rotate(-Math.PI / 2);
        ctx.fillText('Relative volume — lower is better', 0, 0);
        ctx.restore();
        ctx.strokeStyle = '#0d6efd';
        ctx.fillStyle = '#0d6efd';
        ctx.lineWidth = 2.5;
        ctx.beginPath();
        for (var p = 0; p < thresholds.length; p++) {
            var px = xPos(p), py = yPos(thresholds[p].db);
            if (p === 0) ctx.moveTo(px, py); else ctx.lineTo(px, py);
        }
        ctx.stroke();
        for (var q = 0; q < thresholds.length; q++) {
            ctx.beginPath();
            ctx.arc(xPos(q), yPos(thresholds[q].db), 5, 0, Math.PI * 2);
            ctx.fill();
        }
    }
})();
</script>
@endsection
