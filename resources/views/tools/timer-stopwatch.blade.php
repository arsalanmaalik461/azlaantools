@extends('layouts.app')

@section('title', 'Timer & Stopwatch Online - Free Countdown Timer | Azlaan Tools')
@section('meta_description', 'Free online timer and stopwatch. Set a countdown with alarm beep, or use the stopwatch with laps and centisecond display. No signup needed.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="mb-3">Timer &amp; Stopwatch</h1>
            <p class="lead text-muted">A precise countdown timer with alarm and a stopwatch with laps — free, in your browser, no signup.</p>
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#timerPane" type="button">Timer</button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#swPane" type="button">Stopwatch</button></li>
            </ul>
            <div class="tab-content border border-top-0 rounded-bottom p-4 bg-white shadow-sm mb-4">
                <div class="tab-pane fade show active" id="timerPane">
                    <div class="text-center display-3 fw-bold font-monospace" id="timerDisplay">00:00:00</div>
                    <div class="row g-2 mt-3 justify-content-center">
                        <div class="col-3"><label class="form-label small">Hours</label><input type="number" class="form-control" id="tH" min="0" max="99" value="0"></div>
                        <div class="col-3"><label class="form-label small">Minutes</label><input type="number" class="form-control" id="tM" min="0" max="59" value="5"></div>
                        <div class="col-3"><label class="form-label small">Seconds</label><input type="number" class="form-control" id="tS" min="0" max="59" value="0"></div>
                    </div>
                    <div class="text-center mt-3">
                        <button type="button" class="btn btn-success" id="tStart">Start</button>
                        <button type="button" class="btn btn-warning" id="tPause">Pause</button>
                        <button type="button" class="btn btn-outline-danger" id="tReset">Reset</button>
                    </div>
                    <div class="progress mt-3" style="height: 14px;"><div class="progress-bar" id="tBar" style="width: 100%;"></div></div>
                </div>
                <div class="tab-pane fade" id="swPane">
                    <div class="text-center display-3 fw-bold font-monospace" id="swDisplay">00:00:00.00</div>
                    <div class="text-center mt-3">
                        <button type="button" class="btn btn-success" id="swStart">Start</button>
                        <button type="button" class="btn btn-warning" id="swPause">Pause</button>
                        <button type="button" class="btn btn-info text-white" id="swLap">Lap</button>
                        <button type="button" class="btn btn-outline-danger" id="swReset">Reset</button>
                    </div>
                    <ol class="list-group list-group-numbered mt-3" id="lapList"></ol>
                </div>
            </div>
            <div class="card shadow-sm"><div class="card-body">
                <h2>How to use</h2>
                <ol><li>Timer: set hours, minutes and seconds, then press Start — a beep sounds when time is up.</li><li>Pause and Resume keep the remaining time accurate.</li><li>Stopwatch: press Start, use Lap to record split times, Reset to clear.</li></ol>
            </div></div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    function pad(n) { return String(n).padStart(2, '0'); }
    // TIMER
    var tEnd = 0, tRemaining = 0, tTotal = 0, tIv = null, tRunning = false;
    function tShow(ms) {
        if (ms < 0) ms = 0;
        var s = Math.ceil(ms / 1000);
        document.getElementById('timerDisplay').textContent = pad(Math.floor(s / 3600)) + ':' + pad(Math.floor(s % 3600 / 60)) + ':' + pad(s % 60);
        document.getElementById('tBar').style.width = (tTotal > 0 ? (ms / tTotal * 100) : 0) + '%';
    }
    function beep() {
        try {
            var ctx = new (window.AudioContext || window.webkitAudioContext)();
            [0, 0.4, 0.8].forEach(function (off) { var o = ctx.createOscillator(), g = ctx.createGain(); o.connect(g); g.connect(ctx.destination); o.frequency.value = 880; g.gain.value = 0.25; o.start(ctx.currentTime + off); o.stop(ctx.currentTime + off + 0.3); });
        } catch (e) {}
    }
    function tTick() { var rem = tEnd - Date.now(); if (rem <= 0) { tShow(0); clearInterval(tIv); tRunning = false; beep(); alert('Time is up!'); return; } tShow(rem); }
    document.getElementById('tStart').addEventListener('click', function () {
        if (tRunning) return;
        if (tRemaining <= 0) {
            tTotal = ((parseInt(document.getElementById('tH').value, 10) || 0) * 3600 + (parseInt(document.getElementById('tM').value, 10) || 0) * 60 + (parseInt(document.getElementById('tS').value, 10) || 0)) * 1000;
            if (tTotal <= 0) { alert('Please set a time greater than zero.'); return; }
            tRemaining = tTotal;
        }
        tEnd = Date.now() + tRemaining; tRunning = true; tIv = setInterval(tTick, 100); tTick();
    });
    document.getElementById('tPause').addEventListener('click', function () { if (!tRunning) return; tRemaining = tEnd - Date.now(); clearInterval(tIv); tRunning = false; tShow(tRemaining); });
    document.getElementById('tReset').addEventListener('click', function () { clearInterval(tIv); tRunning = false; tRemaining = 0; tTotal = 0; document.getElementById('timerDisplay').textContent = '00:00:00'; document.getElementById('tBar').style.width = '100%'; });
    // STOPWATCH
    var swStartT = 0, swElapsed = 0, swIv = null, swRunning = false, lapCount = 0;
    function swFmt(ms) { var cs = Math.floor(ms / 10) % 100, s = Math.floor(ms / 1000) % 60, m = Math.floor(ms / 60000) % 60, h = Math.floor(ms / 3600000); return pad(h) + ':' + pad(m) + ':' + pad(s) + '.' + pad(cs); }
    function swTick() { document.getElementById('swDisplay').textContent = swFmt(swElapsed + (Date.now() - swStartT)); }
    document.getElementById('swStart').addEventListener('click', function () { if (swRunning) return; swStartT = Date.now(); swRunning = true; swIv = setInterval(swTick, 31); });
    document.getElementById('swPause').addEventListener('click', function () { if (!swRunning) return; swElapsed += Date.now() - swStartT; clearInterval(swIv); swRunning = false; swTick(); });
    document.getElementById('swLap').addEventListener('click', function () { if (!swRunning) return; lapCount++; var li = document.createElement('li'); li.className = 'list-group-item d-flex justify-content-between'; li.innerHTML = '<span>Lap ' + lapCount + '</span><strong>' + swFmt(swElapsed + (Date.now() - swStartT)) + '</strong>'; document.getElementById('lapList').prepend(li); });
    document.getElementById('swReset').addEventListener('click', function () { clearInterval(swIv); swRunning = false; swElapsed = 0; lapCount = 0; document.getElementById('swDisplay').textContent = '00:00:00.00'; document.getElementById('lapList').innerHTML = ''; });
})();
</script>
@endsection
