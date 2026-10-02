@extends('layouts.app')

@section('title', 'Metronome Online Free - BPM, Tap Tempo, Time Signatures | Azlaan Tools')
@section('meta_description', 'Free online metronome: 30-240 BPM with precise WebAudio timing, tap tempo, time signatures 2/4, 3/4, 4/4 and 6/8, accented first beat and visual pulse. No signup.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Metronome</h1>
            <p class="lead text-muted">A precise, rock-steady beat for music practice — set the tempo, pick a time signature, and play along.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body text-center">
                    <div id="mnDot" class="rounded-circle mx-auto mb-3" style="width: 90px; height: 90px; background: #dee2e6; transition: transform 0.06s, background 0.06s;"></div>
                    <div class="display-3 fw-bold font-monospace"><span id="mnBpmVal">100</span> <small class="fs-4 text-muted">BPM</small></div>
                    <p class="text-muted mb-1" id="mnBeatInfo">Beat – of –</p>
                    <p class="small text-muted" id="mnTempoName">Andante</p>

                    <label class="form-label fw-semibold w-100 text-start" for="mnBpm">Tempo (30–240 BPM)</label>
                    <input type="range" id="mnBpm" class="form-range" min="30" max="240" step="1" value="100">
                    <div class="row g-2 justify-content-center">
                        <div class="col-5 col-md-3">
                            <input type="number" id="mnBpmNum" class="form-control form-control-lg text-center" min="30" max="240" value="100">
                        </div>
                        <div class="col-5 col-md-3">
                            <button type="button" id="mnTap" class="btn btn-outline-primary btn-lg w-100">Tap Tempo</button>
                        </div>
                    </div>

                    <label class="form-label fw-semibold w-100 text-start mt-3" for="mnSig">Time signature</label>
                    <select id="mnSig" class="form-select form-select-lg">
                        <option value="2">2/4 — two beats</option>
                        <option value="3">3/4 — three beats (waltz)</option>
                        <option value="4" selected>4/4 — four beats</option>
                        <option value="6">6/8 — six beats</option>
                    </select>

                    <div class="form-check form-switch fs-5 mt-3 d-flex justify-content-center gap-2">
                        <input class="form-check-input" type="checkbox" id="mnAccent" checked>
                        <label class="form-check-label" for="mnAccent">Accent first beat</label>
                    </div>

                    <div class="d-grid d-md-block mt-3">
                        <button type="button" id="mnToggle" class="btn btn-success btn-lg px-5">▶ Start</button>
                    </div>
                    <p class="small text-muted mt-2 mb-0" id="mnStatus">Ready. Press Start and play along.</p>
                </div>
            </div>

            <div class="alert alert-secondary"><strong>Privacy note:</strong> Everything runs on your device — nothing is uploaded and no account is needed.</div>

            <div class="card shadow-sm"><div class="card-body">
                <h2>How to use</h2>
                <ol>
                    <li>Set the tempo with the slider or number box (30–240 BPM), or press <strong>Tap Tempo</strong> repeatedly in rhythm to find a song's speed.</li>
                    <li>Choose a time signature: 2/4, 3/4, 4/4 or 6/8.</li>
                    <li>Press <strong>Start</strong> — the first beat of each bar is higher and louder (turn the accent off if you prefer all beats equal).</li>
                    <li>The dot pulses and the beat counter shows where you are in the bar.</li>
                </ol>
                <p class="mb-0 small text-muted">Timing note: this metronome uses a lookahead scheduler, so the beat stays accurate even if the page is busy — far steadier than a simple timer.</p>
            </div></div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    var bpmRange = document.getElementById('mnBpm');
    var bpmNum = document.getElementById('mnBpmNum');
    var bpmVal = document.getElementById('mnBpmVal');
    var dot = document.getElementById('mnDot');
    var beatInfo = document.getElementById('mnBeatInfo');
    var tempoName = document.getElementById('mnTempoName');
    var statusEl = document.getElementById('mnStatus');
    var toggleBtn = document.getElementById('mnToggle');
    var audioCtx = null;
    var running = false;
    var timerId = null;
    var nextNoteTime = 0;
    var currentBeat = 0;
    var tapTimes = [];
    var visualTimers = [];

    function bpm() {
        var v = parseInt(bpmNum.value, 10) || 100;
        return Math.max(30, Math.min(240, v));
    }
    function beatsPerBar() {
        return parseInt(document.getElementById('mnSig').value, 10) || 4;
    }
    function nameFor(b) {
        if (b < 50) return 'Grave / Largo';
        if (b < 66) return 'Adagio';
        if (b < 76) return 'Andante (walking pace)';
        if (b < 108) return 'Moderato';
        if (b < 120) return 'Allegretto';
        if (b < 156) return 'Allegro (fast & bright)';
        if (b < 200) return 'Vivace / Presto';
        return 'Prestissimo';
    }
    function syncBpm(source) {
        var v = bpm();
        if (source !== 'range') bpmRange.value = v;
        if (source !== 'num') bpmNum.value = v;
        bpmVal.textContent = v;
        tempoName.textContent = nameFor(v);
    }
    function click(time, isAccent) {
        var osc = audioCtx.createOscillator();
        var gain = audioCtx.createGain();
        osc.type = 'square';
        osc.frequency.value = isAccent ? 1568 : 1046;
        gain.gain.setValueAtTime(isAccent ? 0.5 : 0.32, time);
        gain.gain.exponentialRampToValueAtTime(0.001, time + 0.07);
        osc.connect(gain);
        gain.connect(audioCtx.destination);
        osc.start(time);
        osc.stop(time + 0.08);
    }
    function showBeat(beat, isAccent) {
        beatInfo.textContent = 'Beat ' + (beat + 1) + ' of ' + beatsPerBar();
        dot.style.background = isAccent ? '#dc3545' : '#0dcaf0';
        dot.style.transform = 'scale(1.25)';
        setTimeout(function () { dot.style.transform = 'scale(1)'; }, 70);
    }
    function scheduler() {
        var secondsPerBeat = 60 / bpm();
        while (nextNoteTime < audioCtx.currentTime + 0.1) {
            var beat = currentBeat;
            var isAccent = document.getElementById('mnAccent').checked && beat === 0;
            click(nextNoteTime, isAccent);
            var delayMs = Math.max(0, (nextNoteTime - audioCtx.currentTime) * 1000);
            // beat/isAccent are function-scoped vars: without capturing them per
            // beat, several visuals scheduled in one pass would all show the
            // final beat of the loop.
            var tid = setTimeout((function (b, a) { return function () { showBeat(b, a); }; })(beat, isAccent), delayMs);
            visualTimers.push(tid);
            nextNoteTime += secondsPerBeat;
            currentBeat = (currentBeat + 1) % beatsPerBar();
        }
    }
    function start() {
        if (!audioCtx) audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        if (audioCtx.state === 'suspended') audioCtx.resume();
        running = true;
        currentBeat = 0;
        nextNoteTime = audioCtx.currentTime + 0.06;
        timerId = setInterval(scheduler, 25);
        toggleBtn.textContent = '■ Stop';
        toggleBtn.classList.remove('btn-success');
        toggleBtn.classList.add('btn-danger');
        statusEl.textContent = 'Playing… keep in time!';
    }
    function stop() {
        running = false;
        if (timerId) clearInterval(timerId);
        visualTimers.forEach(function (t) { clearTimeout(t); } );
        visualTimers = [];
        dot.style.background = '#dee2e6';
        dot.style.transform = 'scale(1)';
        beatInfo.textContent = 'Beat – of –';
        toggleBtn.textContent = '▶ Start';
        toggleBtn.classList.add('btn-success');
        toggleBtn.classList.remove('btn-danger');
        statusEl.textContent = 'Stopped.';
    }
    bpmRange.addEventListener('input', function () {
        bpmNum.value = bpmRange.value;
        syncBpm('num');
    } );
    bpmNum.addEventListener('input', function () { syncBpm('num'); } );
    bpmNum.addEventListener('change', function () { syncBpm(); } );
    toggleBtn.addEventListener('click', function () {
        if (running) stop(); else start();
    } );
    document.getElementById('mnTap').addEventListener('click', function () {
        var now = performance.now();
        tapTimes = tapTimes.filter(function (t) { return now - t < 2000; } );
        tapTimes.push(now);
        if (tapTimes.length >= 2) {
            var intervals = [];
            for (var i = 1; i < tapTimes.length; i++) intervals.push(tapTimes[i] - tapTimes[i - 1]);
            var avg = intervals.reduce(function (a, b) { return a + b; }, 0) / intervals.length;
            var tapped = Math.round(60000 / avg);
            tapped = Math.max(30, Math.min(240, tapped));
            bpmNum.value = tapped;
            syncBpm();
            statusEl.textContent = 'Tap tempo detected: ' + tapped + ' BPM (from ' + tapTimes.length + ' taps).';
        } else {
            statusEl.textContent = 'Keep tapping in rhythm…';
        }
    } );
    syncBpm();
} )();
</script>
@endsection
